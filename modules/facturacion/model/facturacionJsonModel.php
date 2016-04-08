<?php
class ModeloJsonFacturacion extends DBAbstractModel {
    public $_clienteFacturacion;
    public $_numeracionFacturacion;
    public $_itemFacturacion=array();
    public $_plazoFacturacion;
    public $_numComprobanteIngreso;
    public $_numComprobanteEgreso;
    public $_numExistencias;
    public $_datosComprobanteFac;
    public $_datosFacturaCliente=array();
    public $_datosAfectados=array();
    public $_datosTercero=array();
    public $_data;
    public $_datosEsquema  =array();
    public $_datosResultado=array();
    public $_count         =0;
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
    
    public function getNumeracionFacturacion($cod_empresa,$ind){
        #traemos los datos del cliente para llenar la factura
        $aux = $ind==1 ? "ind_cotizacion=1" : "ind_factura=1"; 
        $this->rows  = "";
        $this->query = "";
        $this->query = "SELECT cod_numeracion, concat(pre_numeracion,(IF(ind_auto_numeracion=1,(num_sig_numeracion),num_inicial_numeracion))) numeracion  
                          FROM fa_numeracion
                         WHERE cod_estado='AAA'
                           AND ind_preferida_numeracion=1
                           AND cod_empresa=".$cod_empresa."
                           AND ".$aux;
        $this->get_results_from_query();
        if(count($this->query) >= 1){
                foreach ($this->rows as $pro=>$va) {
                    $this->_numeracionFacturacion[$pro] = $va;
                }
        }
    }
    
    public function getRefItemFacturacion($cod_item = 0){
        #traemos los datos item para cargar los valores
        $this->rows  = "";
        $this->query = "";
        $this->query = "SELECT count(1)  as item
                          FROM fa_inventario 
                         WHERE cod_item=".$cod_item;
        $this->get_results_from_query();
        if(count($this->query) >= 1):
            foreach ($this->rows as $pro=>$va):
                $this->_data[$pro] = $va;
            endforeach;
        endif; 
        if($this->_data[0]["item"] > 0):
            $this->rows  = "";
            $this->query = "";
            $this->query = "SELECT t1.ref_item, t2.imp_pro_ponderado_inventario as imp_venta, '1' as cantidad,t2.cod_inventario  
                              FROM fa_item as t1, fa_inventario as t2
                             WHERE t1.cod_item          =t2.cod_item
                               AND t1.cod_item          =".$cod_item."
                               AND existencia_inventario>0";
            $this->get_results_from_query();
            if(count($this->query) >= 1):
                foreach ($this->rows as $pro=>$va):
                    $this->_itemFacturacion[$pro] = $va;
                endforeach;
            endif;
        else:
            $this->rows  = "";
            $this->query = "";
            $this->query = "SELECT ref_item, imp_venta as imp_venta, '1' as cantidad,'' as cod_inventario  
                              FROM fa_item 
                             WHERE cod_item          =".$cod_item;
            $this->get_results_from_query();
            if(count($this->query) >= 1):
                foreach ($this->rows as $pro=>$va):
                    $this->_itemFacturacion[$pro] = $va;
                endforeach;
            endif;
        endif;
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
    
    public function getNumComprobante($tipCom=0,$cod_empresa){
        #traemos el numero del siguiente comprobante configurado en el sistema
        $this->row  = "";
        $this->query = "";
        $tipCom==1 ? $cAux='num_sig_recibocaja+1' : $cAux='num_sig_compago+1';
        $this->query = "SELECT CONCAT(pre_sig_comp_ingreso,(num_sig_comp_ingreso)) as numComprobanteIngreso,CONCAT(pre_sig_comp_egreso,(num_sig_comp_egreso)) as numComprobanteEgreso
                          FROM fa_config 
                         WHERE cod_estado='AAA' 
                           AND cod_empresa=".$cod_empresa;
        $this->get_result_from_query();
        $this->_numComprobanteIngreso = $this->row['numComprobanteIngreso'];
        $this->_numComprobanteEgreso = $this->row['numComprobanteEgreso'];
    }
    
    public function getDatosComprobanteFactura($numFac=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT (sum(t1.sub_total_factura) - sum(t1.sub_totaldes_factura)) + sum(t1.imp_factura) as imp_factura, t1.imp_cancelado, t1.imp_adeudado, t2.nom_cliente, t2.cod_cliente, t1.imp_factura as no_impuesto, t1.sub_total_factura 
                        FROM fa_factura as t1,fa_cliente as t2
                       WHERE t1.cod_cliente=t2.cod_cliente
                         AND t1.cod_factura=".$numFac."
                    GROUP BY t1.cod_factura";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
                foreach ($this->rows as $pro=>$va) {
                    $this->_datosComprobanteFac[$pro] = $va;
                }
        }
    }
    
