<?php

namespace app\lr\controller;

use app\common\model\Menu;
use think\Controller;
use Think\View;

class BaseController extends Controller
{
    public function _initialize()
    {
        //视图共享
        View::share('menus',Menu::all());
    }
}
