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

class RouteValidate extends Validate
{
    protected $rule = [
        'url'      => 'require|checkUrl',
        'full_url' => 'require|checkFullUrl',
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->message([
            'url.require'      => lang('DISPLAY_URL_CANNOT_BE_EMPTY'),
            'full_url.require' => lang('ORIGINAL_URL_CANNOT_BE_EMPTY'),
        ]);
    }

    // 自定义验证规则
    protected function checkUrl($value, $rule, $data)
    {
        $value = htmlspecialchars_decode($value);
        if (preg_match("/[()'\";]/", $value)) {
            return lang('DISPLAY_URL_FORMAT_IS_INCORRECT');
        }

        return true;
    }

    // 自定义验证规则
    protected function checkFullUrl($value, $rule, $data)
    {
        $value = htmlspecialchars_decode($value);
        if (preg_match("/[()'\";]/", $value)) {
            return lang('ORIGINAL_URL_FORMAT_IS_INCORRECT');
        }

        return true;
    }

}