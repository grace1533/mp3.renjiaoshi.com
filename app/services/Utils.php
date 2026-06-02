<?php
/**
 * 音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */
namespace app\services;

/**
 * 一些常用函数
 * @package app\services\WebInfo
 */
class Utils
{
    /**
     * 格式化字节大小
     * @param  number $size      字节数
     * @param  string $delimiter 数字和单位分隔符
     * @return string            格式化后的带单位的大小
     */
    public static function formatBytes($size, $delimiter = '')
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $size >= 1024 && $i < 5; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $delimiter . $units[$i];
    }

    /**
     * 时间差
     * @param $time
     * @return float|int|string
     */
    public static function beforeTime($time)
    {
        $time  = is_numeric($time) ? $time : strtotime($time);
        $mtime = time() - $time; //获取秒

        $time1 = $mtime / 60; //转化为分钟
        if ($time1 < 1) {
            $time1 = '刚刚';
        } elseif ($time1 < 60) {
            $time1 = floor($time1) . '分钟前';
        } elseif ($time1 >= 60 && $time1 < (60 * 24)) {
            $time1 = floor($time1 / 60) . '小时前';
        } elseif ($time1 >= 60 * 24 && $time1 < (60 * 24 * 5)) {
            $time1 = floor($time1 / 60 / 24) . '天前';
        } else {
            $time1 = date('y年m月d日', $time);
        }

        return $time1;
    }
    
    /**
     * 系统加密方法
     * @param string $data 要加密的字符串
     * @param string $key  加密密钥
     * @param int $expire  过期时间 单位 秒
     * @return string
     */
    public static function encrypt($data, $key = '', $expire = 0)
    {
        $key    = md5(empty($key) ? UC_AUTH_KEY : $key);
        $data   = base64_encode($data);
        $def    = 0;
        $len    = strlen($data);
        $keyLen = strlen($key);
        $char   = '';
        for ($i = 0; $i < $len; $i++) {
            if ($def == $keyLen) {
                $def = 0;
            }
            $char .= substr($key, $def, 1);
            $def++;
        }
        
        $str = sprintf('%010d', $expire ? $expire + time() : 0);
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
        }
        return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
    }
    
    /**
     * 系统解密方法
     * @param  string $data 要解密的字符串 （必须是jy_encrypt方法加密的字符串）
     * @param  string $key  加密密钥
     * @return string
     */
    public static function decrypt($data, $key = '')
    {
        $key  = md5(empty($key) ? UC_AUTH_KEY : $key);
        $data = str_replace(array('-', '_'), array('+', '/'), $data);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data   = base64_decode($data);
        $expire = substr($data, 0, 10);
        $data   = substr($data, 10);
        
        if ($expire > 0 && $expire < time()) {
            return '';
        }
        $def    = 0;
        $len    = strlen($data);
        $keyLen = strlen($key);
        $char   = $str   = '';
        
        for ($i = 0; $i < $len; $i++) {
            if ($def == $keyLen) {
                $def = 0;
            }
            
            $char .= substr($key, $def, 1);
            $def++;
        }
        
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return base64_decode($str);
    }
    
    /**
     * 对查询结果集进行排序
     * @param array  $list   查询结果
     * @param string $field  排序的字段名
     * @param string $sortby 排序类型 asc正向排序 desc逆向排序 nat自然排序
     * @return array|boolean
     */
    public static function listSort($list, $field, $sortby = 'asc')
    {
        if (is_array($list)) {
            $refer = $resultSet = array();
            foreach ($list as $i => $data) {
                $refer[$i] = &$data[$field];
            }
            switch ($sortby) {
                case 'asc': // 正向排序
                    asort($refer);
                    break;
                case 'desc': // 逆向排序
                    arsort($refer);
                    break;
                case 'nat': // 自然排序
                    natcasesort($refer);
                    break;
            }
            foreach ($refer as $key => $val) {
                $resultSet[] = &$list[$key];
            }
            return $resultSet;
        }
        return false;
    }
    
    /**
     * 把返回的数据集转换成Tree
     *
     * @param object $list  要转换的数据集
     * @param string $pk    主键字段
     * @param string $pid   parent标记字段
     * @param string $child 输出child字段
     * @param int    $root  root标记字段
     *
     * @return array
     */
    public static function listToTree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = [];
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                //$data   = $val->data();
                if (is_object($data)) {
                    $data       = $data->toArray();
                    $list[$key] = $data;
                }
                
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent           = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }
    
    /**
     * 将list_to_tree的树还原成列表
     *
     * @param array  $tree  原来的树
     * @param string $child 孩子节点的键
     * @param string $order 排序显示的键，一般是主键 升序排列
     * @param array  $list  过渡用的中间数组，
     *
     * @return array 返回排过序的列表数组
     */
    public static function treeToList($tree, $child = '_child', $order = 'id', &$list = [])
    {
        if (is_array($tree)) {
            foreach ($tree as $key => $value) {
                $reffer = $value;
                if (isset($reffer[$child])) {
                    unset($reffer[$child]);
                    self::treeToList($value[$child], $child, $order, $list);
                }
                $list[] = $reffer;
            }
            $list = self::listSort($list, $order,  'asc');
        }
        return $list;
    }

    /**
     * 输出安全的html
     * @param  string $html 
     * @return string
     */
    public function filter($html = '')
    {

    }

}
