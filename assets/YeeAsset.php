<?php

namespace yeesoft\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class YeeAsset
 * @package yeesoft\base
 */
class YeeAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__ . '/source';

        $this->js = [
            'js/sb-admin-2.js?v=1.0.0',
            'js/metisMenu.js?v=1.1.3',
            'js/jquery.formstyler.min.js?v=1.6.4',
            'js/jquery.formstyler.init.js?v=1.0'
        ];

        $this->css = [
            'css/yee-admin.css?v=1.0.0',
            'css/yee-widget.css?v=1.0.0',
            'css/bootstrap-theme.css?v=1.0.0',
            'css/sb-admin-2.css?v=1.0.0',
            'css/jquery.formstyler.css?v=1.6.4',
        ];

        $this->depends = [
            JqueryAsset::className(),
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        parent::init();
    }
}