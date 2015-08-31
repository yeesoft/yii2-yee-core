<?php

namespace yeesoft\widgets;

use yeesoft\models\User;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class Nav
 *
 * Show only those items in navigation menu which user can see.
 * If item has no "visible" key, than "visible" => User::canRoute($item['url') will be added.
 *
 * Nav support sub-dropdown menus. Submenus has no nested level limit.
 *
 * @var array $options setting for menu and submenus
 *
 * If $options is one dimensional array then this options will be applied to all sub-menus. Example:
 * 'options' => ['class' => 'nav nav-menu']
 *
 * If $options is two dimensional array then this options will be applied according
 * to nested submenu level. If there is no options for current level, default settings
 * will be applied. Example:
 * 'options' => [
 *   [ 'class' => 'nav nav-first-level'],
 *   [ 'class' => 'nav nav-second-level'],
 *   [ 'class' => 'nav nav-third-level']
 * ]
 *
 * @package yeesoft\widgets
 */
class Nav extends \yii\bootstrap\Nav
{

    public function init()
    {
        parent::init();

        $this->ensureVisibility($this->items);
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
            if (isset($item['url']) AND !isset($item['visible']) AND !in_array($item['url'],
                    ['', '#'])
            ) {
                $item['visible'] = User::canRoute($item['url']);
            }

            if (isset($item['items'])) {
                // If not children are visible - make invisible this node
                if (!$this->ensureVisibility($item['items']) AND !isset($item['visible'])) {
                    $item['visible'] = false;
                }
            }

            if (isset($item['label']) AND (!isset($item['visible']) OR $item['visible']
                    === true)
            ) {
                $allVisible = true;
            }
        }

        return $allVisible;
    }

    /**
     * Renders widget items.
     *
     * @param string|array $itemsList items to render.
     * @param int $level navigation nested level.
     * @return string the rendering result.
     * @throws InvalidConfigException
     */
    public function renderItems($itemsList = NULL, $level = 0)
    {
        $renderItems = ($itemsList === NULL) ? $this->items : $itemsList;
        $items = [];
        foreach ($renderItems as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $items[] = $this->renderItem($item, $level);
        }

        return Html::tag('ul', implode("\n", $items),
            $this->getLevelOptions($level));
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
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);

        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item);
        }

        if ($items !== null) {
            $linkOptions['data-toggle'] = 'dropdown';
            Html::addCssClass($options, 'dropdown');
            Html::addCssClass($linkOptions, 'dropdown-toggle');
            if ($this->dropDownCaret !== '') {
                $label .= ' ' . $this->dropDownCaret;
            }
            if (is_array($items)) {
                if ($this->activateItems) {
                    $items = $this->isChildActive($items, $active);
                }
                $items = $this->renderItems($items, $level + 1);
            }
        }

        if ($this->activateItems && $active) {
            Html::addCssClass($options, 'active');
        }

        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items,
            $options);
    }

    /**
     *  Return options array for current level navigation.
     *
     * @param type $level navigation nested level.
     * @return array options for current level navigation.
     */
    private function getLevelOptions($level = 0)
    {
        if ($this->options === NULL) return NULL;

        if (isset($this->options[$level]) && is_array($this->options[$level]))
            return $this->options[$level];

        if (!isset($this->options[$level]) && isset($this->options[0]) && is_array($this->options[0]))
            return ['class' => 'nav'];

        return $this->options;
    }
}