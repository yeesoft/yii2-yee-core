<?php

namespace yeesoft\db;

use Yii;
use yii\db\Query;
use yeesoft\rbac\DbManager;

/**
 * This class helps to create migrations for modules permissions. 
 */
abstract class PermissionsMigration extends \yii\db\Migration
{

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

    public function safeUp()
    {
        $this->beforeUp();

        $authManager = $this->getAuthManager();
        $params = $this->getPermissions();

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
                $this->insert($authManager->itemTable, ['name' => $code, 'group_code' => $group, 'description' => $title, 'type' => '2', 'created_at' => time(), 'updated_at' => time()]);

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

    public function safeDown()
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
                $this->delete($authManager->itemTable, ['name' => $code, 'group_code' => $group]);

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
    abstract public function getPermissions();

    /**
     * Executes before up method.
     * 
     * @return mixed
     */
    public function beforeUp()
    {
        return;
    }

    /**
     * Executes after up method.
     * 
     * @return mixed
     */
    public function afterUp()
    {
        return;
    }

    /**
     * Executes before down method.
     * 
     * @return mixed
     */
    public function beforeDown()
    {
        return;
    }

    /**
     * Executes after down method.
     * 
     * @return mixed
     */
    public function afterDown()
    {
        return;
    }

    /**
     * Creates new permissions group.
     * 
     * @param string $code
     * @param string $name
     */
    public function addPermissionsGroup($code, $name)
    {
        $this->insert($this->getAuthManager()->itemGroupTable, ['code' => $code, 'name' => $name, 'created_at' => time(), 'updated_at' => time()]);
    }

    /**
     * Deletes permissions group.
     * 
     * @param string $code
     */
    public function removePermissionsGroup($code)
    {
        $this->delete($this->getAuthManager()->itemGroupTable, ['code' => $code]);
    }

    /**
     * Creates new role.
     * 
     * @param string $name
     * @param string $description
     */
    public function addRole($name, $description)
    {
        $this->insert($this->getAuthManager()->itemTable, ['name' => $name, 'type' => '1', 'description' => $description, 'created_at' => time(), 'updated_at' => time()]);
    }

    /**
     * Deletes role.
     * 
     * @param string $name
     */
    public function removeRole($name)
    {
        $this->delete($this->getAuthManager()->itemTable, ['name' => $name, 'type' => '1']);
    }

    /**
     * Link child auth item to parent.
     * 
     * @param string $parent
     * @param string|array $child
     */
    public function addChild($parent, $children)
    {
        if (!is_array($children)) {
            $children = [$children];
        }

        foreach ($children as $child) {
            $this->insert($this->getAuthManager()->itemChildTable, ['parent' => $parent, 'child' => $child]);
        }
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
        if(!is_array($roles)){
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
