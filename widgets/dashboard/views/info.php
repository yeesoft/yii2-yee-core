<?php
use yeesoft\Yee;

/* @var $this yii\web\View */
?>

<div class="pull-<?= $position ?> col-lg-<?= $width ?> widget-height-<?= $height ?>">
    <div class="panel panel-default">
        <div class="panel-heading"><?= Yii::t('yee', 'System Info') ?></div>
        <div class="panel-body">
            <b><?= Yii::t('yee', 'Yee CMS Version') ?>:</b> <?= Yii::$app->params['version']; ?><br/>
            <b><?= Yii::t('yee', 'Yee Core Version') ?>:</b> <?= Yee::getVersion(); ?><br/>
            <b><?= Yii::t('yee', 'Yii Framework Version') ?>:</b> <?= Yii::getVersion(); ?><br/>
            <b><?= Yii::t('yee', 'PHP Version') ?>:</b> <?= phpversion(); ?><br/>
        </div>
    </div>
</div>