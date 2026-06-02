<?php
/**
 * 音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\model;

use think\Url;
use think\Model;

class ArtistType extends Model
{

    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;
    
    protected $append = ['url'];

    /** 自动过滤标题 */
    protected function setNameAttr($value)
    {
        return text_filter($value);
    }

    /** 自动获取别名 */
    protected function setAliasAttr($value, $data)
    {
        return get_pinyin($data['name'], $value, 3);
    }
    
    /** 自动设置别名 */
    protected function getUrlAttr($val, $data)
    {
        $alias = !empty($data['alias'])? $data['alias'] : $data['id'];
        return  Url::build('home/Artist/type', ['name' => $alias]);
    }

    /**
     * 获取类型列表
     * @param  array $param
     * @return array
     */
    public function getList($param = [])
    {
        $map['status'] = isset($param['status']) ? intval($param['status']) : 1;

        return parseTagsList($this, $param, $map);
    }
    
    public function getUrl($id = 0)
    {
        if (!(int)$id || !$data = $this->cache(true)->field('id,alias')->find($id)) {
            return null;
        }
        $alias = !empty($data['alias']) && !is_numeric($data['alias']) ? $data['alias'] : $data['id'];
        return  url('home/Artist/type', ['name' => $alias]);
    }
}
