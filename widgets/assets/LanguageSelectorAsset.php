<?php

namespace yeesoft\widgets\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class LanguageSelectorAsset
 * 
 * @package yeesoft\widgets\assets
 */
class LanguageSelectorAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__ . '/source/language-selector';

        $this->js = [
            //'js/language.js',
        ];

        $this->css = [
            'css/language-selector.css',
        ];

        $this->depends = [
            JqueryAsset::className(),
        ];

        parent::init();
    }
}