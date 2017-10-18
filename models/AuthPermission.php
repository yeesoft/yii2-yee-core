<?php

namespace yeesoft\models;

use Yii;
use Exception;
use yii\rbac\DbManager;
use yii\helpers\ArrayHelper;
use yeesoft\helpers\AuthHelper;

class AuthPermission extends AuthItem
{

    public $groupName;

    const ITEM_TYPE = self::TYPE_PERMISSION;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
                    ['groupName', 'string']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['description', 'rule_name', 'groupName'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_FIND, [$this, 'loadGroupName']);
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'saveGroup']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'saveGroup']);
    }

//    public function linkRoutes($ids)
//    {
//        $routes = AuthRoute::findAll($ids);
//        foreach ($routes as $route) {
//            $this->link('routes', $route);
//        }
//    }
//
//    public function unlinkRoutes($ids)
//    {
//        $routes = AuthRoute::findAll($ids);
//        foreach ($routes as $route) {
//            $this->unlink('routes', $route, true);
//        }
//    }

    /**
     * @param int $userId
     *
     * @return array|\yii\rbac\Permission[]
     */
    public static function getUserPermissions($userId)
    {
        return (new DbManager())->getPermissionsByUser($userId);
    }

    /**
     * Assign route to permission and create them if they don't exists
     * Helper mainly for migrations
     *
     * @param string $permissionName
     * @param array|string $routes
     * @param null|string $permissionDescription
     * @param null|string $groupName
     *
     * @throws \InvalidArgumentException
     * @return true|static|string
     */
    public static function assignRoutes($permissionName, $routes, $permissionDescription = null, $groupName = null)
    {
        $permission = static::findOne(['name' => $permissionName]);
        $routes = (array) $routes;

        if (!$permission) {
            $permission = static::create($permissionName, $permissionDescription, $groupName);

            if ($permission->hasErrors()) {
                return $permission;
            }
        }

        foreach ($routes as $route) {
            $route = '/' . ltrim($route, '/');
            try {
                Yii::$app->db->createCommand()
                        ->insert(Yii::$app->authManager->itemChildTable, [
                            'parent' => $permission->name,
                            'child' => $route,
                        ])->execute();
            } catch (Exception $e) {
                // Don't throw Exception because this permission may already have this route,
                // so just go to the next route
            }
        }

        AuthHelper::invalidatePermissions();

        return true;
    }

    public function getGroup()
    {
        return ($groups = $this->groups) ? array_shift($groups) : null;
    }

    /**
     * @inheritdoc
     */
    public function loadGroupName()
    {
        $this->groupName = @$this->group->name;
    }

    public function saveGroup()
    {
        if ($this->groupName AND $group = AuthGroup::findOne($this->groupName)) {
            $this->unlinkAll('groups', true);
            $this->link('groups', $group);
        }
    }

    /**
     * 
     * @param array $exclude
     * @return array
     */
    public static function getGroupedPermissions($exclude = [])
    {
        $result = [];
        $permissions = static::find()
                ->andWhere(['not in', Yii::$app->authManager->itemTable . '.name', is_array($exclude) ? $exclude : [$exclude]])
                ->joinWith('groups')
                ->all();

        foreach ($permissions as $permission) {
            $result[@$permission->group->title][$permission->name] = $permission->description;
        }

        return $result;
    }

}
