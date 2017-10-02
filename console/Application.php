<?php

namespace yeesoft\console;

class Application extends \yii\console\Application
{

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

}
