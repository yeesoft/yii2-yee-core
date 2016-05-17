<?php
namespace yeesoft\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yeesoft\db\ActiveRecord;
use yii\helpers\Inflector;
use yii\validators\Validator;

class MultilingualSettingsBehavior extends Behavior
{
    /**
     * Multilingual attributes
     * @var array
     */
    public $attributes;

    /**
     * Available languages
     * It can be a simple array: array('fr', 'en') or an associative array: array('fr' => 'Français', 'en' => 'English')
     * For associative arrays, only the keys will be used.
     * @var array
     */
    public $languages;

    /**
     * @var string the default language.
     * Example: 'en'.
     */
    public $defaultLanguage;

    /**
     * @var string the name of the translation table
     */
    public $tableName;

    /**
     * @var string the prefix of the localized attributes in the lang table. Here to avoid collisions in queries.
     * In the translation table, the columns corresponding to the localized attributes have to be name like this: 'l_[name of the attribute]'
     * and the id column (primary key) like this : 'l_id'
     * Default to ''.
     */
    public $localizedPrefix = '';

    /**
     * @var string the name of the lang field of the translation table. Default to 'language'.
     */
    public $languageField = 'language';

    /**
     * @var boolean if this property is set to true required validators will be applied to all translation models.
     * Default to false.
     */
    public $requireTranslations = false;

    /**
     * @var boolean whether to force deletion of the associated translations when a base model is deleted.
     * Not needed if using foreign key with 'on delete cascade'.
     * Default to true.
     */
    public $forceDelete = true;

    /**
     * @var boolean whether to dynamically create translation model class.
     * If true, the translation model class will be generated on runtime with the use of the eval() function so no additional php file is needed.
     * See {@link createLangClass()}
     * Default to true.
     */
    public $dynamicLangClass = true;

    /**
     * @var boolean whether to abridge the language ID.
     * Default to true.
     */
    public $abridge = false;

    private $currentLanguage;

    private $langAttributes = [];

