<?php

namespace app\admin\controller;

use app\common\model\Menu;
use think\Controller;
use think\Request;

class MenuController extends BaseController
{
    //数据添加或修改时所使用的字段名称
    protected $fields = ['name','value'];
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //获取分页数据
        $rows = Menu::paginate();
        //显示视图
        $this->assign('rows',$rows);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $news = new Menu();
        foreach ($this->fields as $f)
        {
            $news->$f = input($f);
        }
        //插入到数据库中
        if ($news -> save()){
            //return $this->success('数据插入成功','/admin/menu');
            //插入成功则创建菜单api
            $m= new \weixin\wxMenu();
            return $m->createMenu($news['value']);
        }else{
            return $this->error('数据插入失败');
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $this->assign('row',Menu::get($id));
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $news = Menu::get($id);
        foreach ($this->fields as $f)
        {
            $news->$f = input($f);
        }
        $news['value'] = input('value');
         //更新到数据库中
        if ($news -> save()){
            //return $this->success('数据更新成功','/admin/news');
            $m= new \weixin\wxMenu();
            return $m->createMenu($news['value']);
        }else{
            return $this->error('数据更新失败');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        /* echo 'delete'.$id;*/
        $new = Menu::get($id);
        if ($new->delete()){
            return "ok";
        }else{
            return "error";
        }
    }
}
