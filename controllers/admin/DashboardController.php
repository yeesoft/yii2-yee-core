<?php

namespace yeesoft\controllers\admin;

use yii\helpers\ArrayHelper;

class DashboardController extends BaseController
{
    /**
     * @inheritdoc
     */
    public $enableOnlyActions = ['index'];
    public $widgets = NULL;

    public function actions()
    {

        if ($this->widgets === NULL) {
            $this->widgets = [
                'yeesoft\comment\widgets\dashboard\Comments',
                [
                    'class' => 'yeesoft\widgets\dashboard\Info',
                    'position' => 'right',
                ],

                [
                    'class' => 'yeesoft\media\widgets\dashboard\Media',
                    'position' => 'right',
                ],
                'yeesoft\post\widgets\dashboard\Posts',
                'yeesoft\user\widgets\dashboard\Users',
            ];
        }

        return ArrayHelper::merge(parent::actions(), [
            'index' => [
                'class' => 'yeesoft\web\DashboardAction',
                'widgets' => $this->widgets,
            ]
        ]);
    }
}