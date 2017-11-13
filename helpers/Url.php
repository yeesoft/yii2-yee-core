<?php

namespace yeesoft\helpers;

use Yii;

class Url extends \yii\helpers\BaseUrl
{

    /**
     * Return object that simulates \yii\base\Action instance for $route.
     * 
     * @param array $route
     * @return object
     * @throws \yii\base\InvalidParamException
     */
    public static function createAction($route)
    {
        if (!is_array($route) || !isset($route[0])) {
            throw new \yii\base\InvalidParamException();
        }

        $path = explode('/', static::normalizeRoute($route[0]));

        $actionId = array_pop($path);
        $uniqueId = implode('/', $path);

        return (object) [
                    'id' => $actionId,
                    'controller' => (object) [
                        'uniqueId' => $uniqueId
                    ]
        ];
    }
    
        /**
     * Check if controller has $freeAccess = true or $action in $freeAccessActions
     * Or it's login, logout, error page
     *
     * @param string $route
     * @param Action|null $action
     *
     * @return bool
     */
    public static function isFreeAccess($route, $action = null)
    {
        if ($action) {
            $controller = $action->controller;

            if ($controller->hasProperty('freeAccess') AND $controller->freeAccess === true) {
                return true;
            }

            if ($controller->hasProperty('freeAccessActions') AND in_array($action->id, $controller->freeAccessActions)) {
                return true;
            }
        }

        $systemPages = [
            '/auth/logout',
            //Yii::$app->errorHandler->errorAction,
            Yii::$app->user->loginUrl,
        ];

        if (in_array($route, $systemPages)) {
            return true;
        }

        return false;
    }

}
