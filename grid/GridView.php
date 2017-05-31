<?php

namespace yeesoft\grid;

use Yii;
use yii\helpers\Html;

class GridView extends \yii\grid\GridView
{

    /**
     * @var array | boolean actions that you can perform in bulk. `yeesoft\grid\CheckboxColumn` 
     * is required for bulk action component. You must include this column type 
     * to your grid view columns. If this property is set to false, the block will 
     * be skipped.
     * @see GridBulkActions for details.
     */
    public $bulkActions;

    /**
     * @var string | boolean the settings for filter cleaner widget. If this property 
     * is set to false, the block will be skipped.
     * @see GridFilterCleaner for details.
     */
    public $filterCleaner;

    /**
     * @var string | boolean additional content or actions that is displayed just 
     * after bulk actions in the grid view header. If this property is set to false, 
     * the block will be skipped.
     */
    public $extraActions = false;

    /**
     * @inheritdoc
     */
    public $filterPosition = self::FILTER_POS_HEADER;

    /**
     * @var string | boolean the settings for quick filter links like "Active", 
     * "Inactive", etc. If this property is set to false, the block will be skipped.
     * @see GridQuickFilters for details.
     */
    public $quickFilters;

    /**
     * @var string | boolean the settings for grid page size widget. If this property 
     * is set to false, the block will be skipped.
     * @see GridPageSize for details.
     */
    public $pageSize;

    /**
     * @var string the Pjax widget ID.
     */
    public $pjaxId;

    /**
     * @inheritdoc
     */
    public $pager = [
        'options' => ['class' => 'pagination pagination-sm'],
        'hideOnSinglePage' => true,
        'firstPageLabel' => '<<',
        'prevPageLabel' => '<',
        'nextPageLabel' => '>',
        'lastPageLabel' => '>>',
    ];

    /**
     * @inheritdoc
     */
    public $tableOptions = ['class' => 'table table-striped'];

    /**
     * @inheritdoc
     */
    public $layout = '<div class="row head-row">'
            . '<div class="col-xs-6">{quickFilters}</div>'
            . '<div class="col-xs-6 text-right">{filterCleaner} {summary}</div>'
            . '</div>'
            . '<div class="row head-row">'
            . '<div class="col-xs-12 col-md-6">{bulkActions}</div>'
            . '<div class="col-xs-12 col-md-6 text-right">{extraActions}</div>'
            . '</div>'
            . '<div class="row">'
            . '<div class="col-xs-12">{items}</div>'
            . '</div>'
            . '<div class="row">'
            . '<div class="col-xs-12 col-md-4 bottom-row">{bulkActions}</div>'
            . '<div class="col-xs-12 col-md-5 text-center">{pager}</div>'
            . '<div class="col-xs-12 col-md-3 bottom-row">{pageSize}</div>'
            . '</div>';

    /**
     * @inheritdoc
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{bulkActions}':
                return $this->renderBulkActions();
            case '{filterCleaner}':
                return $this->renderFilterCleaner();
            case '{extraActions}':
                return $this->renderExtraActions();
            case '{pageSize}':
                return $this->renderPageSize();
            case '{quickFilters}':
                return $this->renderQuickFilters();
            default:
                return parent::renderSection($name);
        }
    }

    /**
     * Renders the bulk actions section.
     * @return string the rendering result of the section.
     */
    public function renderBulkActions()
    {
        if ($this->bulkActions !== false) {
            if (!isset($this->bulkActions['class'])) {
                $this->bulkActions['class'] = GridBulkActions::class;
            }

            if (!isset($this->bulkActions['pjaxId']) && $this->pjaxId) {
                $this->bulkActions['pjaxId'] = $this->pjaxId;
            }

            if (!isset($this->bulkActions['gridId'])) {
                $this->bulkActions['gridId'] = $this->id;
            }

            return Yii::createObject($this->bulkActions)->run();
        }
    }

    /**
     * Renders the filter cleaner section.
     * @return string the rendering result of the section.
     */
    public function renderFilterCleaner()
    {
        if ($this->filterCleaner !== false) {
            if (!isset($this->filterCleaner['class'])) {
                $this->filterCleaner['class'] = GridFilterCleaner::class;
            }

            if (!isset($this->filterCleaner['gridId'])) {
                $this->filterCleaner['gridId'] = $this->id;
            }

            return Yii::createObject($this->filterCleaner)->run();
        }
    }

    /**
     * Renders the extra actions section.
     * @return string the rendering result of the section.
     */
    public function renderExtraActions()
    {
        if ($this->extraActions !== false) {
            return Html::tag('span', $this->extraActions, ['class' => 'grid-additional-actions']);
        }
    }

    /**
     * Renders the quick filters section.
     * @return string the rendering result of the section.
     */
    public function renderQuickFilters()
    {
        if ($this->quickFilters !== false) {
            if (!isset($this->quickFilters['class'])) {
                $this->quickFilters['class'] = GridQuickFilters::class;
            }

            if (!isset($this->quickFilters['searchModel'])) {
                $this->quickFilters['searchModel'] = $this->filterModel;
            }

            return Yii::createObject($this->quickFilters)->run();
        }
    }

    /**
     * Renders the page size section.
     * @return string the rendering result of the section.
     */
    public function renderPageSize()
    {
        if ($this->pageSize !== false) {
            if (!isset($this->pageSize['class'])) {
                $this->pageSize['class'] = GridPageSize::class;
            }

            if (!isset($this->pageSize['pjaxId']) && $this->pjaxId) {
                $this->pageSize['pjaxId'] = $this->pjaxId;
            }

            return Yii::createObject($this->pageSize)->run();
        }
    }

}
