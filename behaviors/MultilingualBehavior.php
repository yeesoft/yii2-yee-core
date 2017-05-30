<?php

namespace yeesoft\behaviors;

use Yii;

class MultilingualBehavior extends \yeesoft\multilingual\behaviors\MultilingualBehavior
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->languages = Yii::$app->yee->languages;
        
        parent::init();
    }

}
