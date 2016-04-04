<?php

namespace yeesoft\grid;

use yeesoft\helpers\YeeHelper;
use yeesoft\models\OwnerAccess;
use yeesoft\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class GridQuickLinks extends Widget
{
    /**
     * Model class
     *
     * @var string
     */
    public $model;

    /**
     * Search model class
     *
     * @var string
     */
    public $searchModel;

    /**
     * Action where search is located
     *
     * @var string
     */
    public $action = 'index';

    /**
     * Wrapper html element class
     *
     * @var string
     */
    public $wrapperClass = 'quick-actions';

    /**
     * Array of links to display.
     *
     * Usage examples:
     *
     * ~~~
     *  'links' = [
     *     'Dashboard' => ['/index'],
     *     'Settings' => ['/settings', 'id' => 'main']
     *  ]
     * ~~~
     *
     * @var array
     */
    public $links;

    /**
     * Link options
     *
     * @var array
     */
    public $linkOptions = [];

    /**
     * This settings will be used to generate links if $links param is NULL.
     *
     * Usage examples:
     *
     * ~~~
     *  'options' = [
     *    [ 'label' => 'All', 'filterWhere' => []],
     *    [ 'label' => 'Active', 'filterWhere' => ['status' => 1]],
     *    [ 'label' => 'Inactive', 'filterWhere' => ['status' => 0]],
     *  ]
     * ~~~
     *
     * or
     *
     * ~~~
     *  'options' = [
     *    [ 'label' => 'All', 'filterWhere' => []],
     *    [ 'label' => 'New', 'filterWhere' => ['date' => date("Y-m-d")]],
     *  ]
     * ~~~
     *
     * @var array
     */
    public $options;

    /**
     * Labels to override default labels.
     *
     * Usage examples:
     *
     * ~~~
     * 'labels' => [
     *    'all' => 'All',
     *    'active' => 'Published',
     *    'inactive' => 'Pending',
     * ]
     * ~~~
     *
     * @var array
     */
    public $labels;

    /**
     * Weather display or not counts in labels
     *
     * @var boolean
     */
    public $showCount = TRUE;

    /**
     * Default options
     *
     * @var type
     */
    protected $defaultOptions;

    public function init()
    {
        parent::init();

        if (!$this->defaultOptions) {
            $this->defaultOptions = [
                'all' => ['label' => Yii::t('yee', 'All'), 'filterWhere' => []],
                'active' => ['label' => Yii::t('yee', 'Active'), 'filterWhere' => ['status' => 1]],
                'inactive' => ['label' => Yii::t('yee', 'Inactive'), 'filterWhere' => ['status' => 0]],
            ];
        }

    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @return string
     */
    public function run()
    {
        if (!$this->model || !$this->searchModel) {
            throw new InvalidConfigException('Missing gridId param');
        }

        $this->setDefaultOptions();

        return $this->render('quick-links');
    }

    /**
     * Set default options
     */
    protected function setDefaultOptions()
    {

        if (!$this->links) {
            $model = $this->model;
            $formName = $this->searchModel->formName();

            if (!$this->options) {

                $this->options = $this->defaultOptions;

                if (is_array($this->labels)) {
                    $this->options = ArrayHelper::merge($this->options, self::addKeyToValue($this->labels, 'label')
                    );
                }
            }

            foreach ($this->options as $option) {
                if (($this->showCount)) {

                    if ((YeeHelper::isImplemented($model, OwnerAccess::CLASSNAME)
                        && !User::hasPermission($model::getFullAccessPermission()))
                    ) {
                        $option['filterWhere'][$model::getOwnerField()] = Yii::$app->user->identity->id;
                    }

                    $count = $model::find()->filterWhere($option['filterWhere'])->count();
                    $count = " ({$count})";
                }

                $label = $option['label'] . (($count) ? $count : '');
                $url = [$this->action, $formName => $option['filterWhere']];

                $this->links[$label] = $url;
            }
        }
    }

    /**
     * Addes key to value.
     *
     * Usage examples,
     *
     * ~~~
     * // $array = ['dashboard' => 'Dashboard', 'settings' => 'Settings'];
     * // working with array
     * $array = ArrayHelper::addKeyToValue($array, 'label');
     * // $array content
     * // $array = ['dashboard' => ['label' => 'Dashboard'], 'settings' => ['label' => 'Settings']];
     * ~~~
     *
     * @param array $array array
     * @param array $key key to add
     * @return array new array
     */
    protected static function addKeyToValue($array, $key)
    {
        array_walk($array, function (&$value) use ($key) {
            $value = [$key => $value];
        }
        );

        return $array;
    }
}