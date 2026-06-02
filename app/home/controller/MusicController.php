<?php
/**
 * 音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\home\controller;

use app\common\model\Songs;
use app\services\Actions;

class MusicController extends HomeController
{

    /**
     * 讲道首页
     */
    public function index()
    {
        abort(404, '页面不存在！');
    }

    /**
     * @param int $id
     * @return \think\Response
     */
    public function read($id = 0)
    {
        $this->setSong($id);
        return $this->fetch('detail');
    }

    /**
     * 讲道下载页面
     * @param int $id
     * @return \think\Response
     */
    public function down($id = 0)
    {
        $this->setSong($id);
        return $this->fetch();
    }

    /**
     * 获取讲道数据
     * @param $id
     * @return void
     */
    protected function setSong($id)
    {
        if (!intval($id)) {
            abort(404, '页面不存在');
        }

        $model = new Songs;
        $song  = $model->where('status', 1)->with('extend')->find($id);
        if (!$song) {
            $this->error('讲道不存在或正在审核');
        }
        //$song = $song->toArray();
        //halt($song);
        $song = $this->setNum($song);
        $this->seoMeta($song);
        $this->assign('data', $song);
    }
    
    private function setNum($song)
    {
        $fields = ['favtimes','download','digg','listens'];
        foreach ($fields as $val) {
            $listens = Actions::getNum('songs', $val, $song['id']);
            $song[$val] = intval($song[$val]) + intval($listens);
        }
        return $song;
    }
}
