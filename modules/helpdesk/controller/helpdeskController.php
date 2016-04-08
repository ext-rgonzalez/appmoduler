<?php

class helpdeskController {
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo Obligatorio de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracin de usuario desde el modelo propio del
//             controlador.
    public function index() {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        if (!Session::get('usuario')) {
            $Objvista = new view;
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $helpdesk = new ModeloHelpdesk();
            $Objvista = new view;
            $helpdesk->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa = $helpdesk->_empresa;
            $var = "";
            for ($t = 0; $t < count($helpdesk->_notificacion); $t++) {
                $var = $var . $helpdesk->_notificacion[$t]['des_notificacion'];
            }
            $Objvista->_notificacion = array('des_notificacion' => $var);
            $Objvista->_numNotificacion = $helpdesk->_numNotificacion;
            $var = "";
            for ($t = 0; $t < count($helpdesk->_mensajes); $t++) {
                $var = $var . $helpdesk->_mensajes[$t]['des_mensajes'];
            }
            $Objvista->_mensajes = array('des_mensajes' => $var);
            $Objvista->_numMensajes = $helpdesk->_numMensajes;
            $var = "";
            for ($t = 0; $t < count($helpdesk->_menu); $t++) {
                $var = $var . $helpdesk->_menu[$t]['menu'];
            }
            $Objvista->_menu = array('menu' => $var);
            $Objvista->_menuHeader = $helpdesk->_menuHeader;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'helpdesk', 'index', 'indexHelpDesk');
        }
    }

    public function areaSolicitud($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $helpdesk = new ModeloHelpdesk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (isset($argumentos[3]) And $argumentos[3]!='') {
                $helpdesk->get_datos('*', "cod_area_solicitud=" . $argumentos[3], $argumentos[4]);
                $data = $helpdesk->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $helpdesk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $helpdesk->_data;
            setVariables($helpdesk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hd_view_area_solicitud',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'helpdesk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function categorias($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $helpdesk = new ModeloHelpdesk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $helpdesk->get_datos('*', "cod_categorias=" . $argumentos[3], $argumentos[4]);
                $data = $helpdesk->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $helpdesk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $helpdesk->_data;
            setVariables($helpdesk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hd_view_categorias',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'helpdesk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function prioridad($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $helpdesk = new ModeloHelpdesk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $helpdesk->get_datos('*', "cod_prioridad=" . $argumentos[3], $argumentos[4]);
                $data = $helpdesk->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $helpdesk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $helpdesk->_data;
            setVariables($helpdesk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hd_view_prioridad',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'helpdesk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function referencia($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $helpdesk = new ModeloHelpdesk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $helpdesk->get_datos('*', "cod_referencia=" . $argumentos[3], $argumentos[4]);
                $data = $helpdesk->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $helpdesk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $helpdesk->_data;
            setVariables($helpdesk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hd_view_referencia',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'helpdesk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function servicio($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $helpdesk = new ModeloHelpdesk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$cicloInput=1;$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $helpdesk->get_datos('*', "cod_servicio=" . $argumentos[3], $argumentos[4]);
                $data = $helpdesk->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_prioridad" => $data[0]["cod_prioridad"],
                                     "cod_area_solicitud" => $data[0]["cod_area_solicitud"],"cod_referencia" => $data[0]["cod_referencia"],"cod_categorias" => $data[0]["cod_categorias"]);
                $arrayChek   = array("ind_email" => $data[0]["ind_email"], "ind_tels" => $data[0]["ind_tels"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    $valorChek == 1 ? $campoChek[]=$llave : $campoChek[]="";
                }
                $helpdesk->get_datos('t2.des_gestion as Descripcion,t2.fec_gestion as FechaGestion,t3.usuario_usuario as Usuario,t2.hora_inicio as Inicio,t2.hora_fin as Finalizacion, t2.total_tiempo_gestion as Tiempo_Total', ' t1.cod_servicio='.$argumentos[3].' and t1.cod_asignacion=t2.cod_asignacion and t2.cod_usuario=t3.cod_usuario', ' hd_asignacion as t1, hd_gestion as t2, sys_usuario as t3');
                $camposComboEsp=$helpdesk->_data;
                $desAnidada = armaTextAnidado($helpdesk->_data);
                $data[0]["ult_gestiones"] = !empty($desAnidada) ? $desAnidada : array("ult_gestiones"=>"");
            }
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $helpdesk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $helpdesk->_data;
            setVariables($helpdesk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hd_view_servicio',' cod_empresa in('.$cadEmp[0]['result'].') order by Tiquet desc',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'helpdesk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function configHelpDesk($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $helpdesk = new ModeloHelpdesk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $helpdesk->get_datos('*', "cod_config=" . $argumentos[3], $argumentos[4]);
                $data = $helpdesk->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"]);
//                $arrayChek   = array("ind_email" => $data[0]["ind_email"], "ind_tels" => $data[0]["ind_tels"]);
//                foreach ($arrayChek as $llave => $valorChek) {
//                    $valorChek == 1 ? $campoChek[]=$llave : $campoChek[]="";
//                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $helpdesk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $helpdesk->_data;
            setVariables($helpdesk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hd_view_config',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'helpdesk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function asignacion($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $helpdesk = new ModeloHelpdesk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $helpdesk->get_datos('*', "cod_asignacion=" . $argumentos[3], $argumentos[4]);
                $data = $helpdesk->_data;
                $helpdesk->get_datos('*'," cod_servicio=".$data[0]["cod_servicio"],' hd_servicio');!empty($helpdesk->_data) ? $data[1]=$helpdesk->_data : $data[1]=array();
                $helpdesk->get_datos('*'," cod_asignacion=".$data[0]["cod_asignacion"]." order by cod_gestion desc LIMIT 1",' hd_gestion');!empty($helpdesk->_data) ? $data[2]=$helpdesk->_data : $data[2]=array("fec_gestion"=>"","des_gestion"=>"");
                //var_dump($data[1][0]);exit;
                $camposCombo = array("cod_categorias" => $data[1][0]["cod_categorias"],"cod_prioridad" => $data[1][0]["cod_prioridad"],"cod_referencia" => $data[1][0]["cod_referencia"],
                                     "cod_area_solicitud" => $data[1][0]["cod_area_solicitud"]);
                $arrayChek   = array("ind_email" => $data[1][0]["ind_email"], "ind_tels" => $data[1][0]["ind_tels"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    $valorChek == 1 ? $campoChek[]=$llave : $campoChek[]="";
                }
                $helpdesk->get_datos('t1.des_gestion as Descripcion,t1.fec_gestion as FechaGestion,t2.usuario_usuario as Usuario,t1.hora_inicio as Inicio,t1.hora_fin as Finalizacion', ' t1.cod_asignacion=' . $argumentos[3] .' and t1.cod_usuario=t2.cod_usuario order by cod_gestion desc', 'hd_gestion as t1, sys_usuario as t2');
                $desAnidada = armaTextAnidado($helpdesk->_data);
                $data[0]["ult_gestiones"] = !empty($desAnidada) ? $desAnidada : array("ult_gestiones"=>"");
            }
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            $helpdesk->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $helpdesk->_data;
            setVariables($helpdesk,$Objvista,$metodo,$argumentos[1],$argumentos[0],'hd_view_asignacion',' cod_usuario ='.Session::get('cod').' and cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'helpdesk','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function nuevoRegistro($metodo) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $helpdesk = new ModeloHelpdesk();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $user_data = helper_user_data($metodo);
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaAreaSolicitud':
                    $campo = array('nom_area_solicitud= ');
                    $clave = array("'".$user_data['nom_area_solicitud']."'");
                break;
                case 'nuevaCategorias':
                    $campo = array('nom_categorias= ');
                    $clave = array("'".$user_data['nom_categorias']."'");
                break;
                case 'nuevaReferencia':
                    $campo = array('nom_referencia= ');
                    $clave = array("'".$user_data['nom_referencia']."'");
                break;
                case 'nuevaPrioridad':
                    $campo = array('nom_prioridad= ',' and dias_resolucion_prioridad=');
                    $clave = array("'".$user_data['nom_prioridad']."'", $user_data["dias_resolucion_prioridad"]);
                break;
                case 'nuevaServicio':
                    if($user_data["no_metodo"]=="AGREGAR"):
                        $campo = array('cod_servicio= ');
                        $clave = array('-1');
                        $user_data["cod_estado"]='SAA';
                    else:
                        $valida=false;
                        $helpdesk->msj="No se puede asignar un tiquet sin antes haberlo creado. por favor guarde primero el tiquet y asignelo posteriormente. ";
                        $helpdesk->err="2";
                    endif;
                break;
                case 'nuevaConfigHelpDesk':
                    $campo = array('nom_config= ');
                    $clave = array("'".$user_data['nom_config']."'");
                break;
            }
            if ($valida) {
                if (!$helpdesk->getRegistro($user_data['no_esq_tabla'], $campo, $clave)) {
                    $helpdesk->setRegistro($user_data);
                    $data = array('ERR' => 6,
                                  'MSJ' => $helpdesk->msj);
                    setVariables($helpdesk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$helpdesk->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                } else {
                    $data = array('ERR' => 2,
                                  'MSJ' => "El registro ya existe");                    
                    setVariables($helpdesk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$helpdesk->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                }
            }else{
                $data= array('ERR' => $helpdesk->err,
                             'MSJ' => $helpdesk->msj);
                setVariables($helpdesk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$helpdesk->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
    
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/helpdesk/model/helpdeskModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $helpdesk  = new ModeloHelpdesk();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $helpdesk->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$helpdesk->_data;
            //var_dump($sistema->_data);exit();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaAreaSolicitud':
                    $campo = array('nom_area_solicitud= ');
                    $clave = array("'".$user_data['nom_area_solicitud']."'");
                break;
                case 'nuevaCategorias':
                    $campo = array('nom_categorias= ');
                    $clave = array("'".$user_data['nom_categorias']."'");
                break;
                case 'nuevaReferencia':
                    $campo = array('nom_referencia= ');
                    $clave = array("'".$user_data['nom_referencia']."'");
                break;
                case 'nuevaPrioridad':
                    $campo = array('nom_prioridad= ',' and dias_resolucion_prioridad');
                    $clave = array("'".$user_data['nom_referencia']."'", $user_data["dias_resolucion_prioridad"]);
                break;
                case 'nuevaServicio':
                    $indAsigna = 0;$secuencia;
                    if (empty($user_data["no_cod_usuario_trasferido"])):
                        $user_data["no_cod_usuario"] = Session::get('cod');
                        $indAsigna = 1;
                    endif;
                    $servicio = "";
                    $helpdesk->getRegistroStoreP("call pbNuevoServicio(" . $user_data['cod_servicio'] . "," . $user_data["no_cod_usuario_trasferido"] . "," . Session::get('cod') . ",-1,''," . $indAsigna . ",'SAA','".$user_data["nom_servicio"].' - '.$user_data["email_servicio"] . "',@cDesError,@cNumError)");                        
                    //Si el tiquet es asignado lo enviamos por correo electronico al usuario que se asigno
                    if($helpdesk->err!=2){
                        $helpdesk->get_datos("t1.nom_usuario,t1.email_usuario,t2.des_asignacion ", "t1.cod_usuario = " . $user_data["no_cod_usuario_trasferido"] . " and t1.cod_usuario=t2.cod_usuario and t2.cod_estado='STR' and t2.cod_servicio=".$user_data['cod_servicio']." order by cod_asignacion desc", "sys_usuario as t1, hd_asignacion as t2");
                        $data = $helpdesk->_data;
                        $helpdesk->get_datos('*', ' cod_estado="AAA"', ' hd_config');
                        $data[1]=$helpdesk->_data;
                        $email_array=array("Saludo"=>"Cordial Saludo: ".$data[0]["nom_usuario"]."",
                                           "Introduccion"=>$data[1][0]["asunto_config"],
                                           "Descripcion"=>$data[0]["des_asignacion"],
                                           "to"=>$data[0]["email_usuario"]);
                        sendEmail("sistema", 1, $email_array, $Objvista,$data);
                    }
                    $campo = array('cod_servicio= ');
                    $clave = array('-1');
                    $user_data["ind_revisado"]=1;
                break;
                case 'nuevaConfigHelpDesk':
                    $campo = array('nom_config= ');
                    $clave = array("'".$user_data['nom_config']."'");
                break;
                case 'nuevaAsignacion':
                    //var_dump($user_data);exit;
                    $valida=false;
                    if(!isset($user_data["no_cod_estado"][0])){$data = array('ERR'=>2,'MSJ'=>"El estado es obligatorio para este proceso");break 1;}
                    $user_data_1=array("cod_gestion"=>"","cod_asignacion"=>$user_data["cod_asignacion"],"des_gestion"=>$user_data["des_gestion"],
                                       "fec_gestion"=>date("Y-m-d H:i:s"),"hora_inicio"=>$user_data["hora_inicio"],"hora_fin"=>$user_data["hora_fin"],
                                       "no_esq_tabla"=>$user_data["no_esq_tabla"],"no_nom_tabla"=>"nuevaAsignacion",
                                       "cod_usuario"=>Session::get('cod'));
                    $helpdesk->setRegistro($user_data_1);
                    $helpdesk->err = 6;
                    $helpdesk->msj = " La gestion se registro correctamente";
                    if(isset($user_data["no_cod_estado"])){
                        switch ($user_data["no_cod_estado"]){
                            case 'SCC':
                                $user_data_2=array("cod_servicio"=>$user_data["no_cod_servicio"],"cod_estado"=>$user_data["no_cod_estado"],"fec_cierre"=>date("Y-m-d H:i:s"),
                                                   "no_esq_tabla"=>"hd_servicio","no_nom_tabla"=>"nuevaAsignacion");
                                $helpdesk->editRegistro($user_data_2);
                                $helpdesk->msj .= " El tiquet fue cerraado correctamente";
                            break;
                        }
                    }
                break;
            }
            if($valida){
                $helpdesk->editRegistro($user_data);
                $data = array('ERR'=>$helpdesk->err,
                              'MSJ'=>$helpdesk->msj);
                setVariables($helpdesk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$helpdesk->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data= array('ERR' => $helpdesk->err,
                             'MSJ' => $helpdesk->msj);
                setVariables($helpdesk,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $helpdesk->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$helpdesk->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
}

?>