<?php
class ModeloJsonStk extends DBAbstractModel {
    public $_datosAfectados=array();
    public $_datosEsquema  =array();
    public $_datosResultado=array();
    public $_count         =0;
    public function deleteDatos($table,$id){
        #eliminamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE FROM ".$table." where cod_".str_replace('sys_','',str_replace("stk_","",$table))."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function inactiveDatos($table,$id,$est){
        #desactivamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE ".$table." SET cod_estado='".$est."' where cod_".str_replace('sys_','',str_replace("stk_","",$table))."=".$id."";
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
    
    function __construct(){
        $this->db_name = 'appmoduler';
    }

    function __destruct(){
        unset($this);
    }
}
?>
