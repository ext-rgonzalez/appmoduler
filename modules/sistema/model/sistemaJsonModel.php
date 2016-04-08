<?php
class ModeloJsonSistema extends DBAbstractModel {
    public $_datosAfectados=array();
    public $_datosEsquema  =array();
    public $_datosResultado=array();
    public $_count         =0;
    public $_datosAgenda   =array();
    public function setMenuHeader($cod_menu_sub,$ind_header){
        #actualizar menu para habilitarlo o deshabilitarlo en el header
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE sys_menu_sub 
                           SET ind_header=".$ind_header." 
                         WHERE cod_menu_sub=".$cod_menu_sub."";
        $this->execute_single_query();
    }
    
    public function deleteDatos($table,$id){
        #eliminamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE FROM ".$table." where cod_".str_replace("sys_","",$table)."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function inactiveDatos($table,$id,$est){
        #desactivamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE ".$table." SET cod_estado='".$est."' where cod_".str_replace("sys_","",$table)."=".$id."";
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
    
    public function InsertaEvento($data=array(), $codUsu){
        $this->query = "";
        if($data["ind_confirmado"]==0) {
            $this->query = "call pbNuevaNotificacionTarea(-1, " . $data["cod_empresa"] . ", " . $codUsu . ", 2, '" . $data['nom_evento_agenda'] . "',now(), @cDesError, @cNumError)";
        }else{
            $this->query = "call pbNuevaNotificacionTarea(-1, " . $data["cod_empresa"] . ", " . $codUsu . ", 1, '" . $data['nom_evento_agenda'] . "',now(), @cDesError, @cNumError)";
        }
        $this->execute_single_query();

        $this->row  = "";
        $this->query = "";
        $secuencia = ModeloJsonSistema::setSigSecuencia('sys_agenda');
        $this->query = "INSERT INTO sys_agenda
                                    (nom_evento_agenda,des_evento_agenda,start_evento_agenda,end_evento_agenda,color_agenda,ind_confirmado,fec_agenda,cod_usuario,cod_empresa)
                             VALUES ('".$data['nom_evento_agenda']."','".$data["des_evento_agenda"]."','".$data["start_evento_agenda"]."','".$data["end_evento_agenda"]."',
                                     '".$data["color"]."','".$data["ind_confirmado"]."',now(),'".$codUsu."','".$data["cod_empresa"]."')";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $secuencia;

    }

    public function EditaEvento($data=array(), $codUsu){
        $this->query = "";
        $this->query = "call pbNuevaNotificacionTarea(-1, " . $data["cod_empresa"] . ", " . $codUsu . ", 1, '" . $data['nom_evento_agenda'] . "', now(), @cDesError, @cNumError)";
        $this->execute_single_query();

        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE sys_agenda
                           SET nom_evento_agenda='".$data['nom_evento_agenda']."',des_evento_agenda='".$data["des_evento_agenda"]."',start_evento_agenda='".$data["start_evento_agenda"]."',end_evento_agenda='".$data["end_evento_agenda"]."',color_agenda='".$data["color"]."',ind_confirmado='".$data["ind_confirmado"]."',fec_mod_agenda=now()
                         WHERE cod_agenda='".$data['cod_agenda']."'";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }

    public function EliminaEvento($data=array(), $codUsu){

        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE 
                          FROM sys_agenda
                         WHERE cod_agenda='".$data['cod_agenda']."'";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }

    public function EditaDragEvento($data=array(), $codUsu){

        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE sys_agenda
                           SET start_evento_agenda='".$data["start_evento_agenda"]."',end_evento_agenda='".$data["end_evento_agenda"]."',fec_mod_agenda=now()
                         WHERE cod_agenda='".$data['cod_agenda']."'";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }

    public function getAgenda($codUsu){ 
        $this->rows ="";
        $this->query="";
        $this->query="SELECT fbTraeEmpresa('". $codUsu ."') as result";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosEsquema[$pro]=$va;
            }
        }
        $this->rows ="";
        $this->query="";
        $this->query="SELECT * 
                        FROM sys_agenda
                       WHERE cod_empresa in('".$this->_datosEsquema[0]["result"]."')";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosAgenda[]=$va;
            }
        }
    }

    public function setSigSecuencia($nomTbl) {
        $this->rows="";
        $this->query = "SELECT IF(MAX(cod_" . str_replace('sys_','',str_replace('fa_', '',str_replace('sys_', '',str_replace('fa_', '', $nomTbl)))) . " IS NOT NULL),MAX(cod_" . str_replace('sys_','',str_replace('fa_', '',str_replace('sys_', '',str_replace('fa_', '', $nomTbl)))) . " + 1),1) as codSec 
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
