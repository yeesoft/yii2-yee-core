<?php

namespace yeesoft\grid;

class GridView extends \yii\grid\GridView
{

    public $pageSize;
    public $bulkActions;
    public $bulkActionOptions = [];
    public $filterPosition = self::FILTER_POS_HEADER;
    public $pager = [
        'options' => ['class' => 'pagination pagination-sm'],
        'hideOnSinglePage' => true,
        'firstPageLabel' => '<<',
        'prevPageLabel' => '<',
        'nextPageLabel' => '>',
        'lastPageLabel' => '>>',
    ];
    public $tableOptions = ['class' => 'table table-striped'];
    public $layout = '<div class="row">'
            . '<div class="col-sm-6">{bulkActions}</div>'
            . '<div class="col-sm-6 text-right" style="padding-top: 5px">{summary}</div>'
            . '</div>{items}'
            . '<div class="row">'
            . '<div class="col-sm-4 m-tb-20">{bulkActions}</div>'
            . '<div class="col-sm-5 text-center">{pager}</div>'
            . '<div class="col-sm-3 m-tb-20">{pageSize}</div>'
            . '</div>';

    public function renderSection($name)
    {
        switch ($name) {
            case '{bulkActions}':
                return $this->renderBulkActions();
            case '{pageSize}':
                return $this->renderPageSize();
            default:
                return parent::renderSection($name);
        }
    }

    public function renderPageSize()
    {
        if (!$this->pageSize) {
            $this->pageSize = GridPageSize::widget(['pjaxId' => 'page-grid-pjax']);
        }
        return $this->pageSize;
    }

    public function renderBulkActions()
    {
        if (!$this->bulkActions) {
            $this->bulkActions = GridBulkActions::widget($this->bulkActionOptions);
        }
        return $this->bulkActions;
    }

}
