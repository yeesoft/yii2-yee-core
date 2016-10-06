<?php

namespace yeesoft\grid;

use yeesoft\helpers\Html;
use Closure;

class CheckboxColumn extends \yii\grid\CheckboxColumn
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->checkboxOptions instanceof Closure) {
            $options = call_user_func($this->checkboxOptions, $model, $key, $index, $this);
        } else {
            $options = $this->checkboxOptions;
            if (!isset($options['value'])) {
                $options['value'] = is_array($key) ? json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $key;
            }
        }

        return Html::checkbox($this->name, !empty($options['checked']), $options);
    }

}
