<?php
/**
 * 音乐管理系统- 听客网 [未经授权严禁私自出售，否者承担法律责任]
 *
 * @author    战神~~巴蒂 <jyuucn@163.com>
 * @copyright 2014-2017 JYmusic
 * @license   http://jyuu.cn/license license
 * @version   GIT: 1.1
 */

namespace app\common\model;

use think\Model;
use think\Session;
use app\common\api\User;

/**
 * 用户模型
 *
 * @package Member
 */
class Member extends Model
{
    protected $append = ['url','group', 'music_url','album_url','follow_url', 'fans_url'];
    
    /** 自动过滤 */
    protected function setNicknameAttr($value)
    {
        return $value ? text_filter($value) : '未知';
    }

    /** 自动过滤 */
    protected function setLocationAttr($local, $data)
    {
        $local = isset($data['province']) ? $data['province'] : '';
        $local .= isset($data['city']) ? $data['city'] : '';
        $local .= isset($data['district']) ? $data['district'] : '';
        return text_filter($local);
    }

    /** 自动过滤 */
    protected function setProvinceAttr($value)
    {
        return $value ? text_filter($value) : ' ';
    }

    /** 自动过滤*/
    protected function setCityAttr($value)
    {
        return $value ? text_filter($value) : ' ';
    }

    /** 自动过滤*/
    protected function setDistrictAttr($value)
    {
        return $value ? text_filter($value) : ' ';
    }

    /** 设置生日*/
    protected function setBirthdayAttr($time)
    {
        return strtotime($time);
    }
    
    /** 过滤*/
    protected function setSignatureAttr($value)
    {
        return $value ? text_filter($value) : ' ';
    }

    /** 返回时间格式*/
    protected function getBirthdayAttr($birthday)
    {
        return date('Y-m-d', $birthday);
    }

    /** 格式化注册时间 */
    protected function getRegTimeAttr($time)
    {
        return date('Y-m-d H:i', $time);
    }

    /** 格式化登录时间 */
    protected function getlastLoginTimeAttr($time)
    {
        return date('Y-m-d H:i', $time);
    }

    /** 格式化注册IP */
    protected function getRegIpAttr($userIp)
    {
        return long2ip($userIp);
    }

    /** 格式化注册IP */
    protected function getLastLoginIpAttr($userIp)
    {
        return long2ip($userIp);
    }
    
    /** 自动设置url */
    protected function getAvatarAttr($val, $data)
    {
        $val = !is_numeric($data['avatar']) ? $data['avatar'] : '';
        return get_cover_url($val, 'avatar');
    }
    
    /** 自动设置用户组 */
    protected function getGroupAttr($val, $data)
    {
        return $this->getGroup($data['uid']);
    }

        /** 自动设置url */
    protected function getUrlAttr($val, $data)
    {
        return url('user/Index/read', ['uid' => $data['uid']]);
    }
    
    /**设置URL */
    protected function getMusicUrlAttr($val, $data)
    {
        return url('user/Index/music', ['uid' => $data['uid']]);
    }
    
    /**设置URL */
    protected function getAlbumUrlAttr($val, $data)
    {
        return url('user/Index/album', ['uid' => $data['uid']]);
    }
    
    /**设置URL */
    protected function getFollowUrlAttr($val, $data)
    {
        return url('user/Index/follow', ['uid' => $data['uid']]);
    }
    
    /**设置URL */
    protected function getFansUrlAttr($val, $data)
    {
        return url('user/Index/fans', ['uid' => $data['uid']]);
    }
    
    /**设置URL */
    protected function getVisitorUrlAttr($val, $data)
    {
        return url('user/Index/fav', ['uid' => $data['uid']]);
    }
    
    /**设置URL */
    protected function getDownUrlAttr($val, $data)
    {
        return url('user/Index/down', ['uid' => $data['uid']]);
    }
    
    /**
    protected function getSongsAttr($val, $data)
    {
        return db('songs')->where(['status' => 1, 'up_uid' => $data['uid']])->count();
    }
    
    protected function getAlbumsAttr($val, $data)
    {
        return db('album')->where(['status' => 1, 'add_uid' => $data['uid']])->count();
    }
    
    protected function getFollowsAttr($val, $data)
    {
        return db('user_follow')->where(['uid' => $data['uid']])->count();
    }
    
    protected function getFansAttr($val, $data)
    {
        return db('user_follow')->where(['follow_uid' => $data['uid']])->count();
    }
    */

    /**
     * 登录指定用户
     * @param integer $uid 用户ID
     * @return boolean ture-登录成功，false-登录失败
     */
    public function login($uid)
    {
        /* 检测是否在当前应用注册 */
        $user = $this->get($uid);
        if (is_null($user)) {
            //未注册
            /* 在当前应用中注册用户 */
            $info = (new User())->getUser(['id' => $uid]);
            $user = [
                'uid'      => $uid,
                'nickname' => $info->username,
                'reg_time' => time(),
                'reg_ip'   => get_client_ip(1),
                'status'   => 1,
            ];
            if (!$this->save($user)) {
                $this->error = '前台用户信息注册失败，请重试！';
                return false;
            }
        } else {
            $user = $user->toArray();
        }

        if (1 != $user['status']) {
            $this->error = '用户未激活或已禁用！'; //应用级别禁用
            return false;
        }

        //记录行为
        //action_log('user_login', 'member', $uid, $uid);
        /* 登录用户 */
        return $this->_autoLogin($user);
    }

