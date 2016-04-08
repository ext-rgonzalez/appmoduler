<?php

class endodonciaController{
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracion de usuario desde el modelo propio del
//             controlador.
    public function index() {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/endodoncia/model/endodonciaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        if (!Session::get('usuario')) {
            $Objvista = new view;
            $data = array('ERR' => 3,
                          'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $endodoncia->get_datos('fbDevuelveArchivos(275,1) as ARCHIVOSCSS');
            $Objvista->_archivos_css = $endodoncia->_data;
            $endodoncia->get_datos('fbDevuelveArchivos(275,2) as ARCHIVOSSCRIPT');
            $Objvista->_archivos_js  = $endodoncia->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $endodoncia = new Modeloendodoncia();
            $Objvista = new view;
            $endodoncia->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa = $endodoncia->_empresa;
            $var = "";
            for ($t = 0; $t < count($endodoncia->_notificacion); $t++) {
                $var = $var . $endodoncia->_notificacion[$t]['des_notificacion'];
            }
            $Objvista->_notificacion = array('des_notificacion' => $var);
            $Objvista->_numNotificacion = $endodoncia->_numNotificacion;
            $var = "";
            for ($t = 0; $t < count($endodoncia->_mensajes); $t++) {
                $var = $var . $endodoncia->_mensajes[$t]['des_mensajes'];
            }
            $Objvista->_mensajes = array('des_mensajes' => $var);
            $Objvista->_numMensajes = $endodoncia->_numMensajes;
            $var = "";
            for ($t = 0; $t < count($endodoncia->_menu); $t++) {
                $var = $var . $endodoncia->_menu[$t]['menu'];
            }
            $Objvista->_menu = array('menu' => $var);
            $Objvista->_menuHeader = $endodoncia->_menuHeader;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'index', 'index');
        }
    }

