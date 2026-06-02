<?php

namespace app\model;

use think\Model;

/**
 * 基础模型
 */
class BaseModel extends Model
{
    /**
     * 自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 创建时间字段
     * @var string
     */
    protected $createTime = 'create_time';

    /**
     * 更新时间字段
     * @var string
     */
    protected $updateTime = 'update_time';

    /**
     * 软删除字段
     * @var string
     */
    protected $deleteTime = 'delete_time';

    /**
     * 默认软删除值
     * @var mixed
     */
    protected $defaultSoftDelete = null;

    /**
     * 数据验证
     * @param array $data 数据
     * @param array $rules 规则
     * @return bool
     */
    public function validateData(array $data, array $rules): bool
    {
        $validate = new \think\Validate($rules);
        return $validate->check($data);
    }

    /**
     * 防止 SQL 注入的查询封装
     * @param string $field 字段名
     * @param mixed $value 值
     * @return array
     */
    protected function safeWhere(string $field, $value): array
    {
        // 白名单验证字段名
        $allowedFields = $this->getFields();
        if (!in_array($field, $allowedFields)) {
            throw new \InvalidArgumentException('Invalid field name');
        }
        
        return [$field, '=', $value];
    }
}
