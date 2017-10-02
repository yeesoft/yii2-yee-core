<?php

namespace yeesoft\models;

use Yii;
use yii\rbac\Rule;

/**
 * This is the model class for table "auth_rule".
 *
 * @property string $name
 * @property resource $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthPermission[] $permissions
 */
class AuthRule extends \yii\db\ActiveRecord
{

    public $className;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_rule}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['className'], 'required'],
            [['className'], 'validateClassName'],
            [['data'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'data' => 'Data',
            'className' => 'Class Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_FIND, [$this, 'readClassName']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(AuthPermission::className(), ['rule_name' => 'name']);
    }

    /**
     * @inheritdoc
     */
    public function insert($runValidation = true, $attributes = null)
    {
        $authManager = Yii::$app->authManager;
        if ($runValidation && !$this->validate($attributes)) {
            Yii::info('Model not inserted due to validation error.', __METHOD__);
            return false;
        }

        /* @var $rule Rule */
        $className = $this->className;
        $rule = new $className;
        $this->name = $rule->name;
        return $authManager->add($rule);
    }

    /**
     * @inheritdoc
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        throw new \yii\base\NotSupportedException('Rule update is not supported.');
    }

    /**
     * Rule class name validation.
     * 
     * @param string $attribute
     * @param mixed $params
     * @param \yii\validators\Validator $validator
     */
    public function validateClassName($attribute, $params, $validator)
    {
        $className = $this->$attribute;

        if (!class_exists($className)) {
            $this->addError($attribute, 'Class "' . $className . '" does not exist.');
            return;
        }

        $rule = new $className;
        if (!($rule instanceof Rule)) {
            $this->addError($attribute, 'Class "' . $className . '" must be instance of "' . Rule::class . '".');
        }
    }

    /**
     * Read Rule class name from `data` field.
     * 
     * @param \yii\base\Event $event
     */
    public function readClassName($event)
    {
        try {
            if ($rule = unserialize($this->data)) {
                $this->className = get_class($rule);
            }
        } catch (\Exception $ex) {
            //do nothing
        }
    }

}
