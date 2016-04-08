<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);
define('APP_PATH', ROOT . 'app' . DS);
require_once APP_PATH . 'constants.php';
require_once APP_PATH . 'appcontroller.php';
require_once APP_PATH . 'session.php';
require_once APP_PATH . 'bootstrap.php';
require_once APP_PATH . 'autoload.php';
#inicializamos la funcion principal de App
Session::init();
Bootstrap::run(new appModel);
?>