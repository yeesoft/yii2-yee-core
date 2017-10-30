<?php

namespace yeesoft\rbac;

use Yii;
use yii\rbac\Permission;
use yii\helpers\ArrayHelper;
use yeesoft\models\AuthRole;
use yeesoft\models\AuthRoute;

class DbManager extends \yii\rbac\DbManager implements ManagerInterface
{

    /**
     * @var array the list of actions with free access to all users.
     */
    public $freeAccessActions = [];

    /**
     * @var array the default configuration of access rules.
     */
    public $ruleConfig = ['class' => 'yeesoft\filters\AccessRule'];

    /**
     * @var string the name of the table storing permission groups. Defaults to "auth_group".
     */
    public $groupTable = '{{%auth_group}}';

    /**
     * @var string the name of the table storing relations between permissions and permission groups. Defaults to "auth_item_group".
     */
    public $itemGroupTable = '{{%auth_item_group}}';

    /**
     * @var string the name of the table storing ActiveQuery filters. Defaults to "auth_filter".
     */
    public $filterTable = '{{%auth_filter}}';

    /**
     * @var string the name of the table storing relations between roles and filters. Defaults to "auth_item_filter".
     */
    public $itemFilterTable = '{{%auth_item_filter}}';

    /**
     * @var string the name of the table storing ActiveRecord list. Defaults to "auth_model".
     */
    public $modelTable = '{{%auth_model}}';

    /**
     * @var string the name of the table storing relations between models and filters. Defaults to "auth_model_filter".
     */
    public $modelFilterTable = '{{%auth_model_filter}}';

    /**
     * @var string the name of the table storing authorization routes. Defaults to "auth_route".
     */
    public $routeTable = '{{%auth_route}}';

    /**
     * @var string the name of the table storing relations between permissions and routes. Defaults to "auth_item_route".
     */
    public $itemRouteTable = '{{%auth_item_route}}';

