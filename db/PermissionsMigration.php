<?php

namespace yeesoft\db;

use Yii;
use Exception;
use yii\db\Query;
use yii\rbac\Role as RoleItem;
use yii\rbac\Permission as PermissionItem;
use yeesoft\rbac\DbManager;
use yeesoft\models\AuthRole;
use yeesoft\models\AuthGroup;
use yeesoft\models\AuthRoute;
use yeesoft\models\AuthPermission;

/**
 * This class helps to create migrations for modules permissions. 
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

    public function createPermissions($params)
    {
        $authManager = $this->getAuthManager();

        //$this->validatePermissionGroupNames(array_keys($params));

        foreach ($params as $groupName => $permissions) {

            //$this->validatePermissionNames(array_keys($permissions));

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

                $this->addPermission($permissionName, $groupName, $permission['title'], isset($permission['rule']) ? $permission['rule'] : null);

                if (isset($permission['routes'])) {
                    foreach ($permission['routes'] as $route) {
                        $this->addRouteToPermission($permissionName, $route['bundle'], $route['controller'], isset($route['action']) ? $route['action'] : null);
                    }
                }

                if (isset($permission['rule'])) {
                    $this->setPermissionRule($permissionName, $permission['rule']);
                }

                if (isset($permission['child'])) {
                    $this->addChild($permissionName, $permission['child']);
                }
            }
        }



        die;



//            'group' => [
//                'permission' => [
//                    'title' => 'View Dashboard',
//                    'child' => ['childPermission'],
//                    'roles' => [self::ROLE_AUTHOR],
//                    'rules' => ['authorRule'],
//                    'routes' => [
//                        ['base' => 'admin', 'controller' => 'site/default', 'action' => 'index'],
//                     ],
//                ],
//            ],
        //Insert new items
        foreach ($params as $group => $permissions) {

            //Insert general links
            if (isset($permissions['links'])) {
                foreach ($permissions['links'] as $link) {
                    $this->insert($authManager->itemTable, ['name' => $link, 'type' => '3', 'created_at' => time(), 'updated_at' => time()]);
                }
                unset($permissions['links']);
            }

            foreach ($permissions as $code => $permission) {
                $title = (isset($permission['title'])) ? $permission['title'] : '';
                $this->insert($authManager->itemTable, ['name' => $code, 'group_name' => $group, 'description' => $title, 'type' => '2', 'created_at' => time(), 'updated_at' => time()]);

                if (isset($permission['links'])) {
                    foreach ($permission['links'] as $link) {
                        $this->insert($authManager->itemTable, ['name' => $link, 'type' => '3', 'created_at' => time(), 'updated_at' => time()]);
                    }
                }
            }
        }

        //Link created items
        foreach ($params as $group => $permissions) {
            foreach ($permissions as $code => $permission) {

                if (isset($permission['links'])) {
                    foreach ($permission['links'] as $link) {
                        $this->insert($authManager->itemChildTable, ['parent' => $code, 'child' => $link]);
                    }
                }

                if (isset($permission['roles'])) {
                    foreach ($permission['roles'] as $role) {
                        $this->insert($authManager->itemChildTable, ['parent' => $role, 'child' => $code]);
                    }
                }

                if (isset($permission['childs'])) {
                    foreach ($permission['childs'] as $child) {
                        $this->insert($authManager->itemChildTable, ['parent' => $code, 'child' => $child]);
                    }
                }
            }
        }

        $this->afterUp();
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

    public function removePermissions()
    {
        $this->beforeDown();

        $authManager = $this->getAuthManager();
        $params = $this->getPermissions();

        //Delete Link created items
        foreach ($params as $group => $permissions) {

            //Delete general links
            if (isset($permissions['links'])) {
                foreach ($permissions['links'] as $link) {
                    $this->delete($authManager->itemTable, ['name' => $link, 'type' => '3']);
                }
                unset($permissions['links']);
            }

            foreach ($permissions as $code => $permission) {

                if (isset($permission['links'])) {
                    foreach ($permission['links'] as $link) {
                        $this->delete($authManager->itemChildTable, ['parent' => $code, 'child' => $link]);
                    }
                }

                if (isset($permission['roles'])) {
                    foreach ($permission['roles'] as $role) {
                        $this->delete($authManager->itemChildTable, ['parent' => $role, 'child' => $code]);
                    }
                }

                if (isset($permission['childs'])) {
                    foreach ($permission['childs'] as $child) {
                        $this->delete($authManager->itemChildTable, ['parent' => $code, 'child' => $child]);
                    }
                }
            }
        }

        //Delete created items
        foreach ($params as $group => $permissions) {
            foreach ($permissions as $code => $permission) {
                $this->delete($authManager->itemTable, ['name' => $code, 'group_name' => $group]);

                if (isset($permission['links'])) {
                    foreach ($permission['links'] as $link) {
                        $this->delete($authManager->itemTable, ['name' => $link, 'type' => '3']);
                    }
                }
            }
        }

        $this->afterDown();
    }

    /**
     * Returns an array of permissions settings.
     * All items except permissions groups and roles will be created automatically.
     *
     * Example of settings that may be returned by this method:
     * 
     * ```
     * 'groupCode' => [
     *   'links' => [
     *     '/admin/media/*',
     *     '/admin/media/default/*',
     *   ],
     *   'permissionCode' => [
     *     'title' => 'Permission Title',
     *     'links' => [
     *       '/admin/module/*',
     *       '/admin/module/default/*',
     *       '/admin/module/default/index',
     *       '/admin/module/manage/delete'
     *     ],
     *     'roles' => [
     *       'administrator',
     *       'moderator',
     *     ],
     *     'childs' => [
     *       'child1PermissionCode',
     *       'child2PermissionCode',
     *     ],
     *   ],
     * ]
     * ```
     */
    //abstract public function getPermissions();

    /**
     * Executes before up method.
     * 
     * @return mixed
     */
