<?php

namespace app\lr\controller;

use app\common\model\User;
use app\common\model\Menu;
use think\Controller;
use think\Request;

class UserController extends BaseController
{
    public function register()
    {
        return $this->fetch();
    }
    public function doregister(Request $request)
    {
        //创建模型类对象
        $user = new User();
        //获取表单数据
        $user ->username = $request->param('username');  //依赖注入
        $user ->password = md5(input('password'));
        //直接使用助手函数获取表单数据(学习！！)
        //插入到数据库中
        if ($user->save()){
            //注册成功
            //使用控制器success方法
            return $this->success('Welcome','http://mqian.wywwwxm.com/tp5/public/index.php/lr/user/login');
        }else{
            //注册失败
            //回退到上一步
            return $this->error('注册失败，请重试！');
        }
    }
    public function login()
    {
        return $this->fetch();
    }

    public function dologin()
    {
        //校验验证码有效性
        $captcha = input('captcha');
        if (!captcha_check($captcha,'login')){
            //验证错误
            return $this->error('验证码错误请重试！');
        }
        //构造条件

        $condition = [];
        //获取表单数据
        $condition['username'] = input('username');
        $condition['password'] = md5(input('password'));
        //获取匹配记录
        $user = User::where($condition)->find();
        //判断
        if ($user){   //登录成功
            //写入session
            session('loginedUser',$user->username);
            //跳转
            return $this->success('用户登录成功！','http://mqian.wywwwxm.com/tp5/public/index.php/admin/index/index');
        }
        else{
            return $this->error('用户名或者密码错误');
        }

    }
    public function logout()
    {
        session('loginedUser',null);
        return $this->redirect('http://mqian.wywwwxm.com/tp5/public/index.php/lr/user/login'); //直接重定向没有任何提示消息
    }


}
