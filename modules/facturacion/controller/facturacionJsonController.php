<?php
function facturacionJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/facturacionJsonModel.php';
    require_once '../../../app/principalFunction.php';
    require_once '../../../app/session.php';
    Session::init();
    $facturacionJson = new ModeloJsonFacturacion();
    $user_data = helper_user_data('nuevoRegistro');
    switch ($user_data["dTabla"]){
        case 'fa_factura':
            switch ($user_data["cCase"]){
                case 'cod_cliente':
                    $facturacionJson->getClienteFacturacion($user_data["sValue"]);
                    for($x=0;$x<count($facturacionJson->_clienteFacturacion);$x++){
                        $jsonReturArray = $facturacionJson->_clienteFacturacion[$x];
                    }
                break;
                case 'cod_empresa':
                    $facturacionJson->getNumeracionFacturacion($user_data["sValue"],1);
                    for($x=0;$x<count($facturacionJson->_numeracionFacturacion);$x++){
                        $jsonReturArray = $facturacionJson->_numeracionFacturacion[$x];
                    }
                break;
                case 'no_cod_item[]':
                    $facturacionJson->getRefItemFacturacion($user_data["sValue"]);
                    for($x=0;$x<count($facturacionJson->_itemFacturacion);$x++){
                        $jsonReturArray = $facturacionJson->_itemFacturacion[$x];
                    }
                break;
                case 'cod_tipopago':
                    $facturacionJson->getPlazoFacturacion($user_data["sValue"]);
                    for($x=0;$x<count($facturacionJson->_plazoFacturacion);$x++){
                        $jsonReturArray = $facturacionJson->_plazoFacturacion[$x];
                    }
                break;
                case 'no_cod_impuesto[]':
                    $jsonReturArray=array("1"=>1);
                break;
                case 'no_cod_descuento[]':
                    $jsonReturArray=array("1"=>1);
                break;
                case 'num_inventario':
                    $facturacionJson->getNumExitencias($user_data["sValue"]);
                    $jsonReturArray = array("numExistencias"=>$facturacionJson->_numExistencias);
                break;
            }
        break;
        case 'fa_facturacion':
            switch ($user_data["cCase"]){
                case 'cod_cliente':
                    $facturacionJson->getClienteFacturacion($user_data["sValue"]);
                    for($x=0;$x<count($facturacionJson->_clienteFacturacion);$x++){
                        $jsonReturArray = $facturacionJson->_clienteFacturacion[$x];
                    }
                break;
                case 'cod_empresa':
                    $facturacionJson->getNumeracionFacturacion($user_data["sValue"],0);
                    for($x=0;$x<count($facturacionJson->_numeracionFacturacion);$x++){
                        $jsonReturArray = $facturacionJson->_numeracionFacturacion[$x];
                    }
                break;
                case 'no_cod_item[]':
                    $facturacionJson->getRefItemFacturacion($user_data["sValue"]);
                    for($x=0;$x<count($facturacionJson->_itemFacturacion);$x++){
                        $jsonReturArray = $facturacionJson->_itemFacturacion[$x];
                    }
                break;
                case 'cod_tipopago':
                    $facturacionJson->getPlazoFacturacion($user_data["sValue"]);
                    for($x=0;$x<count($facturacionJson->_plazoFacturacion);$x++){
                        $jsonReturArray = $facturacionJson->_plazoFacturacion[$x];
                    }
                break;
                case 'no_cod_impuesto[]':
                    $jsonReturArray=array("1"=>1);
                break;
                case 'no_cod_descuento[]':
                    $jsonReturArray=array("1"=>1);
                break;
                case 'num_inventario':
                    $facturacionJson->getNumExitencias($user_data["sValue"]);
                    $jsonReturArray = array("numExistencias"=>$facturacionJson->_numExistencias);
                break;
            }
        break;
        case 'fa_pago':
            switch ($user_data['cCase']){
                case 'cod_empresa':
                    $facturacionJson->getNumComprobante(1,$user_data["sValue"]);
                    $jsonReturArray = array("numComprobanteIngreso"=>$facturacionJson->_numComprobanteIngreso,"numComprobanteEgreso"=>$facturacionJson->_numComprobanteEgreso);
                break;
                case 'cod_empresa_1':
                    $facturacionJson->getNumComprobante(2);
                    $jsonReturArray = array("numComprobante"=>$facturacionJson->_numComprobante);
                break;
                case 'no_cod_factura[]':
                    $facturacionJson->getDatosComprobanteFactura($user_data["sValue"]);
                    for($x=0;$x<count($facturacionJson->_datosComprobanteFac);$x++){
                        $jsonReturArray = $facturacionJson->_datosComprobanteFac[$x];
                    }
                break; 
                case 'cod_cliente':
                    $facturacionJson->getDatosFacturaCliente($user_data["sValue"]);
                    $jsonReturArray =$facturacionJson->_datosFacturaCliente;
                break;
                case 'cod_cliente_1':
                    $facturacionJson->getDatosTercero($user_data["sValue"]);
                    $jsonReturArray =$facturacionJson->_datosTercero;
                break;
            }
        break;
        case 'fa_inventario':
            switch ($user_data['cCase']){
                case 'no_cod_impuesto':
                    $jsonReturArray =array("a"=>1);
                break;
                case 'no_cod_retencion[]':
                    $jsonReturArray =array("a"=>1);
                break;
            }
        break;
        case 'fa_inventario_aud':
            switch ($user_data['cCase']){
                case 'no_cod_impuesto':
                    $jsonReturArray =array("a"=>1);
                break;
                case 'no_cod_retencion[]':
                    $jsonReturArray =array("a"=>1);
                break;
            }
        break;
        case 'fa_inventario_ajuste':
            switch ($user_data['cCase']){
                case 'no_cod_impuesto':
                    $jsonReturArray =array("a"=>1);
                break;
                case 'no_cod_retencion[]':
                    $jsonReturArray =array("a"=>1);
                break;
            }
        break;
        case 'general':
            switch ($user_data['cCase']){
                case 'deleteRegistro':
                    $facturacionJson->deleteDatos($user_data["cTable"],$user_data["sValue"]);
                    $jsonReturArray =$facturacionJson->_datosAfectados;
                break;
                case 'inactiveRegistro':
                    $facturacionJson->inactiveDatos($user_data["cTable"],$user_data["sValue"],$user_data["sEst"]);
                    $jsonReturArray =$facturacionJson->_datosAfectados;
                break;
                case 'ActualizarCombo':
                    $facturacionJson->ActualizarCombo($user_data["sValue"],Session::get('cod'));
                    $jsonReturArray =$facturacionJson->_datosResultado;
                break;
            }
        break;
    }
    
    print json_encode($jsonReturArray);
}

facturacionJsonController();

?>
