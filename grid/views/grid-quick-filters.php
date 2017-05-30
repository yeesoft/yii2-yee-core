<?php

/**
 * @var View $this
 */
use yii\helpers\Html;
use yii\web\View;

?>
<?= Html::beginTag('div', ['class' => $this->context->wrapperClass]) ?>
<?php foreach ($links as $label => $url) : ?>
    <?= Html::a($label, $url, $this->context->linkOptions); ?>
<?php endforeach; ?>
<?= Html::endTag('div') ?>