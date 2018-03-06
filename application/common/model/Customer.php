<?php
/**
 * Created by PhpStorm.
 * User: xiaoqian
 * Date: 2017/12/7
 * Time: 10:14
 */

namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class Customer extends Model
{
    //引入软删除机制
    use SoftDelete;
}