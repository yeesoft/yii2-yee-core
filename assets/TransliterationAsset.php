<?php

namespace yeesoft\assets;

use yii\web\AssetBundle;

class TransliterationAsset extends AssetBundle
{

    public $sourcePath = '@bower/transliteration/lib/browser';
    public $js = [
        'transliteration.min.js',
    ];

}
