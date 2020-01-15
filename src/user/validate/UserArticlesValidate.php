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
namespace app\user\validate;

use think\Validate;

class UserArticlesValidate extends Validate
{
    protected $rule = [
        'post_title' => 'require',
    ];

    protected $scene = [
        'add'  => ['post_title'],
        'edit' => ['post_title'],
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->message([
            'post_title.require' => lang('ARTICLE_TITLE_CANNOT_BE_EMPTY'),
        ]);
    }
}