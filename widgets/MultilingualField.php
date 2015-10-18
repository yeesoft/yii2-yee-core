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

    public function init()
    {
        parent::init();

        $languages = Yii::$app->params['languages'];
        $isCurrentLanguage = (Yii::$app->language == $this->language);

        if ($this->language !== NULL && LanguageHelper::isMultilingual($this->model)) {
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

            $this->attribute .= ($isCurrentLanguage ? '' : '_' . $this->language);
        }
    }

}