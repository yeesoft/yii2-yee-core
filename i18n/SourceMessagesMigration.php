<?php

namespace yeesoft\i18n;

use yii\base\NotSupportedException;
use yii\db\Migration;

class SourceMessagesMigration extends Migration
{

    public function up()
    {
        $category = $this->getCategory();
        $messages = $this->getMessages();

        foreach ($messages as $message => $immutable) {
            $this->insert('message_source', ['category' => $category, 'message' => $message, 'immutable' => $immutable]);
        }
    }

    public function down()
    {
        $this->delete('message_source', ['category' => $this->getCategory()]);
    }


    /**
     * Return category of messages
     *
     * @throws NotSupportedException if method is not overriden
     * @return string
     */
    public function getCategory()
    {
        throw new NotSupportedException('Method getCategory should be overriden.');
    }

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
    public function getMessages()
    {
        throw new NotSupportedException('Method getCategory should be overriden.');
    }

}