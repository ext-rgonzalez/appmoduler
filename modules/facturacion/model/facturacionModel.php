<?php

class ModeloFacturacion extends DBAbstractModel {
    private $_modulo=2;
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
        ModeloFacturacion::get_boton($metodo,$usuario,$form,$cMet);
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
        ModeloFacturacion::get_boton($metodo, $usuario, $form,$cMet);
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
        //print $this->query;
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
        $tblVal = '';
        $err_img = '';
        $colExt = '';
        $colName = '';
        $valExt = '';
        $valName = '';

        if (isset($user_data['cod_' . str_replace('fa_', '', $user_data['no_esq_tabla'])]) and empty($user_data['cod_' . str_replace('fa_', '', $user_data['no_esq_tabla'])])) {
            $user_data['cod_' . str_replace('fa_', '', $user_data['no_esq_tabla'])] = ModeloFacturacion::setSigSecuencia($user_data['no_esq_tabla']);
        }
        // se llena el valor para los campos que lo requiran el el array
        $user_data["no_cod_valor"]=$user_data['cod_' . str_replace('fa_', '', $user_data['no_esq_tabla'])];
        if (isset($user_data['cod_usuario']) and empty($user_data['cod_usuario'])) {
            $user_data['cod_usuario'] = Session::get('cod');
        }
        /* obtengo el nombre de la tabla para futuras validaciones */
        $pos = strpos($user_data['no_esq_tabla'], '_');
        $nomTabla = substr($user_data['no_esq_tabla'], $pos + 1, strlen($user_data['no_esq_tabla']));
        fbFormateaPost($user_data, $tblCol, $tblVal);
        
