<?php

namespace yeesoft\models;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "auth_route".
 *
 * @property integer $id
 * @property string $bundle
 * @property string $controller
 * @property string $action
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthItemRoute[] $authItemRoutes
 * @property AuthItem[] $itemNames
 */
class AuthRoute extends \yeesoft\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_route}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['controller'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['bundle', 'action'], 'string', 'max' => 64],
            [['controller'], 'string', 'max' => 128],
            [['bundle', 'controller', 'action'], 'unique', 'targetAttribute' => ['bundle', 'controller', 'action'], 'message' => 'The combination of Bundle, Controller and Action has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bundle' => 'Bundle',
            'controller' => 'Controller',
            'action' => 'Action',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(AuthPermission::className(), ['name' => 'item_name'])
            ->viaTable('{{%auth_item_route}}', ['route_id' => 'id']);
    }

    public function getName()
    {
        return '/' . implode('/', array_filter([trim($this->bundle, ' /'), trim($this->controller, ' /'), trim($this->action, ' /')]));
    }

    public function getRule()
    {
        $rule['allow'] = true;
        $rule['controllers'] = [$this->controller];

        if (!empty($this->action)) {
            $rule['actions'] = [$this->action];
        }

        foreach ($this->permissions as $permission) {
            $rule['roles'][] = $permission->name;
        }

        return $rule;
    }

    public static function getRoutes()
    {
        $routes = static::find()->orderBy(['bundle' => SORT_ASC, 'controller' => SORT_ASC, 'action' => SORT_ASC])->all();
        return ArrayHelper::map($routes, 'id', 'name');
    }

}
