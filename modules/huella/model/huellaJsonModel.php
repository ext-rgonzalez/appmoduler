<?php
class ModeloJsonFacturacion extends DBAbstractModel {
    public $_clienteFacturacion;
    public $_numeracionFacturacion;
    public $_itemFacturacion;
    public $_plazoFacturacion;
    
    public function getClienteFacturacion($codCliente = 0){
        #traemos los datos del cliente para llenar la factura
        $this->rows  = "";
        $this->query = "";
        $this->query = "SELECT t1.cod_cliente,t2.nom_tipopago,t2.cod_tipopago,DATE(now()) fecActual,
                               DATE(DATE_ADD(now(), INTERVAL t2.num_dias_tipopago DAY)) as fechaVencimiento  
                          FROM fa_cliente as t1, fa_tipopago as t2
                         WHERE t1.cod_tipopago = t2.cod_tipopago
                           AND t1.cod_cliente = " .$codCliente. "";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
                foreach ($this->rows as $pro=>$va) {
                    $this->_clienteFacturacion[$pro] = $va;
                }
        }
    }
    
    public function getNumeracionFacturacion(){
        #traemos los datos del cliente para llenar la factura
        $this->rows  = "";
        $this->query = "";
        $this->query = "SELECT cod_numeracion, concat(pre_numeracion,(IF(ind_auto_numeracion=1,(num_sig_numeracion+1),num_sig_numeracion))) numeracion  
                          FROM fa_numeracion
                         WHERE cod_estado='AAA'
                           AND ind_preferida_numeracion=1";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
                foreach ($this->rows as $pro=>$va) {
                    $this->_numeracionFacturacion[$pro] = $va;
                }
        }
    }
    
    public function getRefItemFacturacion($cod_item = 0){
        #traemos los datos del cliente para llenar la factura
        $this->rows  = "";
        $this->query = "";
        $this->query = "SELECT ref_item, imp_venta, '1' as cantidad  
                          FROM fa_item
                         WHERE cod_item = " .$cod_item. "";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
                foreach ($this->rows as $pro=>$va) {
                    $this->_itemFacturacion[$pro] = $va;
                }
        }
    }
    
    public function getPlazoFacturacion($cod_plazo = 0){
        #traemos los datos del cliente para llenar la factura
        $this->rows  = "";
        $this->query = "";
        $this->query = "SELECT DATE(now()) fecActual,
                               DATE(DATE_ADD(now(), INTERVAL num_dias_tipopago DAY)) as fechaVencimiento  
                          FROM fa_tipopago
                         WHERE cod_tipopago = " .$cod_plazo. "";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
                foreach ($this->rows as $pro=>$va) {
                    $this->_plazoFacturacion[$pro] = $va;
                }
        }
        
    }
    function __construct(){
        $this->db_name = 'appmoduler';
    }

    function __destruct(){
        unset($this);
    }
}
?>
