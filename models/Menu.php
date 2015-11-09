<?php

namespace yeesoft\models;

use omgdef\multilingual\MultilingualQuery;
use yeesoft\behaviors\MultilingualBehavior;
use yeesoft\helpers\MenuHelper;
use yii\helpers\ArrayHelper;
use yeesoft\Yee;

/**
 * This is the model class for table "menu".
 *
 * @property string $id
 * @property string $title
 *
 * @property MenuLink[] $menuLinks
 */
class Menu extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'multilingual' => [
                'class' => MultilingualBehavior::className(),
                'langForeignKey' => 'menu_id',
                'tableName' => "{{%menu_lang}}",
                'attributes' => [
                    'title'
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title'], 'required'],
            [['id'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['id'], 'match', 'pattern' => '/^[a-z0-9_-]+$/', 'message' =>  Yee::t('yee', 'Menu ID can only contain lowercase alphanumeric characters, underscores and dashes.')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yee::t('yee', 'ID'),
            'title' => Yee::t('yee', 'Title'),
        ];
    }

    /**
     * @return \omgdef\multilingual\MultilingualQuery
     */
    public function getLinks()
    {
        return $this->hasMany(MenuLink::className(), ['menu_id' => 'id'])->joinWith('translations');
    }

    /**
     * get list of menus
     * @return array
     */
    public static function getList()
    {
        return ArrayHelper::map(self::find()->asArray()->all(), 'id', 'title');
    }

    /**
     * get list of menus
     * @return array
     */
    public static function getMenuItems($menu_id)
    {
        $links = self::findOne($menu_id)
            ->getLinks()
            ->orderBy(['parent_id' => 'ACS', 'order' => 'ACS'])
            ->all();

        return self::generateNavigationItems($links);
    }

    private static function generateNavigationItems($links)
    {
        $items = [];
        $linksByParent = [];

        foreach ($links as $link) {
            $linksByParent[$link->parent_id][] = $link;
        }

        foreach ($linksByParent[''] as $link) {
            $items[] = self::generateItem($link, $linksByParent);
        }

        return $items;
    }

    private static function generateItem($link, $menuLinks)
    {
        $item = [];
        $icon = (!empty($link->image)) ? MenuHelper::generateIcon($link->image) . ' ' : '';

        $subItems = self::generateSubItems($link->id, $menuLinks);

        $item['label'] = $icon . $link->label;

        if (isset($link->alwaysVisible) && $link->alwaysVisible) {
            $item['visible'] = true;
        }

        if ($link->link) {
            $url = parse_url($link->link);
            $item['url'] = (isset($url['scheme'])) ? $link->link : [$link->link];
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

    /**
     * @inheritdoc
     * @return MultilingualQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }
}