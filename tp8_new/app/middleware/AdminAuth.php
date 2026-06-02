<?php

namespace app\middleware;

use think\Request;
use think\Response;
use think\facade\Session;

/**
 * 管理员认证中间件
 */
class AdminAuth
{
    /**
     * 处理请求
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 检查用户是否登录
        $userId = $this->getLoginUserId();
        
        if ($userId <= 0) {
            if ($request->isAjax()) {
                return json([
                    'code' => 401,
                    'msg' => '请先登录',
                ], 401);
            }
            
            return redirect((string)url('admin/Login/index'));
        }

        // 检查是否为管理员
        if (!$this->isAdmin($userId)) {
            if ($request->isAjax()) {
                return json([
                    'code' => 403,
                    'msg' => '没有权限访问',
                ], 403);
            }
            
            abort(403, '没有权限访问');
        }

        // 将管理员 ID 存入请求
        $request->adminId = $userId;
        
        return $next($request);
    }

    /**
     * 获取登录用户 ID
     * @return int
     */
    protected function getLoginUserId(): int
    {
        $user = Session::get('admin_auth');
        
        if (empty($user)) {
            return 0;
        }
        
        // 验证签名
        $sign = $this->dataAuthSign($user);
        if (Session::get('admin_auth_sign') !== $sign) {
            return 0;
        }
        
        return $user['uid'] ?? 0;
    }

    /**
     * 检查是否为管理员
     * @param int $userId
     * @return bool
     */
    protected function isAdmin(int $userId): bool
    {
        $user = \think\facade\Db::name('member')
            ->where('id', $userId)
            ->where('status', 1)
            ->find();
        
        if (!$user) {
            return false;
        }
        
        // 检查用户组
        $groupAccess = \think\facade\Db::name('auth_group_access')
            ->where('uid', $userId)
            ->find();
        
        if (!$groupAccess) {
            return false;
        }
        
        $group = \think\facade\Db::name('auth_group')
            ->where('id', $groupAccess['group_id'])
            ->where('status', 1)
            ->find();
        
        if (!$group) {
            return false;
        }
        
        // 管理员组 ID 为 1
        return in_array($group['id'], [1]);
    }

    /**
     * 数据加密签名
     * @param array $data
     * @return string
     */
    protected function dataAuthSign(array $data): string
    {
        ksort($data);
        $code = http_build_query($data);
        return sha1($code);
    }
}
