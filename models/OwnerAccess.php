<?php

namespace yeesoft\models;

/**
 * Interface to implement functions to check owner rights.
 */
interface OwnerAccess
{

    const CLASSNAME = 'yeesoft\models\OwnerAccess';

    /**
     * Get permission to check whether the access is denied to users who are not authors of item.
     */
    public static function getFullAccessPermission();

    /**
     * Returns name of field in the model indicating the author's id. Is used only when $ownerAccessPermission is set
     */
    public static function getOwnerField();

}
