<?php

namespace yeesoft\behaviors;

use omgdef\multilingual\MultilingualBehavior as OriginalMultilingualBehavior;
use yeesoft\helpers\LanguageHelper;
use Yii;

class MultilingualBehavior extends OriginalMultilingualBehavior
{

    /**
     * @inheritdoc
     */
    public $requireTranslations = true;

    /**
     * @inheritdoc
     */
    public $abridge = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->languages = LanguageHelper::getLanguages();
        $this->defaultLanguage = Yii::$app->language;
    }

}