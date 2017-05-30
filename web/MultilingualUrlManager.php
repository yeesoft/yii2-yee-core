<?php

namespace yeesoft\web;

use Yii;

class MultilingualUrlManager extends \yeesoft\multilingual\web\MultilingualUrlManager
{

    public $showScriptName = false;
    public $enablePrettyUrl = true;

    public function init()
    {
        $this->languages = Yii::$app->yee->languages;
        $this->languageRedirects = Yii::$app->yee->languageRedirects;

        parent::init();
    }

}
