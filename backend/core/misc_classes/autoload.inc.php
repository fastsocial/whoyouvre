<?php

function __autoload($class_name) {
    $fileName = mb_strtolower($class_name).".class.php";
    require_once dirname(__FILE__)."/".$fileName;
}