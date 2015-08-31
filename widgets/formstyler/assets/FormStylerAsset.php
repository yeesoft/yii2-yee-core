<?php

namespace yeesoft\widgets\formstyler\assets;

use yii\web\AssetBundle;

/**
 * Class FormStylerAsset
 * @package yeesoft\assets
 */
class FormStylerAsset extends AssetBundle
{
    public $js      = ['js/jquery.formstyler.min.js'];
    public $css     = ['css/jquery.formstyler.css'];
    public $depends = ['yii\web\JqueryAsset'];

    public function init()
    {
        $this->sourcePath = __DIR__.'/source';
    }
}