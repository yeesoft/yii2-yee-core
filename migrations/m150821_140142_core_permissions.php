<?php

use yeesoft\db\PermissionsMigration;

class m150821_140142_core_permissions extends PermissionsMigration
{

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

        $this->addFilter('author-filter', 'Author Filter', yeesoft\filters\AuthorFilter::class);

        $this->addFilterToRole(self::ROLE_USER, 'author-filter');
        $this->addFilterToRole(self::ROLE_AUTHOR, 'author-filter');
    }

    public function safeDown()
    {
        $this->removeFilter('author-filter');

        $this->removePermissionsGroup('common-permissions');

        $this->removeRole(self::ROLE_ADMIN);
        $this->removeRole(self::ROLE_MODERATOR);
        $this->removeRole(self::ROLE_AUTHOR);
        $this->removeRole(self::ROLE_USER);
    }

}
