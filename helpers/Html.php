<?php

namespace yeesoft\helpers;

use Yii;
use yeesoft\helpers\Url;

/**
 * @inheritdoc
 */
class Html extends \yii\helpers\Html {

    protected $_rules;

    /**
     * Hide the link if user has no access to it.
     *
     * @inheritdoc
     */
    public static function a($text, $url = null, $options = []) {
        if (Yii::$app->user->isSuperadmin) {
            return parent::a($text, $url, $options);
        }

        if (is_array($url)) {
            $user = Yii::$app->user;
            $request = Yii::$app->request;
            $rules = Yii::$app->authManager->getRouteRules();
            $action = static::getActionByRoute($url);

            /* @var $rule AccessRule */
            foreach ($rules as $rule) {
                if ($allow = $rule->allows($action, $user, $request)) {
                    return parent::a($text, $url, $options);
                }
            }

            return '';
        }

        return parent::a($text, $url, $options);
    }

    /**
     * Return abstract Action from $route for checking access.
     * 
     * @param array $route
     * @return \stdClass
     * @throws \yii\base\InvalidParamException
     */
    protected static function getActionByRoute($route) {
        if (!is_array($route) || !isset($route[0])) {
            throw new \yii\base\InvalidParamException();
        }

        $parts = explode('/', Url::normalizeRoute($route[0]));

        $action = new \stdClass();
        $action->id = array_pop($parts);
        $action->controller = new \stdClass();
        $action->controller->uniqueId = implode('/', $parts);

        return $action;
    }

}
