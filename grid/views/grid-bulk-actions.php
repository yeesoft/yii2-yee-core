<?php

/**
 * @var View $this
 */
use yii\web\View;
use yii\helpers\Html;

$dropDown = Html::dropDownList('grid-bulk-actions', null, $this->context->actions, [
            'class' => ['grid-bulk-actions-list', $this->context->dropDownClass],
            'prompt' => $this->context->promptText,
        ]);

$applyButton = Html::tag('span', Yii::t('yee', 'Apply'), [
            'class' => ['grid-bulk-apply-button', $this->context->applyButtonClass, 'disabled']
        ]);

echo Html::tag('div', $dropDown . ' ' . $applyButton, [
    'id' => $this->context->id,
    'class' => $this->context->wrapperClass,
]);
