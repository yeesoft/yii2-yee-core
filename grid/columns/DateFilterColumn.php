<?php

namespace yeesoft\grid\columns;

use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\jui\DatePicker;

class DateFilterColumn extends DataColumn
{
    /**
     * @var array
     */
    public $contentOptions = ['style' => 'text-align:center; vertical-align: middle;'];

    /**
     * @var array
     */
    public $headerOptions = ['style' => 'text-align:center;'];

    /**
     * Renders the filter cell content.
     * The default implementation simply renders a space.
     * This method may be overridden to customize the rendering of the filter cell (if any).
     * @return string the rendering result
     */
    protected function renderFilterCellContent()
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute,
                        $this->grid->filterErrorOptions);
            } else {
                $error = '';
            }

            $filterOptions = ['=' => '=', '>' => '>', '<' => '<'];
            Html::addCssClass($this->filterInputOptions, 'date-filter-input');

            $dropDown = Html::activeDropDownList($model,
                $this->attribute . '_operand', $filterOptions, [
                    'class' => 'form-control pull-left',
                    'style' => 'width: 32px; appearance: none; -moz-appearance: none; -webkit-appearance: none;',
                    ]);
            $field = DatePicker::widget(['model' => $model, 'attribute' => $this->attribute,
                'options' => $this->filterInputOptions, 'dateFormat' => 'yyyy-MM-dd',]);

            return $dropDown . $field . $error;
        } else {
            return parent::renderFilterCellContent();
        }
    }
}