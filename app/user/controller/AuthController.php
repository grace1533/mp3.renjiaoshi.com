<?php
/**
 * 音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\user\controller;

use think\Loader;
use think\Request;
use think\captcha\Captcha;
use app\common\api\User as UserApi;
use app\common\api\Email as EmailApi;
use app\common\controller\BaseController;

/**
 * Auth 用户认证控制器
 */
class AuthController extends BaseController
{
    /**
     * 显示用户登录页面
     * @return \think\Response
     */
    public function login()
    {
        if (is_login()) {
            $this->redirect('user/Account/index');
        }

        $title = '用户登录 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }

    /**
     * 显示注册用户
     * @return \think\Response
     */
    public function signup()
    {
        if (is_login()) {
            $this->redirect('user/Account/index');
        }

        if (!config('user_allow_register')) {
            $this->error(lang('signup_off'));
        }
        $title = '用户注册 - ' . config('web_site_title');
        return $this->fetch('', ['meta_title' => $title]);
    }

    /**
     * 显示当前登录用户
     * @return \think\Response
     */
    public function active()
    {
        $uid = is_login();
        $api = new UserApi();
        
        //检测自动登录
        if (!$uid && $autoUid = cookie('JYUUID')) {
            if ($uid = jy_decrypt($autoUid)) {
                $uid = $api->autoLogin($uid);
            }
        }

        if (intval($uid)) {
            $info               = $api->info($uid);
            $return['uid']      = $uid;
            $return['url']      = url('user/Index/read', ['uid' => $uid]);
            $return['avatar']   = $info['avatar'];
            $return['nickname'] = $info['nickname'];
            $return['location'] = $info['location'];
            unset($info);

            return $this->retSucc([
                'msg'    => '已登录',
                'uid'    => $uid,
                'result' => $return,
            ]);
        }
        return $this->retErr(40001);
    }

    /**
     * 用户登录
     * @return \think\response
     */
    public function loginUser()
    {
        if (is_login()) {
            return $this->retErr(40038);
        }

        $options = [
            'action'     => 'login_user',
            'limit_time' => 3600,
            'limit_num'  => 10,
            'lock_time'  => 3600,
        ];

        if (isOffSpite($options)) {
            return $this->retErr(40405);
        }

        $data = $this->request->param();
        //验证码
        if (config('verify_off') && !captcha_check($data['verify'])) {
            return $this->retErr(40035);
        }
        
        $validate = Loader::validate('UcenterMember');
        if (!$validate->scene('login')->check($data)) {
            return $this->retErr(40030, $validate->getError());
        }
    
        $api = new UserApi();
    
        if ($uid = $api->login($data['username'], $data['password'])) {
            if (isset($data['autologin']) && $data['autologin'] == 'on') {
                $api->remember($uid);
            }
            return $this->retSucc(['msg' => lang('login_success'), 'url' => url('user/Account/index')]);
        }
        $error = $api->getError();
        return $this->retErr(40502, $error);
    }

    /**
     * 注册一个新用户
     * @param  Request $request
     * @return \think\Response
     */
    public function createUser(Request $request)
    {
        if (!config('user_allow_register')) {
            abort(404, lang('signup_off'));
        }

        $options = [
            'action'     => 'create_user',
            'limit_time' => 3600,
            'limit_num'  => 10,
            'lock_time'  => 3600,
        ];
        if (isOffSpite($options)) {
            return $this->retErr(40405);
        }

        if (is_login()) {
            return $this->retErr(40038);
        }

        //api 内置验证
        $post   = $request->post();
        $result = $this->validate($post, 'UcenterMember.register');
        if (true !== $result) {
            return $this->retErr(40030, $result);
        }
        $api = new UserApi();
        //激活邮件
        if (config('send_activate_mail')) {
            //发送激活邮件
            $post['status'] = 2;
            $uid            = $api->register($post);
            if (!$uid) {
                $error = $api->getError();
                return $this->retErr(40501, $error);
            }

            if ($this->sendActiveEmail($uid, $post)) {
                return $this->retSucc(lang('active_email_success'));
            } else {
                return $this->retErr(40503);
            }
        } else {
            $uid = $api->register($post, true);
            if ($uid) {
                return $this->retSucc(['msg' => lang('signup_success'), 'url' => url('user/Account/index')]);
            }

            $error = $api->getError();
            return $this->retErr(40501, $error);
        }
    }

    /**
     * 用户激活
     * @param Request $request
     * @return \think\response
     */
    public function activate(Request $request)
    {
        if ($request->isPost()) {
            //验证
            $post   = $request->post();
            $result = $this->validate($post, 'UcenterMember.activate');

            if (true !== $result) {
                // 验证失败 输出错误信息
                return $this->retErr(40030, $result);
            }

            $map['email'] = $post['email'];
            $api          = new UserApi();
            $user         = $api->getUser($map);

            if (empty($user)) {
                return $this->retErr(40040);
            }

            if ($user->status == 1) {
                return $this->retErr(40041);
            }

            if ($user->status < 1) {
                return $this->retErr(40044);
            }

            if ($this->sendActiveEmail($user['id'], $user)) {
                return $this->retSucc(lang('active_email_success'));
            } else {
                return $this->retErr(40503);
            }
        } else {
            $title = '用户激活 - ' . config('web_site_title');
            return $this->fetch('', ['meta_title' => $title]);
        }
    }

    /**
     * 重置密码
     * @param Request $request
     * @return \think\Response
     */
    public function reset(Request $request)
    {
        if ($request->isPost()) {
            $options = [
                'action'     => 'rest_pwd',
                'limit_time' => 24 * 3600,
                'limit_num'  => 5,
                'lock_time'  => 24 * 3600,
            ];

            if (isOffSpite($options)) {
                return $this->retErr(40405);
            }

            $post   = $request->post();
            $result = $this->validate($post, 'UcenterMember.rest_pwd');

            if (true !== $result) {
                // 验证失败 输出错误信息
                return $this->retErr(40030, $result);
            }

            //检测验证码
            $api = new EmailApi();
            if (!$api->checkVcode($post['vcode'])) {
                $error = $api->getError();
                return $this->retErr(40034, $error);
            }

            $map['email'] = $post['email'];
            $userApi      = new UserApi();
            $user         = $userApi->getUser($map);
            if (empty($user)) {
                return $this->retErr(40045);
            }

            if (!$userApi->restPassword($user['id'], $post['password'])) {
                $error = $userApi->getError();
                return $this->retErr(40500, $error);
            } else {
                return $this->retSucc(['msg' => lang('pwd_rest_success'), 'url' => url('user/Auth/login')]);
            }
        } else {
            return $this->fetch();
        }
    }

    /**
     * 获取验证码
     * @param string $email
     * @return \think\response
     */
    public function getVcode($email)
    {
        $options = [
            'action'     => 'get_vcode',
            'limit_time' => 24 * 3600,
            'limit_num'  => 5,
            'lock_time'  => 24 * 3600,
        ];

        if (isOffSpite($options)) {
            return $this->retErr(40405);
        }

        //验证邮箱
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->retErr(40032);
        }

        $map['email'] = $email;
        $userApi      = new UserApi();
        $user         = $userApi->getUser($map);
        if (empty($user)) {
            return $this->retErr(40045);
        }
        $api = new EmailApi();
        if ($api->sendVcode($email)) {
            return $this->retSucc(lang('email_vcode_success'));
        } else {
            return $this->retErr(40504);
        }
    }

