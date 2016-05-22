<?php

use yii\db\Schema;

class m150319_194321_init_menus extends yii\db\Migration
{

    const TABLE_NAME = '{{%menu}}';
    const TABLE_LANG_NAME = '{{%menu_lang}}';
    const TABLE_LINK_NAME = '{{%menu_link}}';
    const TABLE_LINK_LANG_NAME = '{{%menu_link_lang}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TABLE_NAME, [
            'id' => Schema::TYPE_STRING.'(64) NOT NULL PRIMARY KEY',
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('fk_menu_created_by', self::TABLE_NAME, 'created_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_menu_updated_by', self::TABLE_NAME, 'updated_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');

        $this->createTable(self::TABLE_LANG_NAME, [
            'id' => $this->primaryKey(),
            'menu_id' => $this->string(64)->notNull(),
            'language' => $this->string(6)->notNull(),
            'title' => $this->text(),
        ], $tableOptions);

        $this->createIndex('menu_lang_post_id', self::TABLE_LANG_NAME, 'menu_id');
        $this->createIndex('menu_lang_language', self::TABLE_LANG_NAME, 'language');
        $this->addForeignKey('fk_menu_lang', self::TABLE_LANG_NAME, 'menu_id', self::TABLE_NAME, 'id', 'CASCADE', 'CASCADE');

        $this->createTable(self::TABLE_LINK_NAME, [
            'id' => Schema::TYPE_STRING.'(64) NOT NULL PRIMARY KEY',
            'menu_id' => $this->string(64)->notNull(),
            'link' => $this->string(255),
            'parent_id' => $this->string(64)->defaultValue(''),
            'image' => $this->string(24),
            'alwaysVisible' => $this->integer(1)->notNull()->defaultValue(0),
            'order' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('link_menu_id', self::TABLE_LINK_NAME, 'menu_id');
        $this->createIndex('link_parent_id', self::TABLE_LINK_NAME, 'parent_id');
        $this->addForeignKey('fk_menu_link_created_by', self::TABLE_LINK_NAME, 'created_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_menu_link_updated_by', self::TABLE_LINK_NAME, 'updated_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_menu_link', self::TABLE_LINK_NAME, 'menu_id', self::TABLE_NAME, 'id', 'CASCADE');

        $this->createTable(self::TABLE_LINK_LANG_NAME, [
            'id' => $this->primaryKey(),
            'link_id' => $this->string(64)->notNull(),
            'language' => $this->string(6)->notNull(),
            'label' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->createIndex('menu_link_lang_link_id', self::TABLE_LINK_LANG_NAME, 'link_id');
        $this->createIndex('menu_link_lang_language', self::TABLE_LINK_LANG_NAME, 'language');
        $this->addForeignKey('fk_menu_link_lang', self::TABLE_LINK_LANG_NAME, 'link_id', self::TABLE_LINK_NAME, 'id', 'CASCADE', 'CASCADE');

        $this->insert(self::TABLE_NAME, ['id' => 'admin-menu', 'created_by' => 1]);
        $this->insert(self::TABLE_LANG_NAME, ['menu_id' => 'admin-menu', 'language' => 'en-US', 'title' => 'Control Panel Menu']);

        $this->insert(self::TABLE_LINK_NAME, ['id' => 'dashboard', 'menu_id' => 'admin-menu', 'link' => '/', 'image' => 'th', 'created_by' => 1, 'order' => 1]);
        $this->insert(self::TABLE_LINK_LANG_NAME, ['link_id' => 'dashboard', 'label' => 'Dashboard', 'language' => 'en-US']);
    }

    public function down()
    {
        $this->dropForeignKey('fk_menu_created_by', self::TABLE_NAME);
        $this->dropForeignKey('fk_menu_updated_by', self::TABLE_NAME);
        $this->dropForeignKey('fk_menu_link_created_by', self::TABLE_LINK_NAME);
        $this->dropForeignKey('fk_menu_link_updated_by', self::TABLE_LINK_NAME);

        $this->dropForeignKey('fk_menu_link_lang', self::TABLE_LINK_LANG_NAME);
        $this->dropForeignKey('fk_menu_link', self::TABLE_LINK_NAME);
        $this->dropForeignKey('fk_menu_lang', self::TABLE_LANG_NAME);

        $this->dropTable(self::TABLE_LINK_LANG_NAME);
        $this->dropTable(self::TABLE_LINK_NAME);
        $this->dropTable(self::TABLE_LANG_NAME);
        $this->dropTable(self::TABLE_NAME);
    }

}
