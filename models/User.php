<?php

namespace yeesoft\models;

use yeesoft\helpers\AuthHelper;
use yeesoft\helpers\YeeHelper;
use yeesoft\Yee;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property integer $email_confirmed
 * @property string $auth_key
 * @property string $password_hash
 * @property string $confirmation_token
 * @property string $bind_to_ip
 * @property string $registration_ip
 * @property integer $status
 * @property integer $superadmin
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends UserIdentity
{
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;
    const STATUS_BANNED = -1;

    /**
     * @var string
     */
    public $gridRoleSearch;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $repeat_password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->getModule('yee')->user_table;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'unique'],
            ['username', 'trim'],
            [['status', 'email_confirmed'], 'integer'],
            ['email', 'email'],
            ['email', 'validateEmailConfirmedUnique'],
            ['bind_to_ip', 'validateBindToIp'],
            ['bind_to_ip', 'trim'],
            ['bind_to_ip', 'string', 'max' => 255],
            ['password', 'required', 'on' => ['newUser', 'changePassword']],
            ['password', 'string', 'max' => 255, 'on' => ['newUser', 'changePassword']],
            ['password', 'trim', 'on' => ['newUser', 'changePassword']],
            ['repeat_password', 'required', 'on' => ['newUser', 'changePassword']],
            ['repeat_password', 'compare', 'compareAttribute' => 'password'],
        ];
    }
    
    /**
     * Store result in session to prevent multiple db requests with multiple calls
     *
     * @param bool $fromSession
     *
     * @return static
     */
    public static function getCurrentUser($fromSession = true)
    {
        if (!$fromSession) {
            return static::findOne(Yii::$app->user->id);
        }

        $user = Yii::$app->session->get('__currentUser');

        if (!$user) {
            $user = static::findOne(Yii::$app->user->id);

            Yii::$app->session->set('__currentUser', $user);
        }

        return $user;
    }

    /**
     * Assign role to user
     *
     * @param int $userId
     * @param string $roleName
     *
     * @return bool
     */
    public static function assignRole($userId, $roleName)
    {
        try {
            Yii::$app->db->createCommand()
                ->insert(Yii::$app->getModule('yee')->auth_assignment_table, [
                    'user_id' => $userId,
                    'item_name' => $roleName,
                    'created_at' => time(),
                ])->execute();

            AuthHelper::invalidatePermissions();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Assign roles to user
     *
     * @param int $userId
     * @param array $roles
     *
     * @return bool
     */
    public function assignRoles(array $roles)
    {
        foreach ($roles as $role) {
            User::assignRole($this->id, $role);
        }
    }

    /**
     * Revoke role from user
     *
     * @param int $userId
     * @param string $roleName
     *
     * @return bool
     */
    public static function revokeRole($userId, $roleName)
    {
        $result = Yii::$app->db->createCommand()
                ->delete(Yii::$app->getModule('yee')->auth_assignment_table, ['user_id' => $userId, 'item_name' => $roleName])
                ->execute() > 0;

        if ($result) {
            AuthHelper::invalidatePermissions();
        }

        return $result;
    }

    /**
     * @param string|array $roles
     * @param bool $superAdminAllowed
     *
     * @return bool
     */
    public static function hasRole($roles, $superAdminAllowed = true)
    {
        if ($superAdminAllowed AND Yii::$app->user->isSuperadmin) {
            return true;
        }
        $roles = (array)$roles;

        AuthHelper::ensurePermissionsUpToDate();

        return array_intersect($roles, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_ROLES, []))
        !== [];
    }

    /**
     * @param string $permission
     * @param bool $superAdminAllowed
     *
     * @return bool
     */
    public static function hasPermission($permission, $superAdminAllowed = true)
    {
        if ($superAdminAllowed AND Yii::$app->user->isSuperadmin) {
            return true;
        }

        AuthHelper::ensurePermissionsUpToDate();

        return in_array($permission, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_PERMISSIONS, []));
    }

    /**
     * Useful for Menu widget
     *
     * <example>
     *    ...
     *        [ 'label'=>'Some label', 'url'=>['/site/index'], 'visible'=>User::canRoute(['/site/index']) ]
     *    ...
     * </example>
     *
     * @param string|array $route
     * @param bool $superAdminAllowed
     *
     * @return bool
     */
    public static function canRoute($route, $superAdminAllowed = true)
    {
        if ($superAdminAllowed AND Yii::$app->user->isSuperadmin) {
            return true;
        }

        $baseRoute = AuthHelper::unifyRoute($route);

        if (substr($baseRoute, 0, 4) === "http") {
            return true;
        }

        if (Route::isFreeAccess($baseRoute)) {
            return true;
        }

        AuthHelper::ensurePermissionsUpToDate();

        return Route::isRouteAllowed($baseRoute, Yii::$app->session->get(AuthHelper::SESSION_PREFIX_ROUTES, []));
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_ACTIVE => Yee::t('yee', 'Active'),
            self::STATUS_INACTIVE => Yee::t('yee', 'Inactive'),
            self::STATUS_BANNED => Yee::t('yee', 'Banned'),
        );
    }

    /**
     * getUsersList
     *
     * @return array
     */
    public static function getUsersList()
    {
        $users = static::find()->select(['id', 'username'])->asArray()->all();
        return ArrayHelper::map($users, 'id', 'username');
    }

    /**
     * getStatusValue
     *
     * @param string $val
     *
     * @return string
     */
    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Check that there is no such confirmed E-mail in the system
     */
    public function validateEmailConfirmedUnique()
    {
        if ($this->email) {
            $exists = User::findOne([
                'email' => $this->email,
                'email_confirmed' => 1,
            ]);

            if ($exists AND $exists->id != $this->id) {
                $this->addError('email', Yee::t('yee', 'This e-mail already exists'));
            }
        }
    }

    /**
     * Validate bind_to_ip attr to be in correct format
     */
    public function validateBindToIp()
    {
        if ($this->bind_to_ip) {
            $ips = explode(',', $this->bind_to_ip);

            foreach ($ips as $ip) {
                if (!filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                    $this->addError('bind_to_ip', Yee::t('yee', "Wrong format. Enter valid IPs separated by comma"));
                }
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yee::t('yee', 'ID'),
            'username' => Yee::t('yee', 'Login'),
            'superadmin' => Yee::t('yee', 'Superadmin'),
            'confirmation_token' => Yee::t('yee', 'Confirmation Token'),
            'registration_ip' => Yee::t('yee', 'Registration IP'),
            'bind_to_ip' => Yee::t('yee', 'Bind to IP'),
            'status' => Yee::t('yee', 'Status'),
            'gridRoleSearch' => Yee::t('yee', 'Roles'),
            'created_at' => Yee::t('yee', 'Created'),
            'updated_at' => Yee::t('yee', 'Updated'),
            'password' => Yee::t('yee', 'Password'),
            'repeat_password' => Yee::t('yee', 'Repeat password'),
            'email_confirmed' => Yee::t('yee', 'E-mail confirmed'),
            'email' => Yee::t('yee', 'E-mail'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::className(), ['name' => 'item_name'])
            ->viaTable(Yii::$app->getModule('yee')->auth_assignment_table, ['user_id' => 'id']);
    }

    /**
     * Make sure user will not deactivate himself and superadmin could not demote himself
     * Also don't let non-superadmin edit superadmin
     *
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            if (php_sapi_name() != 'cli') {
                $this->registration_ip = YeeHelper::getRealIp();
            }
            $this->generateAuthKey();
        } else {
            // Console doesn't have Yii::$app->user, so we skip it for console
            if (php_sapi_name() != 'cli') {
                if (Yii::$app->user->id == $this->id) {
                    // Make sure user will not deactivate himself
                    $this->status = static::STATUS_ACTIVE;

                    // Superadmin could not demote himself
                    if (Yii::$app->user->isSuperadmin AND $this->superadmin != 1) {
                        $this->superadmin = 1;
                    }
                }

                // Don't let non-superadmin edit superadmin
                if (!Yii::$app->user->isSuperadmin AND $this->oldAttributes['superadmin']
                    == 1
                ) {
                    return false;
                }
            }
        }

        // If password has been set, than create password hash
        if ($this->password) {
            $this->setPassword($this->password);
        }

        return parent::beforeSave($insert);
    }

    /**
     * Don't let delete yourself and don't let non-superadmin delete superadmin
     *
     * @inheritdoc
     */
    public function beforeDelete()
    {
        // Console doesn't have Yii::$app->user, so we skip it for console
        if (php_sapi_name() != 'cli') {
            // Don't let delete yourself
            if (Yii::$app->user->id == $this->id) {
                return false;
            }

            // Don't let non-superadmin delete superadmin
            if (!Yii::$app->user->isSuperadmin AND $this->superadmin == 1) {
                return false;
            }
        }

        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     * @return PostQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    public function getCreatedDate()
    {
        return date(Yii::$app->settings->get('general.dateformat'), ($this->isNewRecord) ? time() : $this->created_at);
    }

    public function getCreatedDateTime()
    {
        $format = Yii::$app->settings->get('general.dateformat') . ' ' . Yii::$app->settings->get('general.timeformat');
        return date($format, ($this->isNewRecord) ? time() : $this->created_at);
    }
}