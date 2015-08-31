<?php

use yeesoft\widgets\metismenu\assets\MetisMenuAsset;
use yeesoft\widgets\Nav;

/* @var $this yii\web\View */

MetisMenuAsset::register($this);
?>

<?= $wrapper[0]; ?>
<?=
Nav::widget(compact('encodeLabels', 'dropDownCaret', 'options', 'items'));
?>
<?= $wrapper[1]; ?>