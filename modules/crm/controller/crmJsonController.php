<?php
function crmJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/crmJsonModel.php';
    require_once '../../../app/principalFunction.php';
    require_once '../../../app/session.php';
    Session::init();
    header('Access-Control-Allow-Origin: *');
    $crmJson = new ModeloJsonCrm();
    $user_data = helper_user_data('nuevoRegistro');
    switch ($user_data["dTabla"]){
        case 'general':
            switch ($user_data['cCase']){
                case 'deleteRegistro':
                    $crmJson->deleteDatos($user_data["cTable"],$user_data["sValue"]);
                    $jsonReturArray =$crmJson->_datosAfectados;
                break;
                case 'inactiveRegistro':
                    $crmJson->inactiveDatos($user_data["cTable"],$user_data["sValue"],$user_data["sEst"]);
                    $jsonReturArray =$crmJson->_datosAfectados;
                break;
                case 'ActualizarCombo':
                    $crmJson->ActualizarCombo($user_data["sValue"],Session::get('cod'));
                    $jsonReturArray =$crmJson->_datosResultado;
                break;
            }
        break;
        case 'fa_cliente':
            switch ($user_data['cCase']){
                case 'no_cod_productos':case 'no_cod_productos[]':
                    $crmJson->getSubProductosMedios($user_data["sValue"]);
                    $jsonReturArray =$crmJson->_datosSubProductos;
                break;
                case'nuevoContacto':
                    $crmJson->setContacto($user_data);                    
                    $jsonReturArray =$crmJson->_datosAfectados;
                break;
            }
        break;
        case 'crm_contacto':
            switch ($user_data['cCase']){
                case 'no_cod_productos':
                    $crmJson->getSubProductosMedios($user_data["sValue"]);
                    $jsonReturArray =$crmJson->_datosSubProductos;
                break;
            }
        break;
        // objetos propios de la app
        case 'sys_usuario':
            switch ($user_data["cCase"]){
                case 'validaSession':
                    $crmJson->getDatosSession($user_data);
                    $jsonReturArray=$crmJson->_datosSession;
                break;
            }
        break;
        case 'crm_productos':
            switch ($user_data["cCase"]){
                case 'productos':
                    $crmJson->getDatosProductos($user_data);
                    $jsonReturArray=$crmJson->_datosProductos;
                break;
            }
        break;
        case 'crm_subproductos':
            switch ($user_data["cCase"]){
                case 'subproductos':
                    $crmJson->getDatosSubProductos($user_data);
                    $jsonReturArray=$crmJson->_datosSubProductos;
                break;
            }
        break;
        case 'crm_medios':
            switch ($user_data["cCase"]){
                case 'medios':
                    $crmJson->getDatosMedios($user_data);
                    $jsonReturArray=$crmJson->_datosMedios;
                break;
            }
        break;
        case'sys_usuario_empresa':
            switch ($user_data["cCase"]){
                case 'consultaEmpresa':
                    $crmJson->getEmpresa($user_data);
                    $jsonReturArray=$crmJson->_datosEmpresa;
                break;
            }
        break;
        case 'crm_contactos':
            switch ($user_data["cCase"]){
                case 'reporte':
                    $crmJson->getReporte($user_data);
                    $jsonReturArray=$crmJson->_datosReporte;
                break;
            }
        break;
    // getDatosProductos
    }    
    print json_encode($jsonReturArray);
}
crmJsonController();
?>
