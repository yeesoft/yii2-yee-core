<?php

namespace yeesoft\console;

use Yii;
use yii\db\Query;
use yii\helpers\Console;
use yii\console\Exception;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

//php yii i18n --mode=soft --lookup=@yeesoft/yii2-yee-page/i18n/ --language=uk

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
     * @var string the directory storing the migration classes. This can be either
     * a path alias or a directory.
     */
    public $path = '@app/i18n';

    /**
     * @var array additional aliases of migration directories
     */
    public $lookup = [];

    /**
     * @var string 
     */
    public $mode;
    //hard - clear category + add
    //update - rewrite modified messages + add new
    //soft - add new only

    /**
     * @var string 
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

    public function options($actionID)
    {
        return ['mode', 'path', 'language', 'lookup'];
    }

    public function optionAliases()
    {
        return ['m' => 'mode', 'p' => 'path', 'l' => 'language'];
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
                ->delete(static::TABLE_MESSAGE_TRANSLATIONS, ['and', 'language = :language', 'link_id = :link'], [':language' => $language, ':link' => $link])
                ->execute();
    }

    protected function sourceUp($params)
    {
        //check is valid $params

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

    protected function translationUp($params)
    {
        //check is valid $params

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
                    echo $title . "Cannot add translation. Source message \"{$message}\" doesn't exist in category \"{$category}\"." . PHP_EOL;
                }
            }
        }
    }

    protected function menuUp($messages)
    {
        //check is valid $params

        if ($this->mode === static::MODE_UPDATE) {
            foreach ($messages as $link => $translation) {
                $this->removeMenuLinkTranslation($link, $this->language);
            }
        }

        foreach ($messages as $link => $translation) {
            if ($this->existsMenuLink($link)) {
                if (!$this->existsMenuLinkTranslation($link, $this->language)) {
                    $this->addMenuLinkTranslation($link, $this->language, $translation);
                }
            } else {
                $title = $this->ansiFormat('Warning: ', Console::FG_YELLOW, Console::BOLD);
                echo $title . "Cannot add menu link translation. Menu link with id \"{$link}\" doesn't exist." . PHP_EOL;
            }
        }
    }

    public function actionIndex()
    {

        print_r($this->lookup);

        $files = $this->findFiles();

        print_r($files);
        
        $this->update($files);





//        if ($this->confirm('Apply i18n translations?')) {
//            echo PHP_EOL . "Updated successfully." . PHP_EOL;
//        }




        echo $this->mode . ' - ' . $this->path . ' - ' . $this->language . PHP_EOL;
    }

    /**
     * Upgrades with the specified migration class.
     *
     * @param array $files the migration class name
     *
     * @return boolean whether the migration is successful
     */
    protected function update($files)
    {
        echo "*** applying \n";
        $start = microtime(true);

        if (isset($files['source']) && is_array($files['source'])) {
            foreach ($files['source'] as $alias) {
                $this->sourceUp(require(Yii::getAlias($alias)));
            }
        }

        $time = microtime(true) - $start;
        echo "*** applied source (time: " . sprintf("%.3f", $time) . "s)\n\n";

        if (isset($files['translation']) && is_array($files['translation'])) {
            foreach ($files['translation'] as $alias) {
                $this->translationUp(require(Yii::getAlias($alias)));
            }
        }
        
        $time = microtime(true) - $start;
        echo "*** applied translation (time: " . sprintf("%.3f", $time) . "s)\n\n";

        if (isset($files['menu']) && is_array($files['menu'])) {
            foreach ($files['menu'] as $alias) {
                $this->menuUp(require(Yii::getAlias($alias)));
            }
        }
        
        $time = microtime(true) - $start;
        echo "*** applied menu (time: " . sprintf("%.3f", $time) . "s)\n\n";
 
        return true;
    }

    /**
     * Creates a new migration instance.
     *
     * @param string $class the migration class name
     *
     * @return \yii\db\Migration the migration instance
     */
    protected function createMigration($class, $alias)
    {
        $file = $class . '.php';
        require_once(\Yii::getAlias($alias) . '/' . $file);

        return new $class(['db' => $this->db]);
    }

    /**
     * Returns the migrations that are not applied.
     * @return array list of new migrations, (key: migration version; value: alias)
     */
    protected function findFiles()
    {
        $directories = ArrayHelper::merge([$this->path], $this->lookup);

        if ($this->language) {
            $directories[] = rtrim($this->path, '/') . '/' . $this->language;
            foreach ($this->lookup as $lookup) {
                $directories[] = rtrim($lookup, '/') . '/' . $this->language;
            }
        }

        print_r($directories);

        $migrations = [];

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
//                        print_r($matches);
                        $migrations[$matches[1]][] = rtrim($alias, '/') . '/' . $matches[0];
                    }
                }
                closedir($handle);
            }
        }


        return $migrations;
    }

}