    /**
     * @var array excluded validators
     */
    private $excludedValidators = ['unique'];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->languages = Yii::$app->yee->languages;
        $this->defaultLanguage = Yii::$app->language;
    }

    /**
     * @inheritdoc
     *
     * @var $owner yeesoft\settings\models\BaseSettingsModel
     */
    public function attach($owner)
    {
        /** @var ActiveRecord $owner */
        parent::attach($owner);

        if (empty($this->languages) || !is_array($this->languages)) {
            throw new InvalidConfigException('Please specify array of available languages for the ' . get_class($this) . ' in the '
                . get_class($this->owner) . ' or in the application parameters', 101);
        }

        if (array_values($this->languages) !== $this->languages) { //associative array
            $this->languages = array_keys($this->languages);
        }

        $this->languages = array_unique(array_map(function ($language) {
            return $this->getLanguageSubtag($language);
        }, $this->languages));

        if (!$this->defaultLanguage) {
            $this->defaultLanguage = isset(Yii::$app->params['defaultLanguage']) && Yii::$app->params['defaultLanguage'] ?
                Yii::$app->params['defaultLanguage'] : Yii::$app->language;
        }

        $this->defaultLanguage = $this->getLanguageSubtag($this->defaultLanguage);

        if (!$this->currentLanguage) {
            $this->currentLanguage = $this->getLanguageSubtag(Yii::$app->language);
        }

        if (empty($this->attributes) || !is_array($this->attributes)) {
            throw new InvalidConfigException('Please specify multilingual attributes for the ' . get_class($this) . ' in the '
                . get_class($this->owner), 103);
        }

        $rules = $owner->rules();

        $validators = $owner->getValidators();

        foreach ($rules as $rule) {
            if (in_array($rule[1], $this->excludedValidators))
                continue;

            $rule_attributes = is_array($rule[0]) ? $rule[0] : [$rule[0]];
            $attributes = array_intersect($this->attributes, $rule_attributes);

            if (empty($attributes))
                continue;

            $rule_attributes = [];
            foreach ($attributes as $key => $attribute) {
                foreach ($this->languages as $language)
                    if ($language != $this->defaultLanguage)
                        $rule_attributes[] = $this->getAttributeName($attribute, $language);
            }

            if (isset($rule['skipOnEmpty']) && !$rule['skipOnEmpty'])
                $rule['skipOnEmpty'] = !$this->requireTranslations;

            $params = array_slice($rule, 2);

            if ($rule[1] !== 'required' || $this->requireTranslations) {
                $validators[] = Validator::createValidator($rule[1], $owner, $rule_attributes, $params);
            } elseif ($rule[1] === 'required') {
                $validators[] = Validator::createValidator('safe', $owner, $rule_attributes, $params);
            }
        }


        foreach ($this->languages as $lang) {
            foreach ($this->attributes as $attribute) {
                $attrinuteName = $this->getAttributeName($attribute, $lang);

                $owner->{$attrinuteName} = '1';

            }
        }


    }


    /**
     * Handle 'beforeValidate' event of the owner.
     */
    public function beforeValidate()
    {
        foreach ($this->attributes as $attribute) {
            $this->setLangAttribute($this->getAttributeName($attribute, $this->defaultLanguage), $this->getLangAttribute($attribute));
        }
    }

    /**
     * Handle 'afterFind' event of the owner.
     */
    public function afterFind()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        if ($owner->isRelationPopulated('translations') && $related = $owner->getRelatedRecords()['translations']) {
            $translations = $this->indexByLanguage($related);
            foreach ($this->languages as $lang) {
                foreach ($this->attributes as $attribute) {
                    foreach ($translations as $translation) {
                        if ($this->getLanguageSubtag($translation->{$this->languageField}) == $lang) {
                            $attributeName = $this->localizedPrefix . $attribute;
                            $this->setLangAttribute($this->getAttributeName($attribute, $lang), $translation->{$attributeName});

                            if ($lang == $this->defaultLanguage) {
                                $this->setLangAttribute($attribute, $translation->{$attributeName});
                            }
                        }
                    }
                }
            }
        } else {
            if (!$owner->isRelationPopulated('translation')) {
                $owner->translation;
            }

            $translation = $owner->getRelatedRecords()['translation'];
            if ($translation) {
                foreach ($this->attributes as $attribute) {
                    $attribute_name = $this->localizedPrefix . $attribute;
                    $owner->setLangAttribute($attribute, $translation->$attribute_name);
                }
            }
        }

        foreach ($this->attributes as $attribute) {
            if ($owner->hasAttribute($attribute) && $this->getLangAttribute($attribute)) {
                $owner->setAttribute($attribute, $this->getLangAttribute($attribute));
            }
        }
    }


    /**
     * Whether an attribute exists
     * @param string $name the name of the attribute
     * @return boolean
     */
    public function hasLangAttribute($name)
    {
        return array_key_exists($name, $this->langAttributes);
    }

    /**
     * @param string $name the name of the attribute
     * @return string the attribute value
     */
    public function getLangAttribute($name)
    {
        return $this->hasLangAttribute($name) ? $this->langAttributes[$name] : null;
    }

    /**
     * @param string $name the name of the attribute
     * @param string $value the value of the attribute
     */
    public function setLangAttribute($name, $value)
    {
        $this->langAttributes[$name] = $value;
    }

    /**
     * @param $records
     * @return array
     */
    protected function indexByLanguage($records)
    {
        $sorted = array();
        foreach ($records as $record) {
            $sorted[$record->{$this->languageField}] = $record;
        }
        unset($records);
        return $sorted;
    }

    /**
     * Extract language two-letter abbreviation (ISO 639-1) from language key.
     * 
     * @param $language
     * @return string
     */
    protected function getLanguageSubtag($language)
    {
        return $this->abridge ? substr($language, 0, 2) : $language;
    }

    /**
     * @param string $className
     * @return string
     */
    private function getShortClassName($className)
    {
        return substr($className, strrpos($className, '\\') + 1);
    }

    /**
     * @return mixed|string
     */
    public function getCurrentLanguage()
    {
        return $this->currentLanguage;
    }

    /**
     * @param $attribute
     * @param $language
     * @return string
     */
    protected function getAttributeName($attribute, $language)
    {
        $language = $this->abridge ? $language : Inflector::camel2id(Inflector::id2camel($language), "_");
        return $attribute . "_" . $language;
    }
}
