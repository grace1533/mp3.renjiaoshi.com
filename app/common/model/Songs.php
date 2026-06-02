<?php
/**
 * 音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 [jyuucn@163.com]
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\common\model;

use think\Model;
use think\Url;

/**
 * @package Song 讲道模型
 * @author 战神~~巴蒂 <jyuucn@163.com>
 */
class Songs extends Model
{
    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    protected $auto = ['coin', 'score', 'up_uname'];
    
    protected $update = ['position'];

    protected $append = ['url', 'down_url', 'user_url', 'artist_url', 'album_url', 'genre_url', 'genre_name'];

    protected $genre;

    /** 过滤名称 */
    protected function setNameAttr($val, $data)
    {
        return str_replace("\\", "", text_filter($val));
    }

    /** 设置艺人名称 */
    protected function setArtistNameAttr($value, $data)
    {
        if ($aid = intval($data['artist_id'])) {
            $value = (new Artist())->where('id', $aid)->value('name');
        }
        return $value;
    }

    /** 设置专辑 */
    protected function setAlbumNameAttr($value, $data)
    {
        if ($aid = intval($data['album_id'])) {
            $value = (new Album())->where('id', $aid)->value('name');
        }
        return $value;
    }

    /** 设置用户 */
    protected function setUpUidAttr($val)
    {
        return intval($val) ? $val : 1;
    }

    /** 设置用户 */
    protected function setUpUnameAttr($value, $data)
    {
        $uid = intval($data['up_uid']) ? $data['up_uid'] : 1;
        return (new Member())->where('uid', $uid)->value('nickname');
    }

    /** 设置标签 */
    protected function setTagsAttr($value)
    {
        if (empty($value)) {
            $tags  = explode(',', $value);
            $model = new Tags();
            foreach ($tags as $val) {
                $map = ['name' => $val];
                if (!$model->where('name', $val)->find()) {
                    $model->save($map);
                }
            }
        }
        return $value;
    }
    
    /** 过滤封面 */
    protected function setCoverIdAttr($value)
    {
        return is_numeric($value) ? $value : 0;
    }
    
    /** 过滤封面 */
    protected function setCoverUrlAttr($value)
    {
        return !empty($value) ? text_filter($value) : '';
    }

    /** 设置推荐位 */
    protected function setPositionAttr($value)
    {
        $pos = 0;
        if (is_array($value)) {
            //将各个推荐位的值相加
            foreach ($value as $val) {
                $pos += $val;
            }
        }
        return intval($pos);
    }

    /** 设置金币 */
    protected function setCoinAttr($val, $data)
    {
        return !empty($data['extend']['down_rule']['coin']) ? intval($data['extend']['down_rule']['coin']) : 0;
    }

    /** 设置积分 */
    protected function setScoreAttr($val, $data)
    {
        return !empty($data['extend']['down_rule']['score']) ? intval($data['extend']['down_rule']['score']) : 0;
    }

    /** 过滤原唱 */
    protected function setSignAttr($value)
    {
        return !empty($value) ? text_filter($value) : '';
    }

    /** 过滤作词 */
    protected function setLyricsAttr($value)
    {
        return !empty($value) ? text_filter($value) : '';
    }

    /** 过滤作曲 */
    protected function setComposerAttr($value)
    {
        return !empty($value) ? text_filter($value) : '';
    }

    /** 过滤编曲 */
    protected function setMidiAttr($value)
    {
        return !empty($value) ? text_filter($value) : '';
    }

    /** 过滤混编 */
    protected function setMixAttr($value)
    {
        return !empty($value) ? text_filter($value) : '';
    }

    /** 过滤混编 */
    protected function setStatusAttr($value)
    {
        return is_numeric($value) ? $value : 1;
    }
    
    /** 获取标签 */
    protected function getTagsAttr($value)
    {
        if (!empty($value)) {
            //$tags = explode(',', $value);
            $model = new Tags();
            $list  = $model->where('name', 'in', $value)->field('id,name,alias')->select();

            return $list;
        }
        return $value;
    }

    /** 自动追加url */
    protected function getUrlAttr($val, $data)
    {
        return Url::build('home/Music/read', ['id' => $data['id']]);
    }

    /** 自动追加url */
    protected function getDownUrlAttr($val, $data)
    {
        return Url::build('home/Music/down', ['id' => $data['id']]);
    }

    /** 自动追加url */
    protected function getUserUrlAttr($val, $data)
    {
        return isset($data['up_uid']) ? Url::build('user/Index/read', ['uid' => $data['up_uid']]) : '';
    }

    /** 自动追加url */
    protected function getArtistUrlAttr($val, $res)
    {
        return isset($res['artist_id']) ? Url::build('home/Artist/read', ['id' => $res['artist_id']]) : '';
    }

    /** 自动追加url */
    protected function getAlbumUrlAttr($val, $res)
    {
        return isset($res['album_id']) ? Url::build('home/Album/read', ['id' => $res['album_id']]) : '';
    }

