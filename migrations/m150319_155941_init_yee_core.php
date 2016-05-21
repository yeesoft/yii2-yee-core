<?php

class m150319_155941_init_yee_core extends \yii\db\Migration
{

    const USER_TABLE = '{{%user}}';
    const AUTH_RULE_TABLE = '{{%auth_rule}}';
    const AUTH_ITEM_TABLE = '{{%auth_item}}';
    const AUTH_ITEM_CHILD_TABLE = '{{%auth_item_child}}';
    const AUTH_ITEM_GROUP_TABLE = '{{%auth_item_group}}';
    const AUTH_ASSIGNMENT_TABLE = '{{%auth_assignment}}';
    const USER_VISIT_LOG_TABLE = '{{%user_visit_log}}';
    const USER_SETTING_TABLE = '{{%user_setting}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::AUTH_RULE_TABLE, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ], $tableOptions);

        $this->createTable(self::AUTH_ITEM_GROUP_TABLE, [
            'code' => $this->string(64)->notNull(),
            'name' => $this->string(255)->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (code)',
        ], $tableOptions);

        $this->createTable(self::AUTH_ITEM_TABLE, [
            'name' => $this->string(64)->notNull(),
            'type' => $this->integer()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'group_code' => $this->string(64),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ], $tableOptions);

        $this->createIndex('auth_item_type', self::AUTH_ITEM_TABLE, ['type']);
        $this->addForeignKey('fk_auth_item_table_rule_name', self::AUTH_ITEM_TABLE, ['rule_name'], self::AUTH_RULE_TABLE, ['name'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_auth_item_table_group_code', self::AUTH_ITEM_TABLE, ['group_code'], self::AUTH_ITEM_GROUP_TABLE, ['code'], 'SET NULL', 'CASCADE');

        $this->createTable(self::AUTH_ITEM_CHILD_TABLE, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY (parent, child)',
        ], $tableOptions);

        $this->addForeignKey('fk_parent_auth_item_child_table', self::AUTH_ITEM_CHILD_TABLE, ['parent'], self::AUTH_ITEM_TABLE, ['name'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_child_auth_item_child_table', self::AUTH_ITEM_CHILD_TABLE, ['child'], self::AUTH_ITEM_TABLE, ['name'], 'CASCADE', 'CASCADE');

        $this->createTable(self::AUTH_ASSIGNMENT_TABLE, [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY (item_name, user_id)',
        ], $tableOptions);

        $this->addForeignKey('fk_item_name_auth_assignment_table', self::AUTH_ASSIGNMENT_TABLE, ['item_name'], self::AUTH_ITEM_TABLE, ['name'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_user_id_auth_assignment_table', self::AUTH_ASSIGNMENT_TABLE, ['user_id'], self::USER_TABLE, ['id'], 'CASCADE', 'CASCADE');

        $this->createTable(self::USER_VISIT_LOG_TABLE, [
            'id' => $this->primaryKey(),
            'token' => $this->string(255)->notNull(),
            'ip' => $this->string(15)->notNull(),
            'language' => $this->string(5)->notNull(),
            'user_agent' => $this->string(255)->notNull(),
            'browser' => $this->string(30)->notNull(),
            'os' => $this->string(20)->notNull(),
            'user_id' => $this->integer(),
            'visit_time' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('visit_log_user_id', self::USER_VISIT_LOG_TABLE, 'user_id');
        $this->addForeignKey('fk_user_id_user_visit_log_table', self::USER_VISIT_LOG_TABLE, ['user_id'], self::USER_TABLE, ['id'], 'SET NULL', 'CASCADE');

        $this->createTable(self::USER_SETTING_TABLE, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'key' => $this->string(64)->notNull(),
            'value' => $this->text(),
        ], $tableOptions);
        
        $this->createIndex('user_setting_user_key', self::USER_SETTING_TABLE, ['user_id','key']);
        $this->addForeignKey('fk_user_id_user_setting_table', self::USER_SETTING_TABLE, ['user_id'], self::USER_TABLE, ['id'], 'CASCADE', 'CASCADE');

        $this->insert(self::USER_TABLE, ['id' => 1, 'username' => 'admin', 'auth_key' => '', 'password_hash' => '', 'email' => '', 'superadmin' => 1, 'created_at' => 0, 'updated_at' => 0]);
    }

    public function down()
    {
        $this->dropTable(self::USER_SETTING_TABLE);
        $this->dropTable(self::USER_VISIT_LOG_TABLE);
        $this->dropTable(self::AUTH_ASSIGNMENT_TABLE);
        $this->dropTable(self::AUTH_ITEM_CHILD_TABLE);
        $this->dropTable(self::AUTH_ITEM_TABLE);
        $this->dropTable(self::AUTH_ITEM_GROUP_TABLE);
        $this->dropTable(self::AUTH_RULE_TABLE);
    }

}
