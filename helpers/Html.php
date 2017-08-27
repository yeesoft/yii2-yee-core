<?php

namespace yeesoft\helpers;

use Yii;
use yeesoft\helpers\Url;

/**
 * @inheritdoc
 */
class Html extends \yii\helpers\Html
{

    protected $_rules;

    /**
     * Hide the link if user has no access to it.
     *
     * @inheritdoc
     */
    public static function a($text, $url = null, $options = [])
    {
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
     *
     * @inheritdoc
     */
    public static function checkbox($name, $checked = false, $options = [])
    {
        $options['checked'] = (bool) $checked;
        $value = array_key_exists('value', $options) ? $options['value'] : '1';
        if (isset($options['uncheck'])) {
            // add a hidden field so that if the checkbox is not selected, it still submits a value
            $hidden = static::hiddenInput($name, $options['uncheck']);
            unset($options['uncheck']);
        } else {
            $hidden = '';
        }

        $label = (isset($options['label'])) ? $options['label'] : ' ';
        $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
        unset($options['label'], $options['labelOptions']);
        $content = static::input('checkbox', $name, $value, $options) . static::label($label, null, $labelOptions);
        return '<div class="checkbox">' . $hidden . $content . '</div>';
    }

    /**
     * Return abstract Action from $route for checking access.
     * 
     * @param array $route
     * @return \stdClass
     * @throws \yii\base\InvalidParamException
     */
    protected static function getActionByRoute($route)
    {
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
