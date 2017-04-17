<?php

namespace yeesoft\widgets;

use Yii;

class Breadcrumbs extends \yii\widgets\Breadcrumbs
{

    /**
     * @var string the name of the breadcrumb container tag.
     */
    public $tag = 'ol';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->homeLink = [
            'label' => 'Dashboard',
            'url' => Yii::$app->homeUrl,
        ];
    }

}
