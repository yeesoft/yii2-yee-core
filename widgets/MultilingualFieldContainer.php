<?php

namespace yeesoft\widgets;

use yii\base\BaseObject;
use yii\helpers\Inflector;

/**
 * MultilinualFieldContainer
 *
 */
class MultilingualFieldContainer extends BaseObject
{
    public $fields;

    public function __call($method, $arguments)
    {
        $_html = '';
        foreach ($this->fields as $field) {
            $_html .= call_user_func_array(array($field, $method), $this->updateArguments($method, $arguments, $field));
        }

        return $_html;
    }

    /**
     * Fix generating id for multilingual inputs with custom id.
     * 
     * @param string $method
     * @param array $arguments
     * @param mixed $field
     * @return array
     */
    protected function updateArguments($method, $arguments, $field)
    {
        $language = '_' . Inflector::camel2id(Inflector::id2camel($field->language), "_");

        if ($method == 'widget' && isset($arguments[1]['options']['id'])) {
            $arguments[1]['options']['id'] .= $language;
        }

        if (in_array($method, ['textInput', 'textarea', 'radio', 'checkbox', 'fileInput', 'hiddenInput', 'passwordInput']) && isset($arguments[0]['id'])) {
            $arguments[0]['id'] .= $language;
        }

        if (in_array($method, ['input', 'dropDownList', 'listBox', 'radioList', 'checkboxList']) && isset($arguments[1]['id'])) {
            $arguments[1]['id'] .= $language;
        }

        return $arguments;
    }

}
