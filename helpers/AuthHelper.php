<?php

namespace yeesoft\helpers;

use Yii;
use yii\rbac\DbManager;
use yeesoft\models\AuthRole;

class AuthHelper
{

    const SESSION_PREFIX_LAST_UPDATE = '__auth_last_update';
    const SESSION_PREFIX_ROLES = '__userRoles';
    const SESSION_PREFIX_PERMISSIONS = '__userPermissions';
    const SESSION_PREFIX_ROUTES = '__userRoutes';

    /**
     * Gather all user permissions and roles and store them in the session
     *
     * @param UserIdentity $identity
     */
    public static function updatePermissions($identity)
    {
        $session = Yii::$app->session;

        // Clear data first in case we want to refresh permissions
        $session->remove(self::SESSION_PREFIX_ROLES);
        $session->remove(self::SESSION_PREFIX_PERMISSIONS);
        $session->remove(self::SESSION_PREFIX_ROUTES);

        // Set permissions last mod time
        $session->set(self::SESSION_PREFIX_LAST_UPDATE, filemtime(self::getPermissionsLastModFile()));

        // Save roles, permissions and routes in session
        $session->set(self::SESSION_PREFIX_ROLES, array_keys(AuthRole::getUserRoles($identity->id)));
        $session->set(self::SESSION_PREFIX_PERMISSIONS, array_keys(Permission::getUserPermissions($identity->id)));
        $session->set(self::SESSION_PREFIX_ROUTES, Route::getUserRoutes($identity->id));
    }

    /**
     * Checks if permissions has been changed somehow, and refresh data in session if necessary
     */
    public static function ensurePermissionsUpToDate()
    {
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->session->get(self::SESSION_PREFIX_LAST_UPDATE) != filemtime(self::getPermissionsLastModFile())) {
                static::updatePermissions(Yii::$app->user->identity);
            }
        }
    }

    /**
     * Get path to file that store time of the last auth changes
     *
     * @return string
     */
    public static function getPermissionsLastModFile()
    {
        $file = Yii::$app->runtimePath . '/__permissions_last_mod.txt';

        if (!is_file($file)) {
            file_put_contents($file, '');
            chmod($file, 0777);
        }

        return $file;
    }

    /**
     * Change modification time of permissions last mod file
     */
    public static function invalidatePermissions()
    {
        touch(static::getPermissionsLastModFile());
    }

    /**
     * Get child routes, permissions or roles
     *
     * @param string $itemName
     * @param integer $childType
     *
     * @return array
     */
    public static function getChildrenByType($itemName, $childType)
    {
        $children = (new DbManager())->getChildren($itemName);

        $result = [];

        foreach ($children as $id => $item) {
            if ($item->type == $childType) {
                $result[$id] = $item;
            }
        }

        return $result;
    }

}
