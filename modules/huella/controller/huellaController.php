<?php

class huellaController {
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracin de usuario desde el modelo propio del
//             controlador.
    public function index() {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        if (!Session::get('usuario')) {
            $Objvista = new view;
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $huella = new ModeloHuella();
            $Objvista = new view;
            $huella->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa = $huella->_empresa;
            $var = "";
            for ($t = 0; $t < count($huella->_notificacion); $t++) {
                $var = $var . $huella->_notificacion[$t]['des_notificacion'];
            }
            $Objvista->_notificacion = array('des_notificacion' => $var);
            $Objvista->_numNotificacion = $huella->_numNotificacion;
            $var = "";
            for ($t = 0; $t < count($huella->_mensajes); $t++) {
                $var = $var . $huella->_mensajes[$t]['des_mensajes'];
            }
            $Objvista->_mensajes = array('des_mensajes' => $var);
            $Objvista->_numMensajes = $huella->_numMensajes;
            $var = "";
            for ($t = 0; $t < count($huella->_menu); $t++) {
                $var = $var . $huella->_menu[$t]['menu'];
            }
            $Objvista->_menu = array('menu' => $var);
            $Objvista->_menuHeader = $huella->_menuHeader;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'huella', 'index', 'indexHuella');
        }
    }

    public function claseVehiculo($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $huella = new ModeloHuella();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $huella->get_datos('*', "cod_vehiculo_clase=" . $argumentos[3], $argumentos[4]);
                $data = $huella->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $huella->_data;
            $huella->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            setVariables($huella,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hue_view_vehiculo_clase',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2));
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'huella','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function tipoServicio($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $huella = new ModeloHuella();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $huella->get_datos('*', "cod_tip_servicio=" . $argumentos[3], $argumentos[4]);
                $data = $huella->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $huella->_data;
            $huella->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            setVariables($huella,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hue_view_tip_servicio',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2));
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'huella','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function marcaVehiculo($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $huella = new ModeloHuella();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $huella->get_datos('*', "cod_marca_vehiculo=" . $argumentos[3], $argumentos[4]);
                $data = $huella->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $huella->_data;
            $huella->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            setVariables($huella,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hue_view_marca_vehiculo',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2));
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'huella','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function tipoDocumento($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $huella = new ModeloHuella();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $huella->get_datos('*', "cod_tip_documento=" . $argumentos[3], $argumentos[4]);
                $data = $huella->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $huella->_data;
            $huella->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            setVariables($huella,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hue_view_tip_documento',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2));
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'huella','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

   public function combustible($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $huella = new ModeloHuella();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $huella->get_datos('*', "cod_combustible=" . $argumentos[3], $argumentos[4]);
                $data = $huella->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $huella->_data;
            $huella->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            setVariables($huella,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hue_view_combustible',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2));
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'huella','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function vehiculo($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $huella      = new ModeloHuella();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $Objvista = new view;
            $data = array();
            $camposCombo = array();
            $camposComboEsp = array();
            $campoChek = array();
            $ciclo=array();
            $cicloInput=1;
            $dataFormGeneral=array();
            if (!empty($argumentos[3])) {
                $cadenaSql="b.*,a.res_mov_vehiculo_datos as res_mov_vehiculo_datos,a.blindaje_vehiculo_datos as blindaje_vehiculo_datos, a.pot_vehiculo_datos as pot_vehiculo_datos, a.num_pue_vehiculo_datos as num_pue_vehiculo_datos,a.lim_pro_vehiculo_datos as lim_pro_vehiculo_datos,a.fec_mat_vehiculo_datos as fec_mat_vehiculo_datos,a.fec_exp_lic_vehiculo_datos as fec_exp_lic_vehiculo_datos,a.fec_ven_vehiculo_datos as fec_ven_vehiculo_datos, a.tt_vehiculo_datos as tt_vehiculo_datos, a.lat_vehiculo_datos as lat_vehiculo_datos, a.cod_vehiculo as cod_vehiculo";
                $huella->get_datos('*', "a.cod_vehiculo=" . $argumentos[3] . ' and a.cod_vehiculo=b.cod_vehiculo', 'hue_vehiculo as b, hue_vehiculo_datos as a', $argumentos[4]);
                $data = $huella->_data;//var_dump($data);exit();
                $camposCombo = array("cod_vehiculo_clase"=>$data[0]["cod_vehiculo_clase"],"cod_tip_servicio" => $data[0]["cod_tip_servicio"],"cod_empresa"=> $data[0]["cod_empresa"],
                                     "cod_combustible"=>$data[0]["cod_combustible"],"cod_estado"=>$data[0]["cod_estado"]);
                $arrayChek = array("blindaje_vehiculo_datos" => $data[0]["blindaje_vehiculo_datos"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
                $cadenaSql="cod_tip_documento,num_vehiculo_documento,fec_vehiculo_documento,fec_ven_vehiculo_documento";
                $huella->get_datos($cadenaSql, ' cod_vehiculo='.$data[0]["cod_vehiculo"], ' hue_vehiculo_documentos');
                $camposComboEsp=$huella->_data;
                //invocamos el ciclo para este proceso para cargar los items facturados
                $huella->get_datos('count(1) as ciclo', 'cod_vehiculo='.$data[0]["cod_vehiculo"], 'hue_vehiculo_documentos');
                $ciclo=$huella->_data;
                $cicloInput=$ciclo[0]["ciclo"];
            }
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            setVariables($huella,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hue_view_vehiculo','',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2));
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'huella','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function clienteHue($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $huella = new ModeloHuella();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $huella->get_datos('*', "cod_cliente=" . $argumentos[3], $argumentos[4]);
                $data = $huella->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_ciudad" => $data[0]["cod_ciudad"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $huella->_data;
            $huella->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            setVariables($huella,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hue_view_cliente',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2));
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'huella','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function clienteVehiculo($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $huella = new ModeloHuella();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $huella->get_datos('*', "cod_cliente_vehiculo=" . $argumentos[3], $argumentos[4]);
                $data = $huella->_data;
                $camposCombo = array("cod_cliente" => $data[0]["cod_cliente"],"cod_vehiculo" => $data[0]["cod_vehiculo"],
                                     "cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $huella->_data;
            $huella->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            setVariables($huella,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hue_view_cliente_vehiculo',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2));
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'huella','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function nuevoRegistro($metodo) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $huella = new ModeloHuella();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $user_data = helper_user_data($metodo);
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaClaseVehiculo':
                    $campo = array('nom_vehiculo_clase= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nom_vehiculo_clase'] . "'", $user_data['cod_empresa']);
                    $user_data['fec_vehiculo_clase'] = date("Y.m.d H:i:s");
                break;
                case 'nuevaTipoServicio':
                    $campo = array('nom_tip_servicio= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nom_tip_servicio'] . "'", $user_data['cod_empresa']);
                    $user_data['fec_tip_servicio'] = date("Y.m.d H:i:s");
                break;
                case 'nuevaTipoDocumento':
                    $campo = array('nom_tip_documento= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nom_tip_documento'] . "'", $user_data['cod_empresa']);
                    $user_data['fec_tip_documento'] = date("Y.m.d H:i:s");
                break;
                case 'nuevaCombustible':
                    $campo = array('nom_combustible= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nom_combustible'] . "'", $user_data['cod_empresa']);
                    $user_data['fec_combustible'] = date("Y.m.d H:i:s");
                break;
                case 'nuevaVehiculo':
                    $campo = array('placa_vehiculo' . "=");
                    $clave = array("'" . $user_data['placa_vehiculo'] . "'");
                    $user_data['fec_vehiculo'] = date("Y.m.d H:i:s");
                break;
                case 'nuevaClienteHue':
                    $campo = array('nit_cliente= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nit_cliente'] . "'", $user_data['cod_empresa']);
                break;
                case 'nuevaClienteVehiculo':
                    $campo = array('cod_cliente=', ' and cod_vehiculo=', ' and cod_estado=');
                    $clave = array($user_data['cod_cliente'], $user_data['cod_vehiculo'],'"AAA"');
                break;
                case 'nuevaMarcaVehiculo':
                    $campo = array('nom_marca_vehiculo=');
                    $clave = array("'".$user_data['nom_marca_vehiculo']."'");
                break;
            }
            if ($valida) {
                if (!$huella->getRegistro($user_data['no_esq_tabla'], $campo, $clave)) {
                    $huella->setRegistro($user_data);
                    $data = array('ERR' => 6,
                                  'MSJ' => $huella->msj);
                    setVariables($huella,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $huella->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$huella->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                } else {
                    $data = array('ERR' => 2,
                                  'MSJ' => "El registro ya existe");                    
                    setVariables($huella,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $huella->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$huella->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                }
            }
        }
    }
    
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/huella/model/huellaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $huella  = new ModeloHuella();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $huella->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$huella->_data;
            //var_dump($sistema->_data);exit();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaClaseVehiculo':
                    $campo = array('nom_vehiculo_clase= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nom_vehiculo_clase'] . "'", $user_data['cod_empresa']);
                break;
                case 'nuevaTipoServicio':
                    $campo = array('nom_tip_servicio= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nom_tip_servicio'] . "'", $user_data['cod_empresa']);
                break;
                case 'nuevaTipoDocumento':
                    $campo = array('nom_tip_documento= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nom_tip_documento'] . "'", $user_data['cod_empresa']);
                break;
                case 'nuevaCombustible':
                    $campo = array('nom_combustible= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nom_combustible'] . "'", $user_data['cod_empresa']);
                break;
                case 'nuevaVehiculo':
                    $campo = array('placa_vehiculo' . "=");
                    $clave = array("'" . $user_data['placa_vehiculo'] . "'");
                break;
                case 'nuevaClienteHue':
                    $campo = array('nit_cliente= ' , ' and cod_empresa= ');
                    $clave = array("'" . $user_data['nit_cliente'] . "'", $user_data['cod_empresa']);
                break;
                case 'nuevaClienteVehiculo':
                    $campo = array('cod_cliente=', ' and cod_vehiculo=', ' and cod_estado=');
                    $clave = array($user_data['cod_cliente'], $user_data['cod_vehiculo'],'"AAA"');
                break;
            case 'nuevaMarcaVehiculo':
                    $campo = array('nom_marca_vehiculo=');
                    $clave = array($user_data['nom_marca_vehiculo']);
                break;
            }
            if($valida){
                $huella->editRegistro($user_data);
                $data = array('ERR'=>6,
                              'MSJ'=>$huella->msj);
                setVariables($huella,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $huella->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$huella->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
}

?>