<?php
function datoleeJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/datoleeJsonModel.php';
    require_once '../../../app/principalFunction.php';
    header('Access-Control-Allow-Origin: *');
    $datoleeJson = new ModeloJsonDatolee();
    $user_data = helper_user_data('nuevoRegistro');
    switch ($user_data["dTabla"]){
        case 'general':
            switch ($user_data['cCase']){
                case 'deleteRegistro':
                    $datoleeJson->deleteDatos($user_data["cTable"],$user_data["sValue"]);
                    $jsonReturArray =$datoleeJson->_datosAfectados;
                break;
                case 'inactiveRegistro':
                    $datoleeJson->inactiveDatos($user_data["cTable"],$user_data["sValue"],$user_data["sEst"]);
                    $jsonReturArray =$datoleeJson->_datosAfectados;
                break;
            }
        break;
        case 'datolee_voto':
            switch ($user_data['cCase']){
                case 'nuevoVoto':
                    $datoleeJson->setVoto($user_data); 
                    $jsonReturArray =$datoleeJson->_datosAfectados;
                break;
                case 'nuevoVotoApp':
                    $user_data_1 = (json_decode($_POST["obj"]));
                        $user_data["obj"] = objeto_a_array($user_data_1) ;
                    $datoleeJson->setVotoApp($user_data);
                    $jsonReturArray =$datoleeJson->_datosAfectados;
                break;
                case 'sys_ciudad':
                    $datoleeJson->getDatosCiudad($user_data);
                    $jsonReturArray=$datoleeJson->_datosCiudad;
                break;
                case 'sys_comuna':
                    $datoleeJson->getDatosComuna($user_data);
                    $jsonReturArray=$datoleeJson->_datosComuna;
                break;
            }
        break;
        // objetos propios de la app
        case 'sys_usuario':
            switch ($user_data["cCase"]){
                case 'validaSession':
                    $datoleeJson->getDatosSession($user_data);
                    $jsonReturArray=$datoleeJson->_datosSession;
                break;
            }
        break;
        case 'datolee_sector':
            switch ($user_data["cCase"]){
                case 'sectores':
                    $datoleeJson->getDatosSectores($user_data);
                    $jsonReturArray=$datoleeJson->_datosSectores;
                break;
            }
        break;
        case'sys_usuario_empresa':
            switch ($user_data["cCase"]){
                case 'consultaEmpresa':
                    $datoleeJson->getEmpresa($user_data);
                    $jsonReturArray=$datoleeJson->_datosEmpresa;
                break;
            }
        break;
        case 'crm_contactos':
            switch ($user_data["cCase"]){
                case 'reporte':
                    $datoleeJson->getReporte($user_data);
                    $jsonReturArray=$datoleeJson->_datosReporte;
                break;
            }
        break;
        case 'datolee_conexion':
            switch ($user_data["cCase"]){
                case 'conexion':
                    $jsonReturArray=array("conexion"=>1);
                break;
            }
        break;
    // getDatosProductos
    }   
   
    print json_encode($jsonReturArray);
}
datoleeJsonController();

?>
