<?php
/**
 * Created by PhpStorm.
 * User: xiaoqian
 * Date: 2017/12/6
 * Time: 10:21
 */

namespace app\index\controller;


use app\common\model\Customer;
use think\Controller;
use think\Session;
use think\Request;

class YonghuController extends Controller
{
    //第一步作用：判断用户是否登录；根据用户请求的方法进行微信服务器的跳转到oauthController
    public function __construct()
    {
        //将继承父类的构造方法在子类中能够使用
        parent::__construct();
        //获取当前操作名称，比如说index()方法
        $action = Request::instance()->action();
        //判断有没有session保存openid，即是判断登录
        if (!Session::has('openid')) {
            //根据操作名称进行跳转
            return $this->redirect('http://mqian.wywwwxm.com/tp5/public/index.php/index/oauth/OauthCode?redirect=user-'.$action);
        }
    }
    public function index()
    {
        $openid = Session::get('openid');
        $c = new Customer();
        $result = $c ->where('openid',$openid)->find();
        $this->assign('rows',$result);
        return $this->fetch();
    }
    public function help()
    {
        $openid = Session::get('openid');
        $c = new Customer();
        $result = $c ->where('openid',$openid)->find();
        $this->assign('rows',$result);
        return $this->fetch();
    }

}