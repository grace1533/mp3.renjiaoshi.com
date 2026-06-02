<?php

namespace app\controller;

use think\App;
use think\response\View;
use think\response\Json;

/**
 * 基础控制器
 */
class BaseController
{
    /**
     * Request 实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    /**
     * 初始化操作
     */
    protected function initialize()
    {
    }

    /**
     * 视图渲染
     * @param string $template 模板文件
     * @param array $vars 变量
     * @return View
     */
    protected function fetch(string $template = '', array $vars = []): View
    {
        return view($template, $vars);
    }

    /**
     * JSON 响应
     * @param mixed $data 数据
     * @param int $code 状态码
     * @param string $msg 消息
     * @return Json
     */
    protected function success($data = null, string $msg = 'success', int $code = 0): Json
    {
        return json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    /**
     * 错误 JSON 响应
     * @param string $msg 消息
     * @param int $code 状态码
     * @param mixed $data 数据
     * @return Json
     */
    protected function error(string $msg = 'error', int $code = 1, $data = null): Json
    {
        return json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    /**
     * 输入验证
     * @param array $data 数据
     * @param array|string $validate 验证器
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return bool
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false): bool
    {
        if (is_array($validate)) {
            $v = new \think\Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }
}
