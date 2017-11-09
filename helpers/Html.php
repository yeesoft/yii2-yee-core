<?php

namespace yeesoft\helpers;

use Yii;

/**
 * @inheritdoc
 */
class Html extends \yii\helpers\Html
{

    /**
     * Hide the link if user has no access to it.
     *
     * @inheritdoc
     */
    public static function a($text, $url = null, $options = [])
    {

        return (Yii::$app->user->canRoute($url)) ? parent::a($text, $url, $options) : '';
    }

}
