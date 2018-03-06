<?php
/**
 * Created by PhpStorm.
 * User: xiaoqian
 * Date: 2017/12/14
 * Time: 10:58
 */

namespace app\admin\controller;


use think\Controller;
use think\Session;

class BaseController extends Controller
{
    public function  _initialize()
    {
        if (Session::get('loginedUser') == NULL){
            return $this->error('请先登录，再进行操作','http://mqian.wywwwxm.com/tp5/public/index.php/lr/user/login');
        }
    }

}