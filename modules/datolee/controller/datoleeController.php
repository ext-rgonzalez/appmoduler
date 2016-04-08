<?php

class datoleeController {
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracion de usuario desde el modelo propio del
//             controlador.
    public function index() {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/datolee/model/datoleeModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        if (!Session::get('usuario')) {
            $Objvista = new view;
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $datolee = new ModeloDatolee();
            $Objvista = new view;
            $datolee->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa = $datolee->_empresa;
            $var = "";
            for ($t = 0; $t < count($datolee->_notificacion); $t++) {
                $var = $var . $datolee->_notificacion[$t]['des_notificacion'];
            }
            $Objvista->_notificacion = array('des_notificacion' => $var);
            $Objvista->_numNotificacion = $datolee->_numNotificacion;
            $var = "";
            for ($t = 0; $t < count($datolee->_mensajes); $t++) {
                $var = $var . $datolee->_mensajes[$t]['des_mensajes'];
            }
            $Objvista->_mensajes = array('des_mensajes' => $var);
            $Objvista->_numMensajes = $datolee->_numMensajes;
            $var = "";
            for ($t = 0; $t < count($datolee->_menu); $t++) {
                $var = $var . $datolee->_menu[$t]['menu'];
            }
            $Objvista->_menu = array('menu' => $var);
            $Objvista->_menuHeader = $datolee->_menuHeader;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'index', 'index');
        }
    }

    public function Config($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/datolee/model/datoleeModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $datolee = new ModeloDatolee();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $datolee->get_datos('*', "cod_config=" . $argumentos[3], $argumentos[4]);
                $data = $datolee->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $datolee->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $datolee->_data;
            setVariables($datolee,$Objvista,$metodo,$argumentos[1],$argumentos[0],'datolee_view_config',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'datolee','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function SubLideres($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/datolee/model/datoleeModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $datolee = new ModeloDatolee();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();$campoChek=array();$camposComboEsp = array();
            if (!empty($argumentos[3])) {
                $datolee->get_datos('*', "cod_sublider=" . $argumentos[3], $argumentos[4]);
                $data = $datolee->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"],"cod_lider" => $data[0]["cod_lider"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $datolee->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $datolee->_data;
            setVariables($datolee,$Objvista,$metodo,$argumentos[1],$argumentos[0],'datolee_view_sublideres',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'datolee','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Sectores($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/datolee/model/datoleeModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $datolee = new ModeloDatolee();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();$campoChek=array();$camposComboEsp = array();
            if(!empty($argumentos[3])){
                $datolee->get_datos('*',"cod_sector=".$argumentos[3],$argumentos[4]); $data=$datolee->_data;
                //var_dump($data);exit();
                $camposComboEsp[0] = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"]);  
            }
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $datolee->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $datolee->_data;
            setVariables($datolee,$Objvista,$metodo,$argumentos[1],$argumentos[0],'datolee_view_sector',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'datolee','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Votos($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/datolee/model/datoleeModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $datolee = new ModeloDatolee();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $datolee->get_datos('*', "cod_voto=" . $argumentos[3], $argumentos[4]);
                $data = $datolee->_data;
                $datolee->get_datos('cod_usuario', "cod_voto=" . $argumentos[3]." and cod_estado='AAA'", ' datolee_bandeja_voto');$data[0]["no_cod_usuario"] = isset($datolee->_data[0]["cod_usuario"]) ? $datolee->_data[0]["cod_usuario"] : array();
                $camposCombo = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"],"cod_sector"=>$data[0]["cod_sector"],
                                     "cod_ciudad"=>$data[0]["cod_ciudad"], "no_cod_usuario"=>$data[0]["no_cod_usuario"]);
                $arrayChek = array("ind_recogida" => $data[0]["ind_recogida"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $datolee->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $datolee->_data;
            setVariables($datolee,$Objvista,$metodo,$argumentos[1],$argumentos[0],'datolee_view_voto',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function GestionarVotos($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/datolee/model/datoleeModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $datolee     = new ModeloDatolee();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $datolee->get_datos("t1.*,t2.*,t1.cod_estado", "t1.cod_voto=" . $argumentos[3].' and t1.cod_voto=t2.cod_voto', $argumentos[4].' as t1, datolee_voto as t2');
                $data = $datolee->_data;
                $datolee->get_datos('cod_usuario', "cod_voto=" . $argumentos[3]." and cod_estado='AAA'", ' datolee_bandeja_voto');$data[0]["no_cod_usuario"] = !empty($datolee->_data[0]["cod_usuario"]) ? $datolee->_data[0]["cod_usuario"] : array();
                $camposCombo = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"],"cod_sector"=>$data[0]["cod_sector"],
                                     "cod_ciudad"=>$data[0]["cod_ciudad"],"no_cod_usuario"=>$data[0]["no_cod_usuario"]);
                $arrayChek = array("no_ind_recogida" => $data[0]["ind_recogida"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
                $datolee->get_datos('t1.obs_gestion as Descripcion,t1.fec_gestion as FechaGestion,t2.usuario_usuario as Usuario', ' t1.cod_voto=' . $argumentos[3] .' and t1.cod_usuario=t2.cod_usuario order by cod_gestion desc', 'datolee_gestion as t1, sys_usuario as t2');
                $desAnidada = armaTextAnidado($datolee->_data);
                $data[0]["ult_gestiones"] = !empty($desAnidada) ? $desAnidada : array();
            }
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            $datolee->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $datolee->_data;
            setVariables($datolee,$Objvista,$metodo,$argumentos[1],$argumentos[0],'datolee_view_voto',' cod_empresa in('.$cadEmp[0]['result'].') and cod_usuario_asignado='.Session::get('cod')." and fec_prox_voto<=date(now()) and cod_estado='AAA' order by Interno desc",$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function nuevoRegistro($metodo) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/datolee/model/datoleeModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $datolee = new ModeloDatolee();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR' => 3,'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $user_data = helper_user_data($metodo);
            switch ($user_data['no_nom_tabla']) {
                case 'NuevaConfig':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                break;
                case 'NuevaSubLideres':
                    $campo = array('ced_sublider' . "=");
                    $clave = array("'" . $user_data['ced_sublider'] . "'");
                    $user_data["fec_sublider"]=date("Y-m-d H:i:s");
                break;
                case 'NuevaSectores':
                    //var_dump($user_data); exit;
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["nom_sector"]);$e++){
                        $campo = array('nom_sector=');
                        $clave = array("'".$user_data['nom_sector'][$e]."'");
                        if(!$datolee->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('nom_sector'     =>$user_data["nom_sector"][$e],
                                              'des_sector'     =>$user_data["des_sector"][$e],
                                              'cod_empresa'    =>$user_data["cod_empresa"][$e],
                                              'cod_estado'     =>$user_data["cod_estado"][$e],
                                              'cod_usuario'    =>'',
                                              'cod_sector'     =>'',
                                              'fec_sector'     =>date("Y-m-d H:i:s"),
                                              'no_esq_tabla'     =>$user_data['no_esq_tabla'],
                                              'no_nom_tabla'     =>$user_data['no_nom_tabla'],
                                              'no_id_formulario' =>$user_data['no_id_formulario']);
                            $datolee->setRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    }	
                    $datolee->err = 6;
                    $datolee->msj = " Sectores creados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaVotos':
                    //var_dump($user_data);exit;
                    $campo = array("ced_voto=");
                    $clave = array("'" . $user_data['ced_voto'] . "'");
                    $user_data['fec_voto'] = date("Y-m-d H:i:s");
                    $user_data['ind_recogida'] = isset($user_data['ind_recogida']) ? $user_data['ind_recogida'] : 0;
                break;
            }
            if ($valida) {
                if (!$datolee->getRegistro($user_data['no_esq_tabla'], $campo, $clave)) {
                    $datolee->setRegistro($user_data);
                    $data = array('ERR' => 6,
                                  'MSJ' => $datolee->msj);
                    setVariables($datolee,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $datolee->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$datolee->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                } else {
                    $data = array('ERR' => 2,
                                  'MSJ' => "El registro ya existe");                    
                    setVariables($datolee,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $datolee->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$datolee->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                }
            }else{
                $data= array('ERR' => $datolee->err,
                             'MSJ' => $datolee->msj);
                setVariables($datolee,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $datolee->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$datolee->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
    
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/datolee/model/datoleeModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $datolee = new ModeloDatolee();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $datolee->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$datolee->_data;
            //var_dump($sistema->_data);exit();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch ($user_data['no_nom_tabla']) {
                case 'NuevaConfig':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                break;
                case 'NuevaSubLideres':
                    $campo = array('ced_sublider' . "=");
                    $clave = array("'" . $user_data['ced_sublider'] . "'");
                break;
                case 'NuevaSectores':
                    //var_dump($user_data); exit;
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["nom_sector"]);$e++){
                        $campo = array('nom_sector=');
                        $clave = array("'".$user_data['nom_sector'][$e]."'");
                        if(!$datolee->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('cod_sector'       =>$user_data["cod_sector"][$e],
                                              'nom_sector'       =>$user_data["nom_sector"][$e],
                                              'des_sector'       =>$user_data["des_sector"][$e],
                                              'cod_empresa'      =>$user_data["cod_empresa"][$e],
                                              'cod_estado'       =>$user_data["cod_estado"][$e],
                                              'cod_usuario'      =>'',
                                              'fec_mod_sector'   =>date("Y-m-d H:i:s"),
                                              'no_esq_tabla'     =>$user_data['no_esq_tabla'],
                                              'no_nom_tabla'     =>$user_data['no_nom_tabla'],
                                              'no_id_formulario' =>$user_data['no_id_formulario']);
                            $datolee->editRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    }	
                    $datolee->err = 6;
                    $datolee->msj = " Sectores editados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaVotos':
                    //var_dump($user_data);exit;
                    $campo = array("ced_voto=");
                    $clave = array("'" . $user_data['ced_voto'] . "'");
                    $user_data['fec_mod_voto'] = date("Y-m-d H:i:s");
                    $user_data['ind_recogida'] = isset($user_data['ind_recogida']) ? $user_data['ind_recogida'] : 0;
                break;
                case 'NuevaGestionarVotos':
                    $valida=false;
                    $user_data["fec_gestion"]= date("Y-m-d H:i:s");
                    $user_data["cod_voto"]=$user_data["no_cod_voto"];
                    $datolee->setRegistro($user_data);
                    $datolee->err=6;
                break;
            }
            if($valida){
                $datolee->editRegistro($user_data);
                $data = array('ERR'=>6,
                              'MSJ'=>$datolee->msj);
                setVariables($datolee,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $datolee->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$datolee->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data= array('ERR' => $datolee->err,
                             'MSJ' => $datolee->msj);
                setVariables($datolee,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $datolee->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$datolee->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
}

?>