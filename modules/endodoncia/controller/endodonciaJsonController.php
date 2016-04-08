<?php
function endodonciaJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/endodonciaJsonModel.php';
    require_once '../../../app/principalFunction.php';
    require_once '../../../app/session.php';
    Session::init();
    header('Access-Control-Allow-Origin: *');
    $endodonciaJson = new ModeloJsonendodoncia();
    $user_data = helper_user_data('nuevoRegistro');
    switch ($user_data["dTabla"]){
        case 'general':
            switch ($user_data['cCase']){
                case 'deleteRegistro':
                    $endodonciaJson->deleteDatos($user_data["cTable"],$user_data["sValue"]);
                    $jsonReturArray =$endodonciaJson->_datosAfectados;
                break;
                case 'inactiveRegistro':
                    $endodonciaJson->inactiveDatos($user_data["cTable"],$user_data["sValue"],$user_data["sEst"]);
                    $jsonReturArray =$endodonciaJson->_datosAfectados;
                break;
                case 'ActualizarCombo':
                    $endodonciaJson->ActualizarCombo($user_data["sValue"],Session::get('cod'));
                    $jsonReturArray =$endodonciaJson->_datosResultado;
                break;
            }
        break;
        case 'endodoncia_config_dental':
            switch ($user_data['cCase']){
                case 'consultaOdontograma':
                    $endodonciaJson->getOdontograma($user_data["sValue"]);
                    $jsonReturArray =$endodonciaJson->_datosResultado;
                break;
                case 'consultaDienteHistoria':
                    $endodonciaJson->getOdontogramaHistoria($user_data["sValue"],0,0,$user_data["cod_paciente"]);
                    $jsonReturArray =$endodonciaJson->_datosResultado;
                break;
                case 'consultaDienteHistoriaDatos':
                    $cod_paciente = isset($user_data["cod_paciente"]) ? $user_data["cod_paciente"] : 0;
                    $endodonciaJson->getOdontogramaHistoria($user_data["sValue"],1,$user_data["cod_historia_clinica"],$cod_paciente);
                    $jsonReturArray =$endodonciaJson->_datosResultado;
                break;
                case 'NuevaConfigDental':
                    $jsonReturArray =array("row"=>1);
                break;
            }
        break;
        case 'endodoncia_config_medicamentos':
            switch ($user_data['cCase']){
                case 'consultaMedicamentos':
                    $endodonciaJson->getMedicamentos($user_data["term"]);
                    $jsonReturArray =$endodonciaJson->_datosResultado;
                break;
            }
        break;
        case 'endodoncia_historia_clinica':
            switch ($user_data['cCase']) {
                case 'NuevaHistoriaClinica':
                    $endodonciaJson->getMetMedicamentos();
                    $jsonReturArray =$endodonciaJson->_datosResultado;
                break;
                case 'consultaHistoriaPaciente':
                    $endodonciaJson->getDatosHistoriaPaciente($user_data["sValue"]);
                    $jsonReturArray=$endodonciaJson->_datosHistoriaClinica;
                break;
                case 'no_cod_dia_1':
                    $endodonciaJson->getDiagnosticos($user_data['sValue']);
                    $jsonReturArray =$endodonciaJson->_datosDiagnosticos;
                break; 
                case 'no_cod_tej_bla[]':
                    $jsonReturArray =array("row"=>1);
                break;
                case 'no_cod_tej_den[]':
                    $jsonReturArray =array("row"=>1);
                break;
                case 'no_cod_tej_per[]':
                    $jsonReturArray =array("row"=>1);
                break;
                case 'no_cod_tej_peri[]':
                    $jsonReturArray =array("row"=>1);
                break;
                case 'no_cod_tej_pul[]':
                    $jsonReturArray =array("row"=>1);
                break;
            }
        break;
        case 'endodoncia_paciente':
            switch ($user_data['cCase']) {
                case 'consultaPacientes':
                    $endodonciaJson->getPacientes($user_data["term"]);
                    $jsonReturArray =$endodonciaJson->_datosResultado;
                 break;
            }
        break;
        case 'endodoncia_registro_imagenes':
            switch ($user_data['cCase']) {
                case 'eliminarImg':
                    $endodonciaJson->deleteImagen($data = explode(',', $user_data["sValue"]));
                    $jsonReturArray =$endodonciaJson->_datosAfectados;
                 break;
            }
        break;
        case 'endodoncia_paciente_consentimiento':
            switch ($user_data['cCase']) {
                case 'NuevaConsentimientosInfo':
                    $endodonciaJson->getMetDatosConfig(Session::get('cod'));
                    $jsonReturArray =$endodonciaJson->_datosResultado;
                 break;
            }
        break;
        case 'endodoncia_paciente_evolucion':
        switch ($user_data['cCase']) {
                case 'NuevaEvoluciones':
                    $jsonReturArray =array("row"=>1);
                break;
                case 'cod_historia_clinica':
                    $endodonciaJson->getDatosConfigDental($user_data["sValue"]);
                    $jsonReturArray =$endodonciaJson->_datosResultado;
                break;
            }
        break;
        case 'endodoncia_agenda_medica':
            switch ($user_data['cCase']) {
                case 'NuevaAgendaMedica':
                    $jsonReturArray =array("row"=>1);
                break;
            }
        break;
        case 'endodoncia_pago':
            switch ($user_data['cCase']){
                case 'cod_empresa':
                    $endodonciaJson->getNumComprobante(1,$user_data["sValue"]);
                    $jsonReturArray = array("numComprobanteIngreso"=>$endodonciaJson->_numComprobanteIngreso,"numComprobanteEgreso"=>$endodonciaJson->_numComprobanteEgreso);
                break;
                case 'no_cod_paciente':
                    $endodonciaJson->getDatosHistoriaClinicaCliente($user_data["sValue"]);
                    $jsonReturArray =$endodonciaJson->_datosHistoriaClinicaCliente;
                break;
                case 'no_cod_paciente_1':
                    $endodonciaJson->getDatosHistoriaClinicaCliente_1($user_data["sValue"]);
                    $jsonReturArray =$endodonciaJson->_datosHistoriaClinicaCliente;
                break;
                case 'no_cod_historia_clinica[]':
                    $endodonciaJson->getDatosComprobanteFactura($user_data["sValue"]);
                    $jsonReturArray = $endodonciaJson->_datosComprobanteFac[0];
                break; 
                case 'NuevaIngresos':
                    $jsonReturArray =array("row"=>1);
                break;
            }
        break;
        case 'endodoncia_odontologo':
            switch ($user_data['cCase']) {
                case 'validarOdontologo':
                    $endodonciaJson->getValidaOdontologo($user_data);
                    $jsonReturArray = $endodonciaJson->_datosResultado;
                break;
                case 'cod_historia_clinica':
                    $jsonReturArray =array("row"=>1);
                break;
            }
        break;
    }    
    print json_encode($jsonReturArray);
}
sleep(1);
endodonciaJsonController();
?>
