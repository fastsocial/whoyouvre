<?php

class Core {
    private $config;
    public $bundles;
    const CONFIG_FILE_PATH = "/config/config.inc.php";
    const BUNDLES_PATH = "/bundles/";
    const MODELS_PATH = "/models/";
    const MISC_PATH = "/misc_classes/";

    public static function getAbsolutePath($relative) {
        return dirname(__FILE__).$relative;
    }

    function __construct() {
        require_once(dirname(__FILE__).self::CONFIG_FILE_PATH);
        $this->config = $_CONFIG;
        $this->initBundles();
        $this->initModels();
    }

    private function initModels() {
        $modelsPath = dirname(__FILE__).self::MODELS_PATH;
        if ($handle = opendir($modelsPath)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && is_file($modelsPath.$file)) {
                    echo "loading model $file\n";
                    require_once ($modelsPath.$file);
                }
            }
        }
    }

    public function loadModel($name) {
        $db = Db::getInstance();
        if (is_object($db))
        try {
            $model = new $name($db);
            return $model;
        } catch (Exception $e) {
            return false;
        }
    }

    private function initBundles() {
        $bundlesPath = dirname(__FILE__).self::BUNDLES_PATH;
        if ($handle = opendir($bundlesPath)) {
            while (false !== ($dir = readdir($handle))) {
                if ($dir != "." && $dir != ".." && is_dir($bundlesPath.$dir)) {
                    $bundleClassName = mb_strtolower($dir);
                    echo "loading bundle $bundleClassName\n";
                    require_once ($bundlesPath.$dir."/$bundleClassName.class.php");
                    $bundleClassNameUpper = ucfirst($bundleClassName);
                    $this->bundles[$bundleClassNameUpper] = array("name" => $bundleClassNameUpper);
                }
            }
        }
    }

    public function createBundle($name) {
        if (isset($this->bundles[$name])) {
            $this->bundles[$name]["bundle"] = new $name();
        }
    }

    public function getConfigVar($section, $field) {
        if (isset($this->config[$section]) && isset($this->config[$section][$field])) {
            return $this->config[$section][$field];
        }

        return false;
    }
}

$_core = new Core();
$_core->createBundle("Db");
$_core->loadModel("Paintings");

$paintings = Paintings::getInstance();
$allPaintings = $paintings->getAll();

var_dump($allPaintings[1]->toArray());