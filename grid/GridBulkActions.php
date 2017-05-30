<?php

namespace yeesoft\grid;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\base\InvalidConfigException;

class GridBulkActions extends Widget
{

    /**
     * @var array the list of bulk actions. The array keys are action URL, and the array values
     * are the corresponding labels.
     */
    public $actions;

    /**
     * @var string the grid view ID.
     */
    public $gridId;

    /**
     * @var string the `yii\widgets\Pjax` widget ID. If widget with current
     * selector is not found, the page will be reloaded after applying the action.
     */
    public $pjaxId;

    /**
     * @var string apply button class.
     */
    public $applyButtonClass = 'btn btn-sm btn-default';

    /**
     * @var string bulk actions drop down class.
     */
    public $dropDownClass = 'form-control input-sm';

    /**
     * @var string grid bulk actions wrapper class.
     */
    public $wrapperClass = 'grid-bulk-actions form-inline pull-left';

    /**
     * @var string bulk actions drop down prompt text.
     */
    public $promptText;

    /**
     * @var string bulk actions modal confirmation text.
     */
    public $confirmationText;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->gridId) {
            throw new InvalidConfigException('GridBulkActions configuration must contain a "gridId" parameter.');
        }

        $this->promptText = $this->promptText ? $this->promptText : Yii::t('yee', 'Bulk Actions');
        $this->confirmationText = $this->confirmationText ? $this->confirmationText : Yii::t('yee', 'Are you sure you want to perform the action on selected items?');

        if (!$this->actions) {
            $this->actions = [Url::to(['bulk-delete']) => Yii::t('yee', 'Delete')];
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->view->registerJs($this->js());
        return $this->render('grid-bulk-actions');
    }

    /**
     * @return string return bulk actions js scripts.
     */
    protected function js()
    {
        $js = <<<JS
            $(document).off('change', '[name="grid-bulk-actions"]').on('change', '[name="grid-bulk-actions"]', function () {
                var _t = $(this);
                var applyButton = _t.closest('.grid-bulk-actions').find('.grid-bulk-apply-button');
                if ($(this).val()) {
                    applyButton.removeClass('disabled');
                } else {
                    applyButton.addClass('disabled');
                }
            });

            $(document).off('click', '.grid-bulk-apply-button').on('click', '.grid-bulk-apply-button', function () {
                var _t = $(this);
                if(_t.hasClass('disabled')){
                    return false;
                }
                
                var list = _t.closest('.grid-bulk-actions').find('.grid-bulk-actions-list');
                yii.confirm('{$this->confirmationText}', function(){
                    $.post(list.val(), _t.closest('#{$this->gridId}').find('[name="selection[]"]').serialize()).done(function(){
                        _t.addClass('disabled');
                        list.val('');
                        if(0 !== '{$this->pjaxId}'.length && _t.closest('#{$this->pjaxId}').length){
                            $.pjax.reload({container: '#{$this->pjaxId}'});
                            $(".modal.in").last().trigger('click.dismiss.bs.modal');
                        } else {
                            location.reload();
                        }
                    });
                }, function(){
                    return false;
                });
            });
JS;

        return $js;
    }

}
