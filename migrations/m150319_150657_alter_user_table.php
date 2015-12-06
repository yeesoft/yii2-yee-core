<?php

use yii\db\Migration;
use yii\db\Schema;

class m150319_150657_alter_user_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('user', 'superadmin', Schema::TYPE_SMALLINT . "(6) DEFAULT '0' AFTER `status`");
        $this->addColumn('user', 'registration_ip', Schema::TYPE_STRING . "(15) DEFAULT NULL AFTER `updated_at`");
        $this->addColumn('user', 'bind_to_ip', Schema::TYPE_STRING . "(255) DEFAULT NULL AFTER `updated_at`");
        $this->addColumn('user', 'email_confirmed', Schema::TYPE_SMALLINT . "(1) NOT NULL DEFAULT '0' AFTER `updated_at`");
        $this->addColumn('user', 'confirmation_token', Schema::TYPE_STRING . "(255) DEFAULT NULL AFTER `updated_at`");
        $this->addColumn('user', 'avatar', Schema::TYPE_TEXT . " DEFAULT NULL AFTER `superadmin`");
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'confirmation_token');
        $this->dropColumn('user', 'email_confirmed');
        $this->dropColumn('user', 'bind_to_ip');
        $this->dropColumn('user', 'registration_ip');
        $this->dropColumn('user', 'superadmin');
    }
}