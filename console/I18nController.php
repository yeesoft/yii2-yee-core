<?php

namespace yeesoft\console;

use Yii;
use yii\db\Query;
use yii\helpers\Console;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 *  @property \yii\db\Connection $db The database connection. This property is read-only.
 */
class I18nController extends Controller
{

    const TABLE_MESSAGE_TRANSLATIONS = '{{%message}}';
    const TABLE_MESSAGE_SOURCE = '{{%message_source}}';
    const TABLE_MENU_TRANSLATIONS = '{{%menu_link_lang}}';
    const TABLE_MENU_LINK = '{{%menu_link}}';
    const MODE_SOFT = 'soft';
    const MODE_HARD = 'hard';
    const MODE_UPDATE = 'update';

    /**
     * @var string the default directory storing the translation files. This can be either
     * a path alias or a directory.
     */
    public $path = '@common/i18n';

    /**
     * @var array additional aliases of translation file directories
     */
    public $lookup = [];

    /**
     * @var string the mode of update. Possible modes:
     * soft - adds new translations only (default);
     * update - rewrites modified translations and adds new;
     * hard - clears translation category and creates translations from scratch;
     */
    public $mode = 'soft';

    /**
     * @var string translation language. If not set only source messages will be added. 
     */
    public $language;

    /**
     * 
     * @return \yii\db\Connection
     */
    public function getDb()
    {
        return Yii::$app->db;
    }

    /**
     * 
     * @inheritdoc
     */
    public function options($actionID)
    {
        return ['mode', 'path', 'language', 'lookup'];
    }

    /**
     * 
     * @inheritdoc
     */
    public function optionAliases()
    {
        return ['m' => 'mode', 'p' => 'path', 'l' => 'language'];
    }

    /**
     * Upgrades the application i18n by applying translation files.
     * For example,
     *
     * ~~~
     * yii i18n --lookup=@yeesoft    # apply Yee CMS source messages
     * yii i18n --lookup=@yeesoft --language=de   # apply Yee CMS German translations
     * yii i18n --lookup=@yeesoft/yii2-yee-page/i18n --language=de # apply Page module German translations
     * yii i18n --lookup=@yeesoft --language=de --mode=hard   # apply Yee CMS German translations in hard mode
     * ~~~
     *
     * applying all available translation.
     */
    public function actionIndex()
    {
        $files = $this->findFiles();

        $list = '';
        $count = 0;

        if (isset($files['source']) && is_array($files['source'])) {
            foreach ($files['source'] as $alias) {
                $list .= "*** {$alias}" . PHP_EOL;
                $count++;
            }
        }

        if (isset($files['translation']) && is_array($files['translation'])) {
            foreach ($files['translation'] as $alias) {
                $list .= "*** {$alias}" . PHP_EOL;
                $count++;
            }
        }

        if (isset($files['menu']) && is_array($files['menu'])) {
            foreach ($files['menu'] as $alias) {
                $list .= "*** {$alias}" . PHP_EOL;
                $count++;
            }
        }

        if ($count) {
            echo $this->ansiFormat("You are going to apply {$count} message translations in '{$this->mode}' mode from files: " . PHP_EOL, Console::FG_BLUE, Console::BOLD);

            echo $list . PHP_EOL;

            if ($this->confirm('Are you sure you want to continue?')) {
                $this->update($files);
                $this->ansiFormat(PHP_EOL . "Translations applied successfully." . PHP_EOL, Console::FG_GREEN, Console::BOLD);
            }
        } else {
            echo $this->ansiFormat("No translations found." . PHP_EOL, Console::FG_BLUE, Console::BOLD);
        }
    }

    /**
     * Update message translation with the specified list of translation files.
     *
     * @param array $files the list of translation files.
     *
     * @return boolean whether the update is successful
     */
    protected function update($files)
    {
        $start = microtime(true);

        //Source Messages
        if (isset($files['source']) && is_array($files['source'])) {
            echo $this->ansiFormat("Source Messages:" . PHP_EOL, Console::FG_GREEN, Console::BOLD);

            foreach ($files['source'] as $alias) {
                echo "*** Applying {$alias}" . PHP_EOL;
                $this->sourceUp(require(Yii::getAlias($alias)));
            }

            $time = microtime(true) - $start;
            echo "*** Source Messages Applied (time: " . sprintf("%.3f", $time) . "s)" . PHP_EOL . PHP_EOL;
        }

        //Message Translations
        if (isset($files['translation']) && is_array($files['translation'])) {
            echo $this->ansiFormat("Message Translations:" . PHP_EOL, Console::FG_GREEN, Console::BOLD);

            foreach ($files['translation'] as $alias) {
                echo "*** Applying {$alias}" . PHP_EOL;
                $this->translationUp(require(Yii::getAlias($alias)));
            }

            $time = microtime(true) - $start;
            echo "*** Message Translations Applied (time: " . sprintf("%.3f", $time) . "s)" . PHP_EOL . PHP_EOL;
        }

        //Menu Link Translations
        if (isset($files['menu']) && is_array($files['menu'])) {
            echo $this->ansiFormat("Menu Link Translations:" . PHP_EOL, Console::FG_GREEN, Console::BOLD);

            foreach ($files['menu'] as $alias) {
                echo "*** Applying {$alias}" . PHP_EOL;
                $this->menuUp(require(Yii::getAlias($alias)));
            }

            $time = microtime(true) - $start;
            echo "*** Menu Link Translations Applied (time: " . sprintf("%.3f", $time) . "s)" . PHP_EOL . PHP_EOL;
        }

        return true;
    }

