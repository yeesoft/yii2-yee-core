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

    <?= Html::dropDownList(
        'grid-bulk-actions',
        null,
        $this->context->actions,
        [
            'class' => $this->context->dropDownClass,
            'id' => "{$this->context->gridId}-bulk-actions",
            'data-ok-button' => "#{$this->context->gridId}-ok-button",
            'prompt' => $this->context->promptText,
        ]
    ) ?>

    <?= Html::tag('span', Yii::t('yee', 'OK'), [
        'class' => "grid-bulk-ok-button {$this->context->okButtonClass} disabled",
        'id' => "{$this->context->gridId}-ok-button",
        'data-list' => "#{$this->context->gridId}-bulk-actions",
        'data-pjax' => "#{$this->context->pjaxId}",
        'data-grid' => "#{$this->context->gridId}",
    ]) ?>

</div>