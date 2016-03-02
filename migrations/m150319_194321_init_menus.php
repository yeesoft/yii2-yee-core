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

        $this->createTable('menu', [
            'id' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'updated_by' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'CONSTRAINT `fk_menu_created_by` FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE',
            'CONSTRAINT `fk_menu_updated_by` FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);

        $this->addPrimaryKey('pk', 'menu', 'id');

        $this->createTable('menu_lang', [
            'id' => 'pk',
            'menu_id' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci NOT NULL',
            'language' => Schema::TYPE_STRING . '(6) NOT NULL',
            'title' => Schema::TYPE_TEXT . ' NOT NULL',
        ], $tableOptions);

        $this->createIndex('menu_lang_post_id', 'menu_lang', 'menu_id');
        $this->createIndex('menu_lang_language', 'menu_lang', 'language');
        $this->addForeignKey('fk_menu_lang', 'menu_lang', 'menu_id', 'menu', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('menu_link', [
            'id' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci NOT NULL',
            'menu_id' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci NOT NULL',
            'link' => Schema::TYPE_STRING . '(255) COLLATE utf8_unicode_ci DEFAULT NULL',
            'parent_id' => Schema::TYPE_STRING . "(64) COLLATE utf8_unicode_ci DEFAULT ''",
            'image' => Schema::TYPE_STRING . '(24) COLLATE utf8_unicode_ci DEFAULT NULL',
            'alwaysVisible' => Schema::TYPE_SMALLINT . "(1) NOT NULL DEFAULT '0'",
            'order' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'updated_at' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'created_by' => Schema::TYPE_INTEGER . '(11) DEFAULT NULL',
            'updated_by' => Schema::TYPE_INTEGER . '(11) DEFAULT NULL',
            'CONSTRAINT `fk_menu_link_created_by` FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE',
            'CONSTRAINT `fk_menu_link_updated_by` FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);

        $this->addPrimaryKey('pk', 'menu_link', 'id');
        $this->createIndex('link_menu_id', 'menu_link', 'menu_id');
        $this->createIndex('link_parent_id', 'menu_link', 'parent_id');

        $this->createTable('menu_link_lang', [
            'id' => 'pk',
            'link_id' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci NOT NULL',
            'language' => Schema::TYPE_STRING . '(6) NOT NULL',
            'label' => Schema::TYPE_STRING . '(255) COLLATE utf8_unicode_ci NOT NULL',
        ], $tableOptions);

        $this->createIndex('menu_link_lang_link_id', 'menu_link_lang', 'link_id');
        $this->createIndex('menu_link_lang_language', 'menu_link_lang', 'language');
        $this->addForeignKey('fk_menu_link_lang', 'menu_link_lang', 'link_id', 'menu_link', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk_menu_link', 'menu_link', 'menu_id', 'menu', 'id', 'CASCADE');

        $this->insert('menu', ['id' => 'admin-menu', 'created_by' => 1]);
        $this->insert('menu_lang', ['menu_id' => 'admin-menu', 'language' => 'en-US', 'title' => 'Control Panel Menu']);

        $this->insert('menu_link', ['id' => 'dashboard', 'menu_id' => 'admin-menu', 'link' => '/', 'image' => 'th', 'created_by' => 1, 'order' => 1]);
        $this->insert('menu_link_lang', ['link_id' => 'dashboard', 'label' => 'Dashboard', 'language' => 'en-US']);
    }

    public function down()
    {
        $this->dropForeignKey('fk_menu_created_by', 'menu');
        $this->dropForeignKey('fk_menu_updated_by', 'menu');
        $this->dropForeignKey('fk_menu_link_created_by', 'menu_link');
        $this->dropForeignKey('fk_menu_link_updated_by', 'menu_link');

        $this->dropForeignKey('fk_menu_link_lang', 'menu_link_lang');
        $this->dropForeignKey('fk_menu_link', 'menu_link');
        $this->dropForeignKey('fk_menu_lang', 'menu_link');

        $this->dropTable('menu_link_lang');
        $this->dropTable('menu_link');
        $this->dropTable('menu_lang');
        $this->dropTable('menu');
    }
}
