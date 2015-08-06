<?php

use yeesoft\widgets\Nav;

/* @var $this yii\web\View */
?>

<?= $wrapper[0]; ?>
<?=
Nav::widget(compact('encodeLabels', 'dropDownCaret', 'options', 'items'));
?>
<?= $wrapper[1]; ?>