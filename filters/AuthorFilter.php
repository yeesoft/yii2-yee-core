<?php

namespace yeesoft\filters;

use Yii;

/**
 * Active filter that allows access only for author of the record.
 */
class AuthorFilter extends ActiveFilter
{

    /**
     * @inheritdoc
     */
    public function getCondition($tableName)
    {
        $user = Yii::$app->user;

        if ($user->isGuest) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to access this object.');
        }

        return ["$tableName.created_by" => $user->id];
    }

}
