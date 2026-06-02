<?php

namespace app\middleware;

use think\Request;
use think\Response;
use think\facade\Session;

/**
 * API 认证中间件
 */
class ApiAuth
{
    /**
     * 不需要认证的路径
     * @var array
     */
    protected $except = [
        'api/v1.Auth/login',
        'api/v1.Auth/register',
    ];

    /**
     * 处理请求
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 检查是否在例外列表中
        $path = $request->pathinfo();
        if (in_array($path, $this->except)) {
            return $next($request);
        }

        // 获取 Authorization 头
        $authorization = $request->header('authorization');
        
        if (empty($authorization)) {
            return json([
                'code' => 401,
                'msg' => '未授权访问',
            ], 401);
        }

        // 解析 Token (Bearer Token)
        if (!preg_match('/Bearer\s+(.*)$/i', $authorization, $matches)) {
            return json([
                'code' => 401,
                'msg' => 'Token 格式错误',
            ], 401);
        }

        $token = $matches[1];

        // 验证 Token
        $user = $this->validateToken($token);
        
        if (!$user) {
            return json([
                'code' => 401,
                'msg' => 'Token 无效或已过期',
            ], 401);
        }

        // 将用户信息存入请求
        $request->user = $user;
        
        return $next($request);
    }

    /**
     * 验证 Token
     * @param string $token
     * @return array|false
     */
    protected function validateToken(string $token)
    {
        // 使用 JWT 或数据库验证 Token
        // 这里使用简单的实现
        
        try {
            // 解密 Token (实际项目建议使用 firebase/php-jwt)
            $decoded = json_decode(base64_decode(strtr($token, '-_', '+/')), true);
            
            if (!$decoded || !isset($decoded['uid']) || !isset($decoded['exp'])) {
                return false;
            }
            
            // 检查是否过期
            if ($decoded['exp'] < time()) {
                return false;
            }
            
            // 从数据库获取用户信息
            $user = \think\facade\Db::name('member')
                ->where('id', $decoded['uid'])
                ->where('status', 1)
                ->find();
            
            if (!$user) {
                return false;
            }
            
            // 移除敏感信息
            unset($user['password'], $user['salt']);
            
            return $user;
            
        } catch (\Exception $e) {
            return false;
        }
    }
}
