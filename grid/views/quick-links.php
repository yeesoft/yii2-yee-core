<?php
/**
 * @var View $this
 */
?>
<?php

use yii\helpers\Html;
use yii\web\View;

?>
<div class="<?= $this->context->wrapperClass ?>">
    <?php foreach ($this->context->links as $label => $url) : ?>
        <?= Html::a($label, $url, $this->context->linkOptions); ?>
    <?php endforeach; ?>
</div>