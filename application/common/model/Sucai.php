<?php

namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class Sucai extends Model
{
    //引入软删除机制
    use SoftDelete;
}
