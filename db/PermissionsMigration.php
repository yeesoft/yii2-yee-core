<?php

namespace yeesoft\db;

/**
 * This class helps to create migrations for modules permissions. 
 */
abstract class PermissionsMigration extends \yii\db\Migration
{

    const AUTH_ITEM_TABLE = '{{%auth_item}}';
    const AUTH_ITEM_GROUP_TABLE = '{{%auth_item_group}}';
    const AUTH_ITEM_CHILD_TABLE = '{{%auth_item_child}}';
    const ROLE_AUTHOR = 'author';
    const ROLE_ADMIN = 'administrator';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_USER = 'user';

    public function safeUp()
    {
        $this->beforeUp();

        $params = $this->getPermissions();

        //Insert new items
        foreach ($params as $group => $permissions) {
            
            //Insert general links
            if (isset($permissions['links'])) {
                foreach ($permissions['links'] as $link) {
                    $this->insert(self::AUTH_ITEM_TABLE, ['name' => $link, 'type' => '3', 'created_at' => time(), 'updated_at' => time()]);
                }
                unset($permissions['links']);
            }

            foreach ($permissions as $code => $permission) {
                $title = (isset($permission['title'])) ? $permission['title'] : '';
                $this->insert(self::AUTH_ITEM_TABLE, ['name' => $code, 'group_code' => $group, 'description' => $title, 'type' => '2', 'created_at' => time(), 'updated_at' => time()]);

                if (isset($permission['links'])) {
                    foreach ($permission['links'] as $link) {
                        $this->insert(self::AUTH_ITEM_TABLE, ['name' => $link, 'type' => '3', 'created_at' => time(), 'updated_at' => time()]);
                    }
                }
            }
        }

        //Link created items
        foreach ($params as $group => $permissions) {
            foreach ($permissions as $code => $permission) {

                if (isset($permission['links'])) {
                    foreach ($permission['links'] as $link) {
                        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => $code, 'child' => $link]);
                    }
                }

                if (isset($permission['roles'])) {
                    foreach ($permission['roles'] as $role) {
                        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => $role, 'child' => $code]);
                    }
                }

                if (isset($permission['childs'])) {
                    foreach ($permission['childs'] as $child) {
                        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => $code, 'child' => $child]);
                    }
                }
            }
        }

        $this->afterUp();
    }

    public function safeDown()
    {
        $this->beforeDown();

        $params = $this->getPermissions();

        //Delete Link created items
        foreach ($params as $group => $permissions) {
            
            //Delete general links
            if (isset($permissions['links'])) {
                foreach ($permissions['links'] as $link) {
                    $this->delete(self::AUTH_ITEM_TABLE, ['name' => $link, 'type' => '3']);
                }
                unset($permissions['links']);
            }

            foreach ($permissions as $code => $permission) {

                if (isset($permission['links'])) {
                    foreach ($permission['links'] as $link) {
                        $this->delete(self::AUTH_ITEM_CHILD_TABLE, ['parent' => $code, 'child' => $link]);
                    }
                }

                if (isset($permission['roles'])) {
                    foreach ($permission['roles'] as $role) {
                        $this->delete(self::AUTH_ITEM_CHILD_TABLE, ['parent' => $role, 'child' => $code]);
                    }
                }

                if (isset($permission['childs'])) {
                    foreach ($permission['childs'] as $child) {
                        $this->delete(self::AUTH_ITEM_CHILD_TABLE, ['parent' => $code, 'child' => $child]);
                    }
                }
            }
        }

        //Delete created items
        foreach ($params as $group => $permissions) {
            foreach ($permissions as $code => $permission) {
                $this->delete(self::AUTH_ITEM_TABLE, ['name' => $code, 'group_code' => $group]);

                if (isset($permission['links'])) {
                    foreach ($permission['links'] as $link) {
                        $this->delete(self::AUTH_ITEM_TABLE, ['name' => $link, 'type' => '3']);
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
        $this->insert(self::AUTH_ITEM_GROUP_TABLE, ['code' => $code, 'name' => $name, 'created_at' => time(), 'updated_at' => time()]);
    }

    /**
     * Deletes permissions group.
     * 
     * @param string $code
     */
    public function deletePermissionsGroup($code)
    {
        $this->delete(self::AUTH_ITEM_GROUP_TABLE, ['code' => $code]);
    }

    /**
     * Creates new role.
     * 
     * @param string $name
     * @param string $description
     */
    public function addRole($name, $description)
    {
        $this->insert(self::AUTH_ITEM_TABLE, ['name' => $name, 'type' => '1', 'description' => $description, 'created_at' => time(), 'updated_at' => time()]);
    }

    /**
     * Deletes role.
     * 
     * @param string $name
     */
    public function deleteRole($name)
    {
        $this->delete(self::AUTH_ITEM_TABLE, ['name' => $name, 'type' => '1']);
    }

}
