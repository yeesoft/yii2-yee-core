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

        if (LanguageHelper::isMultilingual($model) && $model->hasLangAttribute($attribute)) {
            $languages = array_keys(LanguageHelper::getModelLanguages($model));

            foreach ($languages as $language) {
                $fields[] = parent::field($model, $attribute, array_merge($options, ['language' => $language]));
            }
        } else {
            return parent::field($model, $attribute, $options);
        }

        return new MultilingualFieldContainer(['fields' => $fields]);
    }
}