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
namespace app\user\controller;

use cmf\controller\HomeBaseController;
use think\facade\Validate;
use app\user\model\UserModel;

class RegisterController extends HomeBaseController
{

    /**
     * 前台用户注册
     */
    public function index()
    {
        $redirect = $this->request->post("redirect");
        if (empty($redirect)) {
            $redirect = $this->request->server('HTTP_REFERER');
        } else {
            $redirect = base64_decode($redirect);
        }
        session('login_http_referer', $redirect);

        if (cmf_is_user_login()) {
            return redirect($this->request->root() . '/');
        } else {
            return $this->fetch(":register");
        }
    }

    /**
     * 前台用户注册提交
     */
    public function doRegister()
    {
        if ($this->request->isPost()) {
            $rules = [
                'captcha'  => 'require',
                'code'     => 'require',
                'password' => 'require|min:6|max:32',

            ];

            $isOpenRegistration = cmf_is_open_registration();

            if ($isOpenRegistration) {
                unset($rules['code']);
            }

            $validate = new \think\Validate($rules);
            $validate->message([
                'code.require'     => lang('CAPTCHA_NOT_RIGHT'),
                'password.require' => lang('PASSWORD_REQUIRED'),
                'password.max'     => lang('MAXIMUM_NUMBER_OF_PASSWORD_STRINGS'),
                'password.min'     => lang('MINIMUM_NUMBER_OF_PASSWORD_STRINGS'),
                'captcha.require'  => lang('CAPTCHA_NOT_RIGHT'),
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $captchaId = empty($data['_captcha_id']) ? '' : $data['_captcha_id'];
            if (!cmf_captcha_check($data['captcha'], $captchaId)) {
                $this->error(lang('CAPTCHA_NOT_RIGHT'));
            }

            if (!$isOpenRegistration) {
                $errMsg = cmf_check_verification_code($data['username'], $data['code']);
                if (!empty($errMsg)) {
                    $this->error($errMsg);
                }
            }

            $register          = new UserModel();
            $user['user_pass'] = $data['password'];
            if (Validate::is($data['username'], 'email')) {
                $user['user_email'] = $data['username'];
                $log                = $register->register($user, 3);
            } else if (cmf_check_mobile($data['username'])) {
                $user['mobile'] = $data['username'];
                $log            = $register->register($user, 2);
            } else {
                $log = 2;
            }
            $sessionLoginHttpReferer = session('login_http_referer');
            $redirect                = empty($sessionLoginHttpReferer) ? cmf_get_root() . '/' : $sessionLoginHttpReferer;
            switch ($log) {
                case 0:
                    $this->success(lang('REGISTERED_SUCCESSFULLY'), $redirect);
                    break;
                case 1:
                    $this->error(lang('THIS_ACCOUNT_HAS_BEEN_REGISTERED'));
                    break;
                case 2:
                    $this->error(lang('THE_ACCOUNT_YOU_ENTERED_IS_NOT_IN_THE_CORRECT_FORMAT'));
                    break;
                default :
                    $this->error(lang('UNACCEPTED_REQUEST'));
            }

        } else {
            $this->error(lang('REQUEST_ERROR'));
        }

    }
}