<?php

namespace yeesoft\models;

use Yii;

/**
 * This is the model class for table "auth_model".
 *
 * @property string $name
 * @property string $title
 * @property string $class_name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthFilter[] $filters
 */
class AuthModel extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_model';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title', 'class_name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'title'], 'string', 'max' => 64],
            [['class_name'], 'string', 'max' => 255],
            [['class_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'title' => 'Title',
            'class_name' => 'Class Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(AuthFilter::className(), ['name' => 'filter_name'])
                        ->viaTable('auth_model_filter', ['model_name' => 'name']);
    }

}
