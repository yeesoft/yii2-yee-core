<?php

namespace yeesoft\widgets\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EditableTextInputAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public $js = [
        'js/editable.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'css/editable.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        JqueryAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/source/editable-text-input';

        parent::init();
    }

}
