<?php

namespace yeesoft\components;

use yeesoft\helpers\AuthHelper;
use Yii;

/**
 * Class User
 * @package yeesoft\components
 */
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
    public $loginUrl = ['/auth/login'];

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
     * @inheritdoc
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        AuthHelper::updatePermissions($identity);

        parent::afterLogin($identity, $cookieBased, $duration);
    }

    /**
     * @inheritdoc
     */
    public function loginRequired($checkAjax = true, $checkAcceptHeader = true)
    {
        $request = Yii::$app->getRequest();
        if ($this->enableSession && (!$checkAjax || !$request->getIsAjax())) {
            $this->setReturnUrl($request->getUrl());
        }
        if ($this->loginUrl !== null) {
            $loginUrl = (array)$this->loginUrl;
            if ($loginUrl[0] !== Yii::$app->requestedRoute) {
                return Yii::$app->getResponse()->redirect(Yii::$app->urlManager->hostInfo . $this->loginUrl[0]);
            }
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'Login Required'));
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
}