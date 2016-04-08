<?php

class facturacionController {
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracion de usuario desde el modelo propio del
//             controlador.
    public function index() {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        if (!Session::get('usuario')) {
            $Objvista = new view;
            $data = array('ERR' => 3,
                'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'facturacion', 'login', 'login', $data);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $facturacion = new ModeloFacturacion();
            $Objvista = new view;
            $facturacion->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa = $facturacion->_empresa;
            $var = "";
            for ($t = 0; $t < count($facturacion->_notificacion); $t++) {
                $var = $var . $facturacion->_notificacion[$t]['des_notificacion'];
            }
            $Objvista->_notificacion = array('des_notificacion' => $var);
            $Objvista->_numNotificacion = $facturacion->_numNotificacion;
            $var = "";
            for ($t = 0; $t < count($facturacion->_mensajes); $t++) {
                $var = $var . $facturacion->_mensajes[$t]['des_mensajes'];
            }
            $Objvista->_mensajes = array('des_mensajes' => $var);
            $Objvista->_numMensajes = $facturacion->_numMensajes;
            $var = "";
            for ($t = 0; $t < count($facturacion->_menu); $t++) {
                $var = $var . $facturacion->_menu[$t]['menu'];
            }
            $Objvista->_menu = array('menu' => $var);
            $Objvista->_menuHeader = $facturacion->_menuHeader;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'facturacion', 'index', 'indexFacturacion');
        }
    }

