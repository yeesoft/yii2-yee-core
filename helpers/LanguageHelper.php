<?php

namespace yeesoft\helpers;

use Yii;

/**
 * Language helper
 *
 */
class LanguageHelper
{

    /**
     * Check is is model multilingual
     *
     * @param ActiveRecord $model
     * @return boolean
     */
    public static function isMultilingual($model)
    {
        return ($model->getBehavior('multilingual') !== NULL);
    }

    /**
     * Return list of languages for model.
     * If model is multilingual, list of languages will be returned.
     * If model is not multilingual, array with default language will be returned.
     *
     * @param ActiveRecord $model
     * @return array
     */
    public static function getModelLanguages($model)
    {
        if (self::isMultilingual($model)) {
            return self::getLanguages();
        } else {
            $defaultLanguage = Yii::$app->language;
            return [$defaultLanguage => $defaultLanguage];
        }
    }

    /**
     * Return list of supported languages.
     *
     * @return array
     */
    public static function getLanguages()
    {
        if (!isset(Yii::$app->params['languages'])) {
            return [Yii::$app->language => Yii::t('yee', 'Default Language')];
        }

        return Yii::$app->params['languages'];
    }

    /**
     * @param $language
     * @return string
     */
    public static function getLanguageBaseName($language)
    {
        return substr($language, 0, 2);
    }
}