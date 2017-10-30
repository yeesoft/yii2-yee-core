<?php

namespace yeesoft\db;

use Yii;
use Exception;
use yii\db\Query;
use yii\rbac\Role;
use yii\rbac\Permission;
use yeesoft\rbac\DbManager;
use yeesoft\models\AuthGroup;
use yeesoft\models\AuthPermission;

/**
 * This class helps to create migrations for modules permissions. 
 * 
 * @property DbManager $authManager
 */
abstract class PermissionsMigration extends \yii\db\Migration
{

    const ADMIN_BUNDLE = 'admin';
    const SITE_BUNDLE = '';
    const API_BUNDLE = 'api';
    const ROLE_ADMIN = 'administrator';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_AUTHOR = 'author';
    const ROLE_USER = 'user';

    /**
     * Returns an array of permissions settings. Example of settings that 
     * may be returned by the method:
     * 
     * ```
     * 'groupCode' => [
     *     'view-pages' => [
     *         'title' => 'View Pages',
     *         'roles' => [self::ROLE_USER, self::ROLE_AUTHOR],
     *         'routes' => [
     *             ['bundle' => self::ADMIN_BUNDLE, 'controller' => 'page/default', 'action' => 'index'],
     *          ],
     *      ],
     *      'edit-pages' => [
     *           'title' => 'Edit Pages',
     *           'child' => ['view-pages'],
     *           'roles' => [self::ROLE_AUTHOR],
     *           'rule' => yeesoft\rbac\AuthorRule::class,
     *            'routes' => [
     *               ['bundle' => self::ADMIN_BUNDLE, 'controller' => 'page/default', 'action' => 'update'],
     *               ['bundle' => self::ADMIN_BUNDLE, 'controller' => 'page/default', 'action' => 'bulk-update'],
     *           ],
     *      ],
     * ]
     * ```
     */
    public function getPermissions()
    {
        return [];
    }

    public function safeUp()
    {
        $this->createPermissions($this->getPermissions());
    }

    public function safeDown()
    {
        $this->removePermissions($this->getPermissions());
    }

    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    public function createPermissions($settings)
    {
        $this->validatePermissionGroupNames(array_keys($settings));

        foreach ($settings as $groupName => $permissions) {

            $this->validatePermissionNames(array_keys($permissions));

            foreach ($permissions as $permissionName => $permission) {

                if (!isset($permission['title'])) {
                    throw new Exception('Permission title is required.');
                }

                if (isset($permission['routes'])) {
                    foreach ($permission['routes'] as $route) {
                        if (!isset($route['bundle']) || !isset($route['controller'])) {
                            throw new Exception('Bundle and controller are required for the route.');
                        }
                        $this->addRoute($route['bundle'], $route['controller'], isset($route['action']) ? $route['action'] : null);
                    }
                }

                $this->addPermission($permissionName, $permission['title']);
                $this->addPermissionToGroup($permissionName, $groupName);

                if (isset($permission['rule'])) {
                    $this->addRuleToPermission($permissionName, $permission['rule']);
                }

                if (isset($permission['routes'])) {
                    foreach ($permission['routes'] as $route) {
                        $this->addRouteToPermission($permissionName, $route['bundle'], $route['controller'], isset($route['action']) ? $route['action'] : null);
                    }
                }

                if (isset($permission['child'])) {
                    foreach ($permission['child'] as $childName) {
                        $this->addChildPermission($permissionName, $childName);
                    }
                }

                if (isset($permission['roles'])) {
                    foreach ($permission['roles'] as $roleName) {
                        $this->addPermissionToRole($roleName, $permissionName);
                    }
                }
            }
        }
    }

    /**
     * Link child permission item to parent.
     * 
     * @param string $parent
     * @param string $child
     */
    public function addChildPermission($parent, $child)
    {
        $this->authManager->addChild(new Permission(['name' => $parent]), new Permission(['name' => $child]));
    }

    /**
     * Link child role item to parent.
     * 
     * @param string $parent
     * @param string $child
     */
    public function addChildRole($parent, $child)
    {
        $this->authManager->addChild(new Role(['name' => $parent]), new Role(['name' => $child]));
    }

    public function addFilter($name, $title, $className)
    {
        $this->authManager->addFilter($name, $title, $className);
    }

    public function addFilterToRole($role, $filters)
    {
        $this->authManager->addFilterToRole($role, $filters);
    }

    public function addModel($name, $title, $className)
    {
        $this->authManager->addModel($name, $title, $className);
    }

    public function addModelToFilter($filter, $models)
    {
        $this->authManager->addModelToFilter($filter, $models);
    }

    /**
     * Adds a permission.
     * 
     * @param string $name
     * @param string $description
     */
    public function addPermission($name, $description)
    {
        $this->authManager->add(new Permission(['name' => $name, 'description' => $description, 'createdAt' => time(), 'updatedAt' => time()]));
    }

