<?php

namespace yeesoft\web;

use Yii;

class User extends \yii\web\User
{

    /**
     * @inheritdoc
     */
    public $identityClass = 'yeesoft\models\User';

    /**
     * @inheritdoc
     */
    public $settingsClass = 'yeesoft\models\UserSetting';

    /**
     * Settings identity
     *
     * @var mixed
     */
    private $_settings = false;

    /**
     * @inheritdoc
     */
    public $enableAutoLogin = true;

    /**
     * @inheritdoc
     */
    public $loginUrl = ['/auth/default/login'];

    /**
     * Allows to call Yii::$app->user->isSuperadmin
     *
     * @return bool
     */
    public function getIsSuperadmin()
    {
        return @Yii::$app->user->identity->superadmin == 1;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return @Yii::$app->user->identity->username;
    }

    /**
     * Returns the settings identity object associated with the currently logged-in user.
     */
    public function getSettings($autoRenew = true)
    {
        if ($this->_settings === false) {
            if ($autoRenew) {
                $this->_settings = null;
                $this->renewSettings();
            } else {
                return null;
            }
        }

        return $this->_settings;
    }

    /**
     * Sets the user's settings identity object.
     */
    public function setSettings($identity)
    {
        if ($identity instanceof $this->settingsClass) {
            $this->_settings = $identity;
        } elseif ($identity === null) {
            $this->_settings = null;
        } else {
            throw new InvalidValueException("The identity object must implement {$this->settingsClass}.");
        }
    }

    protected function renewSettings()
    {
        $userId = Yii::$app->user->id;
        if ($userId === null) {
            $settings = null;
        } else {
            $class = $this->settingsClass;
            $settings = new $class;
        }

        $this->setSettings($settings);
    }
    
    /**
     * @inheritdoc
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if ($this->isSuperadmin) {
           // return true;
        }
        
        return parent::can($permissionName, $params, $allowCaching);
    }
    
    public function canRoute($url, $superAdminAllowed = true) {
        return true;
        
//        if ($superAdminAllowed AND Yii::$app->user->isSuperadmin) {
//            return true;
//        }

        if (is_array($url)) {
            $request = Yii::$app->request;
            $rules = Yii::$app->authManager->getRouteRules();
            $action = \yeesoft\helpers\Html::getActionByRoute($url);

            /* @var $rule AccessRule */
            foreach ($rules as $rule) {
                if ($allow = $rule->allows($action, $this->identity, $request)) {
                    return true;
                }
            }

            return false;
        }


        //return true;
//        if (substr($baseRoute, 0, 4) === "http") {
//            return true;
//        }
//
//        if (Route::isFreeAccess($baseRoute)) {
//            return true;
//        }
//
//
//        return Route::isRouteAllowed($baseRoute);
    }

}
