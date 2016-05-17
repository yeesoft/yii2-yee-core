<?php

namespace yeesoft\widgets;

use Yii;

/**
 * Multilingual ActiveForm
 */
class ActiveForm extends \yii\bootstrap\ActiveForm
{
    public $fieldClass = 'yeesoft\widgets\ActiveField';

    public function field($model, $attribute, $options = [])
    {
        $fields = [];

        $isMultilingualOption = (isset($options['multilingual']) && $options['multilingual']);
        $isMultilingualAttribute = (method_exists($model, 'isMultilingual') && $model->isMultilingual() && $model->hasLangAttribute($attribute));

        if ($isMultilingualOption || $isMultilingualAttribute) {
            $languages = array_keys(Yii::$app->yee->languages);

            foreach ($languages as $language) {
                $fields[] = parent::field($model, $attribute, array_merge($options, ['language' => $language]));
            }

        } else {
            return parent::field($model, $attribute, $options);
        }

        return new MultilingualFieldContainer(['fields' => $fields]);
    }
}