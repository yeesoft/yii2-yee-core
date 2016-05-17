<?php

namespace yeesoft\web;

use Yii;
use yii\web\UrlManager;

class MultilingualUrlManager extends UrlManager
{
    public $multilingualRules = [];

    public $nonMultilingualUrls = [];

    public $languagePattern = '<language:([a-zA-Z-]{2,5})?>';

    /**
     * Initializes UrlManager.
     */
    public function init()
    {

        if (!$this->enablePrettyUrl || (empty($this->rules) && empty($this->multilingualRules))) {
            return;
        }
        if (is_string($this->cache)) {
            $this->cache = Yii::$app->get($this->cache, false);
        }
        if ($this->cache instanceof Cache) {
            $cacheKey = __CLASS__;
            $hash = md5(json_encode($this->rules));
            if (($data = $this->cache->get($cacheKey)) !== false && isset($data[1])
                && $data[1] === $hash
            ) {
                $this->rules = $data[0];
            } else {
                $this->rules = (!empty($this->multilingualRules)) ? $this->getMergedRules() : $this->rules;
                $this->rules = $this->buildRules($this->rules);
                $this->cache->set($cacheKey, [$this->rules, $hash]);
            }
        } else {
            $this->rules = (!empty($this->multilingualRules)) ? $this->getMergedRules() : $this->rules;
            $this->rules = $this->buildRules($this->rules);
        }
    }

    public function createUrl($params)
    {
        if ((isset($params['language']) && $params['language'] === false)
            || (isset($params[0]) && in_array($params[0], $this->nonMultilingualUrls))
        ) {
            unset($params['language']);
            return parent::createUrl($params);
        }

        if (isset($params[0]) && $params[0] === 'auth/default/oauth' && !empty($params['language'])) {
            print_r($params);
            die;

        }


        if (Yii::$app->yee->isMultilingual && !empty($this->multilingualRules)) {
            $languages = array_keys(Yii::$app->yee->displayLanguages);

            //remove incorrect language param
            if (isset($params['language']) && !in_array($params['language'], $languages)) {
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

                if (in_array($language, $languages)) {
                    Yii::$app->language = $language;
                }

                $params['language'] = Yii::$app->yee->getDisplayLanguageShortcode(Yii::$app->language);
            }
        }

        return parent::createUrl($params);
    }

    public function getMergedRules()
    {
        $rules = $this->rules;
        $prefix = Yii::$app->yee->isMultilingual ? $this->languagePattern . '/' : '';

        foreach ($this->multilingualRules as $pattern => $route) {
            $multilingualPattern = $prefix . ltrim($pattern, '/');
            $rules[$multilingualPattern] = $route;
        }

        return $rules;
    }
}