    /** 自动追加url */
    protected function getGenreUrlAttr($val, $res)
    {
        if (isset($res['genre_id'])) {
            $this->genre = $this->genre ? $this->genre : (new Genre())->simpleInfo($res['genre_id']);
            $val         = $this->genre['url'];
        }
        return $val;
    }

    /** 自动追加url */
    protected function getGenreNameAttr($val, $res)
    {
        if (isset($res['genre_id'])) {
            $this->genre = $this->genre ? $this->genre : (new Genre())->simpleInfo($res['genre_id']);
            $val         = $this->genre['name'];
        }
        return $val;
    }

    /** 获取封面 */
    protected function getCoverUrlAttr($val)
    {
        if (!empty($val) && (0 === strpos($val, 'http'))) {
            return $val;
        }
        return get_cover_url($val);
    }

    /** 获取推荐位 */
    protected function getPositionAttr($val)
    {
        $position = [];
        if ($val > 0) {
            $pos     = config('music_position');
            $posIcon = config('music_position_font_icon');
            foreach ($pos as $key => $item) {
                if ($key & $val) {
                    $position[$key]['id']     = $key;
                    $position[$key]['name']     = $item;
                    $position[$key]['font_icon'] = $posIcon[$key];
                }
            }
        }

        return $position;
    }

    /**
     * 更新标签
     * @return void
     */
    public function updateTags()
    {
        $data = $this->getData();
        if (!empty($data['tags'])) {
            (new Tags())->saveSongs($data['tags'], $data['id']);
        }
    }

    /**
     * 检测当前更新的音乐是否需要更新对应名称
     * @param  array  $data
     * @param  array  $song
     * @return array
     */
    public function checkUpdateField($data, $song = [])
    {
        if (empty($song)) {
            $song = $this->where('id', $data['id'])
                ->field('artist_id,album_id,genre_id,tags')
                ->find();
        }

        if (isset($data['artist_id']) && ($song['artist_id'] != $data['artist_id'])) {
            $data['artist_name'] = (new Artist())->where('id', $data['artist_id'])->value('name');
        }
        if (isset($data['album_id']) && ($song['album_id'] != $data['album_id'])) {
            $data['album_name'] = (new Album())->where('id', $data['album_id'])->value('name');
        }
        return $data;
    }

    /**
     * 批量更新的音乐是否需要更新对应名称
     * @param  array  $data
     * @param  string|array  $ids
     * @return mixed
     */
    public function bulkUpdate($data, $ids)
    {
        if (isset($data['artist_id']) && intval($data['artist_id'])) {
            $data['artist_name'] = (new Artist())->where('id', $data['artist_id'])->value('name');
        }
        if (isset($data['album_id']) && intval($data['album_id'])) {
            $data['album_name'] = (new Album())->where('id', $data['album_id'])->value('name');
        }
        return $this->where('id', 'in', $ids)->update($data);
    }

    /**
     * 清空回收站讲道
     */
    public function clear($ids)
    {
        $list = self::with('extend')->field('id,name,cover_url,cover_id')
            ->where('status', -1)
            ->where('id', 'in', $ids)
            ->select();

        if (empty($list)) {return true;}

        foreach ($list as $song) {
            //判断讲道是否是本地
            $extend = $song->extend;
            if (isset($extend) && $extend->server_id == 0) {
                $listenId = $extend->listen_file_id;
                $downId   = $extend->down_file_id;
                if ($listenId) {
                    $this->removeFile($listenId, 'listen');
                }
                if ($downId !== $listenId) {
                    $this->removeFile($listenId, 'down');
                }
                $extend->delete();
            }
            $song->delete();   
        }
        return true;
    }

    /**
     * 删除讲道时移除本地文件
     * @param int $fileId
     * @param string $type
     * @return bool
     */
    protected function removeFile($fileId = 0, $type = 'listen')
    {
        if (!$fileId) {return true;}
        if ('listen' == $type) {
            $isHave = (new SongsExtend())->where('listen_file_id', $fileId)
                ->column('mid');
            if (count($isHave) > 1) {
                return true;
            }
        } elseif ('down' == $type) {
            $isHave = (new SongsExtend())->where('down_file_id', $fileId)
                ->column('mid');
            if (count($isHave) > 1) {
                return true;
            }
        } else {
            $isHave = $this->where('cover_id', $fileId)
                ->column('id');
            if (count($isHave) > 1) {
                return true;
            }
        }
        //判断当前文件是否被其他引用
        $fileModel = 'cover' == $type ? new Picture() : new File();
        $filePath  = $fileModel->where('id', $fileId)->value('filepath');

        if ($filePath) {
            $filePath = ROOT_PATH . ltrim($filePath, '/');
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $fileModel->where('id', $fileId)->delete();
        }
        return true;
    }

    /**
     * 获取讲道扩展
     */
    public function extend()
    {
        return $this->hasOne('SongsExtend', 'mid');
    }

