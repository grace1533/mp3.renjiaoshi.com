<?php
/**
 * 音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\api\behavior;

use think\Request;

class Auth
{
    //行为扩展的执行入口必须是run
    public function run(Request $request)
    {
        //halt($request->header());
        //halt(config('domain_list'));
        //abort(json(['code' => 40403, 'error' => 'unauthorized access'], 403));
    }
}