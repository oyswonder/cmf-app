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

use cmf\controller\UserBaseController;
use app\user\model\UserFavoriteModel;
use think\Db;

class FavoriteController extends UserBaseController
{

    /**
     * 个人中心我的收藏列表
     */
    public function index()
    {
        $userFavoriteModel = new UserFavoriteModel();
        $data              = $userFavoriteModel->favorites();
        $user              = cmf_get_current_user();
        $this->assign($user);
        $this->assign("page", $data['page']);
        $this->assign("lists", $data['lists']);
        return $this->fetch();
    }

    /**
     * 用户取消收藏
     */
    public function delete()
    {
        $id                = $this->request->param("id", 0, "intval");
        $userFavoriteModel = new UserFavoriteModel();
        $data              = $userFavoriteModel->deleteFavorite($id);
        if ($data) {
            $this->success(lang('REMOVED_TO_FAVORITE_SUCCESSFULLY'));
        } else {
            $this->error(lang('FAILED_TO_REMOVE_FROM_FAVORITE'));
        }
    }

    /**
     * 用户收藏
     */
    public function add()
    {
        $data   = $this->request->param();
        $result = $this->validate($data, 'Favorite');

        if ($result !== true) {
            $this->error($result);
        }

        $id    = $this->request->param('id', 0, 'intval');
        $table = $this->request->param('table');


        $findFavoriteCount = Db::name("user_favorite")->where([
            'object_id'  => $id,
            'table_name' => $table,
            'user_id'    => cmf_get_current_user_id()
        ])->count();

        if ($findFavoriteCount > 0) {
            $this->error(lang('ALREADY_IN_YOUR_FAVORITE'));
        }


        $title       = base64_decode($this->request->param('title'));
        $url         = $this->request->param('url');
        $url         = base64_decode($url);
        $description = $this->request->param('description', '', 'base64_decode');
        $description = empty($description) ? $title : $description;
        Db::name("user_favorite")->insert([
            'user_id'     => cmf_get_current_user_id(),
            'title'       => $title,
            'description' => $description,
            'url'         => $url,
            'object_id'   => $id,
            'table_name'  => $table,
            'create_time' => time()
        ]);

        $this->success(lang('ADDED_TO_FAVORITE_SUCCESSFULLY'));

    }
}