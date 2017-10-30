<?php

namespace yeesoft\console;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

//php yii i18n --mode=soft --lookup=@yeesoft/yii2-yee-page/i18n/ --language=uk
class I18nController extends Controller
{

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

    /**
     * @var string 
     */
    public $language;

    public function options($actionID)
    {
        return ['mode', 'path', 'language', 'lookup'];
    }

    public function optionAliases()
    {
        return ['m' => 'mode', 'p' => 'path', 'l' => 'language'];
    }

    public function actionIndex()
    {
        print_r($this->lookup);

        $files = $this->findFiles();

        print_r($files);
        
        
        foreach ($files['source'] as $alias) {
            $file = Yii::getAlias($alias);
            
            $array = require($file);
            
            print_r($array);
            
        }

//        if ($this->confirm('Apply i18n translations?')) {
//            foreach ($files as $file) {
//                if (!$this->migrateUp($file)) {
//                    echo PHP_EOL . "i18n failed. The rest of the i18n are canceled." . PHP_EOL;
//
//                    return;
//                }
//            }
//            echo PHP_EOL . "Migrated up successfully." . PHP_EOL;
//        }




        echo $this->mode . ' - ' . $this->path . ' - ' . $this->language . PHP_EOL;
    }

    /**
     * Upgrades with the specified migration class.
     *
     * @param string $class the migration class name
     *
     * @return boolean whether the migration is successful
     */
    protected function migrateUp($file)
    {
        echo "*** applying $class\n";
        $start = microtime(true);
//        $migration = $this->createMigration($class, $alias);
//        if ($migration->up() !== false) {
//
//            $time = microtime(true) - $start;
//            echo "*** applied $class (time: " . sprintf("%.3f", $time) . "s)\n\n";
//
//            return true;
//        } else {
//            $time = microtime(true) - $start;
//            echo "*** failed to apply $class (time: " . sprintf("%.3f", $time) . "s)\n\n";
//
//            return false;
//        }

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
