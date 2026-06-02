<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;

/**
 * 首页控制器
 */
class Index extends BaseController
{
    /**
     * 首页
     * @return \think\response\View
     */
    public function index()
    {
        // 获取推荐音乐
        $recommendMusic = Db::name('songs')
            ->where('status', 1)
            ->order('play_num', 'desc')
            ->limit(10)
            ->select();

        // 获取最新专辑
        $newAlbums = Db::name('album')
            ->where('status', 1)
            ->order('create_time', 'desc')
            ->limit(6)
            ->select();

        // 获取热门歌手
        $hotArtists = Db::name('artist')
            ->where('status', 1)
            ->order('fans_num', 'desc')
            ->limit(8)
            ->select();

        View::assign([
            'recommend_music' => $recommendMusic,
            'new_albums' => $newAlbums,
            'hot_artists' => $hotArtists,
        ]);

        return $this->fetch('index/index');
    }

    /**
     * 搜索
     * @return \think\response\Json
     */
    public function search()
    {
        $keyword = $this->request->param('keyword', '');
        
        if (empty($keyword)) {
            return $this->error('请输入搜索关键词');
        }

        // 防止 SQL 注入 - 使用参数化查询
        $music = Db::name('songs')
            ->where('status', 1)
            ->where(function($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                      ->whereOr('singer', 'like', "%{$keyword}%");
            })
            ->limit(20)
            ->select();

        return $this->success($music);
    }
}
