<?php
function facturacionJsonController(){
    require_once "../../../core/db_abstract_model.php"; 
    require_once '../model/facturacionJsonModel.php';
    require_once '../../../app/principalFunction.php';
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
                case 'num_factura': 
                    $facturacionJson->getNumeracionFacturacion();
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
            }
        break;
    }
    
    print json_encode($jsonReturArray);
}

facturacionJsonController();

?>
