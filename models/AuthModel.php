<?php

namespace yeesoft\models;

use Yii;

/**
 * This is the model class for table "auth_model".
 *
 * @property integer $id
 * @property string $name
 * @property string $class_name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthModelFilter[] $authModelFilters
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
            [['name', 'class_name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 127],
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
            'id' => 'ID',
            'name' => 'Name',
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
        return $this->hasMany(AuthFilter::className(), ['id' => 'filter_id'])
                        ->viaTable('auth_model_filter', ['model_id' => 'id']);
    }

}
