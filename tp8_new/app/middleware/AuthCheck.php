<?php

namespace app\middleware;

use think\Request;
use think\Response;
use think\facade\Session;

/**
 * 用户认证中间件
 */
class AuthCheck
{
    /**
     * 不需要登录的路径
     * @var array
     */
    protected $except = [
        'user/Login/index',
        'user/Login/register',
        'user/Register/index',
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

        // 检查用户是否登录
        $userId = $this->getLoginUserId();
        
        if ($userId <= 0) {
            if ($request->isAjax()) {
                return json([
                    'code' => 401,
                    'msg' => '请先登录',
                ], 401);
            }
            
            // 重定向到登录页
            return redirect((string)url('user/Login/index'));
        }

        // 将用户 ID 存入请求
        $request->userId = $userId;
        
        return $next($request);
    }

    /**
     * 获取登录用户 ID
     * @return int
     */
    protected function getLoginUserId(): int
    {
        $user = Session::get('user_auth');
        
        if (empty($user)) {
            return 0;
        }
        
        // 验证签名
        $sign = $this->dataAuthSign($user);
        if (Session::get('user_auth_sign') !== $sign) {
            return 0;
        }
        
        return $user['uid'] ?? 0;
    }

    /**
     * 数据加密签名
     * @param array $data
     * @return string
     */
    protected function dataAuthSign(array $data): string
    {
        // 关键字段排序
        ksort($data);
        
        // 序列化并生成签名
        $code = http_build_query($data);
        
        return sha1($code);
    }
}
