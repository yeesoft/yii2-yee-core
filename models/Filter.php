<?php

namespace yeesoft\models;

use Yii;

/**
 * This is the model class for table "auth_filter".
 *
 * @property integer $id
 * @property string $class_name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthItemFilter[] $authItemFilters
 * @property AuthItem[] $itemNames
 */
class Filter extends \yeesoft\db\ActiveRecord
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
    public function rules()
    {
        return [
            [['class_name', 'name'], 'required'],
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
    public function getRoles()
    {
        return $this->hasMany(Role::className(), ['name' => 'item_name'])
                        ->viaTable('{{%auth_item_filter}}', ['filter_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModels()
    {
        return $this->hasMany(AuthModel::className(), ['id' => 'model_id'])
                        ->viaTable('{{%auth_model_filter}}', ['filter_id' => 'id']);
    }

    public function linkModels($modelIds)
    {
        foreach ($modelIds as $modelId) {
            static::getDb()->createCommand()
                    ->insert('{{%auth_model_filter}}', [
                        'filter_id' => $this->id,
                        'model_id' => $modelId,
                    ])->execute();
        }
    }

    public function unlinkModels($modelIds)
    {
        foreach ($modelIds as $modelId) {
            static::getDb()->createCommand()
                    ->delete('{{%auth_model_filter}}', ['filter_id' => $this->id, 'model_id' => $modelId])
                    ->execute();
        }
    }

}
