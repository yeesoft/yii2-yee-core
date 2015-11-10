<?php

namespace yeesoft\components;

use Yii;
use yii\web\UrlManager;

class MultilingualUrlManager extends UrlManager
{

    public function createUrl($params)
    {
        //remove incorrect language param
        if (isset($params['language']) && !isset(Yii::$app->params['languages'][$params['language']])) {
            unset($params['language']);
        }

        //trying to get language param
        if (!isset($params['language'])) {
            if (Yii::$app->session->has('language')) {
                $language = Yii::$app->session->get('language');
            } else if (isset(Yii::$app->request->cookies['language'])) {
                $language = Yii::$app->request->cookies['language']->value;
            }

            if (isset(Yii::$app->params['languages'][$language])) {
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