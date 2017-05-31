<?php

/**
 * @var View $this
 */
use yii\web\View;
use yii\helpers\Html;

echo Html::tag('span', $this->context->label, ['class' => 'grid-filter-cleaner btn btn-sm btn-default']);
