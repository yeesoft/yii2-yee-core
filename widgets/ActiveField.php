<?php

namespace yeesoft\widgets;

use yii\helpers\Html;

/**
 * @inheritdoc
 */
class ActiveField extends \yeesoft\multilingual\widgets\ActiveField
{

    /**
     * Renders a text value.
     *
     * @return $this the field object itself.
     */
    public function value($options = [])
    {
        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);

        if (!array_key_exists('id', $options)) {
            $options['id'] = Html::getInputId($this->model, $this->attribute);
        }

        $value = isset($options['value']) ? $options['value'] : Html::getAttributeValue($this->model, $this->attribute);
        $this->parts['{input}'] = Html::tag('span', ": {$value}", $options);

        return $this;
    }

}
