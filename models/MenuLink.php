<?php

namespace yeesoft\models;

use Yii;
use yeesoft\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yeesoft\behaviors\MultilingualBehavior;
use yeesoft\multilingual\db\MultilingualQuery;
use yeesoft\multilingual\db\MultilingualLabelsTrait;

/**
 * This is the model class for table "menu_link".
 *
 * @property string $id
 * @property string $menu_id
 * @property string $link
 * @property string $label
 * @property string $parent_id
 * @property integer $always_visible
 * @property string $image
 * @property integer $order
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Menu $menu
 */
class MenuLink extends ActiveRecord
{

    use MultilingualLabelsTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu_link}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            BlameableBehavior::className(),
            TimestampBehavior::className(),
            'sluggable' => [
                'class' => SluggableBehavior::className(),
                'slugAttribute' => 'id',
                'attribute' => 'label',
                'ensureUnique' => true,
            ],
            'multilingual' => [
                'class' => MultilingualBehavior::className(),
                'languageForeignKey' => 'link_id',
                'attributes' => [
                    'label'
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
            [['menu_id', 'label'], 'required'],
            ['id', 'unique'],
            [['order', 'always_visible', 'created_by', 'updated_by', 'created_at', 'updated_at',], 'integer'],
            [['id', 'menu_id', 'parent_id'], 'string', 'max' => 64],
            [['link', 'label'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 128],
            [['id'], 'match', 'pattern' => '/^[a-z0-9_-]+$/', 'message' => Yii::t('yee', 'Link ID can only contain lowercase alphanumeric characters, underscores and dashes.')],
            ['order', 'default', 'value' => 999],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yee', 'ID'),
            'menu_id' => Yii::t('yee', 'Menu'),
            'link' => Yii::t('yee', 'Link'),
            'label' => Yii::t('yee', 'Label'),
            'parent_id' => Yii::t('yee', 'Parent Link'),
            'always_visible' => Yii::t('yee', 'Always Visible'),
            'image' => Yii::t('yee', 'Icon'),
            'order' => Yii::t('yee', 'Order'),
            'created_by' => Yii::t('yee', 'Created By'),
            'updated_by' => Yii::t('yee', 'Updated By'),
            'created_at' => Yii::t('yee', 'Created'),
            'updated_at' => Yii::t('yee', 'Updated'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(Menu::className(), ['id' => 'menu_id'])->joinWith('translations');
    }

    /**
     * Get list of link siblings
     * @return array
     */
    public function getSiblings()
    {
        $siblings = MenuLink::find()->joinWith('translations')
                ->andFilterWhere(['like', 'menu_id', $this->menu_id])
                ->andFilterWhere(['!=', 'menu_link.id', $this->id])
                ->all();

        $list = ArrayHelper::map(
                        $siblings, 'id', function ($array, $default) {
                    return $array->label . ' [' . $array->id . ']';
                });

        return ArrayHelper::merge([NULL => Yii::t('yee', 'No Parent')], $list);
    }

    /**
     * Get list of children links.
     * @return array
     */
    public function getChildren()
    {
        return MenuLink::find()->joinWith('translations')
                        ->andFilterWhere(['=', 'menu_id', $this->menu_id])
                        ->andFilterWhere(['=', 'parent_id', $this->id])
                        ->orderBy('order')
                        ->all();
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
