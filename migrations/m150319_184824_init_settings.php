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

        $this->createIndex('setting_group_lang', 'setting', ['group', 'key', 'language']);

    }

    public function down()
    {
        $this->dropTable('setting');
    }
}