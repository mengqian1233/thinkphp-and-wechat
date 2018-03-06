<?php

namespace app\lr\controller;

use app\common\model\News;
use think\Controller;

class IndexController extends BaseController
{
    public function index()
    {
        return $this->fetch();
    }
}
