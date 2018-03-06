<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Newupdate2 extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        //获取当前数据表
        $table = $this->table('news');
        //添加字段
        $table->addSoftDelete();//添加软删除字段 delete_time
        $table->addTimestamps();//添加自动维护字段 create_time和add_time
        //执行操作
        $table->update();

    }
}
