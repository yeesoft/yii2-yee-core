<?php

use yeesoft\db\PermissionsMigration;

class m150821_140141_add_core_permissions extends PermissionsMigration
{

    public function beforeUp()
    {
        $this->addPermissionsGroup('dashboard', 'Dashboard');
        $this->addPermissionsGroup('userCommonPermissions', 'Common Permissions');

        $this->addRole(self::ROLE_ADMIN, 'Administrator');
        $this->addRole(self::ROLE_MODERATOR, 'Moderator');
        $this->addRole(self::ROLE_AUTHOR, 'Author');
        $this->addRole(self::ROLE_USER, 'User');

        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'author', 'child' => 'user']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'moderator', 'child' => 'user']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'moderator', 'child' => 'author']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'administrator', 'child' => 'user']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'administrator', 'child' => 'author']);
        $this->insert(self::AUTH_ITEM_CHILD_TABLE, ['parent' => 'administrator', 'child' => 'moderator']);
    }

    public function afterDown()
    {
        $this->deletePermissionsGroup('dashboard');
        $this->deletePermissionsGroup('userCommonPermissions');

        $this->deleteRole(self::ROLE_ADMIN);
        $this->deleteRole(self::ROLE_MODERATOR);
        $this->deleteRole(self::ROLE_AUTHOR);
        $this->deleteRole(self::ROLE_USER);
    }

    public function getPermissions()
    {
        return [
            'dashboard' => [
                'links' => [
                    '/admin/*',
                    '/admin/default/*',
                ],
                'viewDashboard' => [
                    'title' => 'View Dashboard',
                    'roles' => [self::ROLE_AUTHOR],
                    'links' => [
                        '/admin',
                        '/admin/site/index',
                    ],
                ],
            ],
            'userCommonPermissions' => [
                'commonPermission' => [
                    'title' => 'Common Permission',
                    'roles' => [self::ROLE_USER],
                ],
                'changeOwnPassword' => [
                    'title' => 'Change Own Password',
                    'roles' => [self::ROLE_USER],
                ],
            ],
        ];
    }

}
