<?php
class ModeloJsonendodoncia extends DBAbstractModel {
    public $_datosAfectados              =array();
    public $_datosEsquema                =array();
    public $_datosResultado              =array();
    public $_count                       =0;
    public $_datosSubProductos           =array();   
    public $_datosSession                =array(); 
    public $_datosProductos              =array();
    public $_datosMedios                 =array();
    public $_datosEmpresa                =array();
    public $_datosReporte                =array();
    public $_datosHistoriaClinica        =array();
    public $_datosDiagnosticos           =array();
    public $_datosHistoriaClinicaCliente =array();
    public $_datosComprobanteFac         =array();
    public $_datosOdontologo             =array();
    public function deleteDatos($table,$id){
        #eliminamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE FROM ".$table." where cod_".str_replace('sys_','',str_replace("endodoncia_",'',str_replace("fa_","",$table)))."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function inactiveDatos($table,$id,$est){
        #desactivamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE ".$table." SET cod_estado='".$est."' where cod_".str_replace('sys_','',str_replace("endodoncia_",'',str_replace("fa_","",$table)))."=".$id."";
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
    
    public function getOdontograma($codDiente=0){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT count(1) as result
                        FROM endodoncia_config_dental
                       WHERE cod_imagen_dental=".$codDiente;
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado["row"]=$va;
            }
        }
    }
    
    public function getOdontogramaHistoria($codDiente=0,$case=0,$codHistoria=0,$cod_paciente=0){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT COUNT(1) as retratamiento
                        FROM endodoncia_historia_clinica
                       WHERE cod_paciente=".$cod_paciente." 
                         AND cod_config_dental = (SELECT cod_config_dental
                                                    FROM endodoncia_config_dental
                                                   WHERE cod_imagen_dental=".$codDiente.")";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado["retratamiento"]=$va;
            }
        }
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT IF((SELECT cod_config_dental
                                   FROM endodoncia_config_dental
		                  WHERE cod_imagen_dental=".$codDiente.") is null,0,(SELECT cod_config_dental
                                                                                       FROM endodoncia_config_dental
		                                                                      WHERE cod_imagen_dental=".$codDiente.")) as cod_config_dental FROM DUAL";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado["row"]=$va;
            }
        }
        
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT fbArmaTablaDiagnostico(".$codDiente.",".$case.",".$codHistoria.") as panel FROM DUAL";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado["panel"]=$va;
            }
        }

        $this->row  = "";
        $this->query = "";
        $this->query="SELECT fbArmaTablaDesobturacion(".$codDiente.",".$case.",".$codHistoria.") as desobturacion FROM DUAL";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado["desobturacion"]=$va;
            }
        }

        $this->row  = "";
        $this->query = "";
        $this->query="SELECT fbArmaImagenesHistoriaClinica(".$codHistoria.") as imagenes FROM DUAL";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado["imagenes"]=$va;
            }
        }
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT fbArmaEvolucionesPaciente(".$codHistoria.",".$cod_paciente.") as evoluciones FROM DUAL";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado["evoluciones"]=$va;
            }
        }
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT fbArmaEvolucioneDientePaciente(".$codDiente.", ".$cod_paciente.") as evoluciones_diente FROM DUAL";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado["evoluciones_diente"]=$va;
            }
        }
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT fbArmaImagenesHistoriaClinicaDental(".$codDiente.", ".$cod_paciente.") as imagenes_diente FROM DUAL";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado["imagenes_diente"]=$va;
            }
        }
    }
        
    public function getMedicamentos($nomMedicamento){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT cod_config_medicamentos as codigo,concat(nom_config_medicamentos,forma_farma_config_medicamentos) result
                        FROM endodoncia_config_medicamentos
                       WHERE nom_config_medicamentos like '%".$nomMedicamento."%'
                       LIMIT 10";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado[$pro]=$va;
            }
        }
    }

    public function getMetMedicamentos(){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT met_nuevo as result
                        FROM sys_tablareferencia
                       WHERE nom_referencia = 'endodoncia_config_medicamentos'";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado=$va;
            }
        }
    }

    public function getPacientes($cadenaPaciente){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT cod_paciente as codigo,concat(ape1_paciente,' ',ape2_paciente,' ',nom1_paciente,' ',nom2_paciente,'-',ced_paciente) result
                        FROM endodoncia_paciente
                       WHERE des_paciente like '%".$cadenaPaciente."%'
                       LIMIT 10";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado[$pro]=$va;
            }
        }
    }

    public function deleteImagen($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="DELETE
                        FROM endodoncia_registro_imagenes
                       WHERE cod_registro_imagenes= ".$data[0]."
                         AND cod_historia_clinica = ".$data[1]."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
        unlink("../adjuntos/".$data[2]);
    }

    public function getMetDatosConfig($codUsu){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT t1.cod_empresa
                        FROM sys_usuario_empresa as t1, sys_empresa_contrato as t2 
                       WHERE t1.cod_usuario='".$codUsu."'
                         AND t1.cod_empresa=t2.cod_empresa
                         AND t2.cod_modulo=12";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosEsquema[$pro]=$va;
            }
        }
        $this->rows ="";
        $this->query="";
        $this->query="SELECT ruta_huella_config
                        FROM endodoncia_config
                       WHERE cod_empresa='".$this->_datosEsquema[0]["cod_empresa"]."'
                         AND cod_estado='AAA'";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado=$va;
            }
        }
    }

    public function getDatosHistoriaPaciente($codUsu=0){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT t1.cod_historia_clinica, concat(t2.nom_config_dental,' - ',t2.num_config_dental) as value
                        FROM endodoncia_historia_clinica as t1, endodoncia_config_dental as t2
                       WHERE t1.cod_config_dental = t2.cod_config_dental
                         AND t1.cod_paciente = ".$codUsu."";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosHistoriaClinica[$pro]=$va;
            }
        }
    }

    public function getDatosConfigDental($codHistoria){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT t2.cod_imagen_dental
                        FROM endodoncia_historia_clinica as t1, endodoncia_config_dental as t2
                       WHERE t1.cod_historia_clinica='".$codHistoria."'
                         AND t1.cod_config_dental =  t2.cod_config_dental";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosResultado=$va;
            }
        }
    }

    public function getDiagnosticos($codDia){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT nom_config_diagnosticos
                        FROM endodoncia_config_diagnosticos
                       WHERE cod_config_diagnosticos='".$codDia."'";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosEsquema[$pro]=$va;
            }
        }
        $this->rows ="";
        $this->query="";
        $this->query="SELECT cod_config_diagnosticos, des_config_diagnosticos as value
                        FROM endodoncia_config_diagnosticos 
                       WHERE nom_config_diagnosticos = '".$this->_datosEsquema[0]["nom_config_diagnosticos"]."'";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosDiagnosticos[$pro]=$va;
            }
        }
    }

    public function getNumComprobante($tipCom=0,$cod_empresa){
        #traemos el numero del siguiente comprobante configurado en el sistema
        $this->row  = "";
        $this->query = "";
        $tipCom==1 ? $cAux='num_sig_recibocaja+1' : $cAux='num_sig_compago+1';
        $this->query = "SELECT CONCAT(pre_sig_comp_ingreso,(num_sig_comp_ingreso)) as numComprobanteIngreso,CONCAT(pre_sig_comp_egreso,(num_sig_comp_egreso)) as numComprobanteEgreso
                          FROM fa_config 
                         WHERE cod_estado='AAA' 
                           AND cod_empresa=".$cod_empresa;
        $this->get_result_from_query();
        $this->_numComprobanteIngreso = $this->row['numComprobanteIngreso'];
        $this->_numComprobanteEgreso = $this->row['numComprobanteEgreso'];
    }

    public function getDatosHistoriaClinicaCliente($numCli=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT t1.cod_historia_clinica, concat(t2.nom_config_dental,' - ', t2.des_config_dental) as value
                        FROM endodoncia_historia_clinica as t1, endodoncia_config_dental as t2 
                       WHERE t1.cod_config_dental=t2.cod_config_dental
                         AND t1.cod_paciente=".$numCli."
                         AND t1.imp_adeu_historia_clinica>0";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosHistoriaClinicaCliente["his"][$pro]=$va;
            }
        }
        $this->rows ="";
        $this->query="";
        $this->query="SELECT round(sum(imp_adeu_historia_clinica),2) as value
                        FROM endodoncia_historia_clinica 
                       WHERE cod_paciente=".$numCli;
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosHistoriaClinicaCliente["deuda"]=$va;
            }
        }
    }

    public function getDatosHistoriaClinicaCliente_1($numCli=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT t1.cod_historia_clinica, concat(t2.nom_config_dental,' - ', t2.des_config_dental) as value
                        FROM endodoncia_historia_clinica as t1, endodoncia_config_dental as t2 
                       WHERE t1.cod_config_dental=t2.cod_config_dental
                         AND t1.cod_paciente=".$numCli."";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosHistoriaClinicaCliente[$pro]=$va;
            }
        }
    }

    public function getDatosComprobanteFactura($numHistoria=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT ROUND(imp_total_historia_clinica,2) AS imp_total_historia_clinica, ROUND(imp_canc_historia_clinica,2) AS imp_canc_historia_clinica, ROUND(imp_adeu_historia_clinica,2) AS imp_adeu_historia_clinica 
                        FROM endodoncia_historia_clinica
                       WHERE cod_historia_clinica=".$numHistoria."";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
                foreach ($this->rows as $pro=>$va) {
                    $this->_datosComprobanteFac[$pro] = $va;
                }
        }

        $this->rows  = "";
        $this->query = "";
        $this->query="SELECT fbArmaHistorialPagos(".$numHistoria.") as Historial FROM DUAL";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosComprobanteFac[0]["Historial"]=$va;
            }
        }
    }

    public function getValidaOdontologo($data=array()){
        $valida=false;
        $this->rows ="";
        $this->query="";
        $this->query = " SELECT cod_usuario,CONCAT(nom_usuario,' ',ape_usuario) as nom_usuario,email_usuario,
                                    usuario_usuario,cod_estado,concat('modules/sistema/adjuntos/',img_usuario) as img_usuario 
                               FROM sys_usuario
                              WHERE usuario_usuario  = '" .$data['usuario_usuario']. "' 
                                AND password_usuario = '" .$data['password_usuario']. "'";
        $this->get_results_from_query();
        if(count($this->rows) == 1){
            $valida=true;
            foreach ($this->rows as $pro=>$va) {
                $this->_datosOdontologo[$pro]=$va;
            }
        }
        unset($this->rows);
        if($valida){
            $this->query = "";
            $this->query="SELECT t1.cod_historia_clinica, concat(t3.ced_paciente,'-',t2.nom_config_dental,' - ',t2.num_config_dental) as value
                            FROM endodoncia_historia_clinica as t1, endodoncia_config_dental as t2, endodoncia_paciente as t3
                           WHERE t1.cod_config_dental = t2.cod_config_dental
                             AND t1.cod_paciente      = t3.cod_paciente
                             AND t3.cod_medico        = ".$this->_datosOdontologo[0]["cod_usuario"]."
                             AND date(t1.fec_historia_clinica) between '".$data["fec_inicial"]."' AND '".$data["fec_final"]."'";           
            $this->get_results_from_query();
            if(count($this->query) >= 1){
                foreach ($this->rows as $pro=>$va) {
                    $this->_datosResultado["pacientes"][$pro]=$va;
                }
            }
        }else{
            $this->_datosResultado="";
        }
    }

    public function setSigSecuencia($nomTbl) {
        $this->rows="";
        $this->query = "SELECT IF(MAX(cod_" . str_replace('endodoncia_','',str_replace('fa_', '',str_replace('endodoncia_', '',str_replace('fa_', '', $nomTbl)))) . " IS NOT NULL),MAX(cod_" . str_replace('endodoncia_','',str_replace('fa_', '',str_replace('endodoncia_', '',str_replace('fa_', '', $nomTbl)))) . " + 1),1) as codSec 
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
