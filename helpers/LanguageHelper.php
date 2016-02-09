<?php

namespace yeesoft\helpers;

use Yii;
use yii\helpers\ArrayHelper;

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
     * Return true if site is multilingual.
     *
     * @return boolean
     */
    public static function isSiteMultilingual()
    {
        $languages = static::getLanguages();
        return count($languages) > 1;
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

    /**
     * @param $language
     * @return string
     */
    public static function getLangRedirect($language)
    {
        if (!isset(Yii::$app->params['languageRedirects'])) {
            return $language;
        }

        return (isset(Yii::$app->params['languageRedirects'][$language])) ?
            Yii::$app->params['languageRedirects'][$language] : $language;
    }

    /**
     * @param $language
     * @return string
     */
    public static function getLangRedirectSource($language)
    {
        if (!isset(Yii::$app->params['languageRedirects'])) {
            return $language;
        }

        $languageRedirects = array_flip(Yii::$app->params['languageRedirects']);

        return (isset($languageRedirects[$language])) ? $languageRedirects[$language] : $language;
    }

    /**
     * @return array
     */
    public static function getRedirectedLanguages()
    {
        if (!isset(Yii::$app->params['languageRedirects'])) {
            self::getLanguages();
        }

        $redirects = [];
        $languages = self::getLanguages();

        foreach ($languages as $key => $value) {
            $key = (isset(Yii::$app->params['languageRedirects'][$key])) ? Yii::$app->params['languageRedirects'][$key] : $key;
            $redirects[$key] = $value;
        }

        return $redirects;
    }

    /**
     * @return array
     */
    public static function getValidLanguages($skipReplacedLangs = true)
    {
        $keys = array_keys(self::getLanguages());

        if (isset(Yii::$app->params['languageRedirects'])) {
            $languageRedirects = Yii::$app->params['languageRedirects'];
            if ($skipReplacedLangs) {
                return array_replace($keys, array_keys($languageRedirects), array_values($languageRedirects));
            } else {
                return ArrayHelper::merge($keys, array_values($languageRedirects));
            }
        }

        return $keys;
    }
}