        //Procesamos los archivos adjuntos que vienen con el post
        $icount = contarCoincidencias($user_data,"tmp_img");
        for($f=0;$f<$icount;$f++):
            $t=$f>0 ? $f : '';
            if (isset($user_data["no_tmp_img".$t]) Or !empty($user_data["no_tmp_img".$t])):
                $err_img = $err_img . uploadImg($user_data, FA_DIR_ADJ, $valName, $colName,$t);
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
        //print $this->query;exit();
        $this->execute_single_query();
        switch ($user_data['no_nom_tabla']) {
            case 'nuevaNumeracion':
                $tipo = isset($user_data["ind_factura"]) ? 0 : 1;
                if ($user_data['ind_preferida_numeracion'] == 1) {
                    $this->query = " call pbActualizaNumeracion(" . $user_data['cod_numeracion'] . "," . $user_data['cod_empresa'] . ",".$tipo.")";
                    $this->execute_single_query();
                    $err_img = $err_img . " La Numeracion ha sido definida como preferida";
                }
            break;
            case 'nuevaFacConfig':
                if ($user_data['cod_estado'] == 'AAA') {
                    $this->query = " call pbActualizaConfig(0," . $user_data['cod_config'] . ")";
                    $this->execute_single_query();
                    $err_img = $err_img . " La configuracion ha sido definida como predeterminada";
                }
            break;
            case 'nuevaFactura':case 'nuevaFacturacion':
                for ($i = 0; $i < count($user_data['no_cod_item']); $i++) {
                    if (!empty($user_data['no_cod_item'][$i])) {
                        $this->query = " INSERT INTO fa_detalle 
                                                         (cod_item,can_detalle,imp_detalle,cod_impuesto,imp_impuesto,cod_descuento,imp_descuento,cod_factura)  
                                                  VALUES ('" . $user_data['no_cod_item'][$i] . "','" . $user_data['no_can_detalle'][$i] . "', '" . $user_data["no_imp_detalle"][$i] . "',
                                                          '" . $user_data['no_cod_impuesto'][$i] . "', '" . $user_data["no_imp_impuesto"][$i] . "', '" . $user_data['no_cod_descuento'][$i] . "',
                                                          '" . $user_data["no_imp_descuento"][$i] . "', '" . $user_data["cod_factura"] . "')";
                        $this->execute_single_query();
                    }
                }
                $servicio = "";
                $indAsigna = 0;
                if (empty($user_data["no_cod_usuario"])) {
                    $user_data["no_cod_usuario"] = Session::get('cod');
                    $indAsigna = 1;
                }
                if (isset($user_data["ind_recurrente_factura"])) {
                    $servicio = "Tiquet para seguimiento de cotizacion Recurrente";
                } elseif (isset($user_data["ind_cotizacion"])) {
                    $servicio = "Tiquet para seguimiento de Cotizacion";
                }
                $secuencia = ModeloFacturacion::setSigSecuencia($user_data['no_esq_tabla']);
                $this->query = "call pbNuevoServicio(" . $secuencia . "," . $user_data["no_cod_usuario"] . "," . $user_data["cod_usuario"] . "," . $user_data["cod_cliente"] . ",'" . $servicio . "'," . $indAsigna . ",1,'SAA')";
                //  print $this->query;exit();
                $this->execute_single_query();
            break;
            case 'nuevaCliente':
                for ($i = 0; $i < count($user_data['no_nom_cliente_asociado']); $i++) {
                    if (!empty($user_data['no_nom_cliente_asociado'])) {
                        $this->query = " INSERT INTO fa_cliente_asociado 
                                                         (nom_cliente_asociado,email_cliente_asociado,tel_cliente_asociado,cel_cliente_asociado,cod_estado,cod_usuario,cod_cliente)  
                                                  VALUES ('" . $user_data['no_nom_cliente_asociado'][$i] . "','" . $user_data['no_email_cliente_asociado'][$i] . "',
                                                          '" . $user_data['no_tel_cliente_asociado'][$i] . "','" . $user_data['no_cel_cliente_asociado'][$i] . "',
                                                          'AAA','" . Session::get('cod') . "','" . $user_data['cod_cliente'] . "')";
                        $this->execute_single_query();
                    }
                }
            break;
            case 'nuevaPagosRecibidos':
                $_impuesto  =0;
                $_retencion=0;
                isset($user_data['no_imp_categoria'][3]) ? $_impuesto=$user_data['no_imp_categoria'][3] : $_impuesto=0;
                isset($user_data['no_imp_categoria'][2]) ? $_retencion=$user_data['no_imp_categoria'][2] : $_retencion=0;
                for ($i = 0; $i < count($user_data['no_cod_factura']); $i++) {
                    if (!empty($user_data['no_cod_factura'][0]) and $user_data['no_cod_factura'][0]<>0) {
                        $this->query = " INSERT INTO fa_detpago
                                                         (cod_factura,imp_detpago,cod_retencion,imp_retencion,cod_pago)  
                                                  VALUES ('" . $user_data['no_cod_factura'][$i] . "','" . $user_data['no_imp_detpago_fac'][$i] . "',
                                                          '" . $user_data['no_cod_retencion'][$i] . "','". $_retencion ."'," . $user_data['cod_pago'] . ")";
                        $this->execute_single_query();
                    }
                }
                for ($i = 0; $i < count($user_data['no_cod_categoria_form']); $i++) {
                    if(!empty($user_data['no_cod_categoria_form'][0]) and $user_data['no_cod_categoria_form'][0]<>0) {
                        $this->query = " INSERT INTO fa_detpago
                                                     (cod_categoria,imp_detpago,cod_impuesto,imp_impuesto,cod_retencion,imp_retencion,cod_pago)  
                                              VALUES ('" . $user_data['no_cod_categoria_form'][$i] . "','" . $user_data['no_imp_detpago_cat'][$i] . "',
                                                      '" . $user_data['no_cod_impuesto'][$i] . "','". $_impuesto ."',
                                                      '" . $user_data['no_cod_retencion'][$i+1] . "','". $_retencion ."',
                                                      " . $user_data['cod_pago'] . ")";
                        $this->execute_single_query();
                    }
                    $err_img = $err_img . " el comprobante ha sido generado correctamente";
                }                
            break;
            case 'nuevaPagosRealizado':
                $_impuesto  =0;
                $_retencion=0;
                isset($user_data['no_imp_categoria'][3]) ? $_impuesto=$user_data['no_imp_categoria'][3] : $_impuesto=0;
                isset($user_data['no_imp_categoria'][2]) ? $_retencion=$user_data['no_imp_categoria'][2] : $_retencion=0;
                for ($i = 0; $i < count($user_data['no_cod_categoria_form']); $i++) {
                    if(!empty($user_data['no_cod_categoria_form'][0]) and $user_data['no_cod_categoria_form'][0]<>0) {
                        $this->query = " INSERT INTO fa_detpago
                                                     (cod_categoria,imp_detpago,cod_impuesto,imp_impuesto,cod_retencion,imp_retencion,cod_pago)  
                                              VALUES ('" . $user_data['no_cod_categoria_form'][$i] . "','" . $user_data['no_imp_detpago_cat'][$i] . "',
                                                      '" . $user_data['no_cod_impuesto'][$i] . "','". $_impuesto ."',
                                                      '" . $user_data['no_cod_retencion'][$i] . "','". $_retencion ."',
                                                      " . $user_data['cod_pago'] . ")";
                        $this->execute_single_query();
                    }
                    $err_img = $err_img . " el comprobante ha sido generado correctamente";
                }                
            break;
            case 'nuevaOrden':
                $this->query = "call pbConvierteCotizacion(" . $user_data["cod_factura"] . "," . $user_data["cod_usuario"] . "," . $user_data["cod_empresa"] . ",0)";
                $this->execute_single_query();
            break;
            case 'nuevaInventario':
                $this->query = "call pbTratarInventario(".$user_data["cod_inventario"].",".$user_data["entrada_inventario"].",
                               ".$user_data["imp_uni_inventario"].",0,".Session::get('cod').",".$user_data["cod_empresa"].",0,0)";
                $this->execute_single_query();
                // Insertamos la Cabecera del movimiento contable
                $fecMov = date("Y.m.d H:i:s");
                $obs    = 'Asiento contable para compra de mercaderias e inventario. ';
                $this->query = "INSERT INTO con_cab_mov_contable
	                                    (cod_cab_mov_contable,obs_cab_mov_contable,fec_cab_mov_contable,cod_empresa,cod_usuario)
	                             VALUES (".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")";
                
