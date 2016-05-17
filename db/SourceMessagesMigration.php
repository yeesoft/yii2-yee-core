<?php

namespace yeesoft\db;

use yii\base\NotSupportedException;
use yii\db\Migration;

abstract class SourceMessagesMigration extends Migration
{

    public function safeUp()
    {
        $category = $this->getCategory();
        $messages = $this->getMessages();

        foreach ($messages as $message => $immutable) {
            $this->insert('{{%message_source}}', ['category' => $category, 'message' => $message, 'immutable' => $immutable]);
        }
    }

    public function safeDown()
    {
        $this->delete('{{%message_source}}', ['category' => $this->getCategory()]);
    }


    /**
     * Return category of messages
     *
     * @throws NotSupportedException if method is not overriden
     * @return string
     */
    abstract public function getCategory();

    /**
     * Return array of source messages with immutable indication:
     *
     * [
     *    'Message' => 1,
     *    'Source' => 0,
     * ]
     *
     * @throws NotSupportedException if method is not overriden
     * @return array
     */
    public abstract function getMessages();

}