<?php

namespace yeesoft\helpers;

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

}
