<?php

use yii\db\Migration;
use yii\db\Schema;

class m150319_150657_alter_user_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'superadmin', $this->integer(6)->defaultValue(0));
        $this->addColumn('{{%user}}', 'registration_ip', $this->string(15));
        $this->addColumn('{{%user}}', 'bind_to_ip', $this->string(255));
        $this->addColumn('{{%user}}', 'email_confirmed', $this->integer(1)->defaultValue(0));
        $this->addColumn('{{%user}}', 'confirmation_token', $this->string(255));
        $this->addColumn('{{%user}}', 'avatar', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'confirmation_token');
        $this->dropColumn('{{%user}}', 'email_confirmed');
        $this->dropColumn('{{%user}}', 'bind_to_ip');
        $this->dropColumn('{{%user}}', 'registration_ip');
        $this->dropColumn('{{%user}}', 'superadmin');
    }
}