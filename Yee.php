<?php

namespace yeesoft;

use Yii;
use yii\helpers\ArrayHelper;

class Yee extends \yii\base\Module
{
    /**
     * Version number of the module.
     */
    const VERSION = '0.1-a';

    const SESSION_LAST_ATTEMPT = '_um_last_attempt';
    const SESSION_ATTEMPT_COUNT = '_um_attempt_count';

    /**
     * If set true, then on after registration message with activation code will be sent
     * to user email and after confirmation user status will be "active"
     *
     * @var bool
     * @see $useEmailAsLogin
     */
    public $emailConfirmationRequired = true;

    /**
     * Params for mailer
     * They will be merged with $_defaultMailerOptions
     *
     * @var array
     * @see $_defaultMailerOptions
     */
    public $mailerOptions = [];

    /**
     * Default options for mailer
     *
     * @var array
     */
    protected $_defaultMailerOptions = [
        'from' => '', // If empty it will be - [Yii::$app->params['adminEmail'] => Yii::$app->name . ' robot']
        'signup-confirmation' => '/mail/signup-email-confirmation-html',
        'password-reset-mail' => '/mail/password-reset-html',
        'confirm-email' => '/mail/email-confirmation-html',

    ];

    /**
     * Permission that will be assigned automatically for everyone, so you can assign
     * routes like "site/index" to this permission and those routes will be available for everyone
     *
     * Basically it's permission for guests (and of course for everyone else)
     *
     * @var string
     */
    public $commonPermissionName = 'commonPermission';

    /**
     * After how many seconds confirmation token will be invalid
     *
     * @var int
     */
    public $confirmationTokenExpire = 3600; // 1 hour

    /**
     * Roles that will be assigned to user registered via /auth/signup
     *
     * @var array
     */
    public $rolesAfterRegistration = [];

    /**
     * Pattern that will be applied for names on registration.
     * Default pattern allows user enter only numbers and letters.
     *
     * This will not be used if $useEmailAsLogin set as true !
     *
     * @var string
     */
    public $usernameRegexp = '/^(\w|\d)+$/';

    /**
     * Pattern that will be applied for names on registration. It contain regexp that should NOT be in username
     * Default pattern doesn't allow anything having "admin"
     *
     * This will not be used if $useEmailAsLogin set as true !
     *
     * @var string
     */
    public $usernameBlackRegexp = '/^(.)*admin(.)*$/i';

    /**
     * How much attempts user can made to login or recover password in $attemptsTimeout seconds interval
     *
     * @var int
     */
    public $maxAttempts = 5;

    /**
     * Number of seconds after attempt counter to login or recover password will reset
     *
     * @var int
     */
    public $attemptsTimeout = 60;

    /**
     * Options for registration and password recovery captcha
     *
     * @var array
     */
    public $captchaOptions = [
        'class' => 'yii\captcha\CaptchaAction',
        'minLength' => 5,
        'maxLength' => 5,
        'height' => 45,
        'width' => 100,
        'fontFile' => '@vendor/yeesoft/yii2-yee-auth/fonts/lightweight.ttf',
        'padding' => 0
    ];

    /**
     * Table aliases
     *
     * @var string
     */
    public $user_table = '{{%user}}';
    public $user_visit_log_table = '{{%user_visit_log}}';
    public $auth_item_table = '{{%auth_item}}';
    public $auth_item_child_table = '{{%auth_item_child}}';
    public $auth_item_group_table = '{{%auth_item_group}}';
    public $auth_assignment_table = '{{%auth_assignment}}';
    public $auth_rule_table = '{{%auth_rule}}';
    //public $controllerNamespace   = 'yeesoft\usermanagement\controllers';

    /**
     * @p
     */
    public function init()
    {
        parent::init();

        $this->registerTranslations();
        $this->prepareMailerOptions();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['yee*'] = [
            'class' => 'yeesoft\i18n\DbMessageSource',
            'sourceLanguage' => 'en-US',
            'enableCaching' => true,
        ];
    }

    /**
     * Check how much attempts user has been made in X seconds
     *
     * @return bool
     */
    public function checkAttempts()
    {
        $lastAttempt = Yii::$app->session->get(static::SESSION_LAST_ATTEMPT);

        if ($lastAttempt) {
            $attemptsCount = Yii::$app->session->get(static::SESSION_ATTEMPT_COUNT,
                0);

            Yii::$app->session->set(static::SESSION_ATTEMPT_COUNT,
                ++$attemptsCount);

            // If last attempt was made more than X seconds ago then reset counters
            if (($lastAttempt + $this->attemptsTimeout) < time()) {
                Yii::$app->session->set(static::SESSION_LAST_ATTEMPT, time());
                Yii::$app->session->set(static::SESSION_ATTEMPT_COUNT, 1);

                return true;
            } elseif ($attemptsCount > $this->maxAttempts) {
                return false;
            }

            return true;
        }

        Yii::$app->session->set(static::SESSION_LAST_ATTEMPT, time());
        Yii::$app->session->set(static::SESSION_ATTEMPT_COUNT, 1);

        return true;
    }

    /**
     * Merge given mailer options with default
     */
    protected function prepareMailerOptions()
    {
        if (!isset($this->mailerOptions['from'])) {
            $this->mailerOptions['from'] = [Yii::$app->params['adminEmail'] => Yii::$app->name];
        }

        $this->mailerOptions = ArrayHelper::merge($this->_defaultMailerOptions, $this->mailerOptions);
    }

    /**
     * Returns an HTML hyperlink that can be displayed on your Web page.
     * @return string
     */
    public static function powered()
    {
        return '<a href="http://www.yee-soft.com/" rel="external">Yee CMS</a>';
    }
}