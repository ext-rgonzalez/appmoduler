<?php

class sinedController {
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracion de usuario desde el modelo propio del
//             controlador.
    public function index() {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        if (!Session::get('usuario')) {

        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $sined = new ModeloSined();
            $Objvista = new view;
            $sined->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa = $sined->_empresa;
            $var = "";
            for ($t = 0; $t < count($sined->_notificacion); $t++) {
                $var = $var . $sined->_notificacion[$t]['des_notificacion'];
            }
            $Objvista->_notificacion = array('des_notificacion' => $var);
            $Objvista->_numNotificacion = $sined->_numNotificacion;
            $var = "";
            for ($t = 0; $t < count($sined->_mensajes); $t++) {
                $var = $var . $sined->_mensajes[$t]['des_mensajes'];
            }
            $Objvista->_mensajes = array('des_mensajes' => $var);
            $Objvista->_numMensajes = $sined->_numMensajes;
            $var = "";
            for ($t = 0; $t < count($sined->_menu); $t++) {
                $var = $var . $sined->_menu[$t]['menu'];
            }
            $Objvista->_menu = array('menu' => $var);
            $Objvista->_menuHeader = $sined->_menuHeader;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'index', 'index');
        }
    }

    public function tiposIdentificacion($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_tipo_identificacion=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_tipo_identificacion',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function tiposGenero($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_tipo_genero=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_tipo_genero',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function tiposCaracter($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_ia_caracter=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_ia_caracter',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function tiposEspecialidad($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_ia_especialidad=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_ia_especialidad',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function tiposContrato($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_ia_contrato=" . $argumentos[3], $argumentos[4]);
                //var_dump($sined->_data);exit;
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_ia_contrato',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function eps($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_iss_eps=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_iss_eps',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function SituacionesAcademicas($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_alumno_situacion=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_alumno_situacion',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ars($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_iss_ars=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_iss_ars',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function tipoSangre($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_iss_tiposangre=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_iss_tiposangre',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function tipoPoblacion($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_id_poblacion=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_id_poblacion',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function nivelSisben($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_ise_nivelsisben=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_ise_nivelsisben',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function fuenteRecursos($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_ise_fuenterecursos=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_ise_fuenterecursos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function resguardo($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_ite_resguardo=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_ite_resguardo',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function etnia($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_ite_etnia=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_ite_etnia',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function equivalencia($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_nota_equivalencia=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_nota_equivalencia',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfiguracionGeneralSined($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_config=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
                $arrayChek = array("ind_imp_logros" => $data[0]["ind_imp_logros"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_config',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfiguracionDocumentos($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_config_documentos=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_config_documentos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function MateriasAsignaturas($metodo, $argumentos){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_materia=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_materia',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function TipoParentesco ($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_parentesco=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_parentesco',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function TipoEstCivil($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_estado_civil=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_estado_civil',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function CapacidadesExc($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_capacidades=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_capacidades',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function DiscapacidadesExc($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_discapacidades=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_discapacidades',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Aulas($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_aula=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_responsable" => $data[0]["cod_responsable"],"cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_aula',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Grupo($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_grupo=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_responsable" => $data[0]["cod_responsable"],"cod_estado" => $data[0]["cod_estado"],
                                     "cod_empresa" => $data[0]["cod_empresa"],"cod_tipo_grupo" => $data[0]["cod_tipo_grupo"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_grupo',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function TipoGrupo($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_tipo_grupo=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_grupo" => $data[0]["cod_grupo"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_tipo_grupo',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Preinscripciones($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$camposComboEsp = array();
            $campoChek = array();$ciclo=array();$cicloInput=2;$dataFormGeneral=array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_alumno=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $sined->get_datos('cod_capacidades',"cod_alumno=".$argumentos[3].' and ind_capacidades=1',' sined_alumno_discapacidades_capacidades'); !empty($sined->_data) ? $data[0]["cod_capacidades"]=$sined->_data : $data[0]["cod_capacidades"]=array();
                $sined->get_datos('cod_discapacidades',"cod_alumno=".$argumentos[3].' and ind_discapacidades=1',' sined_alumno_discapacidades_capacidades'); !empty($sined->_data) ? $data[0]["cod_discapacidades"]=$sined->_data : $data[0]["cod_discapacidades"]=array();
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"],"cod_tipo_identificacion" => $data[0]["cod_tipo_identificacion"],
                                     "cod_ciudad_expedicion" => $data[0]["cod_ciudad_expedicion"],"cod_estado_matricula" => $data[0]["cod_estado_matricula"],"cod_ciudad_nacimiento" => $data[0]["cod_ciudad_nacimiento"],
                                     "cod_ciudad_residencia" => $data[0]["cod_ciudad_residencia"],"cod_ia_caracter" => $data[0]["cod_ia_caracter"],"cod_ia_especialidad" => $data[0]["cod_ia_especialidad"],
                                     "cod_ia_contrato" => $data[0]["cod_ia_contrato"],"cod_iss_eps" => $data[0]["cod_iss_eps"],"cod_iss_tiposangre" => $data[0]["cod_iss_tiposangre"],"cod_alumno_situacion" => $data[0]["cod_situacion"],"cod_grupo" => $data[0]["cod_grupo"],
                                     "cod_iss_ars" => $data[0]["cod_iss_ars"],"cod_id_poblacion" => $data[0]["cod_id_poblacion"],"cod_ciudad_expulsion" => $data[0]["cod_ciudad_expulsion"],
                                     "cod_ise_nivelsisben" => $data[0]["cod_ise_nivelsisben"],"cod_estrato" => $data[0]["cod_estrato"],"cod_ise_fuenterecursos" => $data[0]["cod_ise_fuenterecursos"],
                                     "cod_ite_resguardo" => $data[0]["cod_ite_resguardo"],"cod_ite_etnia" => $data[0]["cod_ite_etnia"],"cod_zona" => $data[0]["cod_zona"],"cod_tipo_genero" => $data[0]["cod_tipo_genero"],
                                     "cod_discapacidades" => $data[0]["cod_discapacidades"],"cod_capacidades" => $data[0]["cod_capacidades"] );
                $arrayChek   = array("ind_mat_contratada" => $data[0]["ind_mat_contratada"], "ind_certificado_expulsion" => $data[0]["ind_certificado_expulsion"],"ind_sisbenlll" => $data[0]["ind_sisbenlll"],
                                     "ind_madrecabezahogar" => $data[0]["ind_madrecabezahogar"],"ind_hijosdependientesmadre" => $data[0]["ind_hijosdependientesmadre"],"ind_beneficiariovfp" => $data[0]["ind_beneficiariovfp"],
                                     "ind_beneficiariohn" => $data[0]["ind_beneficiariohn"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    $valorChek == 1 ? $campoChek[]=$llave : $campoChek[]="";
                }
                
                $sined->get_datos('t1.des_alumno_novedades as Descripcion,t1.fec_alumno_novedades as FechaRegistro,t2.usuario_usuario as Usuario', ' t1.cod_alumno=' . $argumentos[3] .' and t1.cod_usuario=t2.cod_usuario order by cod_alumno_novedades desc', 'sined_alumno_novedades as t1, sys_usuario as t2');
                $desAnidada = armaTextAnidado($sined->_data);
                $data[0]["ult_gestiones"] = !empty($desAnidada) ? $desAnidada : array();
                
                $sined->get_datos('*,ind_acudiente as cod_respuestas', 'cod_alumno='.$data[0]["cod_alumno"] , 'sined_alumno_familiares');
                $camposComboEsp=$sined->_data;
                //var_dump($camposComboEsp);exit;
                $sined->get_datos('count(1) as ciclo', 'cod_alumno='.$data[0]["cod_alumno"], 'sined_alumno_familiares');
                $ciclo=$sined->_data;
                $cicloInput=$ciclo[0]["ciclo"];
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_alumno'," cod_empresa in(".$cadEmp[0]['result'].") and Estado='MPR'",$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Matriculas($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$camposComboEsp = array();
            $campoChek = array();$ciclo=array();$cicloInput=2;$dataFormGeneral=array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_alumno=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $sined->get_datos('cod_capacidades',"cod_alumno=".$argumentos[3].' and ind_capacidades=1',' sined_alumno_discapacidades_capacidades'); !empty($sined->_data) ? $data[0]["cod_capacidades"]=$sined->_data : $data[0]["cod_capacidades"]=array();
                $sined->get_datos('cod_discapacidades',"cod_alumno=".$argumentos[3].' and ind_discapacidades=1',' sined_alumno_discapacidades_capacidades'); !empty($sined->_data) ? $data[0]["cod_discapacidades"]=$sined->_data : $data[0]["cod_discapacidades"]=array();
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"],"cod_tipo_identificacion" => $data[0]["cod_tipo_identificacion"],
                                     "cod_ciudad_expedicion" => $data[0]["cod_ciudad_expedicion"],"cod_estado_matricula" => $data[0]["cod_estado_matricula"],"cod_ciudad_nacimiento" => $data[0]["cod_ciudad_nacimiento"],
                                     "cod_ciudad_residencia" => $data[0]["cod_ciudad_residencia"],"cod_ia_caracter" => $data[0]["cod_ia_caracter"],"cod_ia_especialidad" => $data[0]["cod_ia_especialidad"],
                                     "cod_ia_contrato" => $data[0]["cod_ia_contrato"],"cod_iss_eps" => $data[0]["cod_iss_eps"],"cod_iss_tiposangre" => $data[0]["cod_iss_tiposangre"],"cod_alumno_situacion" => $data[0]["cod_situacion"],"cod_grupo" => $data[0]["cod_grupo"],
                                     "cod_iss_ars" => $data[0]["cod_iss_ars"],"cod_id_poblacion" => $data[0]["cod_id_poblacion"],"cod_ciudad_expulsion" => $data[0]["cod_ciudad_expulsion"],
                                     "cod_ise_nivelsisben" => $data[0]["cod_ise_nivelsisben"],"cod_estrato" => $data[0]["cod_estrato"],"cod_ise_fuenterecursos" => $data[0]["cod_ise_fuenterecursos"],
                                     "cod_ite_resguardo" => $data[0]["cod_ite_resguardo"],"cod_ite_etnia" => $data[0]["cod_ite_etnia"],"cod_zona" => $data[0]["cod_zona"],"cod_tipo_genero" => $data[0]["cod_tipo_genero"],
                                     "cod_discapacidades" => $data[0]["cod_discapacidades"],"cod_capacidades" => $data[0]["cod_capacidades"]);
                //var_dump($camposCombo);exit;
                $arrayChek   = array("ind_mat_contratada" => $data[0]["ind_mat_contratada"], "ind_certificado_expulsion" => $data[0]["ind_certificado_expulsion"],"ind_sisbenlll" => $data[0]["ind_sisbenlll"],
                                     "ind_madrecabezahogar" => $data[0]["ind_madrecabezahogar"],"ind_hijosdependientesmadre" => $data[0]["ind_hijosdependientesmadre"],"ind_beneficiariovfp" => $data[0]["ind_beneficiariovfp"],
                                     "ind_beneficiariohn" => $data[0]["ind_beneficiariohn"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    $valorChek == 1 ? $campoChek[]=$llave : $campoChek[]="";
                }
                
                $sined->get_datos('t1.des_alumno_novedades as Descripcion,t1.fec_alumno_novedades as FechaRegistro,t2.usuario_usuario as Usuario', ' t1.cod_alumno=' . $argumentos[3] .' and t1.cod_usuario=t2.cod_usuario order by cod_alumno_novedades desc', 'sined_alumno_novedades as t1, sys_usuario as t2');
                $desAnidada = armaTextAnidado($sined->_data);
                $data[0]["ult_gestiones"] = !empty($desAnidada) ? $desAnidada : array();
                
                $sined->get_datos('*,ind_acudiente as cod_respuestas', 'cod_alumno='.$data[0]["cod_alumno"] , 'sined_alumno_familiares');
                $camposComboEsp=$sined->_data;
                //var_dump($camposComboEsp);exit;
                $sined->get_datos('count(1) as ciclo', 'cod_alumno='.$data[0]["cod_alumno"], 'sined_alumno_familiares');
                $ciclo=$sined->_data;
                $cicloInput= $ciclo[0]["ciclo"] >0 ? $ciclo[0]["ciclo"] : 1 ;
            }
            $user_data = helper_user_data('nuevoRegistro');
            //var_dump($user_data);exit;
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            $_codEmpresa = isset($user_data["no_cod_empresa"]) ? $user_data["no_cod_empresa"] : 0 ;
            $_codGrupo   = isset($user_data["no_cod_grupo"]) ? $user_data["no_cod_grupo"] : 0 ;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_alumno'," cod_empresa in($_codEmpresa) and Estado<>'MPR' and cod_grupo=$_codGrupo",$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Docentes($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();$campoChek=array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_usuario=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                $arrayChek = array("ind_ayuda"=>$data[0]["ind_ayuda"],"ind_helpdesk"=>$data[0]["ind_helpdesk"]);  
                foreach($arrayChek as $llave=>$valorChek){
                    if($valorChek==1){
                       $campoChek[]=$llave; 
                    }
                }
                $sistema->get_datos('cod_perfil',"cod_usuario=".$argumentos[3],'sys_usuario_perfil'); !empty($sistema->_data) ? $data[0]["cod_perfil"]=$sistema->_data[0]["cod_perfil"] : $data[0]["cod_perfil"]=array();
                $sistema->get_datos('cod_menu',"cod_usuario=".$argumentos[3],'sys_usuario_menu');!empty($sistema->_data) ? $data[0]["cod_menu"]=$sistema->_data : $data[0]["cod_menu"]=array();
                $sistema->get_datos('cod_menu_sub',"cod_usuario=".$argumentos[3],'sys_usuario_menu_sub');!empty($sistema->_data) ? $data[0]["cod_menu_sub"]=$sistema->_data : $data[0]["cod_menu_sub"]=array();
                $sistema->get_datos('cod_empresa',"cod_usuario=".$argumentos[3],'sys_usuario_empresa'); !empty($sistema->_data) ?$data[0]["cod_empresa"]=$sistema->_data : $data[0]["cod_empresa"]=array();                      
                $camposCombo=array("cod_perfil"=>$data[0]["cod_perfil"],"cod_menu"=>$data[0]["cod_menu"],
                                   "cod_menu_sub"=>$data[0]["cod_menu_sub"],"cod_empresa"=>$data[0]["cod_empresa"]);                    
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $sistema->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_usuario',' cod_empresa in('.$cadEmp[0]['result'].') and ind_docente=1',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }	
    }
    
    public function CargaAcademica($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_carga_academica=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposComboEsp[0] = array("cod_materia" => $data[0]["cod_materia"],"cod_aula" => $data[0]["cod_aula"],"cod_grupo" => $data[0]["cod_grupo"],"cod_docente" => $data[0]["cod_docente"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            $sined->get_datos('bd_a_config', " cod_estado='AAA'",' sined_config');
            $anoLectivo = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_carga_academica',' cod_empresa in('.$cadEmp[0]['result'].') and Lectivo='.$anoLectivo[0]['bd_a_config'].'',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Familiares($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos("t1.cod_familiares,t1.cod_alumno,t2.*,concat(t3.ape1_alumno,' ',t3.ape2_alumno,' ',t3.nom1_alumno,' ',t3.nom2_alumno) as nom_alumno", "t1.cod_alumno_familiares=" . $argumentos[3] .' and t1.cod_familiares=t2.cod_familiares and t1.cod_alumno = t3.cod_alumno LIMIT 1', $argumentos[4] . ' as t1, sined_familiares as t2, sined_alumno as t3');
                $data = $sined->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_tipo_identificacion" => $data[0]["cod_tipo_identificacion"],"cod_ciudad_expedicion" => $data[0]["cod_ciudad_expedicion"],
                                     "cod_ciudad_residencia" => $data[0]["cod_ciudad_residencia"],"cod_estado" => $data[0]["cod_estado"],
                                     "cod_estado_civil" => $data[0]["cod_estado_civil"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_familiares',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Notas($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            $ciclo=array();$cicloInput=2;$dataFormGeneral=array();
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            if (!empty($argumentos[3])) {
                $user_data = helper_user_data('nuevoRegistro');
                $sined->get_datos("t1.*,concat(t3.ape1_alumno,' ',t3.ape2_alumno,' ',t3.nom1_alumno,' ',t3.nom2_alumno) as nom_alumno,t2.nom_materia,t4.nom_periodo_academico as nom_periodo", 
                                              ' t1.cod_alumno     =t3.cod_alumno 
                                                and t1.cod_materia=t2.cod_materia
                                                and t1.cod_periodo_academico=t4.cod_periodo_academico
                                                and t1.cod_grupo='.$user_data["cod_grupo"]. 
                                              ' and t1.cod_usuario='.$user_data["cod_usuario"] . 
                                              ' and t1.cod_materia='.$user_data["cod_carga_academica"] .
                                              ' and t1.cod_periodo_academico='.$user_data["cod_periodo_academico"]. 
                                              ' order by t3.ape1_alumno desc',
                                              ' sined_notas as t1, sined_alumno as t3, sined_materia as t2, sined_periodo_academico as t4');
                if(!empty($sined->_data)){
                    $met= "M";
                    $camposComboEsp=$sined->_data;
                }else{                                
                    $cadenaSql   ="(".$user_data["cod_carga_academica"].") cod_materia,
                                   (".$user_data["cod_periodo_academico"].") cod_periodo_academico,   
                                   (select nom_periodo_academico from sined_periodo_academico where cod_periodo_academico=".$user_data["cod_periodo_academico"].") nom_periodo,
                                   (select nom_materia from sined_materia where cod_materia=".$user_data["cod_carga_academica"].") nom_materia,
                                    '0.00' cuantitativa_notas, '0.00' fall_inj_notas, '0.00' com_soc_notas,
                                    cod_alumno,concat(ape1_alumno,' ',ape2_alumno,' ',nom1_alumno,' ',nom2_alumno) as nom_alumno,cod_grupo";
                    $sined->get_datos($cadenaSql, 'cod_grupo='.$user_data["cod_grupo"]. " and cod_estado='MAT' order by cod_alumno desc" , 'sined_alumno');
                    $camposComboEsp=$sined->_data;
                }
                $sined->get_datos('count(1) as ciclo', 'cod_grupo='.$user_data["cod_grupo"]. " and cod_estado='MAT'", 'sined_alumno');
                $ciclo=$sined->_data;
                $cicloInput=$ciclo[0]["ciclo"];
                
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $dataFormGeneral[0]["FORM_MET"] = $met=='M' ? base64_encode("editaRegistro"):$dataFormGeneral[0]["FORM_MET"] ;
            
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ValoracionDescriptiva($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            $ciclo=array();$cicloInput=2;$dataFormGeneral=array();
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            if (!empty($argumentos[3])) {
                $user_data = helper_user_data('nuevoRegistro');
                $sined->get_datos("t1.*,concat(t3.ape1_alumno,' ',t3.ape2_alumno,' ',t3.nom1_alumno,' ',t3.nom2_alumno) as nom_alumno,t4.nom_periodo_academico as nom_periodo", 
                                              ' t1.cod_alumno     =t3.cod_alumno 
                                                and t1.cod_periodo_academico=t4.cod_periodo_academico
                                                and t1.cod_grupo='.$user_data["cod_grupo"]. 
                                              ' and t1.cod_usuario='.$user_data["cod_usuario"] . 
                                              ' and t1.cod_periodo_academico='.$user_data["cod_periodo_academico"]. 
                                              ' order by t1.cod_alumno desc',
                                              ' sined_valoracion_descriptiva as t1, sined_alumno as t3, sined_periodo_academico as t4');
                if(!empty($sined->_data)){
                    $met= "M";
                    $camposComboEsp=$sined->_data;
                }else{                                
                    $cadenaSql   ="(".$user_data["cod_periodo_academico"].") cod_periodo_academico,   
                                   (select nom_periodo_academico from sined_periodo_academico where cod_periodo_academico=".$user_data["cod_periodo_academico"].") nom_periodo,
                                    '' des_valoracion_descriptiva,
                                    cod_alumno,concat(ape1_alumno,' ',ape2_alumno,' ',nom1_alumno,' ',nom2_alumno) as nom_alumno,cod_grupo";
                    $sined->get_datos($cadenaSql, 'cod_grupo='.$user_data["cod_grupo"]. " and cod_estado='MAT' order by cod_alumno desc" , 'sined_alumno');
                    exit;
                    $camposComboEsp=$sined->_data;
                }
                $sined->get_datos('count(1) as ciclo', 'cod_grupo='.$user_data["cod_grupo"]. " and cod_estado='MAT'", 'sined_alumno');
                $ciclo=$sined->_data;
                $cicloInput=$ciclo[0]["ciclo"];
                
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $dataFormGeneral[0]["FORM_MET"] = $met=='M' ? base64_encode("editaRegistro"):$dataFormGeneral[0]["FORM_MET"] ;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    public function PeriodoAcademico($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos('*', "cod_periodo_academico=" . $argumentos[3], $argumentos[4]);
                $data = $sined->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            $sined->get_datos('bd_a_config', " cod_estado='AAA'",' sined_config');
            $anoLectivo = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_periodo_academico',' cod_empresa in('.$cadEmp[0]['result'].') and Lectivo='.$anoLectivo[0]['bd_a_config'].'',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function PlanillasGeneradas($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            if (!empty($argumentos[3])) {
                $sined->get_datos("t1.*,concat(t3.ape1_alumno,' ',t3.ape2_alumno,' ',t3.nom1_alumno,' ',t3.nom2_alumno) as nom_alumno,t2.nom_materia,t4.nom_periodo_academico as nom_periodo", 
                                              ' t1.cod_alumno     =t3.cod_alumno 
                                                and t1.cod_materia=t2.cod_materia
                                                and t1.cod_periodo_academico=t4.cod_periodo_academico
                                                and t1.cod_notas='.$argumentos[3],
                                              ' sined_notas as t1, sined_alumno as t3, sined_materia as t2, sined_periodo_academico as t4');
                $camposComboEsp=$sined->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            $sined->get_datos('bd_a_config', " cod_estado='AAA'",' sined_config');
            $anoLectivo = $sined->_data;
            $sined->get_datos('num_periodo_academico', ' DATE(NOW()) BETWEEN fec_inicio_periodo_academico AND fec_fin_periodo_academico; ', 'sined_periodo_academico');
            $Periodo = !empty($sined->_data) ? $sined->_data[0]['num_periodo_academico'] : '0000-00-00';
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_planillas_generadas',' cod_empresa in('.$cadEmp[0]['result'].') and Lectivo='.$anoLectivo[0]['bd_a_config'].' and Periodo='.$Periodo,$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function VerificacionPlanillas($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            $sined->get_datos('bd_a_config', " cod_estado='AAA'",' sined_config');
            $anoLectivo = $sined->_data;
            $sined->get_datos('num_periodo_academico', ' DATE(NOW()) BETWEEN fec_inicio_periodo_academico AND fec_fin_periodo_academico; ', 'sined_periodo_academico');
            $Periodo = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_verificacion_planillas',' cod_empresa in('.$cadEmp[0]['result'].') and Lectivo='.$anoLectivo[0]['bd_a_config'].' and Periodo='.$Periodo[0]['num_periodo_academico'],$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function PlanillasDocente($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            $sined->get_datos('bd_a_config', " cod_estado='AAA'",' sined_config');
            $anoLectivo = $sined->_data;
            $sined->get_datos('num_periodo_academico', ' DATE(NOW()) BETWEEN fec_inicio_periodo_academico AND fec_fin_periodo_academico; ', 'sined_periodo_academico');
            $Periodo = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_verificacion_planillas_docente',' cod_empresa in('.$cadEmp[0]['result'].') and Lectivo='.$anoLectivo[0]['bd_a_config'].' and Periodo='.$Periodo[0]['num_periodo_academico'].' and cod_responsable='.Session::get('cod'),$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function BoletinesAcademicos($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            $ciclo=array();$cicloInput=2;$dataFormGeneral=array();$int_hor;
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            if (!empty($argumentos[3])) {
                $parametroAlumno=array();$parametrosNotas=array();
                $user_data = helper_user_data('nuevoRegistro');
                //var_dump($user_data);exit;
                $sined->get_datos('bd_a_config', " cod_estado='AAA'",' sined_config');
                $anoLectivo = $sined->_data[0]["bd_a_config"];
                $sined->get_datos('nom_periodo_academico', " cod_periodo_academico='".$user_data["cod_periodo_academico"]."'",' sined_periodo_academico');
                $periodo = $sined->_data[0]["nom_periodo_academico"];
                require_once ROOT . 'libs/class.fpdf.php';
                $pdf = new PDF(devuelveString($user_data["cod_tipoimpresion"], '*', 2),'mm',devuelveString($user_data["cod_tipoimpresion"], '*', 1));
                $sined->get_datos("FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",0),0) as matSuperior,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",1),0) as matAlto,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",2),0) as matBasico,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",3),0) as matBajo,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",4),1) as ProInd,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",5),1) as ProGru,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",6),1) as ComSoc,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",7),0) as Fallas,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",8),1) as EscFam,
                                   t1.cod_alumno,CONCAT(t1.ape1_alumno,' ',t1.ape2_alumno,' ',t1.nom1_alumno,' ',t1.nom2_alumno) as alumno, t2.nom_grupo", ' t1.cod_grupo='.$user_data["cod_grupo"]." and t1.bd_a_config=( select bd_a_config from sined_config where cod_estado='AAA') and t1.cod_grupo=t2.cod_grupo", 'sined_alumno as t1, sined_grupo as t2');
                $parametroAlumno=$sined->_data;
//                if($user_data["no_ruta"]!=""){
//                    $pdf->AliasNbPages();
//                    $pdf->AddPage(devuelveString($user_data["cod_tipoimpresion"], '*', 2),devuelveString($user_data["cod_tipoimpresion"], '*', 1));
//                }
                for($i=0;$i<count($parametroAlumno);$i++):
                    $pdf->AliasNbPages();
                    $pdf->AddPage(devuelveString($user_data["cod_tipoimpresion"], '*', 2),devuelveString($user_data["cod_tipoimpresion"], '*', 1));
                    $pdf->Cell(30, 6,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(130,6,'INFORME ACADEMICO Y DISCIPLINARIO '.$anoLectivo.' - '.$periodo,0,0,'C',false); 
                    $pdf->Ln(10);$pdf->Cell(30, 6,'');
                    $pdf->Cell(90,6,'ESTUDIANTE: '.$parametroAlumno[$i]["alumno"],0,0,'C',false);
                    $pdf->Cell(40,6,'GRUPO: '.$parametroAlumno[$i]["nom_grupo"],0,0,'C',false);
                    $pdf->Ln(10);$pdf->SetFont('Arial','',7);$pdf->Cell(15, 10,'');
                    $pdf->Cell(70,10,'AREAS FUNDAMENTALES Y OPTATIVAS',1,0,'C',false);
                    $pdf->Cell(20,5,'INTENSIDAD','TR',0,'C',false);
                    $pdf->Cell(40,5,'VALORACION CUANTITATIVA','T',0,'C',false);
                    $pdf->Cell(40,5,'VALORACION CUALITATIVA','TLR',0,'C',false);
                    $pdf->Ln(5);$pdf->Cell(85, 6,'');
                    $pdf->Cell(20,5,'HORARIA','BR',0,'C',false);
                    $pdf->Cell(40,5,'(DECRETO 1290/09) SIE',0,0,'C',false);
                    $pdf->Cell(40,5,'ESCALA NAL(DECRETO 1290)','LR',1,'C',false);$pdf->Ln(-5);
                    $sined->get_datos('t3.nom_materia,t2.int_horaria_carga_academica,FORMAT(t1.cuantitativa_notas,1) as cuantitativa_notas,t1.cualitativa_notas', ' t1.cod_materia=t3.cod_materia and t1.cod_materia=t2.cod_materia and t1.cod_grupo=t2.cod_grupo and t1.cod_grupo='.$user_data["cod_grupo"].' and t1.cod_periodo_academico='.$user_data["cod_periodo_academico"]." and t1.bd_a_config=( select bd_a_config from sined_config where cod_estado='AAA') and t1.cod_alumno=".$parametroAlumno[$i]["cod_alumno"].' and t1.cod_materia<>31 order by t3.cod_indice',' sined_notas as t1, sined_carga_academica as t2, sined_materia as t3');
                    $parametrosNotas=$sined->_data;
                    for($x=0;$x<count($parametrosNotas);$x++):
                        $pdf->Ln(5);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                        $pdf->Cell(70,5,$parametrosNotas[$x]["nom_materia"],1,0,'L',false);
                        $pdf->Cell(20,5,$int_hor=$parametrosNotas[$x]["int_horaria_carga_academica"]==0 ? '---' : $parametrosNotas[$x]["int_horaria_carga_academica"],1,0,'C',false);
                        $pdf->Cell(40,5,$parametrosNotas[$x]["cuantitativa_notas"],1,0,'C',false);
                        $pdf->Cell(40,5,$parametrosNotas[$x]["cualitativa_notas"],1,0,'C',false);
                    endfor;
                    // comportamiento social
                    $pdf->Ln(5);$pdf->Cell(15,5,'');
                    $pdf->Cell(70,5,'COMPORTAMIENTO SOCIAL',1,0,'L',false);
                    $pdf->Cell(20,5,'---',1,0,'C',false);
                    $pdf->Cell(40,5,$parametroAlumno[$i]["ComSoc"],1,0,'C',false);
                    $ComSoc = !empty($parametroAlumno[$i]["ComSoc"]) ? $parametroAlumno[$i]["ComSoc"] : 0;
                    $sined->get_datos('cualitativa_nota_equivalencia', $ComSoc . ' BETWEEN ini_nota_equivalencia AND fin_nota_equivalencia', 'sined_nota_equivalencia');
                    $equivComSoc=$sined->_data;
                    $pdf->Cell(40,5,$equivComSoc[0]["cualitativa_nota_equivalencia"],1,0,'C',false);
                    // Estadisticas
                    $pdf->Ln(10);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',7);
                    $pdf->Cell(170,6,'RESUMEN ESTADISTICO',1,0,'C',false);
                    $pdf->Ln(6);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',6);
                    $pdf->Cell(20,5,'AREAS SUPERIOR',1,0,'C',false);
                    $pdf->Cell(20,5,'AREAS ALTO',1,0,'C',false);
                    $pdf->Cell(20,5,'AREAS BASICO',1,0,'C',false);
                    $pdf->Cell(20,5,'AREAS BAJO',1,0,'C',false);
                    $pdf->Cell(30,5,'PROMEDIO INDIVIDUAL',1,0,'C',false);
                    $pdf->Cell(30,5,'PROMEDIO GRUPO',1,0,'C',false);
                    $pdf->Cell(30,5,'INASISTENCIAS',1,0,'C',false);
                    // Promedios, fallas
                    $pdf->Ln(5);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["matSuperior"],1,0,'C',false);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["matAlto"],1,0,'C',false);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["matBasico"],1,0,'C',false);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["matBajo"],1,0,'C',false);
                    $pdf->Cell(30,5,$parametroAlumno[$i]["ProInd"],1,0,'C',false);
                    $pdf->Cell(30,5,$parametroAlumno[$i]["ProGru"],1,0,'C',false);
                    $pdf->Cell(30,5,$fallas=$parametroAlumno[$i]["Fallas"]==0 ? '---' : $parametroAlumno[$i]["Fallas"],1,0,'C',false);
                    // Escuela de padres
                    $pdf->Ln(10);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(130,5,'ESCUELA FAMILIAR: Asistencia a talleres, citaciones, colaboración, compromiso con la institución.',1,0,'C',false);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["EscFam"],1,0,'C',false);
                    $EscFam = !empty($parametroAlumno[$i]["EscFam"]) ? $parametroAlumno[$i]["EscFam"] : 0;
                    $sined->get_datos('cualitativa_nota_equivalencia', $EscFam . ' BETWEEN ini_nota_equivalencia AND fin_nota_equivalencia', 'sined_nota_equivalencia');
                    $equivEscFam=$sined->_data;
                    $pdf->Cell(20,5,$equivEscFam[0]["cualitativa_nota_equivalencia"],1,0,'C',false);
                    // valoracion descriptiva
                    $pdf->Ln(10);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(170,5,'VALORACION DESCRIPTIVA',1,0,'C',false);
                    $pdf->Ln(5);$pdf->Cell(15,5,'');
                    $sined->get_datos('des_valoracion_descriptiva', ' cod_grupo='.$user_data["cod_grupo"].' and cod_periodo_academico='.$user_data["cod_periodo_academico"]." and bd_a_config=( select bd_a_config from sined_config where cod_estado='AAA') and cod_alumno=".$parametroAlumno[$i]["cod_alumno"], 'sined_valoracion_descriptiva');
                    $desValoracion= !empty($sined->_data) ? $sined->_data[0]["des_valoracion_descriptiva"] : 'Sin datos';
                    $pdf->MultiCell(170,5,utf8_decode($desValoracion),1,'J',false);
                    // compromiso
                    $pdf->Ln(0);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(170,5,'COMPROMISOS DEL ESTUDIANTE Y SU ACUDIENTE',1,0,'C',false);
                    $pdf->Ln(5);$pdf->Cell(15,5,'');
                    $pdf->MultiCell(170,50,'',1,'J',false);
                    // fecha
                    $pdf->Ln(2);$pdf->Cell(15,5,'');
                    setlocale(LC_TIME,"es_ES");
                    $pdf->Cell(170,5,'Manizales, '.strftime('%d de %B de  %Y').'.',0,0,'L',false);
                    // firmas
                    $pdf->Ln(20);$pdf->Cell(15,5,'');
                    $pdf->Cell(75,5,'RECTORA','',0,'L',false);
                    $pdf->Cell(20,5,'');
                    $pdf->Cell(75,5,'DIRECTOR DE GRUPO','',0,'L',false);
                    // Segundas firmas
                    $pdf->Ln(25);$pdf->Cell(15,5,'');
                    $pdf->Cell(75,5,'ESTUDIANTE','',0,'',false);
                    $pdf->Cell(20,5,'');
                    $pdf->Cell(75,5,'PADRE O ACUDIENTE','',0,'L',false);
                endfor;
                
                //if($user_data["no_ruta"]!=""){$pdf->Output($user_data["no_ruta"]."/".$parametroAlumno[0]["nom_grupo"].time().".pdf",'F');}
                $pdf->Output();
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_boletines_academico',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function CertificadosAcademicos($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            $ciclo=array();$cicloInput=2;$dataFormGeneral=array();$int_hor;
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            if (!empty($argumentos[3])) {
                $parametroAlumno=array();$parametrosNotas=array();
                $user_data = helper_user_data('nuevoRegistro');
                $sined->get_datos('bd_a_config', " cod_estado='AAA'",' sined_config');
                $anoLectivo = $sined->_data[0]["bd_a_config"];
                $sined->get_datos('nom_periodo_academico', " cod_periodo_academico='".$user_data["cod_periodo_academico"]."'",' sined_periodo_academico');
                $periodo = $sined->_data[0]["nom_periodo_academico"];
                require_once ROOT . 'libs/class.fpdf.certificados.php';
                $pdf = new PDF(devuelveString($user_data["cod_tipoimpresion"], '*', 2),'mm',devuelveString($user_data["cod_tipoimpresion"], '*', 1));
                $sined->get_datos("FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",0),0) as matSuperior,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",1),0) as matAlto,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",2),0) as matBasico,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",3),0) as matBajo,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",4),1) as ProInd,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",5),1) as ProGru,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",6),1) as ComSoc,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",7),0) as Fallas,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",8),1) as EscFam,
                                   t1.cod_alumno,CONCAT(t1.ape1_alumno,' ',t1.ape2_alumno,' ',t1.nom1_alumno,' ',t1.nom2_alumno) as alumno, t3.nom_tipo_identificacion,t1.num_ident_alumno, t2.nom_grupo", ' t1.cod_grupo='.$user_data["cod_grupo"]." and t1.bd_a_config=( select bd_a_config from sined_config where cod_estado='AAA') and t1.cod_grupo=t2.cod_grupo and t1.cod_tipo_identificacion=t3.cod_tipo_identificacion", 'sined_alumno as t1, sined_grupo as t2, sined_tipo_identificacion as t3');
                $parametroAlumno=$sined->_data;
//                if($user_data["no_ruta"]!=""){
//                    $pdf->AliasNbPages();
//                    $pdf->AddPage(devuelveString($user_data["cod_tipoimpresion"], '*', 2),devuelveString($user_data["cod_tipoimpresion"], '*', 1));
//                }
                for($i=0;$i<count($parametroAlumno);$i++):
                    $pdf->AliasNbPages();
                    $pdf->AddPage(devuelveString($user_data["cod_tipoimpresion"], '*', 2),devuelveString($user_data["cod_tipoimpresion"], '*', 1));
                    $pdf->Cell(30, 6,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(130,6,'La suscrita Rectora',0,0,'C',false);
                    $pdf->Ln(6);$pdf->Cell(30, 6, '');
                    $pdf->Cell(130,6,'CERTIFICA',0,0,'C',false);
                    $pdf->Ln(10);$pdf->Cell(15, 10, '');
                    $pdf->MultiCell(170,5,utf8_decode('Que el/la alumno/a '.$parametroAlumno[$i]["alumno"].', identificado/a con el documento de identidad '.$parametroAlumno[$i]["nom_tipo_identificacion"].' : '. $parametroAlumno[$i]["num_ident_alumno"].', cursó todas las áreas fundamentales y optativas del GRADO '.$parametroAlumno[$i]["nom_grupo"].', correspondientes al currículo establecido en el Proyecto Educativo Institucional en el año 2013, de acuerdo con la Ley 115/94 y demás normas emanadas por el Ministerio de Educación Nacional, habiendo obtenido la siguiente valoración según el Sistema Institucional de Evaluación y Promoción (Decreto 1290/09):'),0,'J',false);
                    $pdf->Ln(10);$pdf->SetFont('Arial','',7);$pdf->Cell(15, 10,'');
                    $pdf->Cell(70,10,'AREAS FUNDAMENTALES Y OPTATIVAS',1,0,'C',false);
                    $pdf->Cell(20,5,'INTENSIDAD','TR',0,'C',false);
                    $pdf->Cell(40,5,'VALORACION CUANTITATIVA','T',0,'C',false);
                    $pdf->Cell(40,5,'VALORACION CUALITATIVA','TLR',0,'C',false);
                    $pdf->Ln(5);$pdf->Cell(85, 6,'');
                    $pdf->Cell(20,5,'HORARIA','BR',0,'C',false);
                    $pdf->Cell(40,5,'(DECRETO 1290/09) SIE',0,0,'C',false);
                    $pdf->Cell(40,5,'ESCALA NAL(DECRETO 1290)','LR',1,'C',false);$pdf->Ln(-5);
                    $sined->get_datos('t3.nom_materia,t2.int_horaria_carga_academica,FORMAT(t1.cuantitativa_notas,1) as cuantitativa_notas,t1.cualitativa_notas', ' t1.cod_materia=t3.cod_materia and t1.cod_materia=t2.cod_materia and t1.cod_grupo=t2.cod_grupo and t1.cod_grupo='.$user_data["cod_grupo"].' and t1.cod_periodo_academico='.$user_data["cod_periodo_academico"]." and t1.bd_a_config=( select bd_a_config from sined_config where cod_estado='AAA') and t1.cod_alumno=".$parametroAlumno[$i]["cod_alumno"].' and t1.cod_materia<>31 order by t3.cod_indice',' sined_notas as t1, sined_carga_academica as t2, sined_materia as t3');
                    $parametrosNotas=$sined->_data;
                    for($x=0;$x<count($parametrosNotas);$x++):
                        $pdf->Ln(5);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                        $pdf->Cell(70,5,$parametrosNotas[$x]["nom_materia"],1,0,'L',false);
                        $pdf->Cell(20,5,$int_hor=$parametrosNotas[$x]["int_horaria_carga_academica"]==0 ? '---' : $parametrosNotas[$x]["int_horaria_carga_academica"],1,0,'C',false);
                        $pdf->Cell(40,5,$parametrosNotas[$x]["cuantitativa_notas"],1,0,'C',false);
                        $pdf->Cell(40,5,$parametrosNotas[$x]["cualitativa_notas"],1,0,'C',false);
                    endfor;
                    // comportamiento social
                    $pdf->Ln(5);$pdf->Cell(15,5,'');
                    $pdf->Cell(70,5,'COMPORTAMIENTO SOCIAL',1,0,'L',false);
                    $pdf->Cell(20,5,'---',1,0,'C',false);
                    $pdf->Cell(40,5,$parametroAlumno[$i]["ComSoc"],1,0,'C',false);
                    $ComSoc = !empty($parametroAlumno[$i]["ComSoc"]) ? $parametroAlumno[$i]["ComSoc"] : 0;
                    $sined->get_datos('cualitativa_nota_equivalencia', $ComSoc . ' BETWEEN ini_nota_equivalencia AND fin_nota_equivalencia', 'sined_nota_equivalencia');
                    $equivComSoc=$sined->_data;
                    $pdf->Cell(40,5,$equivComSoc[0]["cualitativa_nota_equivalencia"],1,0,'C',false);
                    // Estadisticas
                    $pdf->Ln(10);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',7);
                    $pdf->Cell(170,6,'RESUMEN ESTADISTICO',1,0,'C',false);
                    $pdf->Ln(6);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',6);
                    $pdf->Cell(85,5,'PROMEDIO INDIVIDUAL',1,0,'C',false);
                    $pdf->Cell(85,5,'PROMEDIO GRUPO',1,0,'C',false);
                    // Promedios, fallas
                    $pdf->Ln(5);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(85,5,$parametroAlumno[$i]["ProInd"],1,0,'C',false);
                    $pdf->Cell(85,5,$parametroAlumno[$i]["ProGru"],1,0,'C',false);
                    // Escuela de padres
                    $pdf->Ln(10);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(130,5,utf8_decode('ESCUELA FAMILIAR: Asistencia a talleres, citaciones, colaboración, compromiso con la institución.'),1,0,'C',false);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["EscFam"],1,0,'C',false);
                    $EscFam = !empty($parametroAlumno[$i]["EscFam"]) ? $parametroAlumno[$i]["EscFam"] : 0;
                    $sined->get_datos('cualitativa_nota_equivalencia', $EscFam . ' BETWEEN ini_nota_equivalencia AND fin_nota_equivalencia', 'sined_nota_equivalencia');
                    $equivEscFam=$sined->_data;
                    $pdf->Cell(20,5,$equivEscFam[0]["cualitativa_nota_equivalencia"],1,0,'C',false);
                    // valoracion descriptiva
                    $pdf->Ln(10);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(170,5,'VALORACION DESCRIPTIVA',1,0,'C',false);
                    $pdf->Ln(5);$pdf->Cell(15,5,'');
                    $sined->get_datos('des_valoracion_descriptiva', ' cod_grupo='.$user_data["cod_grupo"].' and cod_periodo_academico='.$user_data["cod_periodo_academico"]." and bd_a_config=( select bd_a_config from sined_config where cod_estado='AAA') and cod_alumno=".$parametroAlumno[$i]["cod_alumno"], 'sined_valoracion_descriptiva');
                    $desValoracion= !empty($sined->_data) ? $sined->_data[0]["des_valoracion_descriptiva"] : 'Sin datos';
                    $pdf->MultiCell(170,5,utf8_decode($desValoracion),1,'J',false);
                    // fecha
                    $pdf->Ln(6);$pdf->Cell(15,10,'');
                    $pdf->Cell(170,5,'SITUACION ACADEMICA FINAL: __________________________________',0,0,'L',false);
                    setlocale(LC_TIME,"es_ES");
                    $pdf->Ln(6);$pdf->Cell(15,10,'');
                    $pdf->Cell(170,5,'Manizales, '.strftime('%d de %B de  %Y').'.',0,0,'L',false);
                    // firmas
                    $pdf->Ln(30);$pdf->Cell(15,5,'');
                    $pdf->Cell(75,5,'RECTORA','',0,'L',false);
                    $pdf->Cell(20,5,'');
                    $pdf->Cell(75,5,'DIRECTOR DE GRUPO','',0,'L',false);
                endfor;
                //if($user_data["no_ruta"]!=""){$pdf->Output($user_data["no_ruta"]."/".$parametroAlumno[0]["nom_grupo"].time().".pdf",'F');}
                $pdf->Output();
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_boletines_academico',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function FichaMatricula($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            $ciclo=array();$cicloInput=2;$dataFormGeneral=array();$int_hor;
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            if (!empty($argumentos[3])) {
                $parametroAlumno=array();$parametrosNotas=array();
                $user_data = helper_user_data('nuevoRegistro');
                //var_dump($user_data);exit;
                $sined->get_datos('bd_a_config', " cod_estado='AAA'",' sined_config');
                $anoLectivo = $sined->_data[0]["bd_a_config"];
                $sined->get_datos('nom_periodo_academico', " cod_periodo_academico='".$user_data["cod_periodo_academico"]."'",' sined_periodo_academico');
                $periodo = $sined->_data[0]["nom_periodo_academico"];
                require_once ROOT . 'libs/class.fpdf.php';
                require_once ROOT . 'libs/class.fpdf.php';
                $pdf = new PDF(devuelveString($user_data["cod_tipoimpresion"], '*', 2),'mm',devuelveString($user_data["cod_tipoimpresion"], '*', 1));
                $sined->get_datos("FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",0),0) as matSuperior,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",1),0) as matAlto,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",2),0) as matBasico,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",3),0) as matBajo,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",4),1) as ProInd,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",5),1) as ProGru,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",6),1) as ComSoc,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",7),0) as Fallas,
                                   FORMAT(fbTraePromerioMateriaEstudiante(t1.cod_alumno,".$user_data["cod_periodo_academico"].",8),1) as EscFam,
                                   t1.cod_alumno,CONCAT(t1.ape1_alumno,' ',t1.ape2_alumno,' ',t1.nom1_alumno,' ',t1.nom2_alumno) as alumno, t2.nom_grupo", ' t1.cod_grupo='.$user_data["cod_grupo"]." and t1.bd_a_config=( select bd_a_config from sined_config where cod_estado='AAA') and t1.cod_grupo=t2.cod_grupo", 'sined_alumno as t1, sined_grupo as t2');
                $parametroAlumno=$sined->_data;
