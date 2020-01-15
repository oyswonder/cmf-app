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

class UserValidate extends Validate
{
    protected $rule = [
        'user_login' => 'require|unique:user,user_login',
        'user_pass'  => 'require',
        'user_email' => 'require|email|unique:user,user_email',
    ];

    protected $scene = [
        'add'  => ['user_login', 'user_pass', 'user_email'],
        'edit' => ['user_login', 'user_email'],
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->message([
            'user_login.require' => lang('USERNAME_CANNOT_BE_EMPTY'),
            'user_login.unique'  => lang('USERNAME_IS_ALREADY_REGISTERED'),
            'user_pass.require'  => lang('PASSWORD_CANNOT_BE_EMPTY'),
            'user_email.require' => lang('EMAIL_REQUIRED'),
            'user_email.email'   => lang('EMAIL_ADDRESS_IS_INCORRECT'),
            'user_email.unique'  => lang('EMAIL_ADDRESS_HAS_BEEN_REGISTERED'),
        ]);
    }
}