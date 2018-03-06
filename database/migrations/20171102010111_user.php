<?php

use think\migration\Migrator;
use think\migration\db\Column;

class User extends Migrator
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
        //获取当前数据
        $table = $this->table('user');
        //添加字段
        //id字段是表自动添加的，不用写
        $table->addColumn('username','string');
        $table->addColumn('password','string');
        $table->addTimestamps();
        $table->addSoftDelete();
        //执行操作
        $table->create();
    }
}
