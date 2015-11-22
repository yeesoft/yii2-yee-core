<?php
namespace yeesoft\grid;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Url;

class GridBulkActions extends Widget
{
    /**
     * @var array
     */
    public $actions;

    /**
     * @var string
     */
    public $controller;
    /**
     * @var string
     */
    public $gridId;

    /**
     * Default - $this->gridId . '-pjax'
     *
     * @var string
     */
    public $pjaxId;

    /**
     * @var string
     */
    public $okButtonClass = 'btn btn-sm btn-default';

    /**
     * @var string
     */
    public $dropDownClass = 'form-control input-sm';

    /**
     * @var string
     */
    public $wrapperClass = 'form-inline';

    /**
     * @var string
     */
    public $promptText;

    /**
     * @var string
     */
    public $confirmationText;

    /**
     * Multilingual support
     */
    public function init()
    {
        parent::init();

        $this->promptText = $this->promptText ? $this->promptText : Yii::t('yee', '--- With selected ---');
        $this->confirmationText = $this->confirmationText ? $this->confirmationText : Yii::t('yii', 'Are you sure you want to delete this item?');
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @return string
     */
    public function run()
    {
        if (!$this->gridId) {
            throw new InvalidConfigException('Missing gridId param');
        }

        $this->setDefaultOptions();

        $this->view->registerJs($this->js());

        return $this->render('bulk-actions');
    }

    /**
     * Set default options
     */
    protected function setDefaultOptions()
    {
        if (!$this->actions) {
            $this->actions = [
                Url::to(['bulk-activate']) => Yii::t('yee', 'Activate'),
                Url::to(['bulk-deactivate']) => Yii::t('yee', 'Deactivate'),
                Url::to(['bulk-delete']) => Yii::t('yee', 'Delete'),
            ];
        }

        if (!$this->pjaxId) {
            $this->pjaxId = $this->gridId . '-pjax';
        }
    }

    /**
     * @return string
     */
    protected function js()
    {
        $js = <<<JS

		// Select values in bulk actions list
		$(document).off('change', '[name="grid-bulk-actions"]').on('change', '[name="grid-bulk-actions"]', function () {
			var _t = $(this);
			var okButton = $(_t.data('ok-button'));

			if (_t.val()) {
				okButton.removeClass('disabled');
			}
			else {
				okButton.addClass('disabled');
			}
		});

		// Clicking OK button
		$(document).off('click', '.grid-bulk-ok-button').on('click', '.grid-bulk-ok-button', function () {
			var _t = $(this);
			var list = $(_t.data('list'));

			if (list.val().indexOf('bulk-delete') >= 0) {
				if ( ! confirm('$this->confirmationText') )
					return false;
			}

			$.post(list.val(), $(_t.data('grid') + ' [name="selection[]"]').serialize() )
				.done(function(){
					_t.addClass('disabled');
					list.val('');

					$.pjax.reload({container: _t.data('pjax')});
				});
		});
JS;

        return $js;

    }
} 
