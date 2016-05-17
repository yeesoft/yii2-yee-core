<?php

namespace yeesoft\db;

use yii\base\NotSupportedException;
use yii\db\Migration;

abstract class TranslatedMessagesMigration extends Migration
{

    public function safeUp()
    {
        $language = $this->getLanguage();
        $messages = $this->getTranslations();
        $sources = $this->getSourceMessages();

        foreach ($messages as $message => $translation) {
            if (isset($sources[$message])) {
                $this->delete('{{%message}}', ['source_id' => $sources[$message]->id, 'language' => $language]); //Delete if exists
                $this->insert('{{%message}}', ['source_id' => $sources[$message]->id, 'translation' => $translation, 'language' => $language]);
            }
        }
    }

    public function safeDown()
    {
        $language = $this->getLanguage();
        $messages = $this->getTranslations();
        $sources = $this->getSourceMessages();

        foreach ($messages as $message => $translation) {
            if (isset($sources[$message])) {
                $this->delete('{{%message}}', ['source_id' => $sources[$message]->id, 'language' => $language]);
            }
        }
    }

    public function getSourceMessages()
    {
        $rows = $this->db->createCommand('SELECT * FROM {{%message_source}} WHERE category = :category')
            ->bindValue(':category', $this->getCategory())
            ->queryAll(\PDO::FETCH_OBJ);

        $messages = [];

        foreach ($rows as $row) {
            $messages[$row->message] = $row;
        }

        return $messages;

    }

    /**
     * Return category of messages
     *
     * @throws NotSupportedException if method is not overriden
     * @return string
     */
    abstract public function getCategory();

    /**
     * Return array of translated messages:
     *
     * [
     *    'Message' => 'Nachricht',
     * ]
     *
     * @throws NotSupportedException if method is not overriden
     * @return array
     */
    abstract public function getTranslations();

    /**
     * Return language code
     *
     * @throws NotSupportedException if method is not overriden
     * @return string
     */
    public function getLanguage()
    {
        throw new NotSupportedException('Method getCategory should be overriden.');
    }
}