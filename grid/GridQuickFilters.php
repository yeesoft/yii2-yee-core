<?php

namespace yeesoft\grid;

use Yii;
use yii\helpers\Html;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yeesoft\db\FilterableQuery;

class GridQuickFilters extends Widget
{

    /**
     * @var string Action where search is located
     */
    public $action = 'index';

    /**
     * @var array the list of filters. The array keys are labels, and the array values
     * are the corresponding filters for `\yii\db\Query::filterWhere`.
     */
    public $filters;

    /**
     * @var array filter link options.
     */
    public $linkOptions = [];

    /**
     * @var \yii\db\ActiveRecord search model class name.
     */
    public $searchModel;

    /**
     * @var string quick filters wrapper CSS class.
     */
    public $wrapperClass = 'grid-quick-filters';

    /**
     * @var boolean weather display or not counts in labels.
     */
    public $showCount = true;

    /**
     * @var array list of links.
     */
    private $_links = [];

    public function init()
    {
        if (!$this->searchModel) {
            throw new InvalidConfigException('GridQuickFilters configuration must contain a "searchModel" parameter.');
        }

        $searchModel = $this->searchModel;
        $formName = $searchModel->formName();

        if (!$this->filters) {
            $this->filters = [Yii::t('yee', 'All') => []];

            if ($this->searchModel->hasAttribute('status')) {
                $this->filters[Yii::t('yee', 'Active')] = ['status' => 1];
                $this->filters[Yii::t('yee', 'Inactive')] = ['status' => 0];
            }
        }

        foreach ($this->filters as $label => $filter) {
            if ($this->showCount) {

                $query = $searchModel::find()->filterWhere($filter);
                
                if ($query instanceof FilterableQuery) {
                    $query->applyFilters();
                }
                
                $count = $query->count();
                $count = Html::tag('b', $count);
            }

            $label = $label . (isset($count) ? " {$count}" : '');
            $filter = ($formName) ? [$formName => $filter] : $filter;
            $url = [$this->action] + $filter;

            $this->_links[$label] = $url;
        }

        parent::init();
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @return string
     */
    public function run()
    {
        return $this->render('grid-quick-filters', ['links' => $this->_links]);
    }

}
