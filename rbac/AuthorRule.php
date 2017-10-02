<?php

namespace yeesoft\rbac;

use yii\rbac\Rule;

class AuthorRule extends Rule
{

    public $name = 'AuthorRule';

    /**
     * @param string|int $userId the user ID.
     * @param Item $item the role or permission that this rule is associated width.
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($userId, $item, $params)
    {
        return isset($params['post']) ? $params['post']->createdBy == $userId : false;
    }

}
