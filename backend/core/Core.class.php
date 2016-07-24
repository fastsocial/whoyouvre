<?php

class Core {
    private $config;
    const CONFIG_FILE_PATH = "/config/config.inc.php";
    const BUNDLES_PATH = "/bundles/";
    const MODELS_PATH = "/models/";

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

    private function initBundles() {
        $bundlesPath = dirname(__FILE__).self::BUNDLES_PATH;
        if ($handle = opendir($bundlesPath)) {
            while (false !== ($dir = readdir($handle))) {
                if ($dir != "." && $dir != ".." && is_dir($bundlesPath.$dir)) {
                    $bundleClassName = mb_strtolower($dir);
                    echo "loading bundle $bundleClassName\n";
                    require_once ($bundlesPath.$dir."/$bundleClassName.class.php");
                }
            }
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
$db = new Db();

//$paintings = new Paintings($db);
//$allPaintings = $paintings->getAll();
//$allPaintings[5]->delete();