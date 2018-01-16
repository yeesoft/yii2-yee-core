<?php

namespace yeesoft\data;

use yii\db\QueryInterface;
use yii\base\InvalidConfigException;

/**
 * @inheritdoc
 */
class ActiveDataProvider extends \yii\data\ActiveDataProvider {

    /**
     * @inheritdoc
     */
    protected function prepareTotalCount() {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }
        $query = clone $this->query;

        if (is_array($query->joinWith)) {
            foreach ($query->joinWith as $key => $join) {
                if ($join[0][0] === 'translations') {
                    unset($query->joinWith[$key]);
                }
            }
        }

        return (int) $query->limit(-1)->offset(-1)->orderBy([])->count('*', $this->db);
    }

}
