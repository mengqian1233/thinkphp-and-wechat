<?php

namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class Menu extends Model
{
    //引入软删除机制
    use SoftDelete;
}
