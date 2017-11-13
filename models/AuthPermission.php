<?php

namespace yeesoft\models;

use Yii;
use yii\helpers\ArrayHelper;

class AuthPermission extends AuthItem
{

    public $groupName;

    const ITEM_TYPE = self::TYPE_PERMISSION;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['groupName', 'string']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['description', 'rule_name', 'groupName'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_FIND, [$this, 'loadGroupName']);
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'saveGroup']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'saveGroup']);
    }

    public function getGroup()
    {
        return ($groups = $this->groups) ? array_shift($groups) : null;
    }

    /**
     * @inheritdoc
     */
    public function loadGroupName()
    {
        $this->groupName = @$this->group->name;
    }

    public function saveGroup()
    {
        if ($this->groupName AND $group = AuthGroup::findOne($this->groupName)) {
            $this->unlinkAll('groups', true);
            $this->link('groups', $group);
        }
    }

    /**
     * 
     * @param array $exclude
     * @param integer $userId
     * @return array
     */
    public static function getGroupedPermissions($exclude = [], $userId = null)
    {
        $result = [];

        /* @var $authManager \yeesoft\rbac\DbManager */
        $authManager = Yii::$app->authManager;

        $query = static::find();

        if ($exclude && !empty($exclude)) {
            $query->andWhere(['not in', $authManager->itemTable . '.name', is_array($exclude) ? $exclude : [$exclude]]);
        }

        if ($userId) {
            $userPermissions = array_keys($authManager->getPermissionsByUser($userId));
            $query->andWhere([$authManager->itemTable . '.name' => $userPermissions]);
        }

        $permissions = $query->joinWith('groups')->all();

        foreach ($permissions as $permission) {
            $result[@$permission->group->title][$permission->name] = $permission->description;
        }

        return $result;
    }

}
