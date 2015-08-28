<?php

use yii\db\Migration;
use yii\db\Schema;

class m150319_194321_init_menus extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('menu',
            [
                'id' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci NOT NULL',
                'title' => Schema::TYPE_STRING . '(255) COLLATE utf8_unicode_ci NOT NULL',
            ], $tableOptions);

        $this->addPrimaryKey('pk', 'menu', 'id');

        $this->createTable('menu_link',
            [
                'id' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci NOT NULL',
                'menu_id' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci NOT NULL',
                'link' => Schema::TYPE_STRING . '(255) COLLATE utf8_unicode_ci DEFAULT NULL',
                'label' => Schema::TYPE_STRING . '(255) COLLATE utf8_unicode_ci NOT NULL',
                'parent_id' => Schema::TYPE_STRING . "(64) COLLATE utf8_unicode_ci DEFAULT ''",
                'image' => Schema::TYPE_STRING . '(24) COLLATE utf8_unicode_ci DEFAULT NULL',
                'alwaysVisible' => Schema::TYPE_SMALLINT . "(1) NOT NULL DEFAULT '0'",
                'order' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            ], $tableOptions);

        $this->addPrimaryKey('pk', 'menu_link', 'id');
        $this->createIndex('link_menu_id', 'menu_link', 'menu_id');
        $this->createIndex('link_parent_id', 'menu_link', 'parent_id');

        $this->addForeignKey('fk_menu_link', 'menu_link', 'menu_id', 'menu',
            'id', 'CASCADE');

        $this->insert('menu',
            ['id' => 'admin-main-menu', 'title' => 'Main Admin Panel Menu']);

        $this->insert('menu_link',
            ['id' => 'dashboard', 'menu_id' => 'admin-main-menu', 'link' => '/',
                'label' => 'Dashboard', 'image' => 'th-large', 'order' => 1]);
    }

    public function down()
    {
        $this->dropForeignKey('fk_menu_link', 'menu_link');
        $this->dropTable('menu_link');
        $this->dropTable('menu');
    }
}