    /**
     * @var array the list of access rules generated from routes settings.
     */
    private $_routeRules;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!Yii::$app->request->isConsoleRequest) {
            $this->freeAccessActions = ArrayHelper::merge($this->freeAccessActions, [
                        Yii::$app->errorHandler->errorAction,
            ]);
        }
    }

    /**
     * 
     * @param string $name
     * @param string $title
     * @param string $className
     */
    public function addFilter($name, $title, $className)
    {
        $this->db->createCommand()
                ->insert($this->filterTable, [
                    'name' => $name,
                    'title' => $title,
                    'class_name' => $className,
                    'created_at' => time(),
                    'updated_at' => time()
                ])->execute();
    }

    /**
     * 
     * @param string $role
     * @param string $filters
     */
    public function addFilterToRole($role, $filters)
    {
        $filters = is_array($filters) ? $filters : [$filters];

        foreach ($filters as $filter) {
            $this->db->createCommand()
                    ->insert($this->itemFilterTable, [
                        'filter_name' => $filter,
                        'item_name' => $role,
                    ])->execute();
        }
    }

    /**
     * 
     * @param string $name
     * @param string $title
     * @param string $className
     */
    public function addModel($name, $title, $className)
    {
        $this->db->createCommand()
                ->insert($this->modelTable, [
                    'name' => $name,
                    'title' => $title,
                    'class_name' => $className,
                    'created_at' => time(),
                    'updated_at' => time()
                ])->execute();
    }

    /**
     * 
     * @param string $filter
     * @param string|array $models
     */
    public function addModelToFilter($filter, $models)
    {
        $models = is_array($models) ? $models : [$models];

        foreach ($models as $model) {
            $this->db->createCommand()
                    ->insert($this->modelFilterTable, [
                        'filter_name' => $filter,
                        'model_name' => $model,
                    ])->execute();
        }
    }

    /**
     * 
     * @param string $item
     * @param string $group
     */
    public function addPermissionToGroup($item, $group)
    {
        $this->db->createCommand()
                ->insert($this->itemGroupTable, [
                    'item_name' => $item,
                    'group_name' => $group,
                ])->execute();
    }

    /**
     * Creates new permissions group.
     * 
     * @param string $name
     * @param string $title
     */
    public function addPermissionsGroup($name, $title)
    {
        $this->db->createCommand()
                ->insert($this->groupTable, [
                    'name' => $name,
                    'title' => $title,
                    'created_at' => time(),
                    'updated_at' => time()
                ])->execute();
    }

    /**
     * 
     * @param string $bundle
     * @param string $controller
     * @param string $action
     */
    public function addRoute($bundle, $controller, $action = null)
    {
        $this->db->createCommand()
                ->insert($this->routeTable, [
                    'bundle' => $bundle,
                    'controller' => $controller,
                    'action' => $action,
                    'created_at' => time(),
                    'updated_at' => time()
                ])->execute();
    }

    /**
     * 
     * @param string $permission
     * @param string|array $routes
     */
    public function addRoutesToPermission($permission, $routes)
    {
        $routes = is_array($routes) ? $routes : [$routes];

        foreach ($routes as $route) {
            $this->db->createCommand()
                    ->insert($this->itemRouteTable, [
                        'item_name' => $permission,
                        'route_id' => $route,
                    ])->execute();
        }
    }

    /**
     * 
     * @param string $permission
     * @param string $rule
     */
    public function addRuleToPermission($permission, $rule)
    {
        $this->db->createCommand()
                ->update($this->itemTable, ['rule_name' => $rule], ['name' => $permission, 'type' => Permission::TYPE_PERMISSION, 'updated_at' => time()])
                ->execute();
    }

    /**
     * @inheritdoc
     */
    public function flushRouteCache()
    {
        $bundles = AuthRoute::find()->distinct('bundle')->select('bundle')->column();
        foreach ($bundles as $bundle) {
            Yii::$app->cache->delete([trim($bundle, ' /'), static::CACHE_AUTH_ROUTES]);
        }
    }

    /**
     * @return string base URL
     */
    protected function getBaseUrl()
    {
        return trim(Yii::$app->getUrlManager()->getBaseUrl(), ' /');
    }

    /**
     * @inheritdoc
     */
    public function getFiltersByRole($modelClass, $role)
    {
        return Yii::$app->cache->getOrSet([$role, $modelClass, static::CACHE_AUTH_FILTERS], function($cache) use($modelClass, $role) {
                    $filters = [];

                    if ($role = AuthRole::findOne($role)) {
                        $filters = $role->getFilters()
                                        ->joinWith('models')
                                        ->andWhere(["{$this->modelTable}.class_name" => $modelClass])
                                        ->select("{$this->filterTable}.class_name")->column();
                    }

                    return $filters;
                });
    }

    /**
     * @inheritdoc
     */
    public function getFiltersByUser($modelClass, $userId)
    {
        return Yii::$app->cache->getOrSet([$userId, $modelClass, static::CACHE_AUTH_USER_FILTERS], function($cache) use($modelClass, $userId) {
                    $filters = [];
                    $roles = array_keys($this->getRolesByUser($userId));

                    foreach ($roles as $role) {
                        $filters = ArrayHelper::merge($filters, $this->getFiltersByRole($modelClass, $role));
                    }

                    return $filters;
                });
    }

    /**
     * @inheritdoc
     */
    public function getRouteRules($ruleConfig = null)
    {
        if (!$this->_routeRules) {

            $this->_routeRules = [];

            if (!$ruleConfig) {
                $ruleConfig = $this->ruleConfig;
            }

            $routes = $this->getRoutes();
            foreach ($routes as $route) {
                $this->_routeRules[] = Yii::createObject(array_merge($ruleConfig, $route->rule));
            }
        }

        return $this->_routeRules;
    }

    /**
     * @inheritdoc
     */
    public function getRoutes()
    {
        return Yii::$app->cache->getOrSet([$this->baseUrl, static::CACHE_AUTH_ROUTES], function($cache) {
                    return AuthRoute::find()
                                    ->where(['bundle' => $this->baseUrl])
                                    ->joinWith(['permissions' => function($query) {
                                            $query->select(['name'])->where(['type' => Permission::TYPE_PERMISSION]);
                                        }])->all();
                });
    }

    /**
     * @inheritdoc
     */
    public function hasFreeAccess($route, $action = null)
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

        if (in_array($route, $this->freeAccessActions)) {
            return true;
        }

        return false;
    }

    /**
     * 
     * @param string $name
     */
    public function removeFilter($name)
    {
        $this->db->createCommand()
                ->delete($this->filterTable, ['name' => $name])->execute();
    }

    /**
     * 
     * @param string $role
     * @param string|array $filters
     */
    public function removeFilterFromRole($role, $filters)
    {
        $filters = is_array($filters) ? $filters : [$filters];

        foreach ($filters as $filter) {
            $this->db->createCommand()
                    ->delete($this->itemFilterTable, [
                        'filter_name' => $filter,
                        'item_name' => $role,
                    ])->execute();
        }
    }

    /**
     * 
     * @param string $name
     */
    public function removeModel($name)
    {
        $this->db->createCommand()->delete($this->modelTable, ['name' => $name])->execute();
    }

    /**
     * 
     * @param string $filter
     * @param string $models
     */
    public function removeModelFromFilter($filter, $models)
    {
        $models = is_array($models) ? $models : [$models];

        foreach ($models as $model) {
            $this->db->createCommand()
                    ->delete($this->modelFilterTable, [
                        'filter_name' => $filter,
                        'model_name' => $model,
                    ])->execute();
        }
    }

    /**
     * 
     * @param string $item
     * @param string $group
     */
    public function removePermissionFromGroup($item, $group)
    {
        $this->db->createCommand()
                ->delete($this->itemGroupTable, [
                    'item_name' => $item,
                    'group_name' => $group,
                ])->execute();
    }

    /**
     * Deletes permissions group.
     * 
     * @param string $name
     */
    public function removePermissionsGroup($name)
    {
        $this->db->createCommand()->delete($this->groupTable, ['name' => $name])->execute();
    }

    /**
     * 
     * @param string $bundle
     * @param string $controller
     * @param string $action
     */
    public function removeRoute($bundle, $controller, $action = null)
    {
        $this->db->createCommand()
                ->delete($this->routeTable, ['bundle' => $bundle, 'controller' => $controller, 'action' => $action])->execute();
    }

    /**
     * 
     * @param string $permission
     * @param string|array $routes
     */
    public function removeRoutesFromPermission($permission, $routes)
    {
        $routes = is_array($routes) ? $routes : [$routes];

        foreach ($routes as $route) {
            $this->db->createCommand()
                    ->delete($this->itemRouteTable, [
                        'item_name' => $permission,
                        'route_id' => $route,
                    ])->execute();
        }
    }

    /**
     * 
     * @param string $permission
     */
    public function removeRuleFromPermission($permission)
    {
        $this->db->createCommand()
                ->update($this->itemTable, ['rule_name' => null], ['name' => $permission, 'type' => Permission::TYPE_PERMISSION, 'updated_at' => time()])
                ->execute();
    }

}
