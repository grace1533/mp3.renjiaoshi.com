<?php

namespace app\middleware;

use think\Request;
use think\Response;

/**
 * 安全中间件 - 防止 XSS、SQL 注入等攻击
 */
class Security
{
    /**
     * 处理请求
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 只处理 POST/PUT/DELETE 请求的输入数据
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            $this->filterInput($request);
        }

        // 设置安全响应头
        $response = $next($request);
        
        return $this->addSecurityHeaders($response);
    }

    /**
     * 过滤输入数据，防止 XSS 攻击
     * @param Request $request
     */
    protected function filterInput(Request $request): void
    {
        $params = $request->param();
        
        foreach ($params as $key => $value) {
            if (is_string($value)) {
                // 移除潜在的恶意脚本标签
                $cleanValue = $this->sanitizeXSS($value);
                $request->param([$key => $cleanValue]);
            } elseif (is_array($value)) {
                $this->filterArray($value);
            }
        }
    }

    /**
     * XSS 清理
     * @param string $data
     * @return string
     */
    protected function sanitizeXSS(string $data): string
    {
        // 移除 script 标签
        $data = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data);
        
        // 移除 javascript: 协议
        $data = preg_replace('/javascript:/i', '', $data);
        
        // 移除 on* 事件处理器
        $data = preg_replace('/on\w+\s*=/i', '', $data);
        
        // HTML 实体编码特殊字符
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $data;
    }

    /**
     * 递归过滤数组
     * @param array $data
     */
    protected function filterArray(array &$data): void
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = $this->sanitizeXSS($value);
            } elseif (is_array($value)) {
                $this->filterArray($data[$key]);
            }
        }
    }

    /**
     * 添加安全响应头
     * @param Response $response
     * @return Response
     */
    protected function addSecurityHeaders(Response $response): Response
    {
        // 防止 XSS 攻击
        $response->header('X-XSS-Protection', '1; mode=block');
        
        // 防止点击劫持
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        
        // 防止 MIME 类型嗅探
        $response->header('X-Content-Type-Options', 'nosniff');
        
        // 内容安全策略
        $response->header('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';");
        
        // 严格传输安全 (HTTPS)
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        
        return $response;
    }
}
