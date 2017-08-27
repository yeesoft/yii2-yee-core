<?php

namespace yeesoft\filters;

use Yii;

class AuthorFilter extends ActiveQueryFilter
{

    /**
     * @inheritdoc
     */
    public function getCondition($tableName)
    {
        $user = Yii::$app->user;
        if ($user->isGuest) {
            
        }

        return ["$tableName.created_by" => $user->id];
    }

}
