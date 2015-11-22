<?php
/* @var $this yii\web\View */
?>

<div class="pull-<?= $position ?> col-lg-<?= $width ?> widget-height-<?= $height ?>">
    <div class="panel panel-default">
        <div class="panel-heading">System Info</div>
        <div class="panel-body">
            <b>Yee CMS Version:</b> <?= Yii::$app->params['version']; ?><br/>
            <b>Yii Version:</b> <?= Yii::getVersion(); ?><br/>
            <b>PHP Version:</b> <?= phpversion(); ?><br/>
            <b>Server IP:</b> <?= $_SERVER['SERVER_ADDR']; ?><br/>
        </div>
    </div>
</div>