<?php

namespace yeesoft\filters;

class AccessRule extends \yii\filters\AccessRule
{

    /**
     * @inheritdoc
     */
    public function allows($action, $user, $request)
    {
        if ($this->matchAction($action)
            && $this->matchRole($user)
            && $this->matchIP($request->getUserIP())
            && $this->matchVerb($request->getMethod())
            && $this->matchController($action->controller)
            && $this->matchCustom($action)
        ) {
            return $this->allow ? true : false;
        } else {
            return null;
        }
    }
}
