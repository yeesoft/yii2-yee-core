<?php

use yeesoft\db\PermissionsMigration;

class m150821_140142_add_core_permissions extends PermissionsMigration
{

    public function getPermissions()
    {
        return [
            'page-management' => [
                'view-pages' => [
                    'title' => 'View Pages',
                    'roles' => [self::ROLE_AUTHOR],
                    'routes' => [
                        ['bundle' => self::ADMIN_BUNDLE, 'controller' => 'page/default', 'action' => 'index'],
                    ],
                ],
                'edit-pages' => [
                    'title' => 'Edit Pages',
                    'child' => ['view-pages'],
                    'roles' => [self::ROLE_AUTHOR],
                    'rule' => yeesoft\rbac\AuthorRule::class,
                    'routes' => [
                        ['bundle' => self::ADMIN_BUNDLE, 'controller' => 'page/default', 'action' => 'update'],
                    ],
                ],
            ],
        ];
    }

    public function safeUp()
    {
        $this->addRole(self::ROLE_USER, 'User');

        $this->addRole(self::ROLE_AUTHOR, 'Author');
        $this->addChildRole(self::ROLE_AUTHOR, self::ROLE_USER);

        $this->addRole(self::ROLE_MODERATOR, 'Moderator');
        $this->addChildRole(self::ROLE_MODERATOR, self::ROLE_USER);
        $this->addChildRole(self::ROLE_MODERATOR, self::ROLE_AUTHOR);

        $this->addRole(self::ROLE_ADMIN, 'Administrator');
        $this->addChildRole(self::ROLE_ADMIN, self::ROLE_USER);
        $this->addChildRole(self::ROLE_ADMIN, self::ROLE_AUTHOR);
        $this->addChildRole(self::ROLE_ADMIN, self::ROLE_MODERATOR);

        $this->addPermissionsGroup('common-permissions', 'Common Permissions');
        $this->addPermissionsGroup('page-management', 'Page Management');
        $this->addPermissionsGroup('dashboard', 'Dashboard');

        $this->addRule(yeesoft\rbac\AuthorRule::class);

        $this->addModel('page', 'Page', yeesoft\page\models\Page::class);
        $this->addModel('post', 'Post', yeesoft\post\models\Post::class);

        $this->addFilter('author-filter', 'Author Filter', yeesoft\filters\AuthorFilter::class);

        $this->addModelToFilter('author-filter', ['page', 'post']);

        $this->addFilterToRole(self::ROLE_USER, 'author-filter');
        $this->addFilterToRole(self::ROLE_AUTHOR, 'author-filter');

        $this->createPermissions($this->getPermissions());
    }

    public function safeDown()
    {
        $this->removePermissions($this->getPermissions());

        $this->removeFilter('author-filter');

        $this->removeModel('post');
        $this->removeModel('page');

        $this->removeRule(yeesoft\rbac\AuthorRule::class);

        $this->removePermissionsGroup('common-permissions');
        $this->removePermissionsGroup('page-management');
        $this->removePermissionsGroup('dashboard');

        $this->removeRole(self::ROLE_ADMIN);
        $this->removeRole(self::ROLE_MODERATOR);
        $this->removeRole(self::ROLE_AUTHOR);
        $this->removeRole(self::ROLE_USER);
    }

}
