<?php
/**
 * 音乐管理系统 PHP version 5.6+
 *
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @version   2.0.0
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017 JYmusic
 */

namespace app\common\model;

use think\Model;
use think\Url;

class Genre extends Model
{
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;
    
    /**
     * @var bool 自动写入状态吗
     */
    protected $insert = ['status' => 1];
    
    protected $append = ['url','url_new','url_new','url_fav','url_down','url_digg','url_hot','url_pos'];
    
    protected $alias;
    
    /** 自动过滤 */
    protected function setNameAttr($val)
    {
        return text_filter($val);
    }
    
    /** 自动过滤 */
    protected function setMetaTitleAttr($val)
    {
        return !empty($val) ? text_filter($val) : '';
    }
    
    /** 自动过滤 */
    protected function setKeywordsAttr($val)
    {
        return !empty($val) ? text_filter($val) : '';
    }
    
    /** 自动过滤 */
    protected function setDescriptionAttr($val)
    {
        return !empty($val) ? text_filter($val) : '';
    }
    
    /** 自动设置别名 */
    protected function setAliasAttr($val, $data)
    {
        return get_pinyin($data['name'], $val);
    }
    
    /** 自动设置排序 */
    protected function setSortAttr($val, $data)
    {
        if (!intval($val)) {
            $map['pid'] = empty($data['pid']) ? 0 : $data['pid'];
            $val = $this->where($map)->count() + 1;
        }
        return $val;
    }
    
    /** 自动设置url */
    protected function getUrlAttr($val, $data)
    {
        $alias = !empty($data['alias']) && !is_numeric($data['alias']) ? $data['alias'] : $data['id'];
        return Url::build('home/Genre/read', ['name' => $alias]);
    }
    
    /** 自动设置url */
    protected function getUrlNewAttr($val, $data)
    {
        return $this->getOrderUrl('new', $data);
    }
    
    /** 自动设置url */
    protected function getUrlFavAttr($val, $data)
    {
        $alias = !empty($data['alias']) && !is_numeric($data['alias']) ? $data['alias'] : $data['id'];
        return $this->getOrderUrl('fav', $data);
    }
    
    /** 自动设置url */
    protected function getUrlDownAttr($val, $data)
    {
        return $this->getOrderUrl('down', $data);
    }
    
    /** 自动设置url */
    protected function getUrlDiggAttr($val, $data)
    {
        return $this->getOrderUrl('digg', $data);
    }
    
    /** 自动设置url */
    protected function getUrlHotAttr($val, $data)
    {
        return $this->getOrderUrl('hot', $data);
    }
    
    /** 自动设置url */
    protected function getUrlPosAttr($val, $data)
    {
        return $this->getOrderUrl('pos', $data);
    }
    
    protected function getOrderUrl($type, $data)
    {
        $alias = !empty($data['alias']) && !is_numeric($data['alias']) ? $data['alias'] : $data['id'];
        return Url::build('home/Genre/order', ['name' => $alias, 'order' => $type]);
    }
    
    /**
     * 获取导航树，指定分类则返回指定分类极其子导航，不指定则返回所有导航树
     * @param integer $id    导航ID
     * @param string  $field
     * @param boolean $format
     * @return array
     */
    public function getTree($cid = 0, $field = null, $isFormat = false)
    {
        /* 获取当前分类信息 */
        if ($cid) {
            $info = self::get($cid)->getData();
            $map['id'] = $cid;
        }

        /* 获取所有导航 */
        $map = ['status' => ['gt', -1]];
        $list = $this->field($field)
            ->where($map)
            ->order('sort')
            ->cache('genre_list_'.$cid, 30*86400, 'genre')
            ->select();

        if ($isFormat) {
            $tree = new Tree();
            return  $tree->toFormatTree($list, 'name');
        }

        $list = list_to_tree($list, 'id', 'pid', '_child', $cid);
        /* 获取返回数据 */
        return isset($info)? $info['_child'] = $list : $list;
    }

    /**
     * 获取子孙ID
     * @param int $pid
     * @param bool $isAddPid
     * @return array
     */
    public function getChildIds($pid = 0, $isAddPid = true)
    {
        $ids = [];
        if (intval($pid)) {
            $ids = $this->where('status', 1)->where('pid', $pid)->column('id');
            if ($isAddPid) {
                $ids[] = $pid;
            }
        }
        return $ids;
    }
    
    public function simpleInfo($id)
    {
        $data = $this->field('id,name,alias')->cache(true)->find($id);
        
        return $data? $data->toArray() : ['id' =>0, 'name' => '其它', 'url' => url('home/Genre/index')];
    }

    /**
     * 列表
     * @return  mixed
     */
    public function getList($param = [])
    {
        $map['status'] = isset($param['status']) ? intval($param['status']) : 1;
        
        if (isset($param['alias'])) {
            $map['alias'] = $param['alias'];
        }
        return parseTagsList($this, $param, $map);
    }
    
    public function getUrl($id = 0)
    {
        if (!(int)$id || !$data = $this->cache(true)->field('id,alias')->find($id)) {
            return null;
        }
        $alias = !empty($data['alias']) && !is_numeric($data['alias']) ? $data['alias'] : $data['id'];
        return  url('home/Genre/read', ['name' => $alias]);
    }
}
