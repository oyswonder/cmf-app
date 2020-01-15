<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\validate;

use think\Validate;

class FavoriteValidate extends Validate
{
    protected $rule = [
        'id'    => 'require',
        'title' => 'require|checkTitle',
        'table' => 'require',
        'url'   => 'require|checkUrl',
    ];

    protected $scene = [];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->message([
            'id.require'    => lang('FAVORITE_ID_CANNOT_BE_EMPTY'),
            'title.require' => lang('FAVORITE_TITLE_CANNOT_BE_EMPTY'),
            'table.require' => lang('FAVORITE_TABLE_CANNOT_BE_EMPTY'),
            'url.require'   => lang('FAVORITE_LINK_CANNOT_BE_EMPTY'),
            'url.checkUrl'  => lang('INCORRECT_LINK_FORMAT')
        ]);
    }

    // 验证url 格式
    protected function checkUrl($value, $rule, $data)
    {
        $url = json_decode(base64_decode($value), true);

        if (!empty($url['action'])) {
            return true;
        }
        return lang('INCORRECT_LINK_FORMAT');
    }

    // 验证url 格式
    protected function checkTitle($value, $rule, $data)
    {
        if (base64_decode($value)!==false) {
            return true;
        }
        return lang('INCORRECT_TITLE_FORMAT');
    }
}