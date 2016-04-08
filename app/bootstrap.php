<?php
class Bootstrap{
    public static function run(appModel $peticion){
        $controller      = $peticion->getControlador() . 'Controller';
        $modulo          = str_replace('Controller','',$controller);
        $rutaModulo      = MODULES_PATH . DS . $modulo . DS ;
        $rutaControlador = $rutaModulo . 'controller' . DS . $controller . '.php';

        date_default_timezone_set("America/Bogota");
        if(is_readable($rutaControlador)):
            $metodo = $peticion->getMetodo();
            require_once $rutaControlador;
            $appController = new $controller;
            is_callable(array($appController, $metodo)) ? $metodo = $peticion->getMetodo() : $metodo= 'index';
            $appController->$metodo($metodo,$peticion->getArgumentos());
            exit();
        else:
            require_once ROOT . VIEW_PACH . DS . 'view.php';
            $Objvista = new view;
            $data = array('ERR_EXC'=>001);
            $data=array();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,$rutaModulo,'error','error');
        endif;
    }
}
?>