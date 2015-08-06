<?php

namespace yeesoft\models;

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
            [['value'], 'string'],
            [['key', 'group'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'key' => 'Key',
            'group' => 'Group',
            'value' => 'Value',
        ];
    }

    /**
     * Get setting by group and key
     *
     * @param type $group
     * @param type $key
     * @return type
     */
    public static function getSetting($group, $key)
    {
        return self::findOne(['group' => $group, 'key' => $key]);
    }
}