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

class ChargeController extends UserController
{
    /**
     * 充值首页
     * @return \think\Response
     */
    public function index()
    {
        $title = '账户充值 - ' . config('web_site_title');
        $this->assign('meta_title', $title);
        return $this->fetch();
    }

    public function history()
    {
        $title = '充值记录 - ' . config('web_site_title');
        $this->assign('meta_title', $title);
        return $this->fetch();
    }
}