    /**
     * 获取讲道扩展
     */
    public function sub()
    {
        return $this->hasOne('SongsExtend', 'mid')
            ->field('mid,server_id,listen_url,play_time,down_rule,listen_file_size,listen_bitrate,lrc');
    }

    /** 获取用户信息*/
    public function user()
    {
        return $this->hasOne('Member', 'uid', 'up_uid')
            ->field('uid,avatar,songs,albums,nickname,location');
    }

    /**
     * 获取单个讲道基础数据
     * @param int $sid
     * @param string $field
     * @return array
     */
    public function getInfo($sid = 0, $field = '')
    {
        if (!intval($sid)) {
            return null;
        }
        $list = cache('songs_info_list');
        if (isset($list[$sid])) {
            //已缓存，直接使用
            return $list[$sid];
        }
        $field = empty($field) ? 'id,name,listens,favtimes,coin,score,download,up_uid,up_uname,cover_url,album_id,album_name,artist_id,artist_name,genre_id' : '';

        $map['id']     = $sid;
        $map['status'] = 1;

        $info = self::field($field)
            ->where($map)
            ->with(['sub' => function ($query) {
                $query->field('mid,server_id,listen_url,play_time,listen_file_size,listen_bitrate,lrc');
            }])
            ->find();

        if ($info) {
            $list[$sid] = $info->toArray();
            $count      = count($list);
            $max        = config('data_max_cache_limit');
            while ($count-- > $max) {
                array_shift($list);
            }
            cache('songs_info_list', $list);
        }
        return $info;
    }

    /**
     * 获取讲道列表
     * @param  array $param 条件
     * @return array
     */
    public function getList($param = [])
    {
        $map['status'] = isset($param['status']) ? intval($param['status']) : 1;

        if (!isset($param['field'])) {
            $param['field'] = [
                'update_time,status,sing,rank_id,lyrics,comment,composer,midi,mix,likes',
                true,
            ];
        }
        if (isset($param['album'])) {
            $map['album_id'] = strpos($param['album'], ',') ? ['in', text_filter($param['album'])] : intval($param['album']);
        }
        if (isset($param['artist'])) {
            $map['artist_id'] = strpos($param['artist'], ',') ? ['in', text_filter($param['artist'])] : intval($param['artist']);
        }
        if (isset($param['uid'])) {
            $map['up_uid'] = strpos($param['uid'], ',') ? ['in', text_filter($param['uid'])] : intval($param['uid']);
        }

        if (isset($param['pos'])) {
            $pos = intval($param['pos']);
            if ($pos) {
                $map[] = ['exp', "position & {$pos} = {$pos}"];
            } elseif ($param['pos'] == "*" || $param['pos'] == "all") {
                $map['position'] = ["neq", 0];
            }
        }

        if (isset($param['coin'])) {
            $map['coin'] = $param['coin'];
        }

        if (isset($param['genre']) || isset($param['cate'])) {
            $ids             = isset($param['genre']) ? $param['genre'] : $param['genre'];
            $map['genre_id'] = strpos($ids, ',') ? ['in', text_filter($ids)] : ['in', (new Genre)->getChildIds($ids)];
        }
        if (isset($param['tag'])) {
            $map['id'] = ['in', (new Tags())->getSongIds(text_filter($param['tag']), $param)];
        }

        if (isset($param['rank'])) {
            $rankParam = (new Ranks())->setSongsMap(text_filter($param['rank']));
            $param = empty($rankParam) ? $param : array_merge($param, $rankParam);
            if (isset($rankParam['rank_id'])) {
                $map['rank_id'] = intval($rankParam['rank_id']);
            }
        }
        return parseTagsList($this, $param, $map);
    }

    /**
     * 获取下载列表
     * @param  array $param
     * @return array
     */
    public function getAuditList($param = [])
    {
        $param['status'] = 2;
        return $this->getList($param);
    }

    /**
     * 获取下载列表
     * @param  array $param
     * @return array
     */
    public function getDownList($param = [])
    {
        return $this->getSubList($param, 'user_down', 'songs_id');
    }

    /**
     * 获取下载列表
     * @param  array $param
     * @return array
     */
    public function getFavList($param = [])
    {
        return $this->getSubList($param, 'songs_fav', 'songs_id');
    }

    protected function getSubList($param = [], $table, $field)
    {
        $limit = isset($param['limit']) ? intval($param['limit']) : 20;
        if (isset($param['page'])) {
            $page  = request()->param('page', 1);
            $limit = (($page - 1) * $limit) . ',' . $limit;
        }

        $where = [];
        if (isset($param['uid'])) {
            $where['uid'] = $param['uid'];
        }

        $sids = db($table)->where($where)->limit($limit);

        if (isset($param['cache'])) {
            $sids = $sids->cache(intval($param['cache']))->column($field);
        } else {
            $sids = $sids->column($field);
        }
        if (!$sids) {
            return null;
        }
        $map['id'] = ['in', $sids];
        return parseTagsList($this, $param, $map);
    }
}
