<?php

namespace yeesoft\components;

use Yii;
use yii\web\UrlManager;

class MultilingualUrlManager extends UrlManager
{

    public function createUrl($params)
    {
        if (!isset($params['language'])) {
            if (Yii::$app->session->has('language')) {
                Yii::$app->language = Yii::$app->session->get('language');
            } else if (isset(Yii::$app->request->cookies['language'])) {
                Yii::$app->language = Yii::$app->request->cookies['language']->value;
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