<?php
/**
 * 音乐管理系统
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

namespace app\api\controller\v1;

use think\Request;
use app\common\model\Album;

class AlbumsController extends ApiController
{
    /**
     * 显示资源列表
     * @param Request $request
     * @return \think\response
     */
    public function index(Request $request)
    {
        $param = $request->get();
        if (empty($param)) {
            return json(['code' => 40404, 'error' => '参数错误',], 404);
        }
    
        $model = new Album;
        $list = $model->getList($param);
    
        if (empty($list)) {
            return $this->retErr(40407);
        }
 
        $list = isset($param['page']) ?  $list->items() : $list;
        return $this->retSucc(['result' => $list]);
    }

    /**
     * 显示指定的资源
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id = 0)
    {
        if (!intval($id)) {
            return json([ 'code' => 40404, 'error' => '参数错误', ], 404);
        }
        //缓存数据
        $model = new Album;
        $data  = $model->hidden(['create_time', 'update_time'])->get($id);
        if ($data) {
            return $this->retSucc(['result' => $data]);
        }
        return $this->retErr(40407);
    }

}
