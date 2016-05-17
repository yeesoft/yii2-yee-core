<?php

namespace yeesoft\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class YeeAsset
 * 
 * @package yeesoft\core
 */
class YeeAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__ . '/admin';

        $this->js = [
            'js/admin.js',
        ];

        $this->css = [
            'css/admin.css',
            'css/widget.css',
            'css/styler.css',
        ];

        $this->depends = [
            JqueryAsset::className(),
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
            'rmrevin\yii\fontawesome\AssetBundle',
        ];

        parent::init();
    }
}