<?php

/**
 * @link http://www.yee-soft.com/
 * @copyright Copyright (c) 2015 Taras Makitra
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace yeesoft;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\base\Component;

/**
 * YeeCMS component. Contains basic settings and functions of YeeCMS.
 */
class Yee extends Component
{

    /**
     * Version number of the component.
     */
    const VERSION = '0.1.0';

    /**
     * Session ID of last login attempt.
     */
    const SESSION_LAST_ATTEMPT_TIME = '_last_attempt';

    /**
     * Session ID of login attempts count.
     */
    const SESSION_ATTEMPTS_COUNT = '_attempt_count';

    /**
     * Indicates whether it is required to confirm email after registration.
     * User's account status will be set to "active" after user confirms his email.
     *
     * @var boolean
     */
    public $emailConfirmationRequired = true;

    /**
     * Default email FROM sender. If empty it will be set to
     * `[Yii::$app->params['adminEmail'] => Yii::$app->name]`
     *
     * @var string
     */
    public $emailSender;

    /**
     * Email templates. These settings will be merged with `$_defaultEmailTemplates`.
     *
     * @var array
     * @see $_defaultEmailTemplates
     */
    public $emailTemplates = [];

    /**
     * Permission that will be assigned automatically for everyone. You can assign
     * routes like "site/index" to this permission and those routes will be 
     * available for everyone.
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
     * Roles that will be assigned to user after registration.
     *
     * @var array
     */
    public $defaultRoles = [];

    /**
     * Pattern that will be used to validate usernames on registration. Default 
     * pattern allows only numbers and letters.
     *
     * @var string
     */
    public $usernameRegexp = '/^(\w|\d)+$/';

    /**
     * Pattern that describe what names should not be allowed for username on 
     * registration. Default pattern does not allow anything having "admin".
     *
     * @var string
     */
    public $usernameBlackRegexp = '/^(.)*admin(.)*$/i';

    /**
     * List of languages used in application.
     *
     * @var array
     */
    public $languages = ['en-US' => 'English'];

    /**
     * List of language slug redirects. You can use this parameter to redirect
     * language slug to another slug. For example `en-US` to `en`.
     *
     * @var array
     */
    public $languageRedirects = ['en-US' => 'en'];

    /**
     * How much attempts user can made to login, update or recover password
     * in `$attemptsTimeout` seconds interval.
     *
     * @var int
     */
    public $maxAttempts = 5;

    /**
     * Number of seconds after attempt counter to login, update or recover 
     * password will reset.
     *
     * @var int
     */
    public $attemptsTimeout = 60;

    /**
     * Captcha action options. Used for registration and password recovery.
     *
     * @var array
     */
    public $captchaAction = [
        'class' => 'yii\captcha\CaptchaAction',
        'minLength' => 5,
        'maxLength' => 5,
        'height' => 45,
        'width' => 100,
        'padding' => 0
    ];

    /**
     * User table alias.
     * 
     * @var string 
     */
    public $user_table = '{{%user}}';

    /**
     * User visit log table alias.
     * 
     * @var string 
     */
    public $user_visit_log_table = '{{%user_visit_log}}';

    /**
     * Auth item table alias.
     * 
     * @var string 
     */
    public $auth_item_table = '{{%auth_item}}';

    /**
     * Auth item child table alias.
     * 
     * @var string 
     */
    public $auth_item_child_table = '{{%auth_item_child}}';

    /**
     * Auth item group table alias.
     * 
     * @var string 
     */
    public $auth_item_group_table = '{{%auth_item_group}}';

    /**
     * Auth assignment table alias.
     * 
     * @var string 
     */
    public $auth_assignment_table = '{{%auth_assignment}}';

    /**
     * Auth rule table alias.
     * 
     * @var string 
     */
    public $auth_rule_table = '{{%auth_rule}}';

    /**
     * List of languages used in frontend rules. Contains the same values as
     * `$languages` but keys is replaced with `$languageRedirects`.
     * 
     * @var array 
     */
    protected $_displayLanguages;

    /**
     * Default email templates.
     *
     * @var array
     */
    protected $_defaultEmailTemplates = [
        'signup-confirmation' => '/mail/signup-email-confirmation-html',
        'password-reset' => '/mail/password-reset-html',
        'confirm-email' => '/mail/email-confirmation-html',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (Yii::$app->id != 'console') {
            $this->registerTranslations();
            $this->initLanguageOptions();
            $this->initEmailOptions();
            $this->initFormatter();
        }
    }

    /**
     * Register YeeCMS DB message translations.
     */
    protected function registerTranslations()
    {
        Yii::$app->i18n->translations['yee*'] = [
            'class' => 'yeesoft\db\DbMessageSource',
            'sourceLanguage' => 'en-US',
            'enableCaching' => true,
        ];
    }

