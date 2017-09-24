<?php

/**
 * @link http://www.yee-soft.com/
 * @copyright Copyright (c) 2015 Taras Makitra
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace yeesoft;

/**
 * YeeCMS component. Contains basic settings and functions of YeeCMS.
 */
class Yee extends yii\base\Component
{

    /**
     * Version number of the component.
     */
    const VERSION = '0.2.x';

    /**
     * Returns an HTML hyperlink that can be displayed on your Web page.
     * 
     * @return string
     */
    public static function powered()
    {
        return '<a href="https://www.yee-soft.com/" rel="external">Yee CMS</a>';
    }

    /**
     * Returns a string representing the current version of the Yee CMS Core.
     * 
     * @return string the version of Yee CMS Core
     */
    public static function getVersion()
    {
        return self::VERSION;
    }

}
