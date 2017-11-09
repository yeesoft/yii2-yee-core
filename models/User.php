<?php

namespace yeesoft\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yeesoft\helpers\YeeHelper;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $superadmin
 * @property string $avatar
 * @property string $first_name
 * @property string $last_name
 * @property string $birthday
 * @property integer $gender
 * @property string $phone
 * @property string $skype
 * @property string $about
 * @property string $registration_ip
 * @property string $bind_to_ip
 * @property integer $email_confirmed
 * @property string $confirmation_token
 */
class User extends UserIdentity {

    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;
    const STATUS_BANNED = -1;
    const SCENARIO_NEW_USER = 'newUser';
    const GENDER_NOT_SET = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

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
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
                [['username', 'email'], 'required'],
                ['username', 'unique'],
                ['username', 'match', 'pattern' => Yii::$app->usernameRegexp, 'message' => Yii::t('yee/auth', 'The username should contain only Latin letters, numbers and the following characters: "-" and "_".')],
                ['username', 'match', 'not' => true, 'pattern' => Yii::$app->usernameBlackRegexp, 'message' => Yii::t('yee/auth', 'Username contains not allowed characters or words.')],
                [['username', 'email', 'bind_to_ip'], 'trim'],
                [['status', 'email_confirmed', 'gender'], 'integer'],
                ['email', 'email'],
                ['email', 'validateEmailUnique'],
                ['bind_to_ip', 'validateBindToIp'],
                ['bind_to_ip', 'string', 'max' => 255],
                [['first_name', 'last_name'], 'string', 'max' => 124],
                [['skype'], 'string', 'max' => 64],
                [['phone'], 'string', 'max' => 24],
                [['bind_to_ip'], 'string', 'max' => 255],
                [['birthday'], 'safe'],
                ['password', 'required', 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
                ['password', 'string', 'max' => 255, 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
                ['password', 'string', 'min' => 6, 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
                ['password', 'trim', 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
                ['repeat_password', 'required', 'on' => [self::SCENARIO_NEW_USER, 'changePassword']],
                ['repeat_password', 'compare', 'compareAttribute' => 'password'],
                [['avatar', 'about'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('yee', 'ID'),
            'username' => Yii::t('yee', 'Login'),
            'superadmin' => Yii::t('yee', 'Superadmin'),
            'confirmation_token' => Yii::t('yee', 'Confirmation Token'),
            'registration_ip' => Yii::t('yee', 'Registration IP'),
            'bind_to_ip' => Yii::t('yee', 'Bind to IP'),
            'status' => Yii::t('yee', 'Status'),
            'gridRoleSearch' => Yii::t('yee', 'Roles'),
            'created_at' => Yii::t('yee', 'Created'),
            'updated_at' => Yii::t('yee', 'Updated'),
            'password' => Yii::t('yee', 'Password'),
            'repeat_password' => Yii::t('yee', 'Repeat password'),
            'email_confirmed' => Yii::t('yee', 'Confirmed'),
            'email' => Yii::t('yee', 'E-mail'),
            'first_name' => Yii::t('yee', 'First Name'),
            'last_name' => Yii::t('yee', 'Last Name'),
            'skype' => Yii::t('yee', 'Skype'),
            'phone' => Yii::t('yee', 'Phone'),
            'gender' => Yii::t('yee', 'Gender'),
            'birthday' => Yii::t('yee', 'Birthday'),
            'about' => Yii::t('yee', 'About'),
        ];
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find() {
        return new UserQuery(get_called_class());
    }

    /**
     * Assign roles to user
     *
     * @param int $userId
     * @param array $roles
     *
     * @return bool
     */
    public function assignRoles(array $roles) {
        /* @var $authManager \yeesoft\rbac\DbManager */
        $authManager = Yii::$app->authManager;

        foreach ($roles as $role) {
            $authManager->assign($authManager->getRole($role), $this->id);
        }
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList() {
        return array(
            self::STATUS_ACTIVE => Yii::t('yee', 'Active'),
            self::STATUS_INACTIVE => Yii::t('yee', 'Inactive'),
            self::STATUS_BANNED => Yii::t('yee', 'Banned'),
        );
    }

    /**
     * Get gender list
     * @return array
     */
    public static function getGenderList() {
        return array(
            self::GENDER_NOT_SET => Yii::t('yii', '(not set)'),
            self::GENDER_MALE => Yii::t('yee', 'Male'),
            self::GENDER_FEMALE => Yii::t('yee', 'Female'),
        );
    }

    /**
     * getUsersList
     *
     * @return array
     */
    public static function getUsersList() {
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
    public static function getStatusValue($val) {
        $ar = self::getStatusList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Check that there is no such confirmed E-mail in the system
     */
    public function validateEmailUnique() {
        if ($this->email) {
            $exists = User::findOne(['email' => $this->email]);

            if ($exists AND $exists->id != $this->id) {
                $this->addError('email', Yii::t('yee', 'This e-mail already exists'));
            }
        }
    }

    /**
     * Validate bind_to_ip attr to be in correct format
     */
    public function validateBindToIp() {
        if ($this->bind_to_ip) {
            $ips = explode(',', $this->bind_to_ip);

            foreach ($ips as $ip) {
                if (!filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                    $this->addError('bind_to_ip', Yii::t('yee', "Wrong format. Enter valid IPs separated by comma"));
                }
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles() {
        return $this->hasMany(AuthRole::className(), ['name' => 'item_name'])
                        ->viaTable(Yii::$app->authManager->assignmentTable, ['user_id' => 'id']);
    }

    /**
     * Make sure user will not deactivate himself and superadmin could not demote himself
     * Also don't let non-superadmin edit superadmin
     *
     * @inheritdoc
     */
    public function beforeSave($insert) {
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
                if (!Yii::$app->user->isSuperadmin AND $this->oldAttributes['superadmin'] == 1
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
    public function beforeDelete() {
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
     * Get created date
     *
     * @return string
     */
    public function getCreatedDate() {
        return Yii::$app->formatter->asDate(($this->isNewRecord) ? time() : $this->created_at);
    }

    /**
     * Get created date
     *
     * @return string
     */
    public function getUpdatedDate() {
        return Yii::$app->formatter->asDate(($this->isNewRecord) ? time() : $this->updated_at);
    }

    /**
     * Get created time
     *
     * @return string
     */
    public function getCreatedTime() {
        return Yii::$app->formatter->asTime(($this->isNewRecord) ? time() : $this->updated_at);
    }

    /**
     * Get created time
     *
     * @return string
     */
    public function getUpdatedTime() {
        return Yii::$app->formatter->asTime(($this->isNewRecord) ? time() : $this->updated_at);
    }

    /**
     * Get created datetime
     *
     * @return string
     */
    public function getCreatedDatetime() {
        return "{$this->createdDate} {$this->createdTime}";
    }

    /**
     * Get created datetime
     *
     * @return string
     */
    public function getUpdatedDatetime() {
        return "{$this->updatedDate} {$this->updatedTime}";
    }

    /**
     *
     * @param string $size
     * @return boolean|string
     */
    public function getAvatar($size = 'small') {
        if (!empty($this->avatar)) {
            $avatars = json_decode($this->avatar);

            if (isset($avatars->$size)) {
                return $avatars->$size;
            }
        }

        return false;
    }

    /**
     *
     * @param array $avatars
     */
    public function setAvatars($avatars) {
        $this->avatar = json_encode($avatars);
        return $this->save();
    }

    /**
     *
     */
    public function removeAvatar() {
        $this->avatar = '';
        return $this->save();
    }

}
