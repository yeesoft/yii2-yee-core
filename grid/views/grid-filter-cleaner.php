<?php

use yii\web\View;
use yii\helpers\Html;

/**
 * @var View $this
 */
?>
<div class="grid-filter-cleaner form-inline pull-right">
    <span style="display: none" id="<?= ltrim($this->context->gridId, '#') ?>-clear-filters-btn"
          class="btn btn-sm btn-default">
        <?= Yii::t('yee', 'Clear filters') ?>
    </span>
</div>