    /**
     * Prepare mailer options. Merge given email templates options with default.
     */
    protected function initLanguageOptions()
    {
        if (empty($this->languages) || !is_array($this->languages)) {
            $this->languages[Yii::$app->language] = Yii::t('yee', 'Default Language');
        }

        if (!in_array(Yii::$app->language, array_keys($this->languages))) {
            throw new InvalidConfigException('Invalid language settings! Default application language should be included into `yeesoft\Yee::$languages` setting.');
        }
        
        if(!empty(array_diff(array_keys($this->languageRedirects), array_keys($this->languages)))){
            throw new InvalidConfigException('Invalid language redirects settings!');
        }
    }

    /**
     * Prepare mailer options. Merge given email templates options with default.
     */
    protected function initEmailOptions()
    {
        if (empty($this->emailSender)) {
            $this->emailSender = [Yii::$app->params['adminEmail'] => Yii::$app->name];
        }

        $this->emailTemplates = ArrayHelper::merge($this->_defaultEmailTemplates, $this->emailTemplates);
    }

    /**
     * Updates formatter to display date and time correcty.
     */
    protected function initFormatter()
    {
        date_default_timezone_set(Yii::$app->settings->get('general.timezone', 'UTC'));
        Yii::$app->formatter->timeZone = Yii::$app->settings->get('general.timezone', 'UTC');
        Yii::$app->formatter->dateFormat = Yii::$app->settings->get('general.dateformat', "yyyy-MM-dd");
        Yii::$app->formatter->timeFormat = Yii::$app->settings->get('general.timeformat', "HH:mm");
        Yii::$app->formatter->datetimeFormat = Yii::$app->formatter->dateFormat . " " . Yii::$app->formatter->timeFormat;
    }

    /**
     * Return true if site is multilingual.
     *
     * @return boolean
     */
    public function getIsMultilingual()
    {
        $languages = Yii::$app->yee->languages;
        return count($languages) > 1;
    }

    /**
     * Returns language shortcode that will be displayed on frontend.
     * 
     * @param string $language
     * @return string
     */
    public function getDisplayLanguageShortcode($language)
    {
        return (isset($this->languageRedirects[$language])) ? $this->languageRedirects[$language] : $language;
    }

    /**
     * Returns original language shortcode from its redirect.
     * 
     * @param string $language
     * @return string
     */
    public function getSourceLanguageShortcode($language)
    {
        if (!isset($this->languageRedirects)) {
            return $language;
        }

        $languageRedirects = array_flip(Yii::$app->yee->languageRedirects);

        return (isset($languageRedirects[$language])) ? $languageRedirects[$language] : $language;
    }

    /**
     * Returns list of languages used in frontend rules. Contains the same values 
     * as `$languages` but keys is replaced with `$languageRedirects`.
     * 
     * @return array
     */
    public function getDisplayLanguages()
    {
        if (!isset($this->_displayLanguages)) {
            foreach ($this->languages as $key => $value) {
                $key = (isset($this->languageRedirects[$key])) ? $this->languageRedirects[$key] : $key;
                $redirects[$key] = $value;
            }

            $this->_displayLanguages = $redirects;
        }
        return $this->_displayLanguages;
    }

    /**
     * Check how much attempts to login, reset or update password user has been
     * made in `$attemptsTimeout` seconds.
     *
     * @return boolean
     */
    public function checkAttempts()
    {
        $lastAttemptTime = Yii::$app->session->get(static::SESSION_LAST_ATTEMPT_TIME);

        if ($lastAttemptTime) {
            $attemptsCount = Yii::$app->session->get(static::SESSION_ATTEMPTS_COUNT, 0);

            Yii::$app->session->set(static::SESSION_ATTEMPTS_COUNT, ++$attemptsCount);

            // If last attempt was made more than X seconds ago then reset counters
            if (($lastAttemptTime + $this->attemptsTimeout) < time()) {
                Yii::$app->session->set(static::SESSION_LAST_ATTEMPT_TIME, time());
                Yii::$app->session->set(static::SESSION_ATTEMPTS_COUNT, 1);

                return true;
            } elseif ($attemptsCount > $this->maxAttempts) {
                return false;
            }

            return true;
        }

        Yii::$app->session->set(static::SESSION_LAST_ATTEMPT_TIME, time());
        Yii::$app->session->set(static::SESSION_ATTEMPTS_COUNT, 1);

        return true;
    }

    /**
     * Returns an HTML hyperlink that can be displayed on your Web page.
     * 
     * @return string
     */
    public static function powered()
    {
        return '<a href="http://www.yee-soft.com/" rel="external">Yee CMS</a>';
    }

    /**
     * Returns a string representing the current version of the Yee CMS Core.
     * 
     * @return string the version of Yee CMS Core
     */
    public static function getVersion()
    {
        return self::VERSION;
    }

}
