<?php
class ModeloJsonCrm extends DBAbstractModel {
    public $_datosAfectados            =array();
    public $_datosEsquema              =array();
    public $_datosResultado            =array();
    public $_count                     =0;
    public $_datosSubProductos         =array();   
    public $_datosSession              =array(); 
    public $_datosProductos            =array();
    public $_datosMedios               =array();
    public $_datosEmpresa              =array();
    public $_datosReporte              =array();
    public function deleteDatos($table,$id){
        #eliminamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE FROM ".$table." where cod_".str_replace('sys_','',str_replace("crm_",'',str_replace("fa_","",$table)))."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function inactiveDatos($table,$id,$est){
        #desactivamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE ".$table." SET cod_estado='".$est."' where cod_".str_replace('sys_','',str_replace("crm_",'',str_replace("fa_","",$table)))."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function ActualizarCombo($esq="",$cod_usuario){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT sql_esquema,nro_columnas
                        FROM sys_tablareferencia 
                       WHERE nom_referencia='".$esq."'";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosEsquema[$pro]=$va;
            }
        }
        $this->rows ="";
        $this->query="";
        $this->query=str_replace("codUsu",$cod_usuario,$this->_datosEsquema[0]["sql_esquema"]);
        $this->get_results_from_query();
        if(count($this->query) >= 1):            
            foreach ($this->rows as $pro=>$va):
                $this->_count=0;
                foreach($va as $pro1=>$va1):
                    $this->_datosResultado[$pro]["col".$this->_count]=$va1;
                    $this->_count++;  
                endforeach;                                 
            endforeach;            
        endif;
    }
    
    public function getSubProductosMedios($codProductos){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT t1.cod_subproductos, 'Sub Producto: ', CONCAT(t1.nom_subproductos,' - ',t2.nom_cliente) as value
                        FROM crm_subproductos as t1,fa_cliente as t2
                       WHERE t1.cod_estado='AAA' 
                         AND t1.cod_productos=".$codProductos."
                         AND t1.cod_proveedor=t2.cod_cliente";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosSubProductos["Productos"][$pro]=$va;
            }
        }
        $this->rows ="";
        $this->query="";
        $this->query="SELECT cod_medios, concat(nom_medios,' - ', des_medios) as value
                        FROM crm_medios
                       WHERE cod_productos=".$codProductos."
                         AND cod_estado='AAA'";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosSubProductos["Medios"][$pro]=$va;
            }
        }
    }
    
    public function getDatosSession($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT cod_usuario,CONCAT(nom_usuario,' ',ape_usuario) as nom_usuario,email_usuario,
                             usuario_usuario,cod_estado,concat('modules/sistema/adjuntos/',img_usuario) as img_usuario 
                        FROM sys_usuario
                       WHERE usuario_usuario  = '" .$data['usuario_usuario']. "' 
                         AND password_usuario = '" .$data['password_usuario']. "'";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosSession[$pro]=$va;
            }
        }
    }
    
    public function getDatosProductos($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT concat('<li><a data-id=',t1.cod_productos,' id=btn-action name=envia-producto>',
                                    if(img_productos is null,'',concat('<img src=http://198.27.87.58/~explorae/appmoduler/modules/crm/adjuntos/',img_productos,'>')),
                                    '<h3>',t1.nom_productos,'</h3>',
                                    '<p>',t1.des_productos,'</p></a></li>') as producto 
                        FROM crm_productos as t1, crm_productos_usuario as t2
                       WHERE t1.cod_estado='AAA'
                         AND t1.cod_productos = t2.cod_productos
                         AND t2.cod_usuario = ".$data["cod_usuario"]."
                         AND t1.cod_empresa in(SELECT cod_empresa    
						 FROM sys_usuario_empresa
						WHERE cod_usuario=".$data["cod_usuario"].")";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosProductos[]=$va;
            }
        }
    }
    
    public function getDatosSubProductos($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT concat('<li><a data-id=',cod_subproductos,' id=btn-action name=envia-subproducto>',
                                    if(img1_subproductos is null,'',concat('<img src=http://198.27.87.58/~explorae/appmoduler/modules/crm/adjuntos/',img1_subproductos,'>')),
                                    '<h3>',nom_subproductos,'</h3>',
                                    '<p>',des_subproductos,'</p></a></li>') as subproducto 
                        FROM crm_subproductos
                       WHERE cod_estado='AAA'
                         AND cod_productos = ".$data["cod_producto"]."";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosSubProductos[]=$va;
            }
        }
    }
    
    public function getDatosMedios($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT concat('<li><a data-id=',cod_medios,' id=btn-action name=envia-medios>',
                                    '<h3>Medio: ',nom_medios,'</h3>',
                                    '<p>',des_medios,'</p></a></li>') as medios 
                        FROM crm_medios
                       WHERE cod_estado='AAA'
                         AND cod_productos = ".$data["cod_producto"]."
                         AND cod_usuario=".$data["cod_usuario"]."
                         AND ind_app=1";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosMedios[]=$va;
            }
        }
    }
    
    public function setContacto($data=array()){
        #insertamos el cliente
        $this->row  = "";
        $this->query = "";
        $secuencia = ModeloJsonCrm::setSigSecuencia('fa_cliente');
        $this->query = "INSERT INTO fa_cliente
                                    (cod_cliente,nom_cliente,dir_cliente,email_cliente,cel_cliente,ind_cliente_cliente,
                                    fec_cliente,cod_ciudad,cod_tipopago,cod_empresa,cod_usuario,cod_estado)
                             VALUES ('".$secuencia."','".$data["nom_cliente"]."','".$data["dir_cliente"]."','".$data["email_cliente"]."',
                                     '".$data["cel_cliente"]."',1,now(),0,7,'".$data["cod_empresa"]."','".$data["cod_usuario"]."','AAA'
                                    )";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
        $this->row  = "";
        $this->query = "";
        $secuenciaC=ModeloJsonCrm::setSigSecuencia('crm_contacto');
        $obs = "Cliente: ".$data["nom_cliente"]."\n"."Telefono: ".$data["cel_cliente"]."\n"."Ciudad Origen: ".$data["nom_ciudad"]."\n"."fecha de evento o viaje: ".$data["fec_aprox_contacto"]."\n"."fecha de regreso o terminacion: ".$data["fec_aprox_regreso"]."\n"."Nro Personas: ".$data["nro_personas"]."\n"."Observacion: ".$data["obs_contacto"];
        $this->query = " INSERT INTO crm_contacto 
                                     (cod_contacto,cod_cliente,obs_contacto,cod_subproductos,cod_medios,fec_contacto,fec_aprox_contacto,fec_prox_contacto,cod_estado,cod_usuario,cod_empresa)  
                              VALUES (".$secuenciaC.",'" . $secuencia . "','".$obs."','" . $data['cod_subproductos'] . "','" . $data['cod_medios'] . "',
                                      now(),'" . $data['fec_aprox_contacto'] . "',now(),'TRM','" . $data['cod_usuario'] . "','" . $data['cod_empresa'] . "')";
        $this->execute_single_query();
        $this->query = " INSERT INTO crm_bandeja_asignacion 
                                     (cod_bandeja_asignacion,cod_usuario,cod_contacto,fec_bandeja_asignacion,cod_usuario_asigna,cod_estado)  
                              VALUES (".ModeloJsonCrm::setSigSecuencia('crm_bandeja_asignacion').",38,".$secuenciaC.",now(),'" . $data['cod_usuario'] . "','AAA')";
        $this->execute_single_query();
        //print $this->query;exit;
    }
    
    public function getEmpresa($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT t1.cod_empresa, t1.nom_empresa as value
                        FROM sys_empresa as t1, sys_usuario_empresa as t2
                       WHERE t1.cod_empresa = t2.cod_empresa
                         AND t2.cod_usuario = ".$data["cod_usuario"]."";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosEmpresa[$pro]=$va;
            }
        }
    }
    
    public function getReporte($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT concat('<li data-theme=b><a data-id=',t3.cod_contacto,' id=btn-action name=envia-contacto>',
                                    '<h3>',t1.nom_cliente,'</h3>',
                                    '<p>',t2.nom_subproductos,' estado: ',t4.des_estado,'</p></a></li>') as contactos
                        FROM fa_cliente as t1, crm_subproductos as t2, crm_contacto as t3, sys_estado as t4
                       WHERE t1.cod_cliente = t3.cod_cliente
                         AND t3.cod_subproductos=t2.cod_subproductos
                         AND t3.cod_estado=t4.cod_estado
                         AND t3.cod_usuario = ".$data["cod_usuario"]."
                    ORDER BY t3.cod_contacto DESC";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosReporte[$pro]=$va;
            }
        }
    }
    
    public function setSigSecuencia($nomTbl) {
        $this->rows="";
        $this->query = "SELECT IF(MAX(cod_" . str_replace('crm_','',str_replace('fa_', '',str_replace('crm_', '',str_replace('fa_', '', $nomTbl)))) . " IS NOT NULL),MAX(cod_" . str_replace('crm_','',str_replace('fa_', '',str_replace('crm_', '',str_replace('fa_', '', $nomTbl)))) . " + 1),1) as codSec 
                       FROM " . $nomTbl . " ";
        $this->get_results_from_query();
        if (count($this->rows) >= 1) {
            return strval($this->rows[0]['codSec']);
        }
    }
    
    function __construct(){
        $this->db_name = 'appmoduler';
    }

    function __destruct(){
        unset($this);
    }
}
?>
