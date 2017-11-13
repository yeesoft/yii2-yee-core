<?php

namespace yeesoft\models;

use Yii;
use yeesoft\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property resource $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthRule $rule
 * @property AuthItem[] $children
 * @property AuthItem[] $parents
 * @property AuthFilter[] $filters
 * @property AuthGroup[] $groups
 * @property AuthRoute[] $routes
 */
abstract class AuthItem extends ActiveRecord
{

    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;

    /**
     * Reassigned in child classes to type role, permission or route
     */
    const ITEM_TYPE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->authManager->itemTable;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['description', 'rule_name'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
            [['name', 'rule_name', 'description'], 'trim'],
            ['name', 'validateUniqueName'],
            ['type', 'in', 'range' => [static::TYPE_ROLE, static::TYPE_PERMISSION]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Title',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'resetRuleName']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'resetRuleName']);
    }

    /**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return parent::find()->andWhere([Yii::$app->authManager->itemTable . '.type' => static::ITEM_TYPE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])
            ->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])
            ->viaTable('auth_item_child', ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(AuthFilter::className(), ['name' => 'filter_name'])
            ->viaTable('auth_item_filter', ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(AuthGroup::className(), ['name' => 'group_name'])
            ->viaTable('auth_item_group', ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoutes()
    {
        return $this->hasMany(AuthRoute::className(), ['id' => 'route_id'])
            ->viaTable('auth_item_route', ['item_name' => 'name']);
    }

    /**
     * Default unique validator search only within specific class (Role, Route or Permission) because of the overwritten find() method
     */
    public function validateUniqueName($attribute)
    {
        if ($this->$attribute !== $this->getOldAttribute($attribute) && AuthRole::find()->where(['name' => $this->$attribute])->exists()) {
            $this->addError('name', Yii::t('yii', '{attribute} "{value}" has already been taken.', [
                'attribute' => $this->getAttributeLabel($attribute),
                'value' => $this->$attribute,
            ]));
        }
    }

    /**
     * Ensure type of item
     *
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->type = static::ITEM_TYPE;

        return parent::beforeSave($insert);
    }

    /**
     * Invalidate permissions if some item is deleted
     */
    public function afterDelete()
    {
        parent::afterDelete();
    }

    public function resetRuleName()
    {
        if (empty($this->rule_name)) {
            $this->rule_name = null;
        }
    }

}
