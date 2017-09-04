<?php

namespace yeesoft\filters;

use Yii;
use yeesoft\models\User;
use yeesoft\models\Route;
use yeesoft\helpers\YeeHelper;
use yeesoft\models\OwnerAccess;
use yeesoft\rbac\ManagerInterface;
use yii\helpers\ArrayHelper;

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

//        if (!Yii::$app->authManager instanceof ManagerInterface) {
//            throw new \yii\base\InvalidConfigException('`Yii::$app->authManager` must implement `yeesoft\rbac\ManagerInterface`');
//        }
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
        return true;
        
        /* @var $auth \yeesoft\rbac\DbManager */
        $user = $this->user;
        $auth = Yii::$app->authManager;
        $request = Yii::$app->getRequest();
        $route = '/' . $action->uniqueId;

        //return true;
//        if ($auth->hasFreeAccess($route, $action)) {
//            return true;
//        }
//
//        if ($user->isGuest) {
//            $this->denyAccess($user);
//        }
        // If user has been deleted, then destroy session and deny access
//        if (!$user->isGuest AND $user->identity === null) {
//            Yii::$app->getSession()->destroy();
//            $this->denyAccess($user);
//        }
        if ($user->isSuperadmin) {
            //return true;
        }
//        if ($user->identity AND $user->identity->status != User::STATUS_ACTIVE) {
//            $user->logout();
//            Yii::$app->response->redirect(Yii::$app->getHomeUrl());
//        }
//
//        if (User::canRoute($route)) {
//            $modelId = Yii::$app->getRequest()->getQueryParam('id');
//            $modelClass = (isset($this->owner->modelClass)) ? $this->owner->modelClass : null;
//
//            //Check access for owners
//            if ($modelClass && YeeHelper::isImplemented($modelClass, OwnerAccess::CLASSNAME) && !User::hasPermission($modelClass::getFullAccessPermission()) && $modelId) {
//                $model = $modelClass::findOne(['id' => $modelId]);
//                if ($model && $user->identity->id == $model->{$modelClass::getOwnerField()}) {
//                    return true;
//                }
//            } else {
//                return true;
//            }
//        }


        return parent::beforeAction($action);
    }

}
