<?php
function stkJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/stkJsonModel.php';
    require_once '../../../app/principalFunction.php';
    require_once '../../../app/session.php';
    Session::init();
    $stkJson = new ModeloJsonStk();
    $user_data = helper_user_data('nuevoRegistro');
    switch ($user_data["dTabla"]){
        case 'general':
            switch ($user_data['cCase']){
                case 'deleteRegistro':
                    $stkJson->deleteDatos($user_data["cTable"],$user_data["sValue"]);
                    $jsonReturArray =$stkJson->_datosAfectados;
                break;
                case 'inactiveRegistro':
                    $stkJson->inactiveDatos($user_data["cTable"],$user_data["sValue"],$user_data["sEst"]);
                    $jsonReturArray =$stkJson->_datosAfectados;
                break;
                case 'ActualizarCombo':
                    $sistemaJson->ActualizarCombo($user_data["sValue"],Session::get('cod'));
                    $jsonReturArray =$sistemaJson->_datosResultado;
                break;
            }
        break;
    }
    
    print json_encode($jsonReturArray);
}
stkJsonController();
?>
