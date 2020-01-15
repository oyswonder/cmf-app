<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\HomeBaseController;
use think\facade\Validate;

class VerificationCodeController extends HomeBaseController
{
    public function send()
    {
        $validate = new \think\Validate([
            'username' => 'require',
            'captcha'  => 'require',
        ]);

        $validate->message([
            'username.require' => lang('PLEASE_ENTER_YOUR_MOBILE_OR_EMAIL'),
            'captcha.require'  => lang('IMAGE_CAPTCHA_REQUIRED'),
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $captchaId = empty($data['captcha_id']) ? '' : $data['captcha_id'];
        if (!cmf_captcha_check($data['captcha'], $captchaId, false)) {
            $this->error(lang('IMAGE_CAPTCHA_NOT_CORRECT'));
        }

        $registerCaptcha = session('register_captcha');

        session('register_captcha', $data['captcha']);

        if ($registerCaptcha == $data['captcha']) {
            cmf_captcha_check($data['captcha'], $captchaId, true);
            $this->error(lang('PLEASE_ENTER_A_NEW_IMAGE_CAPTCHA'));
        }

        $accountType = '';

        if (Validate::is($data['username'], 'email')) {
            $accountType = 'email';
        } else if (cmf_check_mobile($data['username'])) {
            $accountType = 'mobile';
        } else {
            $this->error(lang('ENTER_THE_CORRECT_MOBILE_OR_EMAIL'));
        }

        if (isset($data['type']) && $data['type'] == 'register') {
            if ($accountType == 'email') {
                $findUserCount = db('user')->where('user_email', $data['username'])->count();
            } else if ($accountType == 'mobile') {
                $findUserCount = db('user')->where('mobile', $data['username'])->count();
            }

            if ($findUserCount > 0) {
                $this->error(lang('THIS_ACCOUNT_HAS_BEEN_REGISTERED'));
            }
        }

        //TODO 限制 每个ip 的发送次数

        $code = cmf_get_verification_code($data['username']);
        if (empty($code)) {
            $this->error(lang('TOO_MANY_CAPTCHA_SENT_PLEASE_TRY_AGAIN_TOMORROW'));
        }

        if ($accountType == 'email') {

            $emailTemplate = cmf_get_option('email_template_verification_code');

            $user     = cmf_get_current_user();
            $username = empty($user['user_nickname']) ? $user['user_login'] : $user['user_nickname'];

            $message = htmlspecialchars_decode($emailTemplate['template']);
            $message = $this->view->display($message, ['code' => $code, 'username' => $username]);
            $subject = empty($emailTemplate['subject']) ? 'Code: ' : $emailTemplate['subject'];
            $result  = cmf_send_email($data['username'], $subject, $message);

            if (empty($result['error'])) {
                cmf_verification_code_log($data['username'], $code);
                $this->success(lang('VERIFICATION_CODE_HAS_BEEN_SENT_SUCCESSFULLY'));
            } else {
                $this->error(lang('FAILED_TO_SEND_CAPTCHA'). ': ' . $result['message']);
            }

        } else if ($accountType == 'mobile') {

            $param  = ['mobile' => $data['username'], 'code' => $code];
            $result = hook_one("send_mobile_verification_code", $param);

            if ($result !== false && !empty($result['error'])) {
                $this->error($result['message']);
            }

            if ($result === false) {
                $this->error(lang('THE_CAPTCHA_SENDING_PLUGIN_IS_NOT_INSTALLED'));
            }

            $expireTime = empty($result['expire_time']) ? 0 : $result['expire_time'];

            cmf_verification_code_log($data['username'], $code, $expireTime);

            if (!empty($result['message'])) {
                $this->success($result['message']);
            } else {
                $this->success(lang('VERIFICATION_CODE_HAS_BEEN_SENT_SUCCESSFULLY'));
            }

        }


    }

}