//                if($user_data["no_ruta"]!=""){
//                    $pdf->AliasNbPages();
//                    $pdf->AddPage(devuelveString($user_data["cod_tipoimpresion"], '*', 2),devuelveString($user_data["cod_tipoimpresion"], '*', 1));
//                }
                for($i=0;$i<count($parametroAlumno);$i++):
                    $pdf->AliasNbPages();
                    $pdf->AddPage(devuelveString($user_data["cod_tipoimpresion"], '*', 2),devuelveString($user_data["cod_tipoimpresion"], '*', 1));
                    $pdf->Cell(30, 6,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(130,6,'INFORME ACADEMICO Y DISCIPLINARIO '.$anoLectivo.' - '.$periodo,0,0,'C',false); 
                    $pdf->Ln(10);$pdf->Cell(30, 6,'');
                    $pdf->Cell(90,6,'ESTUDIANTE: '.$parametroAlumno[$i]["alumno"],0,0,'C',false);
                    $pdf->Cell(40,6,'GRUPO: '.$parametroAlumno[$i]["nom_grupo"],0,0,'C',false);
                    $pdf->Ln(10);$pdf->SetFont('Arial','',7);$pdf->Cell(15, 10,'');
                    $pdf->Cell(70,10,'AREAS FUNDAMENTALES Y OPTATIVAS',1,0,'C',false);
                    $pdf->Cell(20,5,'INTENSIDAD','TR',0,'C',false);
                    $pdf->Cell(40,5,'VALORACION CUANTITATIVA','T',0,'C',false);
                    $pdf->Cell(40,5,'VALORACION CUALITATIVA','TLR',0,'C',false);
                    $pdf->Ln(5);$pdf->Cell(85, 6,'');
                    $pdf->Cell(20,5,'HORARIA','BR',0,'C',false);
                    $pdf->Cell(40,5,'(DECRETO 1290/09) SIE',0,0,'C',false);
                    $pdf->Cell(40,5,'ESCALA NAL(DECRETO 1290)','LR',1,'C',false);$pdf->Ln(-5);
                    $sined->get_datos('t3.nom_materia,t2.int_horaria_carga_academica,FORMAT(t1.cuantitativa_notas,1) as cuantitativa_notas,t1.cualitativa_notas', ' t1.cod_materia=t3.cod_materia and t1.cod_materia=t2.cod_materia and t1.cod_grupo=t2.cod_grupo and t1.cod_grupo='.$user_data["cod_grupo"].' and t1.cod_periodo_academico='.$user_data["cod_periodo_academico"]." and t1.bd_a_config=( select bd_a_config from sined_config where cod_estado='AAA') and t1.cod_alumno=".$parametroAlumno[$i]["cod_alumno"].' and t1.cod_materia<>31 order by t3.cod_indice',' sined_notas as t1, sined_carga_academica as t2, sined_materia as t3');
                    $parametrosNotas=$sined->_data;
                    for($x=0;$x<count($parametrosNotas);$x++):
                        $pdf->Ln(5);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                        $pdf->Cell(70,5,$parametrosNotas[$x]["nom_materia"],1,0,'L',false);
                        $pdf->Cell(20,5,$int_hor=$parametrosNotas[$x]["int_horaria_carga_academica"]==0 ? '---' : $parametrosNotas[$x]["int_horaria_carga_academica"],1,0,'C',false);
                        $pdf->Cell(40,5,$parametrosNotas[$x]["cuantitativa_notas"],1,0,'C',false);
                        $pdf->Cell(40,5,$parametrosNotas[$x]["cualitativa_notas"],1,0,'C',false);
                    endfor;
                    // comportamiento social
                    $pdf->Ln(5);$pdf->Cell(15,5,'');
                    $pdf->Cell(70,5,'COMPORTAMIENTO SOCIAL',1,0,'L',false);
                    $pdf->Cell(20,5,'---',1,0,'C',false);
                    $pdf->Cell(40,5,$parametroAlumno[$i]["ComSoc"],1,0,'C',false);
                    $ComSoc = !empty($parametroAlumno[$i]["ComSoc"]) ? $parametroAlumno[$i]["ComSoc"] : 0;
                    $sined->get_datos('cualitativa_nota_equivalencia', $ComSoc . ' BETWEEN ini_nota_equivalencia AND fin_nota_equivalencia', 'sined_nota_equivalencia');
                    $equivComSoc=$sined->_data;
                    $pdf->Cell(40,5,$equivComSoc[0]["cualitativa_nota_equivalencia"],1,0,'C',false);
                    // Estadisticas
                    $pdf->Ln(10);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',7);
                    $pdf->Cell(170,6,'RESUMEN ESTADISTICO',1,0,'C',false);
                    $pdf->Ln(6);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',6);
                    $pdf->Cell(20,5,'AREAS SUPERIOR',1,0,'C',false);
                    $pdf->Cell(20,5,'AREAS ALTO',1,0,'C',false);
                    $pdf->Cell(20,5,'AREAS BASICO',1,0,'C',false);
                    $pdf->Cell(20,5,'AREAS BAJO',1,0,'C',false);
                    $pdf->Cell(30,5,'PROMEDIO INDIVIDUAL',1,0,'C',false);
                    $pdf->Cell(30,5,'PROMEDIO GRUPO',1,0,'C',false);
                    $pdf->Cell(30,5,'INASISTENCIAS',1,0,'C',false);
                    // Promedios, fallas
                    $pdf->Ln(5);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["matSuperior"],1,0,'C',false);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["matAlto"],1,0,'C',false);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["matBasico"],1,0,'C',false);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["matBajo"],1,0,'C',false);
                    $pdf->Cell(30,5,$parametroAlumno[$i]["ProInd"],1,0,'C',false);
                    $pdf->Cell(30,5,$parametroAlumno[$i]["ProGru"],1,0,'C',false);
                    $pdf->Cell(30,5,$fallas=$parametroAlumno[$i]["Fallas"]==0 ? '---' : $parametroAlumno[$i]["Fallas"],1,0,'C',false);
                    // Escuela de padres
                    $pdf->Ln(10);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(130,5,'ESCUELA FAMILIAR: Asistencia a talleres, citaciones, colaboración, compromiso con la institución.',1,0,'C',false);
                    $pdf->Cell(20,5,$parametroAlumno[$i]["EscFam"],1,0,'C',false);
                    $EscFam = !empty($parametroAlumno[$i]["EscFam"]) ? $parametroAlumno[$i]["EscFam"] : 0;
                    $sined->get_datos('cualitativa_nota_equivalencia', $EscFam . ' BETWEEN ini_nota_equivalencia AND fin_nota_equivalencia', 'sined_nota_equivalencia');
                    $equivEscFam=$sined->_data;
                    $pdf->Cell(20,5,$equivEscFam[0]["cualitativa_nota_equivalencia"],1,0,'C',false);
                    // valoracion descriptiva
                    $pdf->Ln(10);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(170,5,'VALORACION DESCRIPTIVA',1,0,'C',false);
                    $pdf->Ln(5);$pdf->Cell(15,5,'');
                    $sined->get_datos('des_valoracion_descriptiva', ' cod_grupo='.$user_data["cod_grupo"].' and cod_periodo_academico='.$user_data["cod_periodo_academico"]." and bd_a_config=( select bd_a_config from sined_config where cod_estado='AAA') and cod_alumno=".$parametroAlumno[$i]["cod_alumno"], 'sined_valoracion_descriptiva');
                    $desValoracion= !empty($sined->_data) ? $sined->_data[0]["des_valoracion_descriptiva"] : 'Sin datos';
                    $pdf->MultiCell(170,5,utf8_decode($desValoracion),1,'J',false);
                    // compromiso
                    $pdf->Ln(0);$pdf->Cell(15,5,'');$pdf->SetFont('Arial','',8);
                    $pdf->Cell(170,5,'COMPROMISOS DEL ESTUDIANTE Y SU ACUDIENTE',1,0,'C',false);
                    $pdf->Ln(5);$pdf->Cell(15,5,'');
                    $pdf->MultiCell(170,50,'',1,'J',false);
                    // fecha
                    $pdf->Ln(2);$pdf->Cell(15,5,'');
                    setlocale(LC_TIME,"es_ES");
                    $pdf->Cell(170,5,'Manizales, '.strftime('%d de %B de  %Y').'.',0,0,'L',false);
                    // firmas
                    $pdf->Ln(20);$pdf->Cell(15,5,'');
                    $pdf->Cell(75,5,'RECTORA','',0,'L',false);
                    $pdf->Cell(20,5,'');
                    $pdf->Cell(75,5,'DIRECTOR DE GRUPO','',0,'L',false);
                    // Segundas firmas
                    $pdf->Ln(25);$pdf->Cell(15,5,'');
                    $pdf->Cell(75,5,'ESTUDIANTE','',0,'',false);
                    $pdf->Cell(20,5,'');
                    $pdf->Cell(75,5,'PADRE O ACUDIENTE','',0,'L',false);
                endfor;
                
                //if($user_data["no_ruta"]!=""){$pdf->Output($user_data["no_ruta"]."/".$parametroAlumno[0]["nom_grupo"].time().".pdf",'F');}
                $pdf->Output();
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_boletines_academico',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function GestiondeValoracionDes($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            if (!empty($argumentos[3])) {
                $claveSql=" t1.*,t2.cod_alumno,concat(t2.ape1_alumno,' ',t2.ape2_alumno,' ',t2.nom1_alumno,' ',t2.nom2_alumno) as nom_alumno, 
                            t3.nom_periodo_academico as nom_periodo,t3.cod_periodo_academico";
                $sined->get_datos($claveSql, " t1.cod_alumno=t2.cod_alumno and t1.cod_periodo_academico=t3.cod_periodo_academico and t1.cod_valoracion_descriptiva=".$argumentos[3], $argumentos[4] . ' as t1, sined_alumno as t2, sined_periodo_academico as t3' );
                $camposComboEsp = $sined->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            $sined->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sined->_data;
            $sined->get_datos('bd_a_config', " cod_estado='AAA'",' sined_config');
            $anoLectivo = $sined->_data;
            $condicionSql=" cod_estado='AAA' AND bd_a_config= (SELECT t3.bd_a_config FROM sined_config as t3 WHERE t3.cod_estado='AAA') AND DATE(NOW()) BETWEEN fec_inicio_periodo_academico AND fec_fin_periodo_academico ";
            $sined->get_datos('cod_periodo_academico', $condicionSql, 'sined_periodo_academico');
            $periodoAca = $sined->_data;
            setVariables($sined,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sined_view_gestion_valoracion_descriptiva',' cod_empresa in('.$cadEmp[0]['result'].') and Lectivo='.$anoLectivo[0]['bd_a_config'].' and cod_usuario='.Session::get('cod').' and Periodo='.$periodoAca[0]["cod_periodo_academico"],$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sined','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function GenerarFichaMatriculaPDF($metodo, $argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/endodoncia/model/endodonciaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $endodoncia = new Modeloendodoncia();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            var_dump($argumentos);exit();
        }
    }

    public function nuevoRegistro($metodo) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $sined = new ModeloSined();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR' => 3,'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $user_data = helper_user_data($metodo);
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaTiposIdentificacion':
                    $campo = array('nom_tipo_identificacion' . "=");
                    $clave = array("'" . $user_data['nom_tipo_identificacion'] . "'");
                    $user_data['fec_tipo_identificacion'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTiposGenero':
                    $campo = array('nom_tipo_genero' . "=");
                    $clave = array("'" . $user_data['nom_tipo_genero'] . "'");
                    $user_data['fec_tipo_genero'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTiposCaracter':
                    $campo = array('nom_ia_caracter' . "=");
                    $clave = array("'" . $user_data['nom_ia_caracter'] . "'");
                    $user_data['fec_ia_caracter'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTiposEspecialidad':
                    $campo = array('nom_ia_especialidad' . "=");
                    $clave = array("'" . $user_data['nom_ia_especialidad'] . "'");
                    $user_data['fec_ia_especialidad'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTiposContrato':
                    $campo = array('nom_ia_contrato' . "=");
                    $clave = array("'" . $user_data['nom_ia_contrato'] . "'");
                    $user_data['fec_ia_contrato'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaEps':
                    $campo = array('nom_iss_eps' . "=");
                    $clave = array("'" . $user_data['nom_iss_eps'] . "'");
                    $user_data['fec_iss_eps'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaArs':
                    $campo = array('nom_iss_ars' . "=");
                    $clave = array("'" . $user_data['nom_iss_ars'] . "'");
                    $user_data['fec_iss_ars'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTipoSangre':
                    $campo = array('nom_iss_tiposangre' . "=");
                    $clave = array("'" . $user_data['nom_iss_tiposangre'] . "'");
                    $user_data['fec_iss_tiposangre'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaSituacionesAcademicas':
                    //var_dump($user_data);exit;
                    $campo = array('nom_alumno_situacion' . "=");
                    $clave = array("'" . $user_data['nom_alumno_situacion'] . "'");
                    $user_data['fec_alumno_situacion'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTipoPoblacion':
                    $campo = array('nom_id_poblacion' . "=");
                    $clave = array("'" . $user_data['nom_id_poblacion'] . "'");
                    $user_data['fec_id_poblacion'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaNivelSisben':
                    $campo = array('nom_ise_nivelsisben' . "=");
                    $clave = array("'" . $user_data['nom_ise_nivelsisben'] . "'");
                    $user_data['fec_ise_nivelsisben'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaFuenteRecursos':
                    $campo = array('nom_ise_fuenterecursos' . "=");
                    $clave = array("'" . $user_data['nom_ise_fuenterecursos'] . "'");
                    $user_data['fec_ise_fuenterecursos'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaResguardo':
                    $campo = array('nom_ite_resguardo' . "=");
                    $clave = array("'" . $user_data['nom_ite_resguardo'] . "'");
                    $user_data['fec_ite_resguardo'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaEtnia':
                    $campo = array('nom_ite_etnia' . "=");
                    $clave = array("'" . $user_data['nom_ite_etnia'] . "'");
                    $user_data['fec_ite_etnia'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaEquivalencia':
                    $campo = array('ini_nota_equivalencia=' , ' and fin_nota_equivalencia=');
                    $clave = array("'" . $user_data['ini_nota_equivalencia'] . "'","'" . $user_data['fin_nota_equivalencia'] . "'");
                    $user_data['fec_nota_equivalencia'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfiguracionGeneralSined':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                break;
                case 'NuevaMateriasAsignaturas':
                    $campo = array('nom_materia' . "=");
                    $clave = array("'" . $user_data['nom_materia'] . "'");
                    $user_data['fec_materia'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaTipoParentesco':
                    $campo = array('nom_parentesco' . "=");
                    $clave = array("'" . $user_data['nom_parentesco'] . "'");
                    $user_data['fec_parentesco'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaTipoEstCivil':
                    $campo = array('nom_estado_civil' . "=");
                    $clave = array("'" . $user_data['nom_estado_civil'] . "'");
                    $user_data['fec_estado_civil'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaPreinscripciones':
                    $user_data["cod_grupo"] = isset($user_data["no_cod_grupo"]) ? $user_data["no_cod_grupo"] : '';
                    $campo = array('num_ident_alumno' . "=");
                    $clave = array("'" . $user_data['num_ident_alumno'] . "'");
                    $user_data['fec_alumno'] = date("Y-m-d H:i:s");
                    $user_data['fec_mod_estado'] = date("Y-m-d H:i:s"); 
                    $user_data['cod_estado_matricula'] = $user_data['cod_grupo']!='' ? 'MAT' : 'MPR';
                    $user_data['cod_estado'] = $user_data['cod_estado_matricula'];
                    // var_dump($user_data);exit;
                break;
                case 'NuevaCapacidadesExc':
                    $campo = array('nom_capacidades' . "=");
                    $clave = array("'" . $user_data['nom_capacidades'] . "'");
                    $user_data['fec_capacidades'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaDiscapacidadesExc':
                    $campo = array('nom_discapacidades' . "=");
                    $clave = array("'" . $user_data['nom_discapacidades'] . "'");
                    $user_data['fec_discapacidades'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaDocentes':
                    //var_dump($user_data);exit;
                    $campo = array('nom_usuario=',' and email_usuario=');
                    $clave = array("'" . $user_data['nom_usuario'] . "'","'" . $user_data['email_usuario'] . "'");
                    $user_data["ind_docente"]=1;
                break;
                case 'NuevaAulas':
                    $campo = array('nom_aula' . "=");
                    $clave = array("'" . $user_data['nom_aula'] . "'");
                    $user_data['fec_aula'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaTipoGrupo':
                    $campo = array('nom_tipo_grupo' . "=");
                    $clave = array("'" . $user_data['nom_tipo_grupo'] . "'");
                    $user_data['fec_tipo_grupo'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaGrupo':
                    $campo = array('nom_grupo' . "=");
                    $clave = array("'" . $user_data['nom_grupo'] . "'");
                    $user_data['fec_grupo'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaFamiliares':
                    $campo = array('num_ident_familiares' . "=");
                    $clave = array("'" . $user_data['num_ident_familiares'] . "'");
                    $user_data['fec_familiares'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaCargaAcademica':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_docente"]);$e++){
                        $campo = array('cod_docente=',' and cod_materia=',' and cod_grupo=');
                        $clave = array("'".$user_data['no_cod_docente'][$e]."'","'".$user_data['no_cod_materia'][$e]."'","'".$user_data['no_cod_grupo'][$e]."'");
                        if(!$sined->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('cod_docente'                 =>$user_data["no_cod_docente"][$e],
                                              'cod_materia'                 =>$user_data["no_cod_materia"][$e],
                                              'int_horaria_carga_academica' =>$user_data["no_int_horaria_carga_academica"][$e],
                                              'cod_aula'                    =>$user_data["no_cod_aula"][$e],
                                              'cod_grupo'                   =>$user_data["no_cod_grupo"][$e],
                                              'no_nom_tabla'                =>$user_data['no_nom_tabla'],
                                              'no_esq_tabla'                =>$user_data['no_esq_tabla'],
                                              'no_id_formulario'            =>$user_data['no_id_formulario'],
                                              'cod_usuario'                 =>'');
                            $sined->setRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    }	
                    $sined->err = 6;
                    $sined->msj = " Carga Academica Asignada !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaPeriodoAcademico':
                    $campo = array('nom_periodo_academico=',' and num_periodo_academico=');
                    $clave = array("'" . $user_data['nom_periodo_academico'] . "'","'" . $user_data['num_periodo_academico'] . "'");
                    $user_data['fec_periodo_academico'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaNotas':
                    //var_dump($user_data);exit;
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_alumno"]);$e++):
                        $campo = array('cod_alumno=',' and cod_grupo=',' and cod_periodo_academico=', ' and cod_materia=');
                        $clave = array("'".$user_data['no_cod_alumno'][$e]."'","'".$user_data['no_cod_grupo'][$e]."'","'".$user_data['no_cod_periodo_academico'][$e]."'","'".$user_data['no_cod_materia'][$e]."'");
                        if(!$sined->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('cod_alumno'                  =>$user_data["no_cod_alumno"][$e],
                                              'cod_materia'                 =>$user_data["no_cod_materia"][$e],
                                              'cod_grupo'                   =>$user_data["no_cod_grupo"][$e],
                                              'cod_periodo_academico'       =>$user_data["no_cod_periodo_academico"][$e],
                                              'cuantitativa_notas'          =>  str_replace(',','.',$user_data["no_cuantitativa_notas"][$e]),
                                              'fall_inj_notas'              =>$user_data["no_fall_inj_notas"][$e],
                                              'com_soc_notas'              =>$user_data["no_com_soc_notas"][$e],
                                              'no_nom_tabla'                =>$user_data['no_nom_tabla'],
                                              'no_esq_tabla'                =>$user_data['no_esq_tabla'],
                                              'no_id_formulario'            =>$user_data['no_id_formulario'],
                                              'cod_usuario'                 =>'');
                            $sined->setRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                        if($e==count($user_data["no_cod_alumno"])-1):
                            $sined->set_simple_query("UPDATE sined_planilla_periodo 
                                                         SET ind_generada=1,cod_usuario_transaccion=".Session::get('cod').",fec_planilla_periodo='".date("Y-m-d H:i:s")."'
                                                       WHERE cod_grupo            =".$user_data["no_cod_grupo"][$e]." 
                                                         AND cod_materia          =".$user_data["no_cod_materia"][$e]."
                                                         AND bd_a_config          = (SELECT bd_a_config
                                                                                      FROM sined_config
						                                     WHERE cod_estado='AAA')
                                                         AND cod_periodo_academico=".$user_data["no_cod_periodo_academico"][$e]."");
                        endif;
                    endfor;	
                    $sined->err = 6;
                    $sined->msj = " Planilla Guardada !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaValoracionDescriptiva':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_alumno"]);$e++):
                        $campo = array('cod_alumno=',' and cod_grupo=',' and cod_periodo_academico=');
                        $clave = array("'".$user_data['no_cod_alumno'][$e]."'","'".$user_data['no_cod_grupo'][$e]."'","'".$user_data['no_cod_periodo_academico'][$e]."'");
                        if(!$sined->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('cod_alumno'                  =>$user_data["no_cod_alumno"][$e],
                                              'cod_grupo'                   =>$user_data["no_cod_grupo"][$e],
                                              'cod_periodo_academico'       =>$user_data["no_cod_periodo_academico"][$e],
                                              'des_valoracion_descriptiva'  =>$user_data["no_des_valoracion_descriptiva"][$e],
                                              'no_nom_tabla'                =>$user_data['no_nom_tabla'],
                                              'no_esq_tabla'                =>$user_data['no_esq_tabla'],
                                              'no_id_formulario'            =>$user_data['no_id_formulario'],
                                              'cod_usuario'                 =>'');
                            $sined->setRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    endfor;	
                    $sined->err = 6;
                    $sined->msj = " Planilla Guardada !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
            }
            if ($valida) {
                if (!$sined->getRegistro($user_data['no_esq_tabla'], $campo, $clave)) {
                    $sined->setRegistro($user_data);
                    $data = array('ERR' => 6,
                                  'MSJ' => $sined->msj);
                    setVariables($sined,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $sined->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$sined->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                } else {
                    $data = array('ERR' => 2,
                                  'MSJ' => "El registro ya existe");                    
                    setVariables($sined,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $sined->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$sined->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                }
            }else{
                $data= array('ERR' => $sined->err,
                             'MSJ' => $sined->msj);
                setVariables($sined,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $sined->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$sined->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
    
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sined/model/sinedModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $sined  = new ModeloSined();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sined->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sined->_data;
            //var_dump($sistema->_data);exit();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaTiposIdentificacion':
                    $campo = array('nom_tipo_identificacion' . "=");
                    $clave = array("'" . $user_data['nom_tipo_identificacion'] . "'");
                    $user_data['fec_mod_tipo_identificacion'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTiposGenero':
                    $campo = array('nom_tipo_genero' . "=");
                    $clave = array("'" . $user_data['nom_tipo_genero'] . "'");
                    $user_data['fec_mod_tipo_genero'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTiposCaracter':
                    $campo = array('nom_ia_caracter' . "=");
                    $clave = array("'" . $user_data['nom_ia_caracter'] . "'");
                    $user_data['fec_mod_ia_caracter'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTiposEspecialidad':
                    $campo = array('nom_ia_especialidad' . "=");
                    $clave = array("'" . $user_data['nom_ia_especialidad'] . "'");
                    $user_data['fec_mod_ia_especialidad'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTiposContrato':
                    $campo = array('nom_ia_contrato' . "=");
                    $clave = array("'" . $user_data['nom_ia_contrato'] . "'");
                    $user_data['fec_mod_ia_contrato'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaEps':
                    $campo = array('nom_iss_eps' . "=");
                    $clave = array("'" . $user_data['nom_iss_eps'] . "'");
                    $user_data['fec_mod_iss_eps'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaArs':
                    $campo = array('nom_iss_ars' . "=");
                    $clave = array("'" . $user_data['nom_iss_ars'] . "'");
                    $user_data['fec_mod_iss_ars'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTipoSangre':
                    $campo = array('nom_iss_tiposangre' . "=");
                    $clave = array("'" . $user_data['nom_iss_tiposangre'] . "'");
                    $user_data['fec_mod_iss_tiposangre'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaSituacionesAcademicas':
                    $campo = array('nom_alumno_situacion' . "=");
                    $clave = array("'" . $user_data['nom_alumno_situacion'] . "'");
                    $user_data['fec_mod_alumno_situacion'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTipoPoblacion':
                    $campo = array('nom_id_poblacion' . "=");
                    $clave = array("'" . $user_data['nom_id_poblacion'] . "'");
                    $user_data['fec_mod_id_poblacion'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaNivelSisben':
                    $campo = array('nom_ise_nivelsisben' . "=");
                    $clave = array("'" . $user_data['nom_ise_nivelsisben'] . "'");
                    $user_data['fec_mod_ise_nivelsisben'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaFuenteRecursos':
                    $campo = array('nom_ise_fuenterecursos' . "=");
                    $clave = array("'" . $user_data['nom_ise_fuenterecursos'] . "'");
                    $user_data['fec_mod_ise_fuenterecursos'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaResguardo':
                    $campo = array('nom_ite_resguardo' . "=");
                    $clave = array("'" . $user_data['nom_ite_resguardo'] . "'");
                    $user_data['fec_mod_ite_resguardo'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaEtnia':
                    $campo = array('nom_ite_etnia' . "=");
                    $clave = array("'" . $user_data['nom_ite_etnia'] . "'");
                    $user_data['fec_mod_ite_etnia'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaEquivalencia':
                    $campo = array('ini_nota_equivalencia=' , ' and fin_nota_equivalencia=');
                    $clave = array("'" . $user_data['ini_nota_equivalencia'] . "'","'" . $user_data['fin_nota_equivalencia'] . "'");
                    $user_data['fec_mod_nota_equivalencia'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfiguracionGeneralSined':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                    $user_data["ind_imp_logros"]=isset($user_data["ind_imp_logros"]) ? $user_data["ind_imp_logros"] : "0";
                break;
                case 'NuevaMateriasAsignaturas':
                    $campo = array('nom_materia' . "=");
                    $clave = array("'" . $user_data['nom_materia'] . "'");
                    $user_data['fec_mod_materia'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaTipoParentesco':
                    $campo = array('nom_tipo_parentesco' . "=");
                    $clave = array("'" . $user_data['nom_parentesco'] . "'");
                    $user_data['fec_mod_parentesco'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaTipoEstCivil':
                    $campo = array('nom_estado_civil' . "=");
                    $clave = array("'" . $user_data['nom_estado_civil'] . "'");
                    $user_data['fec_mod_estado_civil'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaCapacidadesExc':
                    $campo = array('nom_capacidades' . "=");
                    $clave = array("'" . $user_data['nom_capacidades'] . "'");
                    $user_data['fec_mod_capacidades'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaDiscapacidadesExc':
                    $campo = array('nom_discapacidades' . "=");
                    $clave = array("'" . $user_data['nom_discapacidades'] . "'");
                    $user_data['fec_mod_discapacidades'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaUsuario':
                    $campo = array('nom_usuario=',' and email_usuario=');
                    $clave = array("'" . $user_data['nom_usuario'] . "'","'" . $user_data['email_usuario'] . "'");
                    $user_data["ind_docente"]=1;
                break;
                case 'NuevaAulas':
                    $campo = array('nom_aula' . "=");
                    $clave = array("'" . $user_data['nom_aula'] . "'");
                    $user_data['fec_mod_aula'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaTipoGrupo':
                    $campo = array('nom_tipo_grupo' . "=");
                    $clave = array("'" . $user_data['nom_tipo_grupo'] . "'");
                    $user_data['fec_mod_tipo_grupo'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaGrupo':
                    $campo = array('nom_grupo' . "=");
                    $clave = array("'" . $user_data['nom_grupo'] . "'");
                    $user_data['fec_mod_grupo'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaPreinscripciones':
                    //var_dump($user_data);exit;
                    $user_data["cod_grupo"] = isset($user_data["no_cod_grupo"]) ? $user_data["no_cod_grupo"] : '';
                    $campo = array('num_ident_alumno' . "=");
                    $clave = array("'" . $user_data['num_ident_alumno'] . "'");
                    $user_data['cod_estado_matricula'] = $user_data['cod_grupo']!='' ? 'MAT' : 'MPR';
                    $user_data["cod_tipo_novedad"] = $user_data['cod_estado_matricula']=='MAT' ? 10 : '';
                    $user_data['cod_estado'] = $user_data['cod_estado_matricula'];
                    $user_data['fec_mod_alumno'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaMatriculas':
                    if(isset($user_data["no_ind_cambio_grupo"])):
                        $user_data["cod_tipo_novedad"]=5;
                    endif;
                    if(isset($user_data["no_ind_cambio_estado"])):
                        $user_data["cod_tipo_novedad"]=6;
                        $user_data["cod_estado"] = $user_data["no_cod_estado"];
                        $user_data['cod_estado_matricula'] = $user_data['cod_estado'];
                    endif;
                    if(isset($user_data["no_ind_cambio_grupo"]) AND isset($user_data["no_ind_cambio_estado"])):
                        $user_data["cod_tipo_novedad"]=8;
                        $user_data["cod_estado"] = $user_data["no_cod_estado"];
                        $user_data['cod_estado_matricula'] = $user_data['cod_estado'];
                    endif;
                    if(!isset($user_data["no_ind_cambio_grupo"]) AND !isset($user_data["no_ind_cambio_estado"])):
                        $user_data["cod_tipo_novedad"]=1;
                    endif;
                    $user_data['fec_mod_alumno'] = date("Y-m-d H:i:s");
                    //var_dump($user_data);exit;
                break;
                case 'NuevaFamiliares':
                    $campo = array('num_ident_familiares' . "=");
                    $clave = array("'" . $user_data['num_ident_familiares'] . "'");
                    $user_data['fec_mod_familiares'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaCargaAcademica':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_docente"]);$e++){
                        $campo = array('cod_docente=',' and cod_materia=',' and cod_grupo=');
                        $clave = array("'".$user_data['no_cod_docente'][$e]."'","'".$user_data['no_cod_materia'][$e]."'","'".$user_data['no_cod_grupo'][$e]."'");
                        $user_data1=array('cod_docente'                 =>$user_data["no_cod_docente"][$e],
                                          'cod_materia'                 =>$user_data["no_cod_materia"][$e],
                                          'int_horaria_carga_academica' =>$user_data["no_int_horaria_carga_academica"][$e],
                                          'cod_aula'                    =>$user_data["no_cod_aula"][$e],
                                          'cod_grupo'                   =>$user_data["no_cod_grupo"][$e],
                                          'cod_carga_academica'         =>$user_data["no_cod_carga_academica"][$e],
                                          'no_nom_tabla'                =>$user_data['no_nom_tabla'],
                                          'no_esq_tabla'                =>$user_data['no_esq_tabla'],
                                          'no_id_formulario'            =>$user_data['no_id_formulario'],
                                          'cod_usuario'                 =>'');
                        $sined->editRegistro($user_data1);
                        $sinErr += + 1;
                    }	
                    $sined->err = 6;
                    $sined->msj = " Carga Academica Modificada !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaPlanillasGeneradas':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_notas"]);$e++){
                        $user_data1=array('cod_notas'                   =>$user_data["no_cod_notas"][$e],
                                          'cuantitativa_notas'          =>str_replace(',','.',$user_data["no_cuantitativa_notas"][$e]),
                                          'fall_inj_notas'              =>$user_data["no_fall_inj_notas"][$e],
                                          'com_soc_notas'               =>$user_data["no_com_soc_notas"][$e],
                                          'no_nom_tabla'                =>$user_data['no_nom_tabla'],
                                          'no_esq_tabla'                =>$user_data['no_esq_tabla'],
                                          'fec_mod_notas'               =>date("Y-m-d H:i:s"),
                                          'cod_usuario_mod'             =>Session::get('cod'));
                        $sined->editRegistro($user_data1);
                        $sinErr += + 1;
                    }	
                    $sined->err = 6;
                    $sined->msj = " Nota Modificada !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaPeriodoAcademico':
                    $campo = array('nom_periodo_academico='.' and num_periodo_academico=');
                    $clave = array("'" . $user_data['nom_periodo_academico'] . "'","'" . $user_data['num_periodo_academico'] . "'");
                    $user_data['fec_mod_periodo_academico'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaValoracionDescriptiva':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_alumno"]);$e++){
                        $user_data1=array('cod_valoracion_descriptiva'    =>$user_data["no_cod_valoracion_descriptiva"][$e],
                                          'cod_alumno'                    =>$user_data["no_cod_alumno"][$e],
                                          'cod_grupo'                     =>$user_data["no_cod_grupo"][$e],
                                          'cod_periodo_academico'         =>$user_data["no_cod_periodo_academico"][$e],
                                          'des_valoracion_descriptiva'    =>$user_data["no_des_valoracion_descriptiva"][$e],
                                          'no_nom_tabla'                  =>$user_data['no_nom_tabla'],
                                          'no_esq_tabla'                  =>$user_data['no_esq_tabla'],
                                          'no_id_formulario'              =>$user_data['no_id_formulario'],
                                          'fec_mod_valoracion_descriptiva'=>date("Y-m-d H:i:s"),
                                          'cod_usuario_mod'               =>Session::get('cod'));
                        $sined->editRegistro($user_data1);
                        $sinErr += + 1;
                    }	
                    $sined->err = 6;
                    $sined->msj = " Observacion Modificada !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaGestiondeValoracionDes':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_alumno"]);$e++){
                        $user_data1=array('cod_valoracion_descriptiva'    =>$user_data["no_cod_valoracion_descriptiva"][$e],
                                          'cod_alumno'                    =>$user_data["no_cod_alumno"][$e],
                                          'cod_grupo'                     =>$user_data["no_cod_grupo"][$e],
                                          'cod_periodo_academico'         =>$user_data["no_cod_periodo_academico"][$e],
                                          'des_valoracion_descriptiva'    =>$user_data["no_des_valoracion_descriptiva"][$e],
                                          'no_nom_tabla'                  =>$user_data['no_nom_tabla'],
                                          'no_esq_tabla'                  =>$user_data['no_esq_tabla'],
                                          'no_id_formulario'              =>$user_data['no_id_formulario'],
                                          'fec_mod_valoracion_descriptiva'=>date("Y-m-d H:i:s"),
                                          'cod_usuario_mod'               =>Session::get('cod'));
                        $sined->editRegistro($user_data1);
                        $sinErr += + 1;
                    }	
                    $sined->err = 6;
                    $sined->msj = " Observacion Modificada !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaNotas':
                    //var_dump($user_data);exit;
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_alumno"]);$e++):
                        $user_data1=array('cod_notas'                   =>$user_data["no_cod_notas"][$e],
                                          'cod_alumno'                  =>$user_data["no_cod_alumno"][$e],
                                          'cod_materia'                 =>$user_data["no_cod_materia"][$e],
                                          'cod_grupo'                   =>$user_data["no_cod_grupo"][$e],
                                          'cod_periodo_academico'       =>$user_data["no_cod_periodo_academico"][$e],
                                          'cuantitativa_notas'          =>str_replace(',','.',$user_data["no_cuantitativa_notas"][$e]),
                                          'fall_inj_notas'              =>$user_data["no_fall_inj_notas"][$e],
                                          'com_soc_notas'               =>$user_data["no_com_soc_notas"][$e],
                                          'no_nom_tabla'                =>$user_data['no_nom_tabla'],
                                          'no_esq_tabla'                =>$user_data['no_esq_tabla'],
                                          'no_id_formulario'            =>$user_data['no_id_formulario'],
                                          'cod_usuario'                 =>'');
                        $sined->editRegistro($user_data1);
                        $sinErr += + 1;
                        if($e==count($user_data["no_cod_alumno"])-1):
                            $sined->set_simple_query("UPDATE sined_planilla_periodo 
                                                         SET fec_planilla_periodo='".date("Y-m-d H:i:s")."'
                                                       WHERE cod_grupo            =".$user_data["no_cod_grupo"][$e]." 
                                                         AND cod_materia          =".$user_data["no_cod_materia"][$e]."
                                                         AND bd_a_config          = (SELECT bd_a_config
                                                                                      FROM sined_config
						                                     WHERE cod_estado='AAA')
                                                         AND cod_periodo_academico=".$user_data["no_cod_periodo_academico"][$e]."");
                        endif;
                    endfor;	
                    $sined->err = 6;
                    $sined->msj = " Planilla Editada !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
            }
            if($valida){
                $sined->editRegistro($user_data);
                $data = array('ERR'=>6,
                              'MSJ'=>$sined->msj);
                setVariables($sined,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $sined->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$sined->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data= array('ERR' => $sined->err,
                             'MSJ' => $sined->msj);
                setVariables($sined,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $sined->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$sined->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
}

?>