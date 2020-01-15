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
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use app\admin\model\LinkModel;

class LinkController extends AdminBaseController
{
    protected function getTargets()
    {
        return [
            '_blank' => lang('_BLANK'),
            '_self' => lang('_SELF'),
        ];
    }

    /**
     * 友情链接管理
     * @adminMenu(
     *     'name'   => '友情链接',
     *     'parent' => 'admin/Setting/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 50,
     *     'icon'   => '',
     *     'remark' => '友情链接管理',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $content = hook_one('admin_link_index_view');

        if (!empty($content)) {
            return $content;
        }

        $linkModel = new LinkModel();
        $links     = $linkModel->select();
        $this->assign('links', $links);

        return $this->fetch();
    }

    /**
     * 添加友情链接
     * @adminMenu(
     *     'name'   => '添加友情链接',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加友情链接',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $this->assign('targets', $this->getTargets());
        return $this->fetch();
    }

    /**
     * 添加友情链接提交保存
     * @adminMenu(
     *     'name'   => '添加友情链接提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加友情链接提交保存',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data      = $this->request->param();
        $linkModel = new LinkModel();
        $result    = $this->validate($data, 'Link');
        if ($result !== true) {
            $this->error($result);
        }
        $linkModel->allowField(true)->save($data);

        $this->success(lang('ADDED_SUCCESSFULLY'), url("Link/index"));
    }

    /**
     * 编辑友情链接
     * @adminMenu(
     *     'name'   => '编辑友情链接',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑友情链接',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\Exception\DbException
     */
    public function edit()
    {
        $id        = $this->request->param('id', 0, 'intval');
        $linkModel = new LinkModel();
        $link      = $linkModel->get($id);
        $this->assign('targets', $this->getTargets());
        $this->assign('link', $link);
        return $this->fetch();
    }

    /**
     * 编辑友情链接提交保存
     * @adminMenu(
     *     'name'   => '编辑友情链接提交保存',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑友情链接提交保存',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data      = $this->request->param();
        $linkModel = new LinkModel();
        $result    = $this->validate($data, 'Link');
        if ($result !== true) {
            $this->error($result);
        }
        $linkModel->allowField(true)->isUpdate(true)->save($data);

        $this->success(lang('SAVED_SUCCESSFULLY'), url("Link/index"));
    }

    /**
     * 删除友情链接
     * @adminMenu(
     *     'name'   => '删除友情链接',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除友情链接',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        LinkModel::destroy($id);
        $this->success(lang('DELETED_SUCCESSFULLY'), url("link/index"));
    }

    /**
     * 友情链接排序
     * @adminMenu(
     *     'name'   => '友情链接排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '友情链接排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        $linkModel = new  LinkModel();
        parent::listOrders($linkModel);
        $this->success(lang('SORTING_UPDATE_SUCCEEDED'));
    }

    /**
     * 友情链接显示隐藏
     * @adminMenu(
     *     'name'   => '友情链接显示隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '友情链接显示隐藏',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $data      = $this->request->param();
        $linkModel = new LinkModel();

        if (isset($data['ids']) && !empty($data["display"])) {
            $ids = $this->request->param('ids/a');
            $linkModel->where('id', 'in', $ids)->update(['status' => 1]);
            $this->success(lang('UPDATED_SUCCESSFULLY'));
        }

        if (isset($data['ids']) && !empty($data["hide"])) {
            $ids = $this->request->param('ids/a');
            $linkModel->where('id', 'in', $ids)->update(['status' => 0]);
            $this->success(lang('UPDATED_SUCCESSFULLY'));
        }


    }

}