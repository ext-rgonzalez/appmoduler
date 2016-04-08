<?php
function helpdeskJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/HelpdeskJsonModel.php';
    require_once '../../../app/principalFunction.php';
    require_once '../../../view/view.php';
    date_default_timezone_set("America/Bogota");
    require_once '../../../app/session.php';
    Session::init();
    $HelpdeskJson = new ModeloJsonHelpdesk();
    $user_data = helper_user_data('nuevoRegistro');
    switch ($user_data["dTabla"]){
        case 'general':
            switch ($user_data['cCase']){
                case 'deleteRegistro':
                    $HelpdeskJson->deleteDatos($user_data["cTable"],$user_data["sValue"]);
                    $jsonReturArray =$HelpdeskJson->_datosAfectados;
                break;
                case 'inactiveRegistro':
                    $HelpdeskJson->inactiveDatos($user_data["cTable"],$user_data["sValue"],$user_data["sEst"]);
                    $jsonReturArray =$HelpdeskJson->_datosAfectados;
                break;
                case 'ActualizarCombo':
                    $sinedJson->ActualizarCombo($user_data["sValue"],Session::get('cod'));
                    $jsonReturArray =$sinedJson->_datosResultado;
                break;
            }
        break;
        case 'cron':
            switch ($user_data['cCase']){
                case 'cronHelpDesk':
                    $dataIn=array();$data=array();
                    $HelpdeskJson->consultarDatGenerHelpDesk();
                    recuperaEmailHelpDesk($HelpdeskJson->_datosGenerales,$data);
                    $HelpdeskJson->insertaTicketSoporte($data,$HelpdeskJson->_datosGenerales);
                    $jsonReturArray=array();
                break;
            }
        break;
        case 'hd_servicio':
            switch ($user_data['cCase']){
                case 'nuevaServicio':
                    $jsonReturArray=array("1"=>1);
                break;
            }
        break;
    }
    print json_encode($jsonReturArray);
}
helpdeskJsonController();
?>
