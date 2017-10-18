<?php

namespace yeesoft\models;

use Yii;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yeesoft\helpers\AuthHelper;

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

    /**
     * Get all routes available for this user
     *
     * @param int $userId
     * @param bool $withSubRoutes
     *
     * @return array
     */
    public static function getUserRoutes($userId, $withSubRoutes = true)
    {
        $permissions = array_keys(Permission::getUserPermissions($userId));

        if (!$permissions) {
            return [];
        }

        $auth_item = Yii::$app->authManager->itemTable;
        $auth_item_child = Yii::$app->authManager->itemChildTable;

        $routes = (new Query)
                ->select(['name'])
                ->from($auth_item)
                ->innerJoin($auth_item_child, '(' . $auth_item_child . '.child = ' . $auth_item . '.name AND ' . $auth_item . '.type = :type)')
                ->params([':type' => self::TYPE_ROUTE])
                ->where([$auth_item_child . '.parent' => $permissions])
                ->column();

        return $withSubRoutes ? static::withSubRoutes($routes, ArrayHelper::map(Route::find()->asArray()->all(), 'name', 'name')) : $routes;
    }

    /**
     * Return given route with all they sub-routes
     *
     * @param array $givenRoutes
     * @param array $allRoutes
     *
     * @return array
     */
    public static function withSubRoutes($givenRoutes, $allRoutes)
    {
        $result = [];

        foreach ($allRoutes as $route) {
            foreach ($givenRoutes as $givenRoute) {
                if (static::isSubRoute($givenRoute, $route)) {
                    $result[] = $route;
                }
            }
        }

        return $result;
    }

    /**
     * Checks if "candidate" is sub-route of "route". For example:
     *
     * "/module/controller/action" is sub-route of "/module/*"
     *
     * @param string $route
     * @param string $candidate
     *
     * @return bool
     */
    public static function isSubRoute($route, $candidate)
    {
        if ($route == $candidate) {
            return true;
        }

        // If it's full access to module or controller
        if (substr($route, -2) == '/*') {
            $route = rtrim($route, '*');

            if (strpos($candidate, $route) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if route is in array of allowed routes
     *
     * @param string $route
     * @param array $allowedRoutes
     *
     * @return boolean
     */
    public static function isRouteAllowed($route, $allowedRoutes)
    {
        $route = rtrim(Yii::$app->getRequest()->getBaseUrl(), '/') . $route;

        if (in_array($route, $allowedRoutes)) {
            return true;
        }

        foreach ($allowedRoutes as $allowedRoute) {
            // If some controller fully allowed (wildcard)
            if (substr($allowedRoute, -1) == '*') {
                $routeArray = explode('/', $route);
                array_splice($routeArray, -1);

                $allowedRouteArray = explode('/', $allowedRoute);
                array_splice($allowedRouteArray, -1);

                if (array_diff($routeArray, $allowedRouteArray) === array()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if controller has $freeAccess = true or $action in $freeAccessActions
     * Or it's login, logout, error page
     *
     * @param string $route
     * @param Action|null $action
     *
     * @return bool
     */
    public static function isFreeAccess($route, $action = null)
    {
        if ($action) {
            $controller = $action->controller;

            if ($controller->hasProperty('freeAccess') AND $controller->freeAccess === true) {
                return true;
            }

            if ($controller->hasProperty('freeAccessActions') AND in_array($action->id, $controller->freeAccessActions)) {
                return true;
            }
        }

        $systemPages = [
            '/auth/logout',
            //Yii::$app->errorHandler->errorAction,
            Yii::$app->user->loginUrl,
        ];

        if (in_array($route, $systemPages)) {
            return true;
        }

        return false;
    }

    public static function getRoutes()
    {
        $routes = static::find()->orderBy(['bundle' => SORT_ASC, 'controller' => SORT_ASC, 'action' => SORT_ASC])->all();
        return ArrayHelper::map($routes, 'id', 'name');
    }

}
