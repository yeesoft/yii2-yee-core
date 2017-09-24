<?php

namespace yeesoft\filters;

use Yii;
use yii\helpers\ArrayHelper;
use yeesoft\models\User;
use yeesoft\rbac\ManagerInterface;

class AccessControl extends \yii\filters\AccessControl
{

    /**
     * @inheritdoc
     */
    public $ruleConfig = ['class' => 'yeesoft\filters\AccessRule'];

    /**
     * @inheritdic
     */
    public function init()
    {
        parent::init();

        if (!Yii::$app->authManager instanceof ManagerInterface) {
            throw new \yii\base\InvalidConfigException('`Yii::$app->authManager` must implement `yeesoft\rbac\ManagerInterface`');
        }
    }

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        $rules = Yii::$app->authManager->getRouteRules($this->ruleConfig);
        $this->rules = ArrayHelper::merge($this->rules, $rules);
    }

    /**
     * @inheritdoc
     * @param \yii\base\Action $action the action to be executed.
     * @return bool whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        /* @var $auth \yeesoft\rbac\DbManager */
        $user = $this->user;
        $auth = Yii::$app->authManager;

        if ($auth->hasFreeAccess($action->uniqueId, $action)) {
            return true;
        }

        //If user has been deleted, then destroy session and deny access
        if (!$user->isGuest AND $user->identity === null) {
            Yii::$app->getSession()->destroy();
            $this->denyAccess($user);
        }

        if ($user->identity AND $user->identity->status != User::STATUS_ACTIVE) {
            $user->logout();
            $this->denyAccess($user);
        }

        if ($user->isSuperadmin) {
            //return true;
        }

        return parent::beforeAction($action);
    }

}