    public function addPermissionToGroup($name, $group)
    {
        $this->authManager->addPermissionToGroup($name, $group);
    }

    /**
     * Link permission to role.
     * 
     * @param string $role
     * @param string $permission
     */
    public function addPermissionToRole($role, $permission)
    {
        $this->authManager->addChild(new Role(['name' => $role]), new Permission(['name' => $permission]));
    }

    /**
     * Creates new permissions group.
     * 
     * @param string $name
     * @param string $title
     */
    public function addPermissionsGroup($name, $title)
    {
        $this->authManager->addPermissionsGroup($name, $title);
    }

    /**
     * Adds a role.
     * 
     * @param string $name
     * @param string $description
     */
    public function addRole($name, $description)
    {
        $this->authManager->add(new Role(['name' => $name, 'description' => $description, 'createdAt' => time(), 'updatedAt' => time()]));
    }

    public function addRoute($bundle, $controller, $action = null)
    {
        $this->authManager->addRoute($bundle, $controller, $action);
    }

    public function addRouteToPermission($permission, $bundle, $controller, $action = null)
    {
        $route = (new Query())->select(['id'])
                ->from($this->authManager->routeTable)
                ->where(['bundle' => $bundle, 'controller' => $controller, 'action' => $action])
                ->one();

        if (!$route) {
            $path = implode('/', [$bundle, $controller, $action]);
            throw new Exception('Route "' . $path . '" does not exist.');
        }

        $this->authManager->addRoutesToPermission($permission, $route['id']);
    }

    public function addRule($className)
    {
        $this->authManager->add(new $className);
    }

    public function addRuleToPermission($permission, $ruleClassName)
    {
        $this->authManager->addRuleToPermission($permission, (new $ruleClassName)->name);
    }

    /**
     * Unlink child auth item from parent record.
     * 
     * @param string $parent
     * @param string $child
     */
    public function removeChild($parent, $child)
    {
        $this->authManager->removeChild((object) ['name' => $parent], (object) ['name' => $child]);
    }

    public function removeFilter($name)
    {
        $this->authManager->removeFilter($name);
    }

    public function removeFilterFromRole($role, $filters)
    {
        $this->authManager->removeFilterFromRole($role, $filters);
    }

    public function removeModel($name)
    {
        $this->authManager->removeModel($name);
    }

    public function removeModelFromFilter($filter, $models)
    {
        $this->authManager->removeModelFromFilter($filter, $models);
    }

    /**
     * Deletes a permission.
     * 
     * @param string $name
     */
    public function removePermission($name)
    {
        $this->authManager->remove(new Permission(['name' => $name]));
    }

    public function removePermissionFromGroup($name, $group)
    {
        $this->authManager->removePermissionFromGroup($name, $group);
    }

    public function removePermissions($settings)
    {
        foreach ($settings as $groupName => $permissions) {
            foreach ($permissions as $permissionName => $permission) {

                if (isset($permission['routes'])) {
                    foreach ($permission['routes'] as $route) {
                        if (!isset($route['bundle']) || !isset($route['controller'])) {
                            throw new Exception('Bundle and controller are required for the route.');
                        }
                        $this->removeRoute($route['bundle'], $route['controller'], isset($route['action']) ? $route['action'] : null);
                    }
                }

                $this->removePermission($permissionName);
            }
        }
    }

    /**
     * Deletes permissions group.
     * 
     * @param string $name
     */
    public function removePermissionsGroup($name)
    {
        $this->authManager->removePermissionsGroup($name);
    }

    /**
     * Deletes a role.
     * 
     * @param string $name
     */
    public function removeRole($name)
    {
        $this->authManager->remove(new Role(['name' => $name]));
    }

    public function removeRoute($bundle, $controller, $action = null)
    {
        $this->authManager->removeRoute($bundle, $controller, $action);
    }

    public function removeRule($className)
    {
        $this->authManager->remove(new $className);
    }

    public function removeRuleFromPermission($permission)
    {
        $this->authManager->removeRuleFromPermission($permission);
    }

    private function validatePermissionGroupNames($groups)
    {
        $current = AuthGroup::find()->select('name')->column();
        $diff = array_diff($groups, $current);

        if (!empty($diff)) {
            $names = '"' . implode('", "', $diff) . '"';
            $message = (count($diff) == 1) ? 'Permission group with name ' . $names . ' does not exist.' : 'Permission groups with name ' . $names . ' do not exist.';
            throw new Exception($message);
        }

        return true;
    }

    private function validatePermissionNames($permissions)
    {
        $current = AuthPermission::find()->select('name')->column();
        $intersect = array_intersect($current, $permissions);

        if (!empty($intersect)) {
            $names = '"' . implode('", "', $intersect) . '"';
            $message = (count($intersect) == 1) ? 'Permission with name ' . $names . ' already exists.' : 'Permissions with name ' . $names . ' already exist.';
            throw new Exception($message);
        }

        return true;
    }

}
