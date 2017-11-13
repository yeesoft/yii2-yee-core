<?php

namespace yeesoft\web;

use Yii;
use yeesoft\helpers\Url;

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
            return true;
        }

        return parent::can($permissionName, $params, $allowCaching);
    }

    /**
     * Checks if the user can access the route.
     *
     * Note that you must configure "authManager" application component in order to use this method.
     *
     * @param string|array $route value that represent a route (e.g. `index`, `site/index`),
     * @return bool whether the user can access given route.
     */
    public function canRoute($route)
    {
        if ($this->isSuperadmin) {
            return true;
        }

//        if (Url::isFreeAccess($route)) {
//            return true;
//        }

        if (is_array($route)) {
            $rules = Yii::$app->authManager->getRouteRules();
            $action = Url::createAction($route);

            /* @var $rule \yeesoft\filters\AccessRule */
            foreach ($rules as $rule) {
                if ($allow = $rule->allows($action, $this, Yii::$app->request)) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

}
