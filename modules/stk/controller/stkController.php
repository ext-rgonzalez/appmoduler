<?php

class stkController {
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracion de usuario desde el modelo propio del
//             controlador.
    public function index() {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/stk/model/stkModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        if (!Session::get('usuario')) {
            $Objvista = new view;
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'stk', 'login', 'login', $data);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $stk = new ModeloStk();
            $Objvista = new view;
            $stk->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa = $stk->_empresa;
            $var = "";
            for ($t = 0; $t < count($stk->_notificacion); $t++) {
                $var .= $stk->_notificacion[$t]['des_notificacion'];
            }
            $Objvista->_notificacion = array('des_notificacion' => $var);
            $Objvista->_numNotificacion = $stk->_numNotificacion;
            $var = "";
            for ($t = 0; $t < count($stk->_mensajes); $t++) {
                $var .= $stk->_mensajes[$t]['des_mensajes'];
            }
            $Objvista->_mensajes = array('des_mensajes' => $var);
            $Objvista->_numMensajes = $stk->_numMensajes;
            $var = "";
            for ($t = 0; $t < count($stk->_menu); $t++) {
                $var .= $stk->_menu[$t]['menu'];
            }
            $Objvista->_menu = array('menu' => $var);
            $Objvista->_menuHeader = $stk->_menuHeader;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'index', 'index');
        }
    }
//Autor:       David G -  Abr 26-2015
//descripcion: Metodo para gestionar la configuracion general del modulo strike, utiliza los metodos simples
//             para el registro, edicion y aliminacion de registros, cuando se edita el estado de una configuracion 
//             especifica, se desactivan las demas con el procedimiento almacenado pbActualizaConfig(1-case, 2-cod_tabla).
    public function ConfiguracionSTKGeneral($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/stk/model/stkModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $stk = new ModeloStk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $stk->get_datos('*', "cod_config=" . $argumentos[3], $argumentos[4]);
                $data = $stk->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $stk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $stk->_data;
            setVariables($stk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'stk_view_config',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'stk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function CabeceraEncuesta($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/stk/model/stkModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $stk = new ModeloStk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $stk->get_datos('*', "cod_cabecera_encuesta=" . $argumentos[3], $argumentos[4]);
                $data = $stk->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $stk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $stk->_data;
            setVariables($stk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'stk_view_cabecera_encuesta',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'stk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Respuesta($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/stk/model/stkModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $stk = new ModeloStk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $stk->get_datos('*', "cod_respuesta=" . $argumentos[3], $argumentos[4]);
                $data = $stk->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $stk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $stk->_data;
            setVariables($stk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'stk_view_respuesta',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'stk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Preguntas($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/stk/model/stkModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $stk = new ModeloStk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $stk->get_datos('*', "cod_pregunta=" . $argumentos[3], $argumentos[4]);
                $data = $stk->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $stk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $stk->_data;
            setVariables($stk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'stk_view_pregunta',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'stk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function PreguntaRespuesta($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/stk/model/stkModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $stk = new ModeloStk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N"; 
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $stk->get_datos('*', "cod_pregunta=" . $argumentos[3], $argumentos[4]);
                $data = $stk->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            $stk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $stk->_data;
            setVariables($stk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'stk_view_pregunta',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'stk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function nuevoRegistro($metodo) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/stk/model/stkModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $stk    = new ModeloStk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR' => 3,'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $user_data = helper_user_data($metodo);
            $stk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $stk->_data;
            switch ($user_data['no_nom_tabla']) {
                case 'NuevaConfiguracionSTKGeneral':
                    $campo = array('nom_config=',' and cod_empresa=');
                    $clave = array("'" . $user_data['nom_config'] . "'",$user_data["cod_empresa"]);
                break;
                case 'NuevaCabeceraEncuesta':
                    $campo = array('nom_cabecera_encuesta=',' and cod_empresa=');
                    $clave = array("'" . $user_data['nom_cabecera_encuesta'] . "'",$user_data["cod_empresa"]);
                    $user_data["fec_cabecera_encuesta"]= date("Y-m-d H:i:s");
                break;
                case 'NuevaRespuesta':
                    $campo = array('nom_respuesta=',' and cod_empresa=');
                    $clave = array("'" . $user_data['nom_respuesta'] . "'",$user_data["cod_empresa"]);
                    $user_data["fec_respuesta"]= date("Y-m-d H:i:s");
                break;
                case 'NuevaPreguntas':
                    var_dump($user_data);exit;
                break;
            }
            if ($valida) {
                if (!$stk->getRegistro($user_data['no_esq_tabla'], $campo, $clave)) {
                    $stk->setRegistro($user_data);
                    $data = array('ERR' => 6,
                                  'MSJ' => $stk->msj);
                    setVariables($stk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $stk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$stk->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                } else {
                    $data = array('ERR' => 2,
                                  'MSJ' => "El registro ya existe");                    
                    setVariables($stk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $stk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$stk->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                }
            }else{
                $data= array('ERR' => $stk->err,
                             'MSJ' => $stk->msj);
                setVariables($stk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $stk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$stk->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
    
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/stk/model/stkModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $stk    = new ModeloStk();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $stk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$stk->_data;
            //var_dump($sistema->_data);exit();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch ($user_data['no_nom_tabla']) {
                case 'NuevaConfiguracionSTKGeneral':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                break;
                case 'NuevaCabeceraEncuesta':
                    $campo = array('nom_cabecera_encuesta' . "=");
                    $clave = array("'" . $user_data['nom_cabecera_encuesta'] . "'");
                    $user_data["fec_mod_cabecera_encuesta"]= date("Y-m-d H:i:s");
                break;
                case 'NuevaRespuesta':
                    $campo = array('nom_respuesta=');
                    $clave = array("'" . $user_data['nom_respuesta'] . "'");
                    $user_data["fec_mod_respuesta"]= date("Y-m-d H:i:s");
                break;
            }
            if($valida){
                $stk->editRegistro($user_data);
                $data = array('ERR'=>6,
                              'MSJ'=>$stk->msj);
                setVariables($stk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $stk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$stk->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data= array('ERR' => $stk->err,
                             'MSJ' => $stk->msj);
                setVariables($stk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $stk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$stk->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
}

?>