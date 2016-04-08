<?php

class ModeloSined extends DBAbstractModel {
    private $_modulo=8;
    protected $cod;

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
        ModeloSined::get_datos('fbTraeEmpresa('. Session::get('cod') .') as result');
        $cadEmp = $this->_data[0]['result'];
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
        $this->query = " SELECT fbArmaNotificacionTarea('$cadEmp',1) AS des_notificacion FROM DUAL";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_notificacion[$pro] = $va;
            }
        }

        #traemos las notificaciones
        $this->rows  = "";
        $this->query = "";
        $this->query = " SELECT fbArmaNotificacionTarea('$cadEmp',2) AS des_tarea FROM DUAL";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_tarea[$pro] = $va;
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
					   ORDER BY t1.cod_indice";
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
        #traemos los shortcut
        $this->rows  = "";
        $this->query = "";  
        $this->query = " SELECT fbArmaSubMenuHeaderShortCut($usuario) as menu_shortcut";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {                
                $this->_menuShorcut[] = $va;
            }
        }else{$this->_menuShorcut[0] = array('menu_shortcut'=>"");}
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
        #traemos el formulario modal
        $this->rows  = "";
        $this->query = "";
        $this->query = " SELECT fbArmaFormularioModal($form,$usuario) as modal; ";
        //print $this->query;
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {                
                $this->_formulario_modal[] = $va;
                if($this->_formulario_modal[0]['modal'] == ''){              
                    $mensjFrm             = '';
                    $this->_formulario_modal[0] = array('modal'=>$mensjFrm);
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
        ModeloSined::get_boton($metodo,$usuario,$form,$cMet);
    }
    #traemos la tabla
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
        ModeloSined::get_boton($metodo, $usuario, $form,$cMet);
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
        //print $this->query;
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

        if (isset($user_data['cod_' . str_replace('sys_','',str_replace('sined_', '', $user_data['no_esq_tabla']))]) and empty($user_data['cod_' . str_replace('sys_','',str_replace('sined_', '', $user_data['no_esq_tabla']))])) {
            $user_data['cod_' . str_replace('sys_','',str_replace('sined_', '', $user_data['no_esq_tabla']))] = ModeloSined::setSigSecuencia($user_data['no_esq_tabla']);
        }
        // se llena el valor para los campos que lo requiran el el array
        if (isset($user_data['cod_usuario']) and empty($user_data['cod_usuario'])){$user_data['cod_usuario'] = Session::get('cod');}
        /* obtengo el nombre de la tabla para futuras validaciones */
        $pos = strpos($user_data['no_esq_tabla'], '_');
        $nomTabla = substr($user_data['no_esq_tabla'], $pos + 1, strlen($user_data['no_esq_tabla']));
        fbFormateaPost($user_data, $tblCol, $tblVal);
        
        //Procesamos los archivos adjuntos que vienen con el post
        $icount = contarCoincidencias($user_data,"tmp_img");
        for($f=0;$f<$icount;$f++):
            $t=$f>0 ? $f : '';
            if (isset($user_data["no_tmp_img".$t]) Or !empty($user_data["no_tmp_img".$t])):
                $err_img = $err_img . uploadImg($user_data, SINED_DIR_ADJ, $valName, $colName,$t);
            endif;
            //completamos las columnas extras para la transaccion
            if (!empty($valName)) {
                $valExt .= ",'" . $valName . "'";
                $colExt .= "," . $colName . "";
                $colExt .= $nomTabla;
            }
        endfor;

        $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "
                                     (" . substr($tblCol, 0, (strlen($tblCol) - 1)) . "" . $colExt . ")  
                              VALUES (" . substr($tblVal, 0, (strlen($tblVal) - 1)) . "" . $valExt . ")";
        //print $this->query;exit;
        $this->execute_single_query();
        switch ($user_data['no_nom_tabla']) {
            case 'ConfiguracionGeneralSined':
                if ($user_data['cod_estado'] == 'AAA') {
                    $this->query = " call pbActualizaConfig(3," . $user_data['cod_config'] . ")";
                    $this->execute_single_query();
                    $err_img = $err_img . " La configuracion ha sido definida como predeterminada";
                }
            break;
            case 'NuevaPreinscripciones':
                // inserto las capacidades excepcionales
                if(isset($user_data['no_cod_capacidades'])){
                    for ($i = 0; $i < count($user_data['no_cod_capacidades']); $i++) {
                        if (!empty($user_data['no_cod_capacidades'][$i])) {
                            $this->query = " INSERT INTO sined_alumno_discapacidades_capacidades 
                                                             (cod_alumno,cod_capacidades,ind_capacidades)  
                                                      VALUES ('" . $user_data["cod_alumno"] . "','" . $user_data['no_cod_capacidades'][$i] . "', 1)";
                            $this->execute_single_query();
                        }
                    }
                }
                // inserto las discapacidades excepcionales
                if(isset($user_data['no_cod_discapacidades'])){
                    for ($i = 0; $i < count($user_data['no_cod_discapacidades']); $i++) {
                        if (!empty($user_data['no_cod_discapacidades'][$i])) {
                            $this->query = " INSERT INTO sined_alumno_discapacidades_capacidades 
                                                             (cod_alumno,cod_discapacidades,ind_discapacidades)  
                                                      VALUES ('" . $user_data["cod_alumno"] . "','" . $user_data['no_cod_discapacidades'][$i] . "', 1)";
                            $this->execute_single_query();
                        }
                    }
                }
                // relacionamos los familiares al alumno
                if(isset($user_data['no_cod_familiares'])){
                    for ($i = 0; $i < count($user_data['no_cod_familiares']); $i++) {
                        if (!empty($user_data['no_cod_parentesco'][$i])) {
                            $this->query = " INSERT INTO sined_alumno_familiares 
                                                             (cod_alumno,cod_familiares,ind_acudiente,cod_parentesco)  
                                                      VALUES ('" . $user_data["cod_alumno"] . "','" . $user_data["no_cod_familiares"][$i] . "',
                                                              '" . $user_data['no_ind_acudiente'][$i] . "','" . $user_data['no_cod_parentesco'][$i] . "')";
                            $this->execute_single_query();
                        }
                    }
                }
            break;
            case 'NuevaDocentes':
                for($i=0;$i<count($user_data['no_cod_menu']);$i++){
                    $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_menu 
                                                 (cod_usuario,cod_menu)  
                                          VALUES ('" .$user_data['cod_' . $nomTabla] . "','" .$user_data['no_cod_menu'][$i]. "')";
                    $this->execute_single_query();
                }
                for($i=0;$i<count($user_data['no_menu_sub']);$i++){
                    $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_menu_sub 
                                                 (cod_usuario,cod_menu_sub)  
                                          VALUES ('" .$user_data['cod_' . $nomTabla] . "','" .$user_data['no_menu_sub'][$i]. "')";
                    $this->execute_single_query();
                    $this->query = " call pbAsignaSubMenu(" . $user_data['cod_' . $nomTabla] . "," . $user_data['no_menu_sub'][$i] . ")";
                    $this->execute_single_query();
                } 
                for($i=0;$i<count($user_data['no_cod_empresa']);$i++){
                    $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_empresa 
                                                 (cod_usuario,cod_empresa)  
                                          VALUES ('" .$user_data['cod_' . $nomTabla] . "','" .$user_data['no_cod_empresa'][$i]. "')";
                    $this->execute_single_query();
                }

                $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_perfil 
                                             (cod_usuario,cod_perfil)  
                                      VALUES ('" .$user_data['cod_' . $nomTabla] . "','" .$user_data['no_cod_perfil']. "')";
                $this->execute_single_query();
                $err_img = $err_img . " La configuracion del sistema a sido asinada al usuario: " . $user_data['nom_' . $nomTabla] ;
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
        
        //Procesamos los archivos adjuntos que vienen con el post   
        $icount = contarCoincidencias($user_data,"tmp_img");
        for($f=0;$f<$icount;$f++):
            $t=$f>0 ? $f : '';
            if (isset($user_data["no_tmp_img".$t]) Or !empty($user_data["no_tmp_img".$t])):
                $err_img = $err_img . uploadImg($user_data, SINED_DIR_ADJ, $valName, $colName,$t);
            endif;
            //completamos las columnas extras para la transaccion
            if (!empty($valName)) {
            $colExt .= "," . $colName . "";
            $colExt .= $nomTabla . "='" . $valName . "'";
        }
        endfor;
        
        //acciones antes de editar el registro
        switch ($user_data['no_nom_tabla']) {
            case '':

            break;
        }
        
        $this->query = " UPDATE " . $user_data['no_esq_tabla'] . "
                            SET " . substr($tblCol, 0, (strlen($tblCol) - 1)) . $colExt . "   
                          WHERE " . 'cod_' . $nomTabla . "=" . $user_data['cod_' . $nomTabla] . "";

        //print $this->query;exit();
        $this->execute_single_query();
        //acciones antes de editar el registro
        switch ($user_data['no_nom_tabla']) {
            case 'ConfiguracionGeneralSined':
                if ($user_data['cod_estado'] == 'AAA') {
                    $this->query = " call pbActualizaConfig(3," . $user_data['cod_config'] . ")";
                    $this->execute_single_query();
                    $err_img = $err_img . " La configuracion ha sido definida como predeterminada";
                }
            break;
            case 'NuevaDocentes':
                $this->query = "DELETE 
                                  FROM " . $user_data['no_esq_tabla'] . "_menu  
                                 WHERE cod_usuario = '" .$user_data['cod_' . $nomTabla] . "'";
                $this->execute_single_query();
                $this->query = "DELETE 
                                  FROM " . $user_data['no_esq_tabla'] . "_menu_sub  
                                 WHERE cod_usuario = '" .$user_data['cod_' . $nomTabla] . "'";
                $this->execute_single_query();
                $this->query = "DELETE 
                                  FROM " . $user_data['no_esq_tabla'] . "_empresa  
                                 WHERE cod_usuario = '" .$user_data['cod_' . $nomTabla] . "'";
                $this->execute_single_query();
                $this->query = "DELETE 
                                  FROM " . $user_data['no_esq_tabla'] . "_perfil  
                                 WHERE cod_usuario = '" .$user_data['cod_' . $nomTabla] . "'";
                $this->execute_single_query();
                for($i=0;$i<count($user_data['no_cod_menu']);$i++){
                    $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_menu 
                                                 (cod_usuario,cod_menu)  
                                          VALUES ('" .$user_data['cod_' . $nomTabla] . "','" .$user_data['no_cod_menu'][$i]. "')";
                    $this->execute_single_query();
                }
                for($i=0;$i<count($user_data['no_menu_sub']);$i++){
                    $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_menu_sub 
                                                 (cod_usuario,cod_menu_sub)  
                                          VALUES ('" .$user_data['cod_' . $nomTabla] . "','" .$user_data['no_menu_sub'][$i]. "')";
                    $this->execute_single_query();
                    $this->query = " call pbAsignaSubMenu(" . $user_data['cod_' . $nomTabla] . "," . $user_data['no_menu_sub'][$i] . ")";
                    $this->execute_single_query();
                } 
                for($i=0;$i<count($user_data['no_cod_empresa']);$i++){
                    $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_empresa 
                                                 (cod_usuario,cod_empresa)  
                                          VALUES ('" .$user_data['cod_' . $nomTabla] . "','" .$user_data['no_cod_empresa'][$i]. "')";
                    $this->execute_single_query();
                }

                $this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_perfil 
                                             (cod_usuario,cod_perfil)  
                                      VALUES ('" .$user_data['cod_' . $nomTabla] . "','" .$user_data['no_cod_perfil']. "')";
                $this->execute_single_query();
                
                $err_img = $err_img . " La configuracion del sistema a sido asinada al usuario: " . $user_data['nom_' . $nomTabla] ;
            break;
            case 'NuevaPreinscripciones':case 'NuevaMatriculas':
                $this->query = "DELETE 
                                  FROM sined_alumno_discapacidades_capacidades 
                                 WHERE cod_alumno = '" .$user_data['cod_alumno'] . "'";
                $this->execute_single_query();
                $this->query = "DELETE 
                                  FROM sined_alumno_familiares
                                 WHERE cod_alumno = '" .$user_data['cod_alumno'] . "'";
                $this->execute_single_query();
                // inserto las capacidades excepcionales
                if(isset($user_data['no_cod_capacidades'])){
                    for ($i = 0; $i < count($user_data['no_cod_capacidades']); $i++) {
                        if (!empty($user_data['no_cod_capacidades'][$i])) {
                            $this->query = " INSERT INTO sined_alumno_discapacidades_capacidades 
                                                             (cod_alumno,cod_capacidades,ind_capacidades)  
                                                      VALUES ('" . $user_data["cod_alumno"] . "','" . $user_data['no_cod_capacidades'][$i] . "', 1)";
                            $this->execute_single_query();
                        }
                    }
                }
                // inserto las discapacidades excepcionales
                if(isset($user_data['no_cod_discapacidades'])){
                    for ($i = 0; $i < count($user_data['no_cod_discapacidades']); $i++) {
                        if (!empty($user_data['no_cod_discapacidades'][$i])) {
                            $this->query = " INSERT INTO sined_alumno_discapacidades_capacidades 
                                                             (cod_alumno,cod_discapacidades,ind_discapacidades)  
                                                      VALUES ('" . $user_data["cod_alumno"] . "','" . $user_data['no_cod_discapacidades'][$i] . "', 1)";
                            $this->execute_single_query();
                        }
                    }
                }
                // relacionamos los familiares al alumno
                if(isset($user_data['no_cod_familiares'])){
                    for ($i = 0; $i < count($user_data['no_cod_familiares']); $i++) {
                        if (!empty($user_data['no_cod_parentesco'][$i])) {
                            $this->query = " INSERT INTO sined_alumno_familiares 
                                                             (cod_alumno,cod_familiares,ind_acudiente,cod_parentesco)  
                                                      VALUES ('" . $user_data["cod_alumno"] . "','" . $user_data["no_cod_familiares"][$i] . "',
                                                              '" . $user_data['no_ind_acudiente'][$i] . "','" . $user_data['no_cod_parentesco'][$i] . "')";
                            $this->execute_single_query();
                        }
                    }
                }
            break;
        }
        $this->msj = "La transaccion se edito correctamente. " . $err_img;
    }

    #trae la siguiente secuencia de la tabla	

    public function setSigSecuencia($nomTbl) {
        $this->query = "SELECT IF(MAX(cod_" . str_replace('sys_','',str_replace('sined_', '', $nomTbl)) . " IS NOT NULL),MAX(cod_" . str_replace('sys_','',str_replace('sined_', '', $nomTbl)) . " + 1),1) as codSec 
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
