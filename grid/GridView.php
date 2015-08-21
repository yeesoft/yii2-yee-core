<?php

namespace yeesoft\grid;

class GridView extends \yii\grid\GridView
{
    public $bulkActions;
    public $bulkActionOptions = [];
    public $filterPosition = self::FILTER_POS_HEADER;
    public $pager = [
        'options' => ['class' => 'pagination pagination-sm'],
        'hideOnSinglePage' => true,
        'firstPageLabel' => 'First',
        'prevPageLabel' => '<',
        'nextPageLabel' => '>',
        'lastPageLabel' => 'Last',
    ];
    public $tableOptions = ['class' => 'table table-striped'];
    public $layout = '{items}<div class="row"><div class="col-sm-4 m-tb-20">{bulkActions}</div><div class="col-sm-5 text-center">{pager}</div><div class="col-sm-3 text-right m-tb-20">{summary}</div></div>';

    public function renderSection($name)
    {
        switch ($name) {
            case '{bulkActions}':
                return $this->renderBulkActions();
            default:
                return parent::renderSection($name);
        }
    }

    public function renderBulkActions()
    {
        if (!$this->bulkActions) {
            $this->bulkActions = GridBulkActions::widget($this->bulkActionOptions);
        }
        return $this->bulkActions;
    }
}