<?php
class sistemaController{
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo de carga por defecto, valida la sesion activa en el servidor 
//             y carga la vista del index si es una sesion valida, de lo contrario
//             carga la vista de login para validar los datos de usuario, igualmente
//             invoca los datos de configuracin de usuario desde el moduelo propio
//             controlador.
    public function index(){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $dataFormGeneral=array();
        $Objvista = new view;
        $sistema  = new ModeloSistema();
        if(!Session::get('usuario')){
            $data = array('ERR'=>'12',
                          'MSJ'=>'');
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $sistema->get_datos('fbDevuelveArchivos(275,1) as ARCHIVOSCSS');
            $Objvista->_archivos_css = $sistema->_data;
            $sistema->get_datos('fbDevuelveArchivos(275,2) as ARCHIVOSSCRIPT');
            $Objvista->_archivos_js  = $sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $data=array();
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $sistema->get_config_usuario(Session::get('cod'));
            $Objvista->_empresa          = $sistema->_empresa;
            $Objvista->_notificacion     = array('des_notificacion'=>$sistema->_notificacion[0]['des_notificacion']);
            $Objvista->_tarea            = array('des_tarea'=>$sistema->_tarea[0]['des_tarea']);
            $var = "";
            for($t=0;$t<count($sistema->_mensajes);$t++){$var = $var . $sistema->_mensajes[$t]['des_mensajes'];}
            $Objvista->_mensajes         = array ('des_mensajes'=>$var);
            $Objvista->_numMensajes      = $sistema->_numMensajes;
            $var = "";
            for($t=0;$t<count($sistema->_menu);$t++){$var = $var . $sistema->_menu[$t]['menu'];}
            $Objvista->_menu             = array ('menu'=>$var);
            $Objvista->_menuHeader       = $sistema->_menuHeader;
            $Objvista->_menuShorcut      = $sistema->_menuShorcut;
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="index"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $sistema->get_datos('*', ' cod_usuario='.Session::get('cod'), 'sys_usuario');
            $data=$sistema->_data;
            # traemos las modales configuradas para el index
            $sistema->get_datos('fbArmaFormularioModal(1,'.Session::get('cod').') as modal');
            $Objvista->_formulario_modal = $sistema->_data;
            #traemos los archivos relacionados al formulario
            $sistema->get_datos('fbDevuelveArchivos(0,1) as ARCHIVOSCSS');
            $Objvista->_archivos_css = $sistema->_data;
            $sistema->get_datos('fbDevuelveArchivos(0,2) as ARCHIVOSSCRIPT');
            $Objvista->_archivos_js  = $sistema->_data;
            $sistema->get_datos('fbArmaImgEmpresa('.Session::get('cod').') as slide_empresas ');$data[0]["slide_empresas"] = !empty($sistema->_data) ? $sistema->_data : array();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index','index',$data,$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para recuperar las credenciales de una cuenta, valida si el usuario esta activo segun el correo electronico o 
//             nombre de usuario, el metodo de recuperacion se ejecuta unicamente si el usuario se encuentra activo en el sitema.
    public function recover(){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $user_data = helper_user_data('nuevoRegistro');
        $dataFormGeneral=array();
        $Objvista = new view;
        $sistema  = new ModeloSistema();
        $data=array();
        if((isset($user_data["email_usuario"]) and !empty($user_data["email_usuario"])) Or (isset($user_data["usuario_usuario"]) and !empty($user_data["usuario_usuario"]))):
            $sistema->get_datos(' t1.*,t2.cod_empresa ',"(t1.email_usuario=lcase('".$user_data["email_usuario"]."') Or t1.usuario_usuario=lcase('".$user_data["usuario_usuario"]."')) AND t1.cod_usuario=t2.cod_usuario AND t2.ind_principal=1","sys_usuario as t1, sys_usuario_empresa as t2"); $data=$sistema->_data;
            if(!empty($sistema->_data)):
                if($sistema->_data[0]["cod_estado"]=='AAA'):
                    $sistema->get_datos('*', ' cod_estado="AAA" AND cod_empresa='.$sistema->_data[0]["cod_empresa"], ' sys_config');!empty($sistema->_data) ? $data[1]=$sistema->_data : $data[1]=array();
                    //exit;
                    $email_array=array("Saludo"=>"Cordial Saludo: ".$data[0]["nom_usuario"]."",
                                       "Introduccion"=>$data[1][0]["asunto_config"],
                                       "Descripcion"=>"Para seguir reemplazando tu informacion de seguridad, haz clic en el siguiente link. <p>Si no lo has solicitado, ignora este correo electronico.",
                                       "to"=>$data[0]["email_usuario"],
                                       "LINK_EMAIL"=>$data[1][0]["url_servidor_config"]."?app=".base64_encode("sistema")."&met=".base64_encode("recoverPass")."&arg=".  base64_encode($user_data["usuario_usuario"].','.$user_data["email_usuario"].','.$sistema->_data[0]["cod_empresa"]));
                    sendEmail("sistema", 1, $email_array, $Objvista,$data);
                    $data['ERR'] = 2;
                    $data['MSJ'] = 'El sistema ha enviado la notificacion de recuperacion a su correo electronico. ';  
                else:
                    $data['ERR'] = 2;
                    $data['MSJ'] = 'No es posible recuperar las credenciales de el usuario porque se encuentra desactivado, pongase en contacto con el administrador del sistema. ';  
                endif;
            else:
                $data['ERR'] = 2;
                $data['MSJ'] = 'La cuenta o el usuario no existen, consulte a el administrador del sistema. ';  
            endif;
        endif;
        $cadenaSql= fbRetornaConfigForm();
        $sistema->get_datos($cadenaSql, 'head_formulario_config="recover"', 'sys_formulario_config');
        $dataFormGeneral=$sistema->_data;
        $sistema->get_datos('fbDevuelveArchivos(275,1) as ARCHIVOSCSS');
        $Objvista->_archivos_css = $sistema->_data;
        $sistema->get_datos('fbDevuelveArchivos(275,2) as ARCHIVOSSCRIPT');
        $Objvista->_archivos_js  = $sistema->_data;
        $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','recover',$data,$dataFormGeneral);
    }
    
    public function recoverPass($metodo,$argumentos=array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $user_data = helper_user_data('nuevoRegistro');
        $dataFormGeneral=array();
        $Objvista = new view;
        $sistema  = new ModeloSistema();
        $data=array();
        if(!empty($argumentos)):
            $pass = RandomString(8);
            $pass_1 = md5($pass);
            $sistema->set_simple_query("UPDATE sys_usuario SET password_usuario='".$pass_1."') where email_usuario=lcase('".$argumentos[1]."') Or usuario_usuario=lcase('".$argumentos[0]."')");
            $sistema->get_datos('*', ' cod_estado="AAA" AND cod_empresa='.$argumentos[2], ' sys_config');!empty($sistema->_data) ? $data[1]=$sistema->_data : $data[1]=array();
            $data[1]=$sistema->_data;
            $email_array=array("Saludo"=>"Cordial Saludo: ".$argumentos[0]."",
                               "Introduccion"=>$data[1][0]["asunto_config"],
                               "Descripcion"=>"El sistema ha generado la siguiente clave temporalmente copiela y peguela en el inicio de sesion con su usuario, cuando alla iniciado, porfavor cambie su clave.<p>Clave: ".$pass."</p> <p>Si no lo has solicitado, ignora este correo electronico.",
                               "to"=>$argumentos[1],
                               "LINK_EMAIL"=>$data[1][0]["url_servidor_config"]."?app=".base64_encode("sistema")."&met=".base64_encode("login")."&arg=");
            sendEmail("sistema", 1, $email_array, $Objvista,$data);
            $data['ERR'] = 2;
            $data['MSJ'] = 'El sistema ha generado la clave temporal y ha sigo enviada a su correo electronico. ';  
        endif;
        $cadenaSql= fbRetornaConfigForm();
        $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
        $dataFormGeneral=$sistema->_data;
        $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
    }
    
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para validar la sesion desde la vista del login, incoca el metodo
//             desde el modulo y carga la vista de index si la sesion se valida correctamente,
//             igualmente llena todas las variables de sesion con los metodos propios de cada usuario
//             de lo contrario, retorna la vista de login.	
    public function login($metodo){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $user_data = helper_user_data($metodo);
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        $dataFormGeneral=array();$data=array();
        if($sistema->get_login($user_data)){
            for($i=0; $i<count($sistema->_session); $i++) {	
                    Session::set($sistema->_session[$i],$sistema->_sessionVal[$i]);
            }
            setVariables($sistema,$Objvista,$metodo,1,1,'','','','',1,'',64);
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="index"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $sistema->get_datos('*', ' cod_usuario='.Session::get('cod'), 'sys_usuario');
            $data=$sistema->_data;
            $sistema->get_datos('fbDevuelveArchivos(0,1) as ARCHIVOSCSS');
            $Objvista->_archivos_css = $sistema->_data;
            $sistema->get_datos('fbDevuelveArchivos(0,2) as ARCHIVOSSCRIPT');
            $Objvista->_archivos_js  = $sistema->_data;
            $sistema->get_datos('fbArmaImgEmpresa('.Session::get('cod').') as slide_empresas ');$data[0]["slide_empresas"] = !empty($sistema->_data) ? $sistema->_data : array();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index','index',$data,$dataFormGeneral);

        }else{
            $data = array('ERR'=>$sistema->err,
                              'MSJ'=>$sistema->msj);
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            //var_dump($sistema->_data);exit();
            $dataFormGeneral=$sistema->_data;
            $sistema->get_datos('fbDevuelveArchivos(0,1) as ARCHIVOSCSS');
            $Objvista->_archivos_css = $sistema->_data;
            $sistema->get_datos('fbDevuelveArchivos(0,2) as ARCHIVOSSCRIPT');
            $Objvista->_archivos_js  = $sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral); 	
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para asignar menus / vistas a un usuario determinado, carga
//             desde el modelo propio del controlador los datos de los menus asignados
//             al la sesion activa y los usuarios relacionados a las empresas asignadas
//             a la sesion activa.	
    public function MenuUsuario($metodo,$argumentos = array()){
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
            setVariables($sistema,$Objvista,$metodo,4,1,'','','','',1,'',devuelveString($argumentos[2],'*',2),$met);
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index','MenuUsuario','',$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para asignar submenus a un usuario determinado, carga
//             desde el modelo propio del controlador los datos de los menus asignados
//             al la sesion activa y los usuarios relacionados a las empresas asignadas
//             a la sesion activa.	
    public function subMenuUsuario($metodo,$argumentos = array()){
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
            setVariables($sistema,$Objvista,$metodo,5,1,'','','','',1,'',devuelveString($argumentos[2],'*',2),$met);
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index','subMenuUsuario','',$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para gestionar los usuarios del sistema, carga la informacion necesaria 
//             para registrar un nuevo usuario, rol, menus, sub menus, y empresas que debe gestionar
//             igualmente carga la informacion que tiene registrada para modificarlo en caso de ser
//             necesario.
    public function usuario($metodo,$argumentos = array()){
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
                $arrayChek = array("ind_ayuda"=>$data[0]["ind_ayuda"],"ind_helpdesk"=>$data[0]["ind_helpdesk"],"ind_app"=>$data[0]["ind_app"],
                                   "ind_datolee_lider"=>$data[0]["ind_datolee_lider"],"ind_datolee_sublider"=>$data[0]["ind_datolee_sublider"]);  
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
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_usuario',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }	
    }
    
    public function perfilUsuario($metodo,$argumentos = array()){
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
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_usuario',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }	
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para gestionar las empresas del sistema, carga la informacion necesaria 
//             para registrar un nueva empresa igualmente carga la informacion que tiene registrada 
//             para modificarla en caso de ser necesario.   
    public function empresa($metodo,$argumentos = array()){
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
            $data=array();$camposCombo=array();
            $cadenaSql;
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_empresa=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                $camposCombo=array("cod_regimen"=>$data[0]["cod_regimen"],"cod_ciudad"=>$data[0]["cod_ciudad"],"cod_moneda"=>$data[0]["cod_moneda"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_empresa','',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }	
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para asignar los diferentes contratos a una empresa en especifico,
//             los contratos deben ser asignados segun el modulo que la empresa utilize,
//             el finalizar el tiempo del contrato, la empresa ni sus usuarios tendran acceso 
//             al modulo contratado, las fechas de caducidad pueden ser modificadas pero,
//             esta modificacion quedara registrada en el log de movimientos y notificaciones de usuario.
    public function empresaVsContrato($metodo,$argumentos = array()){
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
            $data=array();$camposCombo=array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_empresa_contrato=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                $camposCombo=array("cod_empresa"=>$data[0]["cod_empresa"],"cod_contrato"=>$data[0]["cod_contrato"],"cod_modulo"=>$data[0]["cod_modulo"],
                                   "cod_estado"=>$data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_empresa_contrato','',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }	
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para crear los diferentes contratos que se pueden relacionar a una empresa.	
    public function contrato($metodo,$argumentos = array()){
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
            $data=array();$camposCombo=array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_contrato=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                //$camposCombo=array("cod_regimen"=>$data[0]["cod_regimen"],"cod_ciudad"=>$data[0]["cod_ciudad"],"cod_moneda"=>$data[0]["cod_moneda"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_contrato','','','',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }	
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para crear las ciudades por departamentos de cualquier pais del mundo.
    public function ciudad($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_ciudad=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_ciudad','','','',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }	
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para crear los diferentes estados por proceso y modulo del sistema,
//             los estado AAA y BBB igual qeu los de facturacion no se pueden borrar
//             porq son propios de cada modulo.
    public function estado($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_estado='".$argumentos[3]."'",$argumentos[4]); $data=$sistema->_data;
                $camposCombo=array("cod_modulo"=>$data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_estado','',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para crear los diferencites perfiles que pueden ser asignados a los usuarios
//             des sistema, cada usuario tiene permisos especificos dependiendo de los metodos 
//             asignados a estos, revisar perfil - metodo.	        
    public function perfil($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_perfil=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_perfil','','','',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para crear las acciones (metodos) del sistema, estos deben ser asignados a acada presil
//             y la funcion fbarmaboton se encarga de configurarlos y renderizarlos en la vista.     
    public function metodo($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
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
                $sistema->get_datos('*',"cod_metodos=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                $arrayChek = array("ind_habilita"=>$data[0]["ind_habilita"]);  
                foreach($arrayChek as $llave=>$valorChek){
                    if($valorChek==1){
                       $campoChek[]=$llave; 
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_metodo','','',$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para asignar los metodos a un perfil determianado, estos metodos se mostraran segun el
//             perfil del usuario.  
    public function perfilMetodo($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_perfil_metodos=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                $camposCombo=array("cod_perfil"=>$data[0]["cod_perfil"],"cod_metodos"=>$data[0]["cod_metodos"],
                                   "cod_modulo"=>$data[0]["cod_modulo"],"cod_estado"=>$data[0]["cod_estado"]);
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_perfil_metodos','',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para crear los formularios que componen el sistema, cada formulario debe ser ligado
//             a la tabla de la base de datos que este va ha gestionar, en el campo tabla debe ir exactamente
//             el nombre de la tabla para la cual se esta creando el formulario, igualmente el campo trae datos 
//             debe ir seleccionado siempre para permitir la modificacion de los datos en los diferentes formularios .     
    public function formulario($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();$campoChek  =array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_formulario=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                $camposCombo = array("cod_proceso"=>$data[0]["cod_proceso"]);
                $arrayChek = array("tip_formulario"=>$data[0]["tip_formulario"],"dat_formulario"=>$data[0]["dat_formulario"]);  
                foreach($arrayChek as $llave=>$valorChek){
                    if($valorChek==1){
                       $campoChek[]=$llave; 
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_formulario','',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para asignar los metodos a los formularios, cada formulario maneja metodos en conjunto con 
//             con otros formulario o individuales, los metodos deben ser creados anteriormente antes de este
//             proceso y configurados segun su accion. 
    public function formularioMetodo($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema = new ModeloSistema();
        $Objvista = new view;
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();$camposComboEsp=array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_formulario_metodos=".$argumentos[3],$argumentos[4]); 
                $camposComboEsp = $sistema->_data;
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_formulario_metodos','',$camposCombo,'',1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para crear los frames que componene los formulario, estos mismos contienes los inputs
//             o para el registro de la informacion, los frames se manejan por los tamaños establecidos por
//             bootstrap de 1 hasta 12, segun la cantidad de inputs que contenga un formulario, se pueden 
//             definir 1 o mas frames y relacinarlos al formulario que pertence, posteriormente, relacionar 
//             los inputs a los frames donde se desean ubicar.         
    public function frame($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
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
                $sistema->get_datos('*',"cod_frame=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                $camposCombo = array("cod_formulario"=>$data[0]["cod_formulario"]);  
                $arrayChek = array("ind_enlinea"=>$data[0]["ind_enlinea"]);  
                foreach($arrayChek as $llave=>$valorChek){
                    if($valorChek==1){
                       $campoChek[]=$llave; 
                    }
                }
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_frame','',$camposCombo,$campoChek,1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para crear los frames que componene los formulario, estos mismos contienes los inputs
//             o para el registro de la informacion, los frames se manejan por los tamaños establecidos por
//             bootstrap de 1 hasta 12, segun la cantidad de inputs que contenga un formulario, se pueden 
//             definir 1 o mas frames y relacinarlos al formulario que pertence, posteriormente, relacionar 
//             los inputs a los frames donde se desean ubicar.         
    public function detframe($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();$campoChek=array();$camposComboEsp = array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_detframe=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                //var_dump($data);exit();
                $camposComboEsp[0] = array("cod_tablareferencia"=>$data[0]["nom_tablaref"],"cod_tipoinput"=>$data[0]["cod_tipoinput"],"cod_frame"=>$data[0]["cod_frame"],"cod_estado"=>$data[0]["cod_estado"]);  
            }
            
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_detframe','',$camposCombo,'',1,$camposComboEsp,devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }
    
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para parametrizar la configuracino de general del modulo del sistema, envio
//             y recepcion de correos electronicos para sus diferenctes propositos.         
    public function ConfiguracionGeneral($metodo, $argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data);
        }else{
            $met= isset($argumentos[6]) ? $argumentos[6] : "N";
            $data=array();$camposCombo=array();$campoChek=array();$camposComboEsp = array();
            if(!empty($argumentos[3])){
                $sistema->get_datos('*',"cod_config=".$argumentos[3],$argumentos[4]); $data=$sistema->_data;
                //var_dump($data);exit();
                $camposCombo[0] = array("cod_estado"=>$data[0]["cod_estado"],"cod_empresa"=>$data[0]["cod_empresa"]);  
            }
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="'.devuelveString($argumentos[2],'*',1).'"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            $sistema->get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
            $cadEmp = $sistema->_data;
            setVariables($sistema,$Objvista,$metodo,$argumentos[1],$argumentos[0],'sys_view_config',' cod_empresa in('.$cadEmp[0]['result'].')',$camposCombo,'',1,'',devuelveString($argumentos[2],'*',2),$met);
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',devuelveString($argumentos[2],'*',1),$data,$dataFormGeneral);
        }
    }    
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para registrar las transacciones del sitema segun el formulario, se toma como referencia la tabla del formulario,
//             en el case correspondiente puede realizar las operaciones necesarias para los datos antes de enviarlos al modelo para 
//             que los procese, el array principal de datos "$user_data" almacena los datos de la tabla principal, los demas campos
//             van antepuestos en su nombre con el prefijo "no_" para queno sean procesados, estos ultimos se procesan en el modelo
//             donde igualmente se trabaja con el nombre de la tabla y se hacen los procesos adicionales, para validar si un registro 
//             existe en la tabla en mencion, utilize el metodo privado getRegistro del modelo de datos antes de enviarlo a setRegistro 
//             para que lo procese.              
    public function nuevoRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        if(!Session::get('usuario')){
            $Objvista = new view;
            $data = array('ERR'=>3,
                          'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data);
        }else{
            $user_data = helper_user_data($metodo);
            $pos = strpos($user_data['no_esq_tabla'], '_');
            $nomTabla = substr($user_data['no_esq_tabla'],$pos+1,strlen($user_data['no_esq_tabla']));
            switch($user_data['no_nom_tabla']){
                case 'nuevaEmpresa':case 'nuevaContrato':case 'nuevaCiudad':case 'nuevaPerfil':case 'nuevaMetodo':
                    $campo = array('nom_'.$nomTabla . "=");
                    $clave = array("'" . $user_data['nom_' . $nomTabla] . "'");
                break;
                case 'nuevaUsuario':
                    $campo = array('nom_usuario=',' and email_usuario=');
                    $clave = array("'" . $user_data['nom_usuario'] . "'","'" . $user_data['email_usuario'] . "'");
                break;
                case 'nuevaPerfilMetodo':
                    $campo = array('cod_perfil=',' and cod_metodos=');
                    $clave = array($user_data['cod_perfil'],$user_data['cod_metodos']);
                break;
                case 'nuevaFormulario':
                    $campo = array('nom_formulario=');
                    $clave = array("'".$user_data['nom_formulario']."'");
                    $user_data["fec_formulario"]=date("Y.m.d");
                    $user_data["hora_formulario"]=date("H:i:s");
                break;
                case 'nuevaFormularioMetodo':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_formulario"]);$e++){
                        $campo = array('cod_formulario=',' and cod_metodos=');
                        $clave = array("'".$user_data['no_cod_formulario'][$e]."'","'".$user_data['no_cod_metodos'][$e]."'");
                        if(!$sistema->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('cod_formulario'   =>$user_data["no_cod_formulario"][$e],
                                              'cod_metodos'      =>$user_data["no_cod_metodos"][$e],
                                              'no_nom_tabla'     =>$user_data['no_nom_tabla'],
                                              'no_esq_tabla'     =>$user_data['no_esq_tabla'],
                                              'no_id_formulario' =>$user_data['no_id_formulario']);
                            $sistema->setRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    }	
                    $sistema->err = 6;
                    $sistema->msj = " Metodos Asignados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'nuevaFrame':
                    $campo = array('nom_frame=');
                    $clave = array("'".$user_data['nom_frame']."'");
                break;
                case 'nuevaDetFrame':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_detframe"]);$e++){
                        $campo = array('det_campo=',' and cod_frame=');
                        $clave = array("'".$user_data['no_det_campo'][$e]."'","'".$user_data['no_cod_frame'][$e]."'");
                        if(!$sistema->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('cod_detframe'     =>$user_data["no_cod_detframe"][$e],
                                              'cod_tipoinput'    =>$user_data["no_cod_tipoinput"][$e],
                                              'nom_tablaref'     =>$user_data['no_nom_tablaref'][$e],
                                              'nom_campo'        =>$user_data['no_nom_campo'][$e],
                                              'holder_campo'     =>$user_data['no_holder_campo'][$e],
                                              'tam_campo'        =>$user_data['no_tam_campo'][$e],
                                              'det_campo'        =>$user_data['no_det_campo'][$e],
                                              'val_campo'        =>$user_data['no_val_campo'][$e],
                                              'cod_frame'        =>$user_data['no_cod_frame'][$e],
                                              'cod_estado'       =>$user_data['no_cod_estado'][$e],
                                              'no_nom_tabla'     =>$user_data['no_nom_tabla'],
                                              'no_esq_tabla'     =>$user_data['no_esq_tabla'],
                                              'no_id_formulario' =>$user_data['no_id_formulario']);
                            $sistema->setRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    }	
                    $sistema->err = 6;
                    $sistema->msj = " Campos Creados !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                break;
                case 'MenuUsuario': 
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;									
                    for($a=0;$a<count($user_data["cod_usuario"]);$a++){
                        $campo = array('cod_usuario = ','cod_menu = ');
                        for($e=0;$e<count($user_data["cod_menu"]);$e++){
                            $clave = array($user_data["cod_usuario"][$a] . " and " , $user_data["cod_menu"][$e]);
                            if(!$sistema->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                                $user_data1=array('cod_usuario'     =>$user_data["cod_usuario"][$a],
                                                  'cod_menu'        =>$user_data["cod_menu"][$e],
                                                  'no_nom_tabla'    =>$user_data['no_nom_tabla'],
                                                  'no_esq_tabla'    =>$user_data['no_esq_tabla'],
                                                  'no_id_formulario'=>$user_data['no_id_formulario']);
                                $sistema->setRegistro($user_data1);
                                $sinErr += + 1;
                            }else{ 
                                $conErr += + 1;
                            }
                        }	
                    }
                    $sistema->err = 6;
                    $sistema->msj = " Menu Asignado !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;								
                break;
                case 'subMenuUsuario': 
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    $err = 6;									
                    for($a=0;$a<count($user_data["cod_usuario"]);$a++){
                        $campo = array('cod_usuario = ','cod_menu_sub = ');
                        for($e=0;$e<count($user_data["cod_menu_sub"]);$e++){
                            $clave = array($user_data["cod_usuario"][$a] . " and " , $user_data["cod_menu_sub"][$e]);
                            $sistema->getRegistro($user_data['no_esq_tabla'], $campo, $clave);
                            if(!$sistema->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                                unset($user_data1);
                                $user_data1 = array('cod_usuario'     =>$user_data["cod_usuario"][$a],
                                                    'cod_menu_sub'    =>$user_data["cod_menu_sub"][$e],
                                                    'no_nom_tabla'    =>$user_data['no_nom_tabla'],
                                                    'no_esq_tabla'    =>$user_data['no_esq_tabla'],
                                                    'no_id_formulario'=>$user_data['no_id_formulario']);
                                $sistema->setRegistro($user_data1);
                                $sistema->set_simple_query(" call pbAsignaSubMenu(" . $user_data["cod_usuario"][$a] . "," . $user_data["cod_menu_sub"][$e] . ")");
                                $sinErr +=  1;
                            }else{ 
                                $conErr +=  1;
                            }
                        }	
                    }
                    $sistema->err = 6;
                    $sistema->msj = " Sub Menu Asignado !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;									
                break;
                case 'nuevaEmpresaVsContrato':
                    $campo = array('cod_empresa = ','cod_contrato = ','cod_modulo = ');
                    $clave = array($user_data['cod_empresa']. " and ",$user_data['cod_contrato']. " and ",$user_data['cod_modulo']);
                break;
                case 'mensajes':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    $user_data['de_cod_usuario'] = 	Session::get('cod');
                    for($a=0;$a<count($user_data["a_cod_usuario"]);$a++){
                        unset($user_data1);
                        $user_data1=array('a_cod_usuario '   =>$user_data["a_cod_usuario"][$a],
                                          'de_cod_usuario'   =>$user_data["de_cod_usuario"],
                                          'asu_mensajes'     =>$user_data["asu_mensajes"],
                                          'des_mensajes'     =>$user_data["des_mensajes"],
                                          'no_nom_tabla'     =>$user_data['no_nom_tabla'],
                                          'no_esq_tabla'     =>$user_data['no_esq_tabla'],
                                          'no_id_formulario' =>$user_data['no_id_formulario'],
                                          'no_nombre_img'    =>$user_data['no_nombre_img'],
                                          'no_tamano_img'    =>$user_data['no_tamano_img'],
                                          'no_tmp_img'       =>$user_data['no_tmp_img'],
                                          'cod_estado'       =>'MAA',
                                          'fec_mensajes'     =>date("Y.m.d"),
                                          'hora_mensajes'    =>date("H:i:s"));
                        $sistema->setRegistro($user_data1);
                    }
                    $sistema->err = 6;
                    $sistema->msj = " El mensaje ha sido enviado correcatemente !!!" ;								
                break;
                case 'nuevaEstado':
                    $campo = array('cod_estado' . "=");
                    $clave = array("'" . $user_data['cod_estado'] . "'");
                break;
                case 'NuevaConfiguracionGeneral':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                break;
            }
            if($valida){
                if(!$sistema->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                    $sistema->setRegistro($user_data);
                    $data = array('ERR'=>6,
                                  'MSJ'=>$sistema->msj);
                }else{
                    $data = array('ERR'=>6,
                                  'MSJ'=>"El registro ya existe");	
                }
                setVariables($sistema,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $sistema->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$sistema->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data= array('ERR' => $sistema->err,
                             'MSJ' => $sistema->msj);
                setVariables($sistema,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $sistema->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$sistema->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
        
    public function editaRegistro($metodo,$argumentos = array()){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $valida = true;
        $sistema  = new ModeloSistema();
        $Objvista = new view;
        $dataFormGeneral=array();
        if(!Session::get('usuario')){
            $data = array('ERR'=>3,
                              'MSJ'=>'No ha iniciado Sesi&oacute;n');
            $cadenaSql= fbRetornaConfigForm();
            $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
            $dataFormGeneral=$sistema->_data;
            //var_dump($sistema->_data);exit();
            $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
        }else{
            $user_data = helper_user_data('nuevoRegistro');
            switch($user_data['no_nom_tabla']){
                case 'nuevaDetFrame':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_detframe"]);$e++){
                        $campo = array('det_campo=',' and cod_frame=');
                        $clave = array("'".$user_data['no_det_campo'][$e]."'","'".$user_data['no_cod_frame'][$e]."'");
                        if($sistema->getRegistro($user_data['no_esq_tabla'], $campo, $clave)){
                            $user_data1=array('cod_detframe'     =>$user_data["no_cod_detframe"][$e],
                                              'cod_tipoinput'    =>$user_data["no_cod_tipoinput"][$e],
                                              'nom_tablaref'     =>$user_data['no_nom_tablaref'][$e],
                                              'nom_campo'        =>$user_data['no_nom_campo'][$e],
                                              'holder_campo'     =>$user_data['no_holder_campo'][$e],
                                              'tam_campo'        =>$user_data['no_tam_campo'][$e],
                                              'det_campo'        =>$user_data['no_det_campo'][$e],
                                              'val_campo'        =>$user_data['no_val_campo'][$e],
                                              'cod_frame'        =>$user_data['no_cod_frame'][$e],
                                              'cod_estado'       =>$user_data['no_cod_estado'][$e],
                                              'no_nom_tabla'     =>$user_data['no_nom_tabla'],
                                              'no_esq_tabla'     =>$user_data['no_esq_tabla'],
                                              'no_id_formulario' =>$user_data['no_id_formulario']);
                            
                            $sistema->editRegistro($user_data1);
                            $sinErr += + 1;
                        }else{ 
                            $conErr += + 1;
                        }
                    }	
                    $sistema->err = 6;
                    $sistema->msj = " Detalle Frame creado !!! correctamente: " . $sinErr . " Fallidos: " . $conErr;
                    setVariables($sistema,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                    $cadenaSql= fbRetornaConfigForm();
                    $sistema->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                    $dataFormGeneral=$sistema->_data;
                    $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
                break;
                case 'nuevaMetodo':
                    !isset($user_data["ind_habilita"]) ? $user_data["ind_habilita"]=0 : $user_data["ind_habilita"]=1; 
                break;
                case 'NuevaConfiguracionGeneral':
                    $campo = array('nom_config' . "=");
                    $clave = array("'" . $user_data['nom_config'] . "'");
                break;
                case 'nuevaUsuario':
                    $campo = array('nom_usuario=',' and email_usuario=');
                    $clave = array("'" . $user_data['nom_usuario'] . "'","'" . $user_data['email_usuario'] . "'");
                break;
                case 'NuevaPerfilUsuario':
                    $campo = array('nom_usuario=',' and email_usuario=');
                    $clave = array("'" . $user_data['nom_usuario'] . "'","'" . $user_data['email_usuario'] . "'");
                break;
                case 'nuevaFormularioMetodo':
                    $valida=false;
                    $conErr = 0;
                    $sinErr = 0;
                    for($e=0;$e<count($user_data["no_cod_formulario"]);$e++){
                        $user_data1=array('cod_formulario_metodos'=>$user_data["no_cod_formulario_metodos"][$e],
                                          'cod_formulario'        =>$user_data["no_cod_formulario"][$e],
                                          'cod_metodos'           =>$user_data["no_cod_metodos"][$e],
                                          'no_nom_tabla'          =>$user_data['no_nom_tabla'],
                                          'no_esq_tabla'          =>$user_data['no_esq_tabla'],
                                          'no_id_formulario'      =>$user_data['no_id_formulario']);
                        $sistema->editRegistro($user_data1);
                        $sinErr += + 1;
                    }	
                    $sistema->err = 6;
                    $sistema->msj = " Metodos Asignados !!! correctamente: " . $sinErr;
                break;
            }
            if($valida){
                $sistema->editRegistro($user_data);
                $data = array('ERR'=>6,
                              'MSJ'=>$sistema->msj);
                setVariables($sistema,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $sistema->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$sistema->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }else{
                $data= array('ERR' => $sistema->err,
                             'MSJ' => $sistema->msj);
                setVariables($sistema,$Objvista,$user_data["no_nom_tabla"],$user_data["no_id_formulario"],1,'','','','',1,'',$user_data["no_cod_menu_sub"]);
                $cadenaSql= fbRetornaConfigForm();
                $sistema->get_datos($cadenaSql, 'head_formulario_config="'.$user_data["no_nom_tabla"].'"', 'sys_formulario_config');
                $dataFormGeneral=$sistema->_data;
                $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','index',$user_data["no_nom_tabla"],$data,$dataFormGeneral);
            }
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo de finalizar y destruir las variables de session,
//             retorna la vista de login.	
    public function cerrar(){
        require_once ROOT . DEFAULT_CORE; 
        require_once ROOT . DEFAUL_FUNCTION;
        require_once ROOT . 'modules/sistema/model/sistemaModel.php';
        require_once ROOT . VIEW_PACH . DS . 'view.php';
        $dataFormGeneral=array();
        $Objvista = new view;
        $sistema  = new ModeloSistema();
        foreach($_SESSION as $session=>$valor) {
                Session::destroy($session);
        }
        
        $data = array('ERR'=>13,
                      'MSJ'=>'');
        $cadenaSql= fbRetornaConfigForm();
        $sistema->get_datos($cadenaSql, 'head_formulario_config="login"', 'sys_formulario_config');
        $dataFormGeneral=$sistema->_data;
        $sistema->get_datos('fbDevuelveArchivos(275,1) as ARCHIVOSCSS');
        $Objvista->_archivos_css = $sistema->_data;
        $sistema->get_datos('fbDevuelveArchivos(275,2) as ARCHIVOSSCRIPT');
        $Objvista->_archivos_js  = $sistema->_data;
        $Objvista->retornar_vista(DEFAULT_CONTROLLER,'sistema','login','login',$data,$dataFormGeneral);
    }
}
?>