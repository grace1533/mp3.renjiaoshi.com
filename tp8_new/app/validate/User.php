<?php

namespace app\validate;

use think\Validate;

/**
 * 用户验证器
 */
class User extends Validate
{
    /**
     * 定义验证规则
     * @var array
     */
    protected $rule = [
        'username' => 'require|length:4,20|alphaNum',
        'password' => 'require|length:6,20',
        'email' => 'require|email',
        'mobile' => 'mobile',
        'nickname' => 'length:2,30',
        'captcha' => 'require|captcha',
    ];

    /**
     * 定义错误消息
     * @var array
     */
    protected $message = [
        'username.require' => '用户名不能为空',
        'username.length' => '用户名长度必须在 4-20 位之间',
        'username.alphaNum' => '用户名只能是字母和数字',
        'password.require' => '密码不能为空',
        'password.length' => '密码长度必须在 6-20 位之间',
        'email.require' => '邮箱不能为空',
        'email.email' => '邮箱格式不正确',
        'mobile.mobile' => '手机号格式不正确',
        'nickname.length' => '昵称长度必须在 2-30 位之间',
        'captcha.require' => '验证码不能为空',
        'captcha.captcha' => '验证码不正确',
    ];

    /**
     * 定义验证场景
     * @var array
     */
    protected $scene = [
        'register' => ['username', 'password', 'email', 'captcha'],
        'login' => ['username', 'password', 'captcha'],
        'profile' => ['nickname', 'email', 'mobile'],
        'change_password' => ['password'],
    ];

    /**
     * 自定义验证 - 检查用户名是否存在
     * @param mixed $value 值
     * @param mixed $data 数据
     * @return bool|string
     */
    public function checkUsername($value, $data = [])
    {
        $exists = \think\facade\Db::name('member')
            ->where('username', $value)
            ->find();
        
        return $exists ? '用户名已存在' : true;
    }

    /**
     * 自定义验证 - 检查邮箱是否存在
     * @param mixed $value 值
     * @param mixed $data 数据
     * @return bool|string
     */
    public function checkEmail($value, $data = [])
    {
        $exists = \think\facade\Db::name('member')
            ->where('email', $value)
            ->find();
        
        return $exists ? '邮箱已被注册' : true;
    }
}