//    public function beforeUp()
//    {
//        return;
//    }

    /**
     * Executes after up method.
     * 
     * @return mixed
     */
//    public function afterUp()
//    {
//        return;
//    }

    /**
     * Executes before down method.
     * 
     * @return mixed
     */
//    public function beforeDown()
//    {
//        return;
//    }

    /**
     * Executes after down method.
     * 
     * @return mixed
     */
//    public function afterDown()
//    {
//        return;
//    }

    /**
     * Creates new permissions group.
     * 
     * @param string $code
     * @param string $name
     */
    public function addPermissionsGroup($code, $name)
    {
        $this->insert($this->getAuthManager()->groupTable, ['code' => $code, 'name' => $name, 'created_at' => time(), 'updated_at' => time()]);
    }

    /**
     * Deletes permissions group.
     * 
     * @param string $code
     */
    public function removePermissionsGroup($code)
    {
        $this->delete($this->getAuthManager()->groupTable, ['code' => $code]);
    }

    /**
     * Creates new role.
     * 
     * @param string $name
     * @param string $description
     */
    public function addRole($name, $description)
    {
        $this->insert($this->getAuthManager()->itemTable, ['name' => $name, 'type' => AuthRole::ITEM_TYPE, 'description' => $description, 'created_at' => time(), 'updated_at' => time()]);
    }

    /**
     * Deletes role.
     * 
     * @param string $name
     */
    public function removeRole($name)
    {
        $this->delete($this->getAuthManager()->itemTable, ['name' => $name, 'type' => AuthRole::ITEM_TYPE]);
    }

    /**
     * Creates new permission.
     * 
     * @param string $name
     * @param string $description
     */
    public function addPermission($name, $group, $description, $rule = null)
    {
        $this->insert($this->getAuthManager()->itemTable, ['name' => $name, 'type' => AuthPermission::ITEM_TYPE, 'description' => $description, 'group_name' => $group, 'rule_name' => $rule, 'created_at' => time(), 'updated_at' => time()]);
    }

    /**
     * Deletes permission.
     * 
     * @param string $name
     */
    public function removePermission($name)
    {
        $this->delete($this->getAuthManager()->itemTable, ['name' => $name, 'type' => AuthPermission::ITEM_TYPE]);
    }

    public function setPermissionRule($permission, $rule)
    {
        $this->update($this->getAuthManager()->itemTable, ['rule_name' => $rule], ['name' => $permission, 'type' => Permission::ITEM_TYPE, 'updated_at' => time()]);
    }

    /**
     * Link child role item to parent.
     * 
     * @param string $parent
     * @param string $child
     */
    public function addChildRole($parent, $child)
    {
        $this->getAuthManager()->addChild(new RoleItem(['name' => $parent]), new RoleItem(['name' => $child]));
    }

    /**
     * Link child permission item to parent.
     * 
     * @param string $parent
     * @param string $child
     */
    public function addChildPermission($parent, $child)
    {
        $this->getAuthManager()->addChild(new PermissionItem(['name' => $parent]), new PermissionItem(['name' => $child]));
    }

    /**
     * Link permission to role.
     * 
     * @param string $role
     * @param string $permission
     */
    public function addPermissionToRole($role, $permission)
    {
        $this->getAuthManager()->addChild(new RoleItem(['name' => $role]), new PermissionItem(['name' => $permission]));
    }

    /**
     * Unlink child auth item from parent record.
     * 
     * @param string $parent
     * @param string $child
     */
    public function removeChild($parent, $child)
    {
        $this->delete($this->getAuthManager()->itemChildTable, ['parent' => $parent, 'child' => $child]);
    }

    public function addRule($name, $className)
    {
        $rule = new $className;
        $this->insert($this->getAuthManager()->ruleTable, ['name' => $name, 'class_name' => $className, 'data' => serialize($rule), 'created_at' => time(), 'updated_at' => time()]);
    }

    public function removeRule($name)
    {
        $this->delete($this->getAuthManager()->ruleTable, ['name' => $name]);
    }

    public function addModel($name, $className)
    {
        $this->insert($this->getAuthManager()->modelTable, ['name' => $name, 'class_name' => $className, 'created_at' => time(), 'updated_at' => time()]);
    }

    public function removeModel($name)
    {
        $this->delete($this->getAuthManager()->modelTable, ['name' => $name]);
    }

    public function addRoute($bundle, $controller, $action = null)
    {
        $this->insert($this->getAuthManager()->routeTable, ['bundle' => $bundle, 'controller' => $controller, 'action' => $action, 'created_at' => time(), 'updated_at' => time()]);
    }

    public function removeRoute($bundle, $controller, $action = null)
    {
        $this->delete($this->getAuthManager()->routeTable, ['bundle' => $bundle, 'controller' => $controller, 'action' => $action]);
    }

    public function addRouteToPermission($permission, $bundle, $controller, $action = null)
    {
        $route = (new Query())->select(['id'])
                ->from($this->getAuthManager()->routeTable)
                ->where(['bundle' => $bundle, 'controller' => $controller, 'action' => $action])
                ->one();

        if (!$route) {
            $path = implode('/', [$bundle, $controller, $action]);
            throw new Exception('Route "' . $path . '" does not exist.');
        }

        $this->insert($this->getAuthManager()->itemRouteTable, ['item_name' => $permission, 'route_id' => $route['id']]);
    }

    public function addFilter($name, $className)
    {
        $this->insert($this->getAuthManager()->filterTable, ['name' => $name, 'class_name' => $className, 'created_at' => time(), 'updated_at' => time()]);
    }

    public function removeFilter($name)
    {
        $this->delete($this->getAuthManager()->filterTable, ['name' => $name]);
    }

    public function addFilterToModel($filters, $models)
    {
        $filterIds = (new Query())->select(['id'])
                ->from($this->getAuthManager()->filterTable)
                ->where(['name' => (!is_array($filters)) ? [$filters] : $filters])
                ->column();

        $modelIds = (new Query())->select(['id'])
                ->from($this->getAuthManager()->modelTable)
                ->where(['name' => (!is_array($models)) ? [$models] : $models])
                ->column();

        foreach ($modelIds as $modelId) {
            foreach ($filterIds as $filterId) {
                $this->insert($this->getAuthManager()->modelFilterTable, ['model_id' => $modelId, 'filter_id' => $filterId]);
            }
        }
    }

    public function addFilterToRole($filters, $roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $filterIds = (new Query())->select(['id'])
                ->from($this->getAuthManager()->filterTable)
                ->where(['name' => (!is_array($filters)) ? [$filters] : $filters])
                ->column();

        foreach ($roles as $role) {
            foreach ($filterIds as $filterId) {
                $this->insert($this->getAuthManager()->modelFilterTable, ['item_name' => $role, 'filter_id' => $filterId]);
            }
        }
    }

}
