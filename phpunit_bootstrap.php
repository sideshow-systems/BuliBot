<?php

$config = require_once 'config.php';
ini_set('include_path', ini_get('include_path') . ':' . $config['zend_path']);

require_once $config['zend_path'] . '/Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

$_SERVER['config'] = $config;
?>
