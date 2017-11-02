<?php

namespace yeesoft\widgets;

use Yii;
use yeesoft\models\User;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yeesoft\helpers\FA;

/**
 * Class Nav
 *
 * Show only those items in navigation menu which user can see.
 * If item has no "visible" key, than "visible" => User::canRoute($item['url') will be added.
 *
 * Nav support submenus. Submenus has no nested level limit.
 *
 * @package yeesoft\widgets
 */
class Nav extends \yii\bootstrap\Nav
{

    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'sidebar-menu', 'data-widget' => 'tree'];

    /**
     * @var array the HTML attributes for the inner container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $innerOptions = ['class' => 'treeview-menu'];

    /**
     * @var boolean whether the nav items labels should be HTML-encoded.
     */
    public $encodeLabels = false;

    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     */
    public $activateParents = true;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        $this->trigger(self::EVENT_INIT);

        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        if ($this->dropDownCaret === null) {
            $this->dropDownCaret = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-angle-left pull-right']), ['class' => 'pull-right-container']);
        }

        $this->ensureVisibility($this->items);
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        return $this->renderItems();
    }

    /**
     * @param array $items
     *
     * @return bool
     */
    protected function ensureVisibility(&$items)
    {
        $allVisible = false;

        foreach ($items as &$item) {
            if (isset($item['url']) AND ! isset($item['visible']) AND ! in_array($item['url'], ['', '#'])) {
                $item['visible'] = User::canRoute($item['url']);
            }

            if (isset($item['items'])) {
                // If not children are visible - make invisible this node
                if (!$this->ensureVisibility($item['items']) AND ! isset($item['visible'])) {
                    $item['visible'] = false;
                }
            }

            if (isset($item['label']) AND ( !isset($item['visible']) OR $item['visible'] === true)) {
                $allVisible = true;
            }
        }

        return $allVisible;
    }

    /**
     * Renders widget items.
     *
     * @param string|array $items items to render.
     * @param int $level navigation nested level.
     * @return string the rendering result.
     * @throws InvalidConfigException
     */
    public function renderItems($items = NULL, $level = 0)
    {
        $rendered = [];
        $items = ($items === NULL) ? $this->items : $items;

        foreach ($items as $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $rendered[] = $this->renderItem($item, $level);
        }

        return Html::tag('ul', implode("\n", $rendered), ($level > 0) ? $this->innerOptions : $this->options);
    }

    /**
     * Renders a widget's item.
     *
     * @param string|array $item the item to render.
     * @param int $level navigation nested level.
     * @return string the rendering result.
     * @throws InvalidConfigException
     */
    public function renderItem($item, $level = 0)
    {
        if (is_string($item)) {
            return $item;
        }
        
        if(!ArrayHelper::getValue($item, 'url') && !ArrayHelper::getValue($item, 'items')){
            return Html::tag('li', $item['label'], ['class' => 'header']);
        }

        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $icon = (isset($item['icon']) && !empty($item['icon'])) ? FA::icon($item['icon']) . ' ' : '';
        $circle = ($level > 0) ? Html::tag('i', '', ['class' => 'fa fa-circle-o']) : '';
        $title = Html::tag('span', $item['label']);
        $label = $icon . $circle . $title;
        $label = $encodeLabel ? Html::encode($label) : $label;
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);

        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item);
        }

        if (is_array($items)) {
            Html::addCssClass($options, 'treeview');

            if ($this->dropDownCaret !== '') {
                $label .= ' ' . $this->dropDownCaret;
            }

            if ($this->activateItems) {
                $items = $this->isChildActive($items, $active);
            }
            $items = $this->renderItems($items, $level + 1);
        }

        if ($this->activateItems && $active) {
            Html::addCssClass($options, 'active');
        }

        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items, $options);
    }

}
