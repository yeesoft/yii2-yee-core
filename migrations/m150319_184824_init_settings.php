<?php

use yii\db\Migration;
use yii\db\Schema;

class m150319_184824_init_settings extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('setting',
            [
                'id' => Schema::TYPE_PK,
                'group' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci DEFAULT "general"',
                'key' => Schema::TYPE_STRING . '(64) COLLATE utf8_unicode_ci NOT NULL',
                'language' => Schema::TYPE_STRING . '(6) COLLATE utf8_unicode_ci DEFAULT NULL',
                'value' => Schema::TYPE_TEXT . ' COLLATE utf8_unicode_ci NOT NULL',
                'description' => Schema::TYPE_TEXT . ' COLLATE utf8_unicode_ci DEFAULT NULL',
            ], $tableOptions);

        $this->createIndex('setting_group_lang', 'setting', 'group', 'language');
        $this->createIndex('setting_group', 'setting', 'group');

        $this->insert('setting',
            ['group' => 'general', 'key' => 'title', 'language' => 'en', 'value' => 'Yee Site']);

        $this->insert('setting',
            ['group' => 'general', 'key' => 'description', 'value' => '']);

        $this->insert('setting',
            ['group' => 'general', 'key' => 'email', 'value' => '', 'description' => 'This address is used for admin purposes, like new user notification.']);

        $this->insert('setting',
            ['group' => 'general', 'key' => 'dateformat', 'value' => 'F j, Y']);

        $this->insert('setting',
            ['group' => 'general', 'key' => 'timeformat', 'value' => 'g:i a']);

        $this->insert('setting',
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Europe/London']);

        $this->insert('setting',
            ['group' => 'reading', 'key' => 'page_size', 'value' => '10', 'description' => 'The number of items per page.']);
    }

    public function down()
    {
        $this->dropTable('setting');
    }
}