<?php

class ModeloStk extends DBAbstractModel {
    private $_modulo=10;
    protected $cod;
    public $nombre;
    public $apellido;
    public $email;
    public $usuario;
    public $password;
    public $cod_estado;
    public $img;
    public $_data = array();
    public $_session = array();
    public $_sessionVal = array();
    public $_empresa = array();
    public $_mensajes = array();
    public $_numMensajes = array();
    public $_menu = array();
    public $_menuHeader = array();
    public $_notificacion = array();
    public $_numNotificacion = array();
    public $_navegacion = array();
    public $_formulario = array();
    public $_formulario_ayuda = array();
    public $_boton = array();
    public $_frame = array();
    public $_input = array();
    public $_tabla = array();
    public $_cTabla = array();

    public function get_login($data = array()) {
        if (array_key_exists('username', $data)) {
            $this->query = " SELECT cod_usuario,CONCAT(nom_usuario,' ',ape_usuario) as nom_usuario,email_usuario,
			                        usuario_usuario,cod_estado,concat('modules/sistema/adjuntos/',img_usuario) as img_usuario 
				               FROM sys_usuario
				              WHERE usuario_usuario  = '" . $data['username'] . "' AND
							        password_usuario = '" . md5($data['password']) . "'";
            $this->get_results_from_query();
        }
        if (count($this->rows) == 1) {
            foreach ($this->rows[0] as $propiedad => $valor) {
                $propiedad = str_replace('_usuario', '', $propiedad);
                $this->_session[] = $propiedad;
                $this->_sessionVal[] = $valor;
                $this->$propiedad = $valor;
            }
            if ($this->cod_estado != 'AAA') {
                $this->msj = 'La sesi&oacute;n esta desactivada, consulte al administrador. ';
                $this->err = '1';
                return false;
                exit();
            }
            return true;
        } else {
            $this->msj = 'Informaci&oacute;n incorrecta, revise los datos. ';
            $this->err = '0';
            return false;
            exit();
        }
    }

