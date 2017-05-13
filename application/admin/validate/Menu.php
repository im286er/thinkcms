<?php

namespace app\admin\validate;

use think\Validate;

/**
 * 菜单验证器
 * @author WeiZeng
 */
class Menu extends Validate
{
    // 验证规则
    protected $rule = [
        'menu_id'            => 'require|number|gt:0',
        'list_order|排序序号' => 'require|number|egt:0',
        'type|菜单类型'       => 'require|number|in:1,2',
        'status|状态'         => 'require|number|in:-1,0,1'
    ];

    // 验证场景
    protected $scene = [
        // 筛选
        'filter' => ['type' => 'number|in:1,2'],
        // 排序
        'sort'   => ['menu_id', 'list_order'],
        // 设置状态
        'setStatus' => ['menu_id', 'status'],
    ];
}
