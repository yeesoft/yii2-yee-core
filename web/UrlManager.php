<?php

namespace yeesoft\web;

use Yii;

class UrlManager extends \yeesoft\multilingual\web\MultilingualUrlManager
{

    public $showScriptName = false;
    public $enablePrettyUrl = true;

    public function init()
    {
        $this->languages = Yii::$app->languages;
        $this->languageRedirects = Yii::$app->languageRedirects;

        parent::init();
    }

}
