<?php
/**
 * Created by PhpStorm.
 * User: xiaoqian
 * Date: 2017/12/13
 * Time: 10:41
 */

namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class Tuwen extends Model
{
    //引入软删除机制
    use SoftDelete;
}