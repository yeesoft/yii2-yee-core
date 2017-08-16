<?php

namespace yeesoft\helpers;

use Yii;
use yeesoft\models\User;

/**
 * @inheritdoc
 */
class Html extends \yii\helpers\Html
{

    /**
     * Hide link if user hasn't access to it
     *
     * @inheritdoc
     */
    public static function a($text, $url = null, $options = [])
    {
        return parent::a($text, $url, $options);
        
        $id = 'index';//parse unique ids
        $controller =  Yii::$app->controller->uniqueId;
        $action = new \yii\base\Action($id, $controller);
        $user = Yii::$app->user;
        $request = Yii::$app->getRequest();
        
        
        $rules = [];//Get Rules from DbManager
        $allow = false;
        /* @var $rule AccessRule */
        foreach ($rules as $rule) {
            if ($rule->allows($action, $user, $request)) {
                $allow =  true;
            } 
        }

        //\Yii::$app->user->can($permissionName);
        
        if (in_array($url, [null, '', '#'])) {
            return parent::a($text, $url, $options);
        }

        return User::canRoute($url) ? parent::a($text, $url, $options) : '';
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

}
