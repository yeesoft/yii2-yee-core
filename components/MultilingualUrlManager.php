<?php

namespace yeesoft\components;

use yeesoft\helpers\LanguageHelper;
use Yii;
use yii\web\UrlManager;

class MultilingualUrlManager extends UrlManager
{

    public function createUrl($params)
    {
        $languages = LanguageHelper::getLanguages();
        //remove incorrect language param
        if (isset($params['language']) && !isset($languages[$params['language']])) {
            unset($params['language']);
        }

        //trying to get language param
        if (!isset($params['language'])) {
            if (Yii::$app->session->has('language')) {
                $language = Yii::$app->session->get('language');
            } elseif (isset(Yii::$app->request->cookies['language'])) {
                $language = Yii::$app->request->cookies['language']->value;
            } else {
                $language = Yii::$app->language;
            }

            if (isset($languages[$language])) {
                Yii::$app->language = $language;
            }

            $params['language'] = Yii::$app->language;
        }

        return parent::createUrl($params);

        /*$url = parent::createUrl($params);

        if( $url == '/' ){
            return '/'.Yii::$app->language;
        }else{
            return '/'.Yii::$app->language.$url;
        }*/

    }
}