<?php

class crmController {
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracion de usuario desde el modelo propio del
//             controlador.
    public function index() {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        if (!Session::get('usuario')) {
            $Objvista = new view;
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $crm = new ModeloCrm();
            $Objvista = new view;
            $crm->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa = $crm->_empresa;
            $var = "";
            for ($t = 0; $t < count($crm->_notificacion); $t++) {
                $var = $var . $crm->_notificacion[$t]['des_notificacion'];
            }
            $Objvista->_notificacion = array('des_notificacion' => $var);
            $Objvista->_numNotificacion = $crm->_numNotificacion;
            $var = "";
            for ($t = 0; $t < count($crm->_mensajes); $t++) {
                $var = $var . $crm->_mensajes[$t]['des_mensajes'];
            }
            $Objvista->_mensajes = array('des_mensajes' => $var);
            $Objvista->_numMensajes = $crm->_numMensajes;
            $var = "";
            for ($t = 0; $t < count($crm->_menu); $t++) {
                $var = $var . $crm->_menu[$t]['menu'];
            }
            $Objvista->_menu = array('menu' => $var);
            $Objvista->_menuHeader = $crm->_menuHeader;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'index', 'index');
        }
    }

    public function Productos($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $crm->get_datos('*', "cod_productos=" . $argumentos[3], $argumentos[4]);
                $data = $crm->_data;
                $crm->get_datos('cod_usuario as no_cod_usuario',"cod_productos=".$argumentos[3],'crm_productos_usuario');$data[0]["no_cod_usuario"]= !empty($crm->_data) ? $crm->_data : array();
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"],"no_cod_usuario"=>$data[0]["no_cod_usuario"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_productos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function SubProducto($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp = array();
            if (!empty($argumentos[3])) {
                $crm->get_datos('*', "cod_subproductos=" . $argumentos[3], $argumentos[4]);
                $data = $crm->_data;
                $camposComboEsp[0] = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_productos" => $data[0]["cod_productos"],"cod_proveedor" => $data[0]["cod_proveedor"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_subproductos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Medios($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $crm->get_datos('*', "cod_medios=" . $argumentos[3], $argumentos[4]);
                $data = $crm->_data;
                $camposComboEsp[0] = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado" => $data[0]["cod_estado"],"cod_productos" => $data[0]["cod_productos"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_medios',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function Proveedores($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $crm->get_datos('*', "cod_cliente=" . $argumentos[3], $argumentos[4]);
                $data = $crm->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"],"cod_tipopago" => $data[0]["cod_tipopago"],"cod_ciudad" => $data[0]["cod_ciudad"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_proveedores',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ClientesCrm($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$cicloInput=1;$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_subproductos" where cod_detframe=1691');
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_medios" where cod_detframe=1695');
                $crm->get_datos('*', "cod_cliente=" . $argumentos[3], $argumentos[4]);$data = $crm->_data;
                $crm->get_datos('t1.cod_productos as no_cod_productos', "t2.cod_cliente=" . $argumentos[3].' and t1.cod_subproductos=t2.cod_subproductos','crm_contacto as t2, crm_subproductos as t1');$data[0]["cod_productos"] = $crm->_data[0]["no_cod_productos"];
                $crm->get_datos('t1.*,t1.cod_usuario as no_cod_usuario,t2.cod_productos', "t1.cod_cliente=" . $argumentos[3].' and t1.cod_subproductos=t2.cod_subproductos', 'crm_contacto as t1, crm_subproductos as t2');
                $camposComboEsp = $crm->_data;$data[2]=$camposComboEsp;
                $camposCombo = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"],"cod_medios"=>$camposComboEsp[0]["cod_medios"],"cod_ciudad"=>$data[0]["cod_ciudad"],
                                     "cod_tipopago"=>$data[0]["cod_tipopago"],"cod_productos"=>$camposComboEsp[0]["cod_productos"],"cod_subproductos"=>$camposComboEsp[0]["cod_subproductos"]);
                $crm->get_datos('count(1) as ciclo', ' cod_cliente='.$argumentos[3], ' crm_contacto');
                $ciclo=$crm->_data;
                $cicloInput=$ciclo[0]["ciclo"];
            }else{
                $crm->set_simple_query('update sys_detframe set nom_tablaref="empty" where (nom_tablaref="crm_subproductos" Or nom_tablaref="crm_medios") and cod_frame=222');
            }
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_clientes',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ContactoCrm($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_subproductos" where cod_detframe=1698');
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_medios" where cod_detframe=1699');
                $crm->get_datos('*', "cod_contacto=" . $argumentos[3], $argumentos[4]);
                $data = $crm->_data;
                $crm->get_datos('t1.cod_productos', "t2.cod_contacto=" . $argumentos[3].' and t1.cod_subproductos=t2.cod_subproductos', $argumentos[4].' as t2, crm_subproductos as t1');$data[0]["cod_productos"] = isset($crm->_data[0]["cod_productos"]) ? $crm->_data[0]["cod_productos"] :  array();
                $crm->get_datos('cod_usuario', "cod_contacto=" . $argumentos[3]." and cod_estado='AAA'", ' crm_bandeja_asignacion');$data[0]["no_cod_usuario"] = isset($crm->_data[0]["cod_usuario"]) ? $crm->_data[0]["cod_usuario"] : array();
                $camposCombo = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"],"cod_medios"=>$data[0]["cod_medios"],
                                     "cod_productos"=>$data[0]["cod_productos"],"cod_cliente"=>$data[0]["cod_cliente"],"cod_subproductos"=>$data[0]["cod_subproductos"],
                                     "no_cod_usuario"=>$data[0]["no_cod_usuario"]);
            }else{
                $crm->set_simple_query('update sys_detframe set nom_tablaref="empty" where (nom_tablaref="crm_subproductos" Or nom_tablaref="crm_medios") and cod_frame=224 ');
            }
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_contactoclientes',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ClientesContactos($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_subproductos" where nom_tablaref="empty" and cod_frame=219');
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_medios" where nom_tablaref="empty" and cod_frame=218');
                $crm->get_datos('t1.*', "t2.cod_contacto=" . $argumentos[3].' and t1.cod_cliente=t2.cod_cliente', $argumentos[4].' as t2, fa_cliente as t1');$data = $crm->_data;
                $crm->get_datos('t1.cod_productos as no_cod_productos', "t2.cod_contacto=" . $argumentos[3].' and t1.cod_subproductos=t2.cod_subproductos', $argumentos[4].' as t2, crm_subproductos as t1');$data[0]["cod_productos"] = !empty($crm->_data[0]["no_cod_productos"]) ? $crm->_data[0]["no_cod_productos"] : array();
                $crm->get_datos('*,cod_usuario as no_cod_usuario', "cod_contacto=" . $argumentos[3], $argumentos[4]);
                $camposComboEsp = $crm->_data;$data[2]=$camposComboEsp;
                $camposCombo = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"],"cod_medios"=>$camposComboEsp[0]["cod_medios"],"cod_ciudad"=>$data[0]["cod_ciudad"],
                                     "cod_tipopago"=>$data[0]["cod_tipopago"],"cod_productos"=>$data[0]["cod_productos"]);
            }else{
                $crm->set_simple_query('update sys_detframe set nom_tablaref="empty" where (nom_tablaref="crm_subproductos" Or nom_tablaref="crm_medios") and (cod_frame=219 Or cod_frame=218)');
            }
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_contactoclientes',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function BandejaAsignacion($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_subproductos" where cod_detframe=1710');
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_medios" where cod_detframe=1711');
                $crm->get_datos("t1.*,t2.*,CONCAT(ifnull(t2.tel_cliente,''),'-',ifnull(t2.cel_cliente,''),'-',ifnull(t2.tel1_cliente,'')) AS tels_cliente", "t1.cod_contacto=" . $argumentos[3].' and t1.cod_cliente=t2.cod_cliente', $argumentos[4].' as t1, fa_cliente as t2');
                $data = $crm->_data;
                $crm->get_datos('t1.cod_productos', "t2.cod_contacto=" . $argumentos[3].' and t1.cod_subproductos=t2.cod_subproductos', $argumentos[4].' as t2, crm_subproductos as t1');$data[0]["cod_productos"] = !empty($crm->_data[0]["cod_productos"]) ? $crm->_data[0]["cod_productos"] : array();
                $crm->get_datos('cod_usuario', "cod_contacto=" . $argumentos[3]." and cod_estado='AAA'", ' crm_bandeja_asignacion');$data[0]["no_cod_usuario"] = !empty($crm->_data[0]["cod_usuario"]) ? $crm->_data[0]["cod_usuario"] : array();
                $camposCombo = array("cod_estado"=>"TRM","cod_empresa"=>$data[0]["cod_empresa"],"cod_medios"=>$data[0]["cod_medios"],
                                     "cod_productos"=>$data[0]["cod_productos"],"cod_cliente"=>$data[0]["cod_cliente"],"cod_subproductos"=>$data[0]["cod_subproductos"],
                                     "no_cod_usuario"=>$data[0]["no_cod_usuario"]);
                $crm->get_datos('t1.obs_gestion as Descripcion,t1.fec_gestion as FechaGestion,t2.usuario_usuario as Usuario', ' t1.cod_contacto=' . $argumentos[3] .' and t1.cod_usuario=t2.cod_usuario order by cod_gestion desc', 'crm_gestion as t1, sys_usuario as t2');
                $desAnidada = armaTextAnidado($crm->_data);
                $data[0]["ult_gestiones"] = !empty($desAnidada) ? $desAnidada : array();
            }else{
                $crm->set_simple_query('update sys_detframe set nom_tablaref="empty" where (nom_tablaref="crm_subproductos" Or nom_tablaref="crm_medios") and (cod_frame=219 Or cod_frame=226)');
            }
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_contactoclientes',' cod_empresa in('.$cadEmp[0]['result'].') and cod_usuario_asignado='.Session::get('cod')." and fec_prox_contacto<=date(now()) and cod_estado='AAA' order by Interno desc",$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function RptUsuario($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_subproductos" where cod_detframe=1829');
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_medios" where cod_detframe=1830');
                $crm->get_datos('*', "cod_contacto=" . $argumentos[3], $argumentos[4]);
                $data = $crm->_data;
                $crm->get_datos('t1.cod_productos', "t2.cod_contacto=" . $argumentos[3].' and t1.cod_subproductos=t2.cod_subproductos', $argumentos[4].' as t2, crm_subproductos as t1');$data[0]["cod_productos"] = isset($crm->_data[0]["cod_productos"]) ? $crm->_data[0]["cod_productos"] :  array();
                $crm->get_datos('cod_usuario', "cod_contacto=" . $argumentos[3]." and cod_estado='AAA'", ' crm_bandeja_asignacion');$data[0]["no_cod_usuario"] = isset($crm->_data[0]["cod_usuario"]) ? $crm->_data[0]["cod_usuario"] : array();
                $camposCombo = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"],"cod_medios"=>$data[0]["cod_medios"],
                                     "cod_productos"=>$data[0]["cod_productos"],"cod_cliente"=>$data[0]["cod_cliente"],"cod_subproductos"=>$data[0]["cod_subproductos"],
                                     "no_cod_usuario"=>$data[0]["no_cod_usuario"]);
            }else{
                $crm->set_simple_query('update sys_detframe set nom_tablaref="empty" where (nom_tablaref="crm_subproductos" Or nom_tablaref="crm_medios") and cod_frame=241 ');
            }
            $user_data = helper_user_data('nuevoRegistro');
            //var_dump($user_data);exit;
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            // filtros para el sql
            $cod_usuario      = isset($user_data["cod_usuario_asignado"]) ? $user_data["cod_usuario_asignado"] : '';
            $fec_ini_contacto = isset($user_data["fec_ini_contacto"]) ? $user_data["fec_ini_contacto"] : '';
            $fec_fin_contacto = isset($user_data["fec_fin_contacto"]) ? $user_data["fec_fin_contacto"] : ''; 
            // fin filtros
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_contactoclientes',' cod_empresa in('.$cadEmp[0]['result'].') and cod_usuario_asignado='. $cod_usuario ." and fec_contacto between '".$fec_ini_contacto."' and '".$fec_fin_contacto."' order by Interno desc",$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function RptUsuarioRegistra($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_subproductos" where cod_detframe=1845');
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_medios" where cod_detframe=1846');
                $crm->get_datos('*', "cod_contacto=" . $argumentos[3], $argumentos[4]);
                $data = $crm->_data;
                $crm->get_datos('t1.cod_productos', "t2.cod_contacto=" . $argumentos[3].' and t1.cod_subproductos=t2.cod_subproductos', $argumentos[4].' as t2, crm_subproductos as t1');$data[0]["cod_productos"] = isset($crm->_data[0]["cod_productos"]) ? $crm->_data[0]["cod_productos"] :  array();
                $crm->get_datos('cod_usuario', "cod_contacto=" . $argumentos[3]." and cod_estado='AAA'", ' crm_bandeja_asignacion');$data[0]["no_cod_usuario"] = isset($crm->_data[0]["cod_usuario"]) ? $crm->_data[0]["cod_usuario"] : array();
                $camposCombo = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"],"cod_medios"=>$data[0]["cod_medios"],
                                     "cod_productos"=>$data[0]["cod_productos"],"cod_cliente"=>$data[0]["cod_cliente"],"cod_subproductos"=>$data[0]["cod_subproductos"],
                                     "no_cod_usuario"=>$data[0]["no_cod_usuario"]);
            }else{
                $crm->set_simple_query('update sys_detframe set nom_tablaref="empty" where (nom_tablaref="crm_subproductos" Or nom_tablaref="crm_medios") and cod_frame=244 ');
            }
            $user_data = helper_user_data('nuevoRegistro');
            //var_dump($user_data);exit;
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            // filtros para el sql
            $cod_usuario      = isset($user_data["cod_usuario"]) ? $user_data["cod_usuario"] : '';
            $fec_ini_contacto = isset($user_data["fec_ini_contacto"]) ? $user_data["fec_ini_contacto"] : '';
            $fec_fin_contacto = isset($user_data["fec_fin_contacto"]) ? $user_data["fec_fin_contacto"] : ''; 
            // fin filtros
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_contactoclientes',' cod_empresa in('.$cadEmp[0]['result'].') and cod_usuario='. $cod_usuario ." and fec_contacto between '".$fec_ini_contacto."' and '".$fec_fin_contacto."' order by Interno desc",$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function RptporEstado($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();$camposComboEsp=array();
            if (!empty($argumentos[3])) {
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_subproductos" where cod_detframe=1845');
                $crm->set_simple_query('update sys_detframe set nom_tablaref="crm_medios" where cod_detframe=1846');
                $crm->get_datos('*', "cod_contacto=" . $argumentos[3], $argumentos[4]);
                $data = $crm->_data;
                $crm->get_datos('t1.cod_productos', "t2.cod_contacto=" . $argumentos[3].' and t1.cod_subproductos=t2.cod_subproductos', $argumentos[4].' as t2, crm_subproductos as t1');$data[0]["cod_productos"] = isset($crm->_data[0]["cod_productos"]) ? $crm->_data[0]["cod_productos"] :  array();
                $crm->get_datos('cod_usuario', "cod_contacto=" . $argumentos[3]." and cod_estado='AAA'", ' crm_bandeja_asignacion');$data[0]["no_cod_usuario"] = isset($crm->_data[0]["cod_usuario"]) ? $crm->_data[0]["cod_usuario"] : array();
                $camposCombo = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"],"cod_medios"=>$data[0]["cod_medios"],
                                     "cod_productos"=>$data[0]["cod_productos"],"cod_cliente"=>$data[0]["cod_cliente"],"cod_subproductos"=>$data[0]["cod_subproductos"],
                                     "no_cod_usuario"=>$data[0]["no_cod_usuario"]);
            }else{
                $crm->set_simple_query('update sys_detframe set nom_tablaref="empty" where (nom_tablaref="crm_subproductos" Or nom_tablaref="crm_medios") and cod_frame=244 ');
            }
            $user_data = helper_user_data('nuevoRegistro');
            //var_dump($user_data);exit;
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            $crm->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $crm->_data;
            // filtros para el sql
            $cod_estado       = isset($user_data["cod_usuario_estado"]) ? $user_data["cod_usuario_estado"] : '';
            $fec_ini_contacto = isset($user_data["fec_ini_contacto"]) ? $user_data["fec_ini_contacto"] : '';
            $fec_fin_contacto = isset($user_data["fec_fin_contacto"]) ? $user_data["fec_fin_contacto"] : ''; 
            // fin filtros
            setVariables($crm,$Objvista,$metodo,$argumentos[1],$argumentos[0],'crm_view_contactoclientes'," cod_empresa in(".$cadEmp[0]['result'].") and Estado='". $cod_estado ."' and fec_contacto between '".$fec_ini_contacto."' and '".$fec_fin_contacto."' order by Interno desc",$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'crm','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function nuevoRegistro($metodo) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $crm = new ModeloCrm();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR' => 3,'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $user_data = helper_user_data($metodo);
            switch ($user_data['no_nom_tabla']) {
                case 'NuevaProductos':
                    $campo = array('nom_productos' . "=");
                    $clave = array("'" . $user_data['nom_productos'] . "'");
                    $user_data['fec_productos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaSubProducto':
                    //var_dump($user_data); exit;
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_productos"]);$e++){
                        $campo = array('cod_productos=',' and nom_subproductos=');
                        $clave = array("'".$user_data['no_cod_productos'][$e]."'","'".$user_data['no_nom_subproductos'][$e]."'");
                        if(!$crm->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('nom_subproductos' =>$user_data["no_nom_subproductos"][$e],
                                              'des_subproductos' =>$user_data["no_des_subproductos"][$e],
                                              'cod_productos'    =>$user_data["no_cod_productos"][$e],
                                              'cod_proveedor'    =>$user_data["no_cod_proveedor"][$e],
                                              'no_nombre_img'    =>$user_data["no_nombre_img"][$e],
                                              'no_tamano_img'    =>$user_data["no_tamano_img"][$e],
                                              'no_tmp_img'       =>$user_data["no_tmp_img"][$e],
                                              'no_nombre_img1'   =>$user_data["no_nombre_img1"][$e],
                                              'no_tamano_img1'   =>$user_data["no_tamano_img1"][$e],
                                              'no_tmp_img1'      =>$user_data["no_tmp_img1"][$e],
                                              'fec_subproductos' =>date("Y-m-d H:i:s"),
                                              'cod_empresa'      =>$user_data["no_cod_empresa"][$e],
                                              'cod_estado'       =>$user_data['no_cod_estado'][$e],
                                              'no_esq_tabla'     =>$user_data['no_esq_tabla'],
                                              'no_nom_tabla'     =>$user_data['no_nom_tabla'],
                                              'no_id_formulario' =>$user_data['no_id_formulario'],
                                              'cod_usuario'      =>'');
                            $crm->setRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    }	
                    $crm->err = 6;
                    $crm->msj = " Sub productos creados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaMedios':
				//var_dump($user_data);exit;
					$valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_medios"]);$e++){
                        $campo = array('cod_medios=',' and nom_medios=');
                        $clave = array("'".$user_data['no_cod_medios'][$e]."'","'".$user_data['no_nom_medios'][$e]."'");
                        if(!$crm->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('nom_medios' =>$user_data["no_nom_medios"][$e],
                                              'des_medios' =>$user_data["no_des_medios"][$e],
                                              'cod_productos'    =>$user_data["no_cod_productos"][$e],
                                              'fec_medios' =>date("Y-m-d H:i:s"),
                                              'cod_empresa'      =>$user_data["no_cod_empresa"][$e],
                                              'cod_estado'       =>$user_data['no_cod_estado'][$e],
                                              'no_esq_tabla'     =>$user_data['no_esq_tabla'],
                                              'no_nom_tabla'     =>$user_data['no_nom_tabla'],
                                              'no_id_formulario' =>$user_data['no_id_formulario'],
                                              'cod_usuario'      =>'');
                            $crm->setRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    }	
                    $crm->err = 6;
                    $crm->msj = " Medios creados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaProveedores':
                    $campo = array('nit_cliente' . "=");
                    $clave = array("'" . $user_data['nit_cliente'] . "'");
                    $user_data['fec_cliente'] = date("Y-m-d H:i:s");
                    $user_data['ind_proveedor_cliente'] = 1;
                break;
                case 'NuevaClientesCrm':
                    $campo = array("nom_cliente=");
                    $clave = array("'" . $user_data['nom_cliente'] . "'");
                    $user_data['fec_cliente'] = date("Y-m-d H:i:s");
                    $user_data['ind_cliente_cliente'] = 1;
                    $user_data['cod_estado'] ='AAA';
                    $user_data['cod_ciudad'] = '1';
                    $user_data['ind_facturacion'] = 0;
                break;
                case 'NuevaClientesContactos':
                    $campo = array("nit_cliente=");
                    $clave = array("'" . $user_data['nit_cliente'] . "'");
                    $user_data['fec_cliente'] = date("Y-m-d H:i:s");
                    $user_data['ind_cliente_cliente'] = 1;
                    $user_data['ind_facturacion'] = 0;
                break;
                case 'NuevaContacto':
                    $campo = array("obs_contacto=");
                    $clave = array("'" . $user_data['obs_contacto'] . "'");
                    $user_data['fec_contacto'] = date("Y-m-d H:i:s");
                    //var_dump($user_data);exit;
                break;
            }
            if ($valida) {
                if (!$crm->getRegistro($user_data['no_esq_tabla'], $campo, $clave)) {
                    $crm->setRegistro($user_data);
                    $data = array('ERR' => 6,
                                  'MSJ' => $crm->msj);
                    setVariables($crm,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $crm->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$crm->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                } else {
                    $data = array('ERR' => 2,
                                  'MSJ' => "El registro ya existe");                    
                    setVariables($crm,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $crm->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$crm->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                }
            }else{
                $data= array('ERR' => $crm->err,
                             'MSJ' => $crm->msj);
                setVariables($crm,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $crm->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$crm->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
    
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/crm/model/crmModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $crm  = new ModeloCrm();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $crm->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$crm->_data;
            //var_dump($sistema->_data);exit();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch ($user_data['no_nom_tabla']) {
                case 'NuevaProductos':
                    $campo = array('nom_productos' . "=");
                    $clave = array("'" . $user_data['nom_productos'] . "'");
                    $user_data['fec_mod_productos'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaSubProducto':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_productos"]);$e++){
                        $user_data1=array('cod_subproductos'     =>$user_data["no_cod_subproductos"][$e],
                                          'nom_subproductos'     =>$user_data["no_nom_subproductos"][$e],
                                          'des_subproductos'     =>$user_data["no_des_subproductos"][$e],
                                          'cod_productos'        =>$user_data["no_cod_productos"][$e],
                                          'cod_proveedor'        =>$user_data["no_cod_proveedor"][$e],
                                          'no_nombre_img'        =>$user_data["no_nombre_img"][$e],
                                          'no_tamano_img'        =>$user_data["no_tamano_img"][$e],
                                          'no_tmp_img'           =>$user_data["no_tmp_img"][$e],
                                          'no_nombre_img1'       =>$user_data["no_nombre_img1"][$e],
                                          'no_tamano_img1'       =>$user_data["no_tamano_img1"][$e],
                                          'no_tmp_img1'          =>$user_data["no_tmp_img1"][$e],
                                          'fec_mod_subproductos' =>date("Y-m-d H:i:s"),
                                          'cod_empresa'          =>$user_data["no_cod_empresa"][$e],
                                          'cod_estado'           =>$user_data['no_cod_estado'][$e],
                                          'no_esq_tabla'         =>$user_data['no_esq_tabla'],
                                          'no_nom_tabla'         =>$user_data['no_nom_tabla'],
                                          'no_id_formulario'     =>$user_data['no_id_formulario'],
                                          'cod_usuario'          =>'');
                        $crm->editRegistro($user_data1);
                        $sinErr += + 1;
                    }	
                    $crm->err = 6;
                    $crm->msj = " Sub Productos Modificados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;   
                case 'NuevaMedios':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_medios"]);$e++){
                        $user_data1=array('cod_medios'     =>$user_data["no_cod_medios"][$e],
                                          'nom_medios'     =>$user_data["no_nom_medios"][$e],
                                          'des_medios'     =>$user_data["no_des_medios"][$e],
                                          'cod_productos'        =>$user_data["no_cod_productos"][$e],
                                          'fec_mod_medios' =>date("Y-m-d H:i:s"),
                                          'cod_empresa'          =>$user_data["no_cod_empresa"][$e],
                                          'cod_estado'           =>$user_data['no_cod_estado'][$e],
                                          'no_esq_tabla'         =>$user_data['no_esq_tabla'],
                                          'no_nom_tabla'         =>$user_data['no_nom_tabla'],
                                          'no_id_formulario'     =>$user_data['no_id_formulario'],
                                          'cod_usuario'          =>'');
                        $crm->editRegistro($user_data1);
                        $sinErr += + 1;
                    }	
                    $crm->err = 6;
                    $crm->msj = " Medios Modificados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'NuevaProveedores':
                    $campo = array('nit_cliente' . "=");
                    $clave = array("'" . $user_data['nit_cliente'] . "'");
                    $user_data['fec_mod_cliente'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaClientesCrm':
                    $campo = array('nit_cliente' . "=");
                    $clave = array("'" . $user_data['nit_cliente'] . "'");
                    $user_data['fec_mod_cliente'] = date("Y-m-d H:i:s");
                    $user_data['ind_cliente_cliente'] = 1;
                break;
                case 'NuevaClientesContactos':
                    $campo = array("nit_cliente=");
                    $clave = array("'" . $user_data['nit_cliente'] . "'");
                    $user_data['fec_mod_cliente'] = date("Y-m-d H:i:s");
                break;
                case 'NuevaContacto':
                    $campo = array("obs_contacto=");
                    $clave = array("'" . $user_data['obs_contacto'] . "'");
                    $user_data['fec_mod_contacto'] = date("Y-m-d H:i:s");
                    //var_dump($user_data);exit;
                break;
                case 'NuevaRptUsuario':
                    $campo = array("obs_contacto=");
                    $clave = array("'" . $user_data['obs_contacto'] . "'");
                    $user_data['fec_mod_contacto'] = date("Y-m-d H:i:s");
                    //var_dump($user_data);exit;
                break;
                case 'NuevaRptUsuarioRegistra':
                    $campo = array("obs_contacto=");
                    $clave = array("'" . $user_data['obs_contacto'] . "'");
                    $user_data['fec_mod_contacto'] = date("Y-m-d H:i:s");
                    //var_dump($user_data);exit;
                break;
                case 'NuevaRptporEstado':
                    $campo = array("obs_contacto=");
                    $clave = array("'" . $user_data['obs_contacto'] . "'");
                    $user_data['fec_mod_contacto'] = date("Y-m-d H:i:s");
                    //var_dump($user_data);exit;
                break;
                case 'NuevaBandejaAsignacion':
                    $valida=false;
                    $user_data["fec_gestion"]= date("Y-m-d H:i:s");
                    $user_data["cod_contacto"]=$user_data["no_cod_contacto"];
                    $crm->setRegistro($user_data);
                    $crm->err=6;
                break;
            }
            if($valida){
                $crm->editRegistro($user_data);
                $data = array('ERR'=>6,
                              'MSJ'=>$crm->msj);
                setVariables($crm,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $crm->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$crm->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data= array('ERR' => $crm->err,
                             'MSJ' => $crm->msj);
                setVariables($crm,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $crm->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$crm->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
}

?>