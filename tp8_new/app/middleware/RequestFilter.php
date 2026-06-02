<?php

namespace app\middleware;

use think\Request;
use think\Response;

/**
 * 请求过滤中间件
 */
class RequestFilter
{
    /**
     * 处理的请求方法白名单
     * @var array
     */
    protected $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD'];

    /**
     * 处理请求
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 检查请求方法
        $method = $request->method();
        if (!in_array($method, $this->allowedMethods)) {
            return response('Method Not Allowed', 405);
        }

        // 检查恶意 User-Agent
        if ($this->isMaliciousUserAgent($request->header('user-agent', ''))) {
            return response('Forbidden', 403);
        }

        // 检查请求频率限制 (简单实现)
        if ($this->isRateLimitExceeded($request)) {
            return response('Too Many Requests', 429);
        }

        // 清理 URL 中的非法字符
        $this->sanitizeUrl($request);

        return $next($request);
    }

    /**
     * 检查恶意 User-Agent
     * @param string $userAgent
     * @return bool
     */
    protected function isMaliciousUserAgent(string $userAgent): bool
    {
        $maliciousPatterns = [
            'sqlmap',
            'nikto',
            'nmap',
            'masscan',
            'havij',
            'python-requests',
            'curl/',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查请求频率限制
     * @param Request $request
     * @return bool
     */
    protected function isRateLimitExceeded(Request $request): bool
    {
        // 简单实现：同一 IP 在 1 秒内最多 60 次请求
        $ip = $request->ip();
        $key = 'rate_limit:' . $ip;
        
        $cache = \think\facade\Cache::store('file');
        $count = $cache->get($key, 0);
        
        if ($count > 60) {
            return true;
        }
        
        $cache->set($key, $count + 1, 1);
        
        return false;
    }

    /**
     * 清理 URL 中的非法字符
     * @param Request $request
     */
    protected function sanitizeUrl(Request $request): void
    {
        $pathinfo = $request->pathinfo();
        
        // 移除可能的路径遍历攻击
        $pathinfo = str_replace(['../', '..\\'], '', $pathinfo);
        
        // 移除 NULL 字节
        $pathinfo = str_replace(chr(0), '', $pathinfo);
    }
}
