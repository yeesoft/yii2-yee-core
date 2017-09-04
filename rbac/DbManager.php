<?php

namespace yeesoft\rbac;

use Yii;
use yii\db\Query;
use yii\rbac\Item;
use yii\rbac\Role;
use yii\rbac\Permission;
use yeesoft\models\Route;
use yeesoft\models\Role as RoleModel;
use yii\helpers\ArrayHelper;

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
     * @var string the name of common permission. 
     */
    public $commonPermissionName = 'commonPermission';

    /**
     * @var string the name of the table storing authorization item groups. Defaults to "auth_item_group".
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
    public $itemModelTable = '{{%auth_model_filter}}';

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

    public function init()
    {
        parent::init();

        if (!Yii::$app->request->isConsoleRequest) {
            $this->freeAccessActions = ArrayHelper::merge($this->freeAccessActions, [
                        Yii::$app->errorHandler->errorAction,
            ]);
        }
    }

//    /**
//     * @inheritdoc
//     */
//    public function createRoute($name)
//    {
//        $route = new Route();
//        $route->name = $name;
//        return $route;
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function getRoute($name)
//    {
//        $item = $this->getItem($name);
//        return $item instanceof Item && $item->type == Route::TYPE_ROUTE ? $item : null;
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function getRoutes()
//    {
//        return $this->getItems(Route::TYPE_ROUTE);
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function getRoutesByUser($userId)
//    {
//        if (empty($userId)) {
//            return [];
//        }
//
//        $permissions = @array_keys($this->getPermissionsByUser($userId));
//        if (empty($permissions)) {
//            return [];
//        }
//
//        return $this->getRoutesByItem($permissions);
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function getRoutesByRole($roleName)
//    {
//        return $this->getRoutesByItem($roleName);
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function getRoutesByPermission($permissionName)
//    {
//        return $this->getRoutesByItem($permissionName);
//    }

    /**
     * @inheritdoc
     */
    public function getCommonPermissions()
    {
        if ($this->commonPermissionName) {
            return $this->getChildren($this->commonPermissionName);
        }
        return [];
    }

    /**
     * @inheritdoc
     */
//    public function getCommonRoutes()
//    {
//        $commonPermissions = array_keys($this->getCommonPermissions());
//        return $this->getRoutesByPermission($commonPermissions);
//    }

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

//        if (in_array($route, array_keys($this->getCommonRoutes()))) {
//            return true;
//        }

        return false;
    }

    /**
     * Returns all routes that the specified authorization item (Role or Permission) represents.
     * @param string|array $itemName the authorization item name or an array of names
     * @return Route[] all routes that the authorization item represents. The array is indexed by the route names.
     */
//    protected function getRoutesByItem($itemName)
//    {
//        $result = [];
//        $childrenList = $this->getChildrenList();
//
//        if (is_string($itemName)) {
//            $itemName = [$itemName];
//        } elseif (!is_array($itemName)) {
//            throw new \yii\base\InvalidParamException('Parameter $itemName must be either a string of an array.');
//        }
//
//        foreach ($itemName as $item) {
//            if (isset($childrenList[$item])) {
//                foreach ($childrenList[$item] as $child) {
//                    $result[$child] = true;
//                    $this->getChildrenRecursive($child, $childrenList, $result);
//                }
//            }
//        }
//
//        if (empty($result)) {
//            return [];
//        }
//
//        $query = (new Query)->from($this->itemTable)->where([
//            'type' => Route::TYPE_ROUTE,
//            'name' => array_keys($result),
//        ]);
//
//        $routes = [];
//        foreach ($query->all($this->db) as $row) {
//            $routes[$row['name']] = $this->populateItem($row);
//        }
//
//        return $routes;
//    }


    public function getRoutes()
    {
        return Yii::$app->cache->getOrSet([$this->baseUrl, static::AUTH_ROUTES], function($cache) {
                    return Route::find()
                                    ->where(['base_url' => $this->baseUrl])
                                    ->joinWith(['permissions' => function($query) {
                                            $query->select(['name'])->where(['type' => Permission::TYPE_PERMISSION]);
                                        }])->all();
                });
    }

    public function flushRouteCache()
    {
        $baseUrls = Route::find()->distinct('base_url')->select('base_url')->column();
        foreach ($baseUrls as $baseUrl) {
            Yii::$app->cache->delete([trim($baseUrl, ' /'), ManagerInterface::AUTH_ROUTES]);
        }
    }

    protected function getBaseUrl()
    {
        return trim(Yii::$app->getUrlManager()->getBaseUrl(), ' /');
    }

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

    public function getFiltersByRole($modelClass, $role)
    {
        return Yii::$app->cache->getOrSet([$role, $modelClass, static::CACHE_AUTH_FILTERS], function($cache) use($modelClass, $role) {
                    $filters = [];
                    
                    if ($role = RoleModel::findOne($role)) {
                        $filters = $role->getFilters()
                                ->joinWith('models')
                                ->andWhere(['auth_model.class_name' => $modelClass])
                                ->select('auth_filter.class_name')->column();

                    }
                    
                    return $filters;
                });
    }

    public function getFiltersByUserId($modelClass, $userId)
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

}
