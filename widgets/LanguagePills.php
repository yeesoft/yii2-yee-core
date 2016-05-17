<?php

namespace yeesoft\widgets;

use yeesoft\assets\LanguagePillsAsset;
use Yii;

/**
 * Widget to display buttons to switch languages in update pages
 */
class LanguagePills extends \yii\base\Widget
{

    public function run()
    {
        LanguagePillsAsset::register($this->view);

        $content = '';
        $languages = Yii::$app->yee->languages;
        $defaultLanguage = Yii::$app->language;

        if (count($languages) > 1) {
            $content = '<ul class="nav nav-pills language-pills pull-right">';

            foreach ($languages as $key => $language) {
                $class = (($key === $defaultLanguage) ? 'class="active"' : '');
                $content .= '<li ' . $class . '><a data-lang="' . $key . '" data-toggle="pill" href="#' . $key . '">' . $language . '</a></li>';
            }
            $content .= '</ul>';
        }

        return $content;
    }
}