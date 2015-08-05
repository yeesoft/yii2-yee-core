<?php

namespace yeesoft\helpers;

use yeesoft\models\User;

/**
 * Class Html
 *
 * Show elements only to those, who can access to them
 *
 * @package yeesoft\core\helpers
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
        if (in_array($url, [null, '', '#'])) {
            return parent::a($text, $url, $options);
        }

        return User::canRoute($url) ? parent::a($text, $url, $options) : '';
    }
}