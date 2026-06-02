<?php
/**
 * 音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\validate;

use think\Validate;

/**
 * 艺人类型验证器
 * @package Channel
 */
class ArtistType extends Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require|max:30|unique:ArtistType',
        'alias'   => 'alphaNum|max:20',
    ];

    //提示信息
    protected $message  = [
        'name.require'  => '艺人类型不能为空',
        'name.max'      => '艺人类型最大长度30个字符',
        'alias.max'   => '艺人类型别名最大长度20个字符',
        'alias.alphaNum'   => '专辑类型只能为字母或数字',
    ];

    protected $scene = [
        'edit'  =>  ['name'=>'require|max:40']
    ];
}
