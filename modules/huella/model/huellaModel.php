<?php

class ModeloHuella extends DBAbstractModel {
    private $_modulo=7;    
    protected $cod;

    //Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para inicializar la sesion siempre y cuando el usuario extista,
//             si no existe se imprime nuevamente la vista de sesion y se muestra el mensaje
    public function get_login($data=array()){
        if(array_key_exists('username', $data)){
            $this->query = " SELECT cod_usuario,CONCAT(nom_usuario,' ',ape_usuario) as nom_usuario,email_usuario,
                                    usuario_usuario,cod_estado,concat('modules/sistema/adjuntos/',img_usuario) as img_usuario 
                               FROM sys_usuario
                              WHERE usuario_usuario  = '" .$data['username']. "' 
                                AND password_usuario = '" .md5($data['password']). "'";
            $this->get_results_from_query();
        }
        if(count($this->rows) == 1){
            foreach ($this->rows[0] as $propiedad=>$valor) {
                $propiedad           = str_replace('_usuario','',$propiedad);
                $this->_session[]    = $propiedad;
                $this->_sessionVal[] = $valor;
                $this->$propiedad    = $valor;
            }
            if($this->cod_estado != 'AAA'){
                $this->msj = 'La sesi&oacute;n esta desactivada, consulte al administrador. ';
                $this->err = '1';				
                return false;exit();	
            }
            return true;
        }else{
            $this->msj = 'Informaci&oacute;n incorrecta, revise los datos. ';
            $this->err = '0';				
            return false;exit();
        }
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para cargar los datos informativos generales de cada usuario en el 
//             dashboard de la app si el usuario se loguea exitosamente.	
    public function get_config_usuario($usuario){
        #traemos contrato, empresa, modulos del usuario
        $this->rows  = "";
        $this->query = "";
        $this->query = " SELECT t1.*,t2.*,t3.*,t4.*,t5.*,concat('modules/sistema/adjuntos/',t2.img_empresa) as LogoEmpresa, t2.nom_empresa as NomEmpresa  
                           FROM sys_empresa_contrato as t1,sys_empresa          as t2,
                                sys_contrato         as t3,mod_modulo           as t4,
                                sys_ciudad           as t5,sys_usuario_empresa  as t6   
                          WHERE t1.cod_empresa     = t2.cod_empresa  
                            AND t1.cod_contrato    = t3.cod_contrato 
                            AND t1.cod_modulo      = t4.cod_modulo   
                            AND t2.cod_ciudad      = t5.cod_ciudad 
                            AND t2.cod_empresa     = t6.cod_empresa
                            AND t1.cod_estado      = 'AAA'           
                            AND t6.cod_usuario     = '" .$usuario. "'";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_empresa[] = array($pro=>$va);
            }
        }
        #traemos las notificaciones
        $this->rows  = "";
        $this->query = "";
        $this->query = " SELECT CONCAT('" .INI_NOT. "', 
                                       'data=',cod_notificacion, '  data-rel=notificaciones',
                                       '" .MED_NOT. "', 
                                       des_notificacion, 
                                       '" .FIN_NOT. "') as des_notificacion    
                           FROM sys_notificacion  
                          WHERE cod_estado      = 'NAA'	 
                            AND cod_usuario     = '" .$usuario. "'";
        $this->get_results_from_query();
        $this->_numNotificacion[0] = array('num_notificacion'=>0);	
        $this->_notificacion[0]    = array('des_notificacion'=>"");
        if(count($this->rows) >= 1){
            $this->_numNotificacion[0] = array('num_notificacion'=>count($this->rows));
            foreach ($this->rows as $pro=>$va) {				
                $this->_notificacion[] = $va;
            }
        }
        
        #traemos los mensajes
        $this->rows  = "";
        $this->query = "";
        $this->query = " SELECT CONCAT('" .INI_MSJ. "', 
                                       'data=',t1.cod_mensajes,'  data-rel=mensajes',
                                       '" .MED_MSJ. "', t2.nom_usuario,  
                                       '" .MED_MSJ_1. "',t1.fec_mensajes,'" .MED_MSJ_2. "',
                                       SUBSTRING(t1.des_mensajes,1,45)  ,'" .FIN_MSJ. "') as des_mensajes     
                                  FROM sys_mensajes as t1, sys_usuario as t2   
                                 WHERE t1.cod_estado      = 'MAA'	       
                                   AND t1.de_cod_usuario  = t2.cod_usuario 
                                   AND t1.a_cod_usuario   = '" .$usuario. "'";
        $this->get_results_from_query();
        $this->_numMensajes[0] = array('num_mensajes'=>0);	
        $this->_mensajes[0]    = array('des_mensajes'=>"");
        if(count($this->rows) >= 1){
            $this->_numMensajes[0] = array('num_mensajes'=>count($this->rows));
            foreach ($this->rows as $pro=>$va) {				
                $this->_mensajes[] = $va;
            }
        }
        #traemos los menus y submenus
        $this->rows  = "";
        $this->query = "";  
        $this->query = " SELECT CONCAT('" .INI_NAV. "', t1.met_menu,
                                       '" .MED_NAV. "', t1.icon_menu,
                                       '" .MED_NAV_1. "',t1.nom_menu,
                                       '" .FIN_NAV. "', '" .INI_SNAV. "',
                                fbArmaSubMenu(t1.cod_menu,t3.cod_usuario),'" .FIN_SNAV. "') as menu 
                           FROM sys_menu as t1, sys_usuario_menu as t3 
                          WHERE t3.cod_menu      = t1.cod_menu 
                            AND t3.cod_usuario   = '" .$usuario. "'  
                       ORDER BY t1.cod_indice";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {				
                $this->_menu[] = $va;
            }
        }else{$this->_menu[0] = array('menu'=>"");}
        #traemos los menusHeader o accesos directos de la app
        $this->rows  = "";
        $this->query = "";  
        $this->query = " SELECT fbArmaSubMenuHeader($usuario) as menu_header";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {				
                $this->_menuHeader[] = $va;
            }
        }else{$this->_menuHeader[0] = array('menu_header'=>"");}
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
        if(!$this->_formulario[0]['formulario']==''){ModeloHuella::get_boton($metodo,$usuario,$form,$cMet);}
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo general para cargar los tablas y su contenido segun los parametros,
//             de la peticion, el siguiente metodo arma una tabla partiendo de una vista 
//             almacenada en mysql. inicialmente carga la cabecera cabecera y columnas dependiendo
//             de lo configurado en la vista y posteriormente carga todo el contenido de la misma vista
//             el metodo tiene la posibilidad de enviar condicion si lo requiere para cargar datos
//             especificos de una tabla, al finalizar la carga, trae la ayuda de la vista y los botones.
    public function get_table($metodo, $usuario, $form, $vista, $condicion=Null,$cMet,$tablaAux=null,$selecionAux=null) {
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
        $this->query = empty($tablaAux) ? " SELECT * from $vista " : "select $selecionAux from $tablaAux";
        if (!empty($condicion)) {
            $this->query .= " where " . $condicion;
        }
        //print $this->query;exit;
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
        #Traemos los botones por formulario y usuario
        ModeloHuella::get_boton($metodo,$usuario,$form,$cMet);
    }
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo general para cargar los botones relacionados a una vista, trae los botones especificos
//             por rol de usuario y permisos configurados para este
    public function get_boton($metodo,$usuario,$form,$cMet="N") {
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
//descripcion: Metodo para traer datos de una consulta dependiendo de los parametros de entrada,
    public function get_datos($clave, $condicion="", $table='dual', $aux = null) {
        #traemos los datos segun la referencia de la entrada de la funcion
        $this->rows = "";
        $this->query = "";
        $this->query = " SELECT $clave FROM $table ";
        !empty($condicion) ? $this->query .= "WHERE $condicion" : "";
        //print $this->query;exit();
        $this->get_results_from_query();
        unset($this->_data);
        if (count($this->rows) >= 1) {
            foreach ($this->rows as $pro => $va) {
                $this->_data[] = $va;
            }
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
//Autor:       David G -  Abr 2-2014 
//descripcion: Metodo para validar registros existentes antes de realizar un insert en la base de datos,
//             retorna true si el registro existe y restringe el insert, el select es dinamico y se arma
//             dependiendo de los campos y las claves, los campos son los propios de la tabla que se desea
//             consultar y las claves los valores de estos campos, se puede hacer la referencia en el controller
//             de este modulo de como funciona.
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
        //print $this->query;exit();
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            return true;
            exit();
        }
        return false;
    }
//Autor:       David G -  Mar 20-2015 
//descripcion: Metodo para ejecutar un procedimiento almacenado y obtener las respuesta del mismo
//en las variables cNumError y cDesError.
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
        $tblVal = '';
        $err_img = '';
        $colExt = '';
        $colName = '';
        $valExt = '';
        $valName = '';

        if (isset($user_data['cod_' . str_replace('hue_', '', $user_data['no_esq_tabla'])]) and empty($user_data['cod_' . str_replace('hue_', '', $user_data['no_esq_tabla'])])) {
            $user_data['cod_' . str_replace('hue_', '', $user_data['no_esq_tabla'])] = ModeloHuella::setSigSecuencia($user_data['no_esq_tabla']);
        }
        if (isset($user_data['cod_usuario']) and empty($user_data['cod_usuario'])) {
            $user_data['cod_usuario'] = Session::get('cod');
        }
        /* obtengo el nombre de la tabla para futuras validaciones */
        $pos = strpos($user_data['no_esq_tabla'], '_');
        $nomTabla = substr($user_data['no_esq_tabla'], $pos + 1, strlen($user_data['no_esq_tabla']));
        foreach ($user_data as $col => $dat) {
            if (strpos($col, 'no_') === false) {
                $tblCol = $tblCol . $col . ',';
                $tblVal = $tblVal . "'" . $dat . "'" . ',';
            }
        }

        if (isset($user_data["no_tmp_img"]) Or !empty($user_data["no_tmp_img"])) {
            $err_img = $err_img . uploadImg($user_data, HUE_DIR_ADJ, $valName, $colName);
        }

        if (!empty($valName)) {
            $valExt = $valExt . ",'" . $valName . "'";
            $colExt = $colExt . "," . $colName . "";
            $colExt = $colExt . $nomTabla;
        }

        $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "
                                         (" . substr($tblCol, 0, (strlen($tblCol) - 1)) . "" . $colExt . ")  
                                  VALUES (" . substr($tblVal, 0, (strlen($tblVal) - 1)) . "" . $valExt . ")";
        //print $this->query;exit();
        $this->execute_single_query();
        switch ($user_data['no_nom_tabla']) {
            case 'nuevaVehiculo':
                $this->query="INSERT INTO hue_vehiculo_datos(res_mov_vehiculo_datos,pot_vehiculo_datos,num_pue_vehiculo_datos,lim_pro_vehiculo_datos,
                                                            fec_mat_vehiculo_datos,fec_exp_lic_vehiculo_datos,fec_ven_vehiculo_datos,tt_vehiculo_datos,
                                                            lat_vehiculo_datos,cod_vehiculo)
                                                     VALUES ('" . $user_data['no_res_mov_vehiculo_datos'] ."','" . $user_data['no_pot_vehiculo_datos'] ."','" . $user_data['no_num_pue_vehiculo_datos'] ."',
                                                             '" . $user_data['no_lim_pro_vehiculo_datos'] ."','" . $user_data['no_fec_mat_vehiculo_datos'] ."','" . $user_data['no_fec_exp_lic_vehiculo_datos'] ."',
                                                             '" . $user_data['no_fec_ven_vehiculo_datos'] ."','" . $user_data['no_tt_vehiculo_datos'] ."','" . $user_data['no_lat_vehiculo_datos'] ."',
                                                             " . $user_data['cod_vehiculo'] .")";
                //print $this->query;exit();
                $this->execute_single_query();
                for ($i = 0; $i < count($user_data['no_cod_documento_documento']); $i++) {
                    if (!empty($user_data['no_cod_documento_documento'])) {
                        $this->query = " INSERT INTO hue_vehiculo_documentos 
                                                         (cod_tip_documento,num_vehiculo_documento,fec_vehiculo_documento,fec_ven_vehiculo_documento,cod_vehiculo)  
                                                  VALUES ('" . $user_data['no_cod_documento_documento'][$i] . "','" . $user_data['no_num_vehiculo_documento'][$i] . "',
                                                          '" . $user_data['no_fec_vehiculo_documento'][$i] . "','" . $user_data['no_fec_ven_vehiculo_documento'][$i] . "',
                                                          '" . $user_data['cod_vehiculo'] . "')";
                        $this->execute_single_query();
                    }
                }
            break;
            case 'nuevaClienteVehiculo':
                $this->query="call pbNuevoAlquiler(".$user_data["cod_cliente"].",".$user_data["cod_vehiculo"].",1)";
                $this->execute_single_query();
                $err_img = $err_img . " El vehiculo a sigo asociado al cliente satisfactoriamente";
            break;
        }
        $this->msj = "La transaccion se registro correctamente. " . $err_img;
    }

    # Modificar un registro

    public function editRegistro($user_data = array()) {
        $this->query = "";
        $tblCol = '';
        $err_img = '';
        $colExt = '';
        $colName = '';
        $valExt = '';
        $valName = '';

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
            $err_img = $err_img . uploadImg($user_data, HUE_DIR_ADJ, $valName, $colName);
        }

        if (!empty($valName)) {
            $colExt = $colExt . "," . $colName . "";
            $colExt = $colExt . $nomTabla . "='" . $valName . "'";
        }
        //acciones antes de editar el registro
        switch ($user_data['no_nom_tabla']) {
            case 'nuevaVehiculo':
                $this->query="DELETE 
                                FROM hue_vehiculo_datos
                               WHERE cod_vehiculo=" .$user_data["cod_vehiculo"]. "";
                $this->execute_single_query();
                $this->query="DELETE 
                                FROM hue_vehiculo_documentos
                               WHERE cod_vehiculo=" .$user_data["cod_vehiculo"]. "";
                $this->execute_single_query();
                $this->query="INSERT INTO hue_vehiculo_datos(res_mov_vehiculo_datos,pot_vehiculo_datos,num_pue_vehiculo_datos,lim_pro_vehiculo_datos,
                                                            fec_mat_vehiculo_datos,fec_exp_lic_vehiculo_datos,fec_ven_vehiculo_datos,tt_vehiculo_datos,
                                                            lat_vehiculo_datos,cod_vehiculo)
                                                     VALUES ('" . $user_data['no_res_mov_vehiculo_datos'] ."','" . $user_data['no_pot_vehiculo_datos'] ."','" . $user_data['no_num_pue_vehiculo_datos'] ."',
                                                             '" . $user_data['no_lim_pro_vehiculo_datos'] ."','" . $user_data['no_fec_mat_vehiculo_datos'] ."','" . $user_data['no_fec_exp_lic_vehiculo_datos'] ."',
                                                             '" . $user_data['no_fec_ven_vehiculo_datos'] ."','" . $user_data['no_tt_vehiculo_datos'] ."','" . $user_data['no_lat_vehiculo_datos'] ."',
                                                             " . $user_data['cod_vehiculo'] .")";
                $this->execute_single_query();
                for ($i = 0; $i < count($user_data['no_cod_documento_documento']); $i++) {
                    if (!empty($user_data['no_cod_documento_documento'])) {
                        $this->query = " INSERT INTO hue_vehiculo_documentos 
                                                         (cod_tip_documento,num_vehiculo_documento,fec_vehiculo_documento,fec_ven_vehiculo_documento,cod_vehiculo)  
                                                  VALUES ('" . $user_data['no_cod_documento_documento'][$i] . "','" . $user_data['no_num_vehiculo_documento'][$i] . "',
                                                          '" . $user_data['no_fec_vehiculo_documento'][$i] . "','" . $user_data['no_fec_ven_vehiculo_documento'][$i] . "',
                                                          '" . $user_data['cod_vehiculo'] . "')";
                        $this->execute_single_query();
                    }
                }
            break;
            case 'nuevaClienteVehiculo':
                $this->query="call pbNuevoAlquiler(".$user_data["cod_cliente"].",".$user_data["cod_vehiculo"].",2)";
                $this->execute_single_query();
                $err_img = $err_img . " El vehiculo a sigo asociado al cliente satisfactoriamente";
            break;
        }

        $this->query = " UPDATE " . $user_data['no_esq_tabla'] . "
                            SET " . substr($tblCol, 0, (strlen($tblCol) - 1)) . $colExt . "   
                          WHERE " . 'cod_' . $nomTabla . "=" . $user_data['cod_' . $nomTabla] . "";

        //print $this->query;exit();
        $this->execute_single_query();
        $this->msj = "La transaccion se edito correctamente. " . $err_img;
    }

    #trae la siguiente secuencia de la tabla	

    public function setSigSecuencia($nomTbl) {
        $this->query = "SELECT IF(MAX(cod_" . str_replace('hue_', '', $nomTbl) . " IS NOT NULL),MAX(cod_" . str_replace('hue_', '', $nomTbl) . " + 1),1) as codSec 
                       FROM " . $nomTbl . " ";
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