    /**
     * Returns the translation files.
     * @return array list of translation files.
     */
    protected function findFiles()
    {
        $this->lookup = array_unique($this->lookup);

        //replace @yeesoft shortcut with module subdirectorias
        if (in_array('@yeesoft', $this->lookup)) {
            unset($this->lookup[array_search('@yeesoft', $this->lookup)]);

            $dir = Yii::getAlias('@yeesoft');
            if (is_dir($dir)) {
                $handle = opendir($dir);
                while (($child = readdir($handle)) !== false) {
                    if ($child === '.' || $child === '..') {
                        continue;
                    }

                    if (is_dir($dir . DIRECTORY_SEPARATOR . $child . DIRECTORY_SEPARATOR . 'i18n')) {
                        $this->lookup[] = Yii::getAlias('@yeesoft') . DIRECTORY_SEPARATOR . $child . DIRECTORY_SEPARATOR . 'i18n';
                    }
                }
                closedir($handle);
            }
        }

        $directories = ArrayHelper::merge([$this->path], $this->lookup);

        if ($this->language) {
            $directories[] = rtrim($this->path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->language;
            foreach ($this->lookup as $lookup) {
                $directories[] = rtrim($lookup, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->language;
            }
        }

        $files = [];
        foreach ($directories AS $alias) {
            $dir = Yii::getAlias($alias);

            if (is_dir($dir)) {
                $handle = opendir($dir);
                while (($file = readdir($handle)) !== false) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $path = $dir . DIRECTORY_SEPARATOR . $file;
                    if (preg_match('/^.*?_(source|translation|menu)\.php$/', $file, $matches) && is_file($path)) {
                        $files[$matches[1]][] = rtrim($alias, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $matches[0];
                    }
                }
                closedir($handle);
            }
        }

        return $files;
    }

    protected function addSourceMessage($category, $message)
    {
        $this->db->createCommand()
                ->insert(static::TABLE_MESSAGE_SOURCE, ['category' => $category, 'message' => $message])
                ->execute();
    }

    protected function addTranslationMessage($category, $message, $language, $translation)
    {
        $sourceId = (new Query())
                        ->from(static::TABLE_MESSAGE_SOURCE)
                        ->where(['category' => $category, 'message' => $message])
                        ->select('id')->scalar();

        if (!$sourceId) {
            throw new \yii\console\Exception("Cannot add translation. Source message \"{$message}\" doesn't exist in category \"{$category}\".");
        }

        $this->db->createCommand()
                ->insert(static::TABLE_MESSAGE_TRANSLATIONS, [
                    'source_id' => $sourceId,
                    'language' => $language,
                    'translation' => $translation,
                ])->execute();
    }

    protected function removeSourceCategory($categories)
    {
        $this->db->createCommand()
                ->delete(static::TABLE_MESSAGE_SOURCE, ['in', 'category', $categories])
                ->execute();
    }

    protected function removeSourceMessages($category, $messages)
    {
        $this->db->createCommand()
                ->delete(static::TABLE_MESSAGE_SOURCE, ['and', 'category = :category', ['in', 'message', $messages]], [':category' => $category])
                ->execute();
    }

    protected function removeTranslationMessages($category, $messages, $language)
    {
        $sourceIds = (new Query())
                        ->from(static::TABLE_MESSAGE_SOURCE)
                        ->where(['and', 'category = :category', ['in', 'message', $messages]], [':category' => $category])
                        ->select('id')->column();

        $this->db->createCommand()
                ->delete(static::TABLE_MESSAGE_TRANSLATIONS, ['and', 'language = :language', ['in', 'source_id', $sourceIds]], [':language' => $language])
                ->execute();
    }

    protected function existsSourceMessage($category, $message)
    {
        $count = (new Query())
                ->from(static::TABLE_MESSAGE_SOURCE)
                ->where(['category' => $category, 'message' => $message])
                ->count();

        return $count > 0;
    }

    protected function existsTranslationMessage($category, $message, $language)
    {
        $count = (new Query())
                ->from(static::TABLE_MESSAGE_TRANSLATIONS)
                ->innerJoin(static::TABLE_MESSAGE_SOURCE, static::TABLE_MESSAGE_SOURCE . '.id = ' . static::TABLE_MESSAGE_TRANSLATIONS . '.source_id')
                ->where(['category' => $category, 'message' => $message, 'language' => $language])
                ->count();

        return $count > 0;
    }

    protected function existsMenuLink($link)
    {
        $count = (new Query())
                ->from(static::TABLE_MENU_LINK)
                ->where(['id' => $link])
                ->count();

        return $count > 0;
    }

    protected function existsMenuLinkTranslation($link, $language)
    {
        $count = (new Query())
                ->from(static::TABLE_MENU_TRANSLATIONS)
                ->where(['link_id' => $link, 'language' => $language])
                ->count();

        return $count > 0;
    }

    protected function addMenuLinkTranslation($link, $language, $translation)
    {
        $this->db->createCommand()
                ->insert(static::TABLE_MENU_TRANSLATIONS, ['link_id' => $link, 'language' => $language, 'label' => $translation])
                ->execute();
    }

    protected function removeMenuLinkTranslation($link, $language)
    {
        $this->db->createCommand()
                ->delete(static::TABLE_MENU_TRANSLATIONS, ['and', 'language = :language', 'link_id = :link'], [':language' => $language, ':link' => $link])
                ->execute();
    }

    protected function validateSourceParams($params)
    {
        if (!is_array($params)) {
            return false;
        }

        foreach ($params as $category => $messages) {
            if (!is_string($category) || !is_array($messages)) {
                return false;
            }

            foreach ($messages as $message) {
                if (!is_string($message)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function sourceUp($params)
    {
        if (!$this->validateSourceParams($params)) {
            $title = $this->ansiFormat('Error: ', Console::FG_RED, Console::BOLD);
            echo "*** " . $title . "Invalid source message parameters in the file. Skipping..." . PHP_EOL;
            return false;
        }

        if ($this->mode === static::MODE_HARD) {
            $this->removeSourceCategory(array_keys($params));
        }

        foreach ($params as $category => $messages) {
            foreach ($messages as $message) {
                if (!$this->existsSourceMessage($category, $message)) {
                    $this->addSourceMessage($category, $message);
                }
            }
        }
    }

    protected function validateTranslationParams($params)
    {
        if (!is_array($params)) {
            return false;
        }

        foreach ($params as $category => $messages) {
            if (!is_string($category) || !is_array($messages)) {
                return false;
            }

            foreach ($messages as $message => $translation) {
                if (!is_string($message) || !is_string($translation)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function translationUp($params)
    {
        if (!$this->validateTranslationParams($params)) {
            $title = $this->ansiFormat('Error: ', Console::FG_RED, Console::BOLD);
            echo "*** " . $title . "Invalid message translation parameters in the file. Skipping..." . PHP_EOL;
            return false;
        }

        if ($this->mode === static::MODE_UPDATE) {
            foreach ($params as $category => $messages) {
                $this->removeTranslationMessages($category, array_keys($messages), $this->language);
            }
        }

        foreach ($params as $category => $messages) {
            foreach ($messages as $message => $translation) {
                if ($this->existsSourceMessage($category, $message)) {

                    if (!$this->existsTranslationMessage($category, $message, $this->language)) {
                        $this->addTranslationMessage($category, $message, $this->language, $translation);
                    }
                } else {
                    $title = $this->ansiFormat('Warning: ', Console::FG_YELLOW, Console::BOLD);
                    echo "*** " . $title . "Cannot add translation. Source message \"{$message}\" doesn't exist in category \"{$category}\"." . PHP_EOL;
                }
            }
        }
    }

    protected function validateMenuParams($params)
    {
        if (!is_array($params)) {
            return false;
        }

        foreach ($params as $link => $translation) {
            if (!is_string($link) || !is_string($translation)) {
                return false;
            }
        }

        return true;
    }

    protected function menuUp($params)
    {
        if (!$this->validateMenuParams($params)) {
            $title = $this->ansiFormat('Error: ', Console::FG_RED, Console::BOLD);
            echo "*** " . $title . "Invalid menu link translation parameters in the file. Skipping..." . PHP_EOL;
            return false;
        }

        if (in_array($this->mode, [static::MODE_UPDATE, static::MODE_HARD])) {
            foreach ($params as $link => $translation) {
                $this->removeMenuLinkTranslation($link, $this->language);
            }
        }

        foreach ($params as $link => $translation) {
            if ($this->existsMenuLink($link)) {
                if (!$this->existsMenuLinkTranslation($link, $this->language)) {
                    $this->addMenuLinkTranslation($link, $this->language, $translation);
                }
            } else {
                $title = $this->ansiFormat('Warning: ', Console::FG_YELLOW, Console::BOLD);
                echo "*** " . $title . "Cannot add menu link translation. Menu link with id \"{$link}\" doesn't exist." . PHP_EOL;
            }
        }
    }

}
