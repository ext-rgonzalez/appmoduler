<?php
function sinedJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/sinedJsonModel.php';
    require_once '../../../app/principalFunction.php';
    require_once '../../../app/session.php';
    Session::init();
    $sinedJson = new ModeloJsonSined();
    $user_data = helper_user_data('nuevoRegistro');
    switch ($user_data["dTabla"]){
        case 'general':
            switch ($user_data['cCase']){
                case 'deleteRegistro':
                    $sinedJson->deleteDatos($user_data["cTable"],$user_data["sValue"]);
                    $jsonReturArray =$sinedJson->_datosAfectados;
                break;
                case 'inactiveRegistro':
                    $sinedJson->inactiveDatos($user_data["cTable"],$user_data["sValue"],$user_data["sEst"]);
                    $jsonReturArray =$sinedJson->_datosAfectados;
                break;
                case 'ActualizarCombo':
                    $sinedJson->ActualizarCombo($user_data["sValue"],Session::get('cod'));
                    $jsonReturArray =$sinedJson->_datosResultado;
                break;
            }
        break;
        case 'sined_notas':
            switch ($user_data['cCase']){
                case 'cod_carga_academica':
                    $sinedJson->getDatosGrupoCargaAcademica($user_data["sValue"], $user_data["sAux"]);
                    $jsonReturArray =$sinedJson->_cargaGrupoAcademicaDocente;
                break;
                case 'cod_usuario':
                    $sinedJson->getCargaAcademicaDocente($user_data["sValue"]);
                    $jsonReturArray =$sinedJson->_cargaAcademicaDocente;
                break;
                case 'cod_grupo':
                    $sinedJson->getPeriodoAcademico($user_data["sValue"]);
                    $jsonReturArray =$sinedJson->_periodoAcademico;
                break;
            }
        break;
        case 'sined_boletines':
            switch ($user_data['cCase']){
                case 'cod_grupo':
                    $sinedJson->getPeriodoAcademico($user_data["sValue"]);
                    $jsonReturArray =$sinedJson->_periodoAcademico;
                break;
            }    
        break;                
        case 'sined_valoracion_descriptiva':
            switch ($user_data['cCase']){
                case 'cod_usuario':
                    $sinedJson->getDatosGrupoValoracion($user_data["sValue"]);
                    $jsonReturArray =$sinedJson->_cargaGrupoValoracion;
                break;
            }
        break;
        case 'sined_alumno':
            switch ($user_data['cCase']){
                case 'no_cod_grupo':case 'cod_grupo':
                    $sinedJson->getDisponibilidadGrupo($user_data["sValue"]);
                    $jsonReturArray =$sinedJson->_datosAfectados;
                break;
            }
        break; 
        case 'sined_ficha_academica':
            switch ($user_data['cCase']){
                case 'cod_grupo':
                    $sinedJson->getAlumnoGrupo($user_data["sValue"]);
                    $jsonReturArray =$sinedJson->_datosAlumnos;
                break;
            }
        break;
        case 'sined_certificados':
            switch ($user_data['cCase']){
                case 'cod_grupo':
                    $sinedJson->getAlumnosPeriodoCertificado($user_data["sValue"],1);
                    $jsonReturArray =$sinedJson->_datosAlumnos;
                break;
            }
        break;
    }
    
    print json_encode($jsonReturArray);
}

sinedJsonController();

?>
