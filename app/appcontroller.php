<?php
class appModel{
    public $_controlador;
    public $_metodo;
    public $_argumentos;
    public function __construct() { 
        if(isset($_GET)):
            $this->_controlador = base64_decode(filter_input(INPUT_GET, 'app', FILTER_SANITIZE_URL)); 
            $this->_metodo      = base64_decode(filter_input(INPUT_GET, 'met', FILTER_SANITIZE_URL));
            $this->_argumentos  = explode(',',base64_decode(filter_input(INPUT_GET, 'arg', FILTER_SANITIZE_URL)));
            //var_dump($this->_argumentos);exit;
        endif;
        if(!$this->_controlador):
            $this->_controlador = DEFAULT_CONTROLLER;
        endif;
        if(!$this->_metodo):
            $this->_metodo = 'index';
        endif;
    }
    public function getControlador(){
        return $this->_controlador;
    }
    public function getMetodo(){
        return $this->_metodo;
    }
    public function getArgumentos(){
        return $this->_argumentos;
    }
    function __destruct() {
        unset($this);
    }
}
?>
