<?php

namespace yeesoft\widgets\assets;

use yii\web\AssetBundle;
use yeesoft\assets\TransliterationAsset;

class SlugableTextInputAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public $js = [
        'js/slugable.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'css/slugable.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        EditableTextInputAsset::class,
        TransliterationAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/source/slugable-text-input';

        parent::init();
    }

}
