<?php
class ModeloJsonHelpdesk extends DBAbstractModel {
    public $_datosAfectados=array();
    public $_datosGenerales=array();
    public $_datosEsquema  =array();
    public $_datosResultado=array();
    public $_count         =0;
    public function deleteDatos($table,$id){
        #eliminamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE FROM ".$table." where cod_".str_replace("hd_","",$table)."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function inactiveDatos($table,$id,$est){
        #desactivamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE ".$table." SET cod_estado='".$est."' where cod_".str_replace("hd_","",$table)."=".$id."";
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
    
    public function consultarDatGenerHelpDesk(){
        #consultamos la configuracion general del helpdesk
        $this->row  = "";
        $this->query = "";
        $this->query = "SELECT * FROM hd_config WHERE cod_estado='AAA'";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosGenerales[$pro]=$va;
            }
        }
        
    }
    
    public function insertaTicketSoporte($data=array(),$dataIn=array()){
        //insertamos los tiquets automaticamente
        $this->row  = "";
        $this->query = "";
        $i=0;
        for($i=0;$i<count($data);$i++):
            $secuencia=0;
            $this->query="SELECT IF(MAX(cod_servicio) IS NOT NULL,MAX(cod_servicio) + 1,1) as codSec 
                            FROM hd_servicio";
            $this->get_result_from_query();  
            $secuencia=$this->row['codSec'];
            $this->query = "";
            $this->query = !empty($data[$i]["query"]) ? str_replace("{cod_servicio}", $secuencia, $data[$i]["query"]) : null;
            if(!empty($this->query)){
                $this->execute_single_query();
                $email_array=array("Saludo"=>"Cordial Saludo: ".$data[$i]["nombre"]."",
                                             "Introduccion"=> "",
                                             "Descripcion"=>str_replace('{NRO_TIQUET}', $secuencia, $dataIn[0]["asunto_respuesta_config"]),
                                             "to"=>$data[$i]["from"]);
                sendEmail("sistema", 2, $email_array, $Objvista=new view(),$dataIn,'../../../',$secuencia);
            }
        endfor;
    }
    
    function __construct(){
        $this->db_name = 'appmoduler';
    }

    function __destruct(){
        unset($this);
    }
}
?>