    /**
     * 注销当前用户
     * @return bool
     */
    public function logout()
    {
        Session::delete('user_auth');
        Session::delete('user_auth_sign');
        cookie('dotcom_user', null);
        cookie('JYUUID', null);
        return true;
    }

    /**
     * 自动登录用户
     * @param array $user 用户信息数组
     * @return boolean
     */
    private function _autoLogin($user)
    {
        /* 更新登录信息 */
        $data = [
            'login'           => ['exp', '`login`+1'],
            'last_login_time' => time(),
            'last_login_ip'   => get_client_ip(1),
        ];

        
        if ( $this->save($data, ['uid' => $user['uid']])){
            /* 记录登录SESSION和COOKIES */
            $auth = [
                'uid'             => $user['uid'],
                'username'        => $user['nickname'],
                'last_login_time' => time(),
            ];
            //储存用户session
            Session::set('user_auth', $auth);
            Session::set('user_auth_sign', data_auth_sign($auth));
            return true;
        }
        return false;
    }
    
    /**
     * 更新用户参数数量
     * @param $uid
     * @param bool $isInc
     */
    public static function updateAttrNum($field, $uid, $isInc = true)
    {
        $user =  self::get($uid);
        if ($user) {
            $user->$field = $isInc? ($user->$field+1) : ($user->$field-1);
            if ($user->$field > -1) {
                $user->save();
            }
        }
    }
    
    /**
     * 获取列表
     * @param  array   $param   条件
     * @return array
     */
    public function getList($param = [])
    {
        $map['status'] = isset($param['status']) ? intval($param['status']) : 1;
        if (isset($param['uid'])) {
            if (is_array($param['uid'])) {
                $param['uid'] = ['in', $param['uid']];
            } elseif (strpos($param['uid'], ',')) {
                $param['uid'] = ['in', text_filter($param['uid'])];
            }
            $map['uid'] = $param['uid'];
        } else {
            $map['uid'] = ['neq', 1];
        }

        if (!isset($param['order'])) {
            $param['order'] = 'uid desc';
        }
        
        if (isset($param['is_vip'])) {
            //暂时不写
        }

        if (isset($param['is_musician'])) {
            $map['is_musician'] = 1;
        }
        
        if (isset($param['sex'])) {
            $map['sex'] = $param['sex'];
        }
        
        if (isset($param['pos'])) {
            if ($pos = intval($param['pos'])) {
                $map[] = ['exp', "position & {$pos} = {$pos}"];
            } elseif ($param['pos'] == "*" || $param['pos'] == "all") {
                $map['position'] = ["neq", 0];
            }
        }

        return parseTagsList($this, $param, $map);
    }
    
    /**
     * 获取用户所在用户组
     * @param  integer $uid
     * @return mixed
     */
    public function getGroup($uid)
    {
        return (new MemberGroup())->getUserIn($uid);
    }

    /**
     * 获取用户粉丝
     * @param  array  $param   条件
     * @return array
     */
    public function getFans($param = [])
    {
        $limit = isset($param['limit']) ? intval($param['limit']) : 20;
        if (isset($param['page'])) {
            $page = request()->param('page', 1);
            $limit = (($page-1)*$limit) . ',' . $limit;
        }
        
        $ids = db('user_follow')
            ->where(['follow_uid' => $param['uid']])
            ->limit($limit)
            ->column('uid');

        if (empty($ids)) {
            return null;
        }
        
        $map['uid'] = ['in',  $ids];
        $param['order'] = 'uid desc';

        return parseTagsList($this, $param, $map);
    }

    /**
     * 获取用户 关注
     * @param  array  $param   条件
     * @return array
     */
    public function getFollow($param = [])
    {
        $limit = isset($param['limit']) ? intval($param['limit']) : 20;
        if (isset($param['page'])) {
            $page = request()->param('page', 1);
            $limit = (($page-1)*$limit) . ',' . $limit;
        }

        $ids = db('user_follow')
            ->where(['uid' => $param['uid']])
            ->limit($limit)
            ->column('follow_uid');

        if (!$ids) {
            return null;
        }
        
        $map['uid'] = ['in',  $ids];
        $param['order'] = 'uid desc';
        return parseTagsList($this, $param, $map);
    }

    /**
     * 获取访客信息
     * @return [type] [description]
     */
    public function getVisitors($param = [])
    {
        $uid = intval($param['uid']);
        
        if (!$uid) {
            return null;
        }

        $visitors = file_cache('visitor_' . $uid);
        
        if (empty($visitors)) {
            return null;
        }

        $limit = isset($param['limit']) ? intval($param['limit']) : 20;
        if (isset($param['page'])) {
            $page = request()->param('page', 1);
            $page = ($page-1) * $limit;
            $visitors = array_slice($page, $limit);
        }
        
        return $visitors;
    }
    
    public function getTrend($param = []) {}
}
