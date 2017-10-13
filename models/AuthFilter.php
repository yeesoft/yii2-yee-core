<?php

namespace yeesoft\models;

/**
 * This is the model class for table "auth_filter".
 *
 * @property string $name
 * @property string $title
 * @property string $class_name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthRole[] $roles
 * @property AuthModel[] $models
 */
class AuthFilter extends \yeesoft\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_filter}}';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['title', 'class_name'];
        return $scenarios;
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
    public function getRoles()
    {
        return $this->hasMany(Role::className(), ['name' => 'item_name'])
                        ->viaTable('{{%auth_item_filter}}', ['filter_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModels()
    {
        return $this->hasMany(AuthModel::className(), ['name' => 'model_name'])
                        ->viaTable('{{%auth_model_filter}}', ['filter_name' => 'name']);
    }

}
