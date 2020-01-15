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

class RoleValidate extends Validate
{
    protected $rule = [
        'name' => 'require',
    ];

    protected $scene = [
        'add' => ['name'],
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->message([
            'name.require' => lang('ROLE_NAME_CANNOT_BE_EMPTY'),
        ]);
    }
}