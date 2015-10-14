<?php

namespace yeesoft\widgets;

use yeesoft\helpers\LanguageHelper;
use Yii;

class LanguageSelector extends \yii\base\Widget
{

    public function run()
    {
        $language = Yii::$app->language;
        $languages = LanguageHelper::getLanguages();

        return $this->render('language-selector', [
            'language' => $language,
            'languages' => $languages
        ]);
    }
}