<?php

use yii\db\Migration;
use yii\db\Schema;

class m150821_140141_add_core_permissions extends Migration
{

    public function up()
    {

        $this->insert('auth_item_group', ['code' => 'dashboard', 'name' => 'Dashboard', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item_group', ['code' => 'userCommonPermissions', 'name' => 'Common Permissions', 'created_at' => '1440180000', 'updated_at' => '1440180000']);

        $this->insert('auth_item', ['name' => 'administrator', 'type' => '1', 'description' => 'Administrator', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item', ['name' => 'moderator', 'type' => '1', 'description' => 'Moderator', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item', ['name' => 'author', 'type' => '1', 'description' => 'Author', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item', ['name' => 'user', 'type' => '1', 'description' => 'User', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item', ['name' => '/admin/', 'type' => '3', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item', ['name' => '/admin/site/*', 'type' => '3', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item', ['name' => '/admin/site/index', 'type' => '3', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item', ['name' => 'commonPermission', 'type' => '2', 'description' => 'Common permission', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item', ['name' => 'changeOwnPassword', 'type' => '2', 'description' => 'Change own password', 'group_code' => 'userCommonPermissions', 'created_at' => '1440180000', 'updated_at' => '1440180000']);
        $this->insert('auth_item', ['name' => 'viewDashboard', 'type' => '2', 'description' => 'View Dashboard', 'group_code' => 'dashboard', 'created_at' => '1440180000', 'updated_at' => '1440180000']);

        $this->insert('auth_item_child', ['parent' => 'viewDashboard', 'child' => '/admin/']);
        $this->insert('auth_item_child', ['parent' => 'viewDashboard', 'child' => '/admin/site/index']);
        $this->insert('auth_item_child', ['parent' => 'changeOwnPassword', 'child' => '/auth/change-own-password']);
        $this->insert('auth_item_child', ['parent' => 'administrator', 'child' => 'author']);
        $this->insert('auth_item_child', ['parent' => 'moderator', 'child' => 'author']);
        $this->insert('auth_item_child', ['parent' => 'administrator', 'child' => 'moderator']);
        $this->insert('auth_item_child', ['parent' => 'administrator', 'child' => 'user']);
        $this->insert('auth_item_child', ['parent' => 'author', 'child' => 'user']);
        $this->insert('auth_item_child', ['parent' => 'moderator', 'child' => 'user']);
        $this->insert('auth_item_child', ['parent' => 'administrator', 'child' => 'changeOwnPassword']);
        $this->insert('auth_item_child', ['parent' => 'author', 'child' => 'changeOwnPassword']);
        $this->insert('auth_item_child', ['parent' => 'administrator', 'child' => 'changeUserPassword']);
        $this->insert('auth_item_child', ['parent' => 'author', 'child' => 'viewDashboard']);
        $this->insert('auth_item_child', ['parent' => 'moderator', 'child' => 'viewDashboard']);
    }

    public function down()
    {

        $this->delete('auth_item_child', ['parent' => 'viewDashboard', 'child' => '/admin/']);
        $this->delete('auth_item_child', ['parent' => 'viewDashboard', 'child' => '/admin/site/index']);
        $this->delete('auth_item_child', ['parent' => 'changeOwnPassword', 'child' => '/auth/change-own-password']);
        $this->delete('auth_item_child', ['parent' => 'administrator', 'child' => 'author']);
        $this->delete('auth_item_child', ['parent' => 'moderator', 'child' => 'author']);
        $this->delete('auth_item_child', ['parent' => 'administrator', 'child' => 'moderator']);
        $this->delete('auth_item_child', ['parent' => 'administrator', 'child' => 'user']);
        $this->delete('auth_item_child', ['parent' => 'author', 'child' => 'user']);
        $this->delete('auth_item_child', ['parent' => 'moderator', 'child' => 'user']);
        $this->delete('auth_item_child', ['parent' => 'administrator', 'child' => 'changeOwnPassword']);
        $this->delete('auth_item_child', ['parent' => 'author', 'child' => 'changeOwnPassword']);
        $this->delete('auth_item_child', ['parent' => 'administrator', 'child' => 'changeUserPassword']);
        $this->delete('auth_item_child', ['parent' => 'author', 'child' => 'viewDashboard']);
        $this->delete('auth_item_child', ['parent' => 'moderator', 'child' => 'viewDashboard']);

        $this->delete('auth_item', ['name' => '/admin/']);
        $this->delete('auth_item', ['name' => '/admin/site/*']);
        $this->delete('auth_item', ['name' => '/admin/site/index']);
        $this->delete('auth_item', ['name' => 'administrator']);
        $this->delete('auth_item', ['name' => 'moderator']);
        $this->delete('auth_item', ['name' => 'author']);
        $this->delete('auth_item', ['name' => 'user']);
        $this->delete('auth_item', ['name' => 'commonPermission']);
        $this->delete('auth_item', ['name' => 'changeOwnPassword']);
        $this->delete('auth_item', ['name' => 'viewDashboard']);

        $this->delete('auth_item_group', ['code' => 'dashboard']);
        $this->delete('auth_item_group', ['code' => 'userCommonPermissions']);

    }
}