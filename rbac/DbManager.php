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

}