    public function configFacturacion($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_config=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
                $arrayChek = array("ind_retenciones_config" => $data[0]["ind_retenciones_config"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_config',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function tipoImpuesto($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_tipoimpuesto=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_tipoimpuesto','',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function metodoPago($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$camposComboEsp=array();$cicloInput=2;
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_met_pago=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
                $cadenaSql=" cod_proceso, cod_categoria, cod_naturaleza, cod_empresa";
                $facturacion->get_datos($cadenaSql, 'cod_met_pago='.$data[0]["cod_met_pago"], 'con_proceso_transaccion');
                $camposComboEsp=$facturacion->_data;
                //invocamos el ciclo para este proceso para cargar los items facturados
                $facturacion->get_datos('count(1) as ciclo', 'cod_met_pago='.$data[0]["cod_met_pago"], 'con_proceso_transaccion');
                $ciclo=$facturacion->_data;
                $cicloInput=$ciclo[0]["ciclo"];$cicloInput==0 ? $cicloInput=2 : $cicloInput ; 
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_met_pago','',$camposCombo,'',$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function impuestos($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$camposComboEsp=array();$cicloInput=2;
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_impuesto=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_tipo_impuesto" => $data[0]["cod_tipoimpuesto"]);
                $cadenaSql=" cod_proceso, cod_categoria, cod_naturaleza, cod_empresa";
                $facturacion->get_datos($cadenaSql, 'cod_impuesto='.$data[0]["cod_impuesto"], 'con_proceso_transaccion');
                $camposComboEsp=$facturacion->_data;
                //invocamos el ciclo para este proceso para cargar los items facturados
                $facturacion->get_datos('count(1) as ciclo', 'cod_impuesto='.$data[0]["cod_impuesto"], 'con_proceso_transaccion');
                $ciclo=$facturacion->_data;
                $cicloInput=$ciclo[0]["ciclo"];$cicloInput==0 ? $cicloInput=2 : $cicloInput ; 
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_impuesto','',$camposCombo,'',$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function tipoPago($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_tipopago=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_tipopago','',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function uniMedidas($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_unimedida=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_unimedida','',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function moneda($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_moneda=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_moneda','',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function numeracion($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_numeracion=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
                $arrayChek = array("ind_preferida_numeracion" => $data[0]["ind_preferida_numeracion"], "ind_auto_numeracion" => $data[0]["ind_auto_numeracion"],
                                   "ind_factura"=>$data[0]["ind_factura"],"ind_cotizacion"=>$data[0]["ind_cotizacion"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_numeracion',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function regimen($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_regimen=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_regimen','',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function item($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_item=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_impuesto" => $data[0]["cod_impuesto"], "cod_cuenta" => $data[0]["cod_cuenta"],
                                     "cod_empresa" => $data[0]["cod_empresa"]);
                $arrayChek = array("ind_inventario_item" => $data[0]["ind_inventario_item"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_item',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);  
        }
    }

    public function inventario($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_inventario=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_item_inventario" => $data[0]["cod_item"], "cod_uni_medida" => $data[0]["cod_unimedida"],
                                     "cod_empresa"=>$data[0]["cod_empresa"],"cod_estado"=>$data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_inventario',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral); 
        }
    }

    public function salidaInventario($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_inventario_aud=" . $argumentos[3], $argumentos[4]);$data = $facturacion->_data;
                $camposCombo = array("cod_inventario" => $data[0]["cod_inventario"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_salida_inventario',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function entradaInventario($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_inventario_aud=" . $argumentos[3], $argumentos[4]);$data = $facturacion->_data;
                $camposCombo = array("cod_inventario" => $data[0]["cod_inventario"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_entrada_inventario',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function ajusteInventario($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_inventario_ajuste=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_inventario" => $data[0]["cod_inventario"], "cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_ajuste_inventario',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral); 
        }
    }
    
    public function kardexInventario($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek = array();
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_kardex_inventario',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral); 
        }
    }
    
    public function factura($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$camposComboEsp = array();
            $campoChek = array();$ciclo=array();$cicloInput=2;$dataFormGeneral=array();
            if (!empty($argumentos[3])) {
                $facturacion->set_simple_query('update sys_detframe set nom_tablaref="fa_descuentos_gen" where nom_tablaref="fa_descuentos"');
                $facturacion->get_datos('*', "cod_factura=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_cliente" =>$data[0]["cod_cliente"],"cod_tipopago" =>$data[0]["cod_tipopago"],"cod_empresa"=>$data[0]["cod_empresa"],"cod_met_pago"=>$data[0]["cod_met_pago"]);
                $arrayChek   = array("ind_recurrente_factura" => $data[0]["ind_recurrente_factura"], "ind_cotizacion" => $data[0]["ind_cotizacion"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    $valorChek == 1 ? $campoChek[]=$llave : $campoChek[]="";
                }
                $cadenaSql="a.cod_item as cod_item,a.can_detalle as can_detalle, a.cod_descuento as cod_descuento, a.cod_impuesto as cod_impuesto,(a.imp_detalle / a.can_detalle) as imp,b.ref_item as ref_item";
                $facturacion->get_datos($cadenaSql, 'a.cod_factura='.$data[0]["cod_factura"] . ' and a.cod_item=b.cod_item', 'fa_detalle as a, fa_item as b');
                $camposComboEsp=$facturacion->_data;
                //invocamos el ciclo para este proceso para cargar los items facturados
                $facturacion->get_datos('count(1) as ciclo', 'cod_factura='.$data[0]["cod_factura"], 'fa_detalle');
                $ciclo=$facturacion->_data;
                $cicloInput=$ciclo[0]["ciclo"];
            }else{
                $facturacion->set_simple_query('update sys_detframe set nom_tablaref="fa_descuentos" where nom_tablaref="fa_descuentos_gen"');
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_factura',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function facturacion($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$camposComboEsp = array();
            $campoChek = array();$ciclo=array();$cicloInput=3;$dataFormGeneral=array();
            if (!empty($argumentos[3])) {
                $facturacion->set_simple_query('update sys_detframe set nom_tablaref="fa_descuentos_gen" where nom_tablaref="fa_descuentos"');
                $facturacion->get_datos('*', "cod_factura=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_cliente" => $data[0]["cod_cliente"],"cod_tipopago" => $data[0]["cod_tipopago"],"cod_empresa"=> $data[0]["cod_empresa"],"cod_met_pago"=>$data[0]["cod_met_pago"]);
                $arrayChek = array("ind_recurrente_factura" => $data[0]["ind_recurrente_factura"], "ind_cotizacion" => $data[0]["ind_cotizacion"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
                $cadenaSql="a.cod_item as cod_item,a.can_detalle as can_detalle, a.cod_descuento as cod_descuento, a.cod_impuesto as cod_impuesto,(a.imp_detalle / a.can_detalle) as imp,b.ref_item as ref_item";
                $facturacion->get_datos($cadenaSql, 'a.cod_factura='.$data[0]["cod_factura"] . ' and a.cod_item=b.cod_item', 'fa_detalle as a, fa_item as b');
                $camposComboEsp=$facturacion->_data;
                //invocamos el ciclo para este proceso para cargar los items facturados
                $facturacion->get_datos('count(1) as ciclo', 'cod_factura='.$data[0]["cod_factura"], 'fa_detalle');
                $ciclo=$facturacion->_data;
                $cicloInput=$ciclo[0]["ciclo"];
            }else{
                $facturacion->set_simple_query('update sys_detframe set nom_tablaref="fa_descuentos" where nom_tablaref="fa_descuentos_gen"');
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_facturacion',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function cliente($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$camposComboEsp = array();$campoChek = array();$ciclo=array();$cicloInput=1;
            $dataFormGeneral=array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_cliente=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_ciudad" => $data[0]["cod_ciudad"],"cod_empresa" => $data[0]["cod_empresa"],"cod_tipopago"=> $data[0]["cod_tipopago"]);
                $arrayChek = array("ind_cliente_cliente" => $data[0]["ind_cliente_cliente"], "ind_proveedor_cliente" => $data[0]["ind_proveedor_cliente"]);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
                $cadenaSql="a.cod_cliente_asociado as cod_cliente_asociado,a.nom_cliente_asociado as nom_cliente_asociado, a.email_cliente_asociado as email_cliente_asociado, a.tel_cliente_asociado as tel_cliente_asociado,a.cel_cliente_asociado as cel_cliente_asociado ";
                $facturacion->get_datos($cadenaSql, 'a.cod_cliente='.$data[0]["cod_cliente"] . ' and a.cod_cliente=b.cod_cliente', 'fa_cliente_asociado as a,fa_cliente as b');
                $camposComboEsp=$facturacion->_data;
                //invocamos el ciclo para este proceso para cargar los items facturados
                $facturacion->get_datos('count(1) as ciclo', 'cod_cliente='.$data[0]["cod_cliente"], 'fa_cliente_asociado');
                $ciclo=$facturacion->_data;
                $cicloInput=$ciclo[0]["ciclo"];
            }
            if($cicloInput==0){$cicloInput=1;};
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_cliente',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function descuento($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$camposComboEsp=array();$cicloInput=2;
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_descuentos=" . $argumentos[3], $argumentos[4]);
                $data = $facturacion->_data;
                $camposCombo = array("cod_estado" => $data[0]["cod_estado"],"cod_empresa" => $data[0]["cod_empresa"]);
                $cadenaSql=" cod_proceso, cod_categoria, cod_naturaleza, cod_empresa";
                $facturacion->get_datos($cadenaSql, 'cod_descuentos='.$data[0]["cod_descuentos"], 'con_proceso_transaccion');
                $camposComboEsp=$facturacion->_data;
                //invocamos el ciclo para este proceso para cargar los items facturados
                $facturacion->get_datos('count(1) as ciclo', 'cod_descuentos='.$data[0]["cod_descuentos"], 'con_proceso_transaccion');
                $ciclo=$facturacion->_data;
                $cicloInput=$ciclo[0]["ciclo"];$cicloInput==0 ? $cicloInput=2 : $cicloInput ; 
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_descuentos',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,'',$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }

    public function orden($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_orden=" . $argumentos[3], $argumentos[4]);$data = $facturacion->_data;
                $camposCombo = array("cod_factura" => $data[0]["cod_factura"],"cod_empresa" => $data[0]["cod_empresa"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_orden',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function pagosRecibidos($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek=array();$camposComboEsp=array();$cAux="";$cAux1="";
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_pago=" . $argumentos[3], $argumentos[4]);$data = $facturacion->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_met_pago"=>$data[0]["cod_met_pago"],"cod_cliente"=>$data[0]["cod_cliente"],
                                     "cod_categoria"=>$data[0]["cod_cuenta"]);
                !empty($data[0]["cod_factura"]) ? $check = "no_ind_factura" : $check = "no_ind_categoria";
                $arrayChek = array($check=>1);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
                $cadenaSql="a.cod_factura as cod_factura,a.cod_categoria as cod_categoria, a.imp_detpago as imp_detpago_fac, a.cod_retencion as cod_retencion,a.cod_impuesto as cod_impuesto,a.imp_detpago as imp_detpago_cat ";
                if(!empty($data[0]["cod_factura"]) and $data[0]["cod_factura"]<>0):
                    $cadenaSql .= ", (c.sub_total_factura - c.sub_totaldes_factura) + c.imp_factura as imp_factura, c.imp_cancelado as pago_factura, c.imp_adeudado as adeudado_factura ";
                    $cAux       = " and a.cod_factura=c.cod_factura group by a.cod_factura";
                    $cAux1      = " , fa_factura as c";
                endif;
                $facturacion->get_datos($cadenaSql, 'a.cod_pago='.$data[0]["cod_pago"] . ' and a.cod_pago=b.cod_pago' . $cAux , ' fa_detpago as a,fa_pago as b ' . $cAux1);
                $camposComboEsp=$facturacion->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_ingreso',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function pagosRealizado($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek=array();$camposComboEsp=array();$cAux="";$cAux1="";
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_pago=" . $argumentos[3], $argumentos[4]);$data = $facturacion->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_met_pago"=>$data[0]["cod_met_pago"],"cod_cliente"=>$data[0]["cod_cliente"],
                                     "cod_categoria"=>$data[0]["cod_cuenta"],"cod_cliente_asociado"=>$data[0]["cod_cliente_asociado"]);
                !empty($data[0]["cod_factura"]) ? $check = "no_ind_factura" : $check = "no_ind_categoria";
                $arrayChek = array($check=>1);
                foreach ($arrayChek as $llave => $valorChek) {
                    if ($valorChek == 1) {
                        $campoChek[] = $llave;
                    }
                }
                $cadenaSql="a.cod_factura as cod_factura,a.cod_categoria as cod_categoria, a.imp_detpago as imp_detpago_fac, a.cod_retencion as cod_retencion,a.cod_impuesto as cod_impuesto,a.imp_detpago as imp_detpago_cat ";
                if(!empty($data[0]["cod_factura"])){ 
                    $cadenaSql .= ", (sum(c.sub_total_factura) - sum(c.sub_totaldes_factura)) + sum(c.imp_factura) as imp_factura, c.imp_cancelado as pago_factura, c.imp_adeudado as adeudado_factura ";
                    $cAux       = " and a.cod_factura=c.cod_factura ";
                    $cAux1      = " , fa_factura as c";
                }
                $facturacion->get_datos($cadenaSql, 'a.cod_pago='.$data[0]["cod_pago"] . ' and a.cod_pago=b.cod_pago ' . $cAux , ' fa_detpago as a,fa_pago as b ' . $cAux1);
                $camposComboEsp=$facturacion->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_egreso',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function retencion($metodo, $argumentos = array()) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        } else {
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data = array();$camposCombo = array();$campoChek=array();$camposComboEsp=array();$cicloInput=2;
            if (!empty($argumentos[3])) {
                $facturacion->get_datos('*', "cod_retencion=" . $argumentos[3], $argumentos[4]);$data = $facturacion->_data;
                $camposCombo = array("cod_empresa" => $data[0]["cod_empresa"],"cod_estado"=>$data[0]["cod_estado"],"cod_tipo_aplicacion"=>$data[0]["cod_tipo_aplicacion"]);
                $cadenaSql=" cod_proceso, cod_categoria, cod_naturaleza, cod_empresa";
                $facturacion->get_datos($cadenaSql, 'cod_retencion='.$data[0]["cod_retencion"], 'con_proceso_transaccion');
                $camposComboEsp=$facturacion->_data;
                //invocamos el ciclo para este proceso para cargar los items facturados
                $facturacion->get_datos('count(1) as ciclo', 'cod_retencion='.$data[0]["cod_retencion"], 'con_proceso_transaccion');
                $ciclo=$facturacion->_data;
                $cicloInput=$ciclo[0]["ciclo"];$cicloInput==0 ? $cicloInput=2 : $cicloInput ;
            }
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            setVariables($facturacion,$Objvista,$metodo,$argumentos[1],$argumentos[0],'fa_view_retencion','',$camposCombo,$campoChek,$cicloInput,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            if(isset($argumentos[5])){$argumentos[5]==1 ? $Objvista->_enabled="disabled" : $Objvista->_enabled="";}
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'facturacion','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
    public function nuevoRegistro($metodo) {
        require_once ROOT . DEFAULT_CORE;
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $facturacion = new ModeloFacturacion();
        $Objvista    = new view;
        if (!Session::get('usuario')) {
            $data = array('ERR' => 3,'MSJ' => 'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER, 'sistema', 'login', 'login', $data);
        } else {
            $user_data = helper_user_data($metodo);
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaFacConfig':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                    $user_data['fec_config'] = date("Y-m-d H:i:s");
                break;
                case 'nuevaTipoImpuesto':
                    $campo = array('nom_tipoimpuesto' . "=");
                    $clave = array("'" . $user_data['nom_tipoimpuesto'] . "'");
                    $user_data['fec_impuesto'] = date("Y.m.d H:i:s");
                break;
                case 'nuevaImpuesto':
                    $campo = array('nom_impuesto' . "=", 'por_impuesto' . "=");
                    $clave = array("'" . $user_data['nom_impuesto'] . "'" . ' and ', $user_data['por_impuesto']);
                    $user_data['fec_impuesto'] = date("Y-m-d H:i:s");
                    $user_data["no_cod_clave"]="cod_impuesto";
                break;
                case 'nuevaTipoPago':
                    $campo = array('nom_tipopago' . "=", 'num_dias_tipopago' . "=");
                    $clave = array("'" . $user_data['nom_tipopago'] . "'" . ' and ', $user_data['num_dias_tipopago']);
                    $user_data['fec_tipoPago'] = date("Y.m.d");
                    $user_data['hora_tipoPago'] = date("H:i:s");
                    $user_data['cod_usuario']=Session::get('cod');
                break;
                case 'nuevaUniMedida':
                    $campo = array('nom_unimedida' . "=", 'pre_unimedida' . "=");
                    $clave = array("'" . $user_data['nom_unimedida'] . "'" . ' and ', "'" . $user_data['pre_unimedida'] . "'");
                    $user_data['fec_unimedida'] = date("Y.m.d");
                    $user_data['hora_unimedida'] = date("H:i:s");
                break;
                case 'nuevaMoneda':
                    $campo = array('nom_moneda' . "=", 'abr_moneda' . "=");
                    $clave = array("'" . $user_data['nom_moneda'] . "'" . ' and ', "'" . $user_data['abr_moneda'] . "'");
                break;
                case 'nuevaNumeracion':
                    $campo = array('nom_numeracion' . "=", 'pre_numeracion' . "=");
                    $clave = array("'" . $user_data['nom_numeracion'] . "'" . ' and ', "'" . $user_data['pre_numeracion'] . "'");
                    $user_data['fec_numeracion'] = date("Y-m-d H:i:s");
                    $user_data['num_sig_numeracion'] = $user_data['num_inicial_numeracion'] + 1;
                    $user_data["ind_preferida_numeracion"]=isset($user_data["ind_preferida_numeracion"]) ? $user_data["ind_preferida_numeracion"] : "0";
                    $user_data["ind_auto_numeracion"]=isset($user_data["ind_auto_numeracion"]) ? $user_data["ind_auto_numeracion"] : "0";
                    $user_data["ind_factura"]=isset($user_data["ind_factura"]) ? $user_data["ind_factura"] : "0";
                    $user_data["ind_cotizacion"]=isset($user_data["ind_cotizacion"]) ? $user_data["ind_cotizacion"] : "0";
                break;
                case 'nuevaRegimen':
                    $campo = array('nom_regimen' . "=");
                    $clave = array("'" . $user_data['nom_regimen'] . "'");
                break;
                case 'nuevaItem':
                    $campo = array('nom_item' . "=", 'ref_item' . "=");
                    $clave = array("'" . $user_data['nom_item'] . "'" . ' and ', "'" . $user_data['ref_item'] . "'");
                    $user_data['imp_venta'] = $user_data['imp_compra_item'] + (($user_data['imp_compra_item'] * $user_data['inc_porcen_item']) / 100);
                break;
                case 'nuevaInventario':
                    $user_data["imp_uni_inventario"]=$user_data["total_inventario"]/$user_data["entrada_inventario"];
                    $campo = array('cod_item' . "=");
                    $clave = array("'" . $user_data['cod_item'] . "'");
                    $user_data["existencia_inventario"]=$user_data["entrada_inventario"];    
                    $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_proceso=".$user_data["no_cod_proceso_interno"]." and ind_propio=1"," con_proceso_transaccion " );
                    if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las la cuenta contable interna para este proceso. ";break 1;}
                    $user_data["no_cod_categoria"][0]=$facturacion->_data[0]["cod_categoria"];
                    $user_data["no_cod_naturaleza"][0]=$facturacion->_data[0]["cod_naturaleza"];
                    $user_data["no_imp_categoria"][0]=$user_data["no_total_inventariado"];
                    if(isset($user_data["no_met_pago"]) And $user_data["no_met_pago"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_met_pago=".$user_data["no_met_pago"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar el tipo de pago para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][1]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][1]=$user_data["total_inventario"];
                        $user_data["no_cod_naturaleza"][1]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    if(isset($user_data["no_cod_impuesto"]) And $user_data["no_cod_impuesto"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_impuesto=".$user_data["no_cod_impuesto"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion" );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar los impuestos para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][2]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][2]=$user_data["no_imp_impuesto"];
                        $user_data["no_cod_naturaleza"][2]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    $count = count($user_data["no_cod_categoria"]);
                    for ($t=0;$t<count($user_data["no_cod_retencion"]);$t++):
                        if($user_data["no_cod_retencion"][$t]>0 ):
                            $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_retencion=".$user_data["no_cod_retencion"][$t]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                            if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las retenciones para este proceso. ";break 2;}
                            $user_data["no_cod_categoria"][$count]=$facturacion->_data[0]["cod_categoria"];
                            $user_data["no_imp_categoria"][$count]=$user_data["no_imp_retencion"][$t];
                            $user_data["no_cod_naturaleza"][$count]=$facturacion->_data[0]["cod_naturaleza"];
                            $count += 1;
                        endif;
                    endfor;
                    $facturacion->get_datos(" IF(MAX(cod_cab_mov_contable IS NOT NULL),MAX(cod_cab_mov_contable + 1),1) as cod_cab_contable", "", "con_cab_mov_contable");
                    $user_data["no_cod_cab_contable"]=$facturacion->_data[0]["cod_cab_contable"];
                break;
                case 'nuevaSalidaInventario':
                    $user_data["imp_inventario_aud"]=$user_data["no_total_inventario"]/$user_data["cantidad_inventario_aud"];
                    $valida=false;
                    $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
                    $cadEmp = $facturacion->_data;
                    $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_proceso=".$user_data["no_cod_proceso_interno"]." and ind_propio=1"," con_proceso_transaccion " );
                    if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las la cuenta contable interna para este proceso. ";break 1;}
                    $user_data["no_cod_categoria"][0]=$facturacion->_data[0]["cod_categoria"];
                    $user_data["no_cod_naturaleza"][0]=$facturacion->_data[0]["cod_naturaleza"];
                    $user_data["no_imp_categoria"][0]=$user_data["no_total_inventariado"];
                    if(isset($user_data["no_met_pago"]) And $user_data["no_met_pago"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_met_pago=".$user_data["no_met_pago"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar el tipo de pago para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][1]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][1]=$user_data["no_total_inventario"];
                        $user_data["no_cod_naturaleza"][1]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    if(isset($user_data["no_cod_impuesto"]) And $user_data["no_cod_impuesto"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_impuesto=".$user_data["no_cod_impuesto"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion" );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar los impuestos para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][2]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][2]=$user_data["no_imp_impuesto"];
                        $user_data["no_cod_naturaleza"][2]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    $count = count($user_data["no_cod_categoria"]);
                    for ($t=0;$t<count($user_data["no_cod_retencion"]);$t++):
                        if($user_data["no_cod_retencion"][$t]>0 ):
                            $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_retencion=".$user_data["no_cod_retencion"][$t]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                            if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las retenciones para este proceso. ";break 2;}
                            $user_data["no_cod_categoria"][$count]=$facturacion->_data[0]["cod_categoria"];
                            $user_data["no_imp_categoria"][$count]=$user_data["no_imp_retencion"][$t];
                            $user_data["no_cod_naturaleza"][$count]=$facturacion->_data[0]["cod_naturaleza"];
                            $count += 1;
                        endif;
                    endfor;

                    $facturacion->get_datos(" IF(MAX(cod_cab_mov_contable IS NOT NULL),MAX(cod_cab_mov_contable + 1),1) as cod_cab_contable", "", "con_cab_mov_contable");
                    $user_data["no_cod_cab_contable"]=$facturacion->_data[0]["cod_cab_contable"];
                    $facturacion->set_simple_query('call pbTratarInventario('.$user_data["cod_inventario"].','.$user_data["cantidad_inventario_aud"].',
                                            '.$user_data["imp_inventario_aud"].',2,'. Session::get('cod') .','.$user_data["cod_empresa"].',0,0)');
                    // Insertamos la Cabecera del movimiento contable
                    $fecMov = date("Y.m.d H:i:s");
                    $obs    = 'Asiento contable para salida de mercaderias e inventario manualmente. ';
                    $facturacion->set_simple_query("INSERT INTO con_cab_mov_contable
                                                (cod_cab_mov_contable,obs_cab_mov_contable,fec_cab_mov_contable,cod_empresa,cod_usuario)
                                         VALUES (".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");
                    for($g=0;$g<count($user_data["no_cod_categoria"]);$g++):
                        $facturacion->set_simple_query("call pbTransaccionContable(".$user_data["no_cod_categoria"][$g].",".$user_data["no_imp_categoria"][$g].",".$user_data["no_cod_naturaleza"][$g].",".$user_data["no_cod_proceso_interno"].",".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");                    
                    endfor;
                break;
                case 'nuevaEntradaInventario':
                    $valida=false;
                    $facturacion->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
                    $cadEmp = $facturacion->_data;
                    $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_proceso=".$user_data["no_cod_proceso_interno"]." and ind_propio=1"," con_proceso_transaccion " );
                    if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las la cuenta contable interna para este proceso. ";break 1;}
                    $user_data["no_cod_categoria"][0]=$facturacion->_data[0]["cod_categoria"];
                    $user_data["no_cod_naturaleza"][0]=$facturacion->_data[0]["cod_naturaleza"];
                    $user_data["no_imp_categoria"][0]=$user_data["no_total_inventariado"];
                    if(isset($user_data["no_met_pago"]) And $user_data["no_met_pago"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_met_pago=".$user_data["no_met_pago"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar el tipo de pago para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][1]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][1]=$user_data["no_total_inventario"];
                        $user_data["no_cod_naturaleza"][1]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    if(isset($user_data["no_cod_impuesto"]) And $user_data["no_cod_impuesto"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_impuesto=".$user_data["no_cod_impuesto"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion" );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar los impuestos para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][2]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][2]=$user_data["no_imp_impuesto"];
                        $user_data["no_cod_naturaleza"][2]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    $count = count($user_data["no_cod_categoria"]);
                    for ($t=0;$t<count($user_data["no_cod_retencion"]);$t++):
                        if($user_data["no_cod_retencion"][$t]>0 ):
                            $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_retencion=".$user_data["no_cod_retencion"][$t]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                            if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las retenciones para este proceso. ";break 2;}
                            $user_data["no_cod_categoria"][$count]=$facturacion->_data[0]["cod_categoria"];
                            $user_data["no_imp_categoria"][$count]=$user_data["no_imp_retencion"][$t];
                            $user_data["no_cod_naturaleza"][$count]=$facturacion->_data[0]["cod_naturaleza"];
                            $count += 1;
                        endif;
                    endfor;
                    $facturacion->get_datos(" IF(MAX(cod_cab_mov_contable IS NOT NULL),MAX(cod_cab_mov_contable + 1),1) as cod_cab_contable", "", "con_cab_mov_contable");
                    $user_data["no_cod_cab_contable"]=$facturacion->_data[0]["cod_cab_contable"];
                    $facturacion->set_simple_query('call pbTratarInventario('.$user_data["cod_inventario"].','.$user_data["cantidad_inventario_aud"].',
                                            '.$user_data["imp_inventario_aud"].',1,'. Session::get('cod') .','.$cadEmp[0]["result"].',0,0)');
                    // Insertamos la Cabecera del movimiento contable
                    $fecMov = date("Y.m.d H:i:s");
                    $obs    = 'Asiento contable para entrada de mercaderias e inventario manualmente. ';
                    $facturacion->set_simple_query("INSERT INTO con_cab_mov_contable
                                                (cod_cab_mov_contable,obs_cab_mov_contable,fec_cab_mov_contable,cod_empresa,cod_usuario)
                                         VALUES (".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");
                    for($g=0;$g<count($user_data["no_cod_categoria"]);$g++):
                        $facturacion->set_simple_query("call pbTransaccionContable(".$user_data["no_cod_categoria"][$g].",".$user_data["no_imp_categoria"][$g].",".$user_data["no_cod_naturaleza"][$g].",".$user_data["no_cod_proceso_interno"].",".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");                    
                    endfor;
                break;
                case 'nuevaAjusteInventario':
                    $campo = array('obs_inventario_ajuste =');
                    $clave = array("'".$user_data['obs_inventario_ajuste']."'");
                    if(isset($user_data["ind_incremento"])){$facturacion->get_datos(" cod_proceso "," nom_formulario ='nuevaSalidaInventario'"," sys_formulario " );$user_data["no_cod_proceso_interno"]=$facturacion->_data[0]["cod_proceso"];}
                    if(isset($user_data["ind_decremento"])){$facturacion->get_datos(" cod_proceso "," nom_formulario ='nuevaEntradaInventario'"," sys_formulario " );$user_data["no_cod_proceso_interno"]=$facturacion->_data[0]["cod_proceso"];}
                    $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_proceso=".$user_data["no_cod_proceso_interno"]." and ind_propio=1"," con_proceso_transaccion " );
                    if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las la cuenta contable interna para este proceso. ";break 1;}
                    $user_data["no_cod_categoria"][0]=$facturacion->_data[0]["cod_categoria"];
                    $user_data["no_cod_naturaleza"][0]=$facturacion->_data[0]["cod_naturaleza"];
                    $user_data["no_imp_categoria"][0]=$user_data["no_total_inventariado"];
                    if(isset($user_data["no_met_pago"]) And $user_data["no_met_pago"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_met_pago=".$user_data["no_met_pago"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar el tipo de pago para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][1]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][1]=$user_data["no_total_inventario"];
                        $user_data["no_cod_naturaleza"][1]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    if(isset($user_data["no_cod_impuesto"]) And $user_data["no_cod_impuesto"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_impuesto=".$user_data["no_cod_impuesto"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion" );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar los impuestos para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][2]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][2]=$user_data["no_imp_impuesto"];
                        $user_data["no_cod_naturaleza"][2]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    $count = count($user_data["no_cod_categoria"]);
                    for ($t=0;$t<count($user_data["no_cod_retencion"]);$t++):
                        if($user_data["no_cod_retencion"][$t]>0 ):
                            $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_retencion=".$user_data["no_cod_retencion"][$t]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                            if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las retenciones para este proceso. ";break 2;}
                            $user_data["no_cod_categoria"][$count]=$facturacion->_data[0]["cod_categoria"];
                            $user_data["no_imp_categoria"][$count]=$user_data["no_imp_retencion"][$t];
                            $user_data["no_cod_naturaleza"][$count]=$facturacion->_data[0]["cod_naturaleza"];
                            $count += 1;
                        endif;
                    endfor;
                    $facturacion->get_datos(" IF(MAX(cod_cab_mov_contable IS NOT NULL),MAX(cod_cab_mov_contable + 1),1) as cod_cab_contable", "", "con_cab_mov_contable");
                    $user_data["no_cod_cab_contable"]=$facturacion->_data[0]["cod_cab_contable"];
                break;
                case 'nuevaCliente':
                    $campo = array('nom_cliente' . "=", 'nit_cliente' . "=");
                    $clave = array("'" . $user_data['nom_cliente'] . "'" . ' and ', "'" . $user_data['nit_cliente'] . "'");
                break;
                case 'nuevaFactura':case 'nuevaFacturacion':
                    if($user_data["no_metodo"]=="AGREGAR"):
                        $codDes=0;$codImp=0;
                        $campo = array('num_factura' . '=');
                        $clave = array("'" . $user_data['num_factura'] . "'");
                        $user_data['fec_factura'] = date("Y.m.d H:i:s");
                        $user_data['cod_estado'] = "FAA";
                        for ($i = 0; $i < count($user_data['no_cod_item']); $i++) :
                            $codDes=0;$codImp=0;
                            if (!empty($user_data['no_cod_item'][$i])):
                                $user_data["no_imp_detalle"][$i] = (float)$user_data["no_imp"][$i] * (float)$user_data["no_can_detalle"][$i];
                                if($user_data["no_cod_descuento"][$i]<>''):
                                    $facturacion->get_datos('prc_descuento as prc', ' cod_descuentos ='.$user_data["no_cod_descuento"][$i], ' fa_descuentos');
                                    !empty($facturacion->_data) ? $codDes = (float)$facturacion->_data[0]["prc"] : $codDes=0;
                                endif;
                                if($user_data["no_cod_impuesto"][$i]<>''):
                                    $facturacion->get_datos('por_impuesto as prc', ' cod_impuesto ='.$user_data["no_cod_impuesto"][$i], ' fa_impuesto');
                                    !empty($facturacion->_data) ? $codImp = (float)$facturacion->_data[0]["prc"] : $codImp=0;
                                endif;
                                $codDes>0 ? $user_data["no_imp_descuento"][$i]=round(($user_data["no_imp_detalle"][$i]*$codDes) / 100, 2) : $user_data["no_imp_descuento"][$i]=0 ;
                                $codImp>0 ? $user_data["no_imp_impuesto"][$i]=round(($user_data["no_imp_detalle"][$i]*$codImp) / 100, 2) : $user_data["no_imp_impuesto"][$i]=0 ;    
                            endif;
                        endfor;   
                        if($user_data["no_nom_tabla"]=="nuevaFacturacion"):
                            $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_proceso=".$user_data["no_cod_proceso_interno"]." and ind_propio=1"," con_proceso_transaccion " );
                            if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las la cuenta contable interna para este proceso. ";break 1;}
                            $user_data["no_cod_categoria"][0]=$facturacion->_data[0]["cod_categoria"];
                            $user_data["no_cod_naturaleza"][0]=$facturacion->_data[0]["cod_naturaleza"];
                            $user_data["no_imp_categoria"][0]=$user_data["sub_total_factura"];
                            if(isset($user_data["cod_met_pago"]) And $user_data["cod_met_pago"]>0):
                                $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_met_pago=".$user_data["cod_met_pago"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                                if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar el tipo de pago para este proceso. ";break 1;}
                                $user_data["no_cod_categoria"][1]=$facturacion->_data[0]["cod_categoria"];
                                $user_data["no_imp_categoria"][1]=$user_data["imp_adeudado"];
                                $user_data["no_cod_naturaleza"][1]=$facturacion->_data[0]["cod_naturaleza"];
                            endif;  
                            $count = count($user_data["no_cod_categoria"]);
                            for ($t=0;$t<count($user_data["no_cod_descuento"]);$t++):
                                if($user_data["no_cod_descuento"][$t]>0 ):
                                    $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_descuentos=".$user_data["no_cod_descuento"][$t]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                                    if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar la cuenta contable de los descuentos para este proceso. ";break 1;}
                                    $user_data["no_cod_categoria"][$count]=$facturacion->_data[0]["cod_categoria"];
                                    $user_data["no_imp_categoria"][$count]=$user_data["no_imp_descuento"][$t];
                                    $user_data["no_cod_naturaleza"][$count]=$facturacion->_data[0]["cod_naturaleza"];
                                    $count += 1;
                                endif;
                            endfor;
                            $count = count($user_data["no_cod_categoria"]);                        
                            for ($t=0;$t<count($user_data["no_cod_impuesto"]);$t++):
                                if($user_data["no_cod_impuesto"][$t]>0 ):
                                    $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_impuesto=".$user_data["no_cod_impuesto"][$t]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                                    if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar la cuenta contable de los impuestos para este proceso. ";break 1;}
                                    $user_data["no_cod_categoria"][$count]=$facturacion->_data[0]["cod_categoria"];
                                    $user_data["no_imp_categoria"][$count]=$user_data["no_imp_impuesto"][$t];
                                    $user_data["no_cod_naturaleza"][$count]=$facturacion->_data[0]["cod_naturaleza"];
                                    $count += 1;
                                endif;
                            endfor;
                            $facturacion->get_datos(" IF(MAX(cod_cab_mov_contable IS NOT NULL),MAX(cod_cab_mov_contable + 1),1) as cod_cab_contable", "", "con_cab_mov_contable");
                            $user_data["no_cod_cab_contable"]=$facturacion->_data[0]["cod_cab_contable"];
                            // Insertamos la Cabecera del movimiento contable
                            $fecMov = date("Y.m.d H:i:s");
                            $obs    = 'Asiento contable para venta de mercaderias y salida de inventario automatica. ';
                            $facturacion->set_simple_query("INSERT INTO con_cab_mov_contable
                                                        (cod_cab_mov_contable,obs_cab_mov_contable,fec_cab_mov_contable,cod_empresa,cod_usuario)
                                                 VALUES (".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");
                            for($g=0;$g<count($user_data["no_cod_categoria"]);$g++):
                                $facturacion->set_simple_query("call pbTransaccionContable(".$user_data["no_cod_categoria"][$g].",".$user_data["no_imp_categoria"][$g].",".$user_data["no_cod_naturaleza"][$g].",".$user_data["no_cod_proceso_interno"].",".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");                    
                            endfor;
                        endif;
                    else:
                        $valida=false;
                        $facturacion->msj="No se puede convertir una cotizacion en factura sin guardarla antes como cotizacion. ";
                        $facturacion->err="2";
                    endif;
                break;
                case 'nuevaDescuento':
                    $campo = array('nom_descuento' . "=", 'prc_descuento' . "=");
                    $clave = array("'" . $user_data['nom_descuento'] . "'" . ' and ', $user_data['prc_descuento']);
                    $user_data["no_cod_clave"]="cod_descuentos";
                break;
                case 'nuevaOrden':
                    $campo = array('cod_factura' . '=');
                    $clave = array($user_data['cod_factura']);
                    $user_data['fec_orden'] = date("Y.m.d H:i:s");
                    empty($user_data["no_tmp_img"]) ? $user_data["ind_adjunto"]=0 :  $user_data["ind_adjunto"]=1;
                break;
                case 'nuevaMetodoPago':
                    $campo = array('nom_met_pago' . '=');
                    $clave = array("'". $user_data['nom_met_pago']."'");
                    $user_data["no_cod_clave"]="cod_met_pago";
                break;
                case 'nuevaRetencion':
                    $campo = array('nom_retencion' . '=');
                    $clave = array("'". $user_data['nom_retencion']."'");
                    $user_data["no_cod_clave"]="cod_retencion";
                break;
                case 'nuevaPagosRecibidos':
                    $user_data["ind_ingreso"]=1;
                    $ind_servicio="";
                    $ind;
                    $campo = array('num_sig_comp_ingreso' . '=');
                    $clave = array("'". $user_data['num_sig_comp_ingreso']."'");
                    $user_data["imp_pago"]=$user_data["no_total"];
                    isset($user_data["no_ind_factura"]) ? $ind=0 : $ind=1; 
                    isset($user_data["no_ind_factura"]) ? $ind_servicio="Facturacion" : $ind_servicio="Categoria Contable"; 
                    $user_data["fec_pago"] = date("Y-m-d H:i:s");
                    /*configuracion contable para segun proceso asociado al formulario*/
                    /*$facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_proceso=".$user_data["no_cod_proceso_interno"]." and ind_propio=1"," con_proceso_transaccion " );
                    if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las la cuenta contable interna para este proceso. ";break 1;}
                    $user_data["no_cod_categoria"][0]=$facturacion->_data[0]["cod_categoria"];
                    $user_data["no_cod_naturaleza"][0]=$facturacion->_data[0]["cod_naturaleza"];
                    $user_data["no_imp_categoria"][0]=$user_data["no_total"];
                    /*configuracion contable para segun el metodo de pago*/
                    /*if(isset($user_data["cod_met_pago"]) And $user_data["cod_met_pago"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_met_pago=".$user_data["cod_met_pago"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar el metodo de pago para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][1]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_cod_naturaleza"][1]=$facturacion->_data[0]["cod_naturaleza"];
                        $user_data["no_imp_categoria"][1]=$user_data["no_sub_total"];
                    endif;
                    /*configuracion contable para segun el impuesto*/
                    /*if(isset($user_data["no_cod_retencion"]) And $user_data["no_cod_retencion"][$ind]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_retencion=".$user_data["no_cod_retencion"][$ind]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion" );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las retenciones para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][2]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][2]=$user_data["no_retencion"];
                        $user_data["no_cod_naturaleza"][2]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    /*configuracion contable para segun la retencion*/
                    /*if(isset($user_data["no_cod_impuesto"]) And $user_data["no_cod_impuesto"][0]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_impuesto=".$user_data["no_cod_impuesto"][0]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion" );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar los impuestos para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][3]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][3]=$user_data["no_val_impuesto"];
                        $user_data["no_cod_naturaleza"][3]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    //var_dump($user_data);exit;
                    $facturacion->get_datos(" IF(MAX(cod_cab_mov_contable IS NOT NULL),MAX(cod_cab_mov_contable + 1),1) as cod_cab_contable", "", "con_cab_mov_contable");
                    $user_data["no_cod_cab_contable"]=$facturacion->_data[0]["cod_cab_contable"];
                    // Insertamos la Cabecera del movimiento contable
                    $fecMov = date("Y.m.d H:i:s");
                    $obs    = 'Asiento contable para ingresos por '. $ind_servicio;
                    $facturacion->set_simple_query("INSERT INTO con_cab_mov_contable
                                                (cod_cab_mov_contable,obs_cab_mov_contable,fec_cab_mov_contable,cod_empresa,cod_usuario)
                                         VALUES (".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");
                    for($g=0;$g<count($user_data["no_cod_categoria"]);$g++):
                        $facturacion->set_simple_query("call pbTransaccionContable(".$user_data["no_cod_categoria"][$g].",".$user_data["no_imp_categoria"][$g].",".$user_data["no_cod_naturaleza"][$g].",".$user_data["no_cod_proceso_interno"].",".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");                    
                    endfor;*/
                break;
                case 'nuevaPagosRealizado':
                    $user_data["ind_egreso"]=1;
                    $ind_servicio="";
                    $ind=0;
                    $campo = array('num_sig_compago' . '=');
                    $clave = array("'". $user_data['num_sig_compago']."'");
                    $user_data["imp_pago"]=$user_data["no_total"];
                    $user_data["cod_cliente"] = $user_data["cod_cliente_1"];
                    unset($user_data["cod_cliente_1"]);
                    isset($user_data["no_ind_factura"]) ? $ind_servicio="Facturacion" : $ind_servicio="Categoria Contable"; 
                    /*configuracion contable para segun proceso asociado al formulario*/
                    $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_proceso=".$user_data["no_cod_proceso_interno"]." and ind_propio=1"," con_proceso_transaccion " );
                    if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las la cuenta contable interna para este proceso. ";break 1;}
                    $user_data["no_cod_categoria"][0]=$facturacion->_data[0]["cod_categoria"];
                    $user_data["no_cod_naturaleza"][0]=$facturacion->_data[0]["cod_naturaleza"];
                    $user_data["no_imp_categoria"][0]=$user_data["no_total"];
                    /*configuracion contable para segun el metodo de pago*/
                    if(isset($user_data["cod_met_pago"]) And $user_data["cod_met_pago"]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_met_pago=".$user_data["cod_met_pago"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar el metodo de pago para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][1]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_cod_naturaleza"][1]=$facturacion->_data[0]["cod_naturaleza"];
                        $user_data["no_imp_categoria"][1]=$user_data["no_sub_total"];
                    endif;
                    /*configuracion contable para segun el impuesto*/
                    if(isset($user_data["no_cod_retencion"]) And $user_data["no_cod_retencion"][$ind]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_retencion=".$user_data["no_cod_retencion"][$ind]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion" );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las retenciones para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][2]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][2]=$user_data["no_retencion"];
                        $user_data["no_cod_naturaleza"][2]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    /*configuracion contable para segun la retencion*/
                    if(isset($user_data["no_cod_impuesto"]) And $user_data["no_cod_impuesto"][0]>0):
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_impuesto=".$user_data["no_cod_impuesto"][0]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion" );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$valida=false;$facturacion->err = 2;$facturacion->msj=" Falta parametrizar los impuestos para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][3]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_imp_categoria"][3]=$user_data["no_val_impuesto"];
                        $user_data["no_cod_naturaleza"][3]=$facturacion->_data[0]["cod_naturaleza"];
                    endif;
                    //var_dump($user_data);exit;
                    $facturacion->get_datos(" IF(MAX(cod_cab_mov_contable IS NOT NULL),MAX(cod_cab_mov_contable + 1),1) as cod_cab_contable", "", "con_cab_mov_contable");
                    $user_data["no_cod_cab_contable"]=$facturacion->_data[0]["cod_cab_contable"];
                    // Insertamos la Cabecera del movimiento contable
                    $fecMov = date("Y.m.d H:i:s");
                    $obs    = 'Asiento contable para egresos por '. $ind_servicio;
                    $facturacion->set_simple_query("INSERT INTO con_cab_mov_contable
                                                (cod_cab_mov_contable,obs_cab_mov_contable,fec_cab_mov_contable,cod_empresa,cod_usuario)
                                         VALUES (".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");
                    for($g=0;$g<count($user_data["no_cod_categoria"]);$g++):
                        $facturacion->set_simple_query("call pbTransaccionContable(".$user_data["no_cod_categoria"][$g].",".$user_data["no_imp_categoria"][$g].",".$user_data["no_cod_naturaleza"][$g].",".$user_data["no_cod_proceso_interno"].",".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");                    
                    endfor;
                break;
            }
            if ($valida) {
                if (!$facturacion->getRegistro($user_data['no_esq_tabla'], $campo, $clave)) {
                    $facturacion->setRegistro($user_data);
                    $data = array('ERR' => 6,
                                  'MSJ' => $facturacion->msj);
                    setVariables($facturacion,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$facturacion->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                } else {
                    $data = array('ERR' => 2,
                                  'MSJ' => "El registro ya existe");                    
                    setVariables($facturacion,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$facturacion->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                }
            }else{
                $data= array('ERR' => $facturacion->err,
                             'MSJ' => $facturacion->msj);
                setVariables($facturacion,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$facturacion->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
    
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/facturacion/model/facturacionModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $facturacion  = new ModeloFacturacion();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $facturacion->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$facturacion->_data;
            //var_dump($sistema->_data);exit();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch ($user_data['no_nom_tabla']) {
                case 'nuevaFacConfig':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                    $user_data['fec_mod_config'] = date("Y-m-d H:i:s");
                    break;
                case 'nuevaTipoImpuesto':
                    $campo = array('nom_tipoimpuesto' . "=");
                    $clave = array("'" . $user_data['nom_tipoimpuesto'] . "'");
                    $user_data['fec_tipoimpuesto'] = date("Y.m.d");
                    $user_data['hora_tipoimpuesto'] = date("H:i:s");
                    break;
                case 'nuevaImpuesto':
                    $campo = array('nom_impuesto' . "=", 'por_impuesto' . "=");
                    $clave = array("'" . $user_data['nom_impuesto'] . "'" . ' and ', $user_data['por_impuesto']);
                    $user_data['fec_impuesto'] = date("Y.m.d H:i:s");
                    $user_data["no_cod_clave"]="cod_impuesto";
                    $user_data["no_cod_valor"]=$user_data["cod_impuesto"];
                    break;
                case 'nuevaTipoPago':
                    $campo = array('nom_tipopago' . "=", 'num_dias_tipopago' . "=");
                    $clave = array("'" . $user_data['nom_tipopago'] . "'" . ' and ', $user_data['num_dias_tipopago']);
                    $user_data['fec_tipoPago'] = date("Y.m.d");
                    $user_data['hora_tipoPago'] = date("H:i:s");
                    break;
                case 'nuevaUniMedida':
                    $campo = array('nom_unimedida' . "=", 'pre_unimedida' . "=");
                    $clave = array("'" . $user_data['nom_unimedida'] . "'" . ' and ', "'" . $user_data['pre_unimedida'] . "'");
                    $user_data['fec_unimedida'] = date("Y.m.d");
                    $user_data['hora_unimedida'] = date("H:i:s");
                    break;
                case 'nuevaMoneda':
                    $campo = array('nom_moneda' . "=", 'abr_moneda' . "=");
                    $clave = array("'" . $user_data['nom_moneda'] . "'" . ' and ', "'" . $user_data['abr_moneda'] . "'");
                    break;
                case 'nuevaNumeracion':
                    $campo = array('nom_numeracion' . "=", 'pre_numeracion' . "=");
                    $clave = array("'" . $user_data['nom_numeracion'] . "'" . ' and ', "'" . $user_data['pre_numeracion'] . "'");
                    $user_data['fec_mod_numeracion'] = date("Y-m-d H:i:s");
                    $user_data["ind_preferida_numeracion"]=isset($user_data["ind_preferida_numeracion"]) ? $user_data["ind_preferida_numeracion"] : "0";
                    $user_data["ind_auto_numeracion"]=isset($user_data["ind_auto_numeracion"]) ? $user_data["ind_auto_numeracion"] : "0";
                    //$user_data['num_sig_numeracion'] = $user_data['num_inicial_numeracion'] + 1;
                    $user_data["ind_factura"]=isset($user_data["ind_factura"]) ? $user_data["ind_factura"] : "0";
                    $user_data["ind_cotizacion"]=isset($user_data["ind_cotizacion"]) ? $user_data["ind_cotizacion"] : "0";
                    break;
                case 'nuevaRegimen':
                    $campo = array('nom_regimen' . "=");
                    $clave = array("'" . $user_data['nom_regimen'] . "'");
                    break;
                case 'nuevaItem':
                    $campo = array('nom_item' . "=", 'ref_item' . "=");
                    $clave = array("'" . $user_data['nom_item'] . "'" . ' and ', "'" . $user_data['ref_item'] . "'");
                    $user_data['imp_venta'] = $user_data['imp_compra_item'] + (($user_data['imp_compra_item'] * $user_data['inc_porcen_item']) / 100);
                    break;
                case 'nuevaInventario':
                   $valida = false;                  
                break;
                case 'nuevaSalidaInventario':
                   $valida = false;
                break;
                case 'nuevaEntradaInventario':
                   $valida = false;
                break;
                case 'nuevaFacturacion':
                    $valida = false;
                break;
                case 'nuevaCliente':
                    $campo = array('nom_cliente' . "=", 'nit_cliente' . "=");
                    $clave = array("'" . $user_data['nom_cliente'] . "'" . ' and ', "'" . $user_data['nit_cliente'] . "'");
                    break;
                case 'nuevaFactura':
                    if($user_data["no_metodo"]=="AGREGAR"):
                        $campo = array('num_factura' . '=');
                        $clave = array("'" . $user_data['num_factura'] . "'");
                        $user_data['fec_modifica'] = date("Y.m.d H:i:s");
                        $user_data['cod_estado'] = "FAA";
                        If (isset($user_data["ind_recurrente_factura"]) Or isset($user_data["ind_cotizacion"])) :
                            $user_data["num_factura"] = '';
                        endif;
                        for ($i = 0; $i < count($user_data['no_cod_item']); $i++) {
                            $codDes=0;$codImp=0;
                            if (!empty($user_data['no_cod_item'][$i])) {
                                $user_data["no_imp_detalle"][$i] = (float)$user_data["no_imp"][$i] * (float)$user_data["no_can_detalle"][$i];
                                if($user_data["no_cod_descuento"][$i]<>''):
                                    $facturacion->get_datos('prc_descuento as prc', ' cod_descuentos ='.$user_data["no_cod_descuento"][$i], ' fa_descuentos');
                                    !empty($facturacion->_data) ? $codDes = (float)$facturacion->_data[0]["prc"] : $codDes=0;
                                endif;
                                if($user_data["no_cod_impuesto"][$i]<>''):
                                    $facturacion->get_datos('por_impuesto as prc', ' cod_impuesto ='.$user_data["no_cod_impuesto"][$i], ' fa_impuesto');
                                    !empty($facturacion->_data) ? $codImp = (float)$facturacion->_data[0]["prc"] : $codImp=0;
                                    $facturacion->get_datos('cod_inventario', ' cod_item='.$user_data["no_cod_item"][$i], ' fa_inventario');
                                    $user_data["no_cod_inventario"][$i]=$facturacion->_data[0]["cod_inventario"];
                                endif;
                                $codDes>0 ? $user_data["no_imp_descuento"][$i]=round(($user_data["no_imp_detalle"][$i]*$codDes) / 100, 2) : $user_data["no_imp_descuento"][$i]=0 ;
                                $codImp>0 ? $user_data["no_imp_impuesto"][$i]=round(($user_data["no_imp_detalle"][$i]*$codImp) / 100, 2) : $user_data["no_imp_impuesto"][$i]=0 ;    
                            }
                        }
                    else:
                        $user_data["ind_cotizacion"]=0;
                        $campo = array('num_factura' . '=');
                        $clave = array("'" . $user_data['num_factura'] . "'");
                        $user_data['fec_modifica'] = date("Y.m.d H:i:s");
                        $user_data['cod_estado'] = "FAA";
                        for ($i = 0; $i < count($user_data['no_cod_item']); $i++) {
                            $codDes=0;$codImp=0;
                            if (!empty($user_data['no_cod_item'][$i])) {
                                $user_data["no_imp_detalle"][$i] = (float)$user_data["no_imp"][$i] * (float)$user_data["no_can_detalle"][$i];
                                if($user_data["no_cod_descuento"][$i]<>''):
                                    $facturacion->get_datos('prc_descuento as prc', ' cod_descuentos ='.$user_data["no_cod_descuento"][$i], ' fa_descuentos');
                                    !empty($facturacion->_data) ? $codDes = (float)$facturacion->_data[0]["prc"] : $codDes=0;
                                endif;
                                if($user_data["no_cod_impuesto"][$i]<>''):
                                    $facturacion->get_datos('por_impuesto as prc', ' cod_impuesto ='.$user_data["no_cod_impuesto"][$i], ' fa_impuesto');
                                    !empty($facturacion->_data) ? $codImp = (float)$facturacion->_data[0]["prc"] : $codImp=0;
                                    $facturacion->get_datos('cod_inventario', ' cod_item='.$user_data["no_cod_item"][$i], ' fa_inventario');
                                    $user_data["no_cod_inventario"][$i]=$facturacion->_data[0]["cod_inventario"];
                                endif;
                                $codDes>0 ? $user_data["no_imp_descuento"][$i]=round(($user_data["no_imp_detalle"][$i]*$codDes) / 100, 2) : $user_data["no_imp_descuento"][$i]=0 ;
                                $codImp>0 ? $user_data["no_imp_impuesto"][$i]=round(($user_data["no_imp_detalle"][$i]*$codImp) / 100, 2) : $user_data["no_imp_impuesto"][$i]=0 ;    
                            }
                        }
                        $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_proceso=".$user_data["no_cod_proceso_interno"]." and ind_propio=1"," con_proceso_transaccion " );
                        if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar las la cuenta contable interna para este proceso. ";break 1;}
                        $user_data["no_cod_categoria"][0]=$facturacion->_data[0]["cod_categoria"];
                        $user_data["no_cod_naturaleza"][0]=$facturacion->_data[0]["cod_naturaleza"];
                        $user_data["no_imp_categoria"][0]=$user_data["sub_total_factura"];
                        if(isset($user_data["cod_met_pago"]) And $user_data["cod_met_pago"]>0):
                            $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_met_pago=".$user_data["cod_met_pago"]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                            if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar el tipo de pago para este proceso. ";break 1;}
                            $user_data["no_cod_categoria"][1]=$facturacion->_data[0]["cod_categoria"];
                            $user_data["no_imp_categoria"][1]=$user_data["imp_adeudado"];
                            $user_data["no_cod_naturaleza"][1]=$facturacion->_data[0]["cod_naturaleza"];
                        endif;  
                        $count = count($user_data["no_cod_categoria"]);
                        for ($t=0;$t<count($user_data["no_cod_descuento"]);$t++):
                            if($user_data["no_cod_descuento"][$t]>0 ):
                                $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_descuentos=".$user_data["no_cod_descuento"][$t]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                                if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar la cuenta contable de los descuentos para este proceso. ";break 1;}
                                $user_data["no_cod_categoria"][$count]=$facturacion->_data[0]["cod_categoria"];
                                $user_data["no_imp_categoria"][$count]=$user_data["no_imp_descuento"][$t];
                                $user_data["no_cod_naturaleza"][$count]=$facturacion->_data[0]["cod_naturaleza"];
                                $count += 1;
                            endif;
                        endfor;
                        $count = count($user_data["no_cod_categoria"]);                        
                        for ($t=0;$t<count($user_data["no_cod_impuesto"]);$t++):
                            if($user_data["no_cod_impuesto"][$t]>0 ):
                                $facturacion->get_datos(" cod_categoria, cod_naturaleza "," cod_impuesto=".$user_data["no_cod_impuesto"][$t]." and cod_proceso=".$user_data["no_cod_proceso_interno"].""," con_proceso_transaccion " );
                                if(!isset($facturacion->_data[0]["cod_categoria"])){$facturacion->err = 2;$facturacion->msj=" Falta parametrizar la cuenta contable de los impuestos para este proceso. ";break 1;}
                                $user_data["no_cod_categoria"][$count]=$facturacion->_data[0]["cod_categoria"];
                                $user_data["no_imp_categoria"][$count]=$user_data["no_imp_impuesto"][$t];
                                $user_data["no_cod_naturaleza"][$count]=$facturacion->_data[0]["cod_naturaleza"];
                                $count += 1;
                            endif;
                        endfor;
                        $facturacion->get_datos(" IF(MAX(cod_cab_mov_contable IS NOT NULL),MAX(cod_cab_mov_contable + 1),1) as cod_cab_contable", "", "con_cab_mov_contable");
                        $user_data["no_cod_cab_contable"]=$facturacion->_data[0]["cod_cab_contable"];
                        // Insertamos la Cabecera del movimiento contable
                        $fecMov = date("Y.m.d H:i:s");
                        $obs    = 'Asiento contable para venta de mercaderias y salida de inventario automatica. ';
                        $facturacion->set_simple_query("INSERT INTO con_cab_mov_contable
                                                    (cod_cab_mov_contable,obs_cab_mov_contable,fec_cab_mov_contable,cod_empresa,cod_usuario)
                                             VALUES (".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");
                        for($g=0;$g<count($user_data["no_cod_categoria"]);$g++):
                            $facturacion->set_simple_query("call pbTransaccionContable(".$user_data["no_cod_categoria"][$g].",".$user_data["no_imp_categoria"][$g].",".$user_data["no_cod_naturaleza"][$g].",".$user_data["no_cod_proceso_interno"].",".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")");                    
                        endfor;
                    endif;
                break;
                case 'nuevaDescuento':
                    $campo = array('nom_descuento' . "=", 'prc_descuento' . "=");
                    $clave = array("'" . $user_data['nom_descuento'] . "'" . ' and ', $user_data['prc_descuento']);
                    $user_data["no_cod_clave"]="cod_descuentos";
                    $user_data["no_cod_valor"]=$user_data["cod_descuentos"];
                break;
                case 'nuevaOrden':
                    $campo = array('cod_factura' . '=');
                    $clave = array($user_data['cod_factura']);
                break;
                case 'nuevaMetodoPago':
                    $campo = array('nom_met_pago' . '=');
                    $clave = array($user_data['nom_met_pago']);
                    $user_data["no_cod_clave"]="cod_met_pago";
                    $user_data["no_cod_valor"]=$user_data["cod_met_pago"];
                break;
                case 'nuevaRetencion':
                    $campo = array('nom_retencion' . '=');
                    $clave = array("'". $user_data['nom_retencion']."'");
                    $user_data["no_cod_clave"]="cod_retencion";
                    $user_data["no_cod_valor"]=$user_data["cod_retencion"];
                break;
                case 'nuevaPagosRecibidos':
                    $user_data["ind_ingreso"]=1;
                    $ind_servicio="";
                    $ind;
                    $campo = array('num_sig_comp_ingreso' . '=');
                    $clave = array("'". $user_data['num_sig_comp_ingreso']."'");
                    $user_data["imp_pago"]=$user_data["no_total"];
                    isset($user_data["no_ind_factura"]) ? $ind=0 : $ind=1; 
                    isset($user_data["no_ind_factura"]) ? $ind_servicio="Facturacion" : $ind_servicio="Categoria Contable"; 
                    $user_data["fec_mod_pago"] = date("Y-m-d H:i:s");
                break;
            }
            if($valida){
                $facturacion->editRegistro($user_data);
                $data = array('ERR'=>6,
                              'MSJ'=>$facturacion->msj);
                setVariables($facturacion,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$facturacion->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data = array('ERR'=>3,
                              'MSJ'=>"La Edicion no esta permitida en este proceso, consulte a su administrador");
                setVariables($facturacion,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $facturacion->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$facturacion->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
}

?>