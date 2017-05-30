<?php

namespace yeesoft\grid;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class GridPageSize extends Widget
{

    /**
     * You can render different views for different places
     *
     * @var string
     */
    public $viewFile = 'grid-page-size';

    /**
     * @var string the `yii\widgets\Pjax` widget ID. If widget with current
     * selector is not found, the page will be reloaded after applying the action.
     */
    public $pjaxId;

    /**
     * Default - Url::to(['grid-page-size'])
     *
     * @var string
     */
    public $url;

    /**
     * @var array
     */
    public $dropDownOptions;

    /**
     * @var array
     */
    public $pageSizes = [5, 10, 20, 50, 100];

    /**
     * Text "Records per page"
     *
     * @var string
     */
    public $text;

    public function init()
    {
        parent::init();

        $this->text = $this->text ? $this->text : Yii::t('yee', 'Records per page');

        if (!$this->dropDownOptions) {
            $this->dropDownOptions = array_combine($this->pageSizes, $this->pageSizes);
        }

        if (!$this->url) {
            $this->url = Url::to(['grid-page-size']);
        }
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @return string
     */
    public function run()
    {
        $this->view->registerJs($this->js());
        return $this->render($this->viewFile);
    }

    /**
     * @return string
     */
    protected function js()
    {
        $js = <<<JS
            $('body').off('change', '[name="grid-page-size"]').on('change', '[name="grid-page-size"]', function () {
                var _t = $(this);
                $.post('$this->url', {'grid-page-size': _t.val()}).done(function(){
                    if(0 !== '{$this->pjaxId}'.length && _t.closest('#{$this->pjaxId}').length){
                        $.pjax.reload({container: '#{$this->pjaxId}'});
                    } else {
                        location.reload();
                    }
                });
            });
JS;
        return $js;
    }

}
