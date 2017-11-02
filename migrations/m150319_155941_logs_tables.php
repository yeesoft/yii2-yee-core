<?php

class m150319_155941_logs_tables extends \yii\db\Migration
{

    const USER_TABLE = '{{%user}}';
    const USER_VISIT_LOG_TABLE = '{{%user_visit_log}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

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



        $this->insert(self::USER_TABLE, ['id' => 1, 'username' => 'admin', 'auth_key' => '', 'password_hash' => '', 'email' => '', 'superadmin' => 1, 'created_at' => 0, 'updated_at' => 0]);
    }

    public function down()
    {
        $this->dropTable(self::USER_VISIT_LOG_TABLE);
    }

}
