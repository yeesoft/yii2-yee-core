<?php

namespace yeesoft\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\caching\Cache;

/**
 * This component allows you to get and set settings from application.
 *
 * Usage examples:
 *
 * Set setting:
 * ~~~
 * Yii::$app->settings->set(['general','title'],'Yee Site');
 * ~~~
 *
 * Get setting:
 * ~~~
 * $setting = Yii::$app->settings->get('general.title');
 * ~~~
 *
 * @author Taras Makitra <makitrataras@gmail.com>
 */
class Settings extends Component
{
    /**
     * @var string Setting model. Make sure your settings model calls clearCache in the afterSave callback
     */
    public $modelClass = 'yeesoft\models\Setting';

    /**
     * @var Cache|string the cache object or the application component ID of the cache object.
     * Settings will be cached through this cache object, if it is available.
     *
     * Set this property to null if you do not want to cache the settings.
     */
    public $cache = 'cache';

    /**
     * Used by the cache component.
     *
     * @var string cache key
     */
    public $cacheKey = 'settings';

    /**
     * Holds a cached copy of the data for the current request
     *
     * @var mixed
     */
    private $_data = [];

    /**
     * Initialize the component
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (is_string($this->cache)) {
            $this->cache = Yii::$app->get($this->cache, false);
        }
    }

    /**
     * Get setting value for the given key and group.
     * You can use dot notation to separate the group from the key:
     * ~~~
     * $value = $settings->get('group.key');
     * ~~~
     *
     * or array:
     *  ~~~
     * $value = $settings->get(['group', 'key']);
     * ~~~
     *
     * @param array|string $key
     * @param null $default Default value
     * @return mixed
     */
    public function get($key, $default = NULL, $language = NULL)
    {
        $key = self::explodeKey($key);

        $group = $key[0];
        $key = $key[1];

        if (!isset($this->_data[$group][$key][$language])) {
            $this->load($group, $key, $language);
        }

        return (isset($this->_data[$group][$key][$language])) ? $this->_data[$group][$key][$language] : $default;
    }

    /**
     * Set setting value for the given key and group or create new setting field if it not exists.
     * You can use dot notation to separate the group from the key:
     * ~~~
     * $value = $settings->get('group.key');
     * ~~~
     *
     * or array:
     *  ~~~
     * $value = $settings->get(['group', 'key']);
     * ~~~
     *
     * @param $key - setting key
     * @param $value - setting new value
     * @param $description - description field in order of creating new setting field
     * @return boolean
     */
    public function set($key, $value, $description = NULL)
    {
        $model = $this->modelClass;
        $key = self::explodeKey($key);

        $language = isset($key[2]) ? $key[2] : NULL;
        $group = $key[0];
        $key = $key[1];

        $setting = $model::getSetting($group, $key, $language);

        if ($setting) {
            $setting->value = $value;
            $setting->save();
            unset($this->_data[$group][$key][$language]);
        } else {
            $setting = new $model();
            $setting->group = $group;
            $setting->key = $key;
            $setting->value = $value;
            $setting->language = $language;
            $setting->description = $description;
            $setting->save();
        }

        if ($this->cache instanceof Cache) {
            $this->cache->flush();
        }
    }

    /**
     * Get setting value from datebase for the given key and group.
     *
     * @param type $group Setting group
     * @param type $key Setting key
     * @param type $default Default value
     * @return mixed
     */
    public function getFromDB($group, $key, $language = NULL, $default = NULL)
    {
        $model = $this->modelClass;
        $setting = $model::getSetting($group, $key, $language);
        return ($setting) ? $setting->value : $default;
    }

    /**
     * Load setting from cache or from database if not found in cache.
     *
     * @param string $group Setting group
     * @param string $key Setting key
     */
    protected function load($group, $key, $language = NULL)
    {
        $value = NULL;

        if ($this->cache instanceof Cache) {
            $value = $this->cache->get($this->cacheKey . $group . $key . $language);

            if (!$value) {
                $value = $this->getFromDB($group, $key, $language);
                $this->cache->set($this->cacheKey . $group . $key . $language, $value);
            }
        } else {
            $value = $this->getFromDB($group, $key, $language);
        }

        if ($value !== NULL) {
            $this->_data[$group][$key][$language] = $value;
        }
    }

    /**
     * Explode setting key.
     *
     * @param type $key
     * @throws \yii\base\InvalidParamException
     */
    public static function explodeKey($key)
    {
        if (is_string($key)) {
            $array = explode('.', $key);
            if (count($array) == 2 || count($array) == 3) {
                return $array;
            } else {
                throw new InvalidParamException();
            }
        }
        if (is_array($key) && (count($key) == 2 || count($key) == 3)) {
            return $key;
        }

        throw new InvalidParamException();
    }
}