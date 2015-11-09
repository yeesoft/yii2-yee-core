<?php

namespace yeesoft\models;

use yeesoft\Yee;

/**
 * This is the model class for table "setting".
 *
 * @property string $key
 * @property string $group
 * @property string $value
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class Setting extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
            [['value', 'language'], 'string'],
            [['key', 'group'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'key' => Yee::t('yee', 'Key') ,
            'group' => Yee::t('yee', 'Group'),
            'value' => Yee::t('yee', 'Value'),
            'language' => Yee::t('yee', 'Language'),
        ];
    }

    /**
     * Get setting by group and key
     *
     * @param type $group
     * @param type $key
     * @return type
     */
    public static function getSetting($group, $key, $language = NULL)
    {
        return self::findOne(['group' => $group, 'key' => $key, 'language' => $language]);
    }
}