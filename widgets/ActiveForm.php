<?php

namespace yeesoft\widgets;

use yeesoft\helpers\LanguageHelper;
use yii\widgets\ActiveForm as DefaultActiveForm;

/**
 * Multilingual ActiveForm
 */
class ActiveForm extends DefaultActiveForm
{
    public $fieldClass = 'yeesoft\widgets\MultilingualField';

    public function field($model, $attribute, $options = [])
    {
        $fields = [];

        $isMultilingualOption = (isset($options['multilingual']) && $options['multilingual']);
        $isMultilingualAttribute = (LanguageHelper::isMultilingual($model) && $model->hasLangAttribute($attribute));

        if ($isMultilingualOption || $isMultilingualAttribute) {
            $languages = array_keys(LanguageHelper::getLanguages());

            foreach ($languages as $language) {
                $fields[] = parent::field($model, $attribute, array_merge($options, ['language' => $language]));
            }

        } else {
            return parent::field($model, $attribute, $options);
        }

        return new MultilingualFieldContainer(['fields' => $fields]);
    }
}