<?php

namespace yeesoft\grid;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Url;

class GridFilterCleaner extends Widget
{

    /**
     * You can render different views for different places
     *
     * @var string
     */
    public $viewFile = 'grid-filter-cleaner';

    /**
     * @var string the `yii\widgets\Pjax` widget ID. If widget with current
     * selector is not found, the page will be reloaded after applying the action.
     */
    public $pjaxId;

    /**
     * Optional. Used only for "Clear filters" button.
     * If not set, then it will be guessed via $pjaxId
     *
     * @var string
     */
    public $gridId;

    public function init()
    {
        parent::init();

        if (!$this->pjaxId) {
            throw new InvalidConfigException('GridPageSize configuration must contain a "pjaxId" parameter.');
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
        $filterSelectors = $this->gridId . ' .filters input[type="text"], ' . $this->gridId . ' .filters select';
        $clearBtnId = $this->gridId . '-clear-filters-btn';

        $js = <<<JS
            var clearFiltersBtn = $('$clearBtnId');

            function showOrHideClearFiltersBtn() {
                var showClearFiltersButton = false;

                $('$filterSelectors').each(function(){
                    var _t = $(this);
                    if (_t.val()){
                        showClearFiltersButton = true;
                    }
                });

                if (showClearFiltersButton) {
                    clearFiltersBtn.show();
                } else {
                    clearFiltersBtn.hide();
                }
            }

            showOrHideClearFiltersBtn();

            // Show button if filters not empty and hide it if they are empty
            $('body').off('change', '$filterSelectors').on('change', '$filterSelectors', function () {
                showOrHideClearFiltersBtn();
            });

            // Clear filters on button click
            $('body').off('click', '$clearBtnId').on('click', '$clearBtnId', function () {
                var filter;
                $('$filterSelectors').each(function(){
                    filter = $(this);
                    filter.val('');
                });
                filter.trigger('change');
            });
JS;

        return $js;
    }

}
