<?php

namespace yeesoft\rbac;

use yii\rbac\Role;
use yii\rbac\Rule;
use yii\rbac\Permission;

interface ManagerInterface extends \yii\rbac\ManagerInterface
{

    /**
     * Adds a role, permission, route or rule to the RBAC system.
     * @param Role|Permission|Rule|Route $object
     * @return bool whether the role, permission, route or rule is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the role, route or permission is not unique)
     */
    public function add($object);

    /**
     * Creates a new Route object.
     * Note that the newly created route is not added to the RBAC system yet.
     * You must fill in the needed data and call [[add()]] to add it to the system.
     * @param string $name the role name
     * @return Route the new Route object
     */
    public function createRoute($name);

    /**
     * Returns the named route.
     * @param string $name the route name.
     * @return null|Route the route corresponding to the specified name. Null is returned if no such route.
     */
    public function getRoute($name);

    /**
     * Returns all routes in the system.
     * @return Route[] all routes in the system. The array is indexed by the route names.
     */
    public function getRoutes();

    /**
     * Returns the routes that are assigned to the user.
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return Route[] all routes assigned to the user. The array is indexed by the route names.
     */
    public function getRoutesByUser($userId);

    /**
     * Returns all routes that the specified role represents.
     * @param string $roleName the role name
     * @return Route[] all routes that the role represents. The array is indexed by the route names.
     */
    public function getRoutesByRole($roleName);

    /**
     * Returns all routes that are assigned to permissions.
     * @param string|array $permission the one or list of permissions
     * @return Route[] all routes that the permissions has. The array is indexed by the route names.
     */
    public function getRoutesByPermission($permission);

    /**
     * Check if controller has $freeAccess = true or $action in $freeAccessActions
     * either it's global free access actions (like error page) or action that 
     * the common permission represents.
     * @param string $route
     * @param \yii\base\Action|null $action
     * @return bool whether the route or action has free access
     */
    public function hasFreeAccess($route, $action = null);

    /**
     * Returns all common permissions in the system.
     * @return Permission[] all common permissions in the system.
     */
    public function getCommonPermissions();

    /**
     * Returns all routes that are assigned to common permissions.
     * @return Route[] all routes that are assigned to common permissions.
     */
    public function getCommonRoutes();
}
