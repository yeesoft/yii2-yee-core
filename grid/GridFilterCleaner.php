<?php

namespace yeesoft\grid;

use Yii;
use yii\base\Widget;
use yii\base\InvalidConfigException;

class GridFilterCleaner extends Widget
{

    /**
     * @var string the grid view ID.
     */
    public $gridId;

    /**
     * @var string clear button label 
     */
    public $label;

    /**
     * @var string filter cleaner view
     */
    public $viewFile = 'grid-filter-cleaner';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->gridId) {
            throw new InvalidConfigException('GridPageSize configuration must contain a "gridId" parameter.');
        }

        if (!$this->label) {
            $this->label = Yii::t('yee', 'Clear filters');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->view->registerJs($this->js());
        return $this->render($this->viewFile);
    }

    /**
     * @return string JavaScript code for the widget.
     */
    protected function js()
    {
        $clearButtonId = "#{$this->gridId} .grid-filter-cleaner";
        $filtersSelector = "#{$this->gridId} .filters input[type=\"text\"], #{$this->gridId} .filters select";

        $js = <<<JS
            var clearFilterButton = $('$clearButtonId');

            function updateClearButtonState() {
                var filterSelected = false;

                $('$filtersSelector').each(function(){
                    if ($(this).val()) {
                        filterSelected = true;
                    }
                });

                if (filterSelected) {
                    clearFilterButton.show();
                } else {
                    clearFilterButton.hide();
                }
            }

            $('body').off('change', '$filtersSelector').on('change', '$filtersSelector', function () {
                updateClearButtonState();
            });

            $('body').off('click', '$clearButtonId').on('click', '$clearButtonId', function () {
                var filter;
                $('$filtersSelector').each(function(){
                    filter = $(this);
                    filter.val('');
                });
                filter.trigger('change');
            });
                
            updateClearButtonState();
JS;

        return $js;
    }

}
