<?php
class ModeloJsonDatolee extends DBAbstractModel {
    public $_datosAfectados            =array();
    public $_datosSectores             =array();   
    public $_datosSession              =array(); 
    public $_datosProductos            =array();
    public $_datosMedios               =array();
    public $_datosEmpresa              =array();
    public $_datosReporte              =array();
    public $_datosCiudad               =array(); 
    public $_datosComuna               =array();      
    public function deleteDatos($table,$id){
        #eliminamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE FROM ".$table." where cod_".str_replace('sys_','',str_replace("datolee_",'',str_replace("fa_","",$table)))."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function inactiveDatos($table,$id,$est){
        #desactivamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE ".$table." SET cod_estado='".$est."' where cod_".str_replace('sys_','',str_replace("datolee_",'',str_replace("fa_","",$table)))."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
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
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosSession[$pro]=$va;
            }
        }
    }

    public function getDatosSectores($data=array()){
        $this->rows  = "";
        $this->query = "";
        $this->query="SELECT concat('<li><a data-id=',cod_sector,' id=btn-action name=envia-sector>',
                                    '<h3>',nom_sector,'</h3>',
                                    '<p>',des_sector,'</p></li>') as sectores
                        FROM datolee_sector
                       WHERE cod_estado='AAA'
                         AND cod_empresa in(SELECT cod_empresa    
						 FROM sys_usuario_empresa
						WHERE cod_usuario=".$data["cod_usuario"].")
		    ORDER BY nom_sector";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosSectores[]=$va;
            }
        }
    }
    
    public function setVoto($data=array()){
        $this->_datosAfectados["row"]=0;
        $this->rows  = "";
        $this->query = "";
        $this->query="SELECT cod_voto 
                        FROM datolee_voto
                       WHERE ced_voto=".$data['ced_voto'];
        $this->get_results_from_query();
        if(!count($this->rows) >= 1):
            $this->rows  = "";
            $this->query = "";
            $secuenciaC=ModeloJsonDatolee::setSigSecuencia('datolee_voto');
            $obs = "Voto: ".$data["nom_voto"]."\n"."Telefono: ".$data["tel_voto"]."\n"."Observacion: ".$data["obs_voto"];
            $this->query = " INSERT INTO datolee_voto 
                                         (cod_voto,cod_sector,ced_voto,nom_voto,tel_voto,dir_voto,cod_ciudad,cod_comuna,fec_voto,fec_prox_voto,obs_voto,cod_estado,cod_usuario,cod_empresa)  
                                  VALUES (".$secuenciaC.",'" . $data['cod_sector'] . "','" . $data['ced_voto'] . "','" . $data['nom_voto'] . "','" . $data['tel_voto'] . "',
                                          '" . $data['dir_voto'] . "','" . $data['cod_ciudad'] . "','" . $data['cod_comuna'] . "',now(),now(),'" .$obs. "','DLT','" . $data['cod_usuario'] . "',20)";
            $this->execute_single_query();
            $this->rows  = "";
            $this->query = "";
            $this->query = " INSERT INTO datolee_bandeja_voto
                                         (cod_bandeja_voto,cod_usuario,cod_voto,fec_bandeja_voto,cod_usuario_asigna,cod_estado)  
                                  VALUES (".ModeloJsonDatolee::setSigSecuencia('datolee_bandeja_voto').",4,".$secuenciaC.",now(),'" . $data['cod_usuario'] . "','AAA')";
            $this->execute_single_query();
            $this->_datosAfectados["row"]=1;            
        endif;
    }
    
    public function setVotoApp($user_data=array()){
        for($i=0;$i<count($user_data["obj"])-1;$i++):
            sleep(1);
            $this->rows  = "";
            $this->query = "";
            $this->query="SELECT cod_voto 
                            FROM datolee_voto
                           WHERE ced_voto=".$user_data["obj"][$i]['ced_voto'];
            $this->get_results_from_query();
            if(!count($this->rows) >= 1):
                $this->rows  = "";
                $this->query = "";
                $secuenciaC=ModeloJsonDatolee::setSigSecuencia('datolee_voto');
                $obs = "Voto: ".$user_data["obj"][$i]["nom_voto"]."\n"."Telefono: ".$user_data["obj"][$i]["tel_voto"]."\n"."Observacion: ".$user_data["obj"][$i]["obs_voto"];
                $this->query = " INSERT INTO datolee_voto 
                                             (cod_voto,cod_sector,ced_voto,nom_voto,tel_voto,dir_voto,cod_ciudad,cod_comuna,fec_voto,fec_prox_voto,obs_voto,cod_estado,cod_usuario,cod_empresa)  
                                      VALUES (".$secuenciaC.",'" . $user_data["obj"][$i]['cod_sector'] . "','" . $user_data["obj"][$i]['ced_voto'] . "','" . $user_data["obj"][$i]['nom_voto'] . "','" . $user_data["obj"][$i]['tel_voto'] . "',
                                              '" . $user_data["obj"][$i]['dir_voto'] . "','" . $user_data["obj"][$i]['cod_ciudad'] . "','" . $user_data["obj"][$i]['cod_comuna'] . "',now(),now(),'" .$obs. "','DLT','" . $user_data['cod_usuario'] . "',20)";
                $this->execute_single_query();
                $this->rows  = "";
                $this->query = "";
                $this->query = " INSERT INTO datolee_bandeja_voto
                                             (cod_bandeja_voto,cod_usuario,cod_voto,fec_bandeja_voto,cod_usuario_asigna,cod_estado)  
                                      VALUES (".ModeloJsonDatolee::setSigSecuencia('datolee_bandeja_voto').",4,".$secuenciaC.",now(),'" . $user_data['cod_usuario'] . "','AAA')";
                $this->execute_single_query();            
            endif;
        endfor;
        $this->_datosAfectados["row"]=1;
    }
    
    public function getEmpresa($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT t1.cod_empresa, t1.nom_empresa as value
                        FROM sys_empresa as t1, sys_usuario_empresa as t2
                       WHERE t1.cod_empresa = t2.cod_empresa
                         AND t2.cod_usuario = ".$data["cod_usuario"]."";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosEmpresa[$pro]=$va;
            }
        }
    }
    
    public function getReporte($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT concat('<li><a data-id=',t1.cod_voto,' id=btn-action name=envia-votos>',
                                    '<h3>',t1.nom_voto,'</h3>',
                                    '<p>',t2.nom_sector,'<p>',
                                    '<p>',t5.nom_ciudad,'<p>',
                                    '<p> estado: ',t4.des_estado,'</p></a></li>') as contactos
                        FROM datolee_voto as t1, datolee_sector as t2, sys_estado as t4, sys_ciudad as t5
                       WHERE t1.cod_sector=t2.cod_sector
                         AND t1.cod_estado=t4.cod_estado
                         AND t1.cod_ciudad=t5.cod_ciudad
                         AND t1.cod_usuario = ".$data["cod_usuario"]."
                    ORDER BY t1.cod_voto DESC";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosReporte[$pro]=$va;
            }
        }
    }
    
    public function getDatosCiudad($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT concat('<li><a data-id=',cod_ciudad,' id=btn-action name=envia-ciudad>',
                                    '<h3>',nom_ciudad,'</h3>',
                                    '<p>',dpt_ciudad,'</p></li>') as ciudad
                        FROM sys_ciudad
                       WHERE nom_ciudad like '".$data["data-value"]."%'
                         AND lower(dpt_ciudad) = 'caldas'";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosCiudad[]=$va;
            }
        }
    }
    
    public function getDatosComuna($data=array()){
        $this->row  = "";
        $this->query = "";
        $this->query="SELECT concat('<li><a data-id=',cod_comuna,' id=btn-action name=envia-comuna>',
                                    '<h3>',nom_comuna,'</h3>',
                                    '<p></p></li>') as comuna
                        FROM sys_comuna
                       WHERE cod_ciudad =".$data["cod_ciudad"]."";
        $this->get_results_from_query();
        if(count($this->rows) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosComuna[]=$va;
            }
        }
    }
    
    public function setSigSecuencia($nomTbl) {
        $this->rows="";
        $this->query = "SELECT IF(MAX(cod_" . str_replace('datolee_','',str_replace('fa_', '',str_replace('datolee_', '',str_replace('fa_', '', $nomTbl)))) . " IS NOT NULL),MAX(cod_" . str_replace('datolee_','',str_replace('fa_', '',str_replace('datolee_', '',str_replace('fa_', '', $nomTbl)))) . " + 1),1) as codSec 
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
