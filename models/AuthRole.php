<?php

namespace yeesoft\models;

use Yii;
use yii\helpers\ArrayHelper;

class AuthRole extends AuthItem
{

    const ITEM_TYPE = self::TYPE_ROLE;

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(AuthFilter::className(), ['name' => 'filter_name'])
                        ->viaTable(Yii::$app->authManager->itemFilterTable, ['item_name' => 'name']);
    }

    /**
     * 
     * @param array $exclude
     * @return array
     */
    public static function getRoles($exclude = [])
    {
        $items = static::find()->andWhere(['not in', Yii::$app->authManager->itemTable . '.name', is_array($exclude) ? $exclude : [$exclude]])->all();
        return ArrayHelper::map($items, 'name', 'description');
    }

}
