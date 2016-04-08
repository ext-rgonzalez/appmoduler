<?php
function sistemaJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/sistemaJsonModel.php';
    require_once '../../../app/principalFunction.php';
    require_once '../../../app/session.php';
    Session::init();
    $sistemaJson = new ModeloJsonSistema();
    $user_data = helper_user_data('nuevoRegistro');
    switch ($user_data["dTabla"]){
        case 'sys_menu_sub':
            switch ($user_data['cCase']){
                case 'editMenuHeader':
                    $sistemaJson->setMenuHeader($user_data["sValue"],$user_data["sId"]);
                    $jsonReturArray = array("a"=>1);
                break;
            }
	   break;
        case 'general':
            switch ($user_data['cCase']){
                case 'deleteRegistro':
                    $sistemaJson->deleteDatos($user_data["cTable"],$user_data["sValue"]);
                    $jsonReturArray =$sistemaJson->_datosAfectados;
                break;
                case 'inactiveRegistro':
                    $sistemaJson->inactiveDatos($user_data["cTable"],$user_data["sValue"],$user_data["sEst"]);
                    $jsonReturArray =$sistemaJson->_datosAfectados;
                break;
                case 'ActualizarCombo':
                    $sistemaJson->ActualizarCombo($user_data["sValue"],Session::get('cod'));
                    $jsonReturArray =$sistemaJson->_datosResultado;
                break;
            }
        break;
        case 'sys_agenda':
            switch ($user_data['cCase']) {
                case 'nueva_agenda':
                    $sistemaJson->InsertaEvento($user_data,Session::get('cod'));
                    $jsonReturArray =$sistemaJson->_datosAfectados;
                break;
                case 'edita_agenda':
                    $sistemaJson->EditaEvento($user_data,Session::get('cod'));
                    $jsonReturArray =$sistemaJson->_datosAfectados;
                break;
                case 'elimina_agenda':
                    $sistemaJson->EliminaEvento($user_data,Session::get('cod'));
                    $jsonReturArray =$sistemaJson->_datosAfectados;
                break;
                case 'edita_drag_agenda':
                    $sistemaJson->EditaDragEvento($user_data,Session::get('cod'));
                    $jsonReturArray =$sistemaJson->_datosAfectados;
                break;
                case 'consultaAgenda':
                    $sistemaJson->getAgenda(Session::get('cod'));
                    $jsonReturArray =$sistemaJson->_datosAgenda;
                break;
            }
        break;    
    }
    print json_encode($jsonReturArray);
}

sistemaJsonController();

?>
