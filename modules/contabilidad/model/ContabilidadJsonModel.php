<?php
class ModeloJsonContabilidad extends DBAbstractModel {
    public $_datosAfectados=array();

    public function deleteDatos($table,$id){
        #eliminamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE FROM ".$table." where cod_".str_replace("con_","",$table)."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function inactiveDatos($table,$id,$est){
        #desactivamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE ".$table." SET cod_estado='".$est."' where cod_".str_replace("con_","",$table)."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    function __construct(){
        $this->db_name = 'appmoduler';
    }

    function __destruct(){
        unset($this);
    }
}
?>
