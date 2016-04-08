<?php
class ModeloJsonSined extends DBAbstractModel {
    public $_datosAfectados            =array();
    public $_cargaAcademicaDocente     =array();
    public $_cargaGrupoAcademicaDocente=array();
    public $_periodoAcademico          =array(); 
    public $_cargaGrupoValoracion      =array();
    public $_datosEsquema              =array();
    public $_datosResultado            =array();
    public $_count                     =0;
    public $_datosAlumnos              =array();
    public function deleteDatos($table,$id){
        #eliminamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE FROM ".$table." where cod_".str_replace('sys_','',str_replace("sined_","",$table))."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function inactiveDatos($table,$id,$est){
        #desactivamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE ".$table." SET cod_estado='".$est."' where cod_".str_replace('sys_','',str_replace("sined_","",$table))."=".$id."";
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
    
    public function getCargaAcademicaDocente($codUsuario=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT t1.cod_materia, concat('Materia: ',t1.nom_materia) as value
                        FROM sined_materia as t1, sined_carga_academica as t2 
                       WHERE t1.cod_materia=t2.cod_materia
                         AND t2.cod_docente=".$codUsuario."
                         AND t1.cod_estado='AAA'
                         AND t2.bd_a_config=(SELECT bd_a_config
                                               FROM sined_config
					      WHERE cod_estado='AAA')
                    GROUP BY t1.cod_materia";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_cargaAcademicaDocente[$pro]=$va;
            }
        }
    }
    
    public function getDatosGrupoCargaAcademica($codMateria=0,$codUsuario=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT t1.cod_grupo, concat('Grupo: ',t1.nom_grupo) as value
                        FROM sined_grupo as t1, sined_carga_academica as t2 
                       WHERE t1.cod_grupo=t2.cod_grupo
                         AND t2.cod_materia=".$codMateria."
                         AND t2.cod_docente=".$codUsuario."
                         AND t1.cod_estado='AAA'
                         AND t2.bd_a_config=(SELECT bd_a_config
                                               FROM sined_config
					      WHERE cod_estado='AAA')
                   GROUP BY t1.cod_grupo";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_cargaGrupoAcademicaDocente[$pro]=$va;
            }
        }
    }
    
    public function getDatosGrupoValoracion($codUsuario=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT cod_grupo, concat('Grupo: ',nom_grupo) as value
                        FROM sined_grupo 
                       WHERE cod_responsable=".$codUsuario."
                    GROUP BY cod_grupo";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_cargaGrupoValoracion[$pro]=$va;
            }
        }
    }
    
    public function getPeriodoAcademico($codGrupo=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT t1.cod_periodo_academico, concat('Periodo: ',t1.nom_periodo_academico) as value
                        FROM sined_periodo_academico as t1, sined_notas as t2 
                       WHERE t1.cod_periodo_academico=t2.cod_periodo_academico
                         AND t2.cod_grupo=".$codGrupo."
                         AND t2.bd_a_config=(SELECT bd_a_config
                                               FROM sined_config
					      WHERE cod_estado='AAA')
                   GROUP BY t1.cod_periodo_academico";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_periodoAcademico[$pro]=$va;
            }
        }
    }
    
    public function getDisponibilidadGrupo($codGrupo=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT fbDevuelveDisponibilidad(".$codGrupo.") as value
                        FROM dual";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosAfectados[$pro]=$va;
            }
        }
    }
    
    public function getAlumnoGrupo($codGrupo=0,$tipo=0){
        $aux = $tipo==0 ? " AND cod_estado<>'MPR " : "";
        $this->rows ="";
        $this->query="";
        $this->query="SELECT cod_alumno, concat(ape1_alumno,' ',ape2_alumno,' ',nom1_alumno,' ',nom2_alumno,' - ',num_ident_alumno) as value
                        FROM sined_alumno  
                       WHERE cod_grupo=".$codGrupo.
                        $aux
                       ." AND bd_a_config=(SELECT bd_a_config
                                            FROM sined_config
					                       WHERE cod_estado='AAA')
                   GROUP BY ape1_alumno";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosAlumnos[$pro]=$va;
            }
        }
    }

/**
 * @param int $codGrupo
 * @param int $tipo
 */
function getAlumnosPeriodoCertificado($codGrupo=0, $tipo=0){
        $aux = $tipo==0 ? " AND cod_estado<>'MPR " : "";
        $this->rows ="";
        $this->query="";
        $this->query="SELECT cod_alumno, concat(ape1_alumno,' ',ape2_alumno,' ',nom1_alumno,' ',nom2_alumno,' - ',num_ident_alumno) as value
                        FROM sined_alumno
                       WHERE cod_grupo=".$codGrupo.
            $aux
            ." AND bd_a_config=(SELECT bd_a_config
                                            FROM sined_config
					                       WHERE cod_estado='AAA')
                   GROUP BY ape1_alumno";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosAlumnos["Alumnos"][$pro]=$va;
            }
        }

        $this->rows ="";
        $this->query="";
        $this->query="SELECT t1.cod_periodo_academico, concat('Periodo: ',t1.nom_periodo_academico) as value
                        FROM sined_periodo_academico as t1, sined_notas as t2
                       WHERE t1.cod_periodo_academico=t2.cod_periodo_academico
                         AND t2.cod_grupo=".$codGrupo."
                         AND t2.bd_a_config=(SELECT bd_a_config
                                               FROM sined_config
					      WHERE cod_estado='AAA')
                   GROUP BY t1.cod_periodo_academico";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosAlumnos["Periodo"][$pro]=$va;
            }
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
