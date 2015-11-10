<?php

namespace yeesoft\widgets;

use yeesoft\helpers\LanguageHelper;
use yii\helpers\ArrayHelper;
use Yii;

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
        $language = Yii::$app->language;
        $languages = LanguageHelper::getLanguages();

        list($route, $params) = Yii::$app->getUrlManager()->parseRequest(Yii::$app->getRequest());
        $params = ArrayHelper::merge($_GET, $params);
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