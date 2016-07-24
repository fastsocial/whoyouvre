<?php

require_once Core::getAbsolutePath(Core::MISC_PATH)."autoload.inc.php";

class Db extends Singleton {
    private $db;
    public function __construct() {
        global $_core;
        $this->db = new mysqli(
            $_core->getConfigVar("db", "host"),
            $_core->getConfigVar("db", "user"),
            $_core->getConfigVar("db", "pass"),
            $_core->getConfigVar("db", "name")
        );
        $this->query("set names utf8");
    }

    public function escape($expr) {
        return $this->db->real_escape_string($expr);
    }

    public function query($query) {
        return $this->db->query($query);
    }

    public function last_insert_id() {
        return $this->db->insert_id;
    }
}

require_once(dirname(__FILE__)."/db_object.class.php");