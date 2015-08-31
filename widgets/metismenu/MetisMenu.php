<?php

namespace yeesoft\widgets\metismenu;

use yeesoft\helpers\MenuHelper;
use yeesoft\models\Menu as MenuModel;

/**
 * MetisMenu widget for Yee CMS.
 *
 * The widget renders menu by ID.
 *
 * Example:
 * <pre>
 * echo MetisMenu::widget([
 * &nbsp; 'id' => 'main-menu',
 * &nbsp; 'dropDownCaret' => '<span class="arrow"></span>',
 * &nbsp; 'wrapper' => [
 * &nbsp; &nbsp; '<div class="sidebar"',
 * &nbsp; &nbsp; '</div>'
 * &nbsp; ],
 * &nbsp; 'options' => [
 * &nbsp; &nbsp; [ 'class' => 'nav nav-first-level'],
 * &nbsp; &nbsp; [ 'class' => 'nav nav-second-level'],
 * &nbsp; &nbsp; [ 'class' => 'nav nav-third-level']
 * &nbsp; ],
 *   ]);
 * </pre>
 */
class MetisMenu extends \yii\base\Widget
{
    /**
     * Menu model id
     *
     * @var string
     */
    public $id;

    /**
     * Menu wrapper. Array with two elements: first element will be placed before
     * menu, second element will be placed after menu.
     *
     * @var array
     */
    public $wrapper = ['<div class="metismenu">', '</div>'];

    /**
     * Menu and submenus options.
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
     * @var array
     */
    public $options;

    /**
     *
     * @inheritdoc
     */
    public $encodeLabels = false;

    /**
     * Submenu dropdown caret
     *
     * @var string
     */
    public $dropDownCaret;

    /**
     * Menu items
     *
     * @var array
     */
    public $items;

    public function run()
    {
        $links = MenuModel::findOne($this->id)
            ->getLinks()
            ->orderBy(['parent_id' => 'ACS', 'order' => 'ACS'])
            ->asArray()->all();

        $this->items = self::generateNavigationItems($links, $this->options);

        return $this->render('metis-menu', get_object_vars($this));
    }

    private static function generateNavigationItems($links, $options)
    {
        $items = [];
        $linksByParent = [];

        foreach ($links as $link) {
            $linksByParent[$link['parent_id']][] = $link;
        }

        foreach ($linksByParent[''] as $link) {
            $items[] = self::generateItem($link, $linksByParent, $options);
        }

        return $items;
    }

    private static function generateItem($link, $menuLinks)
    {
        $item = [];
        $icon = (!empty($link['image'])) ? MenuHelper::generateIcon($link['image']) . ' '
            : '';

        $subItems = self::generateSubItems($link['id'], $menuLinks);

        $item['label'] = $icon . $link['label'];

        if (isset($link['alwaysVisible']) && $link['alwaysVisible']) {
            $item['visible'] = true;
        }

        if ($link['link']) {
            $url = parse_url($link['link']);
            $item['url'] = (isset($url['scheme'])) ? $link['link'] : [$link['link']];
        }

        if (is_array($subItems)) {
            $item['items'] = $subItems;
        }

        return $item;
    }

    private static function generateSubItems($parent_id, $menuLinks)
    {
        if (isset($menuLinks[$parent_id])) {
            $items = [];

            foreach ($menuLinks[$parent_id] as $link) {
                $items[] = self::generateItem($link, $menuLinks);
            }

            return $items;
        }

        return NULL;
    }
}