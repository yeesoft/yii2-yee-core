<?php

namespace yeesoft\widgets;

/**
 * Multilingual ActiveForm
 */
class ActiveForm extends \yeesoft\multilingual\widgets\ActiveForm
{

    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     */
    public $fieldClass = 'yeesoft\widgets\ActiveField';

}
