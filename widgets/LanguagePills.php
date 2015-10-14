<?php

namespace yeesoft\widgets;

use yeesoft\helpers\LanguageHelper;
use Yii;


/**
 * Widget to display buttons to switch languages in update pages
 */
class LanguagePills extends \yii\base\Widget
{

    public function run()
    {
        $content = '';
        $languages = LanguageHelper::getLanguages();
        $defaultLanguage = Yii::$app->language;

        if (count($languages) > 1) {
            $content = '<ul class="nav nav-pills language-pills pull-right">';

            foreach ($languages as $key => $language) {
                $class = (($key === $defaultLanguage) ? 'class="active"' : '');
                $content .= '<li ' . $class . '><a data-toggle="pill" href="#' . $key . '">' . $language . '</a></li>';
            }
            $content .= '</ul>';
        }

        return $content;
    }
}