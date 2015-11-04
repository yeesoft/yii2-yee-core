<?php

namespace yeesoft\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class YeeAsset
 * @package yeesoft\core
 */
class LanguagePillsAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__ . '/language';

        $this->js = [
            'js/language.js',
        ];

        $this->css = [
            'css/language.css',
        ];

        $this->depends = [
            JqueryAsset::className(),
        ];

        parent::init();
    }
}