<?php

class Singleton {
    protected static $instance = array();

    protected function __construct() { }

    public static function class_name() {
        return get_called_class();
    }

    final public static function getInstance() {
        $className = self::class_name();
        if (!isset(static::$instance[$className])) {
            static::$instance[$className] = new static();
        }

        return static::$instance[$className];
    }

    final private function __clone() { }
}