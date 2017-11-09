<?php

use yii\db\Migration;
use yii\db\Schema;

class m150319_150657_alter_user_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'superadmin', $this->integer(6)->defaultValue(0));
        $this->addColumn('{{%user}}', 'avatar', $this->text());
        $this->addColumn('{{%user}}', 'first_name', $this->string(124));
        $this->addColumn('{{%user}}', 'last_name', $this->string(124));
        $this->addColumn('{{%user}}', 'birthday', $this->date());
        $this->addColumn('{{%user}}', 'gender', $this->integer(1));
        $this->addColumn('{{%user}}', 'phone', $this->string(24));
        $this->addColumn('{{%user}}', 'skype', $this->string(64));
        $this->addColumn('{{%user}}', 'about', $this->text());
        $this->addColumn('{{%user}}', 'registration_ip', $this->string(15));
        $this->addColumn('{{%user}}', 'bind_to_ip', $this->string(255));
        $this->addColumn('{{%user}}', 'email_confirmed', $this->integer(1)->defaultValue(0));
        $this->addColumn('{{%user}}', 'confirmation_token', $this->string(127));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'first_name');
        $this->dropColumn('{{%user}}', 'last_name');
        $this->dropColumn('{{%user}}', 'birthday');
        $this->dropColumn('{{%user}}', 'gender');
        $this->dropColumn('{{%user}}', 'phone');
        $this->dropColumn('{{%user}}', 'skype');
        $this->dropColumn('{{%user}}', 'about');
        $this->dropColumn('{{%user}}', 'confirmation_token');
        $this->dropColumn('{{%user}}', 'email_confirmed');
        $this->dropColumn('{{%user}}', 'bind_to_ip');
        $this->dropColumn('{{%user}}', 'registration_ip');
        $this->dropColumn('{{%user}}', 'superadmin');
    }

}
