<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;
use think\Db;

class AdminMenuValidate extends Validate
{
    protected $rule = [
        'name'       => 'require',
        'app'        => 'require',
        'controller' => 'require',
        'parent_id'  => 'checkParentId',
        'action'     => 'require|unique:AdminMenu,app^controller^action',
    ];

    protected $scene = [
        'add'  => ['name', 'app', 'controller', 'action', 'parent_id'],
        'edit' => ['name', 'app', 'controller', 'action', 'id', 'parent_id'],

    ];


    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->message([
            'name.require'       => lang('MENU_NAME_CANNOT_BE_EMPTY'),
            'app.require'        => lang('APPLICATION_CANNOT_BE_EMPTY'),
            'parent_id'          => lang('MENU_OVER_LEVEL'),
            'controller.require' => lang('CONTROLLER_CANNOT_BE_EMPTY'),
            'action.require'     => lang('ACTION_CANNOT_BE_EMPTY'),
            'action.unique'      => lang('SAME_ITEM_ALREADY_EXISTS'),
        ]);
    }

    // 自定义验证规则
    protected function checkParentId($value)
    {
        $find = Db::name('AdminMenu')->where("id", $value)->value('parent_id');

        if ($find) {
            $find2 = Db::name('AdminMenu')->where("id", $find)->value('parent_id');
            if ($find2) {
                $find3 = Db::name('AdminMenu')->where("id", $find2)->value('parent_id');
                if ($find3) {
                    return false;
                }
            }
        }
        return true;
    }
}