    public function getDatosFacturaCliente($numCli=0){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT t1.cod_factura, concat('Codigo Factura: ',t1.cod_factura,' - ','Cliente: ', t2.nom_cliente) as value
                        FROM fa_factura as t1, fa_cliente as t2 
                       WHERE t1.cod_cliente=t2.cod_cliente
                         AND t1.ind_cotizacion=0
                         AND t2.cod_cliente=".$numCli."
                         AND t1.cod_estado='FAA'
                         AND t1.imp_adeudado>0";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosFacturaCliente[$pro]=$va;
            }
        }
    }
    
    public function getDatosTercero($codCliente){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT cod_cliente_asociado,nom_cliente_asociado,email_cliente_asociado
                        FROM fa_cliente_asociado
                       WHERE cod_estado = 'AAA'
                         AND cod_cliente=".$codCliente."";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosTercero[$pro]=$va;
            }
        }
    }
    public function getNumExitencias($item=0){
        #traemos el numero de existencias disponibles en inventario para este item
        $this->row  = "";
        $this->query = "";
        $this->query = "SELECT existencia_inventario as num_existencias FROM fa_inventario where cod_item=".$item."";
        $this->get_result_from_query();
        $this->_numExistencias = $this->row['num_existencias'];
    }
    
    public function deleteDatos($table,$id){
        #eliminamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "DELETE FROM ".$table." where cod_".str_replace("fa_","",$table)."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function inactiveDatos($table,$id,$est){
        #desactivamos el registro segun tabla e id
        $this->row  = "";
        $this->query = "";
        $this->query = "UPDATE ".$table." SET cod_estado='".$est."' where cod_".str_replace("fa_","",$table)."=".$id."";
        $this->execute_single_query();
        $this->_datosAfectados["row"] = $this->rowAffected;
    }
    
    public function ActualizarCombo($esq="",$cod_usuario){
        $this->rows ="";
        $this->query="";
        $this->query="SELECT sql_esquema,nro_columnas
                        FROM sys_tablareferencia 
                       WHERE nom_referencia='".$esq."'";
        $this->get_results_from_query();
        if(count($this->query) >= 1){
            foreach ($this->rows as $pro=>$va) {
                $this->_datosEsquema[$pro]=$va;
            }
        }
        $this->rows ="";
        $this->query="";
        $this->query=str_replace("codUsu",$cod_usuario,$this->_datosEsquema[0]["sql_esquema"]);
        $this->get_results_from_query();
        if(count($this->query) >= 1):            
            foreach ($this->rows as $pro=>$va):
                $this->_count=0;
                foreach($va as $pro1=>$va1):
                    $this->_datosResultado[$pro]["col".$this->_count]=$va1;
                    $this->_count++;  
                endforeach;                 
            endforeach;            
        endif;
    }
    
    function __construct(){
        $this->db_name = 'appmoduler';
    }

    function __destruct(){
        unset($this);
    }
}
?>
