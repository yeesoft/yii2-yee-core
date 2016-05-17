<?php

namespace yeesoft\models;

/**
 * This is the ActiveQuery class for [[Post]].
 *
 * @see Post
 */
class UserQuery extends \yii\db\ActiveQuery
{

    public function active()
    {
        $this->andWhere(['status' => User::STATUS_ACTIVE]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return Post[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Post|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
