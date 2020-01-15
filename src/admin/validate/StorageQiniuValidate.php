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

class StorageQiniuValidate extends Validate
{
    protected $rule = [
        'accessKey' => 'require',
        'secretKey' => 'require',
        'domain'    => 'require',
    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);
        $this->message([
            'accessKey.require' => 'AccessKey '.lang('CANNOT_BE_EMPTY'),
            'secretKey.require' => 'secretKey '.lang('CANNOT_BE_EMPTY'),
            'domain.require' => lang('DOMAIN_CANNOT_BE_EMPTY'),
        ]);
    }

}