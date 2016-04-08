<?php
# Importar modelo de abstracción de base de datos
class appModel extends DBAbstractModel {
    
    public    $_data            = array();
    public    $_session         = array();
    public    $_sessionVal      = array();
    public    $_empresa         = array();
    public    $_mensajes        = array();
    public    $_numMensajes     = array();
    public    $_menu            = array();
    public    $_notificacion    = array();
    public    $_numNotificacion = array();
    public    $_navegacion      = array();
    public    $_formulario      = array();
    public    $_boton           = array();
    public    $_frame           = array();
    public    $_input           = array();  
    public    $_tabla           = array();
    public    $_cTabla          = array();
    
    public function get_config_usuario($usuario){
        #traemos contrato, empresa, modulos del usuario
        $this->rows  = "";
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
                                               t1.cod_usuario     = '" .$usuario. "'";
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
                                 WHERE cod_estado      = 'NAA'	 AND   
                                               cod_usuario     = '" .$usuario. "'";
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
                                 WHERE t1.cod_estado      = 'MAA'	       AND
                                                   t1.de_cod_usuario  = t2.cod_usuario AND    
                                               t1.a_cod_usuario   = '" .$usuario. "'";
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
                                          WHERE t3.cod_menu      = t1.cod_menu and 
                                                    t3.cod_usuario   = '" .$usuario. "'  
                                   ORDER BY t1.cod_menu";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
                foreach ($this->rows as $pro=>$va) {				
                        $this->_menu[] = $va;
                }
        }else{$this->_menu[0] = array('menu'=>"");}	
    }
        
    public function get_form($metodo,$usuario,$form){
		#traemos el formulario
		$this->rows  = "";
		$this->query = "";
		$this->query = " SELECT fbArmaFormulario($form,$usuario) as formulario; ";
		$this->get_results_from_query();
		if(count($this->rows) >= 1){
			foreach ($this->rows as $pro=>$va) {				
				$this->_formulario[] = $va;
				if($this->_formulario[0]['formulario'] == ''){				
					$mensjFrm = getMenssage('danger','Ocurrio un error','No tiene permisos para acceder a esta opcion. ');
					$this->_formulario[0] = array('formulario'=>$mensjFrm);
				}
			}
		}	
		ModeloFacturacion::get_boton($metodo,$usuario,$form);
	}
	#traemos la tabla
	public function get_table($metodo,$usuario,$form,$vista,$condicion = Null){
		$this->rows  = "";
		$this->query = "";  
		$this->query = " SELECT fbArmaCabTabla('$vista') as tabla";
		$this->get_results_from_query();
		if(count($this->rows) >= 1){
			foreach ($this->rows as $pro=>$va) {			
				$this->_tabla[] = $va;
				if($this->_tabla[0]['tabla'] == ''){				
					$mensjFrm = getMenssage('danger','Ocurrio un error','No tiene permisos para acceder a esta opcion. ');
					$this->_tabla[0] = array('tabla'=>$mensjFrm);
				}
			}
		}	
		#traemos los campos
		$this->rows  = "";
		$this->query = "";  
		$this->query = " SELECT * from $vista "; if(!empty($condicion)){ $this->query .= " where " . $condicion; }
		print $this->query;
		$this->get_results_from_query();
		if(count($this->rows) >= 1){
			foreach ($this->rows as $pro=>$va){
				$this->_cTabla[] = $va;			
			}
		}
		ModeloFacturacion::get_boton($metodo,$usuario,$form);
	}
	
	public function get_boton($metodo,$usuario,$form){
		#traemos los botones para las acciones del formulario
		$this->rows  = "";
		$this->query = "";
		$this->query = " SELECT fbArmaBoton($form,$usuario) as boton; ";
		$this->get_results_from_query();
		if(count($this->rows) >= 1){
			foreach ($this->rows as $pro=>$va) {
				$this->_boton[] = $va;
				if($this->_boton[0]['boton'] == ''){
					#mensaje de error: warning, danger, success, info
					$mensjBto = getMenssage('danger','Ocurrio un error','No tiene permisos para realizar ninguna accion en este formulario. ');
					$this->_boton[0] = array('boton'=>$mensjBto);
				}
			}
		}
	}
	
	public function get_datos($usuario){
		#traemos los del usuario para editarlos
		$this->rows  = "";
		$this->query = "";
		$this->query = " SELECT * 
		                   FROM sys_usuario 
						  WHERE cod_usuario = '" .$usuario. "'";
		$this->get_results_from_query();
		if(count($this->rows) >= 1){
			foreach ($this->rows as $pro=>$va) {
				$this->_data[] = $va;
			}
		}
	}
	
	public function getRegistro($tabla, $campo = array(), $clave = array()){
		$this->rows  = "";
		$this->query = "";  
		$condicion   = "";
		for($x=0;$x<count($campo);$x++){
			$v = $campo[$x] . $clave[$x];
			$condicion = $condicion . $v; 
		}
		$this->query = " SELECT * 
						   FROM " . $tabla . " 
						  WHERE " . $condicion . " ";
						  #print $this->query;
		$this->get_results_from_query();
		if(count($this->rows) >= 1){return true;exit();}
		return false;
	}
	
	public function setRegistro($user_data =  array()){
		$this->query = "";
		$tblCol = '';
		$tblVal = ''; $err_img = '';		
		$colExt = ''; $colName = '';
		$valExt = ''; $valName = '';
		if(isset($user_data['cod_' . str_replace('sys_','',$user_data['no_esq_tabla'])])){
			$user_data['cod_' . str_replace('sys_','',$user_data['no_esq_tabla'])] = ModeloFacturacion::setSigSecuencia($user_data['no_esq_tabla']);
		}
		if (!isset($user_data['cod_usuario']) and !empty($user_data['cod_usuario'])){$user_data['cod_usuario'] = Session::get('cod');}
		foreach($user_data as $col=>$dat){
			if(strpos($col,'no_') === false){
				$tblCol = $tblCol . $col . ','; 
				$tblVal = $tblVal . "'" . $dat . "'" . ',';
			}	
		}
		
		if(isset($user_data["no_tmp_img"]) Or !empty($user_data["no_tmp_img"])){
			$err_img = $err_img . uploadImg($user_data,SYS_DIR_ADJ, $valName, $colName);
		}
		
		if(!empty($valName)){
			$valExt = $valExt . ",'" . $valName . "'";
			$colExt = $colExt . "," . $colName . "";
        	$colExt = $colExt . $user_data['no_nom_tabla'];
		}
		
		$this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "
									 (". substr($tblCol,0,(strlen($tblCol)-1)) ."" . $colExt .")  
							  VALUES (". substr($tblVal,0,(strlen($tblVal)-1)) . "" . $valExt . ")";
		$this->execute_single_query();		
		switch ($user_data['no_nom_tabla']){
			case 'usuario':
				for($i=0;$i<count($user_data['no_cod_menu']);$i++){
					$this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_menu 
					                         (cod_usuario,cod_menu)  
									  VALUES ('" .$user_data['cod_' . $user_data['no_nom_tabla']] . "','" .$user_data['no_cod_menu'][$i]. "')";
					$this->execute_single_query();
				}
				for($i=0;$i<count($user_data['no_cod_menu_sub']);$i++){
					$this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_menu_sub 
					                         (cod_usuario,cod_menu_sub)  
									  VALUES ('" .$user_data['cod_' . $user_data['no_nom_tabla']] . "','" .$user_data['no_cod_menu_sub'][$i]. "')";
					$this->execute_single_query();
				}
				for($i=0;$i<count($user_data['no_cod_empresa']);$i++){
					$this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_empresa 
					                         (cod_usuario,cod_empresa)  
									  VALUES ('" .$user_data['cod_' . $user_data['no_nom_tabla']] . "','" .$user_data['no_cod_empresa'][$i]. "')";
					$this->execute_single_query();
				}
				
				$this->query = " INSERT INTO " . $user_data['no_esq_tabla'] . "_perfil 
					                         (cod_usuario,cod_perfil)  
									  VALUES ('" .$user_data['cod_' . $user_data['no_nom_tabla']] . "','" .$user_data['no_cod_perfil']. "')";
				$this->execute_single_query();
				
				$err_img = $err_img . " La configuracion del sistema a sido asinada al usuario: " . $user_data['nom_' . $user_data['no_nom_tabla']] ;
			break;
		}
		$this->msj = "La transaccion se registro correctamente. " . $err_img;
	}
	
	#trae la siguiente secuencia de la tabla	
	public function setSigSecuencia($nomTbl){
		$this->query = " SELECT max(cod_" .str_replace('sys_','',$nomTbl). " + 1) as codSec 
                           FROM " .$nomTbl. " ";
		$this->get_results_from_query();
		if(count($this->rows) >= 1){
			return $numSec = strval($this->rows[0]['codSec']);exit();		
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
