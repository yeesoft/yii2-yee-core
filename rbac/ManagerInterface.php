<?php

namespace yeesoft\rbac;

interface ManagerInterface extends \yii\rbac\ManagerInterface
{

    const CACHE_AUTH_ROUTES = '__auth_routes';
    const CACHE_AUTH_FILTERS = '__auth_filters';
    const CACHE_AUTH_USER_FILTERS = '__auth_user_filters';

    /**
     * Returns list of routes for current application tier (front end, back end).
     * @return \yeesoft\models\AuthRoute[] list of routes for current application tier.
     */
    public function getRoutes();

    /**
     * Generate access filter rules using route permissions.
     * @param array $ruleConfig default rule configuration
     * @return array list of routes for current application tier.
     */
    public function getRouteRules($ruleConfig = null);

    /**
     * Returns the active filters that are assigned to the role.
     * @param string $modelClass model class name.
     * @param string $role role name.
     * @return \yeesoft\filters\ActiveFilter[] all active filters assigned to the role.
     */
    public function getFiltersByRole($modelClass, $role);

    /**
     * Returns the active filters that are assigned to the user.
     * @param string $modelClass model class name.
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return \yeesoft\filters\ActiveFilter[] all active filters assigned to the user.
     */
    public function getFiltersByUser($modelClass, $userId);

    /**
     * Flush route cache.
     */
    public function flushRouteCache();
}
