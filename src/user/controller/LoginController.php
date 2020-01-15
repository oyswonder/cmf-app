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

use think\facade\Validate;
use cmf\controller\HomeBaseController;
use app\user\model\UserModel;

class LoginController extends HomeBaseController
{

    /**
     * 登录
     */
    public function index()
    {
        $redirect = $this->request->param("redirect");
        if (empty($redirect)) {
            $redirect = $this->request->server('HTTP_REFERER');
        } else {
            if (strpos($redirect, '/') === 0 || strpos($redirect, 'http') === 0) {
            } else {
                $redirect = base64_decode($redirect);
            }
        }
        if(!empty($redirect)){
            session('login_http_referer', $redirect);
        }
        if (cmf_is_user_login()) { //已经登录时直接跳到首页
            return redirect($this->request->root() . '/');
        } else {
            return $this->fetch(":login");
        }
    }

    /**
     * 登录验证提交
     */
    public function doLogin()
    {
        if ($this->request->isPost()) {
            $validate = new \think\Validate([
                'captcha'  => 'require',
                'username' => 'require',
                'password' => 'require|min:6|max:32',
            ]);
            $validate->message([
                'username.require' => lang('USERNAME_CANNOT_BE_EMPTY'),
                'password.require' => lang('PASSWORD_REQUIRED'),
                'password.max'     => lang('MAXIMUM_NUMBER_OF_PASSWORD_STRINGS'),
                'password.min'     => lang('MINIMUM_NUMBER_OF_PASSWORD_STRINGS'),
                'captcha.require'  => lang('CAPTCHA_REQUIRED'),
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            if (!cmf_captcha_check($data['captcha'])) {
                $this->error(lang('CAPTCHA_NOT_RIGHT'));
            }

            $userModel         = new UserModel();
            $user['user_pass'] = $data['password'];
            if (Validate::is($data['username'], 'email')) {
                $user['user_email'] = $data['username'];
                $log                = $userModel->doEmail($user);
            } else if (cmf_check_mobile($data['username'])) {
                $user['mobile'] = $data['username'];
                $log            = $userModel->doMobile($user);
            } else {
                $user['user_login'] = $data['username'];
                $log                = $userModel->doName($user);
            }
            $session_login_http_referer = session('login_http_referer');
            $redirect                   = empty($session_login_http_referer) ? $this->request->root() : $session_login_http_referer;
            switch ($log) {
                case 0:
                    cmf_user_action('login');
                    $this->success(lang('LOGIN_SUCCESS'), $redirect);
                    break;
                case 1:
                    $this->error(lang('PASSWORD_NOT_RIGHT'));
                    break;
                case 2:
                    $this->error(lang('ACCOUNT_DOES_NOT_EXIST'));
                    break;
                case 3:
                    $this->error(lang('ACCOUNT_IS_FORBIDDEN_TO_ACCESS_THE_SYSTEM'));
                    break;
                default :
                    $this->error(lang('UNACCEPTED_REQUEST'));
            }
        } else {
            $this->error(lang('REQUEST_ERROR'));
        }
    }

    /**
     * 找回密码
     */
    public function findPassword()
    {
        return $this->fetch('/find_password');
    }

    /**
     * 用户密码重置
     */
    public function passwordReset()
    {

        if ($this->request->isPost()) {
            $validate = new \think\Validate([
                'captcha'           => 'require',
                'verification_code' => 'require',
                'password'          => 'require|min:6|max:32',
            ]);
            $validate->message([
                'verification_code.require' => lang('CAPTCHA_REQUIRED'),
                'password.require'          => lang('PASSWORD_REQUIRED'),
                'password.max'              => lang('MAXIMUM_NUMBER_OF_PASSWORD_STRINGS'),
                'password.min'              => lang('MINIMUM_NUMBER_OF_PASSWORD_STRINGS'),
                'captcha.require'           => lang('CAPTCHA_REQUIRED'),
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $captchaId = empty($data['_captcha_id']) ? '' : $data['_captcha_id'];
            if (!cmf_captcha_check($data['captcha'], $captchaId)) {
                $this->error(lang('CAPTCHA_NOT_RIGHT'));
            }

            $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            if (!empty($errMsg)) {
                $this->error($errMsg);
            }

            $userModel = new UserModel();
            if (Validate::is($data['username'], 'email')) {

                $log = $userModel->emailPasswordReset($data['username'], $data['password']);

            } else if (cmf_check_mobile($data['username'])) {
                $user['mobile'] = $data['username'];
                $log            = $userModel->mobilePasswordReset($data['username'], $data['password']);
            } else {
                $log = 2;
            }
            switch ($log) {
                case 0:
                    $this->success(lang('PASSWORD_RESET_SUCCEEDED'), cmf_url('user/Profile/center'));
                    break;
                case 1:
                    $this->error(lang('THE_ACCOUNT_HAS_NOT_BEEN_REGISTERED'));
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