<?php

use yii\db\Migration;
use yii\db\Schema;

class m160831_224932_alter_user_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'first_name', $this->string(124));
        $this->addColumn('{{%user}}', 'last_name', $this->string(124));
        $this->addColumn('{{%user}}', 'birth_day', $this->integer(2));
        $this->addColumn('{{%user}}', 'birth_month', $this->integer(2));
        $this->addColumn('{{%user}}', 'birth_year', $this->integer(4));
        $this->addColumn('{{%user}}', 'gender', $this->integer(1));
        $this->addColumn('{{%user}}', 'phone', $this->string(24));
        $this->addColumn('{{%user}}', 'skype', $this->string(64));
        $this->addColumn('{{%user}}', 'info', $this->string(255));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'first_name');
        $this->dropColumn('{{%user}}', 'last_name');
        $this->dropColumn('{{%user}}', 'birth_day');
        $this->dropColumn('{{%user}}', 'birth_month');
        $this->dropColumn('{{%user}}', 'birth_year');
        $this->dropColumn('{{%user}}', 'gender');
        $this->dropColumn('{{%user}}', 'phone');
        $this->dropColumn('{{%user}}', 'skype');
        $this->dropColumn('{{%user}}', 'info');
    }

}