                $this->execute_single_query();
                for($g=0;$g<count($user_data["no_cod_categoria"]);$g++):
                    $this->query = "call pbTransaccionContable(".$user_data["no_cod_categoria"][$g].",".$user_data["no_imp_categoria"][$g].",".$user_data["no_cod_naturaleza"][$g].",".$user_data["no_cod_proceso_interno"].",".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")";                    
                    $this->execute_single_query();
                endfor;
            break;
            case 'nuevaAjusteInventario':
                if(isset($user_data["ind_incremento"]) And !isset($user_data["ind_incremento"])==1):
                    $this->query = "call pbTratarInventario(".$user_data["cod_inventario"].",".$user_data["can_inventario_ajuste"].",
                                                            ".$user_data["imp_inventario_ajuste"].",1,".Session::get('cod').",".$user_data["cod_empresa"].",1,0)";
                    $this->execute_single_query();
                elseif(isset($user_data["ind_decremento"]) And $user_data["ind_decremento"]==1):
                    $this->query = "call pbTratarInventario(".$user_data["cod_inventario"].",".$user_data["can_inventario_ajuste"].",
                                                            ".$user_data["imp_inventario_ajuste"].",2,".Session::get('cod').",".$user_data["cod_empresa"].",1,0)";
                    $this->execute_single_query();
                endif;
                // Insertamos la Cabecera del movimiento contable
                $fecMov = date("Y.m.d H:i:s");
                $obs    = 'Asiento contable para compra de mercaderias e inventario. ';
                $this->query = "INSERT INTO con_cab_mov_contable
	                                    (cod_cab_mov_contable,obs_cab_mov_contable,fec_cab_mov_contable,cod_empresa,cod_usuario)
	                             VALUES (".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")";
                
                $this->execute_single_query();
                for($g=0;$g<count($user_data["no_cod_categoria"]);$g++):
                    $this->query = "call pbTransaccionContable(".$user_data["no_cod_categoria"][$g].",".$user_data["no_imp_categoria"][$g].",".$user_data["no_cod_naturaleza"][$g].",".$user_data["no_cod_proceso_interno"].",".$user_data["no_cod_cab_contable"].",'".$obs."','".$fecMov."',".$user_data["cod_empresa"].",".Session::get('cod').")";                    
                    $this->execute_single_query();
                endfor;
            break;
            case 'nuevaImpuesto': case 'nuevaDescuento': case 'nuevaMetodoPago': case 'nuevaRetencion':
                for ($i = 0; $i < count($user_data['no_cod_proceso']); $i++) {
                    if (!empty($user_data['no_cod_proceso'][$i])) {
                        $this->query = " INSERT INTO con_proceso_transaccion 
                                                     (cod_proceso,cod_categoria," .$user_data["no_cod_clave"]. ",cod_naturaleza,cod_empresa,cod_usuario,fec_proceso_transaccion)  
                                              VALUES ('" . $user_data['no_cod_proceso'][$i] . "','" . $user_data['no_cod_categoria'][$i] . "','" .$user_data["no_cod_valor"]. "',
                                                      '" . $user_data['no_cod_naturaleza'][$i] . "','" . $user_data['no_cod_empresa'][$i] . "',
                                                      '" . Session::get('cod') . "',now())";
                        $this->execute_single_query();
                    }
                }
                $err_img .= " Las configuracion de cuentas contables se establecion correctamente. ";
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

        //Procesamos los archivos adjuntos que vienen con el post
        $icount = contarCoincidencias($user_data,"tmp_img");
        for($f=0;$f<$icount;$f++):
            $t=$f>0 ? $f : '';
            if (isset($user_data["no_tmp_img".$t]) Or !empty($user_data["no_tmp_img".$t])):
                $err_img = $err_img . uploadImg($user_data, FA_DIR_ADJ, $valName, $colName,$t);
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
            case 'nuevaNumeracion':
                $tipo = isset($user_data["ind_factura"]) ? 0 : 1;
                if ($user_data['ind_preferida_numeracion'] == 1) {
                    $this->query = " call pbActualizaNumeracion(" . $user_data['cod_numeracion'] . "," . $user_data['cod_empresa'] . ",".$tipo .")";
                    $this->execute_single_query();
                    $err_img = $err_img . " La Numeracion ha sido definida como preferida";
                }
                break;
            case 'nuevaFacConfig':
                if ($user_data['cod_estado'] == 'AAA') {
                    $this->query = " call pbActualizaConfig(0," . $user_data['cod_config'] . ")";
                    $this->execute_single_query();
                    $err_img = $err_img . " La configuracion ha sido definida como predeterminada";
                }
            break;
            case 'nuevaFactura':
                $this->query="DELETE 
                                FROM fa_detalle
                               WHERE cod_factura=" .$user_data["cod_factura"]. "";
                $this->execute_single_query();
                for ($i = 0; $i < count($user_data['no_cod_item']); $i++) {
                    if (!empty($user_data['no_cod_item'][$i])) {
                         $this->query = " INSERT INTO fa_detalle 
                                                         (cod_item,can_detalle,imp_detalle,cod_impuesto,imp_impuesto,cod_descuento,imp_descuento,cod_factura)  
                                                  VALUES ('" . $user_data['no_cod_item'][$i] . "','" . $user_data['no_can_detalle'][$i] . "', '" . $user_data["no_imp_detalle"][$i] . "',
                                                          '" . $user_data['no_cod_impuesto'][$i] . "', '" . $user_data["no_imp_impuesto"][$i] . "', '" . $user_data['no_cod_descuento'][$i] . "',
                                                          '" . $user_data["no_imp_descuento"][$i] . "', '" . $user_data["cod_factura"] . "')";
                        $this->execute_single_query();
                    }
                }
                if($user_data["num_factura"]<>""){
                        $this->query = "call pbConvierteCotizacion(" . $user_data["cod_factura"] . "," . $user_data["cod_usuario"] . "," . $user_data["cod_empresa"] . ",1)";
                        $this->execute_single_query();
                }
            break;
            case 'nuevaCliente':
                $this->query="DELETE 
                                FROM fa_cliente_asociado
                               WHERE cod_cliente=" .$user_data["cod_cliente"]. "";
                $this->execute_single_query();
                for ($i = 0; $i < count($user_data['no_nom_cliente_asociado']); $i++) {
                    if (!empty($user_data['no_nom_cliente_asociado'][$i])) {
                        $this->query = " INSERT INTO fa_cliente_asociado 
                                                         (nom_cliente_asociado,email_cliente_asociado,tel_cliente_asociado,cel_cliente_asociado,cod_estado,cod_usuario,cod_cliente)  
                                                  VALUES ('" . $user_data['no_nom_cliente_asociado'][$i] . "','" . $user_data['no_email_cliente_asociado'][$i] . "',
                                                          '" . $user_data['no_tel_cliente_asociado'][$i] . "','" . $user_data['no_cel_cliente_asociado'][$i] . "',
                                                          'AAA','" . Session::get('cod') . "','" . $user_data['cod_cliente'] . "')";
                        $this->execute_single_query();
                    }
                }
            break;
            case 'nuevaOrden':
                $this->query = "call pbConvierteCotizacion(" . $user_data["cod_factura"] . "," . $user_data["cod_usuario"] . "," . $user_data["cod_empresa"] . ",0)";
                $this->execute_single_query();
            break;
            case 'nuevaImpuesto':case 'nuevaDescuento': case 'nuevaMetodoPago': case 'nuevaRetencion':
                $this->query="DELETE 
                                FROM con_proceso_transaccion
                               WHERE " .$user_data["no_cod_clave"]. "=" .$user_data["no_cod_valor"]. "";
                $this->execute_single_query();
                for ($i = 0; $i < count($user_data['no_cod_proceso']); $i++) {
                    if (!empty($user_data['no_cod_proceso'][$i])) {
                        $this->query = " INSERT INTO con_proceso_transaccion 
                                                     (cod_proceso,cod_categoria," .$user_data["no_cod_clave"]. ",cod_naturaleza,cod_empresa,cod_usuario,fec_proceso_transaccion)  
                                              VALUES ('" . $user_data['no_cod_proceso'][$i] . "','" . $user_data['no_cod_categoria'][$i] . "','" .$user_data["no_cod_valor"]. "',
                                                      '" . $user_data['no_cod_naturaleza'][$i] . "','" . $user_data['no_cod_empresa'][$i] . "',
                                                      '" . Session::get('cod') . "',now())";
                        $this->execute_single_query();
                    }
                }
                $err_img .= " Las configuracion de cuentas contables se establecio correctamente. ";
            break;
            case 'nuevaPagosRecibidos':
                $this->query="DELETE 
                                FROM fa_detpago
                               WHERE cod_pago=" .$user_data["cod_pago"]. "";
                $this->execute_single_query();
                $_impuesto  =0;
                $_retencion=0;
                isset($user_data['no_imp_categoria'][3]) ? $_impuesto=$user_data['no_imp_categoria'][3] : $_impuesto=0;
                isset($user_data['no_imp_categoria'][2]) ? $_retencion=$user_data['no_imp_categoria'][2] : $_retencion=0;
                for ($i = 0; $i < count($user_data['no_cod_factura']); $i++) {
                    if (!empty($user_data['no_cod_factura'][0]) and $user_data['no_cod_factura'][0]<>0) {
                        $this->query = " INSERT INTO fa_detpago
                                                         (cod_factura,imp_detpago,cod_retencion,imp_retencion,cod_pago)  
                                                  VALUES ('" . $user_data['no_cod_factura'][$i] . "','" . $user_data['no_imp_detpago_fac'][$i] . "',
                                                          '" . $user_data['no_cod_retencion'][$i] . "','". $_retencion ."'," . $user_data['cod_pago'] . ")";
                        $this->execute_single_query();
                    }
                }
                for ($i = 0; $i < count($user_data['no_cod_categoria_form']); $i++) {
                    if(!empty($user_data['no_cod_categoria_form'][0]) and $user_data['no_cod_categoria_form'][0]<>0) {
                        $this->query = " INSERT INTO fa_detpago
                                                     (cod_categoria,imp_detpago,cod_impuesto,imp_impuesto,cod_retencion,imp_retencion,cod_pago)  
                                              VALUES ('" . $user_data['no_cod_categoria_form'][$i] . "','" . $user_data['no_imp_detpago_cat'][$i] . "',
                                                      '" . $user_data['no_cod_impuesto'][$i] . "','". $_impuesto ."',
                                                      '" . $user_data['no_cod_retencion'][$i+1] . "','". $_retencion ."',
                                                      " . $user_data['cod_pago'] . ")";
                        $this->execute_single_query();
                    }
                    $err_img = $err_img . " el comprobante ha sido modificado correctamente";
                }                
            break;
        }
        $this->msj = "La transaccion se edito correctamente. " . $err_img;
    }

    #trae la siguiente secuencia de la tabla	

    public function setSigSecuencia($nomTbl) {
        $this->query = "SELECT IF(MAX(cod_" . str_replace('fa_', '', $nomTbl) . " IS NOT NULL),MAX(cod_" . str_replace('fa_', '', $nomTbl) . " + 1),1) as codSec 
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
