<?php

namespace yeesoft\controllers;

use yeesoft\behaviors\AccessFilter;
use Yii;
use yii\web\Controller;

abstract class BaseController extends Controller
{

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access-filter' => [
                'class' => AccessFilter::className(),
            ],
        ];
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Render ajax or usual depends on request
     *
     * @param string $view
     * @param array $params
     *
     * @return string|\yii\web\Response
     */
    protected function renderIsAjax($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($view, $params);
        } else {
            return $this->render($view, $params);
        }
    }
}