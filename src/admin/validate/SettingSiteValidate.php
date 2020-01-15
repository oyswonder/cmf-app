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

use app\admin\model\RouteModel;
use think\Validate;

class SettingSiteValidate extends Validate
{
    protected $rule = [
        'options.site_name'             => 'require',
        'admin_settings.admin_password' => 'alphaNum|checkAlias'
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->message([
            'options.site_name.require'                => lang('SITE_NAME_CANNOT_BE_EMPTY'),
            'admin_settings.admin_password.alphaNum'   => lang('ENCRYPTION_KEY_CAN_ONLY_BE_ENGLISH_LETTERS_AND_NUMBERS'),
            'admin_settings.admin_password.checkAlias' => lang('ENCRYPTION_KEY_CANNOT_BE_USED'),
        ]);
    }


    // 自定义验证规则
    protected function checkAlias($value, $rule, $data)
    {
        if (empty($value)) {
            return true;
        }

        if(preg_match('/^\d+$/',$value)){
            return lang('ENCRYPTION_KEY_CANNOT_PURE_NUMBER');
        }

        $routeModel = new RouteModel();
        $fullUrl    = $routeModel->buildFullUrl('admin/Index/index', []);
        if (!$routeModel->existsRoute($value.'$', $fullUrl)) {
            return true;
        } else {
            return lang('URL_RULE_ALREADY_EXISTS');
        }

    }

}