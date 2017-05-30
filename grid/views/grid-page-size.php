<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */
?>
<div class="grid-page-size form-inline pull-right">
    <?= $this->context->text ?>
    <?= Html::dropDownList('grid-page-size', Yii::$app->request->cookies->getValue('_grid_page_size', 20), $this->context->dropDownOptions, ['class' => 'form-control input-sm']) ?>
</div>