    public function configEndodoncia($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfigDental($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_dental=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposComboEsp[0] = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_imagen_dental"=>$data[0]["cod_imagen_dental"]);
                $arrayChek = array("ind_temporales" => $data[0]["ind_temporales"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            $endodoncia->get_datos('img_config,img1_config', " cod_estado='AAA'", 'endodoncia_config');
            $data["IMGODONTOGRAMA"] = ENDO_DIR_ADJ . $endodoncia->_data[0]["img_config"];
            $data["IMGODONTOGRAMA_1"] = ENDO_DIR_ADJ . $endodoncia->_data[0]["img1_config"];
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_dental',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfigNomenclatura($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_nomenclatura=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_config_dental" => $data[0]["cod_config_dental"]);
                $arrayChek = array("lap_d" => $data[0]["lap_d"],"lap_p" => $data[0]["lap_p"],"lap_ml" => $data[0]["lap_ml"],"lap_mv" => $data[0]["lap_mv"],"lap_dl" => $data[0]["lap_dl"],"lap_dv" => $data[0]["lap_dv"],"lap_uni_conducto" => $data[0]["lap_uni_conducto"],"lap_mb" => $data[0]["lap_mv"],
                                   "longitud_d" => $data[0]["longitud_d"],"longitud_p" => $data[0]["longitud_p"],"longitud_ml" => $data[0]["longitud_ml"],"longitud_mv" => $data[0]["longitud_mv"],"longitud_dl" => $data[0]["longitud_dl"],"longitud_dv" => $data[0]["longitud_dv"],"longitud_uni_conducto" => $data[0]["longitud_uni_conducto"],"longitud_mb" => $data[0]["longitud_mv"],
                                   "conometria_d" => $data[0]["conometria_d"],"conometria_p" => $data[0]["conometria_p"],"conometria_ml" => $data[0]["conometria_ml"],"conometria_mv" => $data[0]["conometria_mv"],"conometria_dl" => $data[0]["conometria_dl"],"conometria_dv" => $data[0]["conometria_dv"],"conometria_uni_conducto" => $data[0]["conometria_uni_conducto"],"conometria_mb" => $data[0]["conometria_mv"],
                                   "ind_desobturacion_canal" => $data[0]["ind_desobturacion_canal"],"ind_desobturacion_longitud" => $data[0]["ind_desobturacion_longitud"],"lap_m" => $data[0]["lap_m"],"lap_v" => $data[0]["lap_v"],"longitud_m" => $data[0]["longitud_m"],"longitud_v" => $data[0]["longitud_v"],"conometria_m" => $data[0]["conometria_m"],"conometria_v" => $data[0]["conometria_v"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_nomenclatura',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfigTipoTejidos($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_tipo_tejidos=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_tipo_tejidos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfigTejidos($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_tejidos=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_config_tipo_tejidos"=>$data[0]["cod_config_tipo_tejidos"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            $endodoncia->get_datos('img_config', " cod_estado='AAA'", 'endodoncia_config');
            $data["IMGODONTOGRAMA"] = ENDO_DIR_ADJ . $endodoncia->_data[0]["img_config"];
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_tejidos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfigDiagnosticos($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_diagnosticos=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            $endodoncia->get_datos('img_config', " cod_estado='AAA'", 'endodoncia_config');
            $data["IMGODONTOGRAMA"] = ENDO_DIR_ADJ . $endodoncia->_data[0]["img_config"];
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_diagnosticos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfigMedicamentos($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_medicamentos=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_medicamentos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfigAlergias($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_alergias=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_alergias',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function ConfigTipoAntecedentes($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_tipo_antecedentes=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_tipo_antecedentes',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ConfigAntecedentes($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_antecedentes=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_config_tipo_antecedentes" => $data[0]["cod_config_tipo_antecedentes"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_antecedentes',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Medicos($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/endodoncia/model/endodonciaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $endodoncia = new Modeloendodoncia();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();$campoChek=array();
            if(!empty($argumentos[3])){
                $endodoncia->get_datos('*',"cod_usuario=".$argumentos[3],$argumentos[4]); $data=$endodoncia->_data;
                $arrayChek = array("ind_ayuda"=>$data[0]["ind_ayuda"],"ind_helpdesk"=>$data[0]["ind_helpdesk"],"ind_app"=>$data[0]["ind_app"],
                                   "ind_datolee_lider"=>$data[0]["ind_datolee_lider"],"ind_datolee_sublider"=>$data[0]["ind_datolee_sublider"]);  
                foreach($arrayChek as $llave=>$valorChek){
                    if($valorChek==1){
                       $campoChek[]=$llave; 
                    }
                }
                $endodoncia->get_datos('cod_perfil',"cod_usuario=".$argumentos[3],'sys_usuario_perfil'); !empty($endodoncia->_data) ? $data[0]["cod_perfil"]=$endodoncia->_data[0]["cod_perfil"] : $data[0]["cod_perfil"]=array();
                $endodoncia->get_datos('cod_menu',"cod_usuario=".$argumentos[3],'sys_usuario_menu');!empty($endodoncia->_data) ? $data[0]["cod_menu"]=$endodoncia->_data : $data[0]["cod_menu"]=array();
                $endodoncia->get_datos('cod_menu_sub',"cod_usuario=".$argumentos[3],'sys_usuario_menu_sub');!empty($endodoncia->_data) ? $data[0]["cod_menu_sub"]=$endodoncia->_data : $data[0]["cod_menu_sub"]=array();
                $endodoncia->get_datos('cod_empresa',"cod_usuario=".$argumentos[3],'sys_usuario_empresa'); !empty($endodoncia->_data) ?$data[0]["cod_empresa"]=$endodoncia->_data : $data[0]["cod_empresa"]=array();                      
                $camposCombo=array("cod_perfil"=>$data[0]["cod_perfil"],"cod_menu"=>$data[0]["cod_menu"],
                                   "cod_menu_sub"=>$data[0]["cod_menu_sub"],"cod_empresa"=>$data[0]["cod_empresa"]);                    
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_usuario',' cod_empresa in('.$cadEmp[0]['result'].') and ind_medico=1',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }	
    }
    
    public function ConfigMotivoConsulta($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_motivo_consulta=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_motivo_consulta',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function HistoriaClinica($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->set_simple_query("update sys_detframe set cod_tipoinput=6,det_campo='no_cod_med1',ind_actualiza=1 where cod_detframe=2185");
                $endodoncia->set_simple_query("update sys_detframe set cod_tipoinput=5,det_campo='cod_paciente1' where cod_detframe=2122");
                $endodoncia->get_datos('t1.*,t2.cod_imagen_dental, concat(t4.des_config_diagnosticos) as no_texto_subdiagnostico', " t1.cod_historia_clinica=" . $argumentos[3]." and t1.cod_config_dental=t2.cod_config_dental", $argumentos[4]." as t1 left join endodoncia_historia_clinica_diagnostico AS t3 ON (t1.cod_historia_clinica = t3.cod_historia_clinica) left join endodoncia_config_diagnosticos AS t4 ON (t3.cod_config_diagnosticos = t4.cod_config_diagnosticos), endodoncia_config_dental as t2");
                $data = $endodoncia->_data;
                $endodoncia->get_datos('cod_config_antecedentes_familiares', "cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_antecedentes');$data[0]["cod_config_antecedentes_familiares"] = !empty($endodoncia->_data) ? $data[0]["cod_config_antecedentes_familiares"]=$endodoncia->_data : $data[0]["cod_config_antecedentes_familiares"]=array(); 
                $endodoncia->get_datos('cod_config_antecedentes_personales', "cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_antecedentes');$data[0]["cod_config_antecedentes_personales"] = !empty($endodoncia->_data) ? $data[0]["cod_config_antecedentes_personales"]=$endodoncia->_data : $data[0]["cod_config_antecedentes_personales"]=array(); 
                $endodoncia->get_datos('cod_config_antecedentes_odontologicos', "cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_antecedentes');$data[0]["cod_config_antecedentes_odontologicos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_antecedentes_odontologicos"]=$endodoncia->_data : $data[0]["cod_config_antecedentes_odontologicos"]=array(); 
                $endodoncia->get_datos('cod_config_medicamentos', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_medicamentos');$data[0]["cod_config_medicamentos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_medicamentos"]=$endodoncia->_data : $data[0]["cod_config_medicamentos"]=array(); 
                $endodoncia->get_datos('cod_config_alergias', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_alergias');$data[0]["cod_config_alergias"] = !empty($endodoncia->_data) ? $data[0]["cod_config_alergias"]=$endodoncia->_data : $data[0]["cod_config_alergias"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_blandos', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_blandos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_blandos"]=$endodoncia->_data : $data[0]["cod_config_tejidos_blandos"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_dental', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_dental"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_dental"]=$endodoncia->_data : $data[0]["cod_config_tejidos_dental"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_periodontal', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_periodontal"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_periodontal"]=$endodoncia->_data : $data[0]["cod_config_tejidos_periodontal"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_perirradicular', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_perirradicular"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_perirradicular"]=$endodoncia->_data : $data[0]["cod_config_tejidos_perirradicular"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_pulpar', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_pulpar"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_pulpar"]=$endodoncia->_data : $data[0]["cod_config_tejidos_pulpar"]=array();
                $endodoncia->get_datos('cod_config_analisis_radiografico', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_analisis_radiografico');$data[0]["cod_config_analisis_radiografico"] = !empty($endodoncia->_data) ? $data[0]["cod_config_analisis_radiografico"]=$endodoncia->_data : $data[0]["cod_config_analisis_radiografico"]=array(); 
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_config_dental"=>$data[0]["cod_config_dental"],"cod_paciente"=>$data[0]["cod_paciente"],"cod_respuestas"=>$data[0]["cod_analisis_sensibilidad"],"cod_config_control"=>$data[0]["cod_config_control"],
                                     "cod_config_antecedentes_familiares"=>$data[0]["cod_config_antecedentes_familiares"],"cod_config_antecedentes_personales"=>$data[0]["cod_config_antecedentes_personales"],"cod_config_antecedentes_odontologicos"=>$data[0]["cod_config_antecedentes_odontologicos"],
                                     "cod_config_medicamentos"=>$data[0]["cod_config_medicamentos"],"cod_config_alergias"=>$data[0]["cod_config_alergias"],"cod_config_tejidos_blandos"=>$data[0]["cod_config_tejidos_blandos"],"cod_config_tejidos_dental"=>$data[0]["cod_config_tejidos_dental"],
                                     "cod_config_tejidos_periodontal"=>$data[0]["cod_config_tejidos_periodontal"],"cod_config_tejidos_perirradicular"=>$data[0]["cod_config_tejidos_perirradicular"],"cod_config_tejidos_pulpar"=>$data[0]["cod_config_tejidos_pulpar"],
                                     "cod_config_analisis_radiografico"=>$data[0]["cod_config_analisis_radiografico"]);

                $arrayChek = array("ind_desobturacion" => $data[0]["ind_desobturacion"],"ind_retratamiento"=>$data[0]["ind_retratamiento"],"ind_temporales"=>$data[0]["ind_temporales"],"cod_config_control"=>$data[0]["cod_config_control"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) { $campoChek[] = $llave; }
                }
                $camposComboEsp[0] = array("cod_imagen_dental"=>$data[0]["cod_imagen_dental"]);
                $endodoncia->get_datos('des_paciente_evolucion as Evolucion,hora_entrada_paciente_evolucion as Hora_Entrada,hora_salida_paciente_evolucion as Hora_Salida,fec_paciente_evolucion as Fecha', ' cod_paciente=' . $data[0]["cod_paciente"] .'  order by cod_paciente_evolucion desc', ' endodoncia_paciente_evolucion');
                $desAnidada = armaTextAnidado($endodoncia->_data);
                $data[0]["ult_evoluciones"] = !empty($desAnidada) ? $desAnidada : array();
            }else{
                $endodoncia->set_simple_query("update sys_detframe set cod_tipoinput=1,det_campo='no_cod_med',ind_actualiza=2 where cod_detframe=2185");
                $endodoncia->set_simple_query("update sys_detframe set cod_tipoinput=1,det_campo='cod_paciente' where cod_detframe=2122");                
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            $endodoncia->get_datos('img_config,img1_config', " cod_estado='AAA'", 'endodoncia_config');
            $data["IMGODONTOGRAMA"] = ENDO_DIR_ADJ . $endodoncia->_data[0]["img_config"];
            $data["IMGODONTOGRAMA_1"] = ENDO_DIR_ADJ . $endodoncia->_data[0]["img1_config"];
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_historia_clinica',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Pacientes($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_paciente=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_medico" => $data[0]["cod_medico"],"cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_ciudad" => $data[0]["cod_ciudad"],"cod_genero" => $data[0]["cod_genero"]);
                $arrayChek = array("ind_embarazada"=>$data[0]["ind_embarazada"]);  
                foreach($arrayChek as $llave=>$valorChek){
                    if($valorChek==1){
                       $campoChek[]=$llave; 
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_paciente',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    
    public function ConfigConsentimientos($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_consentimiento=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_consentimiento',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    

    public function ConfigAnalisisRadiografico($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_config_analisis_radiografico=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_config_analisis_radiografico',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function Evoluciones($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos("t1.*,concat(t2.ape1_paciente,' ',t2.ape2_paciente,' ',t2.nom1_paciente,' ',t2.nom2_paciente,' ',t2.ced_paciente) as nom_paciente", " t1.cod_paciente_evolucion=" . $argumentos[3].' and t1.cod_paciente=t2.cod_paciente', $argumentos[4].' as t1, endodoncia_paciente as t2');
                $data = $endodoncia->_data;

                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_paciente"=>$data[0]["cod_paciente"]);
                $endodoncia->get_datos('des_paciente_evolucion as Evolucion,hora_entrada_paciente_evolucion as Hora_Entrada,hora_salida_paciente_evolucion as Hora_Salida,fec_paciente_evolucion as Fecha', ' cod_paciente=' . $data[0]["cod_paciente"] .'  order by cod_paciente_evolucion desc', ' endodoncia_paciente_evolucion');
                $desAnidada = armaTextAnidado($endodoncia->_data);
                $data[0]["ult_evoluciones"] = !empty($desAnidada) ? $desAnidada : array();
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_paciente_evolucion',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function GenerarHistoriaClinicaPDF($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/endodoncia/model/endodonciaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $endodoncia = new Modeloendodoncia();
        $Objvista   = new view;
        if ( !Session::get('usuario') ) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();
            $endodoncia->get_datos("t1.*,CONCAT('Numero: ',t2.num_config_dental,' ',t2.nom_config_dental,' ',t2.des_config_dental) as diente,t3.nom_respuestas as sensibilidad, concat(if(ind_retratamiento=1,'SI','NO')) as retratamiento", ' t1.cod_historia_clinica=' . $argumentos[0].'', ' endodoncia_historia_clinica as t1 join endodoncia_config_dental as t2 on(t1.cod_config_dental=t2.cod_config_dental) left join sys_respuestas as t3 on(t1.cod_analisis_sensibilidad=t3.cod_respuestas)');
            $data["historia_clinica"] = !empty($endodoncia->_data[0]) ? $endodoncia->_data[0] : array();
            $endodoncia->get_datos('*', ' cod_empresa=' . $data["historia_clinica"]["cod_empresa"], ' sys_empresa');
            $data["empresa"] = $endodoncia->_data[0];
            $endodoncia->get_datos("t3.email_usuario as email_odontologo,t1.*,CONCAT(t1.ape1_paciente,' ',t1.ape2_paciente,' ',t1.nom1_paciente,' ',t1.nom2_paciente) as nombre,((YEAR(CURDATE()) - YEAR(t1.fec_nacimiento_paciente)) + IF((DATE_FORMAT(CURDATE(), '%m-%d') > DATE_FORMAT(t1.fec_nacimiento_paciente,'%m-%d')),0,-(1))) as edad,t2.nom_genero,CONCAT(IF(t1.ind_embarazada=1,'Si','NO')) AS embarazada",' cod_paciente=' . $data["historia_clinica"]["cod_paciente"].' and t1.cod_genero=t2.cod_genero',' endodoncia_paciente as t1 left join sys_usuario as t3 on(t1.cod_medico = t3.cod_usuario), sys_genero as t2');
            $data["paciente"] = $endodoncia->_data[0];
            $endodoncia->get_datos("t2.nom_config_antecedentes",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_antecedentes_familiares=t2.cod_config_antecedentes"," endodoncia_historia_clinica_antecedentes as t1, endodoncia_config_antecedentes as t2");
            $data["antecedentes_medicos_familiares"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.nom_config_antecedentes",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_antecedentes_personales=t2.cod_config_antecedentes"," endodoncia_historia_clinica_antecedentes as t1, endodoncia_config_antecedentes as t2");
            $data["antecedentes_medicos_personales"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.nom_config_antecedentes",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_antecedentes_odontologicos=t2.cod_config_antecedentes"," endodoncia_historia_clinica_antecedentes as t1, endodoncia_config_antecedentes as t2");
            $data["antecedentes_medicos_odontologicos"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.nom_config_alergias",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_alergias=t2.cod_config_alergias"," endodoncia_historia_clinica_alergias as t1, endodoncia_config_alergias as t2");
            $data["alergias"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.nom_config_medicamentos",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_medicamentos=t2.cod_config_medicamentos"," endodoncia_historia_clinica_medicamentos as t1, endodoncia_config_medicamentos as t2");
            $data["medicamentos"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_tejidos_blandos=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
            $data["tejidos_blandos"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_tejidos_dental=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
            $data["tejidos_dental"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_tejidos_periodontal=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
            $data["tejidos_periodontal"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_tejidos_perirradicular=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
            $data["tejidos_perirradicular"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_tejidos_pulpar=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
            $data["tejidos_pulpar"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("t2.des_config_diagnosticos",' t1.cod_historia_clinica='.$argumentos[0]. " and t1.cod_config_diagnosticos=t2.cod_config_diagnosticos"," endodoncia_historia_clinica_diagnostico as t1, endodoncia_config_diagnosticos as t2");
            $data["diagnostico"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $endodoncia->get_datos("*",' cod_historia_clinica='.$argumentos[0]," endodoncia_historia_clinica_informacion_conductos ");
            $data["conductometria"] = !empty($endodoncia->_data[0]) ? $endodoncia->_data[0] : array();
            $endodoncia->get_datos("t1.*,t2.*",' t1.cod_historia_clinica='.$argumentos[0] ." and t1.cod_config_tipo_imagen= t2.cod_config_tipo_imagen"," endodoncia_registro_imagenes  as t1, endodoncia_config_tipo_imagen as t2");
            $data["imagenes"] = !empty($endodoncia->_data) ? $endodoncia->_data : array();
            $endodoncia->get_datos("concat('Fecha-Registro: ',date(fec_paciente_evolucion),' hora-entrada: ',Hora_Entrada_paciente_evolucion,' Hora-Salida: ',hora_salida_paciente_evolucion,' Evolucion: ',des_paciente_evolucion) as evolucion,cod_empresa",'cod_historia_clinica='.$argumentos[0],'endodoncia_paciente_evolucion');
            $data["evoluciones"] = !empty($endodoncia->_data) ? $endodoncia->_data : array();
            require_once ROOT . 'libs/class.fpdf.historiaclinica.php';
            //primera hoja
            $pdf = new PDF('P','mm','Letter');

            $pdf->logo_header    = 'modules/sistema/adjuntos/'.$data["empresa"]["img_empresa"];
            $pdf->titulo         = $data["empresa"]["nom_empresa"];
            $pdf->linea_1        = 'Nit: ' . $data["empresa"]["nit_empresa"];
            $pdf->linea_2        = 'Telefonos: - ' . $data["empresa"]["tel_empresa"];
            $pdf->linea_3        = 'Web: ' . $data["empresa"]["web_empresa"];
            $pdf->linea_4        = 'Sugerenias / PQR: ' . $data["empresa"]["email_empresa"];
            $pdf->linea_1_footer = $data["empresa"]["dir_empresa"];
            $pdf->AddPage('P','Letter');
            $pdf->SetAutoPageBreak(true,20);
            //Datos paciente
            $pdf->Cell(5,5);
            $pdf->Cell(184,5,'INFORMACION DEL PACIENTE',0,1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'NUMERO DE CEDULA: ','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["paciente"]["ced_paciente"]),'T,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'NOMBRE: ','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["paciente"]["nombre"]),'T,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'EDAD: ','T,L',0,'L');
            $pdf->Cell(20,5,utf8_decode($data["paciente"]["edad"]),'T,R',0,'L');
            $pdf->Cell(30,5,'GENERO: ','T,L',0,'L');
            $pdf->Cell(20,5,utf8_decode($data["paciente"]["nom_genero"]),'T,R',0,'L');
            $pdf->Cell(40,5,'FECHA: ','T,L',0,'L');
            $pdf->Cell(34,5,utf8_decode($data["paciente"]["fec_paciente"]),'T,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'DIRECCION: ','T,L',0,'L');
            $pdf->Cell(52,5,utf8_decode($data["paciente"]["dir_paciente"]),'T,R',0,'L');
            $pdf->Cell(40,5,'TELEFONO: ','T,L',0,'L');
            $pdf->Cell(52,5,utf8_decode($data["paciente"]["tel_paciente"]),'T,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'PROFESION: ','T,L',0,'L');
            $pdf->Cell(52,5,utf8_decode($data["paciente"]["profesion_paciente"]),'T,R',0,'L');
            $pdf->Cell(40,5,'CELULAR: ','T,L',0,'L');
            $pdf->Cell(52,5,utf8_decode($data["paciente"]["cel_paciente"]),'T,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'EMBARAZADA: ','T,L,B',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["paciente"]["embarazada"]),'T,R,B',1,'L');
            // Motivo de Consulta
            $pdf->Ln(5);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'RETRATAMIENTO: ','T,L,B',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["historia_clinica"]["retratamiento"]),'T,R,B',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(184,5,'MOTIVO DE CONSULTA','T,L,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->MultiCell(184,10,utf8_decode($data["historia_clinica"]["motivo_historia_clinica"]),'L,R,B','J',false);
            // Anamnesis
            $pdf->Ln(5);$pdf->Cell(5,5);
            $pdf->Cell(184,5,'ANAMNESIS',0,1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(70,5,'ANTECEDENTES MEDICOS FAMILIARES:','T,L',0,'L');
            $pdf->Cell(114,5,utf8_decode($data["antecedentes_medicos_familiares"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(70,5,'ANTECEDENTES MEDICOS PERSONALES:','T,L',0,'L');
            $pdf->Cell(114,5,utf8_decode($data["antecedentes_medicos_personales"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(70,5,'ANTECEDENTES MEDICOS ODONTOLOGICOS:','T,L',0,'L');
            $pdf->Cell(114,5,utf8_decode($data["antecedentes_medicos_odontologicos"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(70,5,'ALERGIAS:','T,L',0,'L');
            $pdf->Cell(114,5,utf8_decode($data["alergias"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(184,5,'MEDICAMENTOS:','T,L,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->MultiCell(184,5,utf8_decode($data["medicamentos"]),'R,L,B','J',false);
            //examen endodontico
            $pdf->Ln(5);$pdf->Cell(5,5);
            $pdf->Cell(184,5,'ANALISIS ENDODONTICO',0,1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'DIENTE :','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["historia_clinica"]["diente"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'QUE ?','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["historia_clinica"]["que_historia_clinica"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'COMO ?','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["historia_clinica"]["como_historia_clinica"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'CUANDO ?','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["historia_clinica"]["cuando_historia_clinica"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'DONDE ?','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["historia_clinica"]["donde_historia_clinica"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'PORQUE ?','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["historia_clinica"]["porque_historia_clinica"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'TEJIDOS BLANDOS:','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["tejidos_blandos"].' - '.$data["historia_clinica"]["otro_tejidos_blandos"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'TEJIDOS DENTAL:','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["tejidos_dental"].' - '.$data["historia_clinica"]["otro_tejidos_dentales"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'TEJIDOS PERIODONTAL:','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["tejidos_periodontal"].' - '.$data["historia_clinica"]["otro_tejidos_periodontales"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'TEJIDOS PERIRRADUCULAR:','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["tejidos_perirradicular"].' - '.$data["historia_clinica"]["otro_tejidos_perirradiculares"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'TEJIDOS PULPAR:','T,L',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["tejidos_pulpar"].' - '.$data["historia_clinica"]["otro_tejidos_pulpares"]),'T,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(184,5,'ANALISIS RADIOGRAFICO:','T,L,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(184,5,utf8_decode($data["tejidos_pulpar"]),'L,R',1,'L',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->MultiCell(184,10,utf8_decode($data["historia_clinica"]["des_anarad_historia_clinica"]),'R,L,B','J',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(184,5,'DIAGNOSTICO:','T,L,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->MultiCell(184,10,utf8_decode($data["diagnostico"]),'R,L,B','J',false);
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(40,5,'ANALISIS DE SENSIBILIDAD: ','T,L,B',0,'L');
            $pdf->Cell(144,5,utf8_decode($data["historia_clinica"]["sensibilidad"]),'T,R,B',1,'L',false);
            //segunda hoja
            $pdf->Ln(10);
            $pdf->Cell(5,5);
            $pdf->Cell(184,5,'INFORMACION CONDUCTOS',0,1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $lap  = contarCoincidencias($data["conductometria"] ,'lap');
            $slap=0;
            foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='lap'){if($val==''){$slap++;}}}$slap = $lap - $slap;
            $clap = 0;
            $pdf->Cell(34,5,'CANAL',0,$slap==0 ? 1 : 0,'L');
            //informacion para lima apical principal
            foreach ($data["conductometria"] as $key => $value):
                if(devuelveString($key,'_',1)=='lap'){
                    if($value!= ''){
                        $clap++;
                        $marco = $slap==$clap ? 1 : 0; 
                        $celda = devuelveString($key,'_',2)!='uni_conducto' ? devuelveString($key,'_',2) : 'uni c';
                        $pdf->Cell(10,5,strtoupper($celda),1,$marco,'C');
                    }
                }
            endforeach;
            $pdf->Ln(0);$pdf->Cell(5,5);
            $lap  = contarCoincidencias($data["conductometria"] ,'lap');
            $slap=0;
            foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='lap'){if($val==''){$slap++;}}}$slap = $lap - $slap;
            $clap=0;
            $pdf->Cell(34,5,'LAP',0,$slap==0 ? 1 : 0,'L');
            foreach ($data["conductometria"] as $key => $value):
               switch (devuelveString($key,'_',1)) {
                    case 'lap':
                        if($value!= ''){
                            $clap++;
                            $marco = $slap==$clap ? 1 : 0;
                            $pdf->Cell(10,5,$value,1,$marco,'C');
                        }else{$lap = $lap-1;}
                    break;
                }
            endforeach;
            //informacion para longitud
            $pdf->Ln(0);$pdf->Cell(5,5);
            $longitud  = contarCoincidencias($data["conductometria"] ,'longitud');
            $slongitud=0;
            foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='longitud'){if($val==''){$slongitud++;}}}$slongitud = $longitud - $slongitud;
            $clongitud = 0;
            $pdf->Cell(34,5,'CANAL',0,$slongitud==0 ? 1 : 0,'L');
            foreach ($data["conductometria"] as $key => $value):
               switch (devuelveString($key,'_',1)) {
                    case 'longitud':
                        if($value!= ''){
                            $clongitud++;
                            $marco = $slongitud==$clongitud ? 1 : 0; 
                            $celda = devuelveString($key,'_',2)!='uni_conducto' ? devuelveString($key,'_',2) : 'uni c';
                            $pdf->Cell(10,5,strtoupper($celda),1,$marco,'C');
                        }
                    break;
                }
            endforeach;
            $pdf->Ln(0);$pdf->Cell(5,5);
            $longitud  = contarCoincidencias($data["conductometria"] ,'longitud');
            $slongitud=0;
            foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='longitud'){if($val==''){$slongitud++;}}}$slongitud = $longitud - $slongitud;
            $clongitud = 0;
            $pdf->Cell(34,5,'LONGITUD',0,$slongitud==0 ? 1 : 0,'L');
            foreach ($data["conductometria"] as $key => $value):
               switch (devuelveString($key,'_',1)) {
                    case 'longitud':
                        if($value!= ''){
                            $clongitud++;
                            $marco = $slongitud==$clongitud ? 1 : 0;
                            $pdf->Cell(10,5,$value,1,$marco,'C');
                        }
                    break;
                }
            endforeach;
            //informacion para conometria
            $pdf->Ln(0);$pdf->Cell(5,5);
            $conometria  = contarCoincidencias($data["conductometria"] ,'conometria');
            $sconometria=0;
            foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='conometria'){if($val==''){$sconometria++;}}}$sconometria = $conometria - $sconometria;
            $cconometria = 0;
            $pdf->Cell(34,5,'CANAL',0,$sconometria==0 ? 1 : 0,'L');
            foreach ($data["conductometria"] as $key => $value):
               switch (devuelveString($key,'_',1)) {
                    case 'conometria':
                        if($value!= ''){
                            $cconometria++;
                            $marco = $sconometria==$cconometria ? 1 : 0; 
                            $celda = devuelveString($key,'_',2)!='uni_conducto' ? devuelveString($key,'_',2) : 'uni c';
                            $pdf->Cell(10,5,strtoupper($celda),1,$marco,'C');
                        }
                    break;
                }
            endforeach;
            $pdf->Ln(0);$pdf->Cell(5,5);
            $conometria  = contarCoincidencias($data["conductometria"] ,'conometria');
            $sconometria=0;
            foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='conometria'){if($val==''){$sconometria++;}}}$sconometria = $conometria - $sconometria;
            $cconometria = 0;
            $pdf->Cell(34,5,'CONOMETRIA',0,$sconometria==0 ? 1 : 0,'L');
            foreach ($data["conductometria"] as $key => $value):
               switch (devuelveString($key,'_',1)) {
                    case 'conometria':
                        if($value!= ''){
                            $cconometria++;
                            $marco = $sconometria==$cconometria ? 1 : 0;
                            $pdf->Cell(10,5,$value,1,$marco,'C');
                        }
                    break;
                }
            endforeach;
            if($data["historia_clinica"]["ind_desobturacion"]==1){
                $pdf->Ln(5);$pdf->Cell(5,5);
                $pdf->Cell(184,5,'DESOBTURACION',0,1,'L');   
                //informacion para lima apical principal
                if($data["conductometria"]["canal_desobturacion"]>0 Or $data["conductometria"]["canal_desobturacion"]!=null):
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(34,5,'CANAL',0,0,'L');
                    $pdf->Cell(34,5,$data["conductometria"]["canal_desobturacion"],1,1,'C');
                endif;
                if($data["conductometria"]["long_desobturacion"]>0 Or $data["conductometria"]["long_desobturacion"]!=null):
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(34,5,'LONGITUD',0,0,'L');
                    $pdf->Cell(34,5,$data["conductometria"]["long_desobturacion"],1,1,'C');
                endif;
            }
            $pdf->AddPage('P','Letter');
            $pdf->Ln(10);
            $pdf->Cell(5,5);
            $pdf->Cell(184,5,'RADIOGRAFIAS E IMAGENES',0,1,'L');
            $mx=$pdf->GetX()+5;
            $my=$pdf->GetY();
            $c=0;
            if(!empty($data["imagenes"])):
                foreach ($data["imagenes"] as $key => $value):
                    $imgFinal = ImagenProporcion('modules/endodoncia/adjuntos/'.$value["img_registro_imagenes"],$value["img_registro_imagenes"],'modules/endodoncia/adjuntos/img_historia/');
                    $pdf->Cell(5,5);
                    $pdf->Cell(70, 40, $pdf->Image('modules/endodoncia/adjuntos/img_historia/'.$imgFinal,$mx,$my,70), 0,0, 'C');
                    $mx = $mx+85;
                    $c++;
                    if($c%2==0){$my = $my+78;$mx=15.00125;}
                    unlink('modules/endodoncia/adjuntos/img_historia/'.$imgFinal);
                endforeach;
            endif;
            $pdf->AddPage('P','Letter');
            $pdf->Ln(10);

            if(!empty($data["evoluciones"])):
                foreach ($data["evoluciones"] as $key):
                    $pdf->MultiCell(184,5,utf8_decode($key["evolucion"]),0,'J');
                endforeach;
            endif;
            
            $endodoncia->get_datos('*', ' cod_estado="AAA" AND cod_empresa='.$endodoncia->_data[0]["cod_empresa"], ' endodoncia_config');!empty($endodoncia->_data) ? $data[1]=$endodoncia->_data : $data[1]=array();
            switch ($argumentos[2]) {
                case 0:
                    $pdf->Output($data["paciente"]["nombre"].'.pdf','D');
                break;
                case 1:
                    $adj = 'modules/endodoncia/adjuntos/historias/historia-'.$data["paciente"]["nombre"].date('Y-m-d').'-'.time().'.pdf';
                    $pdf->Output($adj);
                    $email_array=array("Saludo"=>"Cordial Saludo: ",
                                       "Introduccion"=>$data[1][0]["asunto_config"],
                                       "Descripcion"=>"Este correo contiene la historia clinica generada automaticamente desde miendodoncia.com",
                                       "to"=>$data["paciente"]["email_odontologo"]);
                    sendEmail("sistema", 3, $email_array, $Objvista,$data,null,'',$adj,$r);
                    if($r==0){
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 6, '" . $data["paciente"]["nombre"] . "', now(),@cDesError, @cNumError)");
                    }else{
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 7, '" . $data["paciente"]["nombre"] . "', now(),@cDesError, @cNumError)");
                    }
                    header( 'Location: ?app=ZW5kb2RvbmNpYQ==&met=SGlzdG9yaWFDbGluaWNh&arg=MiwzMTAsSGlzdG9yaWFDbGluaWNhKjI5Ng==' );
                    exit();
                break;
                case 2:
                    $adj = 'modules/endodoncia/adjuntos/historias/'.$data["paciente"]["nombre"].'-'.date('Y-m-d').'-'.time().'.pdf';
                    $pdf->Output($adj);
                    $email_array=array("Saludo"=>"Cordial Saludo: ",
                                       "Introduccion"=>$data[1][0]["asunto_config"],
                                       "Descripcion"=>"Este correo contiene la historia clinica generada automaticamente desde miendodoncia.com",
                                       "to"=>$data["paciente"]["email_paciente"]);
                    sendEmail("sistema", 3, $email_array, $Objvista,$data,null,'',$adj,$r);
                    if($r==0){
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 6, '" . $data["paciente"]["nombre"] . "', now(),@cDesError, @cNumError)");
                    }else{
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 7, '" . $data["paciente"]["nombre"] . "', now(),@cDesError, @cNumError)");
                    }
                    header( 'Location: ?app=ZW5kb2RvbmNpYQ==&met=SGlzdG9yaWFDbGluaWNh&arg=MiwzMTAsSGlzdG9yaWFDbGluaWNhKjI5Ng==' );
                    exit();
                break;
            }
        }
    }

    public function GenerarConsentimiento($metodo, $argumentos = array()) {
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
            $data = array();
            $endodoncia->get_datos("t1.*,CONCAT(t2.ape1_paciente,' ',t2.ape2_paciente,' ',t2.nom1_paciente,' ',t2.nom2_paciente) as nom_paciente,t2.ced_paciente as ced_paciente, t3.des_config_consentimiento as des_consentimiento, t4.nom_ciudad as nom_ciudad", ' t1.cod_paciente_consentimiento=' . $argumentos[0].' and t1.cod_paciente=t2.cod_paciente and t1.cod_config_consentimiento=t3.cod_config_consentimiento', ' endodoncia_paciente_consentimiento as t1, endodoncia_paciente as t2 left join sys_ciudad as t4 on(t2.cod_ciudad=t4.cod_ciudad), endodoncia_config_consentimiento as t3');
            $data["consentimiento"] = $endodoncia->_data[0];
            $endodoncia->get_datos('*', ' cod_empresa=' . $data["consentimiento"]["cod_empresa"], ' sys_empresa');
            $data["empresa"] = $endodoncia->_data[0];
            $endodoncia->get_datos("t2.des_config_diagnosticos",' t1.cod_historia_clinica='.$data["consentimiento"]["cod_historia_clinica"]. " and t1.cod_config_diagnosticos=t2.cod_config_diagnosticos"," endodoncia_historia_clinica_diagnostico as t1, endodoncia_config_diagnosticos as t2");
            $data["diagnostico"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
            $ext = devuelveString($data["consentimiento"]["img_paciente_consentimiento"],'.',2);
            if($ext=='bmp' Or $ext =='BMP'){
                $text_img = devuelveString($data["consentimiento"]["img_paciente_consentimiento"],'.',1).'.jpg';  
                $img      = ImageCreateFromBmp('modules/endodoncia/adjuntos/'.$data["consentimiento"]["img_paciente_consentimiento"]); 
                imagejpeg($img, 'modules/endodoncia/adjuntos/'.$text_img);
                $endodoncia->set_simple_query("update endodoncia_paciente_consentimiento set img_paciente_consentimiento='".$text_img."' where cod_paciente_consentimiento='".$argumentos[0]."'");
                unlink('modules/endodoncia/adjuntos/'.$data["consentimiento"]["img_paciente_consentimiento"]);
            }else{
                $text_img = $data["consentimiento"]["img_paciente_consentimiento"];
            }
            $texto     = utf8_decode(str_replace('{nom_paciente}', $data["consentimiento"]["nom_paciente"], str_replace('{ced_paciente}', $data["consentimiento"]["ced_paciente"], str_replace('{nom_config_diagnostico}', $data["diagnostico"] , str_replace('{nom_ciudad}',$data["consentimiento"]["nom_ciudad"],$data["consentimiento"]["des_consentimiento"])))));
            //print $texto;exit;
            require_once ROOT . 'libs/class.fpdf.historiaclinica.php';
            //primera hoja
            $pdf = new PDF('P','mm','Letter');
            $pdf->logo_header    = 'modules/sistema/adjuntos/'.$data["empresa"]["img_empresa"];
            $pdf->titulo         = $data["empresa"]["nom_empresa"];
            $pdf->linea_1        = 'Nit: ' . $data["empresa"]["nit_empresa"];
            $pdf->linea_2        = 'Telefonos: - ' . $data["empresa"]["tel_empresa"];
            $pdf->linea_3        = 'Web: ' . $data["empresa"]["web_empresa"];
            $pdf->linea_4        = 'Sugerenias / PQR: ' . $data["empresa"]["email_empresa"];
            $pdf->linea_1_footer = $data["empresa"]["dir_empresa"];
            
            $pdf->AddPage('P','Letter');
            //Datos paciente
            $pdf->Ln(10);
            $pdf->Cell(5,5);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(184,5,'CONSENTIMIENTO INFORMADO',0,1,'C');
            $pdf->Ln(10);
            $pdf->Cell(5,5);
            $pdf->MultiCell(184,5,$texto,0,'J');
            $pdf->Ln(30);
            $pdf->Cell(5,5);
            $pdf->cell(80,5,'FIRMA DEL PACIENTE','T',1,'L',false);
            $pdf->Cell(5,5);
            $pdf->Cell(60, 23, $pdf->Image('modules/endodoncia/adjuntos/'.$text_img,15,198,40), 0, 0, 'C');    
            //
            $pdf->Output($data["consentimiento"]["nom_paciente"].'.pdf','D');
        }
    }

    public function ConsentimientosInfo($metodo, $argumentos=array()){
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_paciente_consentimiento=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_paciente_consentimiento',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function AgendaMedica($metodo, $argumentos=array()){
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_agenda_medica=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_medico" => $data[0]["cod_medico"]);
                $arrayChek = array("ind_domingo" => $data[0]["ind_domingo"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) { $campoChek[] = $llave; }
                }
                $endodoncia->get_datos("t1.fec_agenda_medica as Fecha_Agenda,t1.hora_agenda_citas as Hora_Cita,t1.hora_fin_agenda_medica as Hora_Fin_Cita,CONCAT(t2.ape1_paciente,' ',t2.ape2_paciente,' ',t2.nom1_paciente,' ',t2.nom2_paciente,' - ',t2.ced_paciente) asignada, CONCAT(IF(t1.ind_confirmada=1,'SI','NO')) as Confirmada", ' t1.cod_agenda_medica=' . $argumentos[3] .'  order by t1.cod_agenda_citas desc', ' endodoncia_agenda_citas as t1 left join endodoncia_paciente as t2 on(t1.cod_paciente=t2.cod_paciente)');
                $desAnidada = armaTextAnidado($endodoncia->_data);
                $data[0]["ult_evoluciones"] = !empty($desAnidada) ? $desAnidada : array();
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_agenda_medica',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function CitasMedicas($metodo, $argumentos=array()){
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_agenda_citas=" . $argumentos[3], $argumentos[4]);
                $data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
                /*$arrayChek = array("ind_domingo" => $data[0]["ind_domingo"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) { $campoChek[] = $llave; }
                }*/
            }
            $user_data = helper_user_data('nuevoRegistro');
            //var_dump($user_data);exit();
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            $fec_inicio = isset($user_data["fec_Inicio"]) ? $user_data["fec_Inicio"] : '';
            $fec_fin    = isset($user_data["fec_fin"]) ? $user_data["fec_fin"] : '';
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_agenda_citas'," cod_empresa in('".$cadEmp[0]['result']."') and fec_agenda_medica between DATE('".$fec_inicio."') and DATE('".$fec_fin."') and hora_agenda_citas between TIME('".$fec_inicio."') and TIME('".$fec_fin."')",$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function AgendaCitas($metodo, $argumentos=array()){
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_agenda_medica',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function Remisiones($metodo, $argumentos=array()){
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                $endodoncia->set_simple_query("update sys_detframe set cod_tipoinput=6,det_campo='no_cod_med1',ind_actualiza=1 where cod_detframe=2185");
                $endodoncia->set_simple_query("update sys_detframe set cod_tipoinput=5,det_campo='cod_paciente1' where cod_detframe=2122");
                $endodoncia->get_datos('t1.*,t2.cod_imagen_dental', " t1.cod_historia_clinica=" . $argumentos[3]." and t1.cod_config_dental=t2.cod_config_dental", $argumentos[4]." as t1, endodoncia_config_dental as t2");
                $data = $endodoncia->_data;
                $endodoncia->get_datos('cod_config_antecedentes_familiares', "cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_antecedentes');$data[0]["cod_config_antecedentes_familiares"] = !empty($endodoncia->_data) ? $data[0]["cod_config_antecedentes_familiares"]=$endodoncia->_data : $data[0]["cod_config_antecedentes_familiares"]=array(); 
                $endodoncia->get_datos('cod_config_antecedentes_personales', "cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_antecedentes');$data[0]["cod_config_antecedentes_personales"] = !empty($endodoncia->_data) ? $data[0]["cod_config_antecedentes_personales"]=$endodoncia->_data : $data[0]["cod_config_antecedentes_personales"]=array(); 
                $endodoncia->get_datos('cod_config_antecedentes_odontologicos', "cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_antecedentes');$data[0]["cod_config_antecedentes_odontologicos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_antecedentes_odontologicos"]=$endodoncia->_data : $data[0]["cod_config_antecedentes_odontologicos"]=array(); 
                $endodoncia->get_datos('cod_config_medicamentos', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_medicamentos');$data[0]["cod_config_medicamentos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_medicamentos"]=$endodoncia->_data : $data[0]["cod_config_medicamentos"]=array(); 
                $endodoncia->get_datos('cod_config_alergias', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_alergias');$data[0]["cod_config_alergias"] = !empty($endodoncia->_data) ? $data[0]["cod_config_alergias"]=$endodoncia->_data : $data[0]["cod_config_alergias"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_blandos', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_blandos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_blandos"]=$endodoncia->_data : $data[0]["cod_config_tejidos_blandos"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_dental', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_dental"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_dental"]=$endodoncia->_data : $data[0]["cod_config_tejidos_dental"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_periodontal', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_periodontal"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_periodontal"]=$endodoncia->_data : $data[0]["cod_config_tejidos_periodontal"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_perirradicular', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_perirradicular"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_perirradicular"]=$endodoncia->_data : $data[0]["cod_config_tejidos_perirradicular"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_pulpar', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_pulpar"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_pulpar"]=$endodoncia->_data : $data[0]["cod_config_tejidos_pulpar"]=array(); 
                $endodoncia->get_datos('cod_config_diagnosticos', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_diagnostico');$data[0]["cod_config_diagnosticos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_diagnosticos"]=$endodoncia->_data : $data[0]["cod_config_diagnosticos"]=array(); 
                $endodoncia->get_datos('cod_config_analisis_radiografico', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_analisis_radiografico');$data[0]["cod_config_analisis_radiografico"] = !empty($endodoncia->_data) ? $data[0]["cod_config_analisis_radiografico"]=$endodoncia->_data : $data[0]["cod_config_analisis_radiografico"]=array(); 
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_config_dental"=>$data[0]["cod_config_dental"],"cod_paciente"=>$data[0]["cod_paciente"],"cod_respuestas"=>$data[0]["cod_analisis_sensibilidad"],
                                     "cod_config_antecedentes_familiares"=>$data[0]["cod_config_antecedentes_familiares"],"cod_config_antecedentes_personales"=>$data[0]["cod_config_antecedentes_personales"],"cod_config_antecedentes_odontologicos"=>$data[0]["cod_config_antecedentes_odontologicos"],
                                     "cod_config_medicamentos"=>$data[0]["cod_config_medicamentos"],"cod_config_alergias"=>$data[0]["cod_config_alergias"],"cod_config_tejidos_blandos"=>$data[0]["cod_config_tejidos_blandos"],"cod_config_tejidos_dental"=>$data[0]["cod_config_tejidos_dental"],
                                     "cod_config_tejidos_periodontal"=>$data[0]["cod_config_tejidos_periodontal"],"cod_config_tejidos_perirradicular"=>$data[0]["cod_config_tejidos_perirradicular"],"cod_config_tejidos_pulpar"=>$data[0]["cod_config_tejidos_pulpar"],
                                     "cod_config_diagnosticos"=>$data[0]["cod_config_diagnosticos"],"cod_config_analisis_radiografico"=>$data[0]["cod_config_analisis_radiografico"]);
                $arrayChek = array("ind_desobturacion" => $data[0]["ind_desobturacion"],"ind_retratamiento"=>$data[0]["ind_retratamiento"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) { $campoChek[] = $llave; }
                }
                $camposComboEsp[0] = array("cod_imagen_dental"=>$data[0]["cod_imagen_dental"]);
                $endodoncia->get_datos('des_paciente_evolucion as Evolucion,hora_entrada_paciente_evolucion as Hora_Entrada,hora_salida_paciente_evolucion as Hora_Salida,fec_paciente_evolucion as Fecha', ' cod_paciente=' . $data[0]["cod_paciente"] .'  order by cod_paciente_evolucion desc', ' endodoncia_paciente_evolucion');
                $desAnidada = armaTextAnidado($endodoncia->_data);
                $data[0]["ult_evoluciones"] = !empty($desAnidada) ? $desAnidada : array();
            }else{
                $endodoncia->set_simple_query("update sys_detframe set cod_tipoinput=1,det_campo='no_cod_med',ind_actualiza=2 where cod_detframe=2185");
                $endodoncia->set_simple_query("update sys_detframe set cod_tipoinput=1,det_campo='cod_paciente' where cod_detframe=2122");                
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            $endodoncia->get_datos('img_config', " cod_estado='AAA'", 'endodoncia_config');
            $data["IMGODONTOGRAMA"] = ENDO_DIR_ADJ . $endodoncia->_data[0]["img_config"];
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_remision'," cod_empresa in('".$cadEmp[0]['result']."') and Estado='HCT'",$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function GenerarRemisionClinicaPDF($metodo, $argumentos=array()){
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
            $data  = array();$_data=array();$_array=array();
            $_data = explode('-', $argumentos[0]);
            $_data = array_values(array_diff($_data, array('')));
            // llenamos el array con los datos de las remisiones por paciente para generar el pdf sin repetir la informacion basica
            for($u=0;$u<count($_data);$u++):
                $endodoncia->get_datos("cod_paciente", ' cod_historia_clinica=' . $_data[$u].'', ' endodoncia_historia_clinica ');
                $_array[$endodoncia->_data[0]["cod_paciente"]][] = $_data[$u];
            endfor;
            //var_dump($_array);exit;
            require_once ROOT . 'libs/class.fpdf.historiaclinica.php';
            $pdf = new PDF();
            foreach($_array as $ke=>$va):
                $endodoncia->get_datos("t3.email_usuario as email_odontologo,t1.*,CONCAT(t1.ape1_paciente,' ',t1.ape2_paciente,' ',t1.nom1_paciente,' ',t1.nom2_paciente) as nombre,((YEAR(CURDATE()) - YEAR(t1.fec_nacimiento_paciente)) + IF((DATE_FORMAT(CURDATE(), '%m-%d') > DATE_FORMAT(t1.fec_nacimiento_paciente,'%m-%d')),0,-(1))) as edad,t2.nom_genero,CONCAT(IF(t1.ind_embarazada=1,'Si','NO')) AS embarazada",' cod_paciente='.$ke.' and t1.cod_genero=t2.cod_genero and t1.cod_medico=t3.cod_usuario',' endodoncia_paciente as t1, sys_genero as t2, sys_usuario as t3');
                $data["paciente"] = $endodoncia->_data[0];
                $endodoncia->get_datos('*', ' cod_empresa=' . $data["paciente"]["cod_empresa"], ' sys_empresa');
                $data["empresa"] = $endodoncia->_data[0];
                //print $data["paciente"]["nombre"];
                //primera hoja
                // remisiones relacionadas al paciente
                //print count($key);
                for($r=0;$r<count($va);$r++):
                    $pdf->logo_header    = 'modules/sistema/adjuntos/'.$data["empresa"]["img_empresa"];
                    $pdf->titulo         = $data["empresa"]["nom_empresa"];
                    $pdf->linea_1        = 'Nit: ' . $data["empresa"]["nit_empresa"];
                    $pdf->linea_2        = 'Telefonos: - ' . $data["empresa"]["tel_empresa"];
                    $pdf->linea_3        = 'Web: ' . $data["empresa"]["web_empresa"];
                    $pdf->linea_4        = 'Sugerenias / PQR: ' . $data["empresa"]["email_empresa"];
                    $pdf->linea_1_footer = $data["empresa"]["dir_empresa"];
                    $pdf->AddPage('P','Letter');
                    //Datos paciente
                    $pdf->Cell(5,5);
                    $pdf->Cell(184,5,'INFORMACION DEL PACIENTE',0,1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(40,5,'NUMERO DE CEDULA: ','T,L',0,'L');
                    $pdf->Cell(144,5,utf8_decode($data["paciente"]["ced_paciente"]),'T,R',1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(40,5,'NOMBRE: ','T,L',0,'L');
                    $pdf->Cell(144,5,utf8_decode($data["paciente"]["nombre"]),'T,R',1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(40,5,'EDAD: ','T,L',0,'L');
                    $pdf->Cell(20,5,utf8_decode($data["paciente"]["edad"]),'T,R',0,'L');
                    $pdf->Cell(30,5,'GENERO: ','T,L',0,'L');
                    $pdf->Cell(20,5,utf8_decode($data["paciente"]["nom_genero"]),'T,R',0,'L');
                    $pdf->Cell(40,5,'FECHA: ','T,L',0,'L');
                    $pdf->Cell(34,5,utf8_decode($data["paciente"]["fec_paciente"]),'T,R',1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(40,5,'DIRECCION: ','T,L',0,'L');
                    $pdf->Cell(52,5,utf8_decode($data["paciente"]["dir_paciente"]),'T,R',0,'L');
                    $pdf->Cell(40,5,'TELEFONO: ','T,L',0,'L');
                    $pdf->Cell(52,5,utf8_decode($data["paciente"]["tel_paciente"]),'T,R',1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(40,5,'PROFESION: ','T,L',0,'L');
                    $pdf->Cell(52,5,utf8_decode($data["paciente"]["profesion_paciente"]),'T,R',0,'L');
                    $pdf->Cell(40,5,'CELULAR: ','T,L',0,'L');
                    $pdf->Cell(52,5,utf8_decode($data["paciente"]["cel_paciente"]),'T,R',1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(40,5,'EMBARAZADA: ','T,L,B',0,'L');
                    $pdf->Cell(144,5,utf8_decode($data["paciente"]["embarazada"]),'T,R,B',1,'L');
                    $endodoncia->get_datos("t1.*,CONCAT('Numero: ',t2.num_config_dental,' ',t2.nom_config_dental,' ',t2.des_config_dental) as diente,t3.nom_respuestas as sensibilidad, concat(if(ind_retratamiento=1,'SI','NO')) as retratamiento", ' t1.cod_historia_clinica=' . $va[$r], ' endodoncia_historia_clinica as t1 join endodoncia_config_dental as t2 on(t1.cod_config_dental=t2.cod_config_dental) left join sys_respuestas as t3 on(t1.cod_analisis_sensibilidad=t3.cod_respuestas)');
                    $data["historia_clinica"] = $endodoncia->_data[0];
                    $endodoncia->get_datos("t2.nom_config_antecedentes",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_antecedentes_familiares=t2.cod_config_antecedentes"," endodoncia_historia_clinica_antecedentes as t1, endodoncia_config_antecedentes as t2");
                    $data["antecedentes_medicos_familiares"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.nom_config_antecedentes",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_antecedentes_personales=t2.cod_config_antecedentes"," endodoncia_historia_clinica_antecedentes as t1, endodoncia_config_antecedentes as t2");
                    $data["antecedentes_medicos_personales"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.nom_config_antecedentes",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_antecedentes_odontologicos=t2.cod_config_antecedentes"," endodoncia_historia_clinica_antecedentes as t1, endodoncia_config_antecedentes as t2");
                    $data["antecedentes_medicos_odontologicos"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.nom_config_alergias",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_alergias=t2.cod_config_alergias"," endodoncia_historia_clinica_alergias as t1, endodoncia_config_alergias as t2");
                    $data["alergias"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.nom_config_medicamentos",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_medicamentos=t2.cod_config_medicamentos"," endodoncia_historia_clinica_medicamentos as t1, endodoncia_config_medicamentos as t2");
                    $data["medicamentos"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_tejidos_blandos=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
                    $data["tejidos_blandos"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_tejidos_dental=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
                    $data["tejidos_dental"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_tejidos_periodontal=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
                    $data["tejidos_periodontal"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_tejidos_perirradicular=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
                    $data["tejidos_perirradicular"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.nom_config_tejidos",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_tejidos_pulpar=t2.cod_config_tejidos"," endodoncia_historia_clinica_tejidos as t1, endodoncia_config_tejidos as t2");
                    $data["tejidos_pulpar"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("t2.des_config_diagnosticos",' t1.cod_historia_clinica='.$va[$r]. " and t1.cod_config_diagnosticos=t2.cod_config_diagnosticos"," endodoncia_historia_clinica_diagnostico as t1, endodoncia_config_diagnosticos as t2");
                    $data["diagnostico"] = substr(armaTextAnidado($endodoncia->_data,1), 0, (strlen(armaTextAnidado($endodoncia->_data,1)) - 1));
                    $endodoncia->get_datos("*",' cod_historia_clinica='.$va[$r]," endodoncia_historia_clinica_informacion_conductos ");
                    $data["conductometria"] = !empty($endodoncia->_data[0]) ? $endodoncia->_data[0] : array();
                    $endodoncia->get_datos("t1.*,t2.*",' t1.cod_historia_clinica='.$va[$r] ." and t1.cod_config_tipo_imagen= t2.cod_config_tipo_imagen and t1.cod_respuestas=1"," endodoncia_registro_imagenes  as t1, endodoncia_config_tipo_imagen as t2");
                    $data["imagenes"] = !empty($endodoncia->_data) ? $endodoncia->_data : array();
                    
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(40,5,'DIENTE :','T,L,B',0,'L');
                    $pdf->Cell(144,5,utf8_decode($data["historia_clinica"]["diente"]),'T,R,B',1,'L',false);

                    //examen endodontico
                    $pdf->Ln(5);$pdf->Cell(5,5);
                    $pdf->Cell(184,5,'ANALISIS RADIOGRAFICO:','T,L,R',1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(184,5,utf8_decode($data["tejidos_pulpar"]),'L,R',1,'L',false);
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->MultiCell(184,10,utf8_decode($data["historia_clinica"]["des_anarad_historia_clinica"]),'R,L,B','J',false);
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(184,5,'DIAGNOSTICO:','T,L,R',1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->MultiCell(184,10,utf8_decode($data["diagnostico"]),'R,L,B','J',false);
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->Cell(184,5,'OBSERVACIONES:','T,L,R',1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $pdf->MultiCell(184,10,utf8_decode($data["historia_clinica"]["des_remision_historia_clinica"]),'R,L,B','J',false);
                    //segunda hoja
                    $pdf->Ln(10);
                    $pdf->Cell(5,5);
                    $pdf->Cell(184,5,'INFORMACION CONDUCTOS',0,1,'L');
                    $pdf->Ln(0);$pdf->Cell(5,5);
                    $lap  = contarCoincidencias($data["conductometria"] ,'lap');
                    $slap=0;
                    foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='lap'){if($val==''){$slap++;}}}$slap = $lap - $slap;
                    $clap = 0;
                    $pdf->Cell(34,5,'CANAL',0,$slap==0 ? 1 : 0,'L');
                    //informacion para lima apical principal
					foreach ($data["conductometria"] as $key => $value):
						if(devuelveString($key,'_',1)=='lap'){
							if($value!= ''){
								$clap++;
								$marco = $slap==$clap ? 1 : 0; 
								$celda = devuelveString($key,'_',2)!='uni_conducto' ? devuelveString($key,'_',2) : 'uni c';
								$pdf->Cell(10,5,strtoupper($celda),1,$marco,'C');
							}
						}
					endforeach;
					$pdf->Ln(0);$pdf->Cell(5,5);
					$lap  = contarCoincidencias($data["conductometria"] ,'lap');
					$slap=0;
					foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='lap'){if($val==''){$slap++;}}}$slap = $lap - $slap;
					$clap=0;
					$pdf->Cell(34,5,'LAP',0,$slap==0 ? 1 : 0,'L');
					foreach ($data["conductometria"] as $key => $value):
					   switch (devuelveString($key,'_',1)) {
							case 'lap':
								if($value!= ''){
									$clap++;
									$marco = $slap==$clap ? 1 : 0;
									$pdf->Cell(10,5,$value,1,$marco,'C');
								}else{$lap = $lap-1;}
							break;
						}
					endforeach;
					//informacion para longitud
					$pdf->Ln(0);$pdf->Cell(5,5);
					$longitud  = contarCoincidencias($data["conductometria"] ,'longitud');
					$slongitud=0;
					foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='longitud'){if($val==''){$slongitud++;}}}$slongitud = $longitud - $slongitud;
					$clongitud = 0;
					$pdf->Cell(34,5,'CANAL',0,$slongitud==0 ? 1 : 0,'L');
					foreach ($data["conductometria"] as $key => $value):
					   switch (devuelveString($key,'_',1)) {
							case 'longitud':
								if($value!= ''){
									$clongitud++;
									$marco = $slongitud==$clongitud ? 1 : 0; 
									$celda = devuelveString($key,'_',2)!='uni_conducto' ? devuelveString($key,'_',2) : 'uni c';
									$pdf->Cell(10,5,strtoupper($celda),1,$marco,'C');
								}
							break;
						}
					endforeach;
					$pdf->Ln(0);$pdf->Cell(5,5);
					$longitud  = contarCoincidencias($data["conductometria"] ,'longitud');
					$slongitud=0;
					foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='longitud'){if($val==''){$slongitud++;}}}$slongitud = $longitud - $slongitud;
					$clongitud = 0;
					$pdf->Cell(34,5,'LONGITUD',0,$slongitud==0 ? 1 : 0,'L');
					foreach ($data["conductometria"] as $key => $value):
					   switch (devuelveString($key,'_',1)) {
							case 'longitud':
								if($value!= ''){
									$clongitud++;
									$marco = $slongitud==$clongitud ? 1 : 0;
									$pdf->Cell(10,5,$value,1,$marco,'C');
								}
							break;
						}
					endforeach;
					//informacion para conometria
					$pdf->Ln(0);$pdf->Cell(5,5);
					$conometria  = contarCoincidencias($data["conductometria"] ,'conometria');
					$sconometria=0;
					foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='conometria'){if($val==''){$sconometria++;}}}$sconometria = $conometria - $sconometria;
					$cconometria = 0;
					$pdf->Cell(34,5,'CANAL',0,$sconometria==0 ? 1 : 0,'L');
					foreach ($data["conductometria"] as $key => $value):
					   switch (devuelveString($key,'_',1)) {
							case 'conometria':
								if($value!= ''){
									$cconometria++;
									$marco = $sconometria==$cconometria ? 1 : 0; 
									$celda = devuelveString($key,'_',2)!='uni_conducto' ? devuelveString($key,'_',2) : 'uni c';
									$pdf->Cell(10,5,strtoupper($celda),1,$marco,'C');
								}
							break;
						}
					endforeach;
					$pdf->Ln(0);$pdf->Cell(5,5);
					$conometria  = contarCoincidencias($data["conductometria"] ,'conometria');
					$sconometria=0;
					foreach ($data["conductometria"] as $k => $val) { if(devuelveString($k,'_',1)=='conometria'){if($val==''){$sconometria++;}}}$sconometria = $conometria - $sconometria;
					$cconometria = 0;
					$pdf->Cell(34,5,'CONOMETRIA',0,$sconometria==0 ? 1 : 0,'L');
					foreach ($data["conductometria"] as $key => $value):
					   switch (devuelveString($key,'_',1)) {
							case 'conometria':
								if($value!= ''){
									$cconometria++;
									$marco = $sconometria==$cconometria ? 1 : 0;
									$pdf->Cell(10,5,$value,1,$marco,'C');
								}
							break;
						}
					endforeach;
                    if($data["historia_clinica"]["ind_desobturacion"]==1){
                        $pdf->Ln(5);$pdf->Cell(5,5);
                        $pdf->Cell(184,5,'DESOBTURACION',0,1,'L');   
                        //informacion para lima apical principal
                        if($data["conductometria"]["canal_desobturacion"]>0 Or $data["conductometria"]["canal_desobturacion"]!=null):
                            $pdf->Ln(0);$pdf->Cell(5,5);
                            $pdf->Cell(34,5,'CANAL',0,0,'L');
                            $pdf->Cell(34,5,$data["conductometria"]["canal_desobturacion"],1,1,'C');
                        endif;
                        if($data["conductometria"]["long_desobturacion"]>0 Or $data["conductometria"]["long_desobturacion"]!=null):
                            $pdf->Ln(0);$pdf->Cell(5,5);
                            $pdf->Cell(34,5,'LONGITUD',0,0,'L');
                            $pdf->Cell(34,5,$data["conductometria"]["long_desobturacion"],1,1,'C');
                        endif;
                    }
                    $pdf->AddPage('P','Letter');
                    $pdf->Ln(10);
                    $pdf->Cell(5,5);
                    $pdf->Cell(184,5,'RADIOGRAFIAS E IMAGENES',0,1,'L');
                    $mx=$pdf->GetX()+5;
                    $my=$pdf->GetY();
                    $c=0;
                    if(!empty($data["imagenes"])):
                        foreach ($data["imagenes"] as $key => $value):
                            $imgFinal = ImagenProporcion('modules/endodoncia/adjuntos/'.$value["img_registro_imagenes"],$value["img_registro_imagenes"],'modules/endodoncia/adjuntos/img_historia/');
                            $pdf->Cell(5,5);
                            $pdf->Cell(70, 40, $pdf->Image('modules/endodoncia/adjuntos/img_historia/'.$imgFinal,$mx,$my,70), 0,0, 'C');
                            $mx = $mx+85;
                            $c++;
                            if($c%2==0){$my = $my+78;$mx=15.00125;}
                            unlink('modules/endodoncia/adjuntos/img_historia/'.$imgFinal);
                        endforeach;
                    endif;
                endfor;
            endforeach;
            $endodoncia->get_datos('*', ' cod_estado="AAA" AND cod_empresa='.$endodoncia->_data[0]["cod_empresa"], ' endodoncia_config');!empty($endodoncia->_data) ? $data[1]=$endodoncia->_data : $data[1]=array();
            switch ($argumentos[2]) {
                case 0:
                    $pdf->Output('remisiones-'.date('Y-m-d H:i:s').'.pdf','D');
                break;
                case 1:
                    $adj = 'modules/endodoncia/adjuntos/remisiones/remisiones-'.date('Y-m-d').'-'.time().'.pdf';
                    $pdf->Output($adj);
                    $email_array=array("Saludo"=>"Cordial Saludo: ",
                                       "Introduccion"=>$data[1][0]["asunto_config"],
                                       "Descripcion"=>"Este correo contiene las remisiones generadas automaticamente desde miendodoncia.com",
                                       "to"=>$data["paciente"]["email_odontologo"]);
                    sendEmail("sistema", 3, $email_array, $Objvista,$data,null,'',$adj, $r);
                    if($r==0){
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 6, '" . $data["paciente"]["nombre"] . "', now(),@cDesError, @cNumError)");
                    }else{
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 7, '" . $data["paciente"]["nombre"] . "', now(),@cDesError, @cNumError)");
                    }
                    header( 'Location: ?app=ZW5kb2RvbmNpYQ==&met=UmVtaXNpb25lcw==&arg=MiwzMjksUmVtaXNpb25lcyozMTQ=' );
                    exit();
                break;
                case 2:
                    $adj = 'modules/endodoncia/adjuntos/remisiones/'.$data["paciente"]["nombre"].'-'.date('Y-m-d').'-'.time().'.pdf';
                    $pdf->Output($adj);
                    $email_array=array("Saludo"=>"Cordial Saludo: ",
                                       "Introduccion"=>$data[1][0]["asunto_config"],
                                       "Descripcion"=>"Este correo contiene las remisiones generadas automaticamente desde miendodoncia.com",
                                       "to"=>$data["paciente"]["email_paciente"]);
                    sendEmail("sistema", 3, $email_array, $Objvista,$data,null,'',$adj,$r);
                    if($r==0){
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 6, '" . $data["paciente"]["nombre"] . "', now(),@cDesError, @cNumError)");
                    }else{
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 7, '" . $data["paciente"]["nombre"] . "', now(),@cDesError, @cNumError)");
                    }
                    header( 'Location: ?app=ZW5kb2RvbmNpYQ==&met=UmVtaXNpb25lcw==&arg=MiwzMjksUmVtaXNpb25lcyozMTQ=' );
                    exit();
                break;
            }
        }
    }

    public function Odontologos($metodo, $argumentos=array()){
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();$cicloInput=12;
            if (!empty($argumentos[3])) {
                
                $endodoncia->get_datos('t1.*,t2.cod_imagen_dental', " t1.cod_historia_clinica=" . $argumentos[3]." and t1.cod_config_dental=t2.cod_config_dental", $argumentos[4]." as t1, endodoncia_config_dental as t2");
                $data = $endodoncia->_data;
                $endodoncia->get_datos('cod_config_antecedentes_familiares', "cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_antecedentes');$data[0]["cod_config_antecedentes_familiares"] = !empty($endodoncia->_data) ? $data[0]["cod_config_antecedentes_familiares"]=$endodoncia->_data : $data[0]["cod_config_antecedentes_familiares"]=array(); 
                $endodoncia->get_datos('cod_config_antecedentes_personales', "cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_antecedentes');$data[0]["cod_config_antecedentes_personales"] = !empty($endodoncia->_data) ? $data[0]["cod_config_antecedentes_personales"]=$endodoncia->_data : $data[0]["cod_config_antecedentes_personales"]=array(); 
                $endodoncia->get_datos('cod_config_antecedentes_odontologicos', "cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_antecedentes');$data[0]["cod_config_antecedentes_odontologicos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_antecedentes_odontologicos"]=$endodoncia->_data : $data[0]["cod_config_antecedentes_odontologicos"]=array(); 
                $endodoncia->get_datos('cod_config_medicamentos', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_medicamentos');$data[0]["cod_config_medicamentos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_medicamentos"]=$endodoncia->_data : $data[0]["cod_config_medicamentos"]=array(); 
                $endodoncia->get_datos('cod_config_alergias', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_alergias');$data[0]["cod_config_alergias"] = !empty($endodoncia->_data) ? $data[0]["cod_config_alergias"]=$endodoncia->_data : $data[0]["cod_config_alergias"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_blandos', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_blandos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_blandos"]=$endodoncia->_data : $data[0]["cod_config_tejidos_blandos"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_dental', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_dental"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_dental"]=$endodoncia->_data : $data[0]["cod_config_tejidos_dental"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_periodontal', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_periodontal"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_periodontal"]=$endodoncia->_data : $data[0]["cod_config_tejidos_periodontal"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_perirradicular', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_perirradicular"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_perirradicular"]=$endodoncia->_data : $data[0]["cod_config_tejidos_perirradicular"]=array(); 
                $endodoncia->get_datos('cod_config_tejidos_pulpar', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_tejidos');$data[0]["cod_config_tejidos_pulpar"] = !empty($endodoncia->_data) ? $data[0]["cod_config_tejidos_pulpar"]=$endodoncia->_data : $data[0]["cod_config_tejidos_pulpar"]=array(); 
                $endodoncia->get_datos('cod_config_diagnosticos', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_diagnostico');$data[0]["cod_config_diagnosticos"] = !empty($endodoncia->_data) ? $data[0]["cod_config_diagnosticos"]=$endodoncia->_data : $data[0]["cod_config_diagnosticos"]=array(); 
                $endodoncia->get_datos('cod_config_analisis_radiografico', " cod_historia_clinica=" . $argumentos[3],' endodoncia_historia_clinica_analisis_radiografico');$data[0]["cod_config_analisis_radiografico"] = !empty($endodoncia->_data) ? $data[0]["cod_config_analisis_radiografico"]=$endodoncia->_data : $data[0]["cod_config_analisis_radiografico"]=array(); 
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_config_dental"=>$data[0]["cod_config_dental"],"cod_paciente"=>$data[0]["cod_paciente"],"cod_respuestas"=>$data[0]["cod_analisis_sensibilidad"],
                                     "cod_config_antecedentes_familiares"=>$data[0]["cod_config_antecedentes_familiares"],"cod_config_antecedentes_personales"=>$data[0]["cod_config_antecedentes_personales"],"cod_config_antecedentes_odontologicos"=>$data[0]["cod_config_antecedentes_odontologicos"],
                                     "cod_config_medicamentos"=>$data[0]["cod_config_medicamentos"],"cod_config_alergias"=>$data[0]["cod_config_alergias"],"cod_config_tejidos_blandos"=>$data[0]["cod_config_tejidos_blandos"],"cod_config_tejidos_dental"=>$data[0]["cod_config_tejidos_dental"],
                                     "cod_config_tejidos_periodontal"=>$data[0]["cod_config_tejidos_periodontal"],"cod_config_tejidos_perirradicular"=>$data[0]["cod_config_tejidos_perirradicular"],"cod_config_tejidos_pulpar"=>$data[0]["cod_config_tejidos_pulpar"],
                                     "cod_config_diagnosticos"=>$data[0]["cod_config_diagnosticos"],"cod_config_analisis_radiografico"=>$data[0]["cod_config_analisis_radiografico"]);
                $arrayChek = array("ind_desobturacion" => $data[0]["ind_desobturacion"],"ind_retratamiento"=>$data[0]["ind_retratamiento"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) { $campoChek[] = $llave; }
                }
                $camposComboEsp[0] = array("cod_imagen_dental"=>$data[0]["cod_imagen_dental"]);
                $endodoncia->get_datos('des_paciente_evolucion as Evolucion,hora_entrada_paciente_evolucion as Hora_Entrada,hora_salida_paciente_evolucion as Hora_Salida,fec_paciente_evolucion as Fecha', ' cod_paciente=' . $data[0]["cod_paciente"] .'  order by cod_paciente_evolucion desc', ' endodoncia_paciente_evolucion');
                $desAnidada = armaTextAnidado($endodoncia->_data);
                $data[0]["ult_evoluciones"] = !empty($desAnidada) ? $desAnidada : array();
            }else{
                               
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            $endodoncia->get_datos('img_config', " cod_estado='AAA'", 'endodoncia_config');
            $data["IMGODONTOGRAMA"] = ENDO_DIR_ADJ . $endodoncia->_data[0]["img_config"];
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_historia_clinica'," cod_empresa in('".$cadEmp[0]['result']."') and Estado='HCT' and cod_medico=".Session::get('cod'),$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function Ingresos($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek=array();$camposComboEsp=array();$cAux="";$cAux1="";
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_pago=" . $argumentos[3], $argumentos[4]);$data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_met_pago"=>$data[0]["cod_met_pago"],"cod_paciente"=>$data[0]["cod_paciente"]);
                $endodoncia->get_datos('cod_historia_clinica as no_cod_historia', 'cod_pago='.$data[0]["cod_pago"], ' endodoncia_pago_detalle');
                $data[1] =  $endodoncia->_data;
                $cadenaSql  = " t2.cod_historia_clinica, t2.cod_historia_clinica as no_cod_historia, t2.imp_total_historia_clinica as imp_total_historia_clinica, t2.imp_adeu_historia_clinica as imp_adeu_historia_clinica, t2.imp_canc_historia_clinica as imp_canc_historia_clinica";
                $cAux       = " and t1.cod_historia_clinica=t2.cod_historia_clinica";
                $cAux1      = " , endodoncia_historia_clinica as t2";
                $endodoncia->get_datos($cadenaSql, 't1.cod_pago='.$data[0]["cod_pago"] . $cAux , ' endodoncia_pago_detalle as t1' . $cAux1);
                $camposComboEsp=$endodoncia->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_ingreso',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Gastos($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek=array();$camposComboEsp=array();$cAux="";$cAux1="";
            if (!empty($argumentos[3])) {
                $endodoncia->get_datos('*', "cod_pago=" . $argumentos[3], $argumentos[4]);$data = $endodoncia->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_met_pago"=>$data[0]["cod_met_pago"],"cod_paciente"=>$data[0]["cod_paciente"]);
                $endodoncia->get_datos('cod_historia_clinica as no_cod_historia', 'cod_pago='.$data[0]["cod_pago"], ' endodoncia_pago_detalle');
                $data[1] =  $endodoncia->_data;
                $endodoncia->get_datos('*', 'cod_pago='.$data[0]["cod_pago"] , ' endodoncia_pago_detalle');
                $camposComboEsp=$endodoncia->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_egreso',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }

    }

    public function GenerarComprobantePDF($metodo, $argumentos=array()){
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
            $data = array();
            $endodoncia->get_datos("t1.*,t2.*,t3.*,concat(t4.ape1_paciente,' ',t4.ape2_paciente,' ',t4.nom1_paciente,' ',t4.nom2_paciente) as nom_paciente,t4.ced_paciente,t4.dir_paciente,t4.email_paciente,concat('Tratamiento: ',t5.nom_config_dental,' - ',t5.des_config_dental) as tratamiento,t6.nom_ciudad", " t1.cod_pago=" . $argumentos[0]." and t1.cod_pago=t2.cod_pago and t2.cod_historia_clinica=t3.cod_historia_clinica and t3.cod_paciente=t4.cod_paciente and t3.cod_config_dental=t5.cod_config_dental", "endodoncia_pago as t1, endodoncia_pago_detalle as t2, endodoncia_historia_clinica as t3, endodoncia_paciente as t4 left join sys_ciudad as t6 on (t4.cod_ciudad=t6.cod_ciudad), endodoncia_config_dental as t5");
            $data["informacion_comprobante"] = $endodoncia->_data[0];
            $endodoncia->get_datos('*', ' cod_empresa=' . $data["informacion_comprobante"]["cod_empresa"], ' sys_empresa');
            $data["empresa"] = $endodoncia->_data[0];
            require_once ROOT . 'libs/class.fpdf.historiaclinica.php';
            //primera hoja
            $pdf = new PDF('L','mm','A5');

            $pdf->logo_header    = 'modules/sistema/adjuntos/'.$data["empresa"]["img_empresa"];
            $pdf->titulo         = $data["empresa"]["nom_empresa"];
            $pdf->linea_1        = 'Nit: ' . $data["empresa"]["nit_empresa"];
            $pdf->linea_2        = 'Telefonos: - ' . $data["empresa"]["tel_empresa"];
            $pdf->linea_3        = 'Web: ' . $data["empresa"]["web_empresa"];
            $pdf->linea_4        = 'Sugerenias / PQR: ' . $data["empresa"]["email_empresa"];
            $pdf->linea_1_footer = $data["empresa"]["dir_empresa"];
            $pdf->AddPage('L','A5');
            //Datos paciente
            $pdf->Cell(5,5);
            $pdf->Cell(70,5,'COMPROBANTE DE ABONO: ',1,0,'L');
            $pdf->Cell(45,5,$data["informacion_comprobante"]["num_sig_comp_ingreso"],1,1,'L');
            $pdf->Ln(10);$pdf->Cell(5,5);
            $pdf->Cell(50,5,'FECHA Y HORA DE REGISTRO: ','T,L',0,'L');
            $pdf->Cell(134,5,utf8_decode($data["informacion_comprobante"]["fec_pago"]),'T,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(50,5,'PACIENTE: ','T,L',0,'L');
            $pdf->Cell(134,5,utf8_decode($data["informacion_comprobante"]["nom_paciente"]),'T,R',1,'L');
            $pdf->Ln(0);$pdf->Cell(5,5);
            $pdf->Cell(50,5,'DIRECCION: ','T,L,B',0,'L');
            $pdf->Cell(60,5,utf8_decode($data["informacion_comprobante"]["dir_paciente"]),'T,R,B',0,'L');
            $pdf->Cell(54,5,'CIUDAD: ','T,L,B',0,'L');
            $pdf->Cell(20,5,utf8_decode($data["informacion_comprobante"]["nom_ciudad"]),'T,R,B',1,'L');
            $pdf->Ln(10);$pdf->Cell(5,5);
            $pdf->Cell(50,5,'POR CONCEPTO DE: ','T,L,B',0,'L');
            $pdf->Cell(134,5,utf8_decode($data["informacion_comprobante"]["tratamiento"]),'T,R,B',1,'L');
            $pdf->Cell(5,5);
            $pdf->Cell(50,5,'POR VALOR DE: ','T,L,B',0,'L');
            $pdf->Cell(134,5,round($data["informacion_comprobante"]["imp_pago"],2),'T,R,B',1,'L');
            $pdf->Cell(5,5);
            $pdf->Cell(50,5,'NUEVO SALDO: ','T,L,B',0,'L');
            $pdf->Cell(134,5,round($data["informacion_comprobante"]["imp_adeu_historia_clinica"],2),'T,R,B',1,'L');
            $pdf->Cell(5,5);
            $pdf->Cell(50,5,'OBSERVACIONES: ','T,L,B',0,'L');
            $pdf->Cell(134,5,utf8_decode($data["informacion_comprobante"]["obs_pago"]),'T,R,B',1,'L');
            $pdf->Ln(15);$pdf->Cell(5,5);
            $pdf->Cell(100,5,'RECIBI CONFORME: ','T',0,'L');
            $endodoncia->get_datos('*', ' cod_estado="AAA" AND cod_empresa='.$endodoncia->_data[0]["cod_empresa"], ' endodoncia_config');!empty($endodoncia->_data) ? $data[1]=$endodoncia->_data : $data[1]=array();
            switch ($argumentos[2]) {
                case 0:
                    $pdf->Output($data["informacion_comprobante"]["num_sig_comp_ingreso"].'.pdf','D');
                break;
                case 2:
                    $adj = 'modules/endodoncia/adjuntos/comprobantes/'.$data["informacion_comprobante"]["num_sig_comp_ingreso"].'-'.date('Y-m-d').'-'.time().'.pdf';
                    $pdf->Output($adj);
                    $email_array=array("Saludo"=>"Cordial Saludo: ",
                                       "Introduccion"=>$data[1][0]["asunto_config"],
                                       "Descripcion"=>"Este correo contiene los comprobantes generadas automaticamente desde miendodoncia.com",
                                       "to"=>$data["informacion_comprobante"]["email_paciente"]);
                    sendEmail("sistema", 3, $email_array, $Objvista,$data,null,'',$adj, $r);
                    if($r==0){
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 6, '" . $data["paciente"]["nombre"] . "', now(), @cDesError, @cNumError)");
                    }else{
                        $endodoncia->getRegistroStoreP( "call pbNuevaNotificacionTarea(-1, " . $endodoncia->_data[0]["cod_empresa"] . ", " . Session::get('cod') . ", 7, '" . $data["paciente"]["nombre"] . "', now(),@cDesError, @cNumError)");
                    }
                    header( 'Location: ?app=ZW5kb2RvbmNpYQ==&met=SW5ncmVzb3M=&arg=MiwzMzMsSW5ncmVzb3M=' );
                    exit();
                break;
            }
        }
    }

    public function RptIngresosEndodoncia($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
            }
            $user_data = helper_user_data('nuevoRegistro');
            //var_dump($user_data);exit;
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            // filtros para el sql
            //var_dump($user_data);exit;
            $cod_empresa  = isset($user_data["cod_empresa"]) ? $user_data["cod_empresa"] : '';
            $fec_ini_pago = isset($user_data["fec_inicial"]) ? $user_data["fec_inicial"] : '';
            $fec_fin_pago = isset($user_data["fec_final"]) ? $user_data["fec_final"] : ''; 
            $sql          = !isset($user_data["ind_diadia"]) ? " concat('desde: $fec_ini_pago Hasta: $fec_fin_pago'), ROUND(sum(imp_pago),2) " : " date(fec_pago), ROUND(sum(imp_pago),2) ";
            $condicion    = !isset($user_data["ind_diadia"]) ? " date(fec_pago) between '".$fec_ini_pago."' and '".$fec_fin_pago."' and cod_empresa=".$cod_empresa ." and ind_ingreso=1":  "date(fec_pago) between '".$fec_ini_pago."' and '".$fec_fin_pago."' and cod_empresa=".$cod_empresa." and ind_ingreso=1"." group by date(fec_pago)";
            // fin filtros
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_estadistica',$condicion,$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met,'endodoncia_pago',$sql);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function RptGastosEndodoncia($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
            }
            $user_data = helper_user_data('nuevoRegistro');
            //var_dump($user_data);exit;
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            // filtros para el sql
            //var_dump($user_data);exit;
            $cod_empresa  = isset($user_data["cod_empresa"]) ? $user_data["cod_empresa"] : '';
            $fec_ini_pago = isset($user_data["fec_inicial"]) ? $user_data["fec_inicial"] : '';
            $fec_fin_pago = isset($user_data["fec_final"]) ? $user_data["fec_final"] : ''; 
            $sql          = !isset($user_data["ind_diadia"]) ? " concat('desde: $fec_ini_pago Hasta: $fec_fin_pago'), ROUND(sum(imp_pago),2) " : " date(fec_pago), ROUND(sum(imp_pago),2) ";
            $condicion    = !isset($user_data["ind_diadia"]) ? " date(fec_pago) between '".$fec_ini_pago."' and '".$fec_fin_pago."' and cod_empresa=".$cod_empresa ." and ind_egreso=1":  "date(fec_pago) between '".$fec_ini_pago."' and '".$fec_fin_pago."' and cod_empresa=".$cod_empresa." and ind_egreso=1"." group by date(fec_pago)";
            // fin filtros
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_estadistica',$condicion,$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met,'endodoncia_pago',$sql);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function RptCarteraEndodoncia($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
            }
            $user_data = helper_user_data('nuevoRegistro');
            //var_dump($user_data);exit;
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            // filtros para el sql
            //var_dump($user_data);exit;
            $cod_empresa  = isset($user_data["cod_empresa"]) ? $user_data["cod_empresa"] : '';
            $fec_ini      = isset($user_data["fec_inicial"]) ? $user_data["fec_inicial"] : '';
            $fec_fin      = isset($user_data["fec_final"]) ? $user_data["fec_final"] : '';
            $condicion    = " fec_control between '".$fec_ini."' and '".$fec_fin."' and cod_empresa=".$cod_empresa;
            // fin filtros
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_control',$condicion,$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function RptCarteraVencidaEndodoncia($metodo, $argumentos = array()) {
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
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
            }
            $user_data = helper_user_data('nuevoRegistro');
            //var_dump($user_data);exit;
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $endodoncia->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $endodoncia->_data;
            // filtros para el sql
            //var_dump($user_data);exit;
            $cod_empresa  = isset($user_data["cod_empresa"]) ? $user_data["cod_empresa"] : '';
            $fec_ini      = isset($user_data["fec_inicial"]) ? $user_data["fec_inicial"] : '';
            $fec_fin      = isset($user_data["fec_final"]) ? $user_data["fec_final"] : '';
            $condicion    = " fec_cartera between '".$fec_ini."' and '".$fec_fin."' and cod_empresa=".$cod_empresa;
            // fin filtros
            setVariables($endodoncia,$Objvista,$metodo,$argumentos[1],$argumentos[0],'endodoncia_view_cartera',$condicion,$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'endodoncia','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function remisionOdontologo($metodo,$argumentos=array()){
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/endodoncia/model/endodonciaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $endodoncia = new Modeloendodoncia();
        $Objvista   = new view;
        $user_data = helper_user_data('nuevoRegistro');
        $dataFormGeneral=array();
        $data=array();
        $cadenaSql= fbRetornaConfigForm();
        $endodoncia->get_datos($cadenaSql, 'head_formulario_config="GenerarRemision"', 'sys_formulario_config');
        $dataFormGeneral=$endodoncia->_data;
        $endodoncia->get_datos('fbDevuelveArchivos(275,1) as ARCHIVOSCSS');
        $Objvista->_archivos_css = $endodoncia->_data;
        $endodoncia->get_datos('fbDevuelveArchivos(275,2) as ARCHIVOSSCRIPT');
        $Objvista->_archivos_js  = $endodoncia->_data;
        $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral,'endodoncia');
    } 

    public function nuevoRegistro($metodo) {
        print_r($_POST,true);
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/endodoncia/model/endodonciaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $endodoncia = new Modeloendodoncia();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR' => 3,'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $user_data = helper_user_data($metodo);
            switch ($user_data['no_nom_tabla']) {
                case 'NuevaconfigEndodoncia':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                break;
                case 'NuevaConfigDental':
                    //var_dump($user_data); exit;
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["cod_config_dental"]);$e++){
                        $campo = array('nom_config_dental=');
                        $clave = array("'".$user_data['nom_config_dental'][$e]."'");
                        if(!$endodoncia->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('nom_config_dental'  =>$user_data["nom_config_dental"][$e],
                                              'des_config_dental'      =>$user_data["des_config_dental"][$e],
                                              'num_config_dental'  =>$user_data["num_config_dental"][$e],
                                              'cod_config_dental'  =>$user_data["cod_config_dental"][$e],
                                              'cod_imagen_dental'  =>$user_data["cod_imagen_dental"][$e],
                                              'ind_temporales'     =>!isset($user_data["ind_temporales"]) ? 0 : 1,
                                              'fec_config_dental'  =>date("Y-m-d H:i:s"),
                                              'cod_empresa'        =>$user_data["cod_empresa"][$e],
                                              'cod_estado'         =>$user_data['cod_estado'][$e],
                                              'no_esq_tabla'       =>$user_data['no_esq_tabla'],
                                              'no_nom_tabla'       =>$user_data['no_nom_tabla'],
                                              'no_id_formulario'   =>$user_data['no_id_formulario'],
                                              'cod_usuario'        =>'');
                            $endodoncia->setRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    }	
                    $endodoncia->err = 6;
                    $endodoncia->msj = " Dientes creados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;    
                case 'NuevaConfigNomenclatura':
                    //var_dump($user_data);exit;
                    $campo = array('cod_config_dental' . "=");
                    $clave = array("'" . $user_data['cod_config_dental'] . "'");
                    $user_data['fec_config_nomenclatura'] = date("Y-m-d H:i:s");
                    $user_data["ind_lap"] = contarCoincidencias($user_data,"lap_")>0 ? 1 : 0;
                    $user_data["ind_longitud"] = contarCoincidencias($user_data,"longitud_")>0 ? 1 : 0;
                    $user_data["ind_conometria"] = contarCoincidencias($user_data,"conometria_")>0 ? 1 : 0;;
                break;
                case 'NuevaConfigTipoTejidos':
                    $campo = array('nom_config_tipo_tejidos' . "=");
                    $clave = array("'" . $user_data['nom_config_tipo_tejidos'] . "'");
                    $user_data['fec_config_tipo_tejidos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigTejidos':
                    //var_dump($user_data);exit;
                    $campo = array('nom_config_tejidos=' , ' and cod_config_tipo_tejidos=');
                    $clave = array("'" . $user_data['nom_config_tejidos'] . "'","'" . $user_data['cod_config_tipo_tejidos'] . "'");
                    $user_data['fec_config_tejidos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigDiagnosticos':
                    $campo = array('nom_config_diagnosticos=');
                    $clave = array("'" . $user_data['nom_config_diagnosticos'] . "'");
                    $user_data['fec_config_diagnosticos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigMedicamentos':
                    $campo = array('cod_unico_config_medicamentos=');
                    $clave = array("'" . $user_data['cod_unico_config_medicamentos'] . "'");
                    $user_data['fec_config_medicamentos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigAlergias': 
                    $campo = array('nom_config_alergias=');
                    $clave = array("'" . $user_data['nom_config_alergias'] . "'");
                    $user_data['fec_config_alergias'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigTipoAntecedentes':
                    $campo = array('nom_config_tipo_antecedentes=');
                    $clave = array("'" . $user_data['nom_config_tipo_antecedentes'] . "'");
                    $user_data['fec_config_tipo_antecedentes'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigAntecedentes':
                    $campo = array('nom_config_antecedentes=',' and cod_config_tipo_antecedentes=');
                    $clave = array("'" . $user_data['nom_config_antecedentes'] . "'","'" . $user_data['cod_config_tipo_antecedentes'] . "'");
                    $user_data['fec_config_antecedentes'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaMedicos':
                    $campo = array('nom_usuario=',' and email_usuario=');
                    $clave = array("'" . $user_data['nom_usuario'] . "'","'" . $user_data['email_usuario'] . "'");
                    $user_data["ind_medico"]=1;
                break;
                case 'NuevaConfigMotivoConsulta':
                    $campo = array('nom_config_motivo_consulta=');
                    $clave = array("'" . $user_data['nom_config_motivo_consulta'] . "'");
                    $user_data['fec_config_motivo_consulta'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaPacientes':
                    $campo = array('ced_paciente=');
                    $clave = array("'" . $user_data['ced_paciente'] . "'");
                    $user_data['fec_paciente'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaHistoriaClinica':
                    $user_data["no_cod_med"] = explode(',', $user_data["no_cod_med"]);
                    $campo = array('cod_paciente=',' and cod_config_dental=', ' and motivo_historia_clinica=');
                    $clave = array("'" . $user_data['cod_paciente'] . "'","'" . $user_data['cod_config_dental'] . "'","'" . $user_data['motivo_historia_clinica'] . "'");
                    $user_data["imp_adeu_historia_clinica"]=$user_data["imp_total_historia_clinica"];
                    $user_data['fec_historia_clinica'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigConsentimientos':
                    $campo = array('nom_config_consentimiento=');
                    $clave = array("'" . $user_data['nom_config_consentimiento'] . "'");
                    $user_data['fec_config_consentimiento'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigAnalisisRadiografico':
                    $campo = array('nom_config_analisis_radiografico=');
                    $clave = array("'" . $user_data['nom_config_analisis_radiografico'] . "'");
                    $user_data['fec_config_analisis_radiografico'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConsentimientosInfo':
                    $valida=false;
                    $user_data['fec_paciente_consentimiento'] = date("Y-m-d H:i:s");
                    $endodoncia->setRegistro($user_data);
                    $endodoncia->err=6;
                    $endodoncia->msj="consentimiento creado.";
                break;
                case 'NuevaAgendaMedica':
                    $campo = array('nom_agenda_medica=');
                    $clave = array("'" . $user_data['nom_agenda_medica'] . "'");
                    $user_data['fec_agenda_medica'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaIngresos':
                    //var_dump($user_data);exit();
                    $user_data["ind_ingreso"]=1;
                    $user_data["cod_paciente"]=$user_data["no_cod_paciente"];
                    $campo = array('num_sig_comp_ingreso' . '=');
                    $clave = array("'". $user_data['num_sig_comp_ingreso']."'");
                    $user_data["imp_pago"] = floatval($user_data["imp_pago"][0]);
                    $user_data["fec_pago"] = date("Y-m-d H:i:s");
                break;
                case 'NuevaGastos':
                    $user_data["ind_egreso"]=1;
                    $campo = array('num_sig_comp_egreso' . '=');
                    $clave = array("'". $user_data['num_sig_comp_egreso']."'");
                    $user_data["imp_pago"] = floatval($user_data["imp_pago"][0]);
                    $user_data["fec_pago"] = date("Y-m-d H:i:s");
                    //var_dump($user_data);exit();
                break;
            }
            if ($valida) {
                if (!$endodoncia->getRegistro($user_data['no_esq_tabla'], $campo, $clave)) {
                    $endodoncia->setRegistro($user_data);
                    $data = array('ERR' => 6,
                                  'MSJ' => $endodoncia->msj);
                    setVariables($endodoncia,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$endodoncia->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                } else {
                    $data = array('ERR' => 2,
                                  'MSJ' => "El registro ya existe");                    
                    setVariables($endodoncia,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$endodoncia->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                }
            }else{
                $data= array('ERR' => $endodoncia->err,
                             'MSJ' => $endodoncia->msj);
                setVariables($endodoncia,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$endodoncia->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
    
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/endodoncia/model/endodonciaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $endodoncia  = new Modeloendodoncia();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $endodoncia->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$endodoncia->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch ($user_data['no_nom_tabla']) {
                case 'NuevaProductos':
                    $campo = array('nom_productos' . "=");
                    $clave = array("'" . $user_data['nom_productos'] . "'");
                    $user_data['fec_mod_productos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigDental':
                    //var_dump($user_data); exit;
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["cod_config_dental"]);$e++){
                        $user_data1=array('nom_config_dental'      =>$user_data["nom_config_dental"][$e],
                                          'num_config_dental'      =>$user_data["num_config_dental"][$e],
                                          'des_config_dental'      =>$user_data["des_config_dental"][$e],
                                          'cod_config_dental'      =>$user_data["cod_config_dental"][$e],
                                          'cod_imagen_dental'      =>$user_data["cod_imagen_dental"][$e],
                                          'fec_mod_config_dental'  =>date("Y-m-d H:i:s"),
                                          'cod_empresa'            =>$user_data["cod_empresa"][$e],
                                          'cod_estado'             =>$user_data['cod_estado'][$e],
                                          'no_esq_tabla'           =>$user_data['no_esq_tabla'],
                                          'no_nom_tabla'           =>$user_data['no_nom_tabla'],
                                          'no_id_formulario'       =>$user_data['no_id_formulario'],
                                          'cod_usuario'            =>'');
                        $endodoncia->editRegistro($user_data1);
                        $sinErr += + 1;
                    }	
                    $endodoncia->err = 6;
                    $endodoncia->msj = " Dientes Editados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaConfigNomenclatura':
                    //var_dump($user_data);exit;
                    $campo = array('cod_config_dental' . "=");
                    $clave = array("'" . $user_data['cod_config_dental'] . "'");
                    $user_data['fec_mod_config_nomenclatura'] = date("Y-m-d H:i:s");
                    $user_data["ind_lap"] = contarCoincidencias($user_data,"lap_")>0 ? 1 : 0;
                    $user_data["ind_longitud"] = contarCoincidencias($user_data,"longitud_")>0 ? 1 : 0;
                    $user_data["ind_conometria"] = contarCoincidencias($user_data,"conometria_")>0 ? 1 : 0;;
                    $user_data["lap_d"] = isset($user_data["lap_d"]) ? 1 : 0; $user_data["lap_p"] = isset($user_data["lap_p"]) ? 1 : 0; $user_data["lap_ml"] = isset($user_data["lap_ml"]) ? 1 : 0; $user_data["lap_mv"] = isset($user_data["lap_mv"]) ? 1 : 0; $user_data["lap_dl"] = isset($user_data["lap_dl"]) ? 1 : 0; $user_data["lap_dv"] = isset($user_data["lap_dv"]) ? 1 : 0; $user_data["lap_uni_conducto"] = isset($user_data["lap_uni_conducto"]) ? 1 : 0; $user_data["lap_mb"] = isset($user_data["lap_mb"]) ? 1 : 0; 
                    $user_data["longitud_d"] = isset($user_data["longitud_d"]) ? 1 : 0; $user_data["longitud_p"] = isset($user_data["longitud_p"]) ? 1 : 0; $user_data["longitud_ml"] = isset($user_data["longitud_ml"]) ? 1 : 0; $user_data["longitud_mv"] = isset($user_data["longitud_mv"]) ? 1 : 0; $user_data["longitud_dl"] = isset($user_data["longitud_dl"]) ? 1 : 0; $user_data["longitud_dv"] = isset($user_data["longitud_dv"]) ? 1 : 0; $user_data["longitud_uni_conducto"] = isset($user_data["longitud_uni_conducto"]) ? 1 : 0; $user_data["longitud_mb"] = isset($user_data["longitud_mb"]) ? 1 : 0; 
                    $user_data["conometria_d"] = isset($user_data["conometria_d"]) ? 1 : 0; $user_data["conometria_p"] = isset($user_data["conometria_p"]) ? 1 : 0; $user_data["conometria_ml"] = isset($user_data["conometria_ml"]) ? 1 : 0; $user_data["conometria_mv"] = isset($user_data["conometria_mv"]) ? 1 : 0; $user_data["conometria_dl"] = isset($user_data["conometria_dl"]) ? 1 : 0; $user_data["conometria_dv"] = isset($user_data["conometria_dv"]) ? 1 : 0; $user_data["conometria_uni_conducto"] = isset($user_data["conometria_uni_conducto"]) ? 1 : 0; $user_data["conometria_mb"] = isset($user_data["conometria_mb"]) ? 1 : 0; 
                    $user_data["ind_desobturacion_canal"] = isset($user_data["ind_desobturacion_canal"]) ? 1 : 0;$user_data["ind_desobturacion_longitud"] = isset($user_data["ind_desobturacion_longitud"]) ? 1 : 0;$user_data["lap_m"] = isset($user_data["lap_m"]) ? 1 : 0;$user_data["lap_v"] = isset($user_data["lap_v"]) ? 1 : 0;$user_data["longitud_m"] = isset($user_data["longitud_m"]) ? 1 : 0;$user_data["longitud_v"] = isset($user_data["longitud_v"]) ? 1 : 0;$user_data["conometria_m"] = isset($user_data["conometria_m"]) ? 1 : 0;$user_data["conometria_v"] = isset($user_data["conometria_v"]) ? 1 : 0;
                break;
                case 'NuevaConfigTipoTejidos':
                    $campo = array('nom_config_tipo_tejidos' . "=");
                    $clave = array("'" . $user_data['nom_config_tipo_tejidos'] . "'");
                    $user_data['fec_mod_config_tipo_tejidos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigTejidos':
                    $campo = array('nom_config_tejidos=' , ' and cod_config_tipo_tejidos=');
                    $clave = array("'" . $user_data['nom_config_tejidos'] . "'","'" . $user_data['cod_config_tipo_tejidos'] . "'");
                    $user_data['fec_mod_config_tejidos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigDiagnosticos':
                    $campo = array('nom_config_diagnosticos=');
                    $clave = array("'" . $user_data['nom_config_diagnosticos'] . "'");
                    $user_data['fec_mod_config_diagnosticos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigMedicamentos':
                    $campo = array('cod_unico_config_medicamentos=');
                    $clave = array("'" . $user_data['cod_unico_config_medicamentos'] . "'");
                    $user_data['fec_mod_config_medicamentos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigAlergias':
                    $campo = array('nom_config_alergias=');
                    $clave = array("'" . $user_data['nom_config_alergias'] . "'");
                    $user_data['fec_mod_config_alergias'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigTipoAntecedentes':
                    $campo = array('nom_config_tipo_antecedentes=');
                    $clave = array("'" . $user_data['nom_config_tipo_antecedentes'] . "'");
                    $user_data['fec_mod_config_tipo_antecedentes'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigAntecedentes':
                    $campo = array('nom_config_antecedentes=',' and cod_config_tipo_antecedentes=');
                    $clave = array("'" . $user_data['nom_config_antecedentes'] . "'","'" . $user_data['cod_config_tipo_antecedentes'] . "'");
                    $user_data['fec_mod_config_antecedentes'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaMedicos':
                    $campo = array('nom_usuario=',' and email_usuario=');
                    $clave = array("'" . $user_data['nom_usuario'] . "'","'" . $user_data['email_usuario'] . "'");
                break;
                case 'NuevaConfigMotivoConsulta':
                    $campo = array('nom_config_motivo_consulta=');
                    $clave = array("'" . $user_data['nom_config_motivo_consulta'] . "'");
                    $user_data['fec_mod_config_motivo_consulta'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaPacientes':
                    $campo = array('ced_paciente=');
                    $clave = array("'" . $user_data['ced_paciente'] . "'");
                    $user_data['fec_mod_paciente'] = date("Y-m-d H:i:s");
                    $user_data["ind_embarazada"] = isset($user_data["ind_embarazada"]) ? 1 : 0;
                break;
                case 'NuevaConfigAnalisisRadiografico':
                    $campo = array('nom_config_analisis_radiografico=');
                    $clave = array("'" . $user_data['nom_config_analisis_radiografico'] . "'");
                    $user_data['fec_mod_config_analisis_radiografico'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaEvoluciones':
                    $valida=false;
                    $endodoncia->setRegistro($user_data);
                    $endodoncia->err=6;
                break;
                case 'NuevaHistoriaClinica':
                    if(isset($user_data["no_cod_med"])):
                        $user_data["no_cod_med"] = explode(',', $user_data["no_cod_med"]);
                    endif;
                    $campo = array('cod_paciente=',' and cod_config_dental=');
                    $clave = array("'" . $user_data['no_cod_paciente'] . "'","'" . $user_data['no_cod_config_dental'] . "'");
                    $user_data['fec_mod_historia_clinica'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaConfigConsentimientos':
                    $campo = array('nom_config_consentimiento=');
                    $clave = array("'" . $user_data['nom_config_consentimiento'] . "'");
                    $user_data['fec_mod_config_consentimiento'] = date("Y-m-d H:i:s");
                break;
            }
            if($valida){
                $endodoncia->editRegistro($user_data);
                $data = array('ERR'=>6,
                              'MSJ'=>$endodoncia->msj);
                setVariables($endodoncia,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$endodoncia->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data= array('ERR' => $endodoncia->err,
                             'MSJ' => $endodoncia->msj);
                setVariables($endodoncia,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $endodoncia->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$endodoncia->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }

    function __destruct() {
        unset($this);
    }
}

?>