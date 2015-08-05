<?php

use yii\db\Schema;

class m150319_155941_init_role_permission_tables extends \yii\db\Migration
{
    const user_table = 'user';
    const auth_rule_table = 'auth_rule';
    const auth_item_table = 'auth_item';
    const auth_item_child_table = 'auth_item_child';
    const auth_item_group_table = 'auth_item_group';
    const auth_assignment_table = 'auth_assignment';
    const user_visit_log_table = 'user_visit_log_table';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }


        $this->createTable(self::auth_rule_table, [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ], $tableOptions);

        $this->createTable(self::auth_item_group_table, [
            'code' => Schema::TYPE_STRING . '(64) NOT NULL',
            'name' => Schema::TYPE_STRING . '(255) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (code)',
        ], $tableOptions);

        $this->createTable(self::auth_item_table, [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'rule_name' => Schema::TYPE_STRING . '(64)',
            'group_code' => Schema::TYPE_STRING . '(64)',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
            'KEY (type)',
            'KEY (group_code)',
            'FOREIGN KEY (rule_name) REFERENCES ' . self::auth_rule_table . ' (name) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (group_code) REFERENCES ' . self::auth_item_group_table . ' (code) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable(self::auth_item_child_table, [
            'parent' => Schema::TYPE_STRING . '(64) NOT NULL',
            'child' => Schema::TYPE_STRING . '(64) NOT NULL',
            'PRIMARY KEY (parent, child)',
            'KEY (child)',
            'FOREIGN KEY (parent) REFERENCES ' . self::auth_item_table . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . self::auth_item_table . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable(self::auth_assignment_table, [
            'item_name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (item_name, user_id)',
            'KEY (user_id)',
            'FOREIGN KEY (item_name) REFERENCES ' . self::auth_item_table . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (user_id) REFERENCES ' . self::user_table . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable(self::user_visit_log_table,
            array(
                'id' => Schema::TYPE_PK,
                'token' => Schema::TYPE_STRING . '(255) NOT NULL',
                'ip' => Schema::TYPE_STRING . '(15) NOT NULL',
                'language' => Schema::TYPE_STRING . '(2) NOT NULL',
                'user_agent' => Schema::TYPE_STRING . '(255) NOT NULL',
                'browser' => Schema::TYPE_STRING . '(30) NOT NULL',
                'os' => Schema::TYPE_STRING . '(20) NOT NULL',
                'user_id' => Schema::TYPE_INTEGER,
                'visit_time' => Schema::TYPE_INTEGER . ' NOT NULL',
                'KEY (user_id)',
                'FOREIGN KEY (user_id) REFERENCES ' . self::user_table . ' (id) ON DELETE SET NULL ON UPDATE CASCADE',
            ), $tableOptions);

    }

    public function down()
    {
        $this->dropTable(self::user_visit_log_table);
        $this->dropTable(self::auth_assignment_table);
        $this->dropTable(self::auth_item_child_table);
        $this->dropTable(self::auth_item_table);
        $this->dropTable(self::auth_item_group_table);
        $this->dropTable(self::auth_rule_table);
    }
}