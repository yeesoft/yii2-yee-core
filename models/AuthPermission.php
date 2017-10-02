<?php

namespace yeesoft\models;

use Exception;
use yeesoft\helpers\AuthHelper;
use Yii;
use yii\rbac\DbManager;

class AuthPermission extends AuthItem
{

    const ITEM_TYPE = self::TYPE_PERMISSION;

    public function linkRoutes($ids)
    {
        $routes = Route::findAll($ids);
        foreach ($routes as $route) {
            $this->link('routes', $route);
        }
    }
    
    public function unlinkRoutes($ids)
    {
        $routes = Route::findAll($ids);
        foreach ($routes as $route) {
            $this->unlink('routes', $route, true);
        }
    }
    
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

}