    /**
     * 用户确认激活账户
     * @param string $token
     */
    public function confirm($token = '')
    {
        if (empty($token)) {
            abort('404', lang(404));
        }

        $uid = jy_decrypt(text_filter($token));

        if (empty($uid)) {
            $this->error(lang('active_error'), url('user/Auth/activate'));
        }
        //设置用户激活
        $api = new UserApi();

        if ($api->setActive($uid)) {
            $this->success(lang('active_success'), url('user/Auth/login'));
        } else {
            $this->error($api->getError());
        }
    }

    /**
     * 退出登录
     * @return \think\response
     */
    public function logout()
    {
        $api = new UserApi();
        if ($api->logout()) {
            return $this->retSucc([
                'msg' => lang('logout_success'),
                'url' => url('home/Index/index'),
            ]);
        }
        return $this->retErr(40505);
    }

    /**
     * 输出验证码
     * @return \think\Response
     */
    public function captcha()
    {
        $captcha = new Captcha((array) config('captcha'));
        return $captcha->entry();
    }

    /**
     * 注册协议
     * @return mixed
     */
    public function pact()
    {
        $this->assign('meta_title', '用户注册协议');
        return $this->fetch();
    }

    /**
     * 验证字段
     * @param  Request $request
     * @param  string  $field
     * @return \think\Response
     */
    public function validation(Request $request, $field = null)
    {
        $fields = ['username', 'email', 'mobile', 'nickname'];
        $return = ['code' => 0, 'error' => ''];
        if (!in_array($field, $fields) || empty($field)) {
            return $this->retErr(40004);
        }

        $scene  = 'UcenterMember.validate_' . $field;
        $result = $this->validate([$field => $request->param($field)], $scene);
        if (true !== $result) {
            return $this->retErr(40030, $result);
        }
        return json($return);
    }

    /**
     * @param int $uid
     * @param $data
     */
    protected function sendActiveEmail($uid, $data)
    {
        $api   = new EmailApi();
        $link  = url('user/Auth/confirm', ['token' => jy_encrypt($uid, '', 24 * 60 * 60)], '', true);
        $title = '请激活你的帐号，完成注册';
        $body  = '<p>尊敬的用户：' . $data['username'] . '你好</p><br><br>';
        $body .= '<p>感谢您注册' . config('web_site_name') . '用户，请点击以下链接完成注册：<p><br><br>';
        $body .= '<p><a href="' . $link . '">' . $link . '</a></p><br><br>';
        $body .= '<p style="color: #cccccc;">如果您无法点击此链接，请将它复制到浏览器地址栏后访问</p>';
        $body .= '<p>为了保障您帐号的安全性，请在 48小时内完成激活，此链接将在您激活过一次后失效！</p>';
        $body .= '<p>如果不是本人操作，请忽略此邮件</p>';
        return $api->address($data['email'])->title($title)->html($body)->send();
    }
}
