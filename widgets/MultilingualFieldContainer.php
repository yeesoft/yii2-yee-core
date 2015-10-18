<?php

namespace yeesoft\widgets;

use yii\base\Object;

/**
 * MultilinualFieldContainer
 *
 */
class MultilingualFieldContainer extends Object
{
    public $fields;

    public function __call($method, $arguments)
    {
        $_html = '';
        foreach ($this->fields as $field) {
            $_html .= call_user_func_array(array($field, $method), $arguments);
        }

        return $_html;
    }
}