<?php

namespace yeesoft\widgets;

use yeesoft\helpers\LanguageHelper;
use Yii;
use yii\helpers\ArrayHelper;

class LanguageSelector extends \yii\base\Widget
{
    /**
     *
     * @var string  links | pills
     */
    public $view = 'links';

    /**
     *
     * @var string  code | title
     */
    public $display = 'code';

    public function run()
    {
        if (!LanguageHelper::isSiteMultilingual()) {
            return;
        }

        $language = Yii::$app->language;
        $languages = LanguageHelper::getRedirectedLanguages();

        list($route, $params) = Yii::$app->getUrlManager()->parseRequest(Yii::$app->getRequest());
        $params = ArrayHelper::merge(Yii::$app->getRequest()->get(), $params);
        $url = isset($params['route']) ? $params['route'] : $route;

        return $this->render("language-selector/{$this->view}", [
            'language' => $language,
            'languages' => $languages,
            'url' => $url,
            'params' => $params,
            'display' => $this->display,
        ]);
    }
}