<?php
class view {
    public $_empresa          = array();
    public $_notificacion     = array();
    public $_tarea            = array();
    public $_numNotificacion  = array();
    public $_mensajes         = array();
    public $_numMensajes      = array();
    public $_menu             = array();
    public $_menuHeader       = array();
    public $_menuShorcut      = array();
    public $_formulario       = array();
    public $_formulario_ayuda = array();
    public $_formulario_modal = array();
    public $_archivos_css     = array();
    public $_archivos_js      = array();
    public $_boton            = array();
    public $_combos           = array();
    public $_tabla            = array();
    public $_enabled;
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo que trae el archivo de la vista y lo guarda en la variable $template,
//             con la funcion reservada de php file_get_contents()
    public function get_template($modulo,$vista,$ruta=null) {
        $file = $ruta=='' ? 'modules/' . $modulo. '/html/'. $vista .'.html' : '../../' . $modulo. '/html/'. $vista .'.html';
        $template = file_get_contents($file);	
        return $template;
    }
	
    public function render_dinamic_data($modulo, $html, $data=array(), $enabled="") {
        //var_dump($data);exit;
        if(!empty($data)){
            foreach ($data as $clave=>$valor) {
                if(is_array($valor)){
                    foreach($valor as $clave_1=>$valor_1){
                        if(is_array($valor_1)){
                            foreach($valor_1 as $clave_2=>$valor_2){
                                if(is_array($valor_2)){
                                    foreach($valor_2 as $clave_3=>$valor_3){
                                        $html = str_replace('{'.$clave_3.'}', $valor_3, $html);
                                    }
                                }else{
                                    $html = str_replace('{'.$clave_2.'}', $valor_2, $html);	
                                }
                            }
                        }else{
                            $html = str_replace('{'.$clave_1.'}', $valor_1, $html);
                        }
                    }
                }
                else{
                    $html = str_replace('{'.$clave.'}', $valor, $html);
                }
            }
        }
        
        if(!empty($enabled)){
            $html = str_replace('data-disabled', $this->_enabled, $html);
        }
        return $html;
    }
		
    public function get_form($modulo,$html,$data,$rutaForm=''){
        if(!empty($data[0]['FORM'])){
            $html = str_replace('{FORM}',view::get_template(empty($rutaForm) ? $modulo : $rutaForm,$data[0]['FORM']),$html);
        }
        return $html;
    }
		
    public function retornar_vista($modulo, $rutaModulo, $vista, $form, $data=array(),$dataFormGeneral=array(),$rutaForm='') {
        global $diccionario;
        global $diccionario_general;
        $html = view::get_template($modulo,$vista);
        $html = view::get_form($modulo,$html,$dataFormGeneral,$rutaForm);
        $html = view::render_dinamic_data('',$html,$diccionario_general);
        $html = view::render_dinamic_data($modulo,$html,$_SESSION);
        isset($dataFormGeneral[0]['TITLE']) ? $html = str_replace('{TITLE}', $dataFormGeneral[0]['TITLE'],$html) : $html=$html;
        //$html = view::render_dinamic_data($modulo, $html, $this->_numNotificacion);
        $html = view::render_dinamic_data($modulo, $html, $this->_notificacion);
        $html = view::render_dinamic_data($modulo, $html, $this->_tarea);
        $html = view::render_dinamic_data($modulo, $html, $this->_numMensajes);
        $html = view::render_dinamic_data($modulo, $html, $this->_mensajes);
        $html = view::render_dinamic_data($modulo, $html, $this->_empresa);
        $html = view::render_dinamic_data($modulo, $html, $this->_menu);
        $html = view::render_dinamic_data($modulo, $html, $this->_menuHeader);
        $html = view::render_dinamic_data($modulo, $html, $this->_menuShorcut);
        $html = view::render_dinamic_data($modulo, $html, $this->_formulario);
        $html = view::render_dinamic_data($modulo, $html, $this->_formulario_modal);
        $html = view::render_dinamic_data($modulo, $html, $this->_formulario_ayuda);
        $html = view::render_dinamic_data($modulo, $html, $this->_archivos_css);
        $html = view::render_dinamic_data($modulo, $html, $this->_archivos_js);
        $html = view::render_dinamic_data($modulo, $html, $this->_tabla);
        $html = view::render_dinamic_data($modulo, $html, $this->_boton);
        $html = view::render_dinamic_data($modulo, $html, '',$this->_enabled);
        isset($dataFormGeneral[0]) ? $html = view::render_dinamic_data('',$html,$dataFormGeneral[0]) : $html=$html;
        $html = view::render_dinamic_data($modulo, $html, $data);
        print $html;
    }
}
?>
