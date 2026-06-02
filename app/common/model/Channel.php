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
use think\Request;

class Channel extends Model
{
    /**
     * @var bool 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * @var bool 自动写入状态吗
     */
    protected $insert = ['status' => 1];

    /** 自动过滤标题  */
    protected function setTitleAttr($value)
    {
        return text_filter($value);
    }
    
    /**
     * 获取导航树，指定分类则返回指定分类极其子导航，不指定则返回所有导航树
     * @param integer $id    导航ID
     * @param boolean $field 查询字段
     * @return array          分类树
     */
    public function getTree($id = 0, $field = [])
    {
        /* 获取当前分类信息 */
        if ($id) {
            $info = self::get($id)->getData();
        }
        /* 获取所有导航 */
        $map = ['status' => ['gt', -1]];

        $list = $this->where($map)
            ->field($field)
            ->order('sort')
            ->select();

        $list = list_to_tree($list, 'id', 'pid', '_child', $id);
        /* 获取返回数据 */
        if (isset($info)) { //指定分类则返回当前分类极其子分类
            $info['_child'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }
        return $info;
    }

    public function getList($map = [], $limit = 20, $field = '')
    {
        $map['status'] = 1;
        $field = $field ? $field : 'id,title,url,pid,target';
        $list = $this->where($map)
            ->field($field)
            ->cache('nav_list', 720000, 'limit_cache')
            ->order('sort')
            ->select();
        
        return $this->parseList($list);
    }
    
    /**
     * 设置导航高亮
     * @param array $list
     * @return array
     */
    protected function parseList($list = [])
    {
        $request = Request::instance();
        $routeInfo = $request->routeInfo();
        $path = $request->path();
        
        //定义绝对匹配
        $absolute = false;
        //定义相对匹配
        $relative = false;
        $newList = [];
        
        foreach ($list as $val) {
            $val = $val->toArray();
            $val['active'] = false;
            if ((0 !== strpos($val['url'], 'http')) && (0 !== strpos($val['url'], 'www'))) {
                $url = ltrim($val['url'], '/');
                if ($url == $path) {
                    $absolute = $val['id'];
                } elseif (!empty($path) && !empty($routeInfo)) {
                    $rule = $routeInfo['rule'];
                    $urlArr = explode('/', strtolower($url));
                    //路由绝对匹配
                    if ((isset($urlArr[1]) && isset($rule[1])) && ($urlArr[1] == $rule[1]) && ($urlArr[0] == $rule[0])) {
                        $absolute = $val['id'];
                    } elseif ($urlArr[0] == $rule[0]) { //控制器匹配 可能多个
                        $relative = $val['id'];
                    }
                }
                $val['url'] = url($val['url']);
            }
            $newList[$val['id']] = $val;
        }
        
        if ($absolute) {
            $newList[$absolute]['active'] = true;
        } elseif ($relative) {
            $newList[$relative]['active'] = true;
        }

        return $newList;
    }
}
