<?php

class m150319_184824_settings_table extends yii\db\Migration
{

    const USER_TABLE = '{{%user}}';
    const SETTINGS_TABLE = '{{%setting}}';
    const USER_SETTING_TABLE = '{{%user_setting}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::SETTINGS_TABLE, [
            'id' => $this->primaryKey(),
            'group' => $this->string(64)->defaultValue('general'),
            'key' => $this->string(64)->notNull(),
            'language' => $this->string(6),
            'value' => $this->text(),
            'description' => $this->text(),
                ], $tableOptions);

        $this->createIndex('setting_group_lang', self::SETTINGS_TABLE, ['group', 'key', 'language']);


        $this->createTable(self::USER_SETTING_TABLE, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'key' => $this->string(64)->notNull(),
            'value' => $this->text(),
                ], $tableOptions);

        $this->createIndex('user_setting_user_key', self::USER_SETTING_TABLE, ['user_id', 'key']);
        $this->addForeignKey('fk_user_id_user_setting_table', self::USER_SETTING_TABLE, ['user_id'], self::USER_TABLE, ['id'], 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(self::USER_SETTING_TABLE);
        $this->dropTable(self::SETTINGS_TABLE);
    }

}
