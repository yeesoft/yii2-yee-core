<?php

namespace yeesoft\db;

use Yii;

/**
 * @inheritdoc
 */
class ActiveRecord extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    /**
     * Returns TRUE if model support multilingual behavior.
     *
     * @param ActiveRecord $model
     * @return boolean
     */
    public function isMultilingual()
    {
        return ($this->getBehavior('multilingual') !== null);
    }

}
