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

use app\user\model\UserModel;
use cmf\controller\HomeBaseController;

class IndexController extends HomeBaseController
{

    /**
     * 前台用户首页(公开)
     */
    public function index()
    {
        $id        = $this->request->param("id", 0, "intval");
        $userModel = new UserModel();
        $user      = $userModel->where('id', $id)->find();
        if (empty($user)) {
            $this->error(lang('NO_SUCH_USER'));
        }
        $this->assign($user->toArray());
        $this->assign('user',$user);
        return $this->fetch(":index");
    }

    /**
     * 前台ajax 判断用户登录状态接口
     */
    function isLogin()
    {
        if (cmf_is_user_login()) {
            $this->success(lang('USER_IS_LOGGED_IN'),null,['user'=>cmf_get_current_user()]);
        } else {
            $this->error(lang('USER_IS_NOT_LOGGED_IN'));
        }
    }

    /**
     * 退出登录
    */
    public function logout()
    {
        session("user", null);//只有前台用户退出
        return redirect($this->request->root() . "/");
    }

}