    public function get_config_usuario($usuario) {
        #traemos contrato, empresa, modulos del usuario
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT t1.*,t2.*,t3.*,t4.*,t5.*  
				          FROM sys_empresa_contrato as t1, 
						  	   sys_empresa          as t2,
							   sys_contrato         as t3,
							   mod_modulo           as t4,
							   sys_ciudad           as t5  
				         WHERE t1.cod_empresa     = t2.cod_empresa  AND
						       t1.cod_contrato    = t3.cod_contrato AND
							   t1.cod_modulo      = t4.cod_modulo   AND
							   t2.cod_ciudad      = t5.cod_ciudad   AND
							   t1.cod_estado      = 'AAA'           AND 
						       t1.cod_usuario     = '" . $usuario . "'";
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_empresa[] = array($pro => $va);
            }
        }
        #traemos las notificaciones
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT CONCAT('" . INI_NOT . "',
                                       'data=',cod_notificacion, '  data-rel=notificaciones',
				       '" . MED_NOT . "', des_notificacion, 
                                       '" . FIN_NOT . "') as des_notificacion    
		           FROM sys_notificacion  
			  WHERE cod_estado      = 'NAA'	 
                            AND cod_usuario     = '" . $usuario . "'
                       ORDER BY cod_notificacion DESC";
        $this->get_results_from_query();
        $this->_numNotificacion[0] = array('num_notificacion' => 0);
        $this->_notificacion[0] = array('des_notificacion' => "");
        if (count($this->rows) >= 1) {
            $this->_numNotificacion[0] = array('num_notificacion' => count($this->rows));
            foreach ($this->rows as $pro => $va) {
                $this->_notificacion[] = $va;
            }
        }
        #traemos los mensajes
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT CONCAT('" . INI_MSJ . "', 
									   'data=',t1.cod_mensajes,'  data-rel=mensajes',
									   '" . MED_MSJ . "', t2.nom_usuario,  
									   '" . MED_MSJ_1 . "',t1.fec_mensajes,'" . MED_MSJ_2 . "',
									   SUBSTRING(t1.des_mensajes,1,45)  ,'" . FIN_MSJ . "') as des_mensajes     
				          FROM sys_mensajes as t1, sys_usuario as t2   
				         WHERE t1.cod_estado      = 'MAA'	       AND
						 	   t1.de_cod_usuario  = t2.cod_usuario AND    
						       t1.a_cod_usuario   = '" . $usuario . "'";
        $this->get_results_from_query();
        $this->_numMensajes[0] = array('num_mensajes' => 0);
        $this->_mensajes[0] = array('des_mensajes' => "");
        if (count($this->rows) >= 1) {
            $this->_numMensajes[0] = array('num_mensajes' => count($this->rows));
            foreach ($this->rows as $pro => $va) {
                $this->_mensajes[] = $va;
            }
        }
        #traemos los menus y submenus
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT CONCAT('" . INI_NAV . "', t1.met_menu,
			                           '" . MED_NAV . "', t1.icon_menu,
								       '" . MED_NAV_1 . "',t1.nom_menu,
								       '" . FIN_NAV . "', '" . INI_SNAV . "',
								       fbArmaSubMenu(t1.cod_menu,t3.cod_usuario),'" . FIN_SNAV . "') as menu 
                           FROM sys_menu as t1, sys_usuario_menu as t3 
						  WHERE t3.cod_menu      = t1.cod_menu and 
							    t3.cod_usuario   = '" . $usuario . "'  
					   ORDER BY t1.cod_menu";
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_menu[] = $va;
            }
        } else {
            $this->_menu[0] = array('menu' => "");
        }
        #traemos los menusHeader
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT fbArmaSubMenuHeader($usuario) as menu_header";
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_menuHeader[] = $va;
            }
        } else {
            $this->_menuHeader[0] = array('menu_header' => "");
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo general para cargar los formularios y su contenido segun los parametros,
//             de la peticion, el siguiente metodo arma todo el formulario y sus componentes(inputs)
//             arma las ayudas y los botones pertenecientes a este formulario, el formulario solo puede
//             ser accedido por los usuarios que poseen los permisos para este objetivo, si no los posee
//             se imprimira el mensaje de error, se maneja bajo funcion almacenada en mysql para dejar la 
//             la carga al servidor y optimizar el proceso.
    public function get_form($metodo,$usuario,$form,$data=0,$ciclo=1,$codSubM=null,$cMet=""){
        #traemos el formulario
        $this->rows  = "";
        $this->query = "";
        $this->query = " SELECT fbArmaFormulario($form,$usuario,$data,$ciclo,$codSubM,$this->_modulo,'".$cMet."') as formulario; ";
        //print $this->query;exit;
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {				
                $this->_formulario[] = $va;
                if($this->_formulario[0]['formulario'] == ''){				
                    $mensjFrm             = getMenssage('danger','Ocurrio un error','No tiene permisos para acceder a esta opcion. ');
                    $this->_formulario[0] = array('formulario'=>$mensjFrm);
                }
            }
        }	
        #traemos la ayuda del form
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT fbArmaAyudaFormulario($form,$usuario,1) as formulario_ayuda";
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_formulario_ayuda[] = $va;
                if ($this->_formulario_ayuda[0]['formulario_ayuda'] == '') {
                    $mensjFrm                   = getMenssage('info', 'Ocurrio un error', 'No hay ayuda registrada para este proceso,consulte al administrador del sistema. ');
                    $this->_formulario_ayuda[0] = array('formulario_ayuda' => $mensjFrm);
                }
            }
        }
        #Traemos los botones por formulario y usuario
        ModeloStk::get_boton($metodo,$usuario,$form,$cMet);
    }
    #traemos la tabla
    public function get_table($metodo, $usuario, $form, $vista, $condicion = Null,$cMet) {
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT fbArmaCabTabla('$vista') as tabla";
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_tabla[] = $va;
                if ($this->_tabla[0]['tabla'] == '') {
                    $mensjFrm = getMenssage('danger', 'Ocurrio un error', 'No tiene permisos para acceder a esta opcion. ');
                    $this->_tabla[0] = array('tabla' => $mensjFrm);
                }
            }
        }
        #traemos los campos
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT * from $vista ";
        if (!empty($condicion)) {
            $this->query .= " where " . $condicion;
        }
        //print $this->query;exit();
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_cTabla[] = $va;
            }
        }
        #traemos la ayuda del form
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT fbArmaAyudaFormulario($form,$usuario,2) as formulario_ayuda";
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_formulario_ayuda[] = $va;
                if ($this->_formulario_ayuda[0]['formulario_ayuda'] == '') {
                    $mensjFrm = getMenssage('info', 'Ocurrio un error', 'No hay ayuda registrada para este proceso,consulte al administrador del sistema. ');
                    $this->_formulario_ayuda[0] = array('formulario_ayuda' => $mensjFrm);
                }
            }
        }
        ModeloStk::get_boton($metodo, $usuario, $form,$cMet);
    }

    public function get_boton($metodo, $usuario, $form,$cMet="N") {
        #traemos los botones para las acciones del formulario
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT fbArmaBoton($form,$usuario,'".$cMet."') as boton; ";
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_boton[] = $va;
                if ($this->_boton[0]['boton'] == '') {
                    #mensaje de error: warning, danger, success, info
                    $mensjBto = '';
                    $this->_boton[0] = array('boton' => $mensjBto);
                }
            }
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para traer datos de una consulta dependiendo de los parametros de entrada
    public function get_datos($clave, $condicion="", $table='dual', $aux = null) {
        #traemos los datos segun la referencia de la entrada de la funcion
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT $clave FROM $table ";
        !empty($condicion) ? $this->query .= "WHERE $condicion" : "";
        //print $this->query;
        $this->get_results_from_query();
        unset($this->_data);
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_data[] = $va;
            }
        } else {
            $this->_data = "";
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: metodo para ejecutar sentencias simples sin utilizar el modelo
    public function set_simple_query($query) {
        $this->query = "";
        $this->query = $query;
        //print $this->query;exit;
        $this->execute_single_query();
        if(empty($this->err)):
            $this->err = 6;
            $this->msj = "La transaccion se registro correctamente. ";
        endif;    
            
    }
    
    public function getRegistro($tabla, $campo = array(), $clave = array()) {
        $this->rows = "";
        $this->query = "";
        $condicion = "";
        for ($x = 0; $x < count($campo); $x++) {
            $v = $campo[$x] . $clave[$x];
            $condicion = $condicion . $v;
        }
        $this->query = " SELECT * 
                               FROM " . $tabla . " 
                              WHERE " . $condicion . " ";
        //print $this->query;exit;
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            return true;
            exit();
        }
        return false;
    }
    
    public function getRegistroStoreP($procedimiento = "") {
        $this->row = "";
        $this->query = "";
        $this->query = $procedimiento;
        $this->get_results_from_sp();
        $this->err=$this->row["@cNumError"];
        $this->msj=$this->row["@cDesError"];
    }
    
    public function setRegistro($user_data = array()) {
        $this->query = "";
        $tblCol = '';
        $tblVal = ''; $err_img = '';		
        $colExt = ''; $colName = '';
        $valExt = ''; $valName = '';

        if (isset($user_data['cod_' . str_replace('sys_','',str_replace('stk_', '', $user_data['no_esq_tabla']))]) and empty($user_data['cod_' . str_replace('sys_','',str_replace('stk_', '', $user_data['no_esq_tabla']))])) {
            $user_data['cod_' . str_replace('sys_','',str_replace('stk_', '', $user_data['no_esq_tabla']))] = ModeloStk::setSigSecuencia($user_data['no_esq_tabla']);
        }
        // se llena el valor para los campos que lo requiran el el array
        if (isset($user_data['cod_usuario']) and empty($user_data['cod_usuario'])){$user_data['cod_usuario'] = Session::get('cod');}
        /* obtengo el nombre de la tabla para futuras validaciones */
        $pos = strpos($user_data['no_esq_tabla'], '_');
        $nomTabla = substr($user_data['no_esq_tabla'], $pos + 1, strlen($user_data['no_esq_tabla']));
        fbFormateaPost($user_data, $tblCol, $tblVal);
        if (isset($user_data["no_tmp_img"]) Or !empty($user_data["no_tmp_img"])) {
            $err_img = $err_img . uploadImg($user_data, SINED_DIR_ADJ, $valName, $colName);
        }

        if (!empty($valName)) {
            $valExt = $valExt . ",'" . $valName . "'";
            $colExt = $colExt . "," . $colName . "";
            $colExt = $colExt . $nomTabla;
        }

        $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "
                                     (" . substr($tblCol, 0, (strlen($tblCol) - 1)) . "" . $colExt . ")  
                              VALUES (" . substr($tblVal, 0, (strlen($tblVal) - 1)) . "" . $valExt . ")";
        //print $this->query;exit;
        $this->execute_single_query();
        switch ($user_data['no_nom_tabla']) {
            case 'NuevaPreinscripciones':
                
            break;
            case 'ConfiguracionGeneralSined':
                if ($user_data['cod_estado'] == 'AAA') {
                    $this->query = " call pbActualizaConfig(4," . $user_data['cod_config'] . ")";
                    $this->execute_single_query();
                    $err_img = $err_img . " La configuracion ha sido definida como predeterminada";
                }
            break;
        }
        $this->msj = "La transaccion se registro correctamente. " . $err_img;
    }

    # Modificar un registro

    public function editRegistro($user_data = array()) {
        $this->query = "";
        $tblCol = '';
        $tblVal = ''; $err_img = '';		
        $colExt = ''; $colName = '';
        $valExt = ''; $valName = '';

        if (isset($user_data['cod_usuario']) and empty($user_data['cod_usuario'])) {
            $user_data['cod_usuario'] = Session::get('cod');
        }
        /* obtengo el nombre de la tabla para futuras validaciones */
        $pos = strpos($user_data['no_esq_tabla'], '_');
        $nomTabla = substr($user_data['no_esq_tabla'], $pos + 1, strlen($user_data['no_esq_tabla']));
        foreach ($user_data as $col => $dat) {
            if ((strpos(substr($col, 0, 3), 'no_') === false) && (strpos($col, 'cod_' . $nomTabla) === false)) {
                $tblCol = $tblCol . $col . "='" . $dat . "'" . ',';
            }
        }

        if (isset($user_data["no_tmp_img"]) Or !empty($user_data["no_tmp_img"])) {
            $err_img = $err_img . uploadImg($user_data, SINED_DIR_ADJ, $valName, $colName);
        }

        if (!empty($valName)) {
            $colExt = $colExt . "," . $colName . "";
            $colExt = $colExt . $nomTabla . "='" . $valName . "'";
        }
        $this->query = " UPDATE " . $user_data['no_esq_tabla'] . "
                            SET " . substr($tblCol, 0, (strlen($tblCol) - 1)) . $colExt . "   
                          WHERE " . 'cod_' . $nomTabla . "=" . $user_data['cod_' . $nomTabla] . "";

        //print $this->query;exit();
        $this->execute_single_query();
        //acciones antes de editar el registro
        switch ($user_data['no_nom_tabla']) {
            case 'nuevaUsuario':
            break;
            case 'ConfiguracionGeneralSined':
                if ($user_data['cod_estado'] == 'AAA') {
                    $this->query = " call pbActualizaConfig(4," . $user_data['cod_config'] . ")";
                    $this->execute_single_query();
                    $err_img = $err_img . " La configuracion ha sido definida como predeterminada";
                }
            break;
        }
        $this->msj = "La transaccion se edito correctamente. " . $err_img;
    }

    #trae la siguiente secuencia de la tabla	

    public function setSigSecuencia($nomTbl) {
        $this->query = "SELECT IF(MAX(cod_" . str_replace('sys_','',str_replace('stk_', '', $nomTbl)) . " IS NOT NULL),MAX(cod_" . str_replace('sys_','',str_replace('stk_', '', $nomTbl)) . " + 1),1) as codSec 
                       FROM " . $nomTbl . " ";
        //print $this->query;exit;
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            return $numSec = strval($this->rows[0]['codSec']);
            exit();
        }
    }

    # Método constructor

    function __construct() {
        $this->db_name = 'appmoduler';
    }

    # Método destructor del objeto

    function __destruct() {
        unset($this);
    }

}

?>
