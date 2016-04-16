<?php
class view {
    public $_smarty           = null;
    public $_dirForm          = null;
    public $_asignacion       = null;
    public $_diretorio        = '';
    public $_titulo           = '';
    public $_template         = '';
    public $_vista            = '';
    public $_vistaDatos       = '';
    public $_js               = null;
    public $_css              = null;
//Autor:       Ricardo Gonzalez Ablir 8-14
//descripcion: constructor de clase
    public function __construct(){
        $this->_smarty     = new Smarty();
        $this->_asignacion = new stdClass();
        $this->_js         = new stdClass();
        $this->_css        = new stdClass();
        $this->_vista      = '';
        $this->_vistaDatos = array();
        $this->_css        = '';
        $this->_js         = '';
    }
//Autor:       Ricardo Gonzalez Abril 8-14
//descripcion: Metodo publico para asinar las variables a smarty
    public function _asignarVariables(){
        foreach($this->_asignacion as $asignacion=>$valor){
            $this->_smarty->assign($asignacion, $valor);
        }
    }
//Autor:       Ricardo Gonzalez Abril 8-14
//descripcion: Metodo publico para imprimir la vista
    public function _renderVista(){
        $this->_smarty->debugging      = false;
        $this->_smarty->caching        = false;
        $this->_smarty->cache_lifetime = 120;
        $this->_smarty->assign('titulo',     $this->_titulo);
        $this->_smarty->assign('_js',        $this->_js);
        $this->_smarty->assign('_css',       $this->_css);
        $this->_smarty->assign('_vista',     $this->_dirForm.$this->_vista);
        $this->_smarty->assign('_vistaDatos',$this->_vistaDatos);
        $this->_asignarVariables();
        $this->_smarty->display($this->_diretorio . $this->_template);
    }
//Autor:       Ricardo Gonzalez Ablir 8-14
//descripcion: destructor de clase
    public function __destruct(){
        unset($this);
    }
}
?>
