<?php

namespace yeesoft\models;

use Yii;
use Exception;
use yii\rbac\DbManager;
use yii\helpers\ArrayHelper;

class AuthRole extends AuthItem
{

    const ITEM_TYPE = self::TYPE_ROLE;

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilters()
    {
        return $this->hasMany(AuthFilter::className(), ['name' => 'filter_name'])
                        ->viaTable(Yii::$app->authManager->itemFilterTable, ['item_name' => 'name']);
    }

    /**
     * Get permissions assigned to this role or its children
     *
     * @param string $roleName
     * @param bool $asArray
     *
     * @return array|Permission[]
     */
    public static function getPermissionsByRole($roleName, $asArray = true)
    {
        $rbacPermissions = (new DbManager())->getPermissionsByRole($roleName);

        $permissionNames = ArrayHelper::map($rbacPermissions, 'name', 'description');

        return $asArray ? $permissionNames : Permission::find()->andWhere(['name' => array_keys($permissionNames)])->all();
    }

    /**
     * Return only roles, that are assigned to the current user.
     * Return all if superadmin
     * Useful for forms where user can give roles to another users, but we restrict him only with roles he possess
     *
     * @param bool $showAll
     * @param bool $asArray
     *
     * @return static[]
     */
    public static function getAvailableRoles($showAll = false, $asArray = false)
    {
        $condition = (Yii::$app->user->isSuperAdmin OR $showAll) ? [] : ['name' => []];

        $result = static::find()->andWhere($condition)->all();

        return $asArray ? ArrayHelper::map($result, 'name', 'name') : $result;
    }

    /**
     * 
     * @param array $exclude
     * @return array
     */
    public static function getRoles($exclude = [])
    {
        $items = static::find()->andWhere(['not in', Yii::$app->authManager->itemTable . '.name', is_array($exclude) ? $exclude : [$exclude]])->all();
        return ArrayHelper::map($items, 'name', 'description');
    }


}
