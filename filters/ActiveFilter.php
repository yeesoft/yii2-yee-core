<?php

namespace yeesoft\filters;

use Yii;
use yii\base\Object;

abstract class ActiveFilter extends Object implements ActiveFilterInterface
{

    /**
     * Applies [[andWhere]] filters to the $query using condition from [[getCondition]].
     * 
     * @param \yeesoft\db\ActiveQuery $query
     */
    public function apply(&$query)
    {
        $tableName = $this->getTableNameFromQuery($query);
        $query->andWhere($this->getCondition($tableName));
    }

    protected function getTableNameFromQuery($query)
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $query->modelClass;
        $tableName = $modelClass::tableName();

        // Looking for the table alias in $query
        if (!empty($query->from)) {
            if ($alias = array_search($tableName, $query->from)) {
                return $alias;
            }

            $rawTableName = Yii::$app->db->schema->getRawTableName($tableName);
            if ($alias = array_search($rawTableName, $query->from)) {
                return $alias;
            }
        }

        return $tableName;
    }

    /**
     * 
     * @param string $tableName the name of the table of ActiveRecord class.
     * @return string|array|\yii\db\Expression the WHERE condition 
     * that will be applied to the ActiveQuery.
     */
    abstract public function getCondition($tableName);
}
