<?php

class contabilidadController {
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo Obligatorio de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracin de usuario desde el modelo propio del
//             controlador.
    public function index() {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        if (!Session::get('usuario')) {
            $Objvista = new view;
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'facturacion', 'login', 'login', $data);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $contabilidad = new ModeloContabilidad();
            $Objvista = new view;
            $contabilidad->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa = $contabilidad->_empresa;
            $var = "";
            for ($t = 0; $t < count($contabilidad->_notificacion); $t++) {
                $var = $var . $contabilidad->_notificacion[$t]['des_notificacion'];
            }
            $Objvista->_notificacion = array('des_notificacion' => $var);
            $Objvista->_numNotificacion = $contabilidad->_numNotificacion;
            $var = "";
            for ($t = 0; $t < count($contabilidad->_mensajes); $t++) {
                $var = $var . $contabilidad->_mensajes[$t]['des_mensajes'];
            }
            $Objvista->_mensajes = array('des_mensajes' => $var);
            $Objvista->_numMensajes = $contabilidad->_numMensajes;
            $var = "";
            for ($t = 0; $t < count($contabilidad->_menu); $t++) {
                $var = $var . $contabilidad->_menu[$t]['menu'];
            }
            $Objvista->_menu = array('menu' => $var);
            $Objvista->_menuHeader = $contabilidad->_menuHeader;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'contabilidad', 'index', 'indexContabilidad');
        }
    }

    public function cuentasContables($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $contabilidad = new ModeloContabilidad();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $contabilidad->get_datos('*', "cod_cuenta=" . $argumentos[3], $argumentos[4]);
                $data = $contabilidad->_data;
                $camposCombo = array("cod_tipocuenta" => $data[0]["cod_tipocuenta"],"cod_empresa" => $data[0]["cod_empresa"]);
                $arrayChek = array("ind_balance_general" => $data[0]["ind_balance_general"],"ind_catalogo" => $data[0]["ind_catalogo"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            setVariables($contabilidad,$Objvista,$metodo,$argumentos[1],$argumentos[0],'con_view_cuenta','',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'contabilidad','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function catalogoCuentas($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $contabilidad = new ModeloContabilidad();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $contabilidad->get_datos('*', "cod_cuenta=" . $argumentos[3], $argumentos[4]);
                $data = $contabilidad->_data;
                $camposCombo = array("cod_tipocuenta" => $data[0]["cod_tipocuenta"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $contabilidad->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $contabilidad->_data;
            setVariables($contabilidad,$Objvista,$metodo,$argumentos[1],$argumentos[0],'con_view_catalogo_cuenta',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'contabilidad','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function categoria($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $contabilidad = new ModeloContabilidad();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $contabilidad->get_datos('*', "cod_categoria=" . $argumentos[3], $argumentos[4]);
                $data = $contabilidad->_data;
                $camposCombo = array("cod_cuenta" => $data[0]["cod_cuenta"],"cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
                $arrayChek = array("ind_cuenta" => $data[0]["ind_cuenta"],"nat_debito" => $data[0]["nat_debito"],"nat_credito" => $data[0]["nat_credito"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $contabilidad->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $contabilidad->_data;
            setVariables($contabilidad,$Objvista,$metodo,$argumentos[1],$argumentos[0],'con_view_categoria',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'contabilidad','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function categoriaProceso($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $contabilidad = new ModeloContabilidad();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();;$camposComboEsp=array();$cicloInput=2;
            if (!empty($argumentos[3])) {
                $contabilidad->get_datos('*', "cod_proceso=" . $argumentos[3], $argumentos[4]);
                $data = $contabilidad->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"]);
                $cadenaSql=" cod_proceso, cod_categoria, cod_naturaleza, cod_empresa";
                $contabilidad->get_datos($cadenaSql, 'cod_proceso='.$data[0]["cod_proceso"].' and ind_propio=1 ', 'con_proceso_transaccion');
                $camposComboEsp=$contabilidad->_data;
                //invocamos el ciclo para este proceso para cargar los items facturados
                $contabilidad->get_datos('count(1) as ciclo', 'cod_proceso='.$data[0]["cod_proceso"].' and ind_propio=1 ', 'con_proceso_transaccion');
                $ciclo=$contabilidad->_data;
                $cicloInput=$ciclo[0]["ciclo"];$cicloInput==0 ? $cicloInput=2 : $cicloInput ; 
            }
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $contabilidad->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $contabilidad->_data;
            setVariables($contabilidad,$Objvista,$metodo,$argumentos[1],$argumentos[0],'con_view_procesos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'contabilidad','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function libroDiario($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $contabilidad = new ModeloContabilidad();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $contabilidad->get_datos('*', "cod_cab_mov_contable=" . $argumentos[3], $argumentos[4]);
                $data = $contabilidad->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $contabilidad->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $contabilidad->_data;
            setVariables($contabilidad,$Objvista,$metodo,$argumentos[1],$argumentos[0],'con_view_libro_diario',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'contabilidad','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function libroMayor($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $contabilidad = new ModeloContabilidad();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $contabilidad->get_datos('*', "cod_cab_mov_contable=" . $argumentos[3], $argumentos[4]);
                $data = $contabilidad->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $contabilidad->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $contabilidad->_data;
            setVariables($contabilidad,$Objvista,$metodo,$argumentos[1],$argumentos[0],'con_view_libro_mayor',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'contabilidad','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function GraficoCuentas($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $contabilidad = new ModeloContabilidad();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $contabilidad->get_datos('*', "cod_cab_mov_contable=" . $argumentos[3], $argumentos[4]);
                $data = $contabilidad->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            $contabilidad->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $contabilidad->_data;
            $contabilidad->get_datos('fbArmaCuentasContables() as result');
            $data["cuentas_contables"]=$contabilidad->_data[0]['result'];
            //var_dump($dataFormGeneral);exit;
            setVariables($contabilidad,$Objvista,$metodo,$argumentos[1],$argumentos[0],'con_view_libro_mayor',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'contabilidad','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral,'contabilidad');
        }
    }
    
    public function nuevoRegistro($metodo) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $contabilidad = new ModeloContabilidad();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $user_data = helper_user_data($metodo);
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaCuentasContables':
                    $campo = array('clase_cuenta= ' , ' and grupo_cuenta= ', ' and cuenta_cuenta= ', ' and sub_cuenta= ');
                    $clave = array($user_data['clase_cuenta'],$user_data['grupo_cuenta'],$user_data['cuenta_cuenta'],$user_data['sub_cuenta']);
                    $user_data['fec_cuenta'] = date("Y.m.d H:i:s");
                break;
                case 'nuevaCategoria':
                    $campo = array('nom_categoria= ' ,);
                    $clave = array("'".$user_data['nom_categoria']."'");
                    $user_data['fec_categoria'] = date("Y.m.d H:i:s");
                    isset($user_data["nat_debito"]) and $user_data["nat_debito"]==1 ? $user_data["nat_credito"]=0 : $user_data["nat_credito"]=1;
                    isset($user_data["nat_credito"]) and  $user_data["nat_credito"]==1 ? $user_data["nat_debito"]=0 : $user_data["nat_debito"]=1;
                    isset($user_data["ind_cuenta"]) ? $user_data["ind_cuenta"]=1 : $user_data["ind_cuenta"]=0;
                break;
                case 'nuevaCategoriaProceso':
                    $campo = array('nom_proceso= ' ,);
                    $clave = array("'".$user_data['nom_proceso']."'");
                break;
            }
            if ($valida) {
                if (!$contabilidad->getRegistro($user_data['no_esq_tabla'], $campo, $clave)) {
                    $contabilidad->setRegistro($user_data);
                    $data = array('ERR' => 6,
                                  'MSJ' => $contabilidad->msj);
                    setVariables($contabilidad,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$contabilidad->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                } else {
                    $data = array('ERR' => 2,
                                  'MSJ' => "El registro ya existe");                    
                    setVariables($contabilidad,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$contabilidad->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                }
            }
        }
    }
    
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/contabilidad/model/ContabilidadModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $contabilidad  = new ModeloContabilidad();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $contabilidad->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$contabilidad->_data;
            //var_dump($sistema->_data);exit();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaCuentasContables':
                    $campo = array('clase_cuenta= ' , ' and grupo_cuenta= ', ' and cuenta_cuenta= ', ' and sub_cuenta= ');
                    $clave = array($user_data['clase_cuenta'],$user_data['grupo_cuenta'],$user_data['cuenta_cuenta'],$user_data['sub_cuenta']);
                    !isset($user_data["ind_balance_general"]) ? $user_data["ind_balance_general"]=0 : $user_data["ind_balance_general"]=1;
                    !isset($user_data["ind_catalogo"]) ? $user_data["ind_catalogo"]=0 : $user_data["ind_catalogo"]=1;
                break;
                case 'nuevaCategoria':
                    $campo = array('nom_categoria= ' ,);
                    $clave = array("'".$user_data['nom_categoria']."'");
                    isset($user_data["nat_debito"]) and $user_data["nat_debito"]==1 ? $user_data["nat_credito"]=0 : $user_data["nat_credito"]=1;
                    isset($user_data["nat_credito"]) and  $user_data["nat_credito"]==1 ? $user_data["nat_debito"]=0 : $user_data["nat_debito"]=1;
                    isset($user_data["ind_cuenta"]) ? $user_data["ind_cuenta"]=1 : $user_data["ind_cuenta"]=0;
                break;
                case 'nuevaCategoriaProceso':
                    $campo = array('nom_proceso= ' ,);
                    $clave = array("'".$user_data['nom_proceso']."'");
                break;
            }
            if($valida){
                $contabilidad->editRegistro($user_data);
                $data = array('ERR'=>6,
                              'MSJ'=>$contabilidad->msj);
                setVariables($contabilidad,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$contabilidad->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data = array('ERR'=>3,
                              'MSJ'=>"La Edicion no esta permitida en este proceso, consulte a su administrador");
                setVariables($contabilidad,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $contabilidad->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$contabilidad->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
}

?>