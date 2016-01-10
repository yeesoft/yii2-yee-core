<?php

namespace yeesoft\widgets;

use yeesoft\helpers\LanguageHelper;
use Yii;

/**
 * Multilingual ActiveField
 *
 */
class MultilingualField extends \yii\widgets\ActiveField
{
    public $language = NULL;

    public $multilingual = FALSE;

    public function init()
    {
        parent::init();

        $languages = LanguageHelper::getLanguages();
        $isCurrentLanguage = (Yii::$app->language == $this->language);

        if ($this->language !== NULL && (LanguageHelper::isMultilingual($this->model) || $this->multilingual)) {
            $languageLabel = $languages[$this->language];
            $inputLabel = $this->model->getAttributeLabel($this->attribute) . ((count($languages) > 1) ? " [$languageLabel]" : '');

            $this->labelOptions = array_merge($this->labelOptions, [
                'label' => $inputLabel
            ]);

            $this->options = array_merge($this->options, [
                'data-toggle' => 'multilang',
                'data-lang' => $this->language,
                'class' => ($isCurrentLanguage ? 'in' : ''),
            ]);

            $langPart = strtolower(str_replace('-', '_', $this->language));
            $this->attribute .= ($isCurrentLanguage) ? '' : '_' . $langPart;
        }
    }

}