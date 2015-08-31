<?php

namespace yeesoft\assets;

use yii\web\AssetBundle;

/**
 * Class FormStylerAsset
 * @package yeesoft\assets
 */
class FormStylerAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-form-styler';
    public $js = ['jquery.formstyler.min.js'];
    public $css = ['jquery.formstyler.css'];
    public $depends = ['yii\web\JqueryAsset'];

}