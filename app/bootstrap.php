<?php
class Bootstrap{
    public static function run(appController $peticion){
        $controller           = $peticion->getControlador() . 'Controller';
        $modulo               = str_replace('Controller','',$controller);
        $rutaModulo           = MODULES_PATH . DS . $modulo . DS ;
        $rutaControlador      = $rutaModulo . 'controller' . DS . $controller . '.php';
        $Objvista             = new view;
        $Objvista->_diretorio = DIR_SISTEMA;
        date_default_timezone_set("America/Bogota");

        if(is_readable($rutaControlador)):
            $metodo = $peticion->getMetodo();
            require_once $rutaControlador;
            $appController = new $controller;
            is_callable(array($appController, $metodo)) ? $metodo = $peticion->getMetodo() : $metodo= 'index';
            $appController->_metodo     = $metodo;
            $appController->_argumentos = $peticion->getArgumentos();
            $appController->$metodo();
        else:
            $Objvista->_titulo    = 'error::no se han encontrado resultados';
            $Objvista->_template  = 'error.html';
            $Objvista->_renderVista();
        endif;
    }
}
?>