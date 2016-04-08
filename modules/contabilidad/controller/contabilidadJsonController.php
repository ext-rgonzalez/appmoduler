<?php
function contabilidadJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/ContabilidadJsonModel.php';
    require_once '../../../app/principalFunction.php';
    $ContabilidadJson = new ModeloJsonContabilidad();
    $user_data = helper_user_data('nuevoRegistro');
    switch ($user_data["dTabla"]){
        case 'general':
            switch ($user_data['cCase']){
                case 'deleteRegistro':
                    $ContabilidadJson->deleteDatos($user_data["cTable"],$user_data["sValue"]);
                    $jsonReturArray =$ContabilidadJson->_datosAfectados;
                break;
                case 'inactiveRegistro':
                    $ContabilidadJson->inactiveDatos($user_data["cTable"],$user_data["sValue"],$user_data["sEst"]);
                    $jsonReturArray =$ContabilidadJson->_datosAfectados;
                break;
            }
        break;
    }
    print json_encode($jsonReturArray);
}
contabilidadJsonController();
?>
