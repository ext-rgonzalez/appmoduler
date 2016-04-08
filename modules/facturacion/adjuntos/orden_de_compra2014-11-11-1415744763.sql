-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 07-11-2014 a las 11:56:45
-- Versión del servidor: 5.6.12-log
-- Versión de PHP: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `appmoduler`
--
CREATE DATABASE IF NOT EXISTS `appmoduler` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `appmoduler`;

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `pbActualizaConfig`(cCodConfig int(8))
BEGIN
	UPDATE fa_config 
       SET cod_estado = 'CBB'
     WHERE cod_config <> cCodConfig;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pbActualizaEstado`(cNomTabla varchar(45), cCampo varchar(45),cValor varchar(45))
BEGIN
	UPDATE cNomTabla 
       SET cCampo = cValor;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pbActualizaNumeracion`(cCodNumeracion int(8))
BEGIN
	UPDATE fa_numeracion 
       SET ind_preferida_numeracion = 0,
           cod_estado               = 'BBB'
     WHERE cod_numeracion <> cCodNumeracion;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pbActualizaSigNum`()
BEGIN
-- Actualizamos el siguiente numero de la configuracion de facturacion prefererida
 UPDATE fa_numeracion 
	SET num_sig_numeracion=(SELECT num_sig_numeracion+1
                              FROM fa_numeracion
							 WHERE cod_estado='AAA'
							   AND ind_preferida_numeracion=1)
  WHERE cod_estado='AAA'
    AND ind_preferida_numeracion=1;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pbNuevoAlquiler`(cCodCliente int(8),cCodVehiculo int(9),cCase int(1))
BEGIN
 DECLARE cHuella varchar(120);
 CASE TRIM(cCase)
  WHEN 1 THEN
-- Seleccionamos la huella del registro
  SELECT huella_cliente_vehiculo
    INTO cHuella
    FROM hue_cliente_vehiculo
   WHERE cod_cliente=cCodCliente
     AND cod_vehiculo=cCodVehiculo
     AND cod_estado='AAA'; 
-- Actualizamos el estado del vehiculo
  UPDATE hue_vehiculo 
     SET cod_estado='VAA'
   WHERE cod_vehiculo = cCodVehiculo;
-- Actualizamos el estado del cliente y la huella
  UPDATE hue_cliente 
     SET cod_estado = 'CVA', huella_cliente=cHuella  
   WHERE cod_cliente = cCodCliente;
 WHEN 2 THEN
	-- Seleccionamos la huella del registro
  SELECT huella_cliente_vehiculo
    INTO cHuella
    FROM hue_cliente_vehiculo
   WHERE cod_cliente=cCodCliente
     AND cod_vehiculo=cCodVehiculo
     AND cod_estado='AAA'; 
-- Actualizamos el estado del vehiculo
  UPDATE hue_vehiculo 
     SET cod_estado='AAA'
   WHERE cod_vehiculo = cCodVehiculo;
-- Actualizamos el estado del cliente y la huella
  UPDATE hue_cliente 
     SET cod_estado = 'AAA', huella_cliente=cHuella  
   WHERE cod_cliente = cCodCliente;
END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pbNuevoServicio`(cCodServicio int(9),cCodUsuario int(9),cCodUsuarioRegistro int(9), cCodCliente int(8), cCadServicio varchar(20), cIndAsigna int(1), cCodReferencia varchar(120),cCodEstado varchar(3))
BEGIN
 DECLARE cNomCliente varchar(120);
 DECLARE cDescripcion varchar(2000) DEFAULT '';
 DECLARE cFecAlta timestamp DEFAULT now();
 DECLARE cEstado varchar(120);
-- seleccionamos cliente para hacer referencia al servicio
 SELECT concat(nom_cliente, email_cliente) into cNomcliente
   FROM fa_cliente
  WHERE cod_cliente = cCodCliente;
-- seleccionamos el estado del servicio
 SELECT des_estado into cEstado
   FROM sys_estado
  WHERE cod_estado = cCodEstado; 
-- armamos el mensaje del servicio
 SET cDescripcion = concat('Ha sido asignado el siguiente servicio a su Service desk - ',
                           ' Servicio: ', cCadServicio,
						   ' Cliente: ',cNomCliente,
						   ' Fecha de apertura: ', cFecAlta,
						   ' Estado actual: ', cEstado);
-- se inserta el servicio
 INSERT INTO hd_servicio(cod_servicio,des_servicio, fec_servicio, ind_asigna, cod_referencia, cod_estado, cod_usuario)
	  VALUES (cCodServicio,cDescripcion,cFecAlta,cIndAsigna,cCodReferencia,cCodEstado,cCodUsuarioRegistro);
-- se inserta la asignacion
 INSERT INTO hd_asignacion(cod_servicio,cod_usuario,fec_asignacion,cod_usuario_asigna)
	  VALUES (cCodServicio,cCodUsuario,cFecAlta,cCodUsuarioRegistro);
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaAyudaFormulario`(codFrm int(2), codUsu int(8), iTipAyuda int(1)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE cCadenaAyudaTit  longtext DEFAULT '';
 DECLARE cCadenaAyudaDes  longtext DEFAULT '';
 DECLARE cCadenaFinal     longtext DEFAULT '';
  SELECT t2.tit_formulario, t2.des_formulario  
    INTO cCadenaAyudaTit, cCadenaAyudaDes
    FROM sys_formulario as t2, sys_usuario as t1, 
		 sys_menu_sub as t3, sys_usuario_menu_sub as t4 
   WHERE t3.cod_menu_sub             = t4.cod_menu_sub
     AND CASE iTipAyuda 
		  WHEN 1 THEN t3.cod_formulario          = t2.cod_formulario 
		  WHEN 2 THEN t3.cod_formulario_asociado = t2.cod_formulario 		
		 END
     AND CASE iTipAyuda 
		  WHEN 1 THEN t3.cod_formulario          = codFrm 
		  WHEN 2 THEN t3.cod_formulario_asociado = codFrm 		
		 END
	 AND t4.cod_usuario              = t1.cod_usuario
     AND t4.cod_usuario              = codUsu
	 AND t1.ind_ayuda                = 1;
  set cCadenaFinal  = CONCAT('<div class="well" style="text-align:justify;">
			                    <button class="close" data-dismiss="alert">x</button>
			                    <h1 class="semi-bold">',cCadenaAyudaTit,'</h1>
		                        <p>',cCadenaAyudaDes,'</p>
                               </div>'); 
RETURN cCadenaFinal;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaBoton`(codFrm int, codUsu int(8)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done  BOOL DEFAULT FALSE;
 DECLARE cCadenaBoton    longtext DEFAULT '';
 DECLARE cCadenaFinBoton longtext DEFAULT '';
 DECLARE Boton CURSOR FOR 
 SELECT IF(t5.tip_metodos = 'link',
		   CONCAT(' <a href="?app=',t5.href_metodos,'" type="',t5.tip_metodos,'" class="',t5.cla_metodos,'" ',IF(t4.cod_estado='BTI','disabled',''),' id="btoAccion" data="',t5.nom_metodos,'" data-rel="',t5.nom_metodos,'">
                	<span class="btn-label"><i class="',t5.ico_metodos,' white"></i></span> '
                ,btn_metodos,'</a>'),
		   CONCAT(' <button type="',t5.tip_metodos,'" class="',t5.cla_metodos,'"',IF(t4.cod_estado='BTI','disabled',''),' id="btoAccion" data="',t5.nom_metodos,'" data-rel="',t5.nom_metodos,'" ',IF(t5.func_metodos<>'',t5.func_metodos,''),')>
                	<span class="btn-label"><i class="',t5.ico_metodos,' white"></i></span> ',btn_metodos,'
                </button> '))
    FROM sys_usuario as t1, sys_usuario_perfil     as t2,  
         sys_perfil  as t3, sys_perfil_metodos     as t4,
         sys_metodos as t5, sys_formulario_metodos as t6 
   WHERE t1.cod_usuario    = t2.cod_usuario
     AND t2.cod_perfil     = t3.cod_perfil
     AND t2.cod_perfil     = t4.cod_perfil
	 AND t4.cod_metodos    = t5.cod_metodos
     AND t4.cod_metodos    = t6.cod_metodos
     AND t1.cod_usuario    = codUsu
     AND t6.cod_formulario = codFrm
ORDER BY t6.cod_formulario_metodos ASC;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done  = TRUE;
 OPEN Boton;
  BotonLoop: LOOP
  FETCH Boton INTO cCadenaBoton; 
   IF done THEN
    CLOSE Boton;
    LEAVE BotonLoop;
   END IF;
   set cCadenaFinBoton  = CONCAT(cCadenaFinBoton,cCadenaBoton); 
  END LOOP;
RETURN cCadenaFinBoton;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaCabTabla`(cNomView varchar(45)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done  BOOL DEFAULT FALSE;
 DECLARE cCadenaTabla    longtext DEFAULT '';
 DECLARE cCadenaFinTabla longtext DEFAULT '';
 DECLARE cabTabla CURSOR FOR 
  SELECT CONCAT('<th>',COLUMN_NAME,'</th>')
    FROM information_schema.COLUMNS
   WHERE TABLE_SCHEMA = 'appmoduler'
     AND TABLE_NAME = cNomView;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done  = TRUE;
 OPEN cabTabla;
  cabTablaLoop: LOOP
  FETCH cabTabla INTO cCadenaTabla; 
   IF done THEN
    CLOSE cabTabla;
    LEAVE cabTablaLoop;
   END IF;
   set cCadenaFinTabla  = CONCAT(cCadenaFinTabla,cCadenaTabla); 
  END LOOP;
RETURN CONCAT('<thead><tr>',cCadenaFinTabla,'</tr></thead><tbody>');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaCombo`(cNomTablaEnt varchar(45), codUsu int(8), cCadEmp text) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done1 BOOL DEFAULT FALSE;
 DECLARE cNomTabla     varchar(45) DEFAULT '';
 DECLARE cCodTabla     varchar(45) DEFAULT '';
 DECLARE cNomColumna   varchar(45) DEFAULT '';
 DECLARE cDesColumna   varchar(45) DEFAULT '';
 DECLARE auxColumna    varchar(45) DEFAULT '';
 DECLARE cOption       varchar(2000);
 DECLARE cFinOption    longtext    DEFAULT '';
 DECLARE comboCiudad CURSOR FOR
  SELECT cod_ciudad, nom_ciudad, dpt_ciudad
   FROM sys_ciudad;
 DECLARE comboContrato CURSOR FOR
  SELECT cod_contrato, nom_contrato, des_contrato
   FROM sys_contrato;
 DECLARE comboRegimen CURSOR FOR
  SELECT cod_regimen, nom_regimen, des_regimen
   FROM fa_regimen;
 DECLARE comboModulo CURSOR FOR
  SELECT cod_modulo, nom_modulo, des_modulo
   FROM mod_modulo;
 DECLARE comboMoneda CURSOR FOR
  SELECT cod_moneda, abr_moneda, nom_moneda
   FROM fa_moneda;
 DECLARE comboPerfil CURSOR FOR
  SELECT cod_perfil, nom_perfil, des_perfil
   FROM sys_perfil;
 DECLARE comboEmpresa CURSOR FOR
  SELECT t1.cod_empresa, t1.nom_empresa, t1.nit_empresa
   FROM sys_empresa as t1, sys_usuario_empresa as t2
  WHERE t1.cod_empresa = t2.cod_empresa
    AND t2.cod_usuario = codUsu;
 DECLARE comboMenu CURSOR FOR
  SELECT t1.cod_menu, t1.nom_menu, t1.des_menu
    FROM sys_menu as t1, sys_usuario_menu as t2  
   WHERE t1.cod_menu    =  t2.cod_menu
     AND t2.cod_usuario = codUsu; 
 DECLARE comboMenuSub CURSOR FOR
  SELECT t1.cod_menu_sub, t1.nom_menu_sub, t1.des_menu_sub
    FROM sys_menu_sub as t1, sys_usuario_menu_sub as t2  
   WHERE t1.cod_menu_sub    =  t2.cod_menu_sub
     AND t2.cod_usuario = codUsu
GROUP BY t2.cod_menu_sub; 
 DECLARE comboUsuario CURSOR FOR
  SELECT t1.cod_usuario, t2.nom_usuario, t2.ape_usuario
	FROM sys_usuario_empresa as t1, sys_usuario as t2
   WHERE t1.cod_usuario = t2.cod_usuario
     AND t1.cod_empresa in(cCadEmp)
	 AND t1.cod_usuario <> codUsu
GROUP BY t1.cod_usuario; 
 DECLARE comboEstado CURSOR FOR
  SELECT cod_estado, CONCAT(cod_estado,' - ', des_estado), mod_estado
   FROM sys_estado;
  DECLARE comboTipoImpuesto CURSOR FOR
   SELECT cod_tipoimpuesto, nom_tipoimpuesto, des_tipoimpuesto
     FROM fa_tipoimpuesto;
 DECLARE comboImpuesto CURSOR FOR
   SELECT t1.cod_impuesto, t1.nom_impuesto, concat(t1.por_impuesto,' - ',t2.nom_tipoimpuesto),t1.por_impuesto
     FROM fa_impuesto t1,fa_tipoimpuesto t2
    WHERE t1.cod_tipoimpuesto = t2.cod_tipoimpuesto;
 DECLARE comboCliente CURSOR for
  SELECT cod_cliente,nom_cliente,nit_cliente
    FROM fa_cliente
   WHERE ind_cliente_cliente = 1; 
 DECLARE comboProveedor CURSOR for
  SELECT cod_cliente,nom_cliente,nit_cliente
    FROM fa_cliente
   WHERE ind_proveedor_cliente = 1; 
 DECLARE comboTipoPago CURSOR for
  SELECT cod_tipopago,nom_tipopago,concat(num_dias_tipopago,' - Dias')
    FROM fa_tipopago;
 DECLARE comboItem CURSOR FOR
  SELECT cod_item, nom_item, concat('Ref: ',ref_item)
    FROM fa_item;
 DECLARE comboCuenta CURSOR FOR
  SELECT cod_cuenta,nom_cuenta,clase_cuenta
    FROM con_cuenta;
 DECLARE comboItemInventaro CURSOR FOR
  SELECT cod_item, nom_item, concat('Ref: ',ref_item)
    FROM fa_item
  WHERE ind_inventario_item=1;
 DECLARE comboUniMedida CURSOR FOR
  SELECT cod_unimedida, nom_unimedida, concat('Pre: ',pre_unimedida)
    FROM fa_unimedida;
 DECLARE comboDescuento CURSOR FOR
   SELECT cod_descuentos, nom_descuento, concat(prc_descuento,' - ',nom_descuento),prc_descuento
     FROM fa_descuentos
	WHERE cast(now() as date) between fec_inicio_descuento and fec_cierre_descuento
      AND cod_estado = 'DAA';
 DECLARE comboRestaurante CURSOR FOR
  SELECT cod_restaurantes, nom_restaurantes, esl_restaurantes
    FROM lc_restaurantes;
 DECLARE comboMetodo CURSOR FOR
  SELECT cod_metodos, nom_metodos, des_metodos
    FROM sys_metodos;
 DECLARE comboFormulario CURSOR FOR
  SELECT cod_formulario, nom_formulario, nom_tabla
    FROM sys_formulario;
 DECLARE comboTipoInput CURSOR FOR
  SELECT cod_tipoInput, nom_tipoInput, esp_tipoInput
    FROM sys_tipoinput;
 DECLARE comboTablaReferencia CURSOR FOR
  SELECT cod_tablareferencia, 'Referencia: ', nom_referencia
    FROM sys_tablareferencia;
 DECLARE comboFrame CURSOR FOR
  SELECT cod_frame, nom_frame, nom_tabla
    FROM sys_frame;
 DECLARE comboCategorias CURSOR FOR
  SELECT cod_categorias, nom_categorias, ''
    FROM lc_categorias;
 DECLARE comboCategoriasSub CURSOR FOR
  SELECT cod_categorias_sub, nom_categorias_sub, ''
    FROM lc_categorias_sub;
 DECLARE comboClienteLC CURSOR FOR
  SELECT cod_cliente, nom_cliente, ape_cliente
    FROM lc_cliente;
 DECLARE comboCotizacion CURSOR FOR
  SELECT t1.cod_factura, concat('Codigo Cotizacion: ',t1.cod_factura),concat('Cliente: ', t2.nom_cliente) 
    FROM fa_factura as t1, fa_cliente as t2 
   WHERE t1.cod_cliente=t2.cod_cliente
     AND t1.ind_cotizacion=1;
 DECLARE comboFactura CURSOR FOR
  SELECT t1.cod_factura, concat('Codigo Factura: ',t1.cod_factura),concat('Cliente: ', t2.nom_cliente) 
    FROM fa_factura as t1, fa_cliente as t2 
   WHERE t1.cod_cliente=t2.cod_cliente
     AND t1.ind_cotizacion=0;
-- combos para zerofill de huella hirender
 DECLARE comboTipServicio CURSOR FOR
  SELECT cod_tip_servicio, 'Servicio: ', nom_tip_servicio
    FROM hue_tip_servicio;
 DECLARE comboVehiculoClase CURSOR FOR
  SELECT cod_vehiculo_clase, 'Clase: ', nom_vehiculo_clase
    FROM hue_vehiculo_clase;
 DECLARE comboCombustible CURSOR FOR
  SELECT cod_combustible, 'Combustible: ', nom_combustible
    FROM hue_combustible;
 DECLARE comboVehiculo CURSOR FOR
  SELECT cod_vehiculo, 'Vehiculo: ', concat(marca_vehiculo,' - ',placa_vehiculo)
    FROM hue_vehiculo;
 DECLARE comboHueCliente CURSOR FOR
  SELECT cod_cliente, nom_cliente, nit_cliente
    FROM hue_cliente;
 DECLARE comboTipDocumento CURSOR FOR
  SELECT cod_tip_documento, 'Documento: ', nom_tip_documento
    FROM hue_tip_documento;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done1 = TRUE;
 CASE TRIM(cNomTablaEnt)
  WHEN 'sys_ciudad' THEN 
   OPEN comboCiudad;
   comboCiudadLoop: LOOP
   FETCH comboCiudad INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboCiudad;
	 LEAVE comboCiudadLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_ciudad" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_regimen' THEN 
   OPEN comboRegimen;
   comboRegimenLoop: LOOP
   FETCH comboRegimen INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboRegimen;
	 LEAVE comboRegimenLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_regimen" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'sys_contrato' THEN 
   OPEN comboContrato;
   comboContratoLoop: LOOP
   FETCH comboContrato INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboContrato;
	 LEAVE comboContratoLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_contrato" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'mod_modulo' THEN 
   OPEN comboModulo;
   comboModuloLoop: LOOP
   FETCH comboModulo INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboModulo;
	 LEAVE comboModuloLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_modulo" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_moneda' THEN 
   OPEN comboMoneda;
   comboMonedaLoop: LOOP
   FETCH comboMoneda INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboMoneda;
	 LEAVE comboMonedaLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_moneda" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'sys_perfil' THEN 
   OPEN comboPerfil;
   comboPerfilLoop: LOOP
   FETCH comboPerfil INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboPerfil;
	 LEAVE comboPerfilLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_perfil" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'sys_empresa' THEN 
   OPEN comboEmpresa;
   comboEmpresaLoop: LOOP
   FETCH comboEmpresa INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboEmpresa;
	 LEAVE comboEmpresaLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_empresa" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'sys_menu' THEN 
   OPEN comboMenu;
   comboMenuLoop: LOOP
   FETCH comboMenu INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboMenu;
	 LEAVE comboMenuLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_menu" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'sys_menu_sub' THEN 
   OPEN comboMenuSub;
   comboMenuSubLoop: LOOP
   FETCH comboMenuSub INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboMenuSub;
	 LEAVE comboMenuSubLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_menu_sub" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'sys_usuario' THEN 
   OPEN comboUsuario;
   comboUsuarioLoop: LOOP
   FETCH comboUsuario INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboUsuario;
	 LEAVE comboUsuarioLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_usuario" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'sys_estado' THEN 
   OPEN comboEstado;
   comboEstadoLoop: LOOP
   FETCH comboEstado INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboEstado;
	 LEAVE comboEstadoLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_estado" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_tipoimpuesto' THEN 
   OPEN comboTipoImpuesto;
   comboTipoImpuestoLoop: LOOP
   FETCH comboTipoImpuesto INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboTipoImpuesto;
	 LEAVE comboTipoImpuestoLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_tipo_impuesto" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_cliente' THEN 
   OPEN comboCliente;
   comboClienteLoop: LOOP
   FETCH comboCliente INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboCliente;
	 LEAVE comboClienteLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_cliente" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_proveedor' THEN 
   OPEN comboProveedor;
   comboProveedorLoop: LOOP
   FETCH comboProveedor INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboProveedor;
	 LEAVE comboProveedorLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_proveedor" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_tipopago' THEN 
   OPEN comboTipoPago;
   comboTipoPagoLoop: LOOP
   FETCH comboTipoPago INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboTipoPago;
	 LEAVE comboTipoPagoLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_tipopago" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_item' THEN
   OPEN comboItem;
   comboItemLoop: LOOP
   FETCH comboItem INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboItem;
	 LEAVE comboItemLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_item" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'con_cuenta' THEN
   OPEN comboCuenta;
   comboCuentaLoop: LOOP
   FETCH comboCuenta INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboCuenta;
	 LEAVE comboCuentaLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_cuenta" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_item_imventario' THEN
   OPEN comboItemInventaro;
   comboItemInventaroLoop: LOOP
   FETCH comboItemInventaro INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboItemInventaro;
	 LEAVE comboItemInventaroLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_item_inventario" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
 WHEN 'fa_unimedida' THEN
   OPEN comboUniMedida;
   comboUniMedidaLoop: LOOP
   FETCH comboUniMedida INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboUniMedida;
	 LEAVE comboUniMedidaLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_uni_medida" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_impuesto' THEN
   OPEN comboImpuesto;
   comboImpuestoLoop: LOOP
   FETCH comboImpuesto INTO cCodTabla, cNomColumna, cDesColumna, auxColumna;
    IF done1 THEN
     CLOSE comboImpuesto;
	 LEAVE comboImpuestoLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_impuesto" value=',cCodTabla,' data=',auxColumna,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'fa_descuentos' THEN
   OPEN comboDescuento;
   comboDescuentoLoop: LOOP
   FETCH comboDescuento INTO cCodTabla, cNomColumna, cDesColumna, auxColumna;
    IF done1 THEN
     CLOSE comboDescuento;
	 LEAVE comboDescuentoLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_descuento" value=',cCodTabla,' data=',auxColumna,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'lc_restaurantes' THEN
   OPEN comboRestaurante;
   comboRestauranteLoop: LOOP
   FETCH comboRestaurante INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboRestaurante;
	 LEAVE comboRestauranteLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_restaurantes" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'sys_metodos' THEN
   OPEN comboMetodo;
   comboMetodoLoop: LOOP
   FETCH comboMetodo INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboMetodo;
	 LEAVE comboMetodoLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_metodos" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption);
   END LOOP;
  WHEN 'sys_formulario' THEN
   OPEN comboFormulario;
   comboFormularioLoop: LOOP
   FETCH comboFormulario INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboFormulario;
	 LEAVE comboFormularioLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_formulario" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
  WHEN 'sys_tipoinput' THEN
   OPEN comboTipoInput;
   comboTipoInputLoop: LOOP
   FETCH comboTipoInput INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboTipoInput;
	 LEAVE comboTipoInputLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_tipoinput" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
  WHEN 'sys_tablareferencia' THEN
   OPEN comboTablaReferencia;
   comboTablaReferenciaLoop: LOOP
   FETCH comboTablaReferencia INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboTablaReferencia;
	 LEAVE comboTablaReferenciaLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_tablareferencia" value=',cDesColumna,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
 WHEN 'sys_frame' THEN
   OPEN comboFrame;
   comboFrameLoop: LOOP
   FETCH comboFrame INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboFrame;
	 LEAVE comboFrameLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_frame" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
 WHEN 'lc_categorias' THEN
   OPEN comboCategorias;
   comboCategoriasLoop: LOOP
   FETCH comboCategorias INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboCategorias;
	 LEAVE comboCategoriasLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_categorias" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
 WHEN 'lc_categorias_sub' THEN
   OPEN comboCategoriasSub;
   comboCategoriasSubLoop: LOOP
   FETCH comboCategoriasSub INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboCategoriasSub;
	 LEAVE comboCategoriasSubLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_categorias_sub" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
 WHEN 'lc_cliente' THEN
   OPEN comboClienteLC;
   comboClienteLCLoop: LOOP
   FETCH comboClienteLC INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboClienteLC;
	 LEAVE comboClienteLCLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_cliente" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
 WHEN 'fa_cotizacion' THEN
   OPEN comboCotizacion;
   comboCotizacionLoop: LOOP
   FETCH comboCotizacion INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboCotizacion;
	 LEAVE comboCotizacionLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_factura" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
 WHEN 'fa_factura' THEN
   OPEN comboFactura;
   comboFacturaLoop: LOOP
   FETCH comboFactura INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboFactura;
	 LEAVE comboFacturaLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_factura" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
  WHEN 'hue_tip_servicio' THEN
   OPEN comboTipServicio;
   comboTipServicioLoop: LOOP
   FETCH comboTipServicio INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboTipServicio;
	 LEAVE comboTipServicioLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_tip_servicio" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
  WHEN 'hue_vehiculo_clase' THEN
   OPEN comboVehiculoClase;
   comboVehiculoClaseLoop: LOOP
   FETCH comboVehiculoClase INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboVehiculoClase;
	 LEAVE comboVehiculoClaseLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_vehiculo_clase" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
  WHEN 'hue_combustible' THEN
   OPEN comboCombustible;
   comboCombustibleLoop: LOOP
   FETCH comboCombustible INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboCombustible;
	 LEAVE comboCombustibleLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_combustible" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
  WHEN 'hue_vehiculo' THEN
   OPEN comboVehiculo;
   comboVehiculoLoop: LOOP
   FETCH comboVehiculo INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboVehiculo;
	 LEAVE comboVehiculoLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_vehiculo" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
  WHEN 'hue_cliente' THEN
   OPEN comboHueCliente;
   comboHueClienteLoop: LOOP
   FETCH comboHueCliente INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboHueCliente;
	 LEAVE comboHueClienteLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_cliente" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
  WHEN 'hue_tip_documento' THEN
   OPEN comboTipDocumento;
   comboTipDocumentoLoop: LOOP
   FETCH comboTipDocumento INTO cCodTabla, cNomColumna, cDesColumna;
    IF done1 THEN
     CLOSE comboTipDocumento;
	 LEAVE comboTipDocumentoLoop;
	END IF;
    set cOption = CONCAT('<option data-selected="cod_tip_documento" value=',cCodTabla,'>',cNomColumna,IF(cDesColumna is not null,CONCAT(' - ',cDesColumna),''),'</option>');
	set cFinOption = CONCAT(cFinOption,cOption); 
   END LOOP;
 END CASE;
RETURN cFinOption; 
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaCuenta`(cCadenaGrupo int(2)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done  BOOL DEFAULT FALSE;
 DECLARE cCadenaNombre     longtext DEFAULT '';
 DECLARE cCadenaCuenta      INT(4);
 DECLARE cCadenaSubCuenta   INT(6);
 DECLARE cCadCuenta         longtext DEFAULT '';
 DECLARE cCadsubCuenta      longtext DEFAULT '';
 DECLARE Cuenta CURSOR FOR 
  SELECT concat(nom_cuenta,' - ',cuenta_cuenta), cuenta_cuenta, sub_cuenta
    FROM con_cuenta 
   WHERE grupo_cuenta=cCadenaGrupo
     AND cuenta_cuenta>0
	 AND sub_cuenta=0
ORDER BY cuenta_cuenta ASC;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done  = TRUE;
 OPEN Cuenta;
  CuentaLoop: LOOP
  FETCH Cuenta INTO cCadenaNombre,cCadenaCuenta,cCadenaSubCuenta; 
   IF done THEN
    CLOSE Cuenta;
    LEAVE CuentaLoop;
   END IF;
   SET cCadsubCuenta=fbArmaSubCuenta(cCadenaCuenta);
   SET cCadCuenta=concat(cCadCuenta,'<li style="display:none"><span><i class="fa fa-lg fa-plus-circle"></i> ',cCadenaNombre,'</span>',cCadsubCuenta,'</li>'); 
  END LOOP;
RETURN concat('<ul>',cCadCuenta,'</ul>');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaCuentasContables`() RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done  BOOL DEFAULT FALSE;
 DECLARE cCadenaCuentas    longtext DEFAULT '';
 DECLARE cCadenaFinCuentas longtext DEFAULT '';
 DECLARE cCadClase		   longtext DEFAULT '';
 DECLARE cCadenaClase      INT(1);
 DECLARE cCadenaGrupo      longtext DEFAULT '';
 DECLARE cNomCuenta        longtext DEFAULT '';
 DECLARE Clase CURSOR FOR 
  SELECT nom_cuenta,clase_cuenta
    FROM con_cuenta
GROUP BY clase_cuenta
ORDER BY clase_cuenta ASC;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done  = TRUE;
 OPEN Clase;
  ClaseLoop: LOOP
  FETCH Clase INTO cNomCuenta,cCadenaClase; 
   IF done THEN
    CLOSE Clase;
    LEAVE ClaseLoop;
   END IF;
   SET cCadenaGrupo=fbArmaGrupo(cCadenaClase); 
   SET cCadClase=concat(cCadClase,
                           '<li style="display:none"><span><i class="fa fa-lg fa-plus-circle"></i> ',cNomCuenta,'</span>',cCadenaGrupo,'</li>');
  END LOOP;
  set cCadenaFinCuentas = concat('<ul><li><span><i class="fa fa-lg fa-folder-open"></i> PUC (Plan Unico de Cuentas)</span><ul>',cCadClase,'</ul></li></ul>');
RETURN cCadenaFinCuentas;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaFavoritos`(cCodUsu int(8)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done  BOOL DEFAULT FALSE;
 DECLARE cCadenaFavorito      longtext DEFAULT '';
 DECLARE cCadenaIniFavorito   longtext DEFAULT '';
 DECLARE cNomRes              varchar(220);
 DECLARE cEslRes              varchar(20);
 DECLARE cCodRes              varchar(400);
 DECLARE cImgRes              varchar(120);
 DECLARE favorito CURSOR FOR 
	SELECT t1.nom_restaurantes, t1.esl_restaurantes,t1.cod_restaurantes,
           t3.img_restaurantes_img
	  FROM lc_restaurantes as t1, lc_favoritos as t2,
		   lc_restaurantes_img as t3
     WHERE t1.cod_restaurantes=t2.cod_restaurantes 
       AND t1.cod_restaurantes=t3.cod_restaurantes
       AND t2.cod_cliente=cCodUsu 
       AND t3.ind_app= 1
  ORDER BY t2.cod_favoritos DESC;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done  = TRUE;
 OPEN favorito;
  favoritoLoop: LOOP
  FETCH favorito INTO cNomRes,cEslRes,cCodRes,cImgRes;
   IF done THEN
    CLOSE favorito;
    LEAVE favoritoLoop;
   END IF;
   set cCadenaIniFavorito =CONCAT('<li style="border-bottom:1px solid #fafafa;box-shadow: 1px 1px 1px #888888;">',
                                   '<a id="btoAccionFiltro" clase="btoAccionFiltro" data="FiltroRestaurante" data-table="lc_restaurantes" data-case="consultaRestaurante" data-id="',cCodRes,'">',
                                    '<p class="column-responsive half-bottom">',IF(cImgRes<>'',concat('<img src="http://localhost/appmoduler/modules/lacuchara/adjuntos/',cImgRes,'">'),''),
                                     '<strong>',cNomRes,'</strong>',
                                     ' <em>',cEslRes,'</em>',
                                    '</p>',
                                   '</a>',
								  '</li>');
   set cCadenaFavorito = CONCAT(cCadenaFavorito,'<p>',cCadenaIniFavorito,'</p>');
  END LOOP;
RETURN cCadenaFavorito;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaFormulario`(codFrm int(2), codUsu int(8), iDatos int(1),cCiclo int(2),codSubM int(4)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done  BOOL DEFAULT FALSE;
 DECLARE cCodFrame     int(8);
 DECLARE cCodFrm       int(8);   
 DECLARE tDatos        int(1);
 DECLARE cCadenaFrame  varchar(10000) DEFAULT '';
 DECLARE cCadenaInp    longtext DEFAULT '';
 DECLARE cCadenaFinFrm longtext DEFAULT '';
 DECLARE cCadenaFinal  longtext DEFAULT '';
 DECLARE cCodMenuSub   int(8);
 DECLARE cNomTabla     varchar(45);
 DECLARE cNomForm      varchar(45); 
 DECLARE cFinFrame     varchar(2000)  DEFAULT '</div></div></div>';
 DECLARE Frame CURSOR FOR 
  SELECT CASE t2.tip_formulario 
          WHEN '0' THEN CONCAT('<div class="col-lg-',t1.id_frame,'"><div class="panel panel-default"><div class="panel-heading"><h4><span>',t1.nom_frame,'</span></h4></div><div class="panel-body">')
		  WHEN '1' THEN ''
         END, t1.cod_frame, t2.nom_formulario, t2.nom_tabla, t2.cod_formulario,t3.cod_menu_sub     
    FROM sys_frame as t1   , sys_formulario as t2, 
		 sys_menu_sub as t3, sys_usuario_menu_sub as t4 
   WHERE t3.cod_menu_sub            = t4.cod_menu_sub
     AND t1.cod_formulario          = t2.cod_formulario
     AND t3.cod_formulario_asociado = t1.cod_formulario
     AND t3.cod_formulario_asociado = codFrm
     AND t4.cod_usuario             = codUsu 
     AND t4.cod_menu_sub            = codSubM
     ; 
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done  = TRUE;
 set tDatos=iDatos;
 OPEN Frame;
  FrameLoop: LOOP
  FETCH Frame INTO cCadenaFrame, cCodFrame, cNomForm, cNomTabla, cCodFrm, cCodMenuSub;
   IF done THEN
    CLOSE Frame;
    LEAVE FrameLoop;
   END IF;
 	 
   set cCadenaFinFrm = cCadenaFrame;
   set cCadenaInp = fbArmaInput(cCodFrame, codUsu, tDatos,cCiclo);
   set cCadenaFinFrm = CONCAT(cCadenaFinFrm,cCadenaInp,cFinFrame);
   set cCadenaFinal  = CONCAT(cCadenaFinal,cCadenaFinFrm); 
  END LOOP;
RETURN CONCAT(cCadenaFinal,'<input type="hidden" name="no_nom_tabla" value="',cNomForm,'"><input type="hidden" name="no_esq_tabla" value="',cNomTabla,'"><input type="hidden" name="no_id_formulario" value="',cCodFrm,'"><input type="hidden" name="no_cod_menu_sub" value="',cCodMenuSub,'"></div>');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaGrupo`(cCadenaClase int(1)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done  BOOL DEFAULT FALSE;
 DECLARE cCadenaNombre     longtext DEFAULT '';
 DECLARE cCadenaGrupo      INT(2);
 DECLARE cCadenaCuenta     INT(4);
 DECLARE cCadGrupo         longtext DEFAULT '';
 DECLARE cCadCuenta        longtext DEFAULT '';
 DECLARE Grupo CURSOR FOR 
  SELECT concat(nom_cuenta,' - ',grupo_cuenta), grupo_cuenta, cuenta_cuenta
    FROM con_cuenta 
   WHERE clase_cuenta=cCadenaClase
     AND grupo_cuenta>0 
	 AND cuenta_cuenta=0 
     AND sub_cuenta=0
ORDER BY grupo_cuenta ASC;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done  = TRUE;
 OPEN Grupo;
  GrupoLoop: LOOP
  FETCH Grupo INTO cCadenaNombre,cCadenaGrupo,cCadenaCuenta; 
   IF done THEN
    CLOSE Grupo;
    LEAVE GrupoLoop;
   END IF;
   SET cCadCuenta=fbArmaCuenta(cCadenaGrupo);
   SET cCadGrupo=concat(cCadGrupo,'<li><span><i class="fa fa-lg fa-plus-circle"></i>', cCadenaNombre,'</span>',cCadCuenta,'</li>'); 
  END LOOP;
RETURN concat('<ul>',cCadGrupo,'</ul>');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaInput`(codFrm int, codUsu int, tDatos int,cCiclo int(2)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done1 BOOL DEFAULT FALSE; 
 DECLARE cCadenaInputs varchar(10000) DEFAULT '' ;
 DECLARE iTipoInput    int(2);
 DECLARE iIndLinea     int(1);
 DECLARE cTablaRef     varchar(45);
 DECLARE cCadenaInp    longtext       DEFAULT '';
 DECLARE cCadenaFinInp longtext       DEFAULT '';
 DECLARE cIniSelect    varchar(400)   DEFAULT '';
 DECLARE cIniOption    longtext       DEFAULT '';
 DECLARE cFinSelect    varchar(400)   DEFAULT '';
 DECLARE cCadenaEmp    varchar(100)   DEFAULT '';
 DECLARE cFinInput     varchar(100)   DEFAULT '</div></div>';
 DECLARE cCadenaCliclo longtext       DEFAULT '';
 DECLARE InputGroup CURSOR FOR
 SELECT CASE t2.ind_enLinea 
         WHEN 0 THEN
          CONCAT('<div class="form-group">',
				CASE t1.cod_tipoinput  
				 WHEN '1' THEN CONCAT('<label class="col-lg-5 control-label" for="normalInput">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-7">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="form-control" id="',det_campo,'" name="',det_campo,'" value="',IF(tDatos=1,val_campo,''),'" placeholder="',holder_campo,'" data-disabled>')  
				 WHEN '2' THEN CONCAT('<label class="col-lg-5 control-label" for="textareas">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-7">','<textarea id="textarea" id="',det_campo,'" name="',det_campo,'" rows="3" class="form-control" placeholder="',holder_campo,'" data-disabled>',IF(tDatos=1,val_campo,''),'</textarea>')  
				 WHEN '3' THEN CONCAT('<label class="col-lg-5 control-label" for="checkboxes">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-7"><label class="checkbox">','<input type="checkbox" name="',det_campo,'" value="1" data-disabled></label>')  
				 WHEN '4' THEN CONCAT('<div class="radio"><input name="',nom_campo,'" type="radio" id="inlineCheckbox1" value="1" ></div>')    
				 WHEN '5' THEN CONCAT('<label class="col-lg-5 control-label" for="select">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-7">','<select id="',det_campo,'" class="selectFiltro" style="width:100%" name="',det_campo,'" placeholder="Seleccione" data-disabled><option></option>')
				 WHEN '6' THEN CONCAT('<label class="col-lg-5 control-label" for="select">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-7">','<select id="',det_campo,'" class="selectMultiple"  style="width:100%" multiple="multiple" name="',det_campo,'[]" placeholder="Seleccione" data-disabled><option></option>')
				 WHEN '7' THEN CONCAT('<label class="col-lg-5 control-label" for="select">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-7">','<select id="e',det_campo,'" class="nostyle form-control" style="width:100%" multiple="multiple" name="',det_campo,'[]" data-disabled>')
				 WHEN '8' THEN CONCAT('<label class="col-lg-5 control-label" for="normalInput">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-7">','<input type="password" class="form-control" id="',det_campo,'" name="',det_campo,'" value="" data-disabled>')  	
                 WHEN '9' THEN CONCAT('<label class="col-lg-5 control-label" for="fileinput">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-7">','<input type="file" class="form-control" name="',det_campo,'" id="file" value="" data-disabled>') 
                 WHEN '10' THEN CONCAT('<div><input type="',IF(cod_estado='FIV','text','hidden'),'" class="form-control" name="',det_campo,'" value="',IF(tDatos=1,val_campo,''),'">')
				 WHEN '11' THEN CONCAT('<label class="col-lg-5 control-label" for="normalInput">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-4">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="selectFecha form-control" id="',det_campo,'" name="',det_campo,'" value="',IF(tDatos=1,val_campo,''),'" data-disabled>') 
                 WHEN '12' THEN CONCAT('<label class="col-lg-5 control-label" for="normalInput">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-4">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="selectHora form-control" id="',det_campo,'" name="',det_campo,'" value="',IF(tDatos=1,val_campo,''),'" data-disabled>')    
				 WHEN '14' THEN CONCAT('<label class="col-lg-5 control-label" for="normalInput">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-3">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="spinerInput form-control" id="',det_campo,'" name="',det_campo,'" value="',IF(tDatos=1,val_campo,''),'" data-disabled>')
				 WHEN '15' THEN CONCAT('<label class="col-lg-5 control-label" for="normalInput">',IF(cod_estado='FIV',nom_campo,''),'</label><div class="col-lg-3">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="mask-percent form-control" id="',det_campo,'" name="',det_campo,'" value="0',IF(tDatos=1,val_campo,''),'" data-disabled>')  
			   END)
           WHEN 1 THEN
				 CASE t1.cod_tipoinput  
				  WHEN '1' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="form-control" id="',det_campo,'" name="',det_campo,'[]" value="',IF(tDatos=1,val_campo,''),'" placeholder="',holder_campo,'" data-disabled>')  
				  WHEN '2' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<textarea id="textarea" id="',det_campo,'" name="',det_campo,'[]" rows="3" class="form-control" placeholder="',holder_campo,'" data-disabled>',IF(tDatos=1,val_campo,''),'</textarea>')  
				  WHEN '3' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<input type="checkbox" name="',det_campo,'[]" value="1" data-disabled>')  
				  WHEN '4' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<input name="',nom_campo,'" type="radio" id="inlineCheckbox1" value="1" data-disabled>')    
				  WHEN '5' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<select id="',det_campo,'" class="selectFiltro" style="width:100%" name="',det_campo,'[]" placeholder="',holder_campo,'" data-disabled><option></option>')
				  WHEN '6' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<select id="',det_campo,'" class="selectMultiple"  style="width:100%" multiple="multiple" name="',det_campo,'[]" placeholder="',holder_campo,'" data-disabled><option></option>')
				  WHEN '7' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<select id="',det_campo,'" class="nostyle form-control" style="width:100%" multiple="multiple" name="',det_campo,'[]" data-disabled>')
				  WHEN '8' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<input type="password" class="form-control" id="',det_campo,'" name="',det_campo,'[]" value="',IF(tDatos=1,val_campo,''),'" data-disabled>')  	
                  WHEN '9' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<input type="file" class="form-control" id="file" name="',det_campo,'[]" value="',IF(tDatos=1,val_campo,''),'" data-disabled>') 
                  WHEN '10' THEN CONCAT('<div><input type="',IF(cod_estado='FIV','text','hidden'),'" class="form-control" name="',det_campo,'[]" value="',IF(tDatos=1,val_campo,''),'" data-disabled></div>')
				  WHEN '11' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="selectFecha form-control" id="',det_campo,'" name="',det_campo,'[]" value="" placeholder="',holder_campo,'" data-disabled>') 
                  WHEN '12' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="selectHora form-control" id="',det_campo,'" name="',det_campo,'[]" value="" placeholder="',holder_campo,'" data-disabled>')    
				  WHEN '14' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="spinerInput form-control" id="cantidad" name="',det_campo,'[]" value="',IF(tDatos=1,val_campo,''),'" placeholder="',holder_campo,'" data-disabled>')
				  WHEN '15' THEN CONCAT('<div class="col-lg-',t1.tam_campo,'">','<input type="',IF(cod_estado='FIV','text','hidden'),'" class="mask-percent form-control"  id="descuento" name="',det_campo,'[]" value="',IF(tDatos=1,val_campo,''),'" placeholder="',holder_campo,'" data-disabled>')  
			    END
		 END, t1.cod_tipoinput, t1.nom_tablaref,t2.ind_enlinea 
   FROM sys_detframe as t1, sys_frame as t2 
  WHERE t1.cod_frame = codFrm
	AND t1.cod_frame = t2.cod_frame
ORDER BY t1.cod_detframe,t1.cod_estado;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done1 = TRUE;
 set cCadenaEmp = fbTraeEmpresa(codUsu);
 OPEN InputGroup;
   InputGroupLoop: LOOP
	 FETCH InputGroup INTO cCadenaInputs, iTipoInput, cTablaRef,iIndLinea;
	  IF done1 THEN
       CLOSE InputGroup;
       LEAVE InputGroupLoop;
      END IF;
	  CASE iTipoInput
		WHEN 5 THEN
		 set cIniOption = fbArmaCombo(cTablaRef,codUsu,cCadenaEmp);
		 set cCadenaInp = CONCAT(cCadenaInp, cCadenaInputs, cIniOption,'</select>', IF(iIndLinea=0,cFinInput,'</div>'));
        WHEN 6 THEN
		 set cIniOption = fbArmaCombo(cTablaRef,codUsu,cCadenaEmp);
		 set cCadenaInp = CONCAT(cCadenaInp, cCadenaInputs, cIniOption,'</select>', IF(iIndLinea=0,cFinInput,'</div>')) ;
        WHEN 7 THEN
		 set cIniOption = fbArmaCombo(cTablaRef,codUsu,cCadenaEmp);
		 set cCadenaInp = CONCAT(cCadenaInp, cCadenaInputs, cIniOption,'</select>', IF(iIndLinea=0,cFinInput,'</div>')) ;
        ELSE  
         set cCadenaInp = CONCAT(cCadenaInp, cCadenaInputs, IF(iIndLinea=0,cFinInput,'</div>'));
      END CASE;
    END LOOP;
	IF iIndLinea=0 THEN
		RETURN cCadenaInp;
	ELSE
		WHILE cCiclo > 0 DO
		 SET cCadenaCliclo = CONCAT(cCadenaCliclo, CONCAT('<div class="clonar form-group" data-nombre="div-Clon" data-indice="1" id="1"><div class="row" id="rowCopia">',REPLACE(REPLACE(cCadenaInp,'<option data-selected',concat('<option data-array=',cCiclo-1,' data-selected')), '[]" value=', concat('[]" data-array=',cCiclo-1,' value=')),'</div></div>'));
		 set cCiclo = cCiclo - 1;
        END WHILE;
		RETURN cCadenaCliclo;
	END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaSubCuenta`(cCadenaCuenta int(4)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done  BOOL DEFAULT FALSE;
 DECLARE cCadenaNombre     longtext DEFAULT '';
 DECLARE cCadenaSubCuenta      INT(4);
 DECLARE cCadsubCuenta         longtext DEFAULT '';
 DECLARE subCuenta CURSOR FOR 
  SELECT concat(nom_cuenta,' - ',sub_cuenta), sub_cuenta
    FROM con_cuenta 
   WHERE cuenta_cuenta=cCadenaCuenta
     AND sub_cuenta>0 
ORDER BY sub_cuenta ASC;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done  = TRUE;
 OPEN subCuenta;
  subCuentaLoop: LOOP
  FETCH subCuenta INTO cCadenaNombre,cCadenaCuenta; 
   IF done THEN
    CLOSE subCuenta;
    LEAVE subCuentaLoop;
   END IF;
   SET cCadsubCuenta=concat(cCadsubCuenta,'<li><span>', cCadenaNombre,'</span>','</li>'); 
  END LOOP;
RETURN concat('<ul>',cCadsubCuenta,'</ul>');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaSubMenu`(codMenu int, codUsuario int) RETURNS varchar(10000) CHARSET utf8
BEGIN
 DECLARE done BOOL DEFAULT FALSE;
 DECLARE sSubMenu varchar(2000);
 DECLARE cSubMenu varchar(10000) DEFAULT "";
 DECLARE subMenu CURSOR FOR 
   SELECT CONCAT('<li><a href="',t1.met_menu_sub,'*',t1.cod_menu_sub,
                 '"><span class=""></span>',t1.nom_menu_sub,
	 			 '</a></li>') as cadenaSubMenu 
     FROM sys_menu_sub as t1, sys_usuario_menu_sub as t2  
    WHERE t1.cod_menu     = codMenu    
      AND t2.cod_usuario  = codUsuario 
      AND t1.cod_menu_sub = t2.cod_menu_sub
      AND t1.ind_visible  = 1 
 GROUP BY t1.cod_menu_sub
 ORDER BY t1.cod_indice ASC;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = TRUE;
 OPEN subMenu;
  subMenuLoop: LOOP
  FETCH subMenu INTO sSubMenu;
   IF done THEN
    CLOSE subMenu;
    LEAVE subMenuLoop;
   END IF;
   set cSubMenu = CONCAT(cSubMenu , sSubMenu);
  END LOOP;
RETURN cSubMenu;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbArmaSubMenuHeader`(codUsuario int) RETURNS varchar(10000) CHARSET utf8
BEGIN
 DECLARE done BOOL DEFAULT FALSE;
 DECLARE sSubMenu varchar(2000);
 DECLARE cSubMenu varchar(10000) DEFAULT "";
 DECLARE subMenu CURSOR FOR 
   SELECT CONCAT('<li><a href="',t1.met_menu_sub,'" class="jarvismetro-tile big-cubes bg-color-',t1.bg_color,'">
                    <span class="iconbox"><i class="',t1.icon_menu_sub,
				 ' fa-4x"></i><span>',SUBSTRING(t1.nom_menu_sub,1,12),
	 			 '</span></span></a></li>') as cadenaSubMenu 
     FROM sys_menu_sub as t1, sys_usuario_menu_sub as t2  
    WHERE t2.cod_usuario  = codUsuario AND
		  t1.ind_header   = 1          AND
          t1.cod_menu_sub = t2.cod_menu_sub
 GROUP BY t1.cod_menu_sub;
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = TRUE;
 OPEN subMenu;
  subMenuLoop: LOOP
  FETCH subMenu INTO sSubMenu;
   IF done THEN
    CLOSE subMenu;
    LEAVE subMenuLoop;
   END IF;
   set cSubMenu = CONCAT(cSubMenu , sSubMenu);
  END LOOP;
RETURN cSubMenu;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fbTraeEmpresa`(codUsu int(8)) RETURNS longtext CHARSET utf8
BEGIN
 DECLARE done  BOOL DEFAULT FALSE;
 DECLARE cCodEmp       varchar(8);
 DECLARE cCadenaEmp    longtext DEFAULT '';
 DECLARE Empresas CURSOR FOR 
  SELECT cod_empresa    
    FROM sys_usuario_empresa
   WHERE cod_usuario   = codUsu 
GROUP BY cod_empresa
ORDER BY cod_empresa DESC; 
 DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done  = TRUE;
 OPEN Empresas;
  EmpresasLoop: LOOP
  FETCH Empresas INTO cCodEmp;
   IF done THEN
    CLOSE Empresas;
    LEAVE EmpresasLoop;
   END IF;
   set cCadenaEmp = CONCAT(cCadenaEmp,',',cCodEmp);
  END LOOP;
RETURN RIGHT(cCadenaEmp,(CHARACTER_LENGTH(cCadenaEmp)-1));
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aud_tablas`
--

CREATE TABLE IF NOT EXISTS `aud_tablas` (
  `cod_audtablas` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK DE LA TABLA AUTOINCREMENTO',
  `nom_tabla` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DE LA TABLA AFECTADA',
  `cod_campo` varchar(45) DEFAULT NULL COMMENT 'CODIGO DE LA TABLA AFECTADA',
  `nom_campo` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL CAMPO AFECTADO',
  `old_valor` varchar(45) DEFAULT NULL,
  `new_valor` varchar(45) DEFAULT NULL,
  `cod_metodos` int(4) NOT NULL COMMENT 'METODO APLICADO EN LA TABLA',
  `fec_audtablas` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `hora_audtablas` varchar(45) DEFAULT NULL COMMENT 'HORA DE REGISTRO',
  `cod_usuario` int(8) NOT NULL COMMENT 'USUARIO QUE APLICO EL METODO',
  PRIMARY KEY (`cod_audtablas`),
  KEY `fk_aud_met_idx` (`cod_metodos`),
  KEY `fk_aud_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ALMACENA LOS MOVIMIENTO DE LOS EVENTOS EN LAS TABLAS' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `con_categoria`
--

CREATE TABLE IF NOT EXISTS `con_categoria` (
  `cod_categoria` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK DE LA TABLA AUTOINCREMENTABLE',
  `nom_categoria` varchar(45) NOT NULL,
  `des_categoria` varchar(120) NOT NULL,
  `cod_cliente` int(8) NOT NULL,
  `cod_cuenta` int(8) DEFAULT NULL,
  `ind_cuenta` int(1) DEFAULT '0',
  `cod_estado` varchar(3) DEFAULT NULL,
  `cod_empresa` int(8) DEFAULT NULL,
  `cod_usuario` int(8) DEFAULT NULL,
  PRIMARY KEY (`cod_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LAS DIFERENTES CATEGORIAS CONTABLES QUE DEBEN IR RELACIONADAS A LAS CUENTAS' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `con_cuenta`
--

CREATE TABLE IF NOT EXISTS `con_cuenta` (
  `cod_cuenta` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK AUTOINCREMENTO DE LA TABLA',
  `nom_cuenta` varchar(45) NOT NULL COMMENT 'NOMBRE DE LA CUENTA',
  `clase_cuenta` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA QUE CLASE DE CUENTA ES SEGUN NIIF',
  `grupo_cuenta` int(2) NOT NULL DEFAULT '0' COMMENT 'GRUPO AL QUE PERTENECE LA CUENTA SEGUN PUC',
  `cuenta_cuenta` int(4) NOT NULL DEFAULT '0' COMMENT 'CUENTA A LA QUE PERTENECE SEGUN PUC',
  `sub_cuenta` int(6) DEFAULT '0' COMMENT 'SUBCUENTA A AL QUE PERTENCE SEGUN PUC',
  `num_cuenta` varchar(45) NOT NULL COMMENT 'NUMERO DE LA CUENTA EN CASO DE SER TARJETA CREDITO O BANCO',
  `des_cuenta` varchar(500) DEFAULT NULL COMMENT 'DESCRIPCION DE LA CUENTA',
  `imp_inicial_cuenta` varchar(45) DEFAULT NULL COMMENT 'IMPORTE INICIAL CON QUE SE ABRE LA CUENTA PUEDE SER 0',
  `fec_cuenta` datetime DEFAULT NULL COMMENT 'FECHA DE CREACION DEL LA CUENTA',
  `ind_balance_general` int(1) DEFAULT '0' COMMENT 'INDICA SI LA CUENTA ES DEL BALANCE GENERAL',
  `cod_usuario` int(8) NOT NULL COMMENT 'USUARIO QUE REGISTRA LA CUENTA',
  `cod_tipocuenta` int(4) NOT NULL COMMENT 'INDICA EL TIPO DE CUENTA EJEMPLO 1:ACTIVO, 2:PASIVO ',
  PRIMARY KEY (`cod_cuenta`),
  KEY `fk_cue_usu_idx` (`cod_usuario`),
  KEY `fk_cue_tipcue_idx` (`cod_tipocuenta`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS DIFERENTES TIPOS DE CUENTAS PARA CONTABILIDAD,' AUTO_INCREMENT=962 ;

--
-- Volcado de datos para la tabla `con_cuenta`
--

INSERT INTO `con_cuenta` (`cod_cuenta`, `nom_cuenta`, `clase_cuenta`, `grupo_cuenta`, `cuenta_cuenta`, `sub_cuenta`, `num_cuenta`, `des_cuenta`, `imp_inicial_cuenta`, `fec_cuenta`, `ind_balance_general`, `cod_usuario`, `cod_tipocuenta`) VALUES
(1, 'Activo', 1, 0, 0, 0, '', NULL, '0', '2014-06-17 16:39:10', 1, 1, 1),
(12, 'Disponible', 1, 11, 0, 0, '', 'Comprende las cuentas que registran los recursos de liquidez inmediata, total o parcial con que cuenta el ente económico y puede utilizar para fines generales o específicos, dentro de los cuales podemos mencionar la caja, los depósitos en bancos y otras entidades financieras, las remesas en tránsito y los fondos.', '0', '2014-06-17 15:20:28', 1, 1, 1),
(13, 'Caja', 1, 11, 1105, 0, '', 'Registra la existencia en dinero efectivo o en cheques con que cuenta el ente económico, tanto en moneda nacional como extranjera, disponible en forma inmediata.', '0', '2014-06-17 15:27:05', 1, 1, 1),
(14, 'Caja General', 1, 11, 1105, 110505, '', '', '0', '2014-06-17 15:27:05', 1, 1, 1),
(15, 'Cajas Menores', 1, 11, 1105, 110510, '', '', '0', '2014-06-17 15:27:05', 1, 1, 1),
(16, 'Moneda Extranjera', 1, 11, 1105, 110515, '', ' ', '0', '2014-06-17 15:27:06', 1, 1, 1),
(17, 'Bancos', 1, 11, 1110, 0, '', 'Registra el valor de los depósitos constituidos por el ente económico en moneda nacional y extranjera, en bancos tanto del país como del exterior.', '0', '2014-06-17 15:29:32', 1, 1, 1),
(18, 'Moneda Nacional', 1, 11, 1110, 111005, '', NULL, '0', '2014-06-17 15:34:37', 1, 1, 1),
(19, 'Moneda Extranjera', 1, 11, 1110, 111010, '', NULL, '0', '2014-06-17 15:34:37', 1, 1, 1),
(20, 'Remesas en transito', 1, 11, 1115, 0, '', 'Registra el valor de los cheques sobre otras plazas nacionales o del exterior que han sido negociados por el ente económico, los cuales se encuentran pendientes de confirmación.', '0', '2014-06-17 15:38:58', 1, 1, 1),
(21, 'Moneda Nacional', 1, 11, 1115, 111505, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(22, 'Moneda Extranjera', 1, 11, 1115, 111510, '', NULL, '0', '2014-06-17 15:42:37', 1, 1, 1),
(23, 'Cuentas de Ahorro', 1, 11, 1120, 0, '', 'Registra la existencia de fondos a la vista o a término constituidos por el ente económico en las diferentes entidades financieras, las cuales generalmente producen algún tipo de rendimiento.', '0', '2014-06-17 16:01:33', 1, 1, 1),
(24, 'Bancos', 1, 11, 1120, 112005, '', '', '0', '0000-00-00 00:00:00', 1, 1, 1),
(25, 'Corporaciones de ahorro y vivienda', 1, 11, 1120, 112010, '', NULL, '0', '2014-06-17 16:03:02', 1, 1, 1),
(26, 'Organismmos cooperativos financieros', 1, 11, 1120, 112015, '', NULL, '0', '2014-06-17 16:03:03', 1, 1, 1),
(27, 'Inversiones', 1, 12, 0, 0, '', 'Comprende las cuentas que registran las inversiones en acciones, cuotas o partes de interés social, títulos valores, papeles comerciales o cualquier otro documento negociable adquirido por el ente económico con carácter temporal o permanente, con la finalidad de mantener una reserva secundaria de liquidez, establecer relaciones económicas con otras entidades o para cumplir con disposiciones legales o reglamentarias.', '0', '2014-06-17 16:09:39', 1, 1, 1),
(28, 'Acciones', 1, 12, 1205, 0, '', 'Registra el costo histórico de las inversiones realizadas por el ente económico en sociedades por acciones y/o asimiladas, el cual incluye las sumas incurridas directamente en su adquisición.', '0', '2014-06-17 16:09:40', 1, 1, 1),
(29, 'Agricultura, ganaderia, caza y silvicultura', 1, 12, 1205, 120505, '', NULL, '0', '2014-06-17 16:09:40', 1, 1, 1),
(30, 'Pesca', 1, 12, 1205, 120510, '', NULL, '0', '2014-06-17 16:09:40', 1, 1, 1),
(31, 'Explotacion de minas y canteras', 1, 12, 1205, 120515, '', NULL, '0', '2014-06-17 16:09:40', 1, 1, 1),
(32, 'Industia manufacturera', 1, 12, 1210, 120520, '', NULL, '0', '2014-06-17 16:09:40', 1, 1, 1),
(33, 'Cuotas o partes de interes social', 1, 12, 1210, 0, '', 'Registra el costo histórico de las inversiones realizadas por el ente económico en sociedades de responsabilidad limitada y/o asimiladas, el cual incluye las sumas incurridas directamente en su adquisición.', '0', '2014-06-17 16:09:40', 1, 1, 1),
(34, 'Agricultura, ganaderia, caza y silvicultura', 1, 12, 1210, 121005, '', NULL, '0', '2014-06-17 16:09:40', 1, 1, 1),
(35, 'Pesca', 1, 12, 1210, 121010, '', NULL, '0', '2014-06-17 16:09:40', 1, 1, 1),
(36, 'Explotacion de minas y canteras', 1, 12, 1210, 121015, '', NULL, '0', '2014-06-17 16:09:40', 1, 1, 1),
(37, 'Industia manufacturera', 1, 12, 1210, 121020, '', NULL, '0', '2014-06-17 16:09:40', 1, 1, 1),
(38, 'Bonos', 1, 12, 1215, 0, '', NULL, '0', '2014-06-17 16:17:04', 1, 1, 1),
(39, 'Bonos pulicos moneda nacional', 1, 12, 1215, 121505, '', NULL, '0', '2014-06-17 16:20:34', 1, 1, 1),
(40, 'Bonos pulicos moneda extranjera', 1, 12, 1215, 121510, '', NULL, '0', '2014-06-17 16:20:34', 1, 1, 1),
(41, 'Bonos Ordinarios', 1, 12, 1215, 121515, '', NULL, '0', '2014-06-17 16:27:04', 1, 1, 1),
(42, 'Bonos Convertibles en acciones', 1, 12, 1215, 121520, '', NULL, '0', '2014-06-17 16:27:04', 1, 1, 1),
(43, 'Cedulas', 1, 12, 1220, 0, '', NULL, '0', '2014-06-17 16:36:58', 1, 1, 1),
(44, 'Cedulas de capitalizacion', 1, 12, 1220, 122005, '', NULL, '0', '2014-06-17 16:36:58', 1, 1, 1),
(45, 'Cedulas Hipotecarias', 1, 12, 1220, 122010, '', NULL, '0', '2014-06-17 16:36:58', 1, 1, 1),
(46, 'Cedulas de inversion', 1, 12, 1220, 122015, '', NULL, '0', '2014-06-17 16:36:58', 1, 1, 1),
(47, 'Otras', 1, 12, 1220, 122090, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(48, 'Certificados', 1, 12, 1225, 0, '', NULL, '0', '2014-06-17 17:06:02', 1, 1, 1),
(49, 'Certificados de cambio', 1, 12, 1225, 122520, '', NULL, '0', '2014-06-17 17:08:23', 1, 1, 1),
(50, 'Certificados cafeteros valorizables', 1, 12, 1225, 122525, '', NULL, '0', '2014-06-17 17:08:23', 1, 1, 1),
(51, 'Certificados electricos valorizables(CEV)', 1, 12, 1225, 122530, '', NULL, '0', '2014-06-17 17:08:23', 1, 1, 1),
(52, 'Certificados de reembolso tributario(CERT)', 1, 12, 1225, 122535, '', NULL, '0', '2014-06-17 17:08:23', 1, 1, 1),
(55, 'Deudores', 1, 13, 0, 0, '', NULL, '0', '2014-06-17 17:13:30', 1, 1, 1),
(56, 'Clientes', 1, 13, 1305, 0, '', 'Registra los valores a favor del ente económico y a cargo de clientes nacionales y/o extranjeros de cualquier naturaleza, por concepto de ventas de mercancías, productos terminados, servicios y contratos realizados en desarrollo del objeto social, así como la financiación de los mismos.', '0', '2014-06-17 17:13:30', 1, 1, 1),
(57, 'Nacionales', 1, 13, 1305, 130505, '', NULL, '0', '2014-06-17 17:23:52', 1, 1, 1),
(58, 'Del extrerior', 1, 13, 1305, 130510, '', '', '0', '2014-06-17 17:23:52', 1, 1, 1),
(59, 'Deudores del sistema', 1, 13, 1305, 130515, '', NULL, '0', '2014-06-17 17:23:52', 1, 1, 1),
(90, 'Inventarios', 1, 14, 14, 0, '', 'Comprende todos aquellos artículos, materiales, suministros, productos y recursos renovables y no renovables, para ser utilizados en procesos de transformación, consumo, alquiler o venta dentro de las actividades propias del giro ordinario de los negocios del ente económico.', '0', '2014-06-17 14:26:25', 1, 1, 1),
(91, 'Materias Primas', 1, 14, 1405, 0, '', 'Registra el valor de los elementos básicos adquiridos a nivel nacional o internacional, para uso en el proceso de fabricación o producción y que requieren procesamiento adicional. El costo lo constituirá el monto total del valor del artículo más los cargos incurridos hasta colocarlos en bodega para ser utilizados.', '0', '2014-06-17 14:26:25', 1, 1, 1),
(92, 'Ajustes por Inflacion', 1, 14, 1405, 140599, '', 'La descripción y dinámica descritas en la reglamentación, solo van hasta las cuentas. De manera que para saber como tratar las subcuentas, hay que observar el comportamiento de la cuenta superior.En este caso; la subcuenta: "140599 Ajustes por inflación" esta dentro de la cuenta 1405 Materias primas Dale clic para observar su descripción y dinámica.', '0', '2014-06-17 14:26:25', 1, 1, 1),
(93, 'Productos Terminados', 1, 14, 1430, 0, '', 'Registra el valor de las existencias de los diferentes bienes cosechados, extraídos o fabricados parcial o totalmente por el ente económico y que se encuentran disponibles para la comercialización.', '0', '2014-06-17 14:31:11', 1, 1, 1),
(94, 'Mercancias no fabricadas por la empresa', 1, 14, 1435, 0, '', 'Registra el valor de los bienes adquiridos para la venta por el ente económico que no sufren ningún proceso de transformación o adición y se encuentran disponibles para su enajenación.', '0', '2014-06-17 14:33:33', 1, 1, 1),
(95, 'Bienes raices para la venta', 1, 14, 1440, 0, '', 'Registra el valor de los terrenos y construcciones que tiene el ente económico totalmente adecuados y terminados y se encuentran disponibles para la venta, tales como terrenos, casas, apartamentos, bodegas, locales, edificios, oficinas, parqueaderos, garajes y bóvedas. El costo del terreno se deberá registrar por separado del costo de la respectiva construcción.', '0', '2014-06-17 14:34:27', 1, 1, 1),
(96, 'Terrenos', 1, 14, 1450, 0, '', 'Registra los costos y demás cargos capitalizables en que incurre el ente económico para la adquisición de terrenos que están destinados para la venta, o construcciones para la venta.Cuenta de uso exclusivo del ente económico dedicado a las actividades de construcción y/o venta de bienes raíces.', '0', '2014-06-17 14:30:02', 1, 1, 1),
(97, 'Materiales, repuestos y accesorios', 1, 14, 1455, 0, '', 'Registra el valor de los elementos que han sido adquiridos por el ente económico para consumir en la producción de bienes fabricados para la venta o en la prestación de servicios en todas y cada una de las operaciones realizadas en su normal funcionamiento. Comprende conceptos tales como elementos necesarios para mantenimiento y reparaciones, herramientas e implementos de trabajo, repuestos para maquinaria y equipo de producción.', '0', '2014-06-17 14:35:31', 1, 1, 1),
(98, 'Propiedades, planta y equipo', 1, 15, 0, 0, '', 'Comprende el conjunto de las cuentas que registran los bienes de cualquier naturaleza que posea el ente económico, con la intención de emplearlos en forma permanente para el desarrollo del giro normal de sus negocios o que se poseen por el apoyo que prestan en la producción de bienes y servicios, por definición no destinados para la venta en el curso normal de los negocios y cuya vida útil exceda de un año.', '0', '2014-06-17 17:40:18', 1, 1, 1),
(99, 'Terrenos', 1, 15, 1504, 0, '', NULL, '0', '2014-06-17 17:40:18', 1, 1, 1),
(100, 'Urbanos', 1, 15, 1504, 150405, '', NULL, '0', '2014-06-17 17:40:18', 1, 1, 1),
(101, 'Rurales', 1, 15, 1504, 150410, '', NULL, '0', '2014-06-17 17:40:18', 1, 1, 1),
(102, 'Ajustes por inflacion', 1, 15, 1504, 150499, '', NULL, '0', '2014-06-17 17:40:18', 1, 1, 1),
(103, 'Cuentas corrientes comerciales', 1, 13, 1310, 0, '', 'Registra el valor de las operaciones comerciales celebradas entre dos entes económicos, reguladas por las normas legales vigentes. Esta modalidad consiste en un negocio jurídico que tiene por características ser bilateral, oneroso, conmutativo y de ejecución sucesiva, para el cual se acuerdan anotar y compensar en cuenta abierta por debe y haber sus eventuales créditos recíprocos y se establecen condiciones de exigibilidad y disponibilidad del saldo resultante de la compensación progresiva opera', '0', NULL, 1, 1, 1),
(104, 'Casa Matriz', 1, 13, 1310, 131005, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(109, 'Companias Vinculadas', 1, 13, 1310, 131010, '', NULL, '0', '2014-07-17 11:08:28', 1, 1, 1),
(110, 'Accionistas o socios', 1, 13, 1310, 131015, '', NULL, '0', '2014-07-17 11:08:28', 1, 1, 1),
(111, 'Particulares', 1, 13, 1310, 131020, '', NULL, '0', '2014-07-17 11:08:28', 1, 1, 1),
(112, 'Otras', 1, 13, 1310, 131020, '', NULL, '0', '2014-07-17 11:08:28', 1, 1, 1),
(113, 'Intangibles', 1, 16, 0, 0, '', 'Comprende el conjunto de bienes inmateriales, representados en derechos, privilegios o ventajas de competencia que son valiosos porque contribuyen a un aumento en ingresos o utilidades por medio de su empleo en el ente económico; estos derechos se compran o se desarrollan en el curso normal de los negocios.', '0', '2014-07-17 11:22:08', 1, 1, 1),
(114, 'Credito Mercantil', 1, 16, 1605, 0, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(115, 'Formato a Estimado', 1, 16, 1605, 160505, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(116, 'Adquirido o Comprado', 1, 16, 1605, 160510, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(117, 'Ajustes por Inflacion', 1, 16, 1605, 160599, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(118, 'Marcas', 1, 16, 1610, 0, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(119, 'Adquiridas', 1, 16, 1610, 161005, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(120, 'Formadas', 1, 16, 1610, 161010, '', NULL, NULL, '2014-07-17 11:40:16', 1, 1, 1),
(121, 'Ajustes por Inflacion', 1, 16, 1610, 161099, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(122, 'Patentes', 1, 16, 1615, 0, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(123, 'Adquiridas', 1, 16, 1615, 161505, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(124, 'Formadas', 1, 16, 1615, 161510, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(125, 'Ajustes por Inflacion', 1, 16, 1615, 161599, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(126, 'Concesiones y Franquicias', 1, 16, 1620, 0, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(127, 'Concesiones', 1, 16, 1620, 162005, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(128, 'Franquicias', 1, 16, 1620, 162010, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(129, 'Ajustes por Inflacion', 1, 16, 1620, 162099, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(130, 'Drechos', 1, 16, 1625, 0, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(131, 'Derechos de Autor', 1, 16, 1625, 162505, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(132, 'Puesto de Bolsa', 1, 16, 1625, 162510, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(133, 'En fideicomisos inmobiliarios', 1, 16, 1625, 162515, '', NULL, '0', '2014-07-17 11:40:16', 1, 1, 1),
(134, 'en fideicomisos de garantia', 1, 16, 1625, 162520, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(135, 'know how', 1, 16, 1630, 0, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(136, 'Ajustes por inflacion', 1, 16, 1630, 163099, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(137, 'Licencias', 1, 16, 1635, 0, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(138, 'Ajustes por inflacion', 1, 16, 1630, 163599, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(139, 'Depreciacion y/o amortizacion acumulada', 1, 16, 1698, 0, '', '', '0', '2014-07-17 11:40:17', 1, 1, 1),
(140, 'Derechos', 1, 16, 1698, 169830, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(141, 'Know how', 1, 16, 1698, 169835, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(142, 'Licencias', 1, 16, 1698, 169840, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(143, 'Ajustes por inflacion', 1, 16, 1698, 169899, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(144, 'Provisiones', 1, 16, 1699, 0, '', '', '0', '2014-07-17 11:40:17', 1, 1, 1),
(145, 'Diferidos', 1, 17, 0, 0, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(146, 'Pasivo', 2, 0, 0, 0, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(147, 'Obligaciones Financieras', 2, 21, 0, 0, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(148, 'Bancos Nacionales', 2, 21, 2105, 0, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(149, 'Sobregiros', 2, 21, 2105, 210505, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(150, 'Pagarés', 2, 21, 2105, 210510, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(151, 'Cartas de Crédito', 2, 21, 2105, 210515, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(152, 'Aceptaciones Bancarias', 2, 21, 2105, 210520, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(153, 'Bancos del Exterior', 2, 21, 2110, 0, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(154, 'Sobregiros', 2, 21, 2110, 211005, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(155, 'Pagarés', 2, 21, 2110, 211010, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(156, 'Cartas de Crédito', 2, 21, 2110, 211015, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(157, 'Aceptaciones Financieras', 2, 21, 2110, 211020, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(158, 'Corporaciones Financieras', 2, 21, 2115, 0, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(159, 'Pagarés', 2, 21, 2115, 211505, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(160, 'Aceptaciones Financieras', 2, 21, 2115, 211510, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(161, 'Cartas de Crédito', 2, 21, 2115, 211515, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 2),
(162, 'Contratos de Arrendamiento Financiero (Leasin', 2, 21, 2115, 211520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(163, 'Compañías de Financiamiento Comercial', 2, 21, 2120, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(164, 'Pagarés', 2, 21, 2120, 212005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(165, 'Aceptaciones Financieras', 2, 21, 2120, 212010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(166, 'Contratos de Arrendamiento Financiero (Leasin', 2, 21, 2120, 212020, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(167, 'Corporaciones de Ahorro y Vivienda', 2, 21, 2125, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(168, 'Sobregiros', 2, 21, 2125, 212505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(169, 'Pagarés', 2, 21, 2125, 212510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(170, 'Hipotecarias', 2, 21, 2125, 212515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(171, 'Entidades Financieras del Exterior', 2, 21, 2130, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(172, 'Compromisos de Recompra de Inversiones Negoci', 2, 21, 2135, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(173, 'Acciones', 2, 21, 2135, 213505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(174, 'Cuotas o Partes de Interés Social', 2, 21, 2135, 213510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(175, 'Bonos', 2, 21, 2135, 213515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(176, 'Cédulas', 2, 21, 2135, 213520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(177, 'Certificados', 2, 21, 2135, 213525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(178, 'Papeles Comerciales', 2, 21, 2135, 213530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(179, 'Títulos', 2, 21, 2135, 213535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(180, 'Aceptaciones Bancarias o Financieras', 2, 21, 2135, 213540, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(181, 'Otros', 2, 21, 2135, 213595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(182, 'Compromisos de Recompra de Cartera Negociada', 2, 21, 2140, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(183, 'Obligaciones Gubernamentales', 2, 21, 2145, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(184, 'Gobierno Nacional', 2, 21, 2145, 214505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(185, 'Entidades Oficiales', 2, 21, 2145, 214510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(186, 'Otras Obligaciones', 2, 21, 2195, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(187, 'Particulares', 2, 21, 2195, 219505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(188, 'Compañías Vinculadas', 2, 21, 2195, 219510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(189, 'Casa Matriz', 2, 21, 2195, 219515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(190, 'Socios o Accionistas', 2, 21, 2195, 219520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(191, 'Fondos y Cooperativas', 2, 21, 2195, 219525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(192, 'Directores', 2, 21, 2195, 219530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(193, 'Otras', 2, 21, 2195, 219595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(194, 'Proveedores', 2, 22, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(195, 'Nacionales', 2, 22, 2205, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(196, 'Del Exterior', 2, 22, 2210, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(197, 'Cuentas Corrientes Comerciales', 2, 22, 2215, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(198, 'Casa Matriz', 2, 22, 2220, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(199, 'Compañías Vinculadas', 2, 22, 2225, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(200, 'Cuentas por Pagar', 2, 23, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(201, 'Cuentas Corrientes Comerciales', 2, 23, 2305, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(202, 'A Casa Matriz', 2, 23, 2310, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(203, 'A Compañias Vinculadas', 2, 23, 2315, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(204, 'A Contratistas', 2, 23, 2320, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(205, 'Órdenes de Compra por Utilizar', 2, 23, 2330, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(206, 'Costos y Gastos por Pagar', 2, 23, 2335, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(207, 'Gastos Financieros', 2, 23, 2335, 233505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(208, 'Gastos Legales', 2, 23, 2335, 233510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(209, 'Libros, Suscripciones, Periódicos y Revistas', 2, 23, 2335, 233515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(210, 'Comisiones', 2, 23, 2335, 233520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(211, 'Honorarios', 2, 23, 2335, 233525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(212, 'Servicios Técnicos', 2, 23, 2335, 233530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(213, 'Servicios de Mantenimiento', 2, 23, 2335, 233535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(214, 'Arrendamientos', 2, 23, 2335, 233540, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(215, 'Transportes, Fletes y Acarreos', 2, 23, 2335, 233545, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(216, 'Servicios Públicos', 2, 23, 2335, 233550, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(217, 'Seguros', 2, 23, 2335, 233555, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(218, 'Gastos de Viaje', 2, 23, 2335, 233560, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(219, 'Gastos de Representación y Relaciones Pública', 2, 23, 2335, 233565, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(220, 'Servicios Aduaneros', 2, 23, 2335, 233570, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(221, 'Otros', 2, 23, 2335, 233595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(222, 'Instalamentos por Pagar', 2, 23, 2340, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(223, 'Acreedores Oficiales', 2, 23, 2345, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(224, 'Regalías por Pagar', 2, 23, 2350, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(225, 'Deudas con Accionistas o Socios', 2, 23, 2355, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(226, 'Accionistas', 2, 23, 2355, 235505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(227, 'Socios', 2, 23, 2355, 235510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(228, 'Deudas con Directores', 2, 23, 2357, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(229, 'Dividendos o Participaciones por Pagar', 2, 23, 2360, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(230, 'Dividendos', 2, 23, 2360, 236005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(231, 'Participaciones', 2, 23, 2360, 236010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(232, 'Retención en la Fuente', 2, 23, 2365, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(233, 'Salarios y Pagos Laborales', 2, 23, 2365, 236505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(234, 'Dividendos y/o Participaciones', 2, 23, 2365, 236510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(235, 'Honorarios', 2, 23, 2365, 236515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(236, 'Comisiones', 2, 23, 2365, 236520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(237, 'Servicios', 2, 23, 2365, 236525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(238, 'Arrendamientos', 2, 23, 2365, 236530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(239, 'Rendimientos Financieros', 2, 23, 2365, 236535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(240, 'Compras', 2, 23, 2365, 236540, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(241, 'Loterías, Rifas, Apuestas y Similares', 2, 23, 2365, 236545, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(242, 'Por Pagos al Exterior', 2, 23, 2365, 236550, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(243, 'Por Ingresos Obtenidos en el Exterior', 2, 23, 2365, 236555, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(244, 'Enajenación Propiedades Planta y Equipo, Pers', 2, 23, 2365, 236560, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(245, 'Por Impuesto de Timbre', 2, 23, 2365, 236565, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(246, 'Otras Retenciones y Patrimonio', 2, 23, 2365, 236570, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(247, 'Autorretenciones', 2, 23, 2365, 236575, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(248, 'Impuesto a las Ventas Retenido', 2, 23, 2367, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(249, 'Impuesto de Industria y Comercio Retenido', 2, 23, 2368, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(250, 'Retenciones y Aportes de Nómina', 2, 23, 2370, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(251, 'Aportes a Entidades Promotoras de Salud, EPS', 2, 23, 2370, 237005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(252, 'Aportes a Administradoras de Riesgos Profesio', 2, 23, 2370, 237006, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(253, 'Aportes ICBF, SENA y Cajas de Compensación', 2, 23, 2370, 237010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(254, 'Aportes FIC', 2, 23, 2370, 237015, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(255, 'Embargos Judiciales', 2, 23, 2370, 237025, '', NULL, '', '2014-07-17 11:40:17', 1, 1, 2),
(256, 'Libranzas', 2, 23, 2370, 237030, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(257, 'Sindicatos', 2, 23, 2370, 237035, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(258, 'Cooperativas', 2, 23, 2370, 237040, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(259, 'Fondos', 2, 23, 2370, 237045, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(260, 'Otros', 2, 23, 2370, 237095, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(261, 'Cuotas por Devolver', 2, 23, 2375, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(262, 'Acreedores Varios', 2, 23, 2380, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(263, 'Depositarios', 2, 23, 2380, 238005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(264, 'Comisionistas de Bolsas', 2, 23, 2380, 238010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(265, 'Sociedad Administradora-Fondos Inversión', 2, 23, 2380, 238015, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(266, 'Reintegros por Pagar', 2, 23, 2380, 238020, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(267, 'Fondo de Perseverancia', 2, 23, 2380, 238025, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(268, 'Fondos de Cesantías y/o pensiones', 2, 23, 2380, 238030, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(269, 'Donaciones Asignadas por Pagar', 2, 23, 2380, 238035, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(270, 'Otros', 2, 23, 2380, 238095, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(271, 'Impuestos, Gravámenes y Tasas', 2, 24, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(272, 'De Renta y Complementarios', 2, 24, 2404, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(273, 'Vigencia Fiscal Corriente', 2, 24, 2404, 240405, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(274, 'Vigencias Fiscales Anteriores', 2, 24, 2404, 240410, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(275, 'Impuesto Sobre las Ventas por Pagar', 2, 24, 2408, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(276, 'De Industria y Comercio', 2, 24, 2412, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(277, 'Vigencia Fiscal Corriente', 2, 24, 2412, 241205, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(278, 'Vigencias Fiscales Anteriores', 2, 24, 2412, 241210, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(279, 'A la Propiedad Raiz', 2, 24, 2416, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(280, 'Derechos Sobre Instrumentos Públicos', 2, 24, 2420, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(281, 'De Valorización', 2, 24, 2424, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(282, 'Vigencia Fiscal Corriente', 2, 24, 2424, 242405, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(283, 'Vigencias Fiscales Anteriores', 2, 24, 2424, 242410, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(284, 'De Turismo', 2, 24, 2428, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(285, 'Tasa por Utilización de Puertos', 2, 24, 2432, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(286, 'De Vehículos', 2, 24, 2436, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(287, 'Vigencia Fiscal Corriente', 2, 24, 2436, 243605, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(288, 'Vigencias Fiscales Anteriores', 2, 24, 2436, 243610, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(289, 'De Espectáculos Públicos', 2, 24, 2440, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(290, 'De Hidrocarburos y Minas', 2, 24, 2444, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(291, 'De Hidrocarburos', 2, 24, 2444, 244405, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(292, 'De Minas', 2, 24, 2444, 244410, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(293, 'Regalías e impuestos a la Pequeña Minería', 2, 24, 2448, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(294, 'A las Exportaciones Cafeteras', 2, 24, 2452, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(295, 'A las Importaciones', 2, 24, 2456, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(296, 'Cuotas de Fomento', 2, 24, 2460, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(297, 'De Licores, Cervezas y Cigarrillos', 2, 24, 2464, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(298, 'De Licores', 2, 24, 2464, 246405, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(299, 'De Cervezas', 2, 24, 2464, 246410, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(300, 'De Cigarrillos', 2, 24, 2464, 246415, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(301, 'Al Sacrificio de Ganado', 2, 24, 2468, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(302, 'Al Azar y Juegos', 2, 24, 2472, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(303, 'Gravámenes y Regalías por Utilización de Suel', 2, 24, 2476, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(304, 'Otros', 2, 24, 2495, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(305, 'Obligaciones Laborales', 2, 25, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(306, 'Salarios por Pagar', 2, 25, 2505, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(307, 'Cesantías Consolidadas', 2, 25, 2510, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(308, 'Ley Laboral Anterior', 2, 25, 2510, 251005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(309, 'Ley 50 de 1990 y Normas Posteriores', 2, 25, 2510, 251010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(310, 'Msteriales proyectos petroleros', 1, 15, 1506, 0, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(311, 'Tuberias y equipo', 1, 15, 1506, 150605, '', NULL, '0', '2014-07-17 11:40:17', 1, 1, 1),
(312, 'Costos de importacion de materiales', 1, 15, 1506, 150610, '', NULL, '0', NULL, 1, 1, 1),
(313, 'Proyectos de construccion', 1, 15, 1506, 150615, '', NULL, '0', NULL, 1, 1, 1),
(314, 'Ajsutes por inflacion', 1, 15, 1506, 150699, '', NULL, '0', NULL, 1, 1, 1),
(315, 'Construcciones en curso', 1, 15, 1508, 0, '', NULL, '0', NULL, 1, 1, 1),
(316, 'Construcciones y Edificaciones', 1, 15, 1508, 150805, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(317, 'Acueductos, plantas y redes', 1, 15, 1508, 150810, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(318, 'Vias de comunicacion', 1, 15, 1508, 150815, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(319, 'Pozos Artesianos', 1, 15, 1508, 150820, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(320, 'Proyectos de exploracion', 1, 15, 1508, 150825, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(321, 'Proyectos de desarrollo', 1, 15, 1508, 150830, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(322, 'Ajustes por inflacion', 1, 15, 1508, 150899, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(323, 'Maquinarias y equipos en montaje', 1, 15, 1512, 0, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(324, 'Maquinaria y equipos', 1, 15, 1512, 151205, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(325, 'Equipo de oficina', 1, 15, 1512, 151210, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(326, 'Equipo de computacion y comunicacion', 1, 15, 1512, 151215, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(327, 'Equipo medico cientifico', 1, 15, 1512, 151220, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(328, 'Equipo de hoteles y restaurantes', 1, 15, 1512, 151225, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(329, 'Flota y equpo de transporte', 1, 15, 1512, 151230, '', NULL, '0', '2014-09-02 09:33:42', 1, 1, 1),
(330, 'Flota y equipo fluvial y/o maritimo', 1, 15, 1512, 151235, '', NULL, '0', '2014-09-02 09:37:12', 1, 1, 1),
(331, 'Flota y equpo aereo', 1, 15, 1512, 151240, '', NULL, '0', '2014-09-02 09:37:12', 1, 1, 1),
(332, 'Flota y equipo ferreo', 1, 15, 1512, 151245, '', NULL, '0', '2014-09-02 09:37:12', 1, 1, 1),
(333, 'Plantas y redes', 1, 15, 1512, 151250, '', NULL, '0', '2014-09-02 09:37:12', 1, 1, 1),
(334, 'Ajustes por inflacion', 1, 15, 1512, 151299, '', NULL, '0', '2014-09-02 09:37:12', 1, 1, 1),
(335, 'Construcciones y edificaciones', 1, 15, 1516, 0, '', NULL, '0', '2014-09-02 09:37:12', 1, 1, 1),
(336, '·Edificios', 1, 15, 1516, 151605, '', NULL, '0', '2014-09-02 09:38:45', 1, 1, 1),
(337, 'Oficinas', 1, 15, 1516, 151610, '', NULL, '0', '2014-09-02 09:38:45', 1, 1, 1),
(338, 'Almacenes', 1, 15, 1516, 151615, '', NULL, '0', '2014-09-02 09:38:45', 1, 1, 1),
(339, 'Fabricas y plantas industriales', 1, 15, 1516, 151620, '', NULL, '0', '2014-09-02 09:38:45', 1, 1, 1),
(340, 'Intereses sobre Cesantías', 2, 25, 2515, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(341, 'Prima de Servicios', 2, 25, 2520, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(342, 'Vacaciones Consolidadas', 2, 25, 2525, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(343, 'Prestaciones Extralegales', 2, 25, 2530, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(344, 'Primas', 2, 25, 2530, 253005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(345, 'Auxilios', 2, 25, 2530, 253010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(346, 'Dotación y Suministro a Trabajadores', 2, 25, 2530, 253015, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(347, 'Bonificaciones', 2, 25, 2530, 253020, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(348, 'Seguros', 2, 25, 2530, 253025, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(349, 'Otras', 2, 25, 2530, 253095, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(350, 'Pensiones por Pagar', 2, 25, 2532, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(351, 'Cuotas Partes Pensiones de Jubilación', 2, 25, 2535, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(352, 'Indemnizaciones Laborales', 2, 25, 2540, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(353, 'Pasivos Estimados y Provisiones', 2, 26, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(354, 'Para Costos y Gastos', 2, 26, 2605, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(355, 'Intereses', 2, 26, 2605, 260505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(356, 'Comisiones', 2, 26, 2605, 260510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(357, 'Honorarios', 2, 26, 2605, 260515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(358, 'Servicios Técnicos', 2, 26, 2605, 260520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(359, 'Transportes, Fletes y Acarreos', 2, 26, 2605, 260525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(360, 'Gastos de Viaje', 2, 26, 2605, 260530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(361, 'Servicios Públicos', 2, 26, 2605, 260535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(362, 'Regalías', 2, 26, 2605, 260540, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(363, 'Garantías', 2, 26, 2605, 260545, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(364, 'Materiales y Repuestos', 2, 26, 2605, 260550, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(365, 'Otros', 2, 26, 2605, 260595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(366, 'Para Obligaciones Laborales', 2, 26, 2610, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(367, 'Cesantías', 2, 26, 2610, 261005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(368, 'Intereses Sobre Cesantías', 2, 26, 2610, 261010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(369, 'Vacaciones', 2, 26, 2610, 261015, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(370, 'Prima de Servicios', 2, 26, 2610, 261020, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(371, 'Prestaciones Extralegales', 2, 26, 2610, 261025, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(372, 'Viáticos', 2, 26, 2610, 261030, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(373, 'Otras', 2, 26, 2610, 261095, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(374, 'Para Obligaciones Fiscales', 2, 26, 2615, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(375, 'De Renta y Complementarios', 2, 26, 2615, 261505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(376, 'De Industria y Comercio', 2, 26, 2615, 261510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(377, 'Tasa por Utilización de Puertos', 2, 26, 2615, 261515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(378, 'De Vehículos', 2, 26, 2615, 261520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(379, 'De Hidrocarburos y Minas', 2, 26, 2615, 261525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(380, 'Otros', 2, 26, 2615, 261595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(381, 'Pensiones de Jubilación', 2, 26, 2620, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(382, 'Cálculo Actuarial Pensiones de Jubilación', 2, 26, 2620, 262005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(383, 'Pensiones de Jubilación por Amortizar (DB)', 2, 26, 2620, 262010, '', NULL, '', '2014-07-17 11:40:17', 1, 1, 2),
(384, 'Salas de exhibicion y ventas', 1, 15, 1516, 151625, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(385, 'Cafeteria y casinos', 1, 15, 1516, 151630, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(386, 'Silos', 1, 15, 1516, 151635, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(387, 'Invernaderos', 1, 15, 1516, 151640, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(388, 'Casetas y campamentos', 1, 15, 1516, 151645, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(389, 'Instalaciones agropecuarias', 1, 15, 1516, 151650, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(390, 'Vivienda para empleados y obreros', 1, 15, 1516, 151655, '', '', '0', '2014-09-02 09:48:58', 1, 1, 1),
(391, 'Terminal de buses y taxis', 1, 15, 1516, 151660, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(392, 'Terminal maritimo', 1, 15, 1516, 151663, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(393, 'Terminal ferreo', 1, 15, 1516, 151665, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(394, 'Parqueaderos, garajes y otros', 1, 15, 1516, 151670, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(395, 'Hangares', 1, 15, 1516, 151675, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(396, 'Bodegas', 1, 15, 1516, 151680, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(397, 'Otros', 1, 15, 1516, 151695, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(398, 'Ajustes por inflacion', 1, 15, 1516, 151699, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(399, 'Maquinaria y equipò', 1, 15, 1520, 0, '', NULL, '0', '2014-09-02 09:48:58', 1, 1, 1),
(400, 'Ajustes por inflacion', 1, 15, 1520, 152099, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(401, 'Equipo de oficina', 1, 15, 1524, 0, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(402, 'Muebles y enceres', 1, 15, 1524, 152405, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(403, 'Equipos', 1, 15, 1524, 152410, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(404, 'Otros', 1, 15, 1524, 152495, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(405, 'Ajustes por inflacion', 1, 15, 1524, 152499, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(406, 'Equipo de computacion y comunicacion', 1, 15, 1528, 0, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(407, 'Equipos de procesamiento de datos', 1, 15, 1528, 152805, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(408, 'Equipos de telecomunicaciones', 1, 15, 1528, 152810, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(409, 'Equipos de radio', 1, 15, 1528, 152815, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(410, 'Satelites y antenas', 1, 15, 1528, 152820, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(411, 'Lineas telefonicas', 1, 15, 1528, 152825, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(412, 'Otros', 1, 15, 1528, 152895, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(413, 'Ajustes por inflacion', 1, 15, 1528, 152899, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(414, 'Equipo medico', 1, 15, 1532, 0, '', NULL, '0', '2014-09-02 10:04:31', 1, 1, 1),
(415, 'Medico', 1, 15, 1532, 153205, '', NULL, '0', '2014-09-02 10:07:50', 1, 1, 1),
(416, 'Odontologico', 1, 15, 1532, 153210, '', NULL, '0', '2014-09-02 10:07:50', 1, 1, 1),
(417, 'Laboratorio', 1, 15, 1532, 153215, '', NULL, '0', '2014-09-02 10:07:50', 1, 1, 1),
(418, 'Instrumental', 1, 15, 1532, 153220, '', NULL, '0', '2014-09-02 10:07:50', 1, 1, 1),
(419, 'Otros', 1, 15, 1532, 153295, '', NULL, '0', '2014-09-02 10:07:50', 1, 1, 1),
(420, 'Ajustes por inflacion', 1, 15, 1532, 153299, '', NULL, '0', '2014-09-02 10:07:50', 1, 1, 1),
(421, 'Para Obras de Urbanismo', 2, 26, 2625, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(422, 'Acueducto y Alcantarillado', 2, 26, 2625, 262505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(423, 'Energía Eléctrica', 2, 26, 2625, 262510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(424, 'Teléfonos', 2, 26, 2625, 262515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(425, 'Otros', 2, 26, 2625, 262595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(426, 'Para Mantenimiento y Reparaciones', 2, 26, 2630, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(427, 'Terrenos', 2, 26, 2630, 263005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(428, 'Construcciones y Edificaciones', 2, 26, 2630, 263010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(429, 'Maquinaria y Equipo', 2, 26, 2630, 263015, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(430, 'Equipo de Oficina', 2, 26, 2630, 263020, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(431, 'Equipo de Computación y Comunicación', 2, 26, 2630, 263025, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(432, 'Equipo Médico-Científico', 2, 26, 2630, 263030, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(433, 'Equipo de Hoteles y Restaurantes', 2, 26, 2630, 263035, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(434, 'Flota y Equipo de Transporte', 2, 26, 2630, 263040, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(435, 'Flota y Equipo Fluvial y/o Marítimo', 2, 26, 2630, 263045, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(436, 'Flota y Equipo Aéreo', 2, 26, 2630, 263050, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(437, 'Flota y Equipo Férreo', 2, 26, 2630, 263055, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(438, 'Acueductos, Plantas y Redes', 2, 26, 2630, 263060, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(439, 'Armamento de Vigilancia', 2, 26, 2630, 263065, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(440, 'Envases y Empaques', 2, 26, 2630, 263070, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(441, 'Plantaciones Agrícolas y Forestales', 2, 26, 2630, 263075, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(442, 'Vías de Comunicación', 2, 26, 2630, 263080, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(443, 'Pozos Artesianos', 2, 26, 2630, 263085, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(444, 'Otros', 2, 26, 2630, 263095, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(445, 'Para Contingencias', 2, 26, 2635, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(446, 'Multas y Sanciones Autoridades Administrativa', 2, 26, 2635, 263505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(447, 'Intereses por Multas y Sanciones', 2, 26, 2635, 263510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(448, 'Reclamos', 2, 26, 2635, 263515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(449, 'Laborales', 2, 26, 2635, 263520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(450, 'Civiles', 2, 26, 2635, 263525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(451, 'Penales', 2, 26, 2635, 263530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(452, 'Administrativos', 2, 26, 2635, 263535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(453, 'Comerciales', 2, 26, 2635, 263540, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(454, 'Otras', 2, 26, 2635, 263595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(455, 'Para Obligaciones de Garantías', 2, 26, 2640, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(456, 'Provisiones Diversas', 2, 26, 2695, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(457, 'Para Beneficencia', 2, 26, 2695, 269505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(458, 'Para Comunicaciones', 2, 26, 2695, 269510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(459, 'Para Pérdida en Transporte', 2, 26, 2695, 269515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(460, 'Para Operación', 2, 26, 2695, 269520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(461, 'Para Protección de Bienes Agotablers', 2, 26, 2695, 269525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(462, 'Para Ajustes en Redención de Unidades', 2, 26, 2695, 269530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(463, 'Autoseguro', 2, 26, 2695, 269535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(464, 'Planes y Programas de Reforestación y Electri', 2, 26, 2695, 269540, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(465, 'Otras', 2, 26, 2695, 269595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(466, 'Diferidos', 2, 27, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(467, 'Ingresos Recibidos por Anticipado', 2, 27, 2705, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(468, 'Intereses', 2, 27, 2705, 270505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(469, 'Comisiones', 2, 27, 2705, 270510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(470, 'Arrendamientos', 2, 27, 2705, 270515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(471, 'Honorarios', 2, 27, 2705, 270520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(472, 'Servicios Técnicos', 2, 27, 2705, 270525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(473, 'De Suscriptores', 2, 27, 2705, 270530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(474, 'Transporte, Fletes y Acarreos', 2, 27, 2705, 270535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(475, 'Mercancía en Tránsito ya Vendida', 2, 27, 2705, 270540, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(476, 'Matrículas y Pensiones', 2, 27, 2705, 270545, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(477, 'Cuotas de Administración', 2, 27, 2705, 270550, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(478, 'Otros', 2, 27, 2705, 270595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(479, 'Abonos Diferidos', 2, 27, 2710, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(480, 'Reajuste del Sistema', 2, 27, 2710, 271005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(481, 'Utilidad Diferida en Ventas a Plazos', 2, 27, 2715, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(482, 'Crédito por Corrección Monetaria Diferida', 2, 27, 2720, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(483, 'Impuestos Diferidos', 2, 27, 2725, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(484, 'Por Depreciación Flexible', 2, 27, 2725, 272505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(485, 'Diversos', 2, 27, 2725, 272595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(486, 'Ajustes por Inflación', 2, 27, 2725, 272599, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(487, 'Otros Pasivos', 2, 28, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(488, 'Anticipos y Avances Recibidos', 2, 28, 2805, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(489, 'De Clientes', 2, 28, 2805, 280505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(490, 'Sobre Contratos', 2, 28, 2805, 280510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(491, 'Para Obras en Proceso', 2, 28, 2805, 280515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(492, 'Otros', 2, 28, 2805, 280595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(493, 'Depósitos Recibidos', 2, 28, 2810, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(494, 'Para Futura Suscripción de Acciones', 2, 28, 2810, 281005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(495, 'Para Futuro Pago de Cuotas o Derechos Sociale', 2, 28, 2810, 281010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(496, 'Para Garantía en la Prestación de Servicios', 2, 28, 2810, 281015, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(497, 'Para Garantía de Contratos', 2, 28, 2810, 281020, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(498, 'De Licitaciones', 2, 28, 2810, 281025, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(499, 'De Manejo de Bienes', 2, 28, 2810, 281030, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(500, 'Fondo de Reserva', 2, 28, 2810, 281035, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(501, 'Otros', 2, 28, 2810, 281095, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(502, 'Ingresos Recibidos para Terceros', 2, 28, 2815, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(503, 'Valores Recibidos para Terceros', 2, 28, 2815, 281505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(504, 'Venta por Cuenta de Terceros', 2, 28, 2815, 281510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(505, 'Cuentas de Operación Conjunta', 2, 28, 2820, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2);
INSERT INTO `con_cuenta` (`cod_cuenta`, `nom_cuenta`, `clase_cuenta`, `grupo_cuenta`, `cuenta_cuenta`, `sub_cuenta`, `num_cuenta`, `des_cuenta`, `imp_inicial_cuenta`, `fec_cuenta`, `ind_balance_general`, `cod_usuario`, `cod_tipocuenta`) VALUES
(506, 'Retenciones a Terceros sobre Contratos', 2, 28, 2825, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(507, 'Cumplimiento de Obligaciones Laborales', 2, 28, 2825, 282505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(508, 'Para Estabilidad de Obra', 2, 28, 2825, 282510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(509, 'Garantía de Cumplimiento de Contratos', 2, 28, 2825, 282515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(510, 'Embargos Judiciales', 2, 28, 2830, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(511, 'Indemnizaciones', 2, 28, 2830, 283005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(512, 'Equipos de hoteles y restaurantes', 1, 15, 1536, 0, '', NULL, '0', '2014-09-02 10:18:53', 1, 1, 1),
(513, 'De habitaciones', 1, 15, 1536, 153605, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(514, 'De comestibles y bebidas', 1, 15, 1536, 153610, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(515, 'Otros', 1, 15, 1536, 153695, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(516, 'Ajustes por inflacion', 1, 15, 1536, 153699, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(517, 'Flota y equipo de trasporte', 1, 15, 1540, 0, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(518, 'Autos, camionetas y camperos', 1, 15, 1540, 154005, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(519, 'Camiones, volquetas y furgones', 1, 15, 1540, 154008, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(520, 'Tractomulas y remolques', 1, 15, 1540, 154010, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(521, 'Buses y busetas', 1, 15, 1540, 154015, '', '', '0', '2014-09-02 10:18:54', 1, 1, 1),
(522, 'Recolectores y contenedores', 1, 15, 1540, 154017, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(523, 'Montacargas', 1, 15, 1540, 154020, '', '', '0', '2014-09-02 10:18:54', 1, 1, 1),
(524, 'Palas y gruas', 1, 15, 1540, 154025, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(525, 'Motocicletas', 1, 15, 1540, 154030, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(526, 'Bicicletas', 1, 15, 1540, 154030, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(527, 'Estibas y carretaras', 1, 15, 1540, 154035, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(528, 'Bandas Transportadoras', 1, 15, 1540, 154040, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(529, 'Otros', 1, 15, 1540, 154095, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(530, 'Ajustes por inflacion', 1, 15, 1540, 154099, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(531, 'Flota y equipo fluvial y/o maritimo', 1, 15, 1544, 0, '', NULL, '0', '2014-09-02 10:18:54', 1, 1, 1),
(532, 'Buques', 1, 15, 1544, 154405, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(533, 'Lanchas', 1, 15, 1544, 154410, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(534, 'Remolcadoras', 1, 15, 1544, 154415, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(535, 'Botes', 1, 15, 1544, 154420, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(536, 'Boyas', 1, 15, 1544, 154425, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(537, 'Amarres', 1, 15, 1544, 154430, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(538, 'Conteneodres y chasises', 1, 15, 1544, 154435, '', '', '0', '2014-09-02 10:37:13', 1, 1, 1),
(539, 'Gabarras', 1, 15, 1544, 154440, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(540, 'Otros', 1, 15, 1544, 154495, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(541, 'Ajustes por inflacion', 1, 15, 1544, 154499, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(542, 'Flota y equpo aereo', 1, 15, 1548, 0, '', NULL, '0', '2014-09-02 10:37:13', 1, 1, 1),
(543, 'Aviones', 1, 15, 1548, 154805, '', NULL, '0', '2014-09-02 11:57:42', 1, 1, 1),
(544, 'Avionetas', 1, 15, 1548, 154810, '', NULL, '0', '2014-09-02 11:57:43', 1, 1, 1),
(545, 'Helicopteros', 1, 15, 1548, 154815, '', NULL, '0', '2014-09-02 11:57:43', 1, 1, 1),
(546, 'Turbinas y motores', 1, 15, 1548, 154820, '', NULL, '0', '2014-09-02 11:57:43', 1, 1, 1),
(547, 'Manuales de entrenamiento personal tecnico', 1, 15, 1548, 154825, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(548, 'Equipos de vuelo', 1, 15, 1548, 154830, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(549, 'Otros', 1, 15, 1548, 154895, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(550, 'Ajustes por inflacion', 1, 15, 1548, 154899, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(551, 'Flota y equpo ferreo', 1, 15, 1552, 0, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(552, 'Locomotoras', 1, 15, 1552, 155205, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(553, 'Vagones', 1, 15, 1552, 155210, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(554, 'Redes ferreas', 1, 15, 1552, 155215, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(555, 'Otros', 1, 15, 1552, 155295, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(556, 'Ajustes por inflacion', 1, 15, 1552, 155299, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(557, 'Acueductos, plantas y redes', 1, 15, 1556, 0, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(558, 'Instalaciones para agua y energia', 1, 15, 1556, 155605, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(559, 'Acueducto, acequias y canalizacion', 1, 15, 1556, 155610, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(560, 'Plantas de generacion hidraulica', 1, 15, 1556, 155615, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(561, 'Plantas de generacion termica', 1, 15, 1556, 155620, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(562, 'Plantas de generacion de gas', 1, 15, 1556, 155625, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(563, 'Plantas de generacion diesel, gasolina y petr', 1, 15, 1556, 155628, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(564, 'Plantas de distribucion', 1, 15, 1556, 155630, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(565, 'Plantas de transmision y subestaciones', 1, 15, 1556, 155635, '', '', '0', '2014-09-02 11:57:44', 1, 1, 1),
(566, 'Oleoductos', 1, 15, 1556, 155640, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(567, 'Gasoductos', 1, 15, 1556, 155645, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(568, 'Poliductos', 1, 15, 1556, 155647, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(569, 'Redes de distribucion', 1, 15, 1556, 155650, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(570, 'Redes de tratamiento', 1, 15, 1556, 155655, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(571, 'Redes de recoleccion de aguas negras', 1, 15, 1556, 155660, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(572, 'Instalaciones y equipo de bombeo', 1, 15, 1556, 155665, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(573, 'Redes de distribucion de vapor', 1, 15, 1556, 155670, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(574, 'Redes de aire', 1, 15, 1556, 155675, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(575, 'Redes alimentacion de gas', 1, 15, 1556, 155680, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(576, 'Redes externas de telefonia', 1, 15, 1556, 155682, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(577, 'Plantas deshidratadoras', 1, 15, 1556, 155685, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(578, 'Otros', 1, 15, 1556, 155695, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(579, 'Ajustes por inflacion', 1, 15, 1556, 155699, '', NULL, '0', '2014-09-02 11:57:44', 1, 1, 1),
(580, 'Depósitos Judiciales', 2, 28, 2830, 283010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(581, 'Acreedores del Sistema', 2, 28, 2835, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(582, 'Coutas Netas', 2, 28, 2835, 283505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(583, 'Grupos en Formación', 2, 28, 2835, 283510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(584, 'Coutas en Participación', 2, 28, 2840, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(585, 'Diversos', 2, 28, 2895, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(586, 'Préstamos de Productos', 2, 28, 2895, 289505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(587, 'Reembolso de Costos Exploratorios', 2, 28, 2895, 289510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(588, 'Programa de Extensión Agropecuaria', 2, 28, 2895, 289515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(589, 'Bonos y Papeles Comerciales', 2, 29, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(590, 'Bonos de Circulación', 2, 29, 2905, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(591, 'Bonos Obligatoriamente Convertibles en Accion', 2, 29, 2910, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(592, 'Papeles Comerciales', 2, 29, 2915, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(593, 'Bonos Pensionales', 2, 29, 2920, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(594, 'Valor Bonos Pensionales', 2, 29, 2920, 292005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(595, 'Bonos Pensionales por Amortizar (BD)', 2, 29, 2920, 292010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(596, 'Intereses Causados sobre Bonos Pensionales', 2, 29, 2920, 292015, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(597, 'Títulos Pensionales', 2, 29, 2925, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(598, 'Valor Títulos Pensionales', 2, 29, 2925, 292505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(599, 'Títulos Pensionales por Amortizar (BD)', 2, 29, 2925, 292510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(600, 'Intereses Causados sobre Títulos Pensionales', 2, 29, 2925, 292515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 2),
(601, 'Arammento de vigilancia', 1, 15, 1560, 0, '', NULL, '0', '2014-09-02 13:56:55', 1, 1, 1),
(602, 'Ajustes por inflacion', 1, 15, 1560, 156099, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(603, 'Envases y empaques', 1, 15, 1562, 0, '', NULL, '0', '2014-09-02 13:56:55', 1, 1, 1),
(604, 'Ajustes por inflacion', 1, 15, 1562, 156299, '', NULL, '0', '2014-09-02 13:56:55', 1, 1, 1),
(605, 'Plantaciones agricolas y forestales', 1, 15, 1564, 0, '', NULL, '0', '2014-09-02 13:56:55', 1, 1, 1),
(606, 'Cultivos en desarrollo', 1, 15, 1564, 156405, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(607, 'Cultivos Amortizables', 1, 15, 1564, 156410, '', NULL, '0', '2014-09-02 13:56:55', 1, 1, 1),
(608, 'Ajustes por inflacion', 1, 15, 1564, 156499, '', NULL, '0', '2014-09-02 13:56:55', 1, 1, 1),
(609, 'Patrimonio', 3, 0, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(610, 'Capital Social', 3, 31, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(611, 'Capital Suscrito Pagado', 3, 31, 3105, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(612, 'Capital Autorizado', 3, 31, 3105, 310505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(613, 'Capital por Suscribir (DB)', 3, 31, 3105, 310510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(614, 'Capital Suscrito por Cobrar (DB)', 3, 31, 3105, 310515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(615, 'Aportes Sociales', 3, 31, 3115, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(616, 'Cuotas o Partes de Interés Social', 3, 31, 3115, 311505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(617, 'Aportes de Socios-Fondo Mutuo de Inversión', 3, 31, 3115, 311510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(618, 'Contribución de la Empresa-Fondo Mutuo de Inv', 3, 31, 3115, 311515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(619, 'Suscripciones del Público', 3, 31, 3115, 311520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(620, 'Capital Asignado', 3, 31, 3120, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(621, 'Inversión Suplementaria al Capital Asignado', 3, 31, 3125, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(622, 'Capital de Personas Naturales', 3, 31, 3130, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(623, 'Aportes del Estado', 3, 31, 3135, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(624, 'Fondo Social', 3, 31, 3140, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(625, 'Superávit de Capital', 3, 32, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(626, 'Prima en Colocación de Acciones, Cuotas o Par', 3, 32, 3205, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(627, 'Prima en Colocación de Acciones', 3, 32, 3205, 320505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(628, 'Prima en Colocación de Acciones por Cobrar (D', 3, 32, 3205, 320510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(629, 'Prima en Colocación de Cuotas o Partes de Int', 3, 32, 3205, 320515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(630, 'Donaciones', 3, 32, 3210, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(631, 'En Dinero', 3, 32, 3210, 321005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(632, 'En Valores Mobiliarios', 3, 32, 3210, 321010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(633, 'En Bienes Muebles', 3, 32, 3210, 321015, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(634, 'En Bienes Inmuebles', 3, 32, 3210, 321020, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(635, 'En Intangibles', 3, 32, 3210, 321025, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(636, 'Crédito Mercantil', 3, 32, 3215, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(637, 'Know How', 3, 32, 3220, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(638, 'Superávit Método de Participación', 3, 32, 3225, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(639, 'De Acciones', 3, 32, 3225, 322505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(640, 'De Cuotas o Partes de Interés Social', 3, 32, 3225, 322510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(641, 'Reservas', 3, 33, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(642, 'Reservas Obligatorias', 3, 33, 3305, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(643, 'Reserva Legal', 3, 33, 3305, 330505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(644, 'Reserva por Disposiciones Fiscales', 3, 33, 3305, 330510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(645, 'Reserva para Adquisición de Acciones', 3, 33, 3305, 330515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(646, 'Acciones Propias Readquiridas (DB)', 3, 33, 3305, 330516, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(647, 'Vias de comunicacion', 1, 15, 1568, 0, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(648, 'Pavimentacion y patios', 1, 15, 1568, 156805, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(649, 'Visa', 1, 15, 1568, 156810, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(650, 'Puentes', 1, 15, 1568, 156815, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(651, 'Calles', 1, 15, 1568, 156820, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(652, 'Aerodromos', 1, 15, 1568, 156825, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(653, 'Otros', 1, 15, 1568, 156895, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(654, 'Ajustes por imflacion', 1, 15, 1568, 156899, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(655, 'Minas y canteras', 1, 15, 1572, 0, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(656, 'Minas', 1, 15, 1572, 157205, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(657, 'Canteras', 1, 15, 1572, 157210, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(658, 'Ajustes por inflacion', 1, 15, 1572, 157299, '', NULL, '0', '2014-09-02 14:17:51', 1, 1, 1),
(659, 'Yacimientos', 1, 15, 1580, 0, '', NULL, '0', '2014-09-02 14:20:15', 1, 1, 1),
(660, 'Ajustes por inflacion', 1, 15, 1580, 158099, '', NULL, '0', '2014-09-02 14:20:15', 1, 1, 1),
(661, 'Semovientes', 1, 15, 1584, 0, '', NULL, '0', '2014-09-02 14:20:15', 1, 1, 1),
(662, 'Ajustes por inflacion', 1, 15, 1584, 158499, '', NULL, '0', '2014-09-02 14:20:15', 1, 1, 1),
(663, 'Reserva para la Readquisición de Cuotas o Par', 3, 33, 3305, 330517, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(664, 'Cuotas o Partes de Interés Social Propias Rea', 3, 33, 3305, 330518, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(665, 'Reserva para Extensión Agropecuaria', 3, 33, 3305, 330520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(666, 'Reserva Ley Séptima de 1990', 3, 33, 3305, 330525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(667, 'Reserva para Disposición de Semovientes', 3, 33, 3305, 330530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(668, 'Reserva Ley Octaba de 1980', 3, 33, 3305, 330535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(669, 'Otras', 3, 33, 3305, 330595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(670, 'Reservas Estatutarias', 3, 33, 3310, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(671, 'Para Futuras Capitalizaciones', 3, 33, 3310, 331005, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(672, 'Para Reposición de Activos', 3, 33, 3310, 331010, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(673, 'Para Futuros Ensanches', 3, 33, 3310, 331015, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(674, 'Otras', 3, 33, 3310, 331095, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(675, 'Reservas Ocasionales', 3, 33, 3315, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(676, 'Para Beneficencia y Civismo', 3, 33, 3315, 331505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(677, 'Para Futuras Capitalizaciones', 3, 33, 3315, 331510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(678, 'Para Futuros Ensanches', 3, 33, 3315, 331515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(679, 'Para Adquisición o Reposición de Propiedades,', 3, 33, 3315, 331520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(680, 'Para Investigaciones y Desarrollo', 3, 33, 3315, 331525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(681, 'Para Fomento Económico', 3, 33, 3315, 331530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(682, 'Para Capital de Trabajo', 3, 33, 3315, 331535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(683, 'Para Estabilización de Rendimientos', 3, 33, 3315, 331540, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(684, 'A Disposición del Máximo Órgano Social', 3, 33, 3315, 331545, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(685, 'Otras', 3, 33, 3315, 331595, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(686, 'Revalorización del Patrimonio', 3, 34, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(687, 'Ajustes por Inflación', 3, 34, 3405, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(688, 'De Capital Social', 3, 34, 3405, 340505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(689, 'De Superávit de Capital', 3, 34, 3405, 340510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(690, 'De Reservas', 3, 34, 3405, 340515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(691, 'De Resultados de Ejercicios Anteriores', 3, 34, 3405, 340520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(692, 'De Activos en Período Improductivo', 3, 34, 3405, 340525, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(693, 'De Saneamiento Fiscal', 3, 34, 3405, 340530, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(694, 'De Ajustes Decreto 3019 de 1989', 3, 34, 3405, 340535, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(695, 'De Dividendos y Participaciones Decretadas en', 3, 34, 3405, 340540, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(696, 'Propiedades, planta y equipo en transito', 1, 15, 1588, 0, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(697, 'Maquinaria y equipo', 1, 15, 1588, 158805, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(698, 'Equipo de oficina', 1, 15, 1588, 158810, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(699, 'Equpo de computacion y comunicacion', 1, 15, 1588, 158815, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(700, 'Equipo medico-cientifico', 1, 15, 1588, 158820, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(701, 'Equipo de hoteles y restaurantes', 1, 15, 1588, 158825, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(702, 'Flota y equipo de transporte', 1, 15, 1588, 158830, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(703, 'flota y equipo fluvial y/o maritimo', 1, 15, 1588, 158835, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(704, 'Flota y equipo aereo', 1, 15, 1588, 158840, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(705, 'Flota y equipo ferreo', 1, 15, 1588, 158845, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(706, 'Plantas y redes', 1, 15, 1588, 158850, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(707, 'Armamento de vigilancia', 1, 15, 1588, 158855, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(708, 'Semovientes', 1, 15, 1588, 158860, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(709, 'Envases y empaques', 1, 15, 1588, 158865, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(710, 'Ajustes por inflacion', 1, 15, 1588, 158899, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(711, 'Depreciacion acumulada', 1, 15, 1592, 0, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(712, 'Construcciones y edificaciones', 1, 15, 1592, 159205, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(713, 'Maquinaria y/o equipo', 1, 15, 1592, 159210, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(714, 'Equipo de oficina', 1, 15, 1592, 159215, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(715, 'Equipo de computacion y comunicacion', 1, 15, 1592, 159220, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(716, 'Equipo medico-cientifico', 1, 15, 1592, 159225, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(717, 'Equipo de hoteles y restaurantes', 1, 15, 1592, 159230, '', NULL, '0', '2014-09-02 14:47:30', 1, 1, 1),
(718, 'Flota y equipo de transporte', 1, 15, 1592, 159235, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(719, 'Flota y equipo fluvial y/o maritimo', 1, 15, 1592, 159240, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(720, 'flota y equipo aereo', 1, 15, 1592, 159245, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(721, 'Flota y equipo ferreo', 1, 15, 1592, 159250, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(722, 'Acueducto, plantas y redes', 1, 15, 1592, 159255, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(723, 'Arammento de vigilancia', 1, 15, 1592, 159260, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(724, 'Envases y empaques', 1, 15, 1592, 159265, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(725, 'Ajustes por inflacion', 1, 15, 1592, 159299, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(726, 'Depreciacion diferida', 1, 15, 1596, 0, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(727, 'Exceso fiscal sobre la contable', 1, 15, 1596, 159605, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(728, 'Defecto fiscal sobre la contable(CR)', 1, 15, 1596, 159610, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(729, 'Ajustes por inflacion', 1, 15, 1596, 159699, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(730, 'Amortizacion acumulada', 1, 15, 1597, 0, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(731, 'Plantaciones agricolas y forestales', 1, 15, 1597, 159705, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(732, 'Visa de comunicacion', 1, 15, 1597, 159710, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(733, 'Semovientes', 1, 15, 1597, 159715, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(734, 'Ajustes por inflacion', 1, 15, 1597, 159799, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(735, 'Agotamiento Acumulado', 1, 15, 1598, 0, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(736, 'Minas y canteras', 1, 15, 1598, 159805, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(737, 'Pozos artesianos', 1, 15, 1598, 159815, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(738, 'Yacimientos', 1, 15, 1598, 159820, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(739, 'Ajustes por inflacion', 1, 15, 1598, 159899, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(740, 'Provisiones', 1, 15, 1599, 0, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(741, 'Terrenos', 1, 15, 1599, 159904, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(742, 'Materiales proyectos petroleros', 1, 15, 1599, 159908, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(743, 'Maquinaria y montaje', 1, 15, 1599, 159912, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(744, 'Construcciones y edificaciones', 1, 15, 1599, 159916, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(745, 'Maquinaria y equipo', 1, 15, 1599, 159920, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(746, 'Equipo de oficina', 1, 15, 1599, 159924, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(747, 'Equipo de computacion y comunicacion', 1, 15, 1599, 159928, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(748, 'Equipo medico-cientifico', 1, 15, 1599, 159932, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(749, 'Equipo de hoteles y restaurantes', 1, 15, 1599, 159936, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(750, 'Flota y equipo de transporte', 1, 15, 1599, 159940, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(751, 'Flota y equipo fluvial y/o maritimo', 1, 15, 1599, 159944, '', NULL, '0', '2014-09-02 14:47:31', 1, 1, 1),
(752, 'Flota y equipo aereo', 1, 15, 1599, 159948, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(753, 'Flota y equipo ferreo', 1, 15, 1599, 159952, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(754, 'Acueductos, plantas y redes', 1, 15, 1599, 159956, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(755, 'Armamento de vigilancia', 1, 15, 1599, 159960, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(756, 'Envases y empaques', 1, 15, 1599, 159962, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(757, 'Plantaciones agricolas', 1, 15, 1599, 159964, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(758, 'Vias de comunicacion', 1, 15, 1599, 159968, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(759, 'Minas y canteras', 1, 15, 1599, 159972, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(760, 'Pozos artesanios', 1, 15, 1599, 159980, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(761, 'Yacimientos', 1, 15, 1599, 159984, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(762, 'Semoviente', 1, 15, 1599, 159988, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(763, 'Propiedades, plantas y equipo de transito', 1, 15, 1599, 159992, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(764, 'Intangibles', 1, 16, 0, 0, '', NULL, '0', '2014-09-02 14:52:03', 1, 1, 1),
(765, 'Credito mercantil', 1, 16, 1605, 0, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(766, 'Formado o estimado', 1, 16, 1605, 160505, '', NULL, '0', '0000-00-00 00:00:00', 1, 1, 1),
(767, 'Adquirido o comprado', 1, 16, 1605, 160510, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(768, 'Ajustes por inflacion', 1, 16, 1605, 160599, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(769, 'Marcas', 1, 16, 1610, 0, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(770, 'Adquiridas', 1, 16, 1610, 161005, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(771, 'Formadas', 1, 16, 1610, 161010, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(772, 'Ajustes por inflacion', 1, 16, 1610, 161099, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(773, 'Patentes', 1, 16, 1615, 0, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(774, 'Adquiridas', 1, 16, 1615, 161505, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(775, 'Formadas', 1, 16, 1615, 161510, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(776, 'Ajustes por inflacion', 1, 16, 1615, 161599, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(777, 'Concesiones y franquicias', 1, 16, 1620, 0, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(778, 'Concesiones', 1, 16, 1620, 162005, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(779, 'Franquicias', 1, 16, 1620, 162010, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(780, 'Ajustes por inflacion', 1, 16, 1620, 162099, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(781, 'Derechos', 1, 16, 1625, 0, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(782, 'Derechos de autor', 1, 16, 1625, 162505, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(783, 'Puesto de bolsa', 1, 16, 1625, 162510, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(784, 'En fideicomiso', 1, 16, 1625, 162515, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(785, 'En fideicomiso de garantia', 1, 16, 1625, 162520, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(786, 'En fideicomiso de administracion', 1, 16, 1625, 162525, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(787, 'De exhibicion-peliculas', 1, 16, 1625, 162530, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(788, 'En bienes recibidos en arrendamiento financie', 1, 16, 1625, 162535, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(789, 'Otros', 1, 16, 1625, 162595, '', '', '0', '2014-09-02 15:04:14', 1, 1, 1),
(790, 'Ajustes por inflacion', 1, 16, 1625, 162599, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(791, 'Know how', 1, 16, 1630, 0, '', NULL, '0', '2014-09-02 15:04:14', 1, 1, 1),
(792, 'Superávit Método de Participación', 3, 34, 3405, 340545, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(793, 'Saneamiento Fiscal', 3, 34, 3410, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(794, 'Ajustes por Inflación Decreto 3019 de 1989', 3, 34, 3415, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(795, 'Dividendos o Participaciones Decretados en Ac', 3, 35, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(796, 'Dividendos Decretados en Acciones', 3, 35, 3505, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(797, 'Participaciones Decretadas en Cuotas o Partes', 3, 35, 3510, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(798, 'Resultados del Ejercicio', 3, 36, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(799, 'Utilidad del Ejercicio', 3, 36, 3605, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(800, 'Pérdida del Ejercicio', 3, 36, 3610, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(801, 'Resultados de Ejercicios Anteriores', 3, 37, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(802, 'Utilidades Acumuladas', 3, 37, 3705, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(803, 'Pérdidas Acumuladas', 3, 37, 3710, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(804, 'Superávit por Valorizaciones', 3, 38, 0, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(805, 'De Inversiones', 3, 38, 3805, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(806, 'Acciones', 3, 38, 3805, 380505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(807, 'Cuotas o Partes de Interés Social', 3, 38, 3805, 380510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(808, 'Derechos Fiduciarios', 3, 38, 3805, 380515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(809, 'De Propiedad, Planta y Equipo', 3, 38, 3810, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(810, 'Terrenos', 3, 38, 3810, 381004, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(811, 'Materiales Proyectos Petroleros', 3, 38, 3810, 381006, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(812, 'Construcciones y Edificaciones', 3, 38, 3810, 381008, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(813, 'Maquinaria y Equipo', 3, 38, 3810, 381012, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(814, 'Equipo de Oficina', 3, 38, 3810, 381016, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(815, 'Equipo de Computación y Comunicación', 3, 38, 3810, 381020, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(816, 'Equipo Médico-Científico', 3, 38, 3810, 381024, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(817, 'Equipo de Hoteles y Restaurantes', 3, 38, 3810, 381028, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(818, 'Flota de Equipo y Transporte', 3, 38, 3810, 381032, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(819, 'Flota y Equipo Fluvial y/o Marítimo', 3, 38, 3810, 381036, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(820, 'Flota y Equipo Aéreo', 3, 38, 3810, 381040, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(821, 'Flota y Equipo Férreo', 3, 38, 3810, 381044, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(822, 'Acueductos, Plantas y Redes', 3, 38, 3810, 381048, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(823, 'Armamento de Vigilancia', 3, 38, 3810, 381052, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(824, 'Envases y Empaques', 3, 38, 3810, 381056, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(825, 'Plantaciones Agrícolas y Forestales', 3, 38, 3810, 381060, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(826, 'Vías de Comunicación', 3, 38, 3810, 381064, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(827, 'Minas y Canteras', 3, 38, 3810, 381068, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(828, 'Pozos Artesianos', 3, 38, 3810, 381072, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(829, 'Yacimientos', 3, 38, 3810, 381076, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(830, 'Semovientes', 3, 38, 3810, 381080, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(831, 'De Otros Activos', 3, 38, 3895, 0, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(832, 'Bienes de Arte y Cultura', 3, 38, 3895, 389505, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(833, 'Bienes Entregados en Comodato', 3, 38, 3895, 389510, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(834, 'Bienes Recibidos en Pago', 3, 38, 3895, 389515, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(835, 'Inventario de Semovientes', 3, 38, 3895, 389520, '', NULL, NULL, '2014-07-17 11:40:17', 1, 1, 3),
(836, 'Ajustes por inflacion', 1, 16, 1630, 163099, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(837, 'Licencias', 1, 16, 1635, 0, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(838, 'Ajustes por inflacion', 1, 16, 1635, 163599, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(839, 'Depreciacion y/o amortizacion acumulada', 1, 16, 1698, 0, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(840, 'Credito mercantil', 1, 16, 1698, 169805, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(841, 'Marcas', 1, 16, 1698, 169810, '', '', '0', '2014-09-02 15:16:27', 1, 1, 1),
(842, 'Patentes', 1, 16, 1698, 169815, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(843, 'Concesiones y franquicias', 1, 16, 1698, 169820, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(844, 'Derechos', 1, 16, 1698, 169830, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(845, 'Know how', 1, 16, 1698, 169835, '', '', '0', '2014-09-02 15:16:27', 1, 1, 1),
(846, 'Licencias', 1, 16, 1698, 169840, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(847, 'Ajustes por inflacion', 1, 16, 1698, 169899, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(848, 'Provisiones', 1, 16, 1699, 0, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(849, 'Diferidos', 1, 17, 0, 0, '', NULL, '0', '2014-09-02 15:16:27', 1, 1, 1),
(850, 'Gastos pagados por anticipado', 1, 17, 1705, 0, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(851, 'Intereses', 1, 17, 1705, 170505, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(852, 'Honorarios', 1, 17, 1705, 170510, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(853, 'Comisiones', 1, 17, 1705, 170515, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(854, 'Seguros y fianzas', 1, 17, 1705, 170520, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(855, 'Arrendamientos', 1, 17, 1705, 170525, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(856, 'Bodegas', 1, 17, 1705, 170530, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(857, 'Mantenimiento equipos', 1, 17, 1705, 170535, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(858, 'Servicios', 1, 17, 1705, 170540, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(859, 'Suscripciones', 1, 17, 1705, 170545, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(860, 'Otros', 1, 17, 1705, 170595, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(861, 'Cargos diferidos', 1, 17, 1710, 0, '', NULL, '0', '2014-09-02 15:31:53', 1, 1, 1),
(862, 'Organizacion y preoperativos', 1, 17, 1710, 171004, '', NULL, '0', '2014-09-02 15:50:48', 1, 1, 1),
(863, 'Remodelaciones', 1, 17, 1710, 171008, '', NULL, '0', '2014-09-02 15:50:48', 1, 1, 1),
(864, 'Estudios, investigacion y proyectos', 1, 17, 1710, 171012, '', NULL, '0', '2014-09-02 15:50:48', 1, 1, 1),
(865, 'Programas para computadores(software)', 1, 17, 1710, 171016, '', NULL, '0', '2014-09-02 15:50:48', 1, 1, 1),
(866, 'Utiles y papeleria', 1, 17, 1710, 171020, '', NULL, '0', '2014-09-02 15:50:48', 1, 1, 1),
(867, 'Mejoras a propiedades ajenas', 1, 17, 1710, 171024, '', NULL, '0', '2014-09-02 15:50:48', 1, 1, 1),
(868, 'Contribuciones y afiliaciones', 1, 17, 1710, 171028, '', NULL, '0', '2014-09-02 15:50:48', 1, 1, 1),
(869, 'Entrenamiento de personal', 1, 17, 1710, 171032, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(870, 'Ferias y explosiciones', 1, 17, 1710, 171036, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(871, 'Licencias', 1, 17, 1710, 171040, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(872, 'Publicidad, propaganda y promocion', 1, 17, 1710, 171044, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(873, 'Elementos de aseo y cafeteria', 1, 17, 1710, 171048, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(874, 'Moldes y troqueles', 1, 17, 1710, 171052, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(875, 'Instrumental quirurjico', 1, 17, 1710, 171056, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(876, 'Dotacion y suministro a trabajadores', 1, 17, 1710, 171060, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(877, 'Elementos de roperia', 1, 17, 1710, 171064, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(878, 'loza y cristaleria', 1, 17, 1710, 171068, '', NULL, '0', '2014-09-02 16:09:13', 1, 1, 1),
(879, 'Plateria', 1, 17, 1710, 171069, '', NULL, '0', '2014-09-02 16:09:14', 1, 1, 1),
(880, 'Cubierteria', 1, 17, 1710, 171070, '', NULL, '0', '2014-09-02 16:09:14', 1, 1, 1),
(881, 'Impuesto de renta deferido? debitos? por dife', 1, 17, 1710, 171076, '', NULL, '0', '2014-09-02 16:09:14', 1, 1, 1),
(882, 'Concursos y licitaciones', 1, 17, 1710, 171080, '', NULL, '0', '2014-09-02 16:09:14', 1, 1, 1),
(883, 'Otros', 1, 17, 1710, 171095, '', NULL, '0', '2014-09-02 16:09:14', 1, 1, 1),
(884, 'Ajustes por inflacion', 1, 17, 1710, 171099, '', NULL, '0', '2014-09-02 16:09:14', 1, 1, 1),
(896, 'Costos de explotacion por amortizar', 1, 17, 1715, 0, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(897, 'Pozos secos', 1, 17, 1715, 171505, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(898, 'Pozos no comerciales', 1, 17, 1715, 171510, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(899, 'Otros costos de explotacion', 1, 17, 1715, 171515, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(900, 'Ajustes por inflacion', 1, 17, 1715, 171599, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(901, 'Costos de exploracion y desarrollo', 1, 17, 1720, 0, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(902, 'Perforacion y explotacion', 1, 17, 1720, 172005, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(903, 'Perforacion campos de desarrollo', 1, 17, 1720, 172010, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(904, 'Facilidades de produccion', 1, 17, 1720, 172015, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(905, 'Servicio a pozos', 1, 17, 1720, 172020, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(906, 'Ajustes por inflacion', 1, 17, 1720, 172099, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(907, 'Cargos por correcion monetaria diferida', 1, 17, 1730, 0, '', NULL, '0', '2014-09-02 16:28:16', 1, 1, 1),
(908, 'Amortizacion acumulada', 1, 17, 1798, 0, '', NULL, '0', '2014-09-02 16:31:51', 1, 1, 1),
(909, 'Costos de exploracion por amortizar', 1, 17, 1798, 179805, '', NULL, '0', '2014-09-02 16:31:51', 1, 1, 1),
(910, 'Costos de explotacion y desarrollo', 1, 17, 1798, 179810, '', NULL, '0', '2014-09-02 16:31:51', 1, 1, 1),
(911, 'Ajustes por inflacion', 1, 17, 1798, 179899, '', NULL, '0', '2014-09-02 16:31:51', 1, 1, 1),
(912, 'Otros Activos', 1, 18, 0, 0, '', NULL, '0', '2014-09-02 16:31:51', 1, 1, 1),
(913, 'Biienes de arte y cultura', 1, 18, 1805, 0, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(914, 'Obras de Arte', 1, 18, 1805, 180505, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(915, 'Bibliotecas', 1, 18, 1805, 180510, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(916, 'Otros', 1, 18, 1805, 180595, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(917, 'Ajustes por inflacion', 1, 18, 1805, 180599, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(918, 'Diversos', 1, 18, 1895, 0, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(919, 'Maquinas porteadoras', 1, 18, 1895, 189505, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(920, 'Bienes entregados en comodato', 1, 18, 1895, 189510, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(921, 'Amortizacion acumulada de bienes entregados e', 1, 18, 1895, 189515, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(922, 'Bienes recibidos en pago', 1, 18, 1895, 189520, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(923, 'Derechos sucesorales', 1, 18, 1895, 189525, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(924, 'Estampillas', 1, 18, 1895, 189530, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(925, 'Otros', 1, 18, 1895, 189595, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(926, 'Ajustes por inflacion', 1, 18, 1895, 189599, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(927, 'Provisiones', 1, 18, 1899, 0, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(928, 'Bienes de arte y cultura', 1, 18, 1899, 189905, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(929, 'Diversos', 1, 18, 1899, 189995, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(930, 'Valorizaciones', 1, 19, 0, 0, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(931, 'De inversiones', 1, 19, 1905, 0, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(932, 'Acciones', 1, 19, 1905, 190505, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(933, 'Cuotas o partes de interes social', 1, 19, 1905, 190510, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(934, 'Derechos fiduciarios', 1, 19, 1905, 190515, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(935, 'De propiedades, planta y equipo', 1, 19, 1910, 0, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(936, 'Terrenos', 1, 19, 1910, 191004, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(937, 'Materiales proyectos petroleros', 1, 19, 1910, 191006, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(938, 'Construcciones y edificaciones', 1, 19, 1910, 191008, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(939, 'Maquinarias y equipo', 1, 19, 1910, 191012, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(940, 'Equipo de oficina', 1, 19, 1910, 191016, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(941, 'Equipo de computacion y comunicacion', 1, 19, 1910, 191020, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(942, 'Equipo medico-cientifico', 1, 19, 1910, 191024, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(943, 'Equipo de hoteles y restaurantes', 1, 19, 1910, 191028, '', '', '0', '2014-09-02 17:13:35', 1, 1, 1),
(944, 'Flota y equipo de transporte', 1, 19, 1910, 191032, '', NULL, '0', '2014-09-02 17:13:35', 1, 1, 1),
(945, 'Flota y equipo fluvial y/o maritimo', 1, 19, 1910, 191036, '', NULL, '0', NULL, 1, 1, 1),
(946, 'Flota y equipo aereo', 1, 19, 1910, 191040, '', NULL, '0', NULL, 1, 1, 1),
(947, 'Flota equipo ferreo', 1, 19, 1910, 191044, '', NULL, '0', NULL, 1, 1, 1),
(948, 'Acueductos, plantas y redes', 1, 19, 1910, 191048, '', NULL, '0', NULL, 1, 1, 1),
(949, 'Armamento de vigilancia', 1, 19, 1910, 191052, '', NULL, '0', NULL, 1, 1, 1),
(950, 'Envases y empaques', 1, 19, 1910, 191056, '', NULL, '0', NULL, 1, 1, 1),
(951, 'Plantaciones agricolas y forestales', 1, 19, 1910, 191060, '', NULL, '0', NULL, 1, 1, 1),
(952, 'Vias de comunicacion', 1, 19, 1910, 191064, '', NULL, '0', NULL, 1, 1, 1),
(953, 'Minas y canteras', 1, 19, 1910, 191068, '', NULL, '0', NULL, 1, 1, 1),
(954, 'Pozos artesanios', 1, 19, 1910, 191072, '', NULL, '0', NULL, 1, 1, 1),
(955, 'Yacimientos', 1, 19, 1910, 191076, '', NULL, '0', NULL, 1, 1, 1),
(956, 'Semovientes', 1, 19, 1910, 191080, '', NULL, '0', NULL, 1, 1, 1),
(957, 'De otros activos', 1, 19, 1915, 0, '', NULL, '0', NULL, 1, 1, 1),
(958, 'Bienes de arte y cultura', 1, 19, 1915, 191505, '', NULL, '0', NULL, 1, 1, 1),
(959, 'Bienes entregados en comodato', 1, 19, 1915, 191510, '', NULL, '0', NULL, 1, 1, 1),
(960, 'Bienes recibidos en pago', 1, 19, 1915, 191515, '', NULL, '0', NULL, 1, 1, 1),
(961, 'Inventario Semovientes', 1, 19, 1915, 191520, '', NULL, '0', NULL, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `con_detnota`
--

CREATE TABLE IF NOT EXISTS `con_detnota` (
  `cod_detnota` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK DE TABLA AUTOINCREMENTO',
  `cod_nota` int(8) NOT NULL COMMENT 'CODIGO DE LA NOTA CABECERA',
  `cod_cuenta` int(8) DEFAULT NULL COMMENT 'CODIGO DE LA CUENTA',
  `imp_detnota` varchar(45) DEFAULT NULL COMMENT 'IMPORTE DE LA NOTA',
  PRIMARY KEY (`cod_detnota`),
  KEY `fk_detnot_nota_idx` (`cod_nota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `con_mov_contable`
--

CREATE TABLE IF NOT EXISTS `con_mov_contable` (
  `cod_mov_contable` int(8) NOT NULL AUTO_INCREMENT COMMENT 'COLUMNA AUTOINCREMENTO PK DE LA TABLA',
  `cod_cuenta` int(8) NOT NULL COMMENT 'CODIGO DE LA CUENTA',
  `debe_mov_contable` varchar(45) NOT NULL DEFAULT '0',
  `haber_mov_contable` varchar(45) DEFAULT '0',
  `fec_mov_contable` date DEFAULT NULL COMMENT 'FECHA DE TRANSACCION',
  `hora_mov_contable` varchar(45) DEFAULT NULL COMMENT 'HORA DE TRANSACCION',
  `cod_usuario` int(8) DEFAULT NULL COMMENT 'USUARIO QUE REGISTRA',
  PRIMARY KEY (`cod_mov_contable`),
  KEY `fk_movcon_cuen_idx` (`cod_cuenta`),
  KEY `fk_movcon_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LAS TRANSACCIONES ' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `con_nota`
--

CREATE TABLE IF NOT EXISTS `con_nota` (
  `cod_nota` int(8) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO PK DE TABLA',
  `cod_cliente` int(8) NOT NULL COMMENT 'CODIGO DEL CLIENTE AL CUAL SE APLICA LA NOTA',
  `obs_nota` varchar(250) DEFAULT NULL COMMENT 'OBSERVACIONES DE LA NOTA',
  `fecha_nota` date DEFAULT NULL,
  `hora_nota` varchar(45) DEFAULT NULL,
  `imp_nota` varchar(45) DEFAULT NULL COMMENT 'IMPORTE DE LA NOTA',
  `cod_usuario` int(8) NOT NULL COMMENT 'USUARIO QUE REGISTRA',
  `ind_factura` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA SI LA NONTA SE APLICA A UNA ',
  `cod_factura` int(8) DEFAULT NULL COMMENT 'FACTURA A LA QUE AFECTA LA NOTA',
  `ind_cuenta` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA SI SE APLICA A CUENTAS CONTABLES',
  `cod_tiponota` int(4) NOT NULL,
  PRIMARY KEY (`cod_nota`),
  KEY `fk_notcre_cli_idx` (`cod_cliente`),
  KEY `fk_notcre_usu_idx` (`cod_usuario`),
  KEY `fk_not_tipnot_idx` (`cod_tiponota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LAS NOTAS CREDITO CONTABLES' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `con_tipocuenta`
--

CREATE TABLE IF NOT EXISTS `con_tipocuenta` (
  `cod_tipocuenta` int(4) NOT NULL AUTO_INCREMENT COMMENT 'PK AUTOINCREMENTO DE LA TABLA',
  `nom_tipocuenta` varchar(45) NOT NULL COMMENT 'NOMBRE DE LA CUENTA  EJEMPLO 1:ACTIVO, 2:PASIVO, 3:PATRIMONIO, 4:INGRESOS, 5:GASTOS, 6:COSTODEVENTA, 7:COSTOS DE OPERACION,8:CUENTASORDENDEUDORAS, 9:CUENTASORDENACREEDORAS  ',
  `des_tipocuenta` varchar(120) DEFAULT NULL COMMENT 'DESCRIPCION DE LA CUENTA',
  PRIMARY KEY (`cod_tipocuenta`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMECENAR EL TIPO DE CUENTA EJEMPLO 1:ACTIVO, 2:PASIVO, 3:PATRIMONIO, 4:INGRESOS, 5:EGRESOS, 6:COSTODEVENTA, 7:COSTOS DE OPERACION,8:CUENTASORDENDEUDORAS, 9:CUENTASORDENACREEDORAS  ' AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `con_tipocuenta`
--

INSERT INTO `con_tipocuenta` (`cod_tipocuenta`, `nom_tipocuenta`, `des_tipocuenta`) VALUES
(1, 'Activo', 'Activos de la empresa'),
(2, 'Pasivo', 'Pasivos de la empresa'),
(3, 'Patrimonio', 'Patrimonio de la empresa'),
(4, 'Ingresos', 'Ingresos'),
(5, 'Gastos', 'Gastos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `con_tiponota`
--

CREATE TABLE IF NOT EXISTS `con_tiponota` (
  `cod_tiponota` int(4) NOT NULL AUTO_INCREMENT COMMENT 'PK DE TABLA',
  `nom_tiponota` varchar(45) NOT NULL COMMENT 'NOMBRE DEL TIPO DE NOTA',
  `des_tiponota` varchar(150) DEFAULT NULL COMMENT 'DESCRIPCION DEL TIPO DE NOTA',
  PRIMARY KEY (`cod_tiponota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS TIPOS DE NOTAS CREDITO:0, DEBITO:1 ' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `con_tipo_cuenta`
--

CREATE TABLE IF NOT EXISTS `con_tipo_cuenta` (
  `con_tipo_cuenta` int(8) NOT NULL AUTO_INCREMENT,
  `nom_tipo_cuenta` varchar(45) DEFAULT NULL,
  `des_tipo_cuenta` varchar(120) DEFAULT NULL,
  `cod_empresa` int(8) DEFAULT NULL,
  `cod_usuario` int(8) DEFAULT NULL,
  `cod_estado` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`con_tipo_cuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS TIPOS DE CUENTAS CONTABLES BANCOS,TARJETA CREDITO, EFECTIVO' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dat_naturaleza_juridica`
--

CREATE TABLE IF NOT EXISTS `dat_naturaleza_juridica` (
  `cod_naturaleza_juridica` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Codigo de la naturaleza.',
  `nom_naturaleza_juridica` varchar(45) DEFAULT NULL COMMENT 'Nombre de la naturaleza jurídica.\n\nEj: \nNacional, Corporación, Fundacion',
  `des_naturaleza_juridica` varchar(250) DEFAULT NULL COMMENT 'Descripción de la naturaleza.',
  PRIMARY KEY (`cod_naturaleza_juridica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene la naturaleza jurídica de la universidad.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dat_seccionales`
--

CREATE TABLE IF NOT EXISTS `dat_seccionales` (
  `cod_seccionales` int(11) NOT NULL COMMENT 'Código único de la seccional.',
  `cod_universidad` int(11) DEFAULT NULL COMMENT 'Código de la universidad a la que pertenece la universidad.',
  `nom_seccionales` varchar(150) DEFAULT NULL,
  `tipo_seccionales` varchar(45) DEFAULT NULL COMMENT 'Tipo de la seccional.\n\nPrincipal\nSeccional',
  `dir_seccionales` varchar(200) NOT NULL COMMENT 'Dirección de la seccional.',
  `email_seccionales` varchar(45) DEFAULT NULL COMMENT 'Email de la seccional.',
  `web_seccionales` varchar(100) DEFAULT NULL COMMENT 'Página web de la seccional.',
  `tel1_seccionales` varchar(45) DEFAULT NULL COMMENT 'Teléfono 1 de la seccional.',
  `tel2_seccionales` varchar(45) DEFAULT NULL,
  `fax_seccionales` varchar(45) DEFAULT NULL,
  `cx_seccionales` varchar(45) DEFAULT NULL,
  `cy_seccionales` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cod_seccionales`),
  KEY `fk_datuni_datseccio_idx` (`cod_universidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla que contiene la información de las seccionales asociadas a una universidad.';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dat_universidad`
--

CREATE TABLE IF NOT EXISTS `dat_universidad` (
  `cod_universidad` int(11) NOT NULL AUTO_INCREMENT,
  `nom_universidad` varchar(150) NOT NULL COMMENT 'Nombre de la universidad.',
  `cod_ciudad` int(8) DEFAULT NULL COMMENT 'Código de la ciudad de la universidad.',
  `cod_naturaleza_juridica` int(11) NOT NULL,
  `dir_universidad` varchar(150) DEFAULT NULL COMMENT 'Direccion de la universidad.',
  `tel1_universidad` varchar(45) DEFAULT NULL COMMENT 'Teléfono 1 de contacto.',
  `tel2_universidad` varchar(45) DEFAULT NULL COMMENT 'Teléfono 2 de contacto.',
  `email_universidad` varchar(150) DEFAULT NULL COMMENT 'Email oficial de la universidad.',
  `fax_universidad` varchar(45) DEFAULT NULL COMMENT 'Fax de la universidad.',
  `tipo_universidad` varchar(45) DEFAULT NULL COMMENT 'Tipo (Privada ó pública).',
  `tipo_ins_universidad` varchar(100) DEFAULT NULL COMMENT 'Tipo de institución universidad.\n\nEjemplo: Fundación universitaria, etc...',
  `cod_estado` varchar(3) DEFAULT '1' COMMENT 'Estado de la universidad.\n\n1 -> Activo\n0 -> Inactivo',
  `nit_universidad` varchar(30) NOT NULL COMMENT 'Nit de la universidad.',
  `cod_universidad_oficial` varchar(20) DEFAULT NULL,
  `acre_universidad` varchar(2) DEFAULT NULL COMMENT 'Dice si la universidad está o no acreditada.\n\nSI \nNO',
  `cx_universidad` varchar(45) DEFAULT NULL COMMENT 'Coordenada X universidad.',
  `cy_universidad` varchar(45) DEFAULT NULL COMMENT 'Coordenada Y universidad.',
  PRIMARY KEY (`cod_universidad`),
  UNIQUE KEY `nom_universidad_UNIQUE` (`nom_universidad`),
  UNIQUE KEY `nit_universidad_UNIQUE` (`nit_universidad`),
  KEY `fk_datnatur_datuni_idx` (`cod_naturaleza_juridica`),
  KEY `fk_datuniv_sysest_idx` (`cod_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla para el almacenamiento de los datos de las universidades.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_cliente`
--

CREATE TABLE IF NOT EXISTS `fa_cliente` (
  `cod_cliente` int(8) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DE TABLA AUTOINCREMENTO',
  `nom_cliente` varchar(150) NOT NULL,
  `nit_cliente` varchar(20) NOT NULL COMMENT 'NUMERO NIT DEL CLIENTE',
  `dir_cliente` varchar(45) NOT NULL COMMENT 'DIRECCION DE LOCALIZACION',
  `email_cliente` varchar(45) DEFAULT NULL COMMENT 'EMAIL DEL CLIENTE',
  `tel_cliente` varchar(12) NOT NULL COMMENT 'TELEFONO DE CONTACTO 1',
  `tel1_cliente` varchar(12) DEFAULT NULL COMMENT 'TELEFONO DE CONTACTO 2',
  `fax_cliente` varchar(20) DEFAULT NULL COMMENT 'NUMERO DE FAX',
  `cel_cliente` varchar(12) DEFAULT NULL COMMENT 'NUMERO MOVIL',
  `ind_cliente_cliente` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA SI EL CLIENTE ES CLIENTE FACTURABLE',
  `ind_proveedor_cliente` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA SI EL CLIENTE ES PROVEEDOR',
  `obs_cliente` varchar(200) DEFAULT NULL COMMENT 'PARA REGISTRAR TODAS LAS DEMAS OBSERVACIONES DEL CLIENTE',
  `cod_ciudad` int(6) NOT NULL COMMENT 'CODIGO DE LA CUIDAD A LA CUAL PERTENECE',
  `cod_tipopago` int(4) NOT NULL COMMENT 'CODIGO DEL TIPO DE PAGO DEL CLIENTE',
  `cod_empresa` int(8) NOT NULL,
  PRIMARY KEY (`cod_cliente`),
  KEY `fk_cli_ciu_idx` (`cod_ciudad`),
  KEY `fk_cli_tippago_idx` (`cod_tipopago`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA REGISTRAR CLIENTES Y PROVEEDORES DE LA EMPRESA\n' AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `fa_cliente`
--

INSERT INTO `fa_cliente` (`cod_cliente`, `nom_cliente`, `nit_cliente`, `dir_cliente`, `email_cliente`, `tel_cliente`, `tel1_cliente`, `fax_cliente`, `cel_cliente`, `ind_cliente_cliente`, `ind_proveedor_cliente`, `obs_cliente`, `cod_ciudad`, `cod_tipopago`, `cod_empresa`) VALUES
(1, 'j', 'j', 'j', 'j', 'j', 'j', 'j', 'j', 1, 1, 'j', 17001, 6, 15),
(2, 'TATIANA BETANCUR', '7', '7', 'JH', 'J', 'J', '', 'J', 1, 1, 'ASDFAD', 17001, 2, 16),
(3, 'RICARDO GONZALEZ', '1053768699', 'CRA 10C # 48A 28', 'ING.RICARDO.GONZALEZ@HOTMAIL.COM', '8767765', '8854518', '', '3207262467', 1, 0, 'CLIENTE UNE', 5001, 3, 0),
(4, 'FERNEY TAPASCO', '1001254215-1', 'CENTRO', 'TAPAXCO@GMAIL.COM', '87855487', '3201224512', '887548877', '3201542154', 1, 1, 'CLIENTE Y PROVEEDOR', 17001, 5, 16),
(5, 'DANIEL ARBOLEDA', '1001254215-1', 'CENTRO', 'TAPAXCO@GMAIL.COM', '87855487', '3201224512', '887548877', '3201542154', 1, 1, 'CLIENTE Y PROVEEDOR', 17001, 5, 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_cliente_asociado`
--

CREATE TABLE IF NOT EXISTS `fa_cliente_asociado` (
  `cod_cliente_asociado` int(8) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMENTO DE LA TABLA',
  `nom_cliente_asociado` varchar(120) NOT NULL COMMENT 'NOMBRE DE LA PERSONA ASOCIADA A UN PROVEEDOR',
  `email_cliente_asociado` varchar(45) DEFAULT NULL COMMENT 'EMAIL DE LA PERSONA ASOCIADA AL PROVEEDOR',
  `tel_cliente_asociado` varchar(12) DEFAULT NULL COMMENT 'TELEFONO DE CONTACTO',
  `cel_cliente_asociado` varchar(12) DEFAULT NULL COMMENT 'MOVIL DE CONTACTO',
  `fec_cliente_asociado` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `hora_cliente_esatdo` varchar(45) DEFAULT NULL COMMENT 'HORA DEL REGISTRO',
  `cod_estado` varchar(3) NOT NULL COMMENT 'CODIGO DEL ESTADO',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DEL USUARIO QUE REGISTRA',
  `cod_cliente` int(8) NOT NULL COMMENT 'CODIGO DEL CLIENTE ASOCIADO',
  PRIMARY KEY (`cod_cliente_asociado`),
  KEY `fk_cliaso_est_idx` (`cod_estado`),
  KEY `fk_cliaso_usu_idx` (`cod_usuario`),
  KEY `fk_cliaso_cli_idx` (`cod_cliente`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS USUARIOS ASOCIADOS A UN PROVEEDOR, SOLO SE PUEDEN AGREGAR SI ELL CLIENTE ES UN PROVEEDOR' AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `fa_cliente_asociado`
--

INSERT INTO `fa_cliente_asociado` (`cod_cliente_asociado`, `nom_cliente_asociado`, `email_cliente_asociado`, `tel_cliente_asociado`, `cel_cliente_asociado`, `fec_cliente_asociado`, `hora_cliente_esatdo`, `cod_estado`, `cod_usuario`, `cod_cliente`) VALUES
(2, 'DAVID GONZALEZ', 'DAVID@GMAIL.COM', '8768548', '3113440354', NULL, NULL, 'AAA', 1, 5),
(3, 'EVA CASTILLO', 'EVA@GMAIL.COM', '8854871', '3201125487', NULL, NULL, 'AAA', 1, 5),
(4, 'el', 'tu', '454', '56', NULL, NULL, 'AAA', 1, 2),
(5, '{nom_cliente_asociado}', '{email_cliente_asociado}', '{tel_cliente', '{cel_cliente', NULL, NULL, 'AAA', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_config`
--

CREATE TABLE IF NOT EXISTS `fa_config` (
  `cod_config` int(4) NOT NULL AUTO_INCREMENT COMMENT 'AUTOINCREMENTO DE LA TABLA PK',
  `nom_config` varchar(45) NOT NULL COMMENT 'NOMBRE DE LA CONFIGURACION',
  `tyc_config` varchar(45) DEFAULT NULL COMMENT 'TERMINOS Y CONDICIONES',
  `not_config` varchar(120) DEFAULT NULL COMMENT 'NOTAS DE LA CONFIGURACION',
  `ind_retenciones_config` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA SI SE MUESTRAN LAS RETENCIONES EN LAS FACTURAS A IMPRIMIR',
  `cod_estado` varchar(3) NOT NULL COMMENT 'INDICA EN QUE ESTADO ESTA LA CONFIGURACION',
  `cod_usuario` int(8) NOT NULL COMMENT 'USUARIO QUE REGISTRA LA CONFIGURACION',
  `fec_config` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `fec_modifica_config` date DEFAULT NULL COMMENT 'FECHA DE ULTIMA MODIFICACION DE LA CONFIGURACION',
  `hora_config` time DEFAULT NULL COMMENT 'HORA DEL REGISTRO',
  `num_sig_recibocaja` int(8) NOT NULL COMMENT 'SIGUIENTE NUMERO DE RECIBOS DE CAJA',
  `num_sig_compago` int(8) NOT NULL COMMENT 'SIGUIENTE NUMERO DE COMPROBANTE DE PAGO',
  PRIMARY KEY (`cod_config`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA CONFIGURAR LAS DATOS GENERALES DE LAS FACTURAS, ' AUTO_INCREMENT=20 ;

--
-- Volcado de datos para la tabla `fa_config`
--

INSERT INTO `fa_config` (`cod_config`, `nom_config`, `tyc_config`, `not_config`, `ind_retenciones_config`, `cod_estado`, `cod_usuario`, `fec_config`, `fec_modifica_config`, `hora_config`, `num_sig_recibocaja`, `num_sig_compago`) VALUES
(6, 'uno', 'dos', 'tres', 1, 'CBB', 1, NULL, NULL, NULL, 1, 2),
(7, 'dos', 'tres dos', 'cuatro', 1, 'CBB', 1, '2014-05-14', NULL, '14:09:41', 3, 4),
(14, 'TRES', 'CUATRO iii', 'CINCO', 1, 'CBB', 1, '2014-05-14', NULL, '14:52:27', 2, 2),
(15, 'otra', 'otra', 'otra', 1, 'CBB', 1, '2014-05-19', NULL, '11:51:57', 2, 3),
(16, 'fd', 'dfd', 'dfd', 1, 'CBB', 1, '2014-05-19', NULL, '11:56:19', 2, 3),
(17, 'ggg', 'adfs', 'adfs', 0, 'CAA', 1, '2014-05-19', NULL, '13:51:25', 2, 6),
(18, 'ggggggggg', 'gggggggggg', 'gggggggggg', 1, 'CBB', 1, '2014-05-19', NULL, '16:13:16', 4, 4),
(19, 'NUMERACION DIAN 01', 'MIS TERMINOS', 'MIS NOTAS', 1, 'AAA', 1, '2014-05-29', NULL, '16:29:39', 12300, 12310);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_descuentos`
--

CREATE TABLE IF NOT EXISTS `fa_descuentos` (
  `cod_descuentos` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMENTO DE LA TABLA PK',
  `nom_descuento` varchar(45) NOT NULL COMMENT 'NOMBRE DEL DESCUENTO',
  `des_descuento` varchar(120) DEFAULT NULL COMMENT 'DESCRIPCION DEL DESCUENTO',
  `prc_descuento` int(3) NOT NULL DEFAULT '0',
  `fec_inicio_descuento` date NOT NULL COMMENT 'FECHA DE INICIO DEL DESCUENTO',
  `fec_cierre_descuento` date NOT NULL COMMENT 'FECHA DE CIERRE DEL DESCUENTO',
  `cod_estado` varchar(3) NOT NULL COMMENT 'CODIGO DE ESTADO DEL DESCUENTO',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DEL USUARIO QUE REGISTRA',
  PRIMARY KEY (`cod_descuentos`),
  KEY `fk_desc_est_idx` (`cod_estado`),
  KEY `fk_desc_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS DESCUENTOS PARA LOS ITEMS FACTURABLES' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `fa_descuentos`
--

INSERT INTO `fa_descuentos` (`cod_descuentos`, `nom_descuento`, `des_descuento`, `prc_descuento`, `fec_inicio_descuento`, `fec_cierre_descuento`, `cod_estado`, `cod_usuario`) VALUES
(1, 'Des Junio', 'Descuento del mes de junio', 20, '2014-06-10', '2014-10-31', 'DAA', 1),
(2, 'otro', 'otro', 21, '2014-07-23', '2014-07-24', 'DAA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_detalle`
--

CREATE TABLE IF NOT EXISTS `fa_detalle` (
  `cod_detalle` int(8) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMENTON DE LA TABLA ',
  `cod_item` int(8) NOT NULL COMMENT 'CODIGO DEL ITEM',
  `can_detalle` int(6) NOT NULL DEFAULT '1' COMMENT 'CANTIDAD DE INTEMS FACTURADOS',
  `cod_impuesto` int(4) NOT NULL COMMENT 'CODIGO DEL IMPUESTO',
  `cod_descuento` int(4) NOT NULL DEFAULT '0' COMMENT 'CODIGO DEL DESCUENTO APLICADO',
  `cod_factura` int(8) NOT NULL COMMENT 'CODIGO DE LA FACTURA',
  PRIMARY KEY (`cod_detalle`),
  KEY `fk_det_impu_idx` (`cod_impuesto`),
  KEY `fk_det_des_idx` (`cod_descuento`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR EL DETALLE DE LA FACTURA, LOS INTEMS FACTURADOS CON SUS IMPUESTOS' AUTO_INCREMENT=30 ;

--
-- Volcado de datos para la tabla `fa_detalle`
--

INSERT INTO `fa_detalle` (`cod_detalle`, `cod_item`, `can_detalle`, `cod_impuesto`, `cod_descuento`, `cod_factura`) VALUES
(26, 7, 1, 2, 0, 1),
(27, 6, 1, 0, 0, 1),
(28, 5, 4, 1, 0, 1),
(29, 6, 1, 0, 0, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_factura`
--

CREATE TABLE IF NOT EXISTS `fa_factura` (
  `cod_factura` int(8) NOT NULL AUTO_INCREMENT COMMENT 'NUMERO DE FACTURA, AUTOINCREMENTO',
  `cod_numeracion` int(4) NOT NULL COMMENT 'CODIGO DE LA FACTURA FK DESDE FA_NUMERACION',
  `num_factura` varchar(45) NOT NULL COMMENT 'NUMERO DE LA FACTURA',
  `cod_cliente` int(8) NOT NULL COMMENT 'CODIGO DEL CLIENTE, FK DESDE FA_TIPOPAGO',
  `fec_alta_factura` date NOT NULL COMMENT 'FECHA DE ALTA DE LA FACTURA, FECHA DE TRANSACCION',
  `fec_vencimiento_factura` date NOT NULL COMMENT 'FECHA DE VENCIMIENTO, DEGUN EL NUMERO DE DIAS',
  `cod_tipopago` int(4) NOT NULL COMMENT 'PLAZO PARA PAGAR LA FACTURA',
  `sub_total_factura` varchar(20) NOT NULL COMMENT 'IMPORTE TOTAL DE LA FACTURA',
  `sub_totaldes_factura` varchar(20) DEFAULT NULL COMMENT 'SUBTOTAL CON DESCUENTO APLICADO',
  `imp_factura` varchar(45) DEFAULT NULL COMMENT 'IMPUESTO DE LA FACTURA',
  `imp_adeudado` varchar(20) NOT NULL DEFAULT '0' COMMENT 'IMPORTE ADEUDADO DE LA FACTURA',
  `imp_cancelado` varchar(20) NOT NULL DEFAULT '0' COMMENT 'IMPORTE CANCELADO',
  `obs_factura` varchar(200) DEFAULT NULL COMMENT 'OBSERVACIONES DE LA FACTURA, NO VISIBLE EN EL DOCUMENTO',
  `not_factura` varchar(200) DEFAULT NULL COMMENT 'notas adicionasles de la factura',
  `ind_recurrente_factura` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA SI LA FACTURA ES RECURRENTE, DE SER ASI EL CAMPO DE FECHA DE VENCIMIENTO DEBE ESTAR LA FECHA FINAL DEL COBRO RECURRENTE',
  `ind_cotizacion` int(1) DEFAULT '0',
  `cod_estado` varchar(3) NOT NULL,
  `cod_config` int(4) NOT NULL COMMENT 'CODIGO DE LA CONFIGURACION DE LA FACTURA',
  `fec_factura` date NOT NULL COMMENT 'FECHA DE REGISTRO',
  `hora_factura` time NOT NULL,
  `cod_usuario` int(8) NOT NULL,
  `cod_empresa` int(8) NOT NULL,
  PRIMARY KEY (`cod_factura`),
  KEY `fk_fac_num_idx` (`cod_numeracion`),
  KEY `fk_fac_cli_idx` (`cod_cliente`),
  KEY `fk_fac_est_idx` (`cod_estado`),
  KEY `fk_fac_conf_idx` (`cod_config`),
  KEY `fk_fac_usu_idx` (`cod_usuario`),
  KEY `fk_factura_emp_idx` (`cod_empresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA DONDE SE ALMACENAN LOS DOCUMENTOS DE FACTURACION.' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `fa_factura`
--

INSERT INTO `fa_factura` (`cod_factura`, `cod_numeracion`, `num_factura`, `cod_cliente`, `fec_alta_factura`, `fec_vencimiento_factura`, `cod_tipopago`, `sub_total_factura`, `sub_totaldes_factura`, `imp_factura`, `imp_adeudado`, `imp_cancelado`, `obs_factura`, `not_factura`, `ind_recurrente_factura`, `ind_cotizacion`, `cod_estado`, `cod_config`, `fec_factura`, `hora_factura`, `cod_usuario`, `cod_empresa`) VALUES
(1, 1, '', 3, '2014-09-17', '2014-10-02', 3, '1450400', '0', '41712', '1492112', '0', 'obse', 'not', 0, 1, 'FAA', 19, '2014-10-28', '18:22:28', 1, 15),
(2, 1, 'RUBIK_GROUP_3', 2, '2014-10-28', '2014-11-05', 2, '1320000', '0', '0', '1320000', '0', 'd', 'd', 0, 0, 'FAA', 19, '2014-10-28', '22:04:11', 1, 16);

--
-- Disparadores `fa_factura`
--
DROP TRIGGER IF EXISTS `fa_factura_AINS`;
DELIMITER //
CREATE TRIGGER `fa_factura_AINS` AFTER INSERT ON `fa_factura`
 FOR EACH ROW BEGIN
  IF NEW.ind_recurrente_factura=0 THEN
   UPDATE fa_numeracion 
	  SET num_sig_numeracion  = (num_sig_numeracion + 1)
    WHERE cod_estado = 'AAA';
  END IF;
 END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `fa_factura_BENS`;
DELIMITER //
CREATE TRIGGER `fa_factura_BENS` BEFORE INSERT ON `fa_factura`
 FOR EACH ROW BEGIN
  DECLARE iCodConfig int(8);
  set iCodConfig = (SELECT cod_config
                 FROM fa_config
                WHERE cod_estado ='AAA');
  set new.cod_config = iCodConfig;
 END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_impuesto`
--

CREATE TABLE IF NOT EXISTS `fa_impuesto` (
  `cod_impuesto` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMENTO DE LA TABLA PK',
  `nom_impuesto` varchar(45) NOT NULL COMMENT 'NOMBRE DEL IMPUESTO',
  `por_impuesto` varchar(3) NOT NULL DEFAULT '0' COMMENT 'PORCENTAJE DEL IMPUESTO',
  `des_impuesto` varchar(120) DEFAULT NULL,
  `cod_tipoimpuesto` int(4) NOT NULL COMMENT 'CODIGO DEL TIPO DE IMPUESTO RELACIONADO AL IMPUESTO',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DEL USUARIO QUE REGISTRA',
  `fec_impuesto` date DEFAULT NULL COMMENT 'FECHA DEL REGISTRO',
  `hora_impuesto` varchar(45) DEFAULT NULL COMMENT 'HORA DEL REGISTRO',
  PRIMARY KEY (`cod_impuesto`),
  KEY `fk_impu_tipimp_idx` (`cod_tipoimpuesto`),
  KEY `fk_impu_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='<double-click to overwrite multiple objects>' AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `fa_impuesto`
--

INSERT INTO `fa_impuesto` (`cod_impuesto`, `nom_impuesto`, `por_impuesto`, `des_impuesto`, `cod_tipoimpuesto`, `cod_usuario`, `fec_impuesto`, `hora_impuesto`) VALUES
(1, 'uno', '003', 'TRES df', 1, 1, '2014-05-17', '18:48:18'),
(2, 'aaaaaaaa', '33', 'asdfadsfadf', 2, 1, '2014-05-19', '22:08:18'),
(3, 'dos', '22', 'descip dos', 2, 1, '2014-05-19', '22:16:47'),
(4, 'asdf', '004', 'sdfg', 2, 1, '2014-07-23', '15:42:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_inventario`
--

CREATE TABLE IF NOT EXISTS `fa_inventario` (
  `cod_inventario` int(4) NOT NULL AUTO_INCREMENT COMMENT 'PK DE LA TABLA AUTOINCREMENTO',
  `cod_unimedida` int(4) NOT NULL COMMENT 'CODIGO DE LA UNIDAD DE MEDIDA DEL ITEM',
  `can_ini_inventario` varchar(45) NOT NULL COMMENT 'CANTIDAD INICIAL DEL INVENTARIO',
  `imp_uni_inventario` varchar(45) NOT NULL COMMENT 'COSTO UNIDAD',
  `cod_item` int(8) NOT NULL COMMENT 'CODIGO DEL ITEM INVENTARIABLE',
  PRIMARY KEY (`cod_inventario`),
  KEY `fk_inv_unimed_idx` (`cod_unimedida`),
  KEY `fk_inv_item_idx` (`cod_item`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='<double-click to overwrite multiple objects>' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `fa_inventario`
--

INSERT INTO `fa_inventario` (`cod_inventario`, `cod_unimedida`, `can_ini_inventario`, `imp_uni_inventario`, `cod_item`) VALUES
(1, 1, '1', '1', 5),
(2, 2, '3', '120000', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_inventario_ajuste`
--

CREATE TABLE IF NOT EXISTS `fa_inventario_ajuste` (
  `cod_inventario_ajuste` int(8) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMENTO DE LA TABLA',
  `ind_incremento` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA SI EL AJUSTE ES PARA AUMENTAR EL INVENTARIO',
  `ind_descremento` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA SI EL AJUSTE ES PARA DISMINUIR EL INVENTARIO',
  `can_inventario_ajuste` varchar(8) NOT NULL COMMENT 'CANTIDAD QUE DESEO AJUSTAR',
  `obs_inventario_ajuste` varchar(120) DEFAULT NULL COMMENT 'OBSERVACIONES DEL AJUSTE',
  `fec_inventario_ajuste` date DEFAULT NULL COMMENT 'FECHA DEL AJUSTE',
  `hora_inventario_ajuste` varchar(45) DEFAULT NULL COMMENT 'HORA DEL AJUSTE',
  `cod_inventario` int(4) NOT NULL COMMENT 'CODIGO DEL INVENTARIO AL ITEM FACTURABLE QUE DESEO HACER EL AJUSTE',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DEL USUARIO QUE HACE EL AJUSTE',
  PRIMARY KEY (`cod_inventario_ajuste`),
  KEY `fk_invaju_inv_idx` (`cod_inventario`),
  KEY `fk_invaju_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_item`
--

CREATE TABLE IF NOT EXISTS `fa_item` (
  `cod_item` int(8) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO PK AUTOINCREMENTO DE LA TABLA',
  `nom_item` varchar(45) NOT NULL COMMENT 'NOMBRE DEL ITEM FACTURABLE',
  `ref_item` varchar(45) NOT NULL COMMENT 'REFERENCIA DE CODIGO DE BARRAS DEL ITEM',
  `des_item` varchar(120) NOT NULL COMMENT 'DESCRIPCION DEL ITEM FACTURABLE',
  `imp_compra_item` varchar(20) NOT NULL COMMENT 'IMPORTE DE VALOR DE COMPRA DEL ITEM * UNIDAD',
  `inc_porcen_item` varchar(3) NOT NULL COMMENT 'INCREMENTO DE GANANCIA DEL ITEM',
  `imp_venta` varchar(20) NOT NULL COMMENT 'IMPORTE DE VENTA, CALCULADO IMP DE COMPRA * INCREMENTO DE GANANCIA',
  `ind_inventario_item` int(1) NOT NULL DEFAULT '0' COMMENT 'INDICA SI EL ITEM ES INVENTARIABLE',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DEL USUARIO QUE REGISTRA',
  `cod_impuesto` int(4) NOT NULL COMMENT 'CODIGO DEL IMPUESTO',
  `cod_cuenta` int(8) NOT NULL COMMENT 'CODIGO DE LA SUBCATEGORIA',
  PRIMARY KEY (`cod_item`),
  KEY `fk_item_usu_idx` (`cod_usuario`),
  KEY `fk_item_impu_idx` (`cod_impuesto`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS ITEMS FACURABLES' AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `fa_item`
--

INSERT INTO `fa_item` (`cod_item`, `nom_item`, `ref_item`, `des_item`, `imp_compra_item`, `inc_porcen_item`, `imp_venta`, `ind_inventario_item`, `cod_usuario`, `cod_impuesto`, `cod_cuenta`) VALUES
(5, 'con', 'con', 'con', '1000', '10', '1100', 1, 1, 1, 90),
(6, 'celular', 'celular 01', 'celular inteligente', '1200000', '10', '1320000', 0, 1, 2, 90),
(7, 'iphone 5', 'ref_00010303302', 'iphone 5', '120000', '005', '126000', 1, 1, 1, 90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_met_pago`
--

CREATE TABLE IF NOT EXISTS `fa_met_pago` (
  `cod_met_pago` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK UNICO DE TABLA AUTOINCREMENTABLE',
  `nom_met_pagocol` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL METODO DE PAGO',
  `des_met_pago` varchar(120) DEFAULT NULL COMMENT 'DESCRIPCION DEL METODO DE PAGO',
  `cod_estado` varchar(3) DEFAULT NULL COMMENT 'CODIGO DEL ESTADO POR DEFECTO DEBE SER AAA',
  `cod_usuario` int(8) DEFAULT NULL COMMENT 'USUARIO QUE REGISTRA',
  `cod_empresa` int(8) DEFAULT NULL COMMENT 'EMPRESA AL CUAL PERTENECE LE METODO',
  PRIMARY KEY (`cod_met_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS METODOS DE PAGO DE LOS INGRESOS Y EGRESOS' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_moneda`
--

CREATE TABLE IF NOT EXISTS `fa_moneda` (
  `cod_moneda` int(8) NOT NULL COMMENT 'PK DE TABLA AUTOINCREMENTO',
  `nom_moneda` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL TIPO DE MONEDA',
  `des_moneda` varchar(45) DEFAULT NULL COMMENT 'DESCRIPCION DEL TIPO DE MONEDA',
  `abr_moneda` varchar(4) DEFAULT NULL COMMENT 'ABREVIATURA DEL TIPO DE MONEDA',
  PRIMARY KEY (`cod_moneda`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS DIFERENTES TIPOS DE MONEDAS';

--
-- Volcado de datos para la tabla `fa_moneda`
--

INSERT INTO `fa_moneda` (`cod_moneda`, `nom_moneda`, `des_moneda`, `abr_moneda`) VALUES
(0, 'Colombia Peso', 'Peso Colombiano', 'COP'),
(1, 'mi', 'mio', 'oala'),
(2, 'DOLAR', 'MONEDA EXTRANJERA AMERICANA', 'dll');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_numeracion`
--

CREATE TABLE IF NOT EXISTS `fa_numeracion` (
  `cod_numeracion` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMENTO DE LA TABLA',
  `nom_numeracion` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DE LA NUMERACION',
  `pre_numeracion` varchar(45) DEFAULT NULL COMMENT 'PREFIJO DE LA NUMERACION',
  `num_inicial_numeracion` int(45) DEFAULT '0' COMMENT 'NUMERO INICIAL PARA LOS DOCUMENTOS DE FACTURACION, ESTOS SE MUESTRAN CONCATENADOS CON EL PREFIJO DE LA FACTURACION',
  `num_final_numeracion` int(45) DEFAULT '0' COMMENT 'NUMERO HASTA DONDE PUEDE LLEGAR UNA NUMERACION',
  `num_sig_numeracion` int(45) DEFAULT '0' COMMENT 'NUMERO SIGUIENTE FACTURACION',
  `res_numeracion` varchar(150) DEFAULT NULL COMMENT 'RESOLUCION DIAN QUE AUTORIZA LA FACTURACION',
  `ind_preferida_numeracion` int(1) DEFAULT '0' COMMENT 'INDICA SI LA NUMERACION ES LA QUE SE DEBE MOSTRAR EN LOS DOCUMENTOS',
  `ind_auto_numeracion` int(1) DEFAULT '0' COMMENT 'INDICA SI LA FACTURACION SE AUMENTA AUTOMATICAMENTE',
  `cod_estado` varchar(3) DEFAULT NULL COMMENT 'CODIGO DEL ESTADO ACTUAL DE LA NUMERACION',
  `cod_usuario` int(8) DEFAULT NULL COMMENT 'USUARIO QUE REGISTRA LA NUMERACION',
  `fec_numeracion` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `hora_numeracion` varchar(45) DEFAULT NULL COMMENT 'HORA DE REGISTRO',
  PRIMARY KEY (`cod_numeracion`),
  KEY `fk_num_est_idx` (`cod_estado`),
  KEY `fk_num_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LAS DIFERENTES NUMERACIONES PARA LOS DOCUMENTOS DE FACTURACION' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `fa_numeracion`
--

INSERT INTO `fa_numeracion` (`cod_numeracion`, `nom_numeracion`, `pre_numeracion`, `num_inicial_numeracion`, `num_final_numeracion`, `num_sig_numeracion`, `res_numeracion`, `ind_preferida_numeracion`, `ind_auto_numeracion`, `cod_estado`, `cod_usuario`, `fec_numeracion`, `hora_numeracion`) VALUES
(1, 'NUMERACION DIAN 001', 'RUBIK_GROUP_', 0, 99999, 4, 'Resolucion de facturacion dian', 1, 1, 'AAA', 1, '2014-09-17', '11:23:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_orden`
--

CREATE TABLE IF NOT EXISTS `fa_orden` (
  `cod_orden` int(9) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMENTABLE DE LA TABLA',
  `nom_orden` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DE LA ORDEN',
  `des_orden` varchar(250) DEFAULT NULL COMMENT 'DESCRIPCION DE LA ORDEN',
  `img_orden` varchar(150) DEFAULT NULL COMMENT 'ADJUNTO',
  `fec_orden` timestamp NULL DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `cod_usuario` int(9) DEFAULT NULL COMMENT 'USUARIO QUE REGISTRA',
  `cod_factura` int(8) DEFAULT NULL COMMENT 'FACTURA A LA QUE PERTENECE LA ORDEN',
  `cod_empresa` int(8) DEFAULT NULL COMMENT 'CODGO DE LA EMPRESA',
  PRIMARY KEY (`cod_orden`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LAS ORDENES DE COMPRA PARA UNA FACTURA' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `fa_orden`
--

INSERT INTO `fa_orden` (`cod_orden`, `nom_orden`, `des_orden`, `img_orden`, `fec_orden`, `cod_usuario`, `cod_factura`, `cod_empresa`) VALUES
(1, 'orden 1', 'orden de compra para lamacenes exito aprobando cotizacion adjunta', NULL, NULL, 1, 1, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_regimen`
--

CREATE TABLE IF NOT EXISTS `fa_regimen` (
  `cod_regimen` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK DE TABLA AUTOINCREMENTO',
  `nom_regimen` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL REGIMEN',
  `des_regimen` varchar(45) DEFAULT NULL COMMENT 'DESCRIPCION DEL REGIMEN',
  PRIMARY KEY (`cod_regimen`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TAB LA PARA ALMACENAR LOS DIFERENTES TIPOS DE REGIMEN' AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `fa_regimen`
--

INSERT INTO `fa_regimen` (`cod_regimen`, `nom_regimen`, `des_regimen`) VALUES
(1, 'Regimen Comun c', 'Regimen Comun c'),
(2, 'Regimen Simplificado', 'Regimen Simplificado'),
(3, 'Regimen Especial', 'Regimen Especial'),
(4, 'otro', 'otro regimen'),
(5, 'a', 'a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_tipoimpuesto`
--

CREATE TABLE IF NOT EXISTS `fa_tipoimpuesto` (
  `cod_tipoimpuesto` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DE LA TABLA PK',
  `nom_tipoimpuesto` varchar(45) NOT NULL COMMENT 'NOMBRE DEL IMPUESTO',
  `des_tipoimpuesto` varchar(45) DEFAULT NULL COMMENT 'DESCRIPCION DEL IMPUESTO',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DEL USUARIO QUE REGISTRA',
  `fec_tipoimpuesto` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `hora_tipoimpuesto` varchar(45) DEFAULT NULL COMMENT 'HORA DEL REGISTRO',
  PRIMARY KEY (`cod_tipoimpuesto`),
  KEY `fk_imp_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS DIFERENTES TIPOS DE IMPUESTOS SEGUN DISPOSICION DIAN' AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `fa_tipoimpuesto`
--

INSERT INTO `fa_tipoimpuesto` (`cod_tipoimpuesto`, `nom_tipoimpuesto`, `des_tipoimpuesto`, `cod_usuario`, `fec_tipoimpuesto`, `hora_tipoimpuesto`) VALUES
(1, 'IVA', 'impuesto de IVA.', 1, '2014-05-16', '22:12:20'),
(2, 'otro', 'impuesto sads', 1, '2014-05-19', '17:57:50'),
(3, 'ICCO', 'OTRO', 1, '2014-07-23', '15:23:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_tipopago`
--

CREATE TABLE IF NOT EXISTS `fa_tipopago` (
  `cod_tipopago` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMENTO DE LA TABLA',
  `nom_tipopago` varchar(45) NOT NULL COMMENT 'NOMBRE DEL TIPO DE PAGO',
  `num_dias_tipopago` varchar(45) NOT NULL COMMENT 'NUMERO DE DIAS PARA EL PAGO',
  `cod_estado` varchar(3) NOT NULL COMMENT 'CODIGO DE ESTADO DEL TIPO PAGO',
  `cod_usuario` int(8) DEFAULT NULL COMMENT 'CODIGO DEL USUARIO QUE REGISTRA',
  `fec_tipopago` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `hora_tipopago` varchar(45) DEFAULT NULL COMMENT 'HORA DE REGISTRO',
  PRIMARY KEY (`cod_tipopago`),
  KEY `fk_tippago_est_idx` (`cod_estado`),
  KEY `fk_tippago_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA REGISTRA LOS TIPOS DE PAGO PARA LOS CLIENTES.' AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `fa_tipopago`
--

INSERT INTO `fa_tipopago` (`cod_tipopago`, `nom_tipopago`, `num_dias_tipopago`, `cod_estado`, `cod_usuario`, `fec_tipopago`, `hora_tipopago`) VALUES
(1, 'contado', '2', 'AAA', 1, '2014-05-18', '10:26:37'),
(2, 'SEMANAL', '8', 'AAA', 1, '2014-05-27', '20:08:49'),
(3, 'QUINCENAL', '15', 'AAA', 1, '2014-05-27', '20:56:58'),
(4, '30 dias', '30', 'AAA', 1, '2014-07-23', '15:48:41'),
(5, 'Mensual', '30', 'AAA', 1, '2014-07-23', '15:51:05'),
(6, 'TRIMESTRAL', '90', 'AAA', 1, '2014-07-23', '15:58:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_transaccion`
--

CREATE TABLE IF NOT EXISTS `fa_transaccion` (
  `cod_transaccion` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK UNICO DE TABLA AUTOINCREMENTABLE',
  `cod_cliente` int(8) DEFAULT NULL,
  `cod_met_pago` int(8) DEFAULT NULL,
  `fa_transaccioncol` varchar(45) DEFAULT NULL,
  `cod_config` int(8) DEFAULT NULL,
  `num_comprobante` int(8) DEFAULT NULL,
  `ind_egreso` int(8) DEFAULT NULL,
  `ind_ingreso` int(1) DEFAULT NULL,
  `obs_transaccion` varchar(2000) DEFAULT NULL,
  `nota_transaccion` varchar(120) DEFAULT NULL,
  `cod_usuario` int(8) DEFAULT NULL,
  `cod_empresa` int(8) DEFAULT NULL,
  `cod_estado` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`cod_transaccion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='TABLA PARA GUARDAR LAS TRANSACCIONES DE INGRESO O EGRESO DE LA EMPRESA' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fa_unimedida`
--

CREATE TABLE IF NOT EXISTS `fa_unimedida` (
  `cod_unimedida` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DE TABLA',
  `nom_unimedida` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DE LA UNIDAD DE MEDIDA',
  `des_unimedida` varchar(120) DEFAULT NULL COMMENT 'DESCRIPCION DE LA UNIDAD DE MEDIDA',
  `pre_unimedida` varchar(3) DEFAULT NULL COMMENT 'PREFIJO DE LA UNIDAD DE MEDIDA',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DEL USUARIO QUE REGISTRA',
  `fec_unimedida` date DEFAULT NULL COMMENT 'FECHA DEL REGISTRO',
  `hora_unimedida` varchar(45) DEFAULT NULL COMMENT 'HORA DEL REGISTRO',
  PRIMARY KEY (`cod_unimedida`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LAS UNIDADES DE MEDIDA PARA LOS ITEMS DE INVENTARIO' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `fa_unimedida`
--

INSERT INTO `fa_unimedida` (`cod_unimedida`, `nom_unimedida`, `des_unimedida`, `pre_unimedida`, `cod_usuario`, `fec_unimedida`, `hora_unimedida`) VALUES
(1, 'uno', 'descriocion', 'UNI', 1, '2014-05-18', '11:24:31'),
(2, 'KILOGRAMO', 'UNIDADE DE MEDIDAD PARA ARITCULOS DE PESO.', 'Kg', 1, '2014-07-23', '17:22:38');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_cliente`
--
CREATE TABLE IF NOT EXISTS `fa_view_cliente` (
`Cod` varchar(113)
,`Codigo` int(8)
,`Nombre` varchar(150)
,`Nit` varchar(20)
,`Direccion` varchar(45)
,`Email` varchar(45)
,`Telefono` varchar(12)
,`Telefono 1` varchar(12)
,`Fax` varchar(20)
,`Celular` varchar(12)
,`Cliente` varchar(43)
,`Proveedor` varchar(43)
,`Observaciones` varchar(200)
,`Ciudad` varchar(91)
,`Tipo Pago` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_config`
--
CREATE TABLE IF NOT EXISTS `fa_view_config` (
`Cod` varchar(127)
,`Nombre` varchar(45)
,`Terminos y Condiciones` varchar(45)
,`Notas Facturacion` varchar(120)
,`Apl. Retencion` varchar(43)
,`# Recibo Caja` int(8)
,`# Comprobante Pago` int(8)
,`Estado` varchar(45)
,`Registro` text
,`Fecha Registro` date
,`Hora Registro` time
,`Ult. Modificacion` date
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_descuentos`
--
CREATE TABLE IF NOT EXISTS `fa_view_descuentos` (
`Cod` varchar(131)
,`Codigo` int(4)
,`Nombre Descuento` varchar(45)
,`Descripcion` varchar(120)
,`Porcentaje` int(3)
,`Fecha de Inicio` date
,`Fecha Fin` date
,`Estado` varchar(51)
,`Usuario` text
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_factura`
--
CREATE TABLE IF NOT EXISTS `fa_view_factura` (
`Cod` varchar(113)
,`Interno` int(8)
,`Cotizacion` varchar(43)
,`Numeracion` varchar(57)
,`Numero Factura` varchar(45)
,`Cliente` varchar(150)
,`Fecha Registro` date
,`Fecha Vencimiento` date
,`Plazo Dias` varchar(50)
,`Sub Total` varchar(61)
,`Con Descuento` varchar(61)
,`Importe` varchar(95)
,`Adeudado` varchar(61)
,`Cancelado` varchar(61)
,`Observaciones` varchar(200)
,`Notas` varchar(200)
,`Recurrente` varchar(43)
,`Estado Factura` varchar(45)
,`Configuracion` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_facturacion`
--
CREATE TABLE IF NOT EXISTS `fa_view_facturacion` (
`Cod` varchar(113)
,`Interno` int(8)
,`Factura Venta` varchar(43)
,`Numeracion` varchar(57)
,`Numero Factura` varchar(45)
,`Cliente` varchar(150)
,`Fecha Registro` date
,`Fecha Vencimiento` date
,`Plazo Dias` varchar(50)
,`Sub Total` varchar(61)
,`Con Descuento` varchar(61)
,`Importe` varchar(95)
,`Adeudado` varchar(61)
,`Cancelado` varchar(61)
,`Observaciones` varchar(200)
,`Notas` varchar(200)
,`Recurrente` varchar(43)
,`Estado Factura` varchar(45)
,`Configuracion` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_impuesto`
--
CREATE TABLE IF NOT EXISTS `fa_view_impuesto` (
`Cod` varchar(129)
,`Nombre` varchar(45)
,`Porcentaje` varchar(3)
,`Descripcion` varchar(120)
,`Tipo Impuesto` varchar(45)
,`Registro` text
,`Fecha Registro` date
,`Hora Registro` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_inventario`
--
CREATE TABLE IF NOT EXISTS `fa_view_inventario` (
`Cod` varchar(131)
,`Item` varchar(91)
,`Unidad Medida` varchar(49)
,`Cantidad Inicial` varchar(45)
,`Importe X Uni` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_item`
--
CREATE TABLE IF NOT EXISTS `fa_view_item` (
`Cod` varchar(125)
,`Codigo` int(8)
,`Nombre Item` varchar(45)
,`Referencia` varchar(45)
,`Descripcion` varchar(120)
,`Importe compra` varchar(20)
,`Incremento x Utilidad` varchar(3)
,`Importe Venta` varchar(20)
,`Inventario` varchar(43)
,`Registro` text
,`Impuesto` varchar(45)
,`Categoria` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_moneda`
--
CREATE TABLE IF NOT EXISTS `fa_view_moneda` (
`Cod` varchar(127)
,`Nombre` varchar(45)
,`Descripcion` varchar(45)
,`Nom Abreviado` varchar(4)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_numeracion`
--
CREATE TABLE IF NOT EXISTS `fa_view_numeracion` (
`Cod` varchar(131)
,`Nombre` varchar(45)
,`Prefijo` varchar(45)
,`Numero Inicial` int(45)
,`Numero Final` int(45)
,`Numero Siguiente` int(45)
,`Resolucion` varchar(150)
,`Prefierida` varchar(43)
,`AutoIncremento` varchar(43)
,`Estado` varchar(45)
,`Registro` text
,`Fecha Registro` date
,`Hora Registro` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_orden`
--
CREATE TABLE IF NOT EXISTS `fa_view_orden` (
`Cod` varchar(111)
,`Codigo` int(9)
,`Factura` varchar(45)
,`Nombre` varchar(45)
,`Descripcion` varchar(250)
,`Soporte` varchar(246)
,`Fecha Registro` timestamp
,`Usuario` text
,`nom_empresa` varchar(45)
,`cod_empresa` int(8)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_regimen`
--
CREATE TABLE IF NOT EXISTS `fa_view_regimen` (
`Cod` varchar(128)
,`Nombre` varchar(45)
,`Descripcion` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_tipoimpuesto`
--
CREATE TABLE IF NOT EXISTS `fa_view_tipoimpuesto` (
`Cod` varchar(133)
,`Nombre` varchar(45)
,`Descripcion` varchar(45)
,`Usuario` text
,`Fecha Registro` date
,`Hora Registro` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_tipopago`
--
CREATE TABLE IF NOT EXISTS `fa_view_tipopago` (
`Cod` varchar(129)
,`Nombre` varchar(45)
,`# Dias` varchar(45)
,`Estado` varchar(45)
,`Usuario` text
,`Fecha Registro` date
,`Hora Registro` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `fa_view_unimedida`
--
CREATE TABLE IF NOT EXISTS `fa_view_unimedida` (
`Cod` varchar(130)
,`Nombre` varchar(45)
,`Descripcion` varchar(120)
,`Prefijo` varchar(3)
,`Usuario` text
,`Fecha Registro` date
,`Hora Registro` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hd_asignacion`
--

CREATE TABLE IF NOT EXISTS `hd_asignacion` (
  `cod_asignacion` int(8) NOT NULL AUTO_INCREMENT,
  `cod_servicio` int(9) DEFAULT NULL,
  `cod_usuario` int(9) DEFAULT NULL,
  `fec_asignacion` datetime DEFAULT NULL,
  `cod_usuario_asigna` varchar(45) DEFAULT NULL COMMENT 'USUARIO QUE ASIGNA',
  PRIMARY KEY (`cod_asignacion`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA LA ASGINACION DE LOS SERVICIOS A UN USUARIO' AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `hd_asignacion`
--

INSERT INTO `hd_asignacion` (`cod_asignacion`, `cod_servicio`, `cod_usuario`, `fec_asignacion`, `cod_usuario_asigna`) VALUES
(8, 1, 2, '2014-09-17 13:44:10', '1'),
(9, 2, 2, '2014-10-28 22:04:11', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hd_gestion`
--

CREATE TABLE IF NOT EXISTS `hd_gestion` (
  `cod_gestion` int(9) NOT NULL AUTO_INCREMENT,
  `des_gestion` varchar(2000) DEFAULT NULL COMMENT 'DESCRIPCION DE LA GESTION REALIZADA AL SERVICIO',
  `fec_gestion` datetime DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `cod_usuario` int(9) DEFAULT NULL COMMENT 'USURARIO QUE REALIZA EL REGISTRO',
  PRIMARY KEY (`cod_gestion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LAS GESTIONES REALIZADAS A UN SERVICIO' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hd_referencia`
--

CREATE TABLE IF NOT EXISTS `hd_referencia` (
  `cod_referencia` int(9) NOT NULL AUTO_INCREMENT COMMENT 'PK DE TALBLA AUTOINCREMENTO',
  `nom_referncia` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DE LA REFERENCIA',
  `des_referencia` varchar(200) DEFAULT NULL COMMENT 'DESCRIPCION DE LA REFERENCAI',
  `fec_referencia` datetime DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `cod_usuario` int(9) NOT NULL COMMENT 'USUARIO QUE REGISTRA',
  PRIMARY KEY (`cod_referencia`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA LAS REFERENCIAS DE LOS SERVICIOS' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `hd_referencia`
--

INSERT INTO `hd_referencia` (`cod_referencia`, `nom_referncia`, `des_referencia`, `fec_referencia`, `cod_usuario`) VALUES
(1, 'Cotizacion', 'cotizacion', '2014-08-13 22:27:01', 1),
(2, 'Factura', 'Factura', '2014-08-13 22:27:01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hd_servicio`
--

CREATE TABLE IF NOT EXISTS `hd_servicio` (
  `cod_servicio` int(9) NOT NULL AUTO_INCREMENT COMMENT 'PK DE TABLA',
  `des_servicio` varchar(2000) DEFAULT NULL,
  `fec_servicio` datetime DEFAULT NULL COMMENT 'FECHA DE APERTURA DEL SERVICIO',
  `fec_cierre` datetime DEFAULT NULL COMMENT 'FECHA DEL CIERRE DEL SERVICIO',
  `ind_asigna` int(1) DEFAULT '0' COMMENT 'INDICA SI EL SERVICIO SE ASIGNA A EL USAURIO QUE REGISTRA LA TRANSACCION, SI ESTA EL 1 SE ASIGNA AL MISMO USUARIO, SI ESTA EN 0 SE ASIGNA AL USUARIO DE LA LISTA',
  `cod_referencia` int(8) DEFAULT NULL COMMENT 'HACE REFERENCIA AL SERVICIO(COTIZACION-FACTURA-INSTALACION-GARANTIA-SOPORTE)',
  `cod_estado` varchar(3) DEFAULT NULL,
  `cod_usuario` int(9) DEFAULT NULL,
  PRIMARY KEY (`cod_servicio`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA AGENDAR LOS SEVICIOS POR USUARIO' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `hd_servicio`
--

INSERT INTO `hd_servicio` (`cod_servicio`, `des_servicio`, `fec_servicio`, `fec_cierre`, `ind_asigna`, `cod_referencia`, `cod_estado`, `cod_usuario`) VALUES
(1, 'Ha sido asignado el siguiente servicio a su Service desk -  Servicio: Cotizacion Cliente: RICARDO GONZALEZING.RICARDO.GONZALEZ@HOTMAIL.COM Fecha de apertura: 2014-09-17 13:44:10 Estado actual: servicio service desk activo', '2014-09-17 13:44:10', NULL, 0, 1, 'SAA', 1),
(2, 'Ha sido asignado el siguiente servicio a su Service desk -  Servicio:  Cliente: TATIANA BETANCURJH Fecha de apertura: 2014-10-28 22:04:11 Estado actual: servicio service desk activo', '2014-10-28 22:04:11', NULL, 0, 1, 'SAA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hue_cliente`
--

CREATE TABLE IF NOT EXISTS `hue_cliente` (
  `cod_cliente` int(8) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DE TABLA AUTOINCREMENTO',
  `nom_cliente` varchar(150) NOT NULL,
  `nit_cliente` varchar(20) NOT NULL COMMENT 'NUMERO NIT DEL CLIENTE',
  `huella_cliente` text,
  `dir_cliente` varchar(45) NOT NULL COMMENT 'DIRECCION DE LOCALIZACION',
  `email_cliente` varchar(45) DEFAULT NULL COMMENT 'EMAIL DEL CLIENTE',
  `tel_cliente` varchar(12) NOT NULL COMMENT 'TELEFONO DE CONTACTO 1',
  `tel1_cliente` varchar(12) DEFAULT NULL COMMENT 'TELEFONO DE CONTACTO 2',
  `fax_cliente` varchar(20) DEFAULT NULL COMMENT 'NUMERO DE FAX',
  `cel_cliente` varchar(12) DEFAULT NULL COMMENT 'NUMERO MOVIL',
  `obs_cliente` varchar(200) DEFAULT NULL COMMENT 'PARA REGISTRAR TODAS LAS DEMAS OBSERVACIONES DEL CLIENTE',
  `cod_ciudad` int(6) NOT NULL COMMENT 'CODIGO DE LA CUIDAD A LA CUAL PERTENECE',
  `cod_empresa` int(8) NOT NULL,
  `cod_estado` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`cod_cliente`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS CLIENTES' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `hue_cliente`
--

INSERT INTO `hue_cliente` (`cod_cliente`, `nom_cliente`, `nit_cliente`, `huella_cliente`, `dir_cliente`, `email_cliente`, `tel_cliente`, `tel1_cliente`, `fax_cliente`, `cel_cliente`, `obs_cliente`, `cod_ciudad`, `cod_empresa`, `cod_estado`) VALUES
(1, 'H', 'H', 'H - H2014-11-06-2014-11-06.bmp', 'H', 'H', 'H', 'H', 'H', 'H', 'H', 5172, 15, 'CVA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hue_cliente_vehiculo`
--

CREATE TABLE IF NOT EXISTS `hue_cliente_vehiculo` (
  `cod_cliente_vehiculo` int(8) NOT NULL AUTO_INCREMENT,
  `cod_cliente` int(8) DEFAULT NULL,
  `cod_vehiculo` int(8) DEFAULT NULL,
  `fec_registro` datetime DEFAULT NULL,
  `fec_entrega` datetime DEFAULT NULL,
  `fec_entrega_final` datetime DEFAULT NULL,
  `cod_estado` varchar(3) DEFAULT NULL,
  `huella_cliente_vehiculo` text,
  `obs_cliente_vehiculo` varchar(2500) DEFAULT NULL,
  `num_dias_ret_cliente_vehiculo` varchar(45) DEFAULT NULL,
  `cod_usuario` int(8) DEFAULT NULL,
  PRIMARY KEY (`cod_cliente_vehiculo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR EL ALQUILER DE VEHICULOS' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `hue_cliente_vehiculo`
--

INSERT INTO `hue_cliente_vehiculo` (`cod_cliente_vehiculo`, `cod_cliente`, `cod_vehiculo`, `fec_registro`, `fec_entrega`, `fec_entrega_final`, `cod_estado`, `huella_cliente_vehiculo`, `obs_cliente_vehiculo`, `num_dias_ret_cliente_vehiculo`, `cod_usuario`) VALUES
(1, 1, 1, '2014-11-06 00:00:00', '2014-11-07 00:00:00', '2014-11-08 00:00:00', 'BBB', 'H - H2014-11-06-2014-11-07.bmp', 'observacion', NULL, 1),
(2, 1, 1, '2014-11-06 00:00:00', '2014-11-06 00:00:00', '0000-00-00 00:00:00', 'AAA', 'H - H2014-11-06-2014-11-06.bmp', '', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hue_combustible`
--

CREATE TABLE IF NOT EXISTS `hue_combustible` (
  `cod_combustible` int(8) NOT NULL AUTO_INCREMENT,
  `nom_combustible` varchar(45) DEFAULT NULL,
  `des_combustible` varchar(250) DEFAULT NULL,
  `fec_combustible` datetime DEFAULT NULL,
  `cod_usuario` int(8) DEFAULT NULL,
  `cod_empresa` int(8) DEFAULT NULL,
  `cod_estado` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`cod_combustible`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS TIPOS DE COMBUSTIBLE PARA LOS VEHICULOS' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `hue_combustible`
--

INSERT INTO `hue_combustible` (`cod_combustible`, `nom_combustible`, `des_combustible`, `fec_combustible`, `cod_usuario`, `cod_empresa`, `cod_estado`) VALUES
(1, 'GASOLINA', 'VEHICULOS A GASOLINA.', '2014-11-06 14:03:09', 1, 15, 'AAA'),
(2, 'GAS', 'VEHICULOS A GAS', '2014-11-06 14:03:23', 1, 15, 'AAA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hue_tip_documento`
--

CREATE TABLE IF NOT EXISTS `hue_tip_documento` (
  `cod_tip_documento` int(8) NOT NULL AUTO_INCREMENT,
  `nom_tip_documento` varchar(45) DEFAULT NULL,
  `des_tip_documento` varchar(250) DEFAULT NULL,
  `fec_tip_documento` datetime DEFAULT NULL,
  `cod_empresa` int(8) DEFAULT NULL,
  `cod_usuario` int(8) DEFAULT NULL,
  `cod_estado` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`cod_tip_documento`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS TIPOS DE DOCUMENTOS PARA LOS VEHICULOS' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `hue_tip_documento`
--

INSERT INTO `hue_tip_documento` (`cod_tip_documento`, `nom_tip_documento`, `des_tip_documento`, `fec_tip_documento`, `cod_empresa`, `cod_usuario`, `cod_estado`) VALUES
(1, 'SOAT', 'seguro obligatorio de accidentes de transito.', '2014-11-06 13:56:20', 15, 1, 'AAA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hue_tip_servicio`
--

CREATE TABLE IF NOT EXISTS `hue_tip_servicio` (
  `cod_tip_servicio` int(11) NOT NULL AUTO_INCREMENT,
  `nom_tip_servicio` varchar(45) DEFAULT NULL,
  `des_tip_servicio` varchar(250) DEFAULT NULL,
  `fec_tip_servicio` datetime DEFAULT NULL,
  `cod_usuario` int(8) DEFAULT NULL,
  `cod_empresa` int(8) DEFAULT NULL,
  `cod_estado` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`cod_tip_servicio`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS TIPOS DE VEHICULO PARTICULAR,PUBLICO,PRIVADO,GUBERNAMENTAL' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `hue_tip_servicio`
--

INSERT INTO `hue_tip_servicio` (`cod_tip_servicio`, `nom_tip_servicio`, `des_tip_servicio`, `fec_tip_servicio`, `cod_usuario`, `cod_empresa`, `cod_estado`) VALUES
(1, 'PARTICULAR', 'TIPO DE SERVICIO PARTICULAR O PARA USO DEL HOGAR', '2014-11-06 13:36:45', 1, 15, 'AAA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hue_vehiculo`
--

CREATE TABLE IF NOT EXISTS `hue_vehiculo` (
  `cod_vehiculo` int(8) NOT NULL AUTO_INCREMENT,
  `placa_vehiculo` varchar(45) NOT NULL COMMENT 'PLACA DEL VEHICULO',
  `marca_vehiculo` varchar(45) DEFAULT NULL COMMENT 'MARCA DEL VEHICULO',
  `linea_vehiculo` varchar(45) DEFAULT NULL COMMENT 'LINEA DEL VEHICULO',
  `modelo_vehiculo` varchar(4) DEFAULT NULL COMMENT 'MODELO DEL VEHICULO',
  `lic_trans_vehiculo` varchar(45) DEFAULT NULL COMMENT 'LICENCIA DE TRANSITO DEL VEHICULO',
  `cc_vehiculo` varchar(6) DEFAULT NULL COMMENT 'CILINDRAJE DEL VEHICULO',
  `color_vehiculo` varchar(45) DEFAULT NULL,
  `tipo_carro_vehiculo` varchar(45) DEFAULT NULL COMMENT 'TIPO DE CARROCERIA DEL VEHICULO',
  `cap_vehiculo` int(4) DEFAULT NULL COMMENT 'CAPACIDAD',
  `num_motor_vehiculo` varchar(45) DEFAULT NULL COMMENT 'NUMERO DEL MOTOR',
  `num_serie_vehiculo` varchar(45) DEFAULT NULL COMMENT 'NUMERO DE SERIE DEL VEHICULO',
  `num_chasis_vehiculo` varchar(45) DEFAULT NULL COMMENT 'NUMERO DEL CHASIS DEL VEHICULO',
  `img_vehiculo` varchar(120) DEFAULT NULL COMMENT 'IMAGEN DEL CARRO',
  `fec_vehiculo` datetime DEFAULT NULL,
  `cod_vehiculo_clase` int(8) DEFAULT NULL COMMENT 'CLASE DE VEHICULO -  CUATRIMOTO - AUTOMOVIL - CAMIONETA - CAMION',
  `cod_tip_servicio` int(8) DEFAULT NULL COMMENT 'TIPO DE SERVICIO - PARTICULAR - PUBLICO',
  `cod_combustible` int(8) DEFAULT NULL COMMENT 'TIPO COMBUSTIBLE',
  `cod_usuario` int(8) DEFAULT NULL,
  `cod_empresa` int(8) DEFAULT NULL,
  `cod_estado` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`cod_vehiculo`,`placa_vehiculo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LA INFORMACION GENERAL DEL VEHICULO' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `hue_vehiculo`
--

INSERT INTO `hue_vehiculo` (`cod_vehiculo`, `placa_vehiculo`, `marca_vehiculo`, `linea_vehiculo`, `modelo_vehiculo`, `lic_trans_vehiculo`, `cc_vehiculo`, `color_vehiculo`, `tipo_carro_vehiculo`, `cap_vehiculo`, `num_motor_vehiculo`, `num_serie_vehiculo`, `num_chasis_vehiculo`, `img_vehiculo`, `fec_vehiculo`, `cod_vehiculo_clase`, `cod_tip_servicio`, `cod_combustible`, `cod_usuario`, `cod_empresa`, `cod_estado`) VALUES
(1, 'PFD834', 'MAZDA', '3', '2007', '1025514887', '2000', 'PLATA', 'NO', 5, '2012574577', '2356777877', '2315452313', 'adjunto2014-11-06-1415310365.jpg', '2014-11-06 16:46:05', 1, 1, 1, NULL, 15, 'VAA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hue_vehiculo_clase`
--

CREATE TABLE IF NOT EXISTS `hue_vehiculo_clase` (
  `cod_vehiculo_clase` int(8) NOT NULL AUTO_INCREMENT,
  `nom_vehiculo_clase` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DE LA CLASE DE VEHICULO',
  `des_vehiculo_clase` varchar(250) DEFAULT NULL COMMENT 'DESCRIPCION DE LA CLASE DE VEHICULO',
  `fec_vehiculo_clase` datetime DEFAULT NULL,
  `cod_usuario` int(8) DEFAULT NULL COMMENT 'USUARIO QUE REALIZA EL REGISTRO',
  `cod_empresa` int(11) DEFAULT NULL COMMENT 'EMPRESA A LA QUE PERTENCEN LOS REGISTROS',
  `cod_estado` varchar(3) DEFAULT NULL COMMENT 'ESTADO DE LA CLASE DE VEHICULO',
  PRIMARY KEY (`cod_vehiculo_clase`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LAS DIFERENTES CLASES DE VEHICULOS - CAMIONETA,AUTOMOVIL,CAMION ETC' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `hue_vehiculo_clase`
--

INSERT INTO `hue_vehiculo_clase` (`cod_vehiculo_clase`, `nom_vehiculo_clase`, `des_vehiculo_clase`, `fec_vehiculo_clase`, `cod_usuario`, `cod_empresa`, `cod_estado`) VALUES
(1, 'VEHICULO', 'VEHICULO AUTOMOTOR', '2014-11-06 11:44:44', 1, 15, 'AAA'),
(2, 'MOTOCARRO', 'MOTOCARRO', '2014-11-06 13:22:34', 1, 15, 'AAA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hue_vehiculo_datos`
--

CREATE TABLE IF NOT EXISTS `hue_vehiculo_datos` (
  `cod_vehiculo_datos` int(11) NOT NULL AUTO_INCREMENT,
  `res_mov_vehiculo_datos` varchar(45) DEFAULT NULL COMMENT 'RESTRICCION DE MOVILIDAD DEL VEHICULO',
  `blindaje_vehiculo_datos` int(1) DEFAULT '0' COMMENT '1 CON BLINDAJE, 0 SIN BLINDAJE',
  `pot_vehiculo_datos` varchar(45) DEFAULT NULL COMMENT 'POTENCIA DEL VEHICULO',
  `num_pue_vehiculo_datos` int(2) DEFAULT NULL COMMENT 'NUMERO DE PUERTAS DEL VEHICULO',
  `lim_pro_vehiculo_datos` varchar(120) DEFAULT NULL COMMENT 'LIMITACION A LA PROPIEDAD DEL VEHICULO',
  `fec_mat_vehiculo_datos` date DEFAULT NULL COMMENT 'FECHA DE LA MATRICULA DEL VEHICULO',
  `fec_exp_lic_vehiculo_datos` date DEFAULT NULL COMMENT 'FECHA DE EXPEDICION DE LA LICENCIA DE TRANSITO O TARJETA DE PROPIEDAD DEL VEHICULO',
  `fec_ven_vehiculo_datos` date DEFAULT NULL COMMENT 'VENCIMIENTO DE LA TARJETA DE PROPIEDAD DEL VEHICULO',
  `tt_vehiculo_datos` varchar(45) DEFAULT NULL COMMENT 'ORGANISMO DE TRANSITO DONDE ESTA REGISTRADO EL VEHICULO',
  `lat_vehiculo_datos` varchar(45) DEFAULT NULL COMMENT 'LATERAL DEL VEHICULO',
  `cod_vehiculo` int(8) DEFAULT NULL,
  PRIMARY KEY (`cod_vehiculo_datos`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR DATOS GENERALES DE LOS VEHICULOS' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `hue_vehiculo_datos`
--

INSERT INTO `hue_vehiculo_datos` (`cod_vehiculo_datos`, `res_mov_vehiculo_datos`, `blindaje_vehiculo_datos`, `pot_vehiculo_datos`, `num_pue_vehiculo_datos`, `lim_pro_vehiculo_datos`, `fec_mat_vehiculo_datos`, `fec_exp_lic_vehiculo_datos`, `fec_ven_vehiculo_datos`, `tt_vehiculo_datos`, `lat_vehiculo_datos`, `cod_vehiculo`) VALUES
(2, 'NINGUNA', 0, '2000', 5, 'NINGUNA', '2014-11-05', '2014-11-10', '2014-11-03', 'TT MANIZALES', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hue_vehiculo_documentos`
--

CREATE TABLE IF NOT EXISTS `hue_vehiculo_documentos` (
  `cod_vehiculo_documentos` int(8) NOT NULL AUTO_INCREMENT,
  `cod_tip_documento` int(8) DEFAULT NULL COMMENT 'TIPO DE DOCUMENTO SOAT, TECNOMECANICO ETC',
  `num_vehiculo_documento` varchar(45) DEFAULT NULL COMMENT 'NUMERO DE DOCUMENTO SI LO TIENE',
  `fec_vehiculo_documento` datetime DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `fec_ven_vehiculo_documento` date DEFAULT NULL COMMENT 'FECHA DE VENCIMIENTO DEL VEHICULO',
  `cod_vehiculo` int(8) DEFAULT NULL,
  PRIMARY KEY (`cod_vehiculo_documentos`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS DOCUMENTOS DE LOS VEHICULOS' AUTO_INCREMENT=12 ;

--
-- Volcado de datos para la tabla `hue_vehiculo_documentos`
--

INSERT INTO `hue_vehiculo_documentos` (`cod_vehiculo_documentos`, `cod_tip_documento`, `num_vehiculo_documento`, `fec_vehiculo_documento`, `fec_ven_vehiculo_documento`, `cod_vehiculo`) VALUES
(10, 1, '11021554', '2014-01-01 00:00:00', '2016-01-01', 1),
(11, 1, '1187849879', '2016-01-01 00:00:00', '2016-01-01', 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `hue_view_cliente`
--
CREATE TABLE IF NOT EXISTS `hue_view_cliente` (
`Cod` varchar(114)
,`Codigo` int(8)
,`Nombre` varchar(150)
,`Nit` varchar(20)
,`Direccion` varchar(45)
,`Email` varchar(45)
,`Telefono` varchar(12)
,`Telefono 1` varchar(12)
,`Fax` varchar(20)
,`Celular` varchar(12)
,`Observaciones` varchar(200)
,`Ciudad` varchar(91)
,`Empresa` varchar(45)
,`cod_empresa` int(8)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `hue_view_cliente_vehiculo`
--
CREATE TABLE IF NOT EXISTS `hue_view_cliente_vehiculo` (
`Cod` varchar(123)
,`Codigo` int(8)
,`Alerta` varchar(9)
,`Cliente` varchar(173)
,`Vehiculo` varchar(93)
,`Fecha registro` datetime
,`Fecha Vencimiento` datetime
,`Estado` varchar(45)
,`Huella` mediumtext
,`Observaciones` varchar(2500)
,`Dias Retraso` int(7)
,`Usuario` text
,`cod_empresa` int(4)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `hue_view_combustible`
--
CREATE TABLE IF NOT EXISTS `hue_view_combustible` (
`Cod` varchar(118)
,`Codigo` int(8)
,`Combustible` varchar(45)
,`Descripcion` varchar(250)
,`Fecha` datetime
,`Empresa` varchar(45)
,`Estado` varchar(45)
,`Usuario` text
,`cod_empresa` int(8)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `hue_view_tip_documento`
--
CREATE TABLE IF NOT EXISTS `hue_view_tip_documento` (
`Cod` varchar(120)
,`Codigo` int(8)
,`Documento` varchar(45)
,`Descripcion` varchar(250)
,`Fecha` datetime
,`Empresa` varchar(45)
,`Usuario` text
,`Estado` varchar(45)
,`cod_empresa` int(8)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `hue_view_tip_servicio`
--
CREATE TABLE IF NOT EXISTS `hue_view_tip_servicio` (
`Cod` varchar(119)
,`Codigo` int(11)
,`Servicio` varchar(45)
,`Descripcion` varchar(250)
,`Fecha` datetime
,`Empresa` varchar(45)
,`Usuario` text
,`Estado` varchar(45)
,`cod_empresa` int(8)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `hue_view_vehiculo`
--
CREATE TABLE IF NOT EXISTS `hue_view_vehiculo` (
`Cod` varchar(115)
,`Codigo` int(8)
,`Placa` varchar(45)
,`Marca` varchar(45)
,`Linea` varchar(45)
,`Modelo` varchar(4)
,`Lic Transito` varchar(45)
,`Cilindraje` varchar(6)
,`Color` varchar(45)
,`Carroceria` varchar(45)
,`Capacidad` int(4)
,`Nro Motor` varchar(45)
,`Nro Serie` varchar(45)
,`Nro Chasis` varchar(45)
,`Imagen` varchar(194)
,`clase` varchar(45)
,`Tipo Servicio` varchar(45)
,`Combustible` varchar(45)
,`Empresa` varchar(45)
,`Estado` varchar(45)
,`cod_empresa` int(8)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `hue_view_vehiculo_clase`
--
CREATE TABLE IF NOT EXISTS `hue_view_vehiculo_clase` (
`Cod` varchar(121)
,`Codigo` int(8)
,`Clase` varchar(45)
,`Descripcion` varchar(250)
,`Fecha` datetime
,`Empresa` varchar(45)
,`Estado` varchar(45)
,`Usuario` text
,`cod_empresa` int(11)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `hue_view_vehiculo_datos`
--
CREATE TABLE IF NOT EXISTS `hue_view_vehiculo_datos` (
`Cod` varchar(121)
,`Codigo` int(11)
,`Vehiculo` varchar(93)
,`Restriccion de Movilidad` varchar(45)
,`Blindaje` varchar(43)
,`Potencia` varchar(45)
,`Nro Puertas` int(2)
,`Limitaciones de propiedad` varchar(120)
,`Fecha de Matricula` date
,`Expedicion de licencia` date
,`Fecha de Vencimiento` date
,`Organismo de transito` varchar(45)
,`Nro Lateral` varchar(45)
,`cod_empresa` int(8)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `hue_view_vehiculo_documentos`
--
CREATE TABLE IF NOT EXISTS `hue_view_vehiculo_documentos` (
`Cod` varchar(126)
,`Codigo` int(8)
,`Vehiculo` varchar(93)
,`Documento` varchar(45)
,`Nro Documento` varchar(45)
,`Fecha de Registro` datetime
,`Vigencia Hasta` date
,`Vencido` varchar(43)
,`cod_empresa` int(8)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mod_modulo`
--

CREATE TABLE IF NOT EXISTS `mod_modulo` (
  `cod_modulo` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DE LA TABLA AUTOINCREMENTO',
  `nom_modulo` varchar(45) DEFAULT NULL,
  `des_modulo` varchar(45) DEFAULT NULL,
  `fec_ins_modulo` date DEFAULT NULL,
  `hora_ins_modulo` varchar(45) DEFAULT NULL,
  `cod_usuario` int(4) DEFAULT NULL,
  `pach_modulo` varchar(45) DEFAULT NULL,
  `num_vistas_modulos` int(2) DEFAULT NULL,
  `num_menu` int(2) DEFAULT NULL,
  `num_menu_sub` int(2) DEFAULT NULL,
  `num_metodos` int(2) DEFAULT NULL,
  PRIMARY KEY (`cod_modulo`),
  KEY `fk_mod_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS MODULOS QUE SE INSTALAN EN MODULER' AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `mod_modulo`
--

INSERT INTO `mod_modulo` (`cod_modulo`, `nom_modulo`, `des_modulo`, `fec_ins_modulo`, `hora_ins_modulo`, `cod_usuario`, `pach_modulo`, `num_vistas_modulos`, `num_menu`, `num_menu_sub`, `num_metodos`) VALUES
(1, 'MODULO ADMINISTRATIVO', 'MODULO ADMINISTRATIVO', '0000-00-00', NULL, 1, 'MODULES/SISTEMA', 1, 1, 1, 5),
(2, 'MODULO FACTURACION', 'MODULO DE FACTURACION', '0000-00-00', NULL, 1, 'MODULES/FACTURACION', 10, 10, 10, 10),
(3, 'MODULO CONTABLE', 'MODULO CONTABLE', NULL, NULL, 1, 'MODULES/CONTABILIDAD', 10, 10, 10, 10),
(4, 'MODULO LACUCHARA', 'MODULO LACUCHARA', NULL, NULL, 1, 'MODULES/LACUCHARA', 10, 10, 10, 10),
(5, 'HELP DESK', 'MODULO MESA DE SOPORTE', NULL, NULL, 1, 'MODULES/MESASOPORTE', 10, 10, 10, 10),
(6, 'PROYECTO UNI MANIZALES', 'MODULO PROYECTO UNI MANIZALES', NULL, NULL, 1, 'MODULES/PROYECTO', 10, 10, 10, 10),
(7, 'ALQUILER DE VEHICULOS', 'MODULO PARA ALQUILER DE VEHICULOS', NULL, NULL, 1, 'MODULES/HUELLA', 10, 10, 10, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_ciudad`
--

CREATE TABLE IF NOT EXISTS `sys_ciudad` (
  `cod_ciudad` int(6) NOT NULL COMMENT 'CODIGO DE LA TABLA SEGUN INFORMACINO SIG',
  `nom_ciudad` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DE LA CIUDAD',
  `dpt_ciudad` varchar(45) DEFAULT NULL COMMENT 'DEPARTAMENTO AL CUAL PERTENECE LA CIUDAD',
  PRIMARY KEY (`cod_ciudad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABLA QUE ALMACENA LAS CUIDADES POR DEPARTAMENTOS DE COLOMBIA';

--
-- Volcado de datos para la tabla `sys_ciudad`
--

INSERT INTO `sys_ciudad` (`cod_ciudad`, `nom_ciudad`, `dpt_ciudad`) VALUES
(1, 'no aplica', 'no aplica'),
(5001, 'MEDELLÍN', 'Antioquia'),
(5002, 'ABEJORRAL', 'Antioquia'),
(5004, 'ABRIAQUÍ', 'Antioquia'),
(5021, 'ALEJANDRÍA', 'Antioquia'),
(5030, 'AMAGÁ', 'Antioquia'),
(5031, 'AMALFI', 'Antioquia'),
(5034, 'ANDES', 'Antioquia'),
(5036, 'ANGELÓPOLIS', 'Antioquia'),
(5038, 'ANGOSTURA', 'Antioquia'),
(5040, 'ANORÍ', 'Antioquia'),
(5042, 'SANTAFÉ DE ANTIOQUIA', 'Antioquia'),
(5044, 'ANZA', 'Antioquia'),
(5045, 'APARTADÓ', 'Antioquia'),
(5051, 'ARBOLETES', 'Antioquia'),
(5055, 'ARGELIA', 'Antioquia'),
(5059, 'ARMENIA', 'Antioquia'),
(5079, 'BARBOSA', 'Antioquia'),
(5086, 'BELMIRA', 'Antioquia'),
(5088, 'BELLO', 'Antioquia'),
(5091, 'BETANIA', 'Antioquia'),
(5093, 'BETULIA', 'Antioquia'),
(5101, 'sys_ciudad BOLÍVAR', 'Antioquia'),
(5107, 'BRICEÑO', 'Antioquia'),
(5113, 'BURITICÁ', 'Antioquia'),
(5120, 'CÁCERES', 'Antioquia'),
(5125, 'CAICEDO', 'Antioquia'),
(5129, 'CALDAS', 'Antioquia'),
(5134, 'CAMPAMENTO', 'Antioquia'),
(5138, 'CAÑASGORDAS', 'Antioquia'),
(5142, 'CARACOLÍ', 'Antioquia'),
(5145, 'CARAMANTA', 'Antioquia'),
(5147, 'CAREPA', 'Antioquia'),
(5148, 'EL CARMEN DE VIBORAL', 'Antioquia'),
(5150, 'CAROLINA', 'Antioquia'),
(5154, 'CAUCASIA', 'Antioquia'),
(5172, 'CHIGORODÓ', 'Antioquia'),
(5190, 'CISNEROS', 'Antioquia'),
(5197, 'COCORNÁ', 'Antioquia'),
(5206, 'CONCEPCIÓN', 'Antioquia'),
(5209, 'CONCORDIA', 'Antioquia'),
(5212, 'COPACABANA', 'Antioquia'),
(5234, 'DABEIBA', 'Antioquia'),
(5237, 'DON MATÍAS', 'Antioquia'),
(5240, 'EBÉJICO', 'Antioquia'),
(5250, 'EL BAGRE', 'Antioquia'),
(5264, 'ENTRERRIOS', 'Antioquia'),
(5266, 'ENVIGADO', 'Antioquia'),
(5282, 'FREDONIA', 'Antioquia'),
(5284, 'FRONTINO', 'Antioquia'),
(5306, 'GIRALDO', 'Antioquia'),
(5308, 'GIRARDOTA', 'Antioquia'),
(5310, 'GÓMEZ PLATA', 'Antioquia'),
(5313, 'GRANADA', 'Antioquia'),
(5315, 'GUADALUPE', 'Antioquia'),
(5318, 'GUARNE', 'Antioquia'),
(5321, 'GUATAPE', 'Antioquia'),
(5347, 'HELICONIA', 'Antioquia'),
(5353, 'HISPANIA', 'Antioquia'),
(5360, 'ITAGUI', 'Antioquia'),
(5361, 'ITUANGO', 'Antioquia'),
(5364, 'JARDÍN', 'Antioquia'),
(5368, 'JERICÓ', 'Antioquia'),
(5376, 'LA CEJA', 'Antioquia'),
(5380, 'LA ESTRELLA', 'Antioquia'),
(5390, 'LA PINTADA', 'Antioquia'),
(5400, 'LA UNIÓN', 'Antioquia'),
(5411, 'LIBORINA', 'Antioquia'),
(5425, 'MACEO', 'Antioquia'),
(5440, 'MARINILLA', 'Antioquia'),
(5467, 'MONTEBELLO', 'Antioquia'),
(5475, 'MURINDÓ', 'Antioquia'),
(5480, 'MUTATÁ', 'Antioquia'),
(5483, 'NARIÑO', 'Antioquia'),
(5490, 'NECOCLÍ', 'Antioquia'),
(5495, 'NECHÍ', 'Antioquia'),
(5501, 'OLAYA', 'Antioquia'),
(5541, 'PEÑOL', 'Antioquia'),
(5543, 'PEQUE', 'Antioquia'),
(5576, 'PUEBLORRICO', 'Antioquia'),
(5579, 'PUERTO BERRÍO', 'Antioquia'),
(5585, 'PUERTO NARE', 'Antioquia'),
(5591, 'PUERTO TRIUNFO', 'Antioquia'),
(5604, 'REMEDIOS', 'Antioquia'),
(5607, 'RETIRO', 'Antioquia'),
(5615, 'RIONEGRO', 'Antioquia'),
(5628, 'SABANALARGA', 'Antioquia'),
(5631, 'SABANETA', 'Antioquia'),
(5642, 'SALGAR', 'Antioquia'),
(5647, 'SAN ANDRÉS', 'Antioquia'),
(5649, 'SAN CARLOS', 'Antioquia'),
(5652, 'SAN FRANCISCO', 'Antioquia'),
(5656, 'SAN JERÓNIMO', 'Antioquia'),
(5658, 'SAN JOSÉ DE LA MONTAÑA', 'Antioquia'),
(5659, 'SAN JUAN DE URABÁ', 'Antioquia'),
(5660, 'SAN LUIS', 'Antioquia'),
(5664, 'SAN PEDRO', 'Antioquia'),
(5665, 'SAN PEDRO DE URABA', 'Antioquia'),
(5667, 'SAN RAFAEL', 'Antioquia'),
(5670, 'SAN ROQUE', 'Antioquia'),
(5674, 'SAN VICENTE', 'Antioquia'),
(5679, 'SANTA BÁRBARA', 'Antioquia'),
(5686, 'SANTA ROSA DE OSOS', 'Antioquia'),
(5690, 'SANTO DOMINGO', 'Antioquia'),
(5697, 'EL SANTUARIO', 'Antioquia'),
(5736, 'SEGOVIA', 'Antioquia'),
(5756, 'SONSON', 'Antioquia'),
(5761, 'SOPETRÁN', 'Antioquia'),
(5789, 'TÁMESIS', 'Antioquia'),
(5790, 'TARAZÁ', 'Antioquia'),
(5792, 'TARSO', 'Antioquia'),
(5809, 'TITIRIBÍ', 'Antioquia'),
(5819, 'TOLEDO', 'Antioquia'),
(5837, 'TURBO', 'Antioquia'),
(5842, 'URAMITA', 'Antioquia'),
(5847, 'URRAO', 'Antioquia'),
(5854, 'VALDIVIA', 'Antioquia'),
(5856, 'VALPARAÍSO', 'Antioquia'),
(5858, 'VEGACHÍ', 'Antioquia'),
(5861, 'VENECIA', 'Antioquia'),
(5873, 'VIGÍA DEL FUERTE', 'Antioquia'),
(5885, 'YALÍ', 'Antioquia'),
(5887, 'YARUMAL', 'Antioquia'),
(5890, 'YOLOMBÓ', 'Antioquia'),
(5893, 'YONDÓ', 'Antioquia'),
(5895, 'ZARAGOZA', 'Antioquia'),
(8001, 'BARRANQUILLA', 'Atlántico'),
(8078, 'BARANOA', 'Atlántico'),
(8137, 'CAMPO DE LA CRUZ', 'Atlántico'),
(8141, 'CANDELARIA', 'Atlántico'),
(8296, 'GALAPA', 'Atlántico'),
(8372, 'JUAN DE ACOSTA', 'Atlántico'),
(8421, 'LURUACO', 'Atlántico'),
(8433, 'MALAMBO', 'Atlántico'),
(8436, 'MANATÍ', 'Atlántico'),
(8520, 'PALMAR DE VARELA', 'Atlántico'),
(8549, 'PIOJÓ', 'Atlántico'),
(8558, 'POLONUEVO', 'Atlántico'),
(8560, 'PONEDERA', 'Atlántico'),
(8573, 'PUERTO COLOMBIA', 'Atlántico'),
(8606, 'REPELÓN', 'Atlántico'),
(8634, 'SABANAGRANDE', 'Atlántico'),
(8638, 'SABANALARGA', 'Atlántico'),
(8675, 'SANTA LUCÍA', 'Atlántico'),
(8685, 'SANTO TOMÁS', 'Atlántico'),
(8758, 'SOLEDAD', 'Atlántico'),
(8770, 'SUAN', 'Atlántico'),
(8832, 'TUBARÁ', 'Atlántico'),
(8849, 'USIACURÍ', 'Atlántico'),
(11001, 'BOGOTÁ', 'Bogotá D.C'),
(13001, 'CARTAGENA', 'Bolívar'),
(13006, 'ACHI', 'Bolivar'),
(13030, 'ALTOS DEL ROSARIO', 'Bolívar'),
(13042, 'ARENAL', 'Bolívar'),
(13052, 'ARJONA', 'Bolívar'),
(13062, 'ARROYOHONDO', 'Bolívar'),
(13074, 'BARRANCO DE LOBA', 'Bolívar'),
(13140, 'CALAMAR', 'Bolívar'),
(13160, 'CANTAGALLO', 'Bolívar'),
(13188, 'CICUCO', 'Bolívar'),
(13212, 'CÓRDOBA', 'Bolívar'),
(13222, 'CLEMENCIA', 'Bolívar'),
(13244, 'EL CARMEN DE BOLÍVAR', 'Bolívar'),
(13248, 'EL GUAMO', 'Bolívar'),
(13268, 'EL PEÑÓN', 'Bolívar'),
(13300, 'HATILLO DE LOBA', 'Bolívar'),
(13430, 'MAGANGUÉ', 'Bolívar'),
(13433, 'MAHATES', 'Bolívar'),
(13440, 'MARGARITA', 'Bolívar'),
(13442, 'MARÍA LA BAJA', 'Bolívar'),
(13458, 'MONTECRISTO', 'Bolívar'),
(13468, 'MOMPÓS', 'Bolívar'),
(13473, 'MORALES', 'Bolívar'),
(13549, 'PINILLOS', 'Bolívar'),
(13580, 'REGIDOR', 'Bolívar'),
(13600, 'RÍO VIEJO', 'Bolívar'),
(13620, 'SAN CRISTÓBAL', 'Bolívar'),
(13647, 'SAN ESTANISLAO', 'Bolívar'),
(13650, 'SAN FERNANDO', 'Bolívar'),
(13654, 'SAN JACINTO', 'Bolívar'),
(13655, 'SAN JACINTO DEL CAUCA', 'Bolívar'),
(13657, 'SAN JUAN NEPOMUCENO', 'Bolívar'),
(13667, 'SAN MARTÍN DE LOBA', 'Bolívar'),
(13670, 'SAN PABLO', 'Bolívar'),
(13673, 'SANTA CATALINA', 'Bolívar'),
(13683, 'SANTA ROSA', 'Bolívar'),
(13688, 'SANTA ROSA DEL SUR', 'Bolívar'),
(13744, 'SIMITÍ', 'Bolívar'),
(13760, 'SOPLAVIENTO', 'Bolívar'),
(13780, 'TALAIGUA NUEVO', 'Bolívar'),
(13810, 'TIQUISIO', 'Bolívar'),
(13836, 'TURBACO', 'Bolívar'),
(13838, 'TURBANÁ', 'Bolívar'),
(13873, 'VILLANUEVA', 'Bolívar'),
(13894, 'ZAMBRANO', 'Bolívar'),
(15001, 'TUNJA', 'Boyacá'),
(15022, 'ALMEIDA', 'Boyacá'),
(15047, 'AQUITANIA', 'Boyacá'),
(15051, 'ARCABUCO', 'Boyacá'),
(15087, 'BELÉN', 'Boyacá'),
(15090, 'BERBEO', 'Boyacá'),
(15092, 'BETÉITIVA', 'Boyacá'),
(15097, 'BOAVITA', 'Boyacá'),
(15104, 'BOYACÁ', 'Boyacá'),
(15106, 'BRICEÑO', 'Boyacá'),
(15109, 'BUENAVISTA', 'Boyacá'),
(15114, 'BUSBANZÁ', 'Boyacá'),
(15131, 'CALDAS', 'Boyacá'),
(15135, 'CAMPOHERMOSO', 'Boyacá'),
(15162, 'CERINZA', 'Boyacá'),
(15172, 'CHINAVITA', 'Boyacá'),
(15176, 'CHIQUINQUIRÁ', 'Boyacá'),
(15180, 'CHISCAS', 'Boyacá'),
(15183, 'CHITA', 'Boyacá'),
(15185, 'CHITARAQUE', 'Boyacá'),
(15187, 'CHIVATÁ', 'Boyacá'),
(15189, 'CIÉNEGA', 'Boyacá'),
(15204, 'CÓMBITA', 'Boyacá'),
(15212, 'COPER', 'Boyacá'),
(15215, 'CORRALES', 'Boyacá'),
(15218, 'COVARACHÍA', 'Boyacá'),
(15223, 'CUBARÁ', 'Boyacá'),
(15224, 'CUCAITA', 'Boyacá'),
(15226, 'CUÍTIVA', 'Boyacá'),
(15232, 'CHÍQUIZA', 'Boyacá'),
(15236, 'CHIVOR', 'Boyacá'),
(15238, 'DUITAMA', 'Boyacá'),
(15244, 'EL COCUY', 'Boyacá'),
(15248, 'EL ESPINO', 'Boyacá'),
(15272, 'FIRAVITOBA', 'Boyacá'),
(15276, 'FLORESTA', 'Boyacá'),
(15293, 'GACHANTIVÁ', 'Boyacá'),
(15296, 'GAMEZA', 'Boyacá'),
(15299, 'GARAGOA', 'Boyacá'),
(15317, 'GUACAMAYAS', 'Boyacá'),
(15322, 'GUATEQUE', 'Boyacá'),
(15325, 'GUAYATÁ', 'Boyacá'),
(15332, 'GÜICÁN', 'Boyacá'),
(15362, 'IZA', 'Boyacá'),
(15367, 'JENESANO', 'Boyacá'),
(15368, 'JERICÓ', 'Boyacá'),
(15377, 'LABRANZAGRANDE', 'Boyacá'),
(15380, 'LA CAPILLA', 'Boyacá'),
(15401, 'LA VICTORIA', 'Boyacá'),
(15403, 'LA UVITA', 'Boyacá'),
(15407, 'VILLA DE LEYVA', 'Boyacá'),
(15425, 'MACANAL', 'Boyacá'),
(15442, 'MARIPÍ', 'Boyacá'),
(15455, 'MIRAFLORES', 'Boyacá'),
(15464, 'MONGUA', 'Boyacá'),
(15466, 'MONGUÍ', 'Boyacá'),
(15469, 'MONIQUIRÁ', 'Boyacá'),
(15476, 'MOTAVITA', 'Boyacá'),
(15480, 'MUZO', 'Boyacá'),
(15491, 'NOBSA', 'Boyacá'),
(15494, 'NUEVO COLÓN', 'Boyacá'),
(15500, 'OICATÁ', 'Boyacá'),
(15507, 'OTANCHE', 'Boyacá'),
(15511, 'PACHAVITA', 'Boyacá'),
(15514, 'PÁEZ', 'Boyacá'),
(15516, 'PAIPA', 'Boyacá'),
(15518, 'PAJARITO', 'Boyacá'),
(15522, 'PANQUEBA', 'Boyacá'),
(15531, 'PAUNA', 'Boyacá'),
(15533, 'PAYA', 'Boyacá'),
(15537, 'PAZ DE RÍO', 'Boyacá'),
(15542, 'PESCA', 'Boyacá'),
(15550, 'PISBA', 'Boyacá'),
(15572, 'PUERTO BOYACÁ', 'Boyacá'),
(15580, 'QUÍPAMA', 'Boyacá'),
(15599, 'RAMIRIQUÍ', 'Boyacá'),
(15600, 'RÁQUIRA', 'Boyacá'),
(15621, 'RONDÓN', 'Boyacá'),
(15632, 'SABOYÁ', 'Boyacá'),
(15638, 'SÁCHICA', 'Boyacá'),
(15646, 'SAMACÁ', 'Boyacá'),
(15660, 'SAN EDUARDO', 'Boyacá'),
(15664, 'SAN JOSÉ DE PARE', 'Boyacá'),
(15667, 'SAN LUIS DE GACENO', 'Boyacá'),
(15673, 'SAN MATEO', 'Boyacá'),
(15676, 'SAN MIGUEL DE SEMA', 'Boyacá'),
(15681, 'SAN PABLO DE BORBUR', 'Boyacá'),
(15686, 'SANTANA', 'Boyacá'),
(15690, 'SANTA MARÍA', 'Boyacá'),
(15693, 'SANTA ROSA DE VITERBO', 'Boyacá'),
(15696, 'SANTA SOFÍA', 'Boyacá'),
(15720, 'SATIVANORTE', 'Boyacá'),
(15723, 'SATIVASUR', 'Boyacá'),
(15740, 'SIACHOQUE', 'Boyacá'),
(15753, 'SOATÁ', 'Boyacá'),
(15755, 'SOCOTÁ', 'Boyacá'),
(15757, 'SOCHA', 'Boyacá'),
(15759, 'SOGAMOSO', 'Boyacá'),
(15761, 'SOMONDOCO', 'Boyacá'),
(15762, 'SORA', 'Boyacá'),
(15763, 'SOTAQUIRÁ', 'Boyacá'),
(15764, 'SORACÁ', 'Boyacá'),
(15774, 'SUSACÓN', 'Boyacá'),
(15776, 'SUTAMARCHÁN', 'Boyacá'),
(15778, 'SUTATENZA', 'Boyacá'),
(15790, 'TASCO', 'Boyacá'),
(15798, 'TENZA', 'Boyacá'),
(15804, 'TIBANÁ', 'Boyacá'),
(15806, 'TIBASOSA', 'Boyacá'),
(15808, 'TINJACÁ', 'Boyacá'),
(15810, 'TIPACOQUE', 'Boyacá'),
(15814, 'TOCA', 'Boyacá'),
(15816, 'TOGÜÍ', 'Boyacá'),
(15820, 'TÓPAGA', 'Boyacá'),
(15822, 'TOTA', 'Boyacá'),
(15832, 'TUNUNGUÁ', 'Boyacá'),
(15835, 'TURMEQUÉ', 'Boyacá'),
(15837, 'TUTA', 'Boyacá'),
(15839, 'TUTAZÁ', 'Boyacá'),
(15842, 'UMBITA', 'Boyacá'),
(15861, 'VENTAQUEMADA', 'Boyacá'),
(15879, 'VIRACACHÁ', 'Boyacá'),
(15897, 'ZETAQUIRA', 'Boyacá'),
(17001, 'MANIZALES', 'Caldas'),
(17013, 'AGUADAS', 'Caldas'),
(17042, 'ANSERMA', 'Caldas'),
(17050, 'ARANZAZU', 'Caldas'),
(17088, 'BELALCÁZAR', 'Caldas'),
(17174, 'CHINCHINÁ', 'Caldas'),
(17272, 'FILADELFIA', 'Caldas'),
(17380, 'LA DORADA', 'Caldas'),
(17388, 'LA MERCED', 'Caldas'),
(17433, 'MANZANARES', 'Caldas'),
(17442, 'MARMATO', 'Caldas'),
(17444, 'MARQUETALIA', 'Caldas'),
(17446, 'MARULANDA', 'Caldas'),
(17486, 'NEIRA', 'Caldas'),
(17495, 'NORCASIA', 'Caldas'),
(17513, 'PÁCORA', 'Caldas'),
(17524, 'PALESTINA', 'CALDAS'),
(17541, 'PENSILVANIA', 'Caldas'),
(17614, 'RIOSUCIO', 'Caldas'),
(17616, 'RISARALDA', 'Caldas'),
(17653, 'SALAMINA', 'Caldas'),
(17662, 'SAMANÁ', 'Caldas'),
(17665, 'SAN JOSÉ', 'Caldas'),
(17777, 'SUPÍA', 'Caldas'),
(17867, 'VICTORIA', 'Caldas'),
(17873, 'VILLAMARÍA', 'Caldas'),
(17877, 'VITERBO', 'Caldas'),
(18001, 'FLORENCIA', 'Caquetá'),
(18029, 'ALBANIA', 'Caquetá'),
(18094, 'BELÉN DE LOS ANDAQUIES', 'Caquetá'),
(18150, 'CARTAGENA DEL CHAIRÁ', 'Caquetá'),
(18205, 'CURILLO', 'Caquetá'),
(18247, 'EL DONCELLO', 'Caquetá'),
(18256, 'EL PAUJIL', 'Caquetá'),
(18410, 'LA MONTAÑITA', 'Caquetá'),
(18460, 'MILÁN', 'Caquetá'),
(18479, 'MORELIA', 'Caquetá'),
(18592, 'PUERTO RICO', 'Caquetá'),
(18610, 'SAN JOSÉ DEL FRAGUA', 'Caquetá'),
(18753, 'SAN VICENTE DEL CAGUÁN', 'Caquetá'),
(18756, 'SOLANO', 'Caquetá'),
(18785, 'SOLITA', 'Caquetá'),
(18860, 'VALPARAÍSO', 'Caquetá'),
(19001, 'POPAYÁN', 'Cauca'),
(19022, 'ALMAGUER', 'Cauca'),
(19050, 'ARGELIA', 'Cauca'),
(19075, 'BALBOA', 'Cauca'),
(19100, 'BOLÍVAR', 'Cauca'),
(19110, 'BUENOS AIRES', 'Cauca'),
(19130, 'CAJIBÍO', 'Cauca'),
(19137, 'CALDONO', 'Cauca'),
(19142, 'CALOTO', 'Cauca'),
(19212, 'CORINTO', 'Cauca'),
(19256, 'EL TAMBO', 'Cauca'),
(19290, 'FLORENCIA', 'Cauca'),
(19318, 'GUAPI', 'Cauca'),
(19355, 'INZÁ', 'Cauca'),
(19364, 'JAMBALÓ', 'Cauca'),
(19392, 'LA SIERRA', 'Cauca'),
(19397, 'LA VEGA', 'Cauca'),
(19418, 'LÓPEZ', 'Cauca'),
(19450, 'MERCADERES', 'Cauca'),
(19455, 'MIRANDA', 'Cauca'),
(19473, 'MORALES', 'Cauca'),
(19513, 'PADILLA', 'Cauca'),
(19517, 'PAEZ', 'Cauca'),
(19532, 'PATÍA', 'Cauca'),
(19533, 'PIAMONTE', 'Cauca'),
(19548, 'PIENDAMÓ', 'Cauca'),
(19573, 'PUERTO TEJADA', 'Cauca'),
(19585, 'PURACÉ', 'Cauca'),
(19622, 'ROSAS', 'Cauca'),
(19693, 'SAN SEBASTIÁN', 'Cauca'),
(19698, 'SANTANDER DE QUILICHAO', 'Cauca'),
(19701, 'SANTA ROSA', 'Cauca'),
(19743, 'SILVIA', 'Cauca'),
(19760, 'SOTARA', 'Cauca'),
(19780, 'SUÁREZ', 'Cauca'),
(19785, 'SUCRE', 'Cauca'),
(19807, 'TIMBÍO', 'Cauca'),
(19809, 'TIMBIQUÍ', 'Cauca'),
(19821, 'TORIBIO', 'Cauca'),
(19824, 'TOTORÓ', 'Cauca'),
(19845, 'VILLA RICA', 'Cauca'),
(20001, 'VALLEDUPAR', 'Cesar'),
(20011, 'AGUACHICA', 'Cesar'),
(20013, 'AGUSTÍN CODAZZI', 'Cesar'),
(20032, 'ASTREA', 'Cesar'),
(20045, 'BECERRIL', 'Cesar'),
(20060, 'BOSCONIA', 'Cesar'),
(20175, 'CHIMICHAGUA', 'Cesar'),
(20178, 'CHIRIGUANÁ', 'Cesar'),
(20228, 'CURUMANÍ', 'Cesar'),
(20238, 'EL COPEY', 'Cesar'),
(20250, 'EL PASO', 'Cesar'),
(20295, 'GAMARRA', 'Cesar'),
(20310, 'GONZÁLEZ', 'Cesar'),
(20383, 'LA GLORIA', 'Cesar'),
(20400, 'LA JAGUA DE IBIRICO', 'Cesar'),
(20443, 'MANAURE', 'Cesar'),
(20517, 'PAILITAS', 'Cesar'),
(20550, 'PELAYA', 'Cesar'),
(20570, 'PUEBLO BELLO', 'Cesar'),
(20614, 'RÍO DE ORO', 'Cesar'),
(20621, 'LA PAZ', 'Cesar'),
(20710, 'SAN ALBERTO', 'Cesar'),
(20750, 'SAN DIEGO', 'Cesar'),
(20770, 'SAN MARTÍN', 'Cesar'),
(20787, 'TAMALAMEQUE', 'Cesar'),
(23001, 'MONTERÍA', 'Córdoba'),
(23068, 'AYAPEL', 'Córdoba'),
(23079, 'BUENAVISTA', 'Córdoba'),
(23090, 'CANALETE', 'Córdoba'),
(23162, 'CERETÉ', 'Córdoba'),
(23168, 'CHIMÁ', 'Córdoba'),
(23182, 'CHINÚ', 'Córdoba'),
(23189, 'CIÉNAGA DE ORO', 'Córdoba'),
(23300, 'COTORRA', 'Córdoba'),
(23350, 'LA APARTADA', 'Córdoba'),
(23417, 'LORICA', 'Córdoba'),
(23419, 'LOS CÓRDOBAS', 'Córdoba'),
(23464, 'MOMIL', 'Córdoba'),
(23466, 'MONTELÍBANO', 'Córdoba'),
(23500, 'MOÑITOS', 'Córdoba'),
(23555, 'PLANETA RICA', 'Córdoba'),
(23570, 'PUEBLO NUEVO', 'Córdoba'),
(23574, 'PUERTO ESCONDIDO', 'Córdoba'),
(23580, 'PUERTO LIBERTADOR', 'Córdoba'),
(23586, 'PURÍSIMA', 'Córdoba'),
(23660, 'SAHAGÚN', 'Córdoba'),
(23670, 'SAN ANDRÉS SOTAVENTO', 'Córdoba'),
(23672, 'SAN ANTERO', 'Córdoba'),
(23675, 'SAN BERNARDO DEL VIENTO', 'Córdoba'),
(23678, 'SAN CARLOS', 'Córdoba'),
(23686, 'SAN PELAYO', 'Córdoba'),
(23807, 'TIERRALTA', 'Córdoba'),
(23855, 'VALENCIA', 'Córdoba'),
(25001, 'AGUA DE DIOS', 'Cundinamarca'),
(25019, 'ALBÁN', 'Cundinamarca'),
(25035, 'ANAPOIMA', 'Cundinamarca'),
(25040, 'ANOLAIMA', 'Cundinamarca'),
(25053, 'ARBELÁEZ', 'Cundinamarca'),
(25086, 'BELTRÁN', 'Cundinamarca'),
(25095, 'BITUIMA', 'Cundinamarca'),
(25099, 'BOJACÁ', 'Cundinamarca'),
(25120, 'CABRERA', 'Cundinamarca'),
(25123, 'CACHIPAY', 'Cundinamarca'),
(25126, 'CAJICÁ', 'Cundinamarca'),
(25148, 'CAPARRAPÍ', 'Cundinamarca'),
(25151, 'CAQUEZA', 'Cundinamarca'),
(25154, 'CARMEN DE CARUPA', 'Cundinamarca'),
(25168, 'CHAGUANÍ', 'Cundinamarca'),
(25175, 'CHÍA', 'Cundinamarca'),
(25178, 'CHIPAQUE', 'Cundinamarca'),
(25181, 'CHOACHÍ', 'Cundinamarca'),
(25183, 'CHOCONTÁ', 'Cundinamarca'),
(25200, 'COGUA', 'Cundinamarca'),
(25214, 'COTA', 'Cundinamarca'),
(25224, 'CUCUNUBÁ', 'Cundinamarca'),
(25245, 'EL COLEGIO', 'Cundinamarca'),
(25258, 'EL PEÑÓN', 'Cundinamarca'),
(25260, 'EL ROSAL', 'Cundinamarca'),
(25269, 'FACATATIVÁ', 'Cundinamarca'),
(25279, 'FOMEQUE', 'Cundinamarca'),
(25281, 'FOSCA', 'Cundinamarca'),
(25286, 'FUNZA', 'Cundinamarca'),
(25288, 'FÚQUENE', 'Cundinamarca'),
(25290, 'FUSAGASUGÁ', 'Cundinamarca'),
(25293, 'GACHALA', 'Cundinamarca'),
(25295, 'GACHANCIPÁ', 'Cundinamarca'),
(25297, 'GACHETÁ', 'Cundinamarca'),
(25299, 'GAMA', 'Cundinamarca'),
(25307, 'GIRARDOT', 'Cundinamarca'),
(25312, 'GRANADA', 'Cundinamarca'),
(25317, 'GUACHETÁ', 'Cundinamarca'),
(25320, 'GUADUAS', 'Cundinamarca'),
(25322, 'GUASCA', 'Cundinamarca'),
(25324, 'GUATAQUÍ', 'Cundinamarca'),
(25326, 'GUATAVITA', 'Cundinamarca'),
(25328, 'GUAYABAL DE SIQUIMA', 'Cundinamarca'),
(25335, 'GUAYABETAL', 'Cundinamarca'),
(25339, 'GUTIÉRREZ', 'Cundinamarca'),
(25368, 'JERUSALÉN', 'Cundinamarca'),
(25372, 'JUNÍN', 'Cundinamarca'),
(25377, 'LA CALERA', 'Cundinamarca'),
(25386, 'LA MESA', 'Cundinamarca'),
(25394, 'LA PALMA', 'Cundinamarca'),
(25398, 'LA PEÑA', 'Cundinamarca'),
(25402, 'LA VEGA', 'Cundinamarca'),
(25407, 'LENGUAZAQUE', 'Cundinamarca'),
(25426, 'MACHETA', 'Cundinamarca'),
(25430, 'MADRID', 'Cundinamarca'),
(25436, 'MANTA', 'Cundinamarca'),
(25438, 'MEDINA', 'Cundinamarca'),
(25473, 'MOSQUERA', 'Cundinamarca'),
(25483, 'NARIÑO', 'Cundinamarca'),
(25486, 'NEMOCÓN', 'Cundinamarca'),
(25488, 'NILO', 'Cundinamarca'),
(25489, 'NIMAIMA', 'Cundinamarca'),
(25491, 'NOCAIMA', 'Cundinamarca'),
(25506, 'VENECIA', 'Cundinamarca'),
(25513, 'PACHO', 'Cundinamarca'),
(25518, 'PAIME', 'Cundinamarca'),
(25524, 'PANDI', 'Cundinamarca'),
(25530, 'PARATEBUENO', 'Cundinamarca'),
(25535, 'PASCA', 'Cundinamarca'),
(25572, 'PUERTO SALGAR', 'Cundinamarca'),
(25580, 'PULÍ', 'Cundinamarca'),
(25592, 'QUEBRADANEGRA', 'Cundinamarca'),
(25594, 'QUETAME', 'Cundinamarca'),
(25596, 'QUIPILE', 'Cundinamarca'),
(25599, 'APULO', 'Cundinamarca'),
(25612, 'RICAURTE', 'Cundinamarca'),
(25645, 'SAN ANTONIO DEL TEQUENDAMA', 'Cundinamarca'),
(25649, 'SAN BERNARDO', 'Cundinamarca'),
(25653, 'SAN CAYETANO', 'Cundinamarca'),
(25658, 'SAN FRANCISCO', 'Cundinamarca'),
(25662, 'SAN JUAN DE RÍO SECO', 'Cundinamarca'),
(25718, 'SASAIMA', 'Cundinamarca'),
(25736, 'SESQUILÉ', 'Cundinamarca'),
(25740, 'SIBATÉ', 'Cundinamarca'),
(25743, 'SILVANIA', 'Cundinamarca'),
(25745, 'SIMIJACA', 'Cundinamarca'),
(25754, 'SOACHA', 'Cundinamarca'),
(25758, 'SOPÓ', 'Cundinamarca'),
(25769, 'SUBACHOQUE', 'Cundinamarca'),
(25772, 'SUESCA', 'Cundinamarca'),
(25777, 'SUPATÁ', 'Cundinamarca'),
(25779, 'SUSA', 'Cundinamarca'),
(25781, 'SUTATAUSA', 'Cundinamarca'),
(25785, 'TABIO', 'Cundinamarca'),
(25793, 'TAUSA', 'Cundinamarca'),
(25797, 'TENA', 'Cundinamarca'),
(25799, 'TENJO', 'Cundinamarca'),
(25805, 'TIBACUY', 'Cundinamarca'),
(25807, 'TIBIRITA', 'Cundinamarca'),
(25815, 'TOCAIMA', 'Cundinamarca'),
(25817, 'TOCANCIPÁ', 'Cundinamarca'),
(25823, 'TOPAIPÍ', 'Cundinamarca'),
(25839, 'UBALÁ', 'Cundinamarca'),
(25841, 'UBAQUE', 'Cundinamarca'),
(25843, 'VILLA DE SAN DIEGO DE UBATE', 'Cundinamarca'),
(25845, 'UNE', 'Cundinamarca'),
(25851, 'ÚTICA', 'Cundinamarca'),
(25862, 'VERGARA', 'Cundinamarca'),
(25867, 'VIANÍ', 'Cundinamarca'),
(25871, 'VILLAGÓMEZ', 'Cundinamarca'),
(25873, 'VILLAPINZÓN', 'Cundinamarca'),
(25875, 'VILLETA', 'Cundinamarca'),
(25878, 'VIOTÁ', 'Cundinamarca'),
(25885, 'YACOPÍ', 'Cundinamarca'),
(25898, 'ZIPACÓN', 'Cundinamarca'),
(25899, 'ZIPAQUIRÁ', 'Cundinamarca'),
(27001, 'QUIBDÓ', 'Chocó'),
(27006, 'ACANDÍ', 'Chocó'),
(27025, 'ALTO BAUDO', 'Chocó'),
(27050, 'ATRATO', 'Chocó'),
(27073, 'BAGADÓ', 'Chocó'),
(27075, 'BAHÍA SOLANO', 'Chocó'),
(27077, 'BAJO BAUDÓ', 'Chocó'),
(27086, 'BELÉN DE BAJIRÁ', 'Chocó'),
(27099, 'BOJAYA', 'Chocó'),
(27135, 'EL CANTÓN DEL SAN PABLO', 'Chocó'),
(27150, 'CARMEN DEL DARIEN', 'Chocó'),
(27160, 'CÉRTEGUI', 'Chocó'),
(27205, 'CONDOTO', 'Chocó'),
(27245, 'EL CARMEN DE ATRATO', 'Chocó'),
(27250, 'EL LITORAL DEL SAN JUAN', 'Chocó'),
(27361, 'ISTMINA', 'Chocó'),
(27372, 'JURADÓ', 'Chocó'),
(27413, 'LLORÓ', 'Chocó'),
(27425, 'MEDIO ATRATO', 'Chocó'),
(27430, 'MEDIO BAUDÓ', 'Chocó'),
(27450, 'MEDIO SAN JUAN', 'Chocó'),
(27491, 'NÓVITA', 'Chocó'),
(27495, 'NUQUÍ', 'Chocó'),
(27580, 'RÍO IRO', 'Chocó'),
(27600, 'RÍO QUITO', 'Chocó'),
(27615, 'RIOSUCIO', 'Chocó'),
(27660, 'SAN JOSÉ DEL PALMAR', 'Chocó'),
(27745, 'SIPÍ', 'Chocó'),
(27787, 'TADÓ', 'Chocó'),
(27800, 'UNGUÍA', 'Chocó'),
(27810, 'UNIÓN PANAMERICANA', 'Chocó'),
(41001, 'NEIVA', 'Huila'),
(41006, 'ACEVEDO', 'Huila'),
(41013, 'AGRADO', 'Huila'),
(41016, 'AIPE', 'Huila'),
(41020, 'ALGECIRAS', 'Huila'),
(41026, 'ALTAMIRA', 'Huila'),
(41078, 'BARAYA', 'Huila'),
(41132, 'CAMPOALEGRE', 'Huila'),
(41206, 'COLOMBIA', 'Huila'),
(41244, 'ELÍAS', 'Huila'),
(41298, 'GARZÓN', 'Huila'),
(41306, 'GIGANTE', 'Huila'),
(41319, 'GUADALUPE', 'Huila'),
(41349, 'HOBO', 'Huila'),
(41357, 'IQUIRA', 'Huila'),
(41359, 'ISNOS', 'Huila'),
(41378, 'LA ARGENTINA', 'Huila'),
(41396, 'LA PLATA', 'Huila'),
(41483, 'NÁTAGA', 'Huila'),
(41503, 'OPORAPA', 'Huila'),
(41518, 'PAICOL', 'Huila'),
(41524, 'PALERMO', 'Huila'),
(41530, 'PALESTINA', 'Huila'),
(41548, 'PITAL', 'Huila'),
(41551, 'PITALITO', 'Huila'),
(41615, 'RIVERA', 'Huila'),
(41660, 'SALADOBLANCO', 'Huila'),
(41668, 'SAN AGUSTÍN', 'Huila'),
(41676, 'SANTA MARÍA', 'Huila'),
(41770, 'SUAZA', 'Huila'),
(41791, 'TARQUI', 'Huila'),
(41797, 'TESALIA', 'Huila'),
(41799, 'TELLO', 'Huila'),
(41801, 'TERUEL', 'Huila'),
(41807, 'TIMANÁ', 'Huila'),
(41872, 'VILLAVIEJA', 'Huila'),
(41885, 'YAGUARÁ', 'Huila'),
(44001, 'RIOHACHA', 'La Guajira'),
(44035, 'ALBANIA', 'La Guajira'),
(44078, 'BARRANCAS', 'La Guajira'),
(44090, 'DIBULLA', 'La Guajira'),
(44098, 'DISTRACCIÓN', 'La Guajira'),
(44110, 'EL MOLINO', 'La Guajira'),
(44279, 'FONSECA', 'La Guajira'),
(44378, 'HATONUEVO', 'La Guajira'),
(44420, 'LA JAGUA DEL PILAR', 'La Guajira'),
(44430, 'MAICAO', 'La Guajira'),
(44560, 'MANAURE', 'La Guajira'),
(44650, 'SAN JUAN DEL CESAR', 'La Guajira'),
(44847, 'URIBIA', 'La Guajira'),
(44855, 'URUMITA', 'La Guajira'),
(44874, 'VILLANUEVA', 'La Guajira'),
(47001, 'SANTA MARTA', 'Magdalena'),
(47030, 'ALGARROBO', 'Magdalena'),
(47053, 'ARACATACA', 'Magdalena'),
(47058, 'ARIGUANÍ', 'Magdalena'),
(47161, 'CERRO SAN ANTONIO', 'Magdalena'),
(47170, 'CHIBOLO', 'Magdalena'),
(47189, 'CIÉNAGA', 'Magdalena'),
(47205, 'CONCORDIA', 'Magdalena'),
(47245, 'EL BANCO', 'Magdalena'),
(47258, 'EL PIÑON', 'Magdalena'),
(47268, 'EL RETÉN', 'Magdalena'),
(47288, 'FUNDACIÓN', 'Magdalena'),
(47318, 'GUAMAL', 'Magdalena'),
(47460, 'NUEVA GRANADA', 'Magdalena'),
(47541, 'PEDRAZA', 'Magdalena'),
(47545, 'PIJIÑO DEL CARMEN', 'Magdalena'),
(47551, 'PIVIJAY', 'Magdalena'),
(47555, 'PLATO', 'Magdalena'),
(47570, 'PUEBLOVIEJO', 'Magdalena'),
(47605, 'REMOLINO', 'Magdalena'),
(47660, 'SABANAS DE SAN ANGEL', 'Magdalena'),
(47675, 'SALAMINA', 'Magdalena'),
(47692, 'SAN SEBASTIÁN DE BUENAVISTA', 'Magdalena'),
(47703, 'SAN ZENÓN', 'Magdalena'),
(47707, 'SANTA ANA', 'Magdalena'),
(47720, 'SANTA BÁRBARA DE PINTO', 'Magdalena'),
(47745, 'SITIONUEVO', 'Magdalena'),
(47798, 'TENERIFE', 'Magdalena'),
(47960, 'ZAPAYÁN', 'Magdalena'),
(47980, 'ZONA BANANERA', 'Magdalena'),
(50001, 'VILLAVICENCIO', 'Meta'),
(50006, 'ACACÍAS', 'Meta'),
(50110, 'BARRANCA DE UPÍA', 'Meta'),
(50124, 'CABUYARO', 'Meta'),
(50150, 'CASTILLA LA NUEVA', 'Meta'),
(50223, 'CUBARRAL', 'Meta'),
(50226, 'CUMARAL', 'Meta'),
(50245, 'EL CALVARIO', 'Meta'),
(50251, 'EL CASTILLO', 'Meta'),
(50270, 'EL DORADO', 'Meta'),
(50287, 'FUENTE DE ORO', 'Meta'),
(50313, 'GRANADA', 'Meta'),
(50318, 'GUAMAL', 'Meta'),
(50325, 'MAPIRIPÁN', 'Meta'),
(50330, 'MESETAS', 'Meta'),
(50350, 'LA MACARENA', 'Meta'),
(50370, 'URIBE', 'Meta'),
(50400, 'LEJANÍAS', 'Meta'),
(50450, 'PUERTO CONCORDIA', 'Meta'),
(50568, 'PUERTO GAITÁN', 'Meta'),
(50573, 'PUERTO LÓPEZ', 'Meta'),
(50577, 'PUERTO LLERAS', 'Meta'),
(50590, 'PUERTO RICO', 'Meta'),
(50606, 'RESTREPO', 'Meta'),
(50680, 'SAN CARLOS DE GUAROA', 'Meta'),
(50683, 'SAN JUAN DE ARAMA', 'Meta'),
(50686, 'SAN JUANITO', 'Meta'),
(50689, 'SAN MARTÍN', 'Meta'),
(50711, 'VISTAHERMOSA', 'Meta'),
(52001, 'PASTO', 'Nariño'),
(52019, 'ALBÁN', 'Nariño'),
(52022, 'ALDANA', 'Nariño'),
(52036, 'ANCUYÁ', 'Nariño'),
(52051, 'ARBOLEDA', 'Nariño'),
(52079, 'BARBACOAS', 'Nariño'),
(52083, 'BELÉN', 'Nariño'),
(52110, 'BUESACO', 'Nariño'),
(52203, 'COLÓN', 'Nariño'),
(52207, 'CONSACA', 'Nariño'),
(52210, 'CONTADERO', 'Nariño'),
(52215, 'CÓRDOBA', 'Nariño'),
(52224, 'CUASPUD', 'Nariño'),
(52227, 'CUMBAL', 'Nariño'),
(52233, 'CUMBITARA', 'Nariño'),
(52240, 'CHACHAGÜÍ', 'Nariño'),
(52250, 'EL CHARCO', 'Nariño'),
(52254, 'EL PEÑOL', 'Nariño'),
(52256, 'EL ROSARIO', 'Nariño'),
(52258, 'EL TABLÓN DE GÓMEZ', 'Nariño'),
(52260, 'EL TAMBO', 'Nariño'),
(52287, 'FUNES', 'Nariño'),
(52317, 'GUACHUCAL', 'Nariño'),
(52320, 'GUAITARILLA', 'Nariño'),
(52323, 'GUALMATÁN', 'Nariño'),
(52352, 'ILES', 'Nariño'),
(52354, 'IMUÉS', 'Nariño'),
(52356, 'IPIALES', 'Nariño'),
(52378, 'LA CRUZ', 'Nariño'),
(52381, 'LA FLORIDA', 'Nariño'),
(52385, 'LA LLANADA', 'Nariño'),
(52390, 'LA TOLA', 'Nariño'),
(52399, 'LA UNIÓN', 'Nariño'),
(52405, 'LEIVA', 'Nariño'),
(52411, 'LINARES', 'Nariño'),
(52418, 'LOS ANDES', 'Nariño'),
(52427, 'MAGÜI', 'Nariño'),
(52435, 'MALLAMA', 'Nariño'),
(52473, 'MOSQUERA', 'Nariño'),
(52480, 'NARIÑO', 'Nariño'),
(52490, 'OLAYA HERRERA', 'Nariño'),
(52506, 'OSPINA', 'Nariño'),
(52520, 'FRANCISCO PIZARRO', 'Nariño'),
(52540, 'POLICARPA', 'Nariño'),
(52560, 'POTOSÍ', 'Nariño'),
(52565, 'PROVIDENCIA', 'Nariño'),
(52573, 'PUERRES', 'Nariño'),
(52585, 'PUPIALES', 'Nariño'),
(52612, 'RICAURTE', 'Nariño'),
(52621, 'ROBERTO PAYÁN', 'Nariño'),
(52678, 'SAMANIEGO', 'Nariño'),
(52683, 'SANDONÁ', 'Nariño'),
(52685, 'SAN BERNARDO', 'Nariño'),
(52687, 'SAN LORENZO', 'Nariño'),
(52693, 'SAN PABLO', 'Nariño'),
(52694, 'SAN PEDRO DE CARTAGO', 'Nariño'),
(52696, 'SANTA BÁRBARA', 'Nariño'),
(52699, 'SANTACRUZ', 'Nariño'),
(52720, 'SAPUYES', 'Nariño'),
(52786, 'TAMINANGO', 'Nariño'),
(52788, 'TANGUA', 'Nariño'),
(52835, 'TUMACO', 'Nariño'),
(52838, 'TÚQUERRES', 'Nariño'),
(52885, 'YACUANQUER', 'Nariño'),
(54001, 'CÚCUTA', 'Norte de Santander'),
(54003, 'ABREGO', 'Norte de Santander'),
(54051, 'ARBOLEDAS', 'Norte de Santander'),
(54099, 'BOCHALEMA', 'Norte de Santander'),
(54109, 'BUCARASICA', 'Norte de Santander'),
(54125, 'CÁCOTA', 'Norte de Santander'),
(54128, 'CACHIRÁ', 'Norte de Santander'),
(54172, 'CHINÁCOTA', 'Norte de Santander'),
(54174, 'CHITAGÁ', 'Norte de Santander'),
(54206, 'CONVENCIÓN', 'Norte de Santander'),
(54223, 'CUCUTILLA', 'Norte de Santander'),
(54239, 'DURANIA', 'Norte de Santander'),
(54245, 'EL CARMEN', 'Norte de Santander'),
(54250, 'EL TARRA', 'Norte de Santander'),
(54261, 'EL ZULIA', 'Norte de Santander'),
(54313, 'GRAMALOTE', 'Norte de Santander'),
(54344, 'HACARÍ', 'Norte de Santander'),
(54347, 'HERRÁN', 'Norte de Santander'),
(54377, 'LABATECA', 'Norte de Santander'),
(54385, 'LA ESPERANZA', 'Norte de Santander'),
(54398, 'LA PLAYA', 'Norte de Santander'),
(54405, 'LOS PATIOS', 'Norte de Santander'),
(54418, 'LOURDES', 'Norte de Santander'),
(54480, 'MUTISCUA', 'Norte de Santander'),
(54498, 'OCAÑA', 'Norte de Santander'),
(54518, 'PAMPLONA', 'Norte de Santander'),
(54520, 'PAMPLONITA', 'Norte de Santander'),
(54553, 'PUERTO SANTANDER', 'Norte de Santander'),
(54599, 'RAGONVALIA', 'Norte de Santander'),
(54660, 'SALAZAR', 'Norte de Santander'),
(54670, 'SAN CALIXTO', 'Norte de Santander'),
(54673, 'SAN CAYETANO', 'Norte de Santander'),
(54680, 'SANTIAGO', 'Norte de Santander'),
(54720, 'SARDINATA', 'Norte de Santander'),
(54743, 'SILOS', 'Norte de Santander'),
(54800, 'TEORAMA', 'Norte de Santander'),
(54810, 'TIBÚ', 'Norte de Santander'),
(54820, 'TOLEDO', 'Norte de Santander'),
(54871, 'VILLA CARO', 'Norte de Santander'),
(54874, 'VILLA DEL ROSARIO', 'Norte de Santander'),
(63001, 'ARMENIA', 'Quindio'),
(63111, 'BUENAVISTA', 'Quindio'),
(63130, 'CALARCA', 'Quindio'),
(63190, 'CIRCASIA', 'Quindio'),
(63212, 'CÓRDOBA', 'Quindio'),
(63272, 'FILANDIA', 'Quindio'),
(63302, 'GÉNOVA', 'Quindio'),
(63401, 'LA TEBAIDA', 'Quindio'),
(63470, 'MONTENEGRO', 'Quindio'),
(63548, 'PIJAO', 'Quindio'),
(63594, 'QUIMBAYA', 'Quindio'),
(63690, 'SALENTO', 'Quindio'),
(66001, 'PEREIRA', 'Risaralda'),
(66045, 'APÍA', 'Risaralda'),
(66075, 'BALBOA', 'Risaralda'),
(66088, 'BELÉN DE UMBRÍA', 'Risaralda'),
(66170, 'DOSQUEBRADAS', 'Risaralda'),
(66318, 'GUÁTICA', 'Risaralda'),
(66383, 'LA CELIA', 'Risaralda'),
(66400, 'LA VIRGINIA', 'Risaralda'),
(66440, 'MARSELLA', 'Risaralda'),
(66456, 'MISTRATÓ', 'Risaralda'),
(66572, 'PUEBLO RICO', 'Risaralda'),
(66594, 'QUINCHÍA', 'Risaralda'),
(66682, 'SANTA ROSA DE CABAL', 'Risaralda'),
(66687, 'SANTUARIO', 'Risaralda'),
(68001, 'BUCARAMANGA', 'Santander'),
(68013, 'AGUADA', 'Santander'),
(68020, 'ALBANIA', 'Santander'),
(68051, 'ARATOCA', 'Santander'),
(68077, 'BARBOSA', 'Santander'),
(68079, 'BARICHARA', 'Santander'),
(68081, 'BARRANCABERMEJA', 'Santander'),
(68092, 'BETULIA', 'Santander'),
(68101, 'BOLÍVAR', 'Santander'),
(68121, 'CABRERA', 'Santander'),
(68132, 'CALIFORNIA', 'Santander'),
(68147, 'CAPITANEJO', 'Santander'),
(68152, 'CARCASÍ', 'Santander'),
(68160, 'CEPITÁ', 'Santander'),
(68162, 'CERRITO', 'Santander'),
(68167, 'CHARALÁ', 'Santander'),
(68169, 'CHARTA', 'Santander'),
(68176, 'CHIMA', 'Santander'),
(68179, 'CHIPATÁ', 'Santander'),
(68190, 'CIMITARRA', 'Santander'),
(68207, 'CONCEPCIÓN', 'Santander'),
(68209, 'CONFINES', 'Santander'),
(68211, 'CONTRATACIÓN', 'Santander'),
(68217, 'COROMORO', 'Santander'),
(68229, 'CURITÍ', 'Santander'),
(68235, 'EL CARMEN DE CHUCURÍ', 'Santander'),
(68245, 'EL GUACAMAYO', 'Santander'),
(68250, 'EL PEÑÓN', 'Santander'),
(68255, 'EL PLAYÓN', 'Santander'),
(68264, 'ENCINO', 'Santander'),
(68266, 'ENCISO', 'Santander'),
(68271, 'FLORIÁN', 'Santander'),
(68276, 'FLORIDABLANCA', 'Santander'),
(68296, 'GALÁN', 'Santander'),
(68298, 'GAMBITA', 'Santander'),
(68307, 'GIRÓN', 'Santander'),
(68318, 'GUACA', 'Santander'),
(68320, 'GUADALUPE', 'Santander'),
(68322, 'GUAPOTÁ', 'Santander'),
(68324, 'GUAVATÁ', 'Santander'),
(68327, 'GÜEPSA', 'Santander'),
(68344, 'HATO', 'Santander'),
(68368, 'JESÚS MARÍA', 'Santander'),
(68370, 'JORDÁN', 'Santander'),
(68377, 'LA BELLEZA', 'Santander'),
(68385, 'LANDÁZURI', 'Santander'),
(68397, 'LA PAZ', 'Santander'),
(68406, 'LEBRÍJA', 'Santander'),
(68418, 'LOS SANTOS', 'Santander'),
(68425, 'MACARAVITA', 'Santander'),
(68432, 'MÁLAGA', 'Santander'),
(68444, 'MATANZA', 'Santander'),
(68464, 'MOGOTES', 'Santander'),
(68468, 'MOLAGAVITA', 'Santander'),
(68498, 'OCAMONTE', 'Santander'),
(68500, 'OIBA', 'Santander'),
(68502, 'ONZAGA', 'Santander'),
(68522, 'PALMAR', 'Santander'),
(68524, 'PALMAS DEL SOCORRO', 'Santander'),
(68533, 'PÁRAMO', 'Santander'),
(68547, 'PIEDECUESTA', 'Santander'),
(68549, 'PINCHOTE', 'Santander'),
(68572, 'PUENTE NACIONAL', 'Santander'),
(68573, 'PUERTO PARRA', 'Santander'),
(68575, 'PUERTO WILCHES', 'Santander'),
(68615, 'RIONEGRO', 'Santander'),
(68655, 'SABANA DE TORRES', 'Santander'),
(68669, 'SAN ANDRÉS', 'Santander'),
(68673, 'SAN BENITO', 'Santander'),
(68679, 'SAN GIL', 'Santander'),
(68682, 'SAN JOAQUÍN', 'Santander'),
(68684, 'SAN JOSÉ DE MIRANDA', 'Santander'),
(68686, 'SAN MIGUEL', 'Santander'),
(68689, 'SAN VICENTE DE CHUCURÍ', 'Santander'),
(68705, 'SANTA BÁRBARA', 'Santander'),
(68720, 'SANTA HELENA DEL OPÓN', 'Santander'),
(68745, 'SIMACOTA', 'Santander'),
(68755, 'SOCORRO', 'Santander'),
(68770, 'SUAITA', 'Santander'),
(68773, 'SUCRE', 'Santander'),
(68780, 'SURATÁ', 'Santander'),
(68820, 'TONA', 'Santander'),
(68855, 'VALLE DE SAN JOSÉ', 'Santander'),
(68861, 'VÉLEZ', 'Santander'),
(68867, 'VETAS', 'Santander'),
(68872, 'VILLANUEVA', 'Santander'),
(68895, 'ZAPATOCA', 'Santander'),
(70001, 'SINCELEJO', 'Sucre'),
(70110, 'BUENAVISTA', 'Sucre'),
(70124, 'CAIMITO', 'Sucre'),
(70204, 'COLOSO', 'Sucre'),
(70215, 'COROZAL', 'Sucre'),
(70221, 'COVEÑAS', 'Sucre'),
(70230, 'CHALÁN', 'Sucre'),
(70233, 'EL ROBLE', 'Sucre'),
(70235, 'GALERAS', 'Sucre'),
(70265, 'GUARANDA', 'Sucre'),
(70400, 'LA UNIÓN', 'Sucre'),
(70418, 'LOS PALMITOS', 'Sucre'),
(70429, 'MAJAGUAL', 'Sucre'),
(70473, 'MORROA', 'Sucre'),
(70508, 'OVEJAS', 'Sucre'),
(70523, 'PALMITO', 'Sucre'),
(70670, 'SAMPUÉS', 'Sucre'),
(70678, 'SAN BENITO ABAD', 'Sucre'),
(70702, 'SAN JUAN DE BETULIA', 'Sucre'),
(70708, 'SAN MARCOS', 'Sucre'),
(70713, 'SAN ONOFRE', 'Sucre'),
(70717, 'SAN PEDRO', 'Sucre'),
(70742, 'SINCÉ', 'Sucre'),
(70771, 'SUCRE', 'Sucre'),
(70820, 'SANTIAGO DE TOLÚ', 'Sucre'),
(70823, 'TOLÚ VIEJO', 'Sucre'),
(73001, 'IBAGUÉ', 'Tolima'),
(73024, 'ALPUJARRA', 'Tolima'),
(73026, 'ALVARADO', 'Tolima'),
(73030, 'AMBALEMA', 'Tolima'),
(73043, 'ANZOÁTEGUI', 'Tolima'),
(73055, 'ARMERO', 'Tolima'),
(73067, 'ATACO', 'Tolima'),
(73124, 'CAJAMARCA', 'Tolima'),
(73148, 'CARMEN DE APICALÁ', 'Tolima'),
(73152, 'CASABIANCA', 'Tolima'),
(73168, 'CHAPARRAL', 'Tolima'),
(73200, 'COELLO', 'Tolima'),
(73217, 'COYAIMA', 'Tolima'),
(73226, 'CUNDAY', 'Tolima'),
(73236, 'DOLORES', 'Tolima'),
(73268, 'ESPINAL', 'Tolima'),
(73270, 'FALAN', 'Tolima'),
(73275, 'FLANDES', 'Tolima'),
(73283, 'FRESNO', 'Tolima'),
(73319, 'GUAMO', 'Tolima'),
(73347, 'HERVEO', 'Tolima'),
(73349, 'HONDA', 'Tolima'),
(73352, 'ICONONZO', 'Tolima'),
(73408, 'LÉRIDA', 'Tolima'),
(73411, 'LÍBANO', 'Tolima'),
(73443, 'MARIQUITA', 'Tolima'),
(73449, 'MELGAR', 'Tolima'),
(73461, 'MURILLO', 'Tolima'),
(73483, 'NATAGAIMA', 'Tolima'),
(73504, 'ORTEGA', 'Tolima'),
(73520, 'PALOCABILDO', 'Tolima'),
(73547, 'PIEDRAS', 'Tolima'),
(73555, 'PLANADAS', 'Tolima'),
(73563, 'PRADO', 'Tolima'),
(73585, 'PURIFICACIÓN', 'Tolima'),
(73616, 'RIOBLANCO', 'Tolima'),
(73622, 'RONCESVALLES', 'Tolima'),
(73624, 'ROVIRA', 'Tolima'),
(73671, 'SALDAÑA', 'Tolima'),
(73675, 'SAN ANTONIO', 'Tolima'),
(73678, 'SAN LUIS', 'Tolima'),
(73686, 'SANTA ISABEL', 'Tolima'),
(73770, 'SUÁREZ', 'Tolima'),
(73854, 'VALLE DE SAN JUAN', 'Tolima'),
(73861, 'VENADILLO', 'Tolima'),
(73870, 'VILLAHERMOSA', 'Tolima'),
(73873, 'VILLARRICA', 'Tolima'),
(76001, 'CALI', 'Valle del Cauca'),
(76020, 'ALCALÁ', 'Valle del Cauca'),
(76036, 'ANDALUCÍA', 'Valle del Cauca'),
(76041, 'ANSERMANUEVO', 'Valle del Cauca'),
(76054, 'ARGELIA', 'Valle del Cauca'),
(76100, 'BOLÍVAR', 'Valle del Cauca'),
(76109, 'BUENAVENTURA', 'Valle del Cauca'),
(76111, 'GUADALAJARA DE BUGA', 'Valle del Cauca'),
(76113, 'BUGALAGRANDE', 'Valle del Cauca'),
(76122, 'CAICEDONIA', 'Valle del Cauca'),
(76126, 'CALIMA', 'Valle del Cauca'),
(76130, 'CANDELARIA', 'Valle del Cauca'),
(76147, 'CARTAGO', 'Valle del Cauca'),
(76233, 'DAGUA', 'Valle del Cauca'),
(76243, 'EL ÁGUILA', 'Valle del Cauca'),
(76246, 'EL CAIRO', 'Valle del Cauca'),
(76248, 'EL CERRITO', 'Valle del Cauca'),
(76250, 'EL DOVIO', 'Valle del Cauca'),
(76275, 'FLORIDA', 'Valle del Cauca'),
(76306, 'GINEBRA', 'Valle del Cauca'),
(76318, 'GUACARÍ', 'Valle del Cauca'),
(76364, 'JAMUNDÍ', 'Valle del Cauca'),
(76377, 'LA CUMBRE', 'Valle del Cauca'),
(76400, 'LA UNIÓN', 'Valle del Cauca'),
(76403, 'LA VICTORIA', 'Valle del Cauca'),
(76497, 'OBANDO', 'Valle del Cauca'),
(76520, 'PALMIRA', 'Valle del Cauca'),
(76563, 'PRADERA', 'Valle del Cauca'),
(76606, 'RESTREPO', 'Valle del Cauca'),
(76616, 'RIOFRÍO', 'Valle del Cauca'),
(76622, 'ROLDANILLO', 'Valle del Cauca'),
(76670, 'SAN PEDRO', 'Valle del Cauca'),
(76736, 'SEVILLA', 'Valle del Cauca'),
(76823, 'TORO', 'Valle del Cauca'),
(76828, 'TRUJILLO', 'Valle del Cauca'),
(76834, 'TULUÁ', 'Valle del Cauca'),
(76845, 'ULLOA', 'Valle del Cauca'),
(76863, 'VERSALLES', 'Valle del Cauca'),
(76869, 'VIJES', 'Valle del Cauca'),
(76890, 'YOTOCO', 'Valle del Cauca'),
(76892, 'YUMBO', 'Valle del Cauca'),
(76895, 'ZARZAL', 'Valle del Cauca'),
(81001, 'ARAUCA', 'Arauca'),
(81065, 'ARAUQUITA', 'Arauca'),
(81220, 'CRAVO NORTE', 'Arauca'),
(81300, 'FORTUL', 'Arauca'),
(81591, 'PUERTO RONDÓN', 'Arauca'),
(81736, 'SARAVENA', 'Arauca'),
(81794, 'TAME', 'Arauca'),
(85001, 'YOPAL', 'Casanare'),
(85010, 'AGUAZUL', 'Casanare'),
(85015, 'CHAMEZA', 'Casanare'),
(85125, 'HATO COROZAL', 'Casanare'),
(85136, 'LA SALINA', 'Casanare'),
(85139, 'MANÍ', 'Casanare'),
(85162, 'MONTERREY', 'Casanare'),
(85225, 'NUNCHÍA', 'Casanare'),
(85230, 'OROCUÉ', 'Casanare'),
(85250, 'PAZ DE ARIPORO', 'Casanare'),
(85263, 'PORE', 'Casanare'),
(85279, 'RECETOR', 'Casanare'),
(85300, 'SABANALARGA', 'Casanare'),
(85315, 'SÁCAMA', 'Casanare'),
(85325, 'SAN LUIS DE PALENQUE', 'Casanare'),
(85400, 'TÁMARA', 'Casanare'),
(85410, 'TAURAMENA', 'Casanare'),
(85430, 'TRINIDAD', 'Casanare'),
(85440, 'VILLANUEVA', 'Casanare'),
(86001, 'MOCOA', 'Putumayo'),
(86219, 'COLÓN', 'Putumayo'),
(86320, 'ORITO', 'Putumayo'),
(86568, 'PUERTO ASÍS', 'Putumayo'),
(86569, 'PUERTO CAICEDO', 'Putumayo'),
(86571, 'PUERTO GUZMÁN', 'Putumayo'),
(86573, 'LEGUÍZAMO', 'Putumayo'),
(86749, 'SIBUNDOY', 'Putumayo'),
(86755, 'SAN FRANCISCO', 'Putumayo'),
(86757, 'SAN MIGUEL', 'Putumayo'),
(86760, 'SANTIAGO', 'Putumayo'),
(86865, 'VALLE DEL GUAMUEZ', 'Putumayo'),
(86885, 'VILLAGARZÓN', 'Putumayo'),
(88001, 'SAN ANDRÉS', 'Archipiélago de San Andrés, Providencia y San'),
(88564, 'PROVIDENCIA', 'Archipiélago de San Andrés, Providencia y San'),
(91001, 'LETICIA', 'Amazonas'),
(91263, 'EL ENCANTO', 'Amazonas'),
(91405, 'LA CHORRERA', 'Amazonas'),
(91407, 'LA PEDRERA', 'Amazonas'),
(91430, 'LA VICTORIA', 'Amazonas'),
(91460, 'MIRITI - PARANÁ', 'Amazonas'),
(91530, 'PUERTO ALEGRÍA', 'Amazonas'),
(91536, 'PUERTO ARICA', 'Amazonas'),
(91540, 'PUERTO NARIÑO', 'Amazonas'),
(91669, 'PUERTO SANTANDER', 'Amazonas'),
(91798, 'TARAPACÁ', 'Amazonas'),
(94001, 'INÍRIDA', 'Guainía'),
(94343, 'BARRANCO MINAS', 'Guainía'),
(94663, 'MAPIRIPANA', 'Guainía'),
(94883, 'SAN FELIPE', 'Guainía'),
(94884, 'PUERTO COLOMBIA', 'Guainía'),
(94885, 'LA GUADALUPE', 'Guainía'),
(94886, 'CACAHUAL', 'Guainía'),
(94887, 'PANA PANA', 'Guainía'),
(94888, 'MORICHAL', 'Guainía'),
(95001, 'SAN JOSÉ DEL GUAVIARE', 'Guaviare'),
(95015, 'CALAMAR', 'Guaviare'),
(95025, 'EL RETORNO', 'Guaviare'),
(95200, 'MIRAFLORES', 'Guaviare'),
(97001, 'MITÚ', 'Vaupés'),
(97161, 'CARURU', 'Vaupés'),
(97511, 'PACOA', 'Vaupés'),
(97666, 'TARAIRA', 'Vaupés'),
(97777, 'PAPUNAUA', 'Vaupés'),
(97889, 'YAVARATÉ', 'Vaupés'),
(99001, 'PUERTO CARREÑO', 'Vichada'),
(99524, 'LA PRIMAVERA', 'Vichada'),
(99624, 'SANTA ROSALÍA', 'Vichada'),
(99773, 'CUMARIBO', 'Vichada'),
(106621, 'SAN DIEGO', 'CALDAS'),
(170131, 'ARMA', 'CALDAS'),
(172721, 'SAMARIA', 'CALDAS'),
(174461, 'MONTEBONITO', 'CALDAS'),
(175241, 'ARAUCA', 'CALDAS'),
(175411, 'BOLIVIA', 'CALDAS'),
(175412, 'ARBOLEDA', 'CALDAS'),
(176141, 'SAN LORENZO', 'CALDAS'),
(176161, 'SAN JOSE DE CALDAS', 'CALDAS'),
(176531, 'SAN FELIX', 'CALDAS'),
(176621, 'FLORENCIA', 'CALDAS'),
(999999, 'NO DEFINIDO', 'NO DEFINIDO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_contrato`
--

CREATE TABLE IF NOT EXISTS `sys_contrato` (
  `cod_contrato` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DE LA TABLA AUTOINCREMENTO',
  `nom_contrato` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL CONTRATO',
  `des_contrato` varchar(45) DEFAULT NULL COMMENT 'DESCRIPCION DEL CONTRATO',
  `num_meses_contrato` int(2) DEFAULT NULL COMMENT 'NUMERO DE MESES DE VIGENCIA DEL CONTRATO',
  PRIMARY KEY (`cod_contrato`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS TIPOS DE CONTRATO ENTRE UNA EMPRESA Y MODULER - AUDITORIA' AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `sys_contrato`
--

INSERT INTO `sys_contrato` (`cod_contrato`, `nom_contrato`, `des_contrato`, `num_meses_contrato`) VALUES
(1, 'INDEFINIDO', 'CONTRATO POR TERMINO INDEFINIDO', 3002),
(2, 'TRIMESTRAL', 'CONTRATO POR 3 MESES', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_detframe`
--

CREATE TABLE IF NOT EXISTS `sys_detframe` (
  `cod_detframe` int(8) NOT NULL AUTO_INCREMENT COMMENT 'AUTOINCREMENTO DE LA TABLA PK',
  `cod_tipoinput` int(8) NOT NULL COMMENT 'TIPO DE INPUT',
  `nom_tablaref` varchar(45) NOT NULL COMMENT 'DEPENDIENDO DEL TIPO DE INPUT SE COLOCA LA TABLA DE REFERENCIA QUE LO LLENARA ESTO PARA LOS SELECT',
  `nom_campo` varchar(45) DEFAULT ' ' COMMENT 'NOMBRE DEL CAMPO AL CUAL PERTENCENE EL CAMPO DEL FORMULARIO',
  `holder_campo` varchar(45) NOT NULL DEFAULT ' ',
  `tam_campo` int(2) DEFAULT '7',
  `det_campo` varchar(45) DEFAULT ' ' COMMENT 'DETALLE PARA EL LABEL DEL FORMULARIO',
  `val_campo` varchar(45) DEFAULT ' ' COMMENT 'VALOR DEL CAMPO',
  `cod_frame` int(8) NOT NULL COMMENT 'CODIGO DEL FORMULARIO AL CUAL PERTENECE',
  `cod_estado` varchar(3) NOT NULL COMMENT 'ESTADO DEL INPUT',
  PRIMARY KEY (`cod_detframe`),
  KEY `fk_detfrm_tipinput_idx` (`cod_tipoinput`),
  KEY `fk_detfrm_frame_idx` (`cod_frame`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR EL DETALLE DEL FRAME QUE CONTENDRA LOS INPUTS' AUTO_INCREMENT=451 ;

--
-- Volcado de datos para la tabla `sys_detframe`
--

INSERT INTO `sys_detframe` (`cod_detframe`, `cod_tipoinput`, `nom_tablaref`, `nom_campo`, `holder_campo`, `tam_campo`, `det_campo`, `val_campo`, `cod_frame`, `cod_estado`) VALUES
(1, 10, '', 'Codigo Usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 1, 'FIO'),
(2, 1, '', 'Nombres', ' ', 7, 'nom_usuario', '{nom_usuario}', 1, 'FIV'),
(11, 1, '', 'Apellidos', ' ', 7, 'ape_usuario', '{ape_usuario}', 1, 'FIV'),
(12, 1, '', 'Direccion', ' ', 7, 'dir_usuario', '{dir_usuario}', 1, 'FIV'),
(13, 1, '', 'Telefonos', ' ', 7, 'tel_usuario', '{tel_usuario}', 1, 'FIV'),
(14, 1, '', 'Email', ' ', 7, 'email_usuario', '{email_usuario}', 1, 'FIV'),
(15, 1, '', 'Usuario', ' ', 7, 'usuario_usuario', '{usuario_usuario}', 1, 'FIV'),
(16, 8, '', 'Password', ' ', 7, 'password_usuario', '{password_usuario}', 1, 'FIV'),
(17, 8, '', 'Confirmar Password', ' ', 7, 'no_password_usuario_config', '{password_usuario}', 1, 'FIV'),
(18, 1, '', 'Codigo Empresa', ' ', 7, 'cod_empresa', '{cod_empresa}', 3, 'FIO'),
(19, 1, '', 'Nombre Empresa', ' ', 7, 'nom_empresa', '{nom_empresa}', 3, 'FIV'),
(20, 1, '', 'NIT Empresa', ' ', 7, 'nit_empresa', '{nit_empresa}', 3, 'FIV'),
(21, 1, '', 'Represante Empresa', ' ', 7, 'rep_empresa', '{rep_empresa}', 3, 'FIV'),
(22, 1, '', 'Telefonos Empresa', ' ', 7, 'tel_empresa', '{tel_empresa}', 3, 'FIV'),
(23, 1, '', 'Direccion Empresa', ' ', 7, 'dir_empresa', '{dir_empresa}', 3, 'FIV'),
(24, 9, '', 'Logo Empresa', ' ', 7, 'img_empresa', '{img_empresa}', 4, 'FIV'),
(25, 1, '', 'Web Empresa', ' ', 7, 'web_empresa', '{web_empresa}', 4, 'FIV'),
(26, 1, '', 'Email Empresa', ' ', 7, 'email_empresa', '{email_empresa}', 4, 'FIV'),
(28, 5, 'fa_regimen', 'Regimen', ' ', 7, 'cod_regimen', '{cod_regimen}', 4, 'FIV'),
(29, 5, 'sys_ciudad', 'Ciudad', ' ', 7, 'cod_ciudad', '{cod_ciudad}', 4, 'FIV'),
(34, 5, 'fa_moneda', 'Moneda', ' ', 7, 'cod_moneda', '{cod_moneda}', 4, 'FIV'),
(35, 6, 'sys_empresa', 'Empresas', ' ', 7, 'no_cod_empresa', '{cod_empresa}', 8, 'FIV'),
(36, 5, 'sys_perfil', 'Periles de Usuario', ' ', 7, 'no_cod_perfil', '{cod_perfil}', 2, 'FIV'),
(37, 7, 'sys_menu', 'Menus', ' ', 7, 'no_cod_menu', '{cod_menu}', 5, 'FIV'),
(38, 7, 'sys_menu_sub', 'Sub Menus', ' ', 7, 'no_cod_menu_sub', '{cod_menu_sub}', 5, 'FIV'),
(40, 9, '', 'Imagen Usuario', ' ', 7, 'img_usuario', '{img_usuario}', 1, 'FIV'),
(41, 6, 'sys_usuario', 'Usuarios', ' ', 7, 'cod_usuario', '{cod_usuario}', 9, 'FIV'),
(42, 7, 'sys_menu', 'Menu', ' ', 7, 'cod_menu', '{cod_menu}', 9, 'FIV'),
(43, 10, '', 'cod usuario menu', ' ', 7, 'cod_usuario_menu', '{cod_usuario_menu}', 9, 'FIO'),
(44, 10, '', 'cod usuario menu sub', ' ', 7, 'cod_usuario_menu_sub', '{cod_usuario_menu_sub}', 10, 'FIO'),
(45, 6, 'sys_usuario', 'Usuarios', ' ', 7, 'cod_usuario', '{cod_usuario}', 10, 'FIV'),
(46, 7, 'sys_menu_sub', 'Sub Menu', ' ', 7, 'cod_menu_sub', '{cod_menu_sub}', 10, 'FIV'),
(47, 10, '', 'Empresa Contrato', ' ', 7, 'cod_empresa_contrato', '{cod_empresa_contrato}', 11, 'FIO'),
(48, 5, 'sys_empresa', 'Empresas', ' ', 7, 'cod_empresa', '{cod_empresa}', 11, 'FIV'),
(49, 5, 'sys_contrato', 'Contrato', ' ', 7, 'cod_contrato', '{cod_contrato}', 11, 'FIV'),
(50, 5, 'mod_modulo', 'Modulo', ' ', 7, 'cod_modulo', '{cod_modulo}', 11, 'FIV'),
(51, 11, '', 'Fecha de Inicio', ' ', 7, 'fec_inicio_empresa_contrato', '{fec_inicio_empresa_contrato}', 11, 'FIV'),
(52, 11, '', 'Fecha de Baja', ' ', 7, 'fec_baja_empresa_contrato', '{fec_baja_empresa_contrato}', 11, 'FIV'),
(56, 10, '', 'Cod Usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 12, 'FIO'),
(57, 1, '', 'Nombre Usuario', ' ', 7, 'nom_usuario', '{nom_usuario}', 12, 'FIV'),
(58, 1, '', 'Apellido Usuario', ' ', 7, 'ape_usuario', '{ape_usuario}', 12, 'FIV'),
(59, 1, '', 'Direccion', ' ', 7, 'dir_usuario', '{dir_usuario}', 12, 'FIV'),
(60, 1, '', 'Telefonos', ' ', 7, 'tel_usuario', '{tel_usuario}', 12, 'FIV'),
(61, 1, '', 'Email', ' ', 7, 'email_usuario', '{email_usuario}', 12, 'FIV'),
(62, 1, '', 'Usuario', ' ', 7, 'usuario_usuario', '{usuario_usuario}', 12, 'FIV'),
(63, 8, '', 'Password', ' ', 7, 'password_usuario', '{password_usuario}', 12, 'FIV'),
(64, 8, '', 'Confirmar Password', ' ', 7, 'no_password_usuario_config', '{password_usuario}', 12, 'FIV'),
(65, 10, '', 'Cod Contrato', ' ', 7, 'cod_contrato', '{cod_contrato}', 13, 'FIO'),
(66, 1, '', 'Nombre Contrato', ' ', 7, 'nom_contrato', '{nom_contrato}', 13, 'FIV'),
(67, 2, '', 'Descripcion', ' ', 7, 'des_contrato', '{des_contrato}', 13, 'FIV'),
(68, 14, '', 'Meses Contrato', ' ', 7, 'num_meses_contrato', ' {num_meses_contrato}', 13, 'FIV'),
(69, 10, '', 'Cod  Mensajes', ' ', 7, 'cod_mensajes', '{cod_mensajes}', 14, 'FIO'),
(70, 6, 'sys_usuario', 'Para', ' ', 7, 'a_cod_usuario', '{cod_usuario}', 14, 'FIV'),
(71, 10, '', 'de', ' ', 7, 'de_cod_usuario', '{cod_usuario}', 14, 'FIO'),
(72, 1, '', 'Asunto', ' ', 7, 'asu_mensajes', '{asu_mensajes}', 14, 'FIV'),
(73, 2, '', 'Descripcion', ' ', 7, 'des_mensajes', '{des_mensajes}', 14, 'FIV'),
(74, 9, '', 'Imagen Adjunta', ' ', 7, 'img_mensajes', '{img_mensajes}', 14, 'FIV'),
(75, 10, '', 'cod mensajes', ' ', 7, 'cod_mensajes', '{cod_mensajes}', 14, 'FIO'),
(76, 10, '', 'Codigo Configuracion', ' ', 7, 'cod_config', '{cod_config}', 15, 'FIO'),
(77, 1, '', 'Nombre Configuracion', ' ', 7, 'nom_config', '{nom_config}', 15, 'FIV'),
(78, 2, '', 'Terminos y Condiciones', ' ', 7, 'tyc_config', '{tyc_config}', 15, 'FIV'),
(79, 2, '', 'Notas Factura', ' ', 7, 'not_config', '{not_config}', 15, 'FIV'),
(80, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 16, 'FIV'),
(81, 3, '', 'Aplican Retenciones', ' ', 7, 'ind_retenciones_config', '{ind_retenciones_config}', 16, 'FIV'),
(82, 14, '', '# Recibo ', ' ', 7, 'num_sig_recibocaja', '{num_sig_recibocaja}', 16, 'FIV'),
(83, 14, '', '# Comprobante', ' ', 7, 'num_sig_compago', '{num_sig_compago}', 16, 'FIV'),
(84, 10, '', 'Cod tipoimpuesto', ' ', 7, 'cod_tipoimpuesto', '{cod_tipoimpuesto}', 17, 'FIO'),
(85, 1, '', 'Nombre Impuesto', ' ', 7, 'nom_tipoimpuesto', '{nom_tipoimpuesto}', 17, 'FIV'),
(86, 2, '', 'Descripcion', ' ', 7, 'des_tipoimpuesto', '{des_tipoimpuesto}', 17, 'FIV'),
(87, 10, '', 'cod usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 17, 'FIO'),
(88, 10, '', 'cod usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 16, 'FIO'),
(89, 10, '', 'cod impuesto', ' ', 7, 'cod_impuesto', '{cod_impuesto}', 18, 'FIO'),
(90, 1, '', 'Nombre Impuesto', ' ', 7, 'nom_impuesto', '{nom_impuesto}', 18, 'FIV'),
(91, 15, '', 'Porcentaje del Impuesto', ' ', 7, 'por_impuesto', '{por_impuesto}', 18, 'FIV'),
(92, 2, '', 'Descripcion General', ' ', 7, 'des_impuesto', '{des_impuesto}', 18, 'FIV'),
(93, 5, 'fa_tipoimpuesto', 'Clasificaciones del Impuesto', ' ', 7, 'cod_tipoimpuesto', '{cod_tipoimpuesto}', 18, 'FIV'),
(94, 10, '', 'cod usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 18, 'FIO'),
(95, 10, '', 'cod tipoPago', ' ', 7, 'cod_tipopago', '{cod_tipopago}', 19, 'FIO'),
(96, 1, '', 'Nombre del Tipo de Pago', ' ', 7, 'nom_tipopago', '{nom_tipopago}', 19, 'FIV'),
(97, 14, '', 'Numero de dias Habiles', ' ', 7, 'num_dias_tipopago', '{num_dias_tipopago}', 19, 'FIV'),
(98, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 19, 'FIV'),
(99, 10, '', 'cod unimedida', ' ', 7, 'cod_unimedida', '{cod_unimedida}', 20, 'FIO'),
(100, 1, '', 'Nombre unimedida', ' ', 7, 'nom_unimedida', '{nom_unimedida}', 20, 'FIV'),
(101, 2, '', 'Descripcion', ' ', 7, 'des_unimedida', '{des_unimedida}', 20, 'FIV'),
(102, 1, '', 'Prefijo de Unidad', ' ', 7, 'pre_unimedida', '{pre_unimedida}', 20, 'FIV'),
(103, 10, '', 'cod usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 20, 'FIO'),
(104, 10, '', 'cod moneda', ' ', 7, 'cod_moneda', '{cod_moneda}', 21, 'FIO'),
(105, 1, '', 'Nombre de la Moneda', ' ', 7, 'nom_moneda', '{nom_moneda}', 21, 'FIV'),
(106, 2, '', 'Descripcion de la Moneda', ' ', 7, 'des_moneda', '{des_moneda}', 21, 'FIV'),
(107, 1, '', 'Abreviatura', ' ', 7, 'abr_moneda', '{abr_moneda}', 21, 'FIV'),
(108, 10, '', 'cod numeracion', ' ', 7, 'cod_numeracion', '{cod_numeracion}', 22, 'FIO'),
(109, 1, '', 'Nombre Numeracion', ' ', 7, 'nom_numeracion', '{nom_numeracion}', 22, 'FIV'),
(110, 14, '', 'Numero Inicial', ' ', 7, 'num_inicial_numeracion', '{num_inicial_numeracion}', 22, 'FIV'),
(111, 14, '', 'Numero Final', ' ', 7, 'num_final_numeracion', '{num_final_numeracion}', 22, 'FIV'),
(112, 2, '', 'Resolucion DIAN', ' ', 7, 'res_numeracion', '{res_numeracion}', 22, 'FIV'),
(113, 3, '', 'Numeracion Preferida', ' ', 7, 'ind_preferida_numeracion', '{ind_preferida_numeracion}', 23, 'FIV'),
(114, 3, '', 'Autoincrementable', ' ', 7, 'ind_auto_numeracion', '{ind_auto_numeracion}', 23, 'FIV'),
(115, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 23, 'FIV'),
(116, 1, '', 'Prefijo Numeracion', ' ', 7, 'pre_numeracion', '{pre_numeracion}', 23, 'FIV'),
(119, 10, '', 'cod usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 23, 'FIO'),
(120, 1, '', 'Nombre del Regimen', ' ', 7, 'nom_regimen', '{nom_regimen}', 24, 'FIV'),
(121, 2, '', 'Descripcion', ' ', 7, 'des_regimen', '{des_regimen}', 24, 'FIV'),
(122, 10, '', 'cod regimen', ' ', 7, 'cod_regimen', '{cod_regimen}', 24, 'FIO'),
(123, 10, '', 'cod factura', ' ', 7, 'cod_factura', '{cod_factura}', 26, 'FIO'),
(124, 15, 'lc_categorias_sub', 'Numeracion', ' ', 2, 'cod_numeracion', '{cod_numeracion}', 26, 'FIV'),
(125, 1, '', 'Numero Factura', ' ', 7, 'num_factura', '{num_factura}', 26, 'FIV'),
(126, 5, 'fa_cliente', 'Cliente', ' ', 7, 'cod_cliente', '{cod_cliente}', 26, 'FIV'),
(127, 11, '', 'Fecha Registro', ' ', 7, 'fec_alta_factura', '{fec_alta_factura}', 26, 'FIV'),
(128, 11, '', 'Fecha Vencimiento', ' ', 7, 'fec_vencimiento_factura', '{fec_vencimiento_factura}', 26, 'FIV'),
(129, 5, 'fa_tipopago', 'Plazo', ' ', 7, 'cod_tipopago', '{cod_tipopago}', 26, 'FIV'),
(130, 2, '', 'Observaciones', ' ', 7, 'obs_factura', '{obs_factura}', 27, 'FIV'),
(131, 2, '', 'Notas', ' ', 7, 'not_factura', '{not_factura}', 27, 'FIV'),
(132, 3, '', 'Recurrente', ' ', 7, 'ind_recurrente_factura', '{ind_recurrente_factura}', 27, 'FIV'),
(133, 5, 'fa_item', 'Item', 'Item', 2, 'no_cod_item', '{cod_item}', 28, 'FIV'),
(134, 1, '', 'Referencia', 'Referencia', 1, 'no_ref_item', '{ref_item}', 28, 'FIV'),
(135, 1, '', 'Valor', 'Importe', 2, 'no_imp', '{imp}', 28, 'FIV'),
(136, 5, 'fa_descuentos', 'Descuento', '% Des', 2, 'no_cod_descuento', '{cod_descuento}', 28, 'FIV'),
(137, 5, 'fa_impuesto', 'Impuesto', 'Impuesto', 2, 'no_cod_impuesto', '{cod_impuesto}', 28, 'FIV'),
(138, 14, '', 'Cantidad', 'Cantidad', 1, 'no_can_detalle', '{can_detalle}', 28, 'FIV'),
(139, 10, '', 'cod item', ' ', 7, 'cod_item', '{cod_item}', 30, 'FIO'),
(140, 1, '', 'Nombre', ' ', 7, 'nom_item', '{nom_item}', 30, 'FIV'),
(141, 1, '', 'Referencia', ' ', 7, 'ref_item', '{ref_item}', 30, 'FIV'),
(142, 2, '', 'Descripcion', ' ', 7, 'des_item', '{des_item}', 30, 'FIV'),
(143, 1, '', 'Importe Compra', ' ', 7, 'imp_compra_item', '{imp_compra_item}', 30, 'FIV'),
(144, 15, '', 'Incremento', ' ', 4, 'inc_porcen_item', '{inc_porcen_item}', 30, 'FIV'),
(145, 3, '', 'Item Inventariable', ' ', 4, 'ind_inventario_item', '{ind_inventario_item}', 30, 'FIV'),
(146, 5, 'fa_impuesto', 'Impuesto', ' ', 7, 'cod_impuesto', '{cod_impuesto}', 30, 'FIV'),
(147, 5, 'con_cuenta', 'Categoria', ' ', 7, 'cod_cuenta', '{cod_cuenta}', 30, 'FIV'),
(148, 10, '', 'cod usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 30, 'FIO'),
(160, 10, '', 'cod inventario', ' ', 7, 'cod_inventario', '{cod_inventario}', 31, 'FIO'),
(161, 5, 'fa_item_imventario', 'Item', ' ', 7, 'cod_item', '{cod_item}', 31, 'FIV'),
(162, 5, 'fa_unimedida', 'Unidad Medida', ' ', 7, 'cod_unimedida', '{cod_unimedida}', 31, 'FIV'),
(163, 14, '', 'Cantidad Inicial', ' ', 7, 'can_ini_inventario', '{can_ini_inventario}', 31, 'FIV'),
(164, 1, '', 'Importe x Uni', ' ', 7, 'imp_uni_inventario', '{imp_uni_inventario}', 31, 'FIV'),
(165, 10, '', 'cod cliente', ' ', 7, 'cod_cliente', '{cod_cliente}', 32, 'FIO'),
(166, 1, '', 'Nombre Cliente', ' ', 7, 'nom_cliente', '{nom_cliente}', 32, 'FIV'),
(167, 1, '', 'Nit', ' ', 7, 'nit_cliente', '{nit_cliente}', 32, 'FIV'),
(168, 1, '', 'Direccion', ' ', 7, 'dir_cliente', '{dir_cliente}', 32, 'FIV'),
(169, 1, '', 'Email', ' ', 7, 'email_cliente', '{email_cliente}', 32, 'FIV'),
(170, 1, '', 'Telefono', ' ', 7, 'tel_cliente', '{tel_cliente}', 32, 'FIV'),
(171, 1, '', 'Telefono Aux', ' ', 7, 'tel1_cliente', '{tel1_cliente}', 32, 'FIV'),
(172, 1, '', 'Celular', ' ', 7, 'cel_cliente', '{cel_cliente}', 32, 'FIV'),
(173, 1, '', 'Fax', ' ', 7, 'fax_cliente', '{fax_cliente}', 33, 'FIV'),
(174, 3, '', 'Cliente', ' ', 7, 'ind_cliente_cliente', '{ind_cliente_cliente}', 33, 'FIV'),
(175, 3, '', 'Proveedor', ' ', 7, 'ind_proveedor_cliente', '{ind_proveedor_cliente}', 33, 'FIV'),
(176, 2, '', 'Observaciones', ' ', 7, 'obs_cliente', '{obs_cliente}', 33, 'FIV'),
(177, 5, 'sys_ciudad', 'Ciudad', ' ', 7, 'cod_ciudad', '{cod_ciudad}', 33, 'FIV'),
(178, 5, 'fa_tipopago', 'Tipo Pago', ' ', 7, 'cod_tipopago', '{cod_tipopago}', 33, 'FIV'),
(179, 1, '', 'Nombre', 'Nombre', 3, 'no_nom_cliente_asociado', '{nom_cliente_asociado}', 34, 'FIV'),
(180, 1, '', 'Email', 'Email', 3, 'no_email_cliente_asociado', '{email_cliente_asociado}', 34, 'FIV'),
(181, 1, '', 'Telefono', 'Telefono', 3, 'no_tel_cliente_asociado', '{tel_cliente_asociado}', 34, 'FIV'),
(182, 1, '', 'Celular', 'Celular', 2, 'no_cel_cliente_asociado', '{cel_cliente_asociado}', 34, 'FIV'),
(183, 1, '', 'Codigo', 'Codigo del Estado', 7, 'cod_estado', '{cod_estado}', 35, 'FIV'),
(184, 2, '', 'Descripcion', ' ', 7, 'des_estado', '{des_estado}', 35, 'FIV'),
(185, 5, 'mod_modulo', 'Modulo', ' ', 7, 'mod_estado', '{mod_estado}', 35, 'FIV'),
(186, 3, '', 'Cotizacion', ' ', 7, 'ind_cotizacion', '{ind_cotizacion}', 27, 'FIV'),
(187, 5, 'sys_usuario', 'Asignar A:', ' ', 7, 'no_cod_usuario', '{cod_usuario}', 27, 'FIV'),
(189, 10, '', 'total', ' ', 7, 'imp_adeudado', '{imp_adeudado}', 26, 'FIO'),
(190, 10, '', 'cod usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 26, 'FIO'),
(191, 10, '', 'cod_descuento', ' ', 7, 'cod_descuentos', '{cod_descuentos}', 36, 'FIO'),
(192, 1, '', 'Descuento', ' ', 7, 'nom_descuento', '{nom_descuento}', 36, 'FIV'),
(193, 2, '', 'Descripcion', ' ', 7, 'des_descuento', '{des_descuento}', 36, 'FIV'),
(194, 15, '', 'Porcentaje', '', 4, 'prc_descuento', '{prc_descuento}', 36, 'FIV'),
(195, 11, '', 'Fecha Inicio', ' ', 4, 'fec_inicio_descuento', '{fec_inicio_descuento}', 36, 'FIV'),
(196, 11, '', 'Fecha fin', ' ', 4, 'fec_cierre_descuento', '{fec_cierre_descuento}', 36, 'FIV'),
(197, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 36, 'FIV'),
(198, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 36, 'FIO'),
(199, 10, '', 'cod_restaurante', ' ', 7, 'cod_restaurantes', '{cod_restaurantes}', 37, 'FIO'),
(200, 1, '', 'Nombre Restaurante', ' ', 7, 'nom_restaurantes', '{nom_restaurantes}', 37, 'FIV'),
(201, 2, '', 'Descripcion Restaurante', ' ', 7, 'des_restaurantes', '{des_restaurantes}', 37, 'FIV'),
(202, 2, '', 'Eslogan', ' ', 7, 'esl_restaurantes', '{esl_restaurantes}', 37, 'FIV'),
(203, 1, '', 'Direccion - Ubicacion', ' ', 7, 'dir_restaurantes', '{dir_restaurantes}', 37, 'FIV'),
(204, 1, '', 'Telefonos', ' ', 7, 'tel_restaurantes', '{tel_restaurantes}', 37, 'FIV'),
(205, 1, '', 'Email ', ' ', 7, 'email_restaurantes', '{email_restaurantes}', 37, 'FIV'),
(213, 1, '', 'Facebook', ' ', 7, 'fb_restaurantes', '{fb_restaurantes}', 38, 'FIV'),
(214, 1, '', 'Twitter', ' ', 7, 'tw_restaurantes', '{tw_restaurantes}', 38, 'FIV'),
(215, 1, '', 'Coordenadas Map X', ' ', 4, 'cx_restaurantes', '{cx_restaurantes}', 38, 'FIV'),
(216, 1, '', 'Coordenadas Map Y', ' ', 4, 'cy_restaurantes', '{cy_restaurantes}', 38, 'FIV'),
(217, 5, 'sys_ciudad', 'Ciudad', ' ', 7, 'cod_ciudad', '{cod_ciudad}', 38, 'FIV'),
(218, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 38, 'FIV'),
(219, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 38, 'FIO'),
(220, 10, '', 'cod_restaurantes_img', ' ', 7, 'cod_restaurantes_img', '{cod_restaurantes_img}', 39, 'FIO'),
(221, 1, '', 'Nombre de la Iamgen', ' ', 7, 'nom_imagen', '{nom_imagen}', 39, 'FIV'),
(222, 2, '', 'Descripcion', ' ', 7, 'des_imagen', '{des_imagen}', 39, 'FIV'),
(223, 9, '', 'Imagen', ' ', 7, 'img_restaurantes_img', '{img_restaurantes_img}', 39, 'FIV'),
(224, 5, 'lc_restaurantes', 'Restaurante', ' ', 7, 'cod_restaurantes', '{cod_restaurantes}', 39, 'FIV'),
(225, 3, '', 'Img app', ' ', 7, 'ind_app', '{ind_app}', 40, 'FIV'),
(226, 3, '', 'Img Header', ' ', 7, 'ind_header', '{ind_header}', 40, 'FIV'),
(227, 3, '', 'Img Footer', ' ', 7, 'ind_footer', '{ind_footer}', 40, 'FIV'),
(228, 3, '', 'Img Publicidad', ' ', 7, 'ind_publicidad', '{ind_publicidad}', 40, 'FIV'),
(229, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 40, 'FIV'),
(230, 1, '', 'Codigo Ciudad', ' ', 4, 'cod_ciudad', '{cod_ciudad}', 41, 'FIV'),
(231, 1, '', 'Nombre Ciudad', ' ', 4, 'nom_ciudad', '{nom_ciudad}', 41, 'FIV'),
(232, 1, '', 'Dpto Ciudad', ' ', 4, 'dpt_ciudad', '{dpt_ciudad}', 41, 'FIV'),
(233, 10, '', 'cod_perfil', ' ', 7, 'cod_perfil', '{cod_perfil}', 42, 'FIO'),
(234, 1, '', 'Nombre Perfil', ' ', 7, 'nom_perfil', '{nom_perfil}', 42, 'FIV'),
(235, 2, '', 'Descripcion', ' ', 7, 'des_perfil', '{des_perfil}', 42, 'FIV'),
(236, 10, '', 'cod_metodos', ' ', 7, 'cod_metodos', '{cod_metodos}', 43, 'FIO'),
(237, 1, '', 'Nombre del Metodo', ' ', 7, 'nom_metodos', '{nom_metodos}', 43, 'FIV'),
(238, 2, '', 'Descripcion', ' ', 7, 'des_metodos', '{des_metodos}', 43, 'FIV'),
(239, 1, '', 'Nombre Boton', '(Guardar,Cancelar,Eliminar)', 7, 'btn_metodos', '{btn_metodos}', 43, 'FIV'),
(240, 1, '', 'Icono', ' ', 7, 'ico_metodos', '{ico_metodos}', 43, 'FIV'),
(241, 1, '', 'Tipo de Metodo', '(Submit,Link,Reset,button)', 7, 'tip_metodos', '{tip_metodos}', 44, 'FIV'),
(242, 1, '', 'Clase/Color', ' ', 7, 'cla_metodos', '{cla_metodos}', 44, 'FIV'),
(243, 1, '', 'Href/Url', ' ', 7, 'href_metodos', '{href_metodos}', 44, 'FIV'),
(244, 10, '', 'cod_perfil_metodos', ' ', 7, 'cod_perfil_metodos', '{cod_perfil_metodos}', 45, 'FIO'),
(245, 5, 'sys_perfil', 'Periles de Usuario', ' ', 7, 'cod_perfil', '{cod_perfil}', 45, 'FIV'),
(246, 5, 'sys_metodos', 'Metodos del Sistema', ' ', 7, 'cod_metodos', '{cod_metodos}', 45, 'FIV'),
(247, 5, 'mod_modulo', 'Modulos Activos', ' ', 7, 'cod_modulo', '{cod_modulo}', 45, 'FIV'),
(248, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 45, 'FIV'),
(249, 1, '', 'Url del metodo', ' ', 7, 'uri_perfil_metodos', '{uri_perfil_metodos}', 45, 'FIV'),
(250, 1, '', 'Metodo Interno', ' ', 7, 'met_perfil_metodos', '{met_perfil_metodos}', 45, 'FIV'),
(251, 10, '', 'cod_formulario', ' ', 7, 'cod_formulario', '{cod_formulario}', 46, 'FIO'),
(252, 1, '', 'Nombre Fomulario', ' ', 7, 'nom_formulario', '{nom_formulario}', 46, 'FIV'),
(253, 1, '', 'Nombre del Esquema', ' ', 7, 'nom_tabla', '{nom_tabla}', 46, 'FIV'),
(254, 3, '', 'Formulario en Linea', ' ', 7, 'tip_formulario', '{tip_formulario}', 46, 'FIV'),
(255, 3, '', 'Trae Datos', ' ', 7, 'dat_formulario', '{dat_formulario}', 46, 'FIV'),
(256, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 46, 'FIO'),
(257, 10, '', 'cod_formulario_metodos', ' ', 7, 'cod_formulario_metodos', '{cod_formulario_metodos}', 47, 'FIO'),
(258, 5, 'sys_formulario', 'Formulario', ' ', 7, 'cod_formulario', '{cod_formulario}', 47, 'FIV'),
(259, 5, 'sys_metodos', 'Metodos', ' ', 7, 'cod_metodos', '{cod_metodos}', 47, 'FIV'),
(260, 10, '', 'cod_frame', ' ', 7, 'cod_frame', '{cod_frame}', 48, 'FIO'),
(261, 1, '', 'Nombre Frame', ' ', 7, 'nom_frame', '{nom_frame}', 48, 'FIV'),
(262, 1, '', 'Esquema', ' ', 7, 'nom_tabla', '{nom_tabla}', 48, 'FIV'),
(263, 1, '', 'Tamano', '(1,2,3,4-12)', 7, 'id_frame', '{id_frame}', 48, 'FIV'),
(264, 5, 'sys_formulario', 'Formulario', ' ', 7, 'cod_formulario', '{cod_formulario}', 48, 'FIV'),
(265, 3, '', 'Lineal', ' ', 7, 'ind_enlinea', '{ind_enlinea}', 48, 'FIV'),
(266, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 48, 'FIO'),
(267, 10, '', 'cod_detframe', ' ', 0, 'no_cod_detframe', '{cod_detframe}', 49, 'FIO'),
(268, 5, 'sys_tipoinput', 'Tipo Input', ' Tipo de Input', 2, 'no_cod_tipoinput', '{cod_tipoinput}', 49, 'FIV'),
(269, 5, 'sys_tablareferencia', 'Tabla Referencia', ' Taba Referencia', 1, 'no_nom_tablaref', '{nom_tablaref}', 49, 'FIV'),
(270, 1, '', 'Nombre Campo', 'Nombre del label', 2, 'no_nom_campo', '{nom_campo}', 49, 'FIV'),
(271, 1, '', 'Holder Campo', 'Enunciado del input', 1, 'no_holder_campo', '{holder_campo}', 49, 'FIV'),
(272, 1, '', 'Tamano Input', '(1min-12max)', 1, 'no_tam_campo', '{tam_campo}', 49, 'FIV'),
(273, 1, '', 'Detalle del campo', '(columna de BD)', 1, 'no_det_campo', '{det_campo}', 49, 'FIV'),
(274, 1, '', 'Valor del campo', '({columna BD})', 1, 'no_val_campo', '{val_campo}', 49, 'FIV'),
(275, 5, 'sys_frame', 'Frame Padre', ' ', 2, 'no_cod_frame', '{cod_frame}', 49, 'FIV'),
(276, 5, 'sys_estado', 'Estado', ' ', 1, 'no_cod_estado', '{cod_estado}', 49, 'FIV'),
(281, 10, '', 'cod_entrantes', ' ', 7, 'cod_carta', '{cod_carta}', 50, 'FIO'),
(282, 1, '', 'Nombre', ' ', 7, 'nom_entrantes', '{nom_entrantes}', 50, 'FIV'),
(283, 2, '', 'Descripcion', ' ', 7, 'des_entrantes', '{des_entrantes}', 50, 'FIV'),
(284, 9, '', 'Imagen', ' ', 7, 'img_carta', '{img_carta}', 50, 'FIV'),
(286, 10, '', 'cod_naturaleza', ' ', 7, 'cod_naturaleza', '{cod_naturaleza}', 52, 'FIO'),
(287, 1, '', 'Nombre', ' ', 7, 'nom_naturaleza', '{nom_naturaleza}', 52, 'FIV'),
(288, 2, '', 'Descripcion', ' ', 7, 'des_naturaleza', '{des_naturaleza}', 52, 'FIV'),
(289, 5, 'lc_restaurantes', 'Restaurantes', ' ', 7, 'cod_restaurantes', '{cod_restaurantes}', 51, 'FIV'),
(290, 3, '', 'Entrante', ' ', 7, 'ind_entrante', '{ind_entrante}', 51, 'FIV'),
(291, 3, '', 'Plato', ' ', 7, 'ind_plato', '{ind_plato}', 51, 'FIV'),
(292, 3, '', 'Postre', ' ', 7, 'ind_postre', '{ind_postre}', 51, 'FIV'),
(293, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 51, 'FIV'),
(294, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 51, 'FIO'),
(295, 10, '', 'cod_categorias', ' ', 7, 'cod_categorias', '{cod_categorias}', 53, 'FIO'),
(296, 1, '', 'Nombre', ' ', 7, 'nom_categorias', '{nom_categorias}', 53, 'FIV'),
(297, 2, '', 'Descripcion', ' ', 7, 'des_categorias', '{des_categorias}', 53, 'FIV'),
(298, 9, '', 'Imagen', ' ', 7, 'img_categorias', '{img_categorias}', 53, 'FIV'),
(299, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 53, 'FIO'),
(300, 10, '', 'cod_categorias_sub', ' ', 7, 'cod_categorias_sub', '{cod_categorias_sub}', 54, 'FIO'),
(301, 1, '', 'Nombre', ' ', 7, 'nom_categorias_sub', '{nom_categorias_sub}', 54, 'FIV'),
(302, 2, '', 'Descripcion', ' ', 7, 'des_categorias_sub', '{des_categorias_sub}', 54, 'FIV'),
(303, 9, '', 'Imagen', ' ', 7, 'img_categorias_sub', '{img_categorias_sub}', 54, 'FIV'),
(304, 5, 'lc_categorias', 'Categorias', ' ', 7, 'cod_categorias', '{cod_categorias}', 54, 'FIV'),
(305, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 54, 'FIO'),
(306, 1, '', 'Titulo de la ayuda', '', 7, 'tit_formulario', '{tit_formulario}', 46, 'FIV'),
(307, 2, '', 'Descripcion de la ayuda', '', 0, 'des_formulario', '{des_formulario}', 46, 'FIV'),
(308, 3, '', 'Activar Ayuda', '', 0, 'ind_ayuda', '{ind_ayuda}', 1, 'FIV'),
(309, 10, '', 'cod_restaurantes_categoria_sub', ' ', 7, 'cod_restaurantes_categoria_sub', '{cod_restaurantes_categoria_sub}', 90, 'FIO'),
(310, 5, 'lc_restaurantes', 'Restaurante', ' ', 7, 'cod_restaurantes', '{cod_restaurantes}', 90, 'FIV'),
(311, 5, 'lc_categorias_sub', 'Sub Categorias', ' ', 7, 'cod_categorias_sub', '{cod_categorias_sub}', 90, 'FIV'),
(312, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 11, 'FIO'),
(313, 10, '', 'cod_cliente', ' ', 7, 'cod_cliente', '{cod_cliente}', 91, 'FIO'),
(314, 1, '', 'Nombre', ' ', 7, 'nom_cliente', '{nom_cliente}', 91, 'FIV'),
(315, 1, '', 'Apellido', ' ', 7, 'ape_cliente', '{ape_cliente}', 91, 'FIV'),
(316, 1, '', 'Email', ' ', 7, 'email_cliente', '{email_cliente}', 91, 'FIV'),
(317, 8, '', 'Password', ' ', 7, 'password_cliente', '{password_cliente}', 91, 'FIV'),
(318, 1, '', 'Ciudad', ' ', 7, 'ciu_cliente', '{ciu_cliente}', 91, 'FIV'),
(319, 1, '', 'Direccion', ' ', 7, 'dir_cliente', '{dir_cliente}', 91, 'FIV'),
(320, 1, '', 'Telefono', ' ', 7, 'tel_cliente', '{tel_cliente}', 92, 'FIV'),
(321, 1, '', 'Genero', ' ', 7, 'gen_cliente', '{gen_cliente}', 92, 'FIV'),
(322, 3, '', 'Comunicados', ' ', 7, 'ind_comunicados', '{ind_comunicados}', 92, 'FIV'),
(323, 3, '', 'Ofertas', ' ', 7, 'ind_ofertas', '{ind_ofertas}', 92, 'FIV'),
(324, 3, '', 'Politica', ' ', 7, 'ind_politica', '{ind_politica}', 92, 'FIV'),
(325, 10, '', 'cod_comentario', ' ', 7, 'cod_comentario', '{cod_comentario}', 93, 'FIO'),
(326, 2, '', 'Descripcion', ' ', 7, 'des_comentario', '{des_comentario}', 93, 'FIV'),
(327, 1, '', 'Calificacion', ' ', 7, 'cal_comentario', '{cal_comentario}', 93, 'FIV'),
(328, 5, 'lc_cliente', 'Cliente', ' ', 7, 'cod_cliente', '{cod_cliente}', 93, 'FIV'),
(329, 5, 'lc_restaurantes', 'Restaurante', ' ', 7, 'cod_restaurantes', '{cod_restaurantes}', 93, 'FIV'),
(330, 10, '', 'cod_palabrasclave', ' ', 7, 'cod_palabrasclave', '{cod_palabrasclave}', 94, 'FIO'),
(331, 2, '', 'Palabras Clave', ' ', 7, 'des_palabrasclave', '{des_palabrasclave}', 94, 'FIV'),
(332, 5, 'lc_restaurantes', 'Restaurante', ' ', 7, 'cod_restaurantes', '{cod_restaurantes}', 94, 'FIV'),
(333, 5, 'sys_empresa', 'Empresa Facturadora', 'Empresa que factura', 7, 'cod_empresa', '{cod_empres}', 27, 'FIV'),
(334, 10, '', 'sub total', ' ', 7, 'sub_total_factura', '{sub_total_factura}', 27, 'FIO'),
(335, 10, '', 'sub total descuento', ' ', 7, 'sub_totaldes_factura', '{sub_totaldes_factura}', 27, 'FIO'),
(336, 10, '', 'importe', ' ', 7, 'imp_factura', '{imp_factura}', 27, 'FIO'),
(337, 5, 'sys_empresa', 'Empresa Relacionada', 'Seleccione empresa', 7, 'cod_empresa', '{cod_empresa}', 32, 'FIV'),
(339, 10, '', 'cod factura', ' ', 7, 'cod_factura', '{cod_factura}', 95, 'FIO'),
(340, 15, 'lc_categorias_sub', 'Numeracion', ' ', 2, 'cod_numeracion', '{cod_numeracion}', 95, 'FIV'),
(341, 1, '', 'Numero Factura', ' ', 7, 'num_factura', '{num_factura}', 95, 'FIV'),
(342, 5, 'fa_cliente', 'Cliente', ' ', 7, 'cod_cliente', '{cod_cliente}', 95, 'FIV'),
(343, 11, '', 'Fecha Registro', ' ', 7, 'fec_alta_factura', '{fec_alta_factura}', 95, 'FIV'),
(344, 11, '', 'Fecha Vencimiento', ' ', 7, 'fec_vencimiento_factura', '{fec_vencimiento_factura}', 95, 'FIV'),
(345, 5, 'fa_tipopago', 'Plazo', ' ', 7, 'cod_tipopago', '{cod_tipopago}', 95, 'FIV'),
(346, 10, '', 'total', ' ', 7, 'imp_adeudado', '{imp_adeudado}', 95, 'FIO'),
(347, 10, '', 'cod usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 95, 'FIO'),
(348, 2, '', 'Observaciones', ' ', 7, 'obs_factura', '{obs_factura}', 96, 'FIV'),
(349, 2, '', 'Notas', ' ', 7, 'not_factura', '{not_factura}', 96, 'FIV'),
(350, 3, '', 'Recurrente', ' ', 7, 'ind_recurrente_factura', '{ind_recurrente_factura}', 96, 'FIV'),
(351, 5, 'sys_usuario', 'Asignar A:', ' ', 7, 'no_cod_usuario', '{cod_usuario}', 96, 'FIV'),
(352, 5, 'sys_empresa', 'Empresa Facturadora', 'Empresa que factura', 7, 'cod_empresa', '{cod_empres}', 96, 'FIV'),
(353, 10, '', 'sub total', ' ', 7, 'sub_total_factura', '{sub_total_factura}', 96, 'FIO'),
(354, 10, '', 'sub total descuento', ' ', 7, 'sub_totaldes_factura', '{sub_totaldes_factura}', 96, 'FIO'),
(355, 10, '', 'importe', ' ', 7, 'imp_factura', '{imp_factura}', 96, 'FIO'),
(356, 5, 'fa_item', 'Item', 'Item', 2, 'no_cod_item', '{cod_item}', 97, 'FIV'),
(357, 1, '', 'Referencia', 'Referencia', 1, 'no_ref_item', '{ref_item}', 97, 'FIV'),
(358, 1, '', 'Valor', 'Importe', 2, 'no_imp', '{imp}', 97, 'FIV'),
(359, 5, 'fa_descuentos', 'Descuento', '% Des', 2, 'no_cod_descuento', '{cod_descuento}', 97, 'FIV'),
(360, 5, 'fa_impuesto', 'Impuesto', 'Impuesto', 2, 'no_cod_impuesto', '{cod_impuesto}', 97, 'FIV'),
(361, 14, '', 'Cantidad', 'Cantidad', 1, 'no_can_detalle', '{can_detalle}', 97, 'FIV'),
(362, 10, '', 'cod_orden', 'Orden', 1, 'cod_orden', '{cod_orden}', 98, 'FIO'),
(363, 1, '', 'Orden', 'Nombre de la orden', 7, 'nom_orden', '{nom_orden}', 98, 'FIV'),
(364, 2, '', 'Descripcion', '', 7, 'des_orden', '{des_orden}', 98, 'FIV'),
(365, 9, '', 'Adjunto-comprobante', ' ', 7, 'img_orden', '{img_orden}', 98, 'FIV'),
(366, 5, 'fa_cotizacion', 'Cotizacion', ' ', 7, 'cod_factura', '{cod_factura}', 98, 'FIV'),
(367, 5, 'sys_empresa', 'Empresa', ' ', 7, 'cod_empresa', '{cod_empresa}', 98, 'FIV'),
(368, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 98, 'FIO'),
(369, 10, '', 'cod_vehiculo_clase', ' ', 7, 'cod_vehiculo_clase', '{cod_vehiculo_clase}', 99, 'FIO'),
(370, 1, '', 'Clase de Vehiculo', ' ', 7, 'nom_vehiculo_clase', '{nom_vehiculo_clase}', 99, 'FIV'),
(371, 2, '', 'Descripcion', ' ', 7, 'des_vehiculo_clase', '{des_vehiculo_clase}', 99, 'FIV'),
(372, 5, 'sys_empresa', 'Empresa', ' ', 7, 'cod_empresa', '{cod_empresa}', 99, 'FIV'),
(373, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 99, 'FIV'),
(374, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 99, 'FIO'),
(375, 10, '', 'cod_tip_servicio', ' ', 7, 'cod_tip_servicio', '{cod_tip_servicio}', 100, 'FIO'),
(376, 1, '', 'Tipo de Servicio', ' ', 7, 'nom_tip_servicio', '{nom_tip_servicio}', 100, 'FIV'),
(377, 2, '', 'Descripcion', ' ', 7, 'des_tip_servicio', '{des_tip_servicio}', 100, 'FIV'),
(378, 5, 'sys_empresa', 'Empresa', ' ', 7, 'cod_empresa', '{cod_empresa}', 100, 'FIV'),
(379, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 100, 'FIV'),
(380, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 100, 'FIO'),
(381, 10, '', 'cod_tip_documento', ' ', 7, 'cod_tip_documento', '{cod_tip_documento}', 101, 'FIO'),
(382, 1, '', 'Tipo de Documento', ' ', 7, 'nom_tip_documento', '{nom_tip_documento}', 101, 'FIV'),
(383, 2, '', 'Descripcion', ' ', 7, 'des_tip_documento', '{des_tip_documento}', 101, 'FIV'),
(384, 5, 'sys_empresa', 'Empresa', ' ', 7, 'cod_empresa', '{cod_empresa}', 101, 'FIV'),
(385, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 101, 'FIV'),
(386, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 101, 'FIO'),
(387, 10, '', 'cod_combustible', ' ', 7, 'cod_combustible', '{cod_combustible}', 102, 'FIO'),
(388, 1, '', 'Tipo de Documento', ' ', 7, 'nom_combustible', '{nom_combustible}', 102, 'FIV'),
(389, 2, '', 'Descripcion', ' ', 7, 'des_combustible', '{des_combustible}', 102, 'FIV'),
(390, 5, 'sys_empresa', 'Empresa', ' ', 7, 'cod_empresa', '{cod_empresa}', 102, 'FIV'),
(391, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 102, 'FIV'),
(392, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 102, 'FIO'),
(393, 10, '', 'cod_vehiculo', ' ', 7, 'cod_vehiculo', '{cod_vehiculo}', 104, 'FIO'),
(394, 1, '', 'Nro Placa', ' ', 7, 'placa_vehiculo', '{placa_vehiculo}', 103, 'FIV'),
(395, 1, '', 'Marca', 'Mazda, ford etc', 7, 'marca_vehiculo', '{marca_vehiculo}', 103, 'FIV'),
(396, 1, '', 'Linea', '3, i25 etc', 7, 'linea_vehiculo', '{linea_vehiculo}', 103, 'FIV'),
(397, 1, '', 'Modelo', ' ', 7, 'modelo_vehiculo', '{modelo_vehiculo}', 103, 'FIV'),
(398, 1, '', 'Nro Licencia Transito', ' ', 7, 'lic_trans_vehiculo', '{lic_trans_vehiculo}', 103, 'FIV'),
(399, 1, '', 'Cilindraje', ' ', 4, 'cc_vehiculo', '{cc_vehiculo}', 103, 'FIV'),
(400, 1, '', 'Color', ' ', 7, 'color_vehiculo', '{color_vehiculo}', 103, 'FIV'),
(401, 1, '', 'Carroceria', ' ', 7, 'tipo_carro_vehiculo', '{tipo_carro_vehiculo}', 103, 'FIV'),
(402, 1, '', 'Capacidad', ' ', 2, 'cap_vehiculo', '{cap_vehiculo}', 103, 'FIV'),
(403, 1, '', 'Nro Motor', ' ', 7, 'num_motor_vehiculo', '{num_motor_vehiculo}', 104, 'FIV'),
(404, 1, '', 'Nro Serie', ' ', 7, 'num_serie_vehiculo', '{num_serie_vehiculo}', 104, 'FIV'),
(405, 1, '', 'Nro Chasis', ' ', 7, 'num_chasis_vehiculo', '{num_chasis_vehiculo}', 104, 'FIV'),
(406, 9, '', 'Imagen Vehiculo', ' ', 7, 'img_vehiculo', '{img_vehiculo}', 104, 'FIV'),
(407, 5, 'hue_vehiculo_clase', 'Clase de Vehiculo', ' ', 7, 'cod_vehiculo_clase', '{cod_vehiculo_clase}', 104, 'FIV'),
(408, 5, 'hue_tip_servicio', 'Tipo de Servicio', ' ', 7, 'cod_tip_servicio', '{cod_tip_servicio}', 104, 'FIV'),
(409, 5, 'hue_combustible', 'Combustible', ' ', 7, 'cod_combustible', '{cod_combustible}', 104, 'FIV'),
(410, 5, 'sys_empresa', 'Empresa', ' ', 7, 'cod_empresa', '{cod_empresa}', 104, 'FIV'),
(411, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 104, 'FIV'),
(412, 2, '', 'Resticciones de Movilidad', ' ', 7, 'no_res_mov_vehiculo_datos', '{res_mov_vehiculo_datos}', 105, 'FIV'),
(413, 3, '', 'Blindaje', ' ', 7, 'no_blindaje_vehiculo_datos', '{blindaje_vehiculo_datos}', 105, 'FIV'),
(414, 1, '', 'Potencia Motor', ' ', 4, 'no_pot_vehiculo_datos', '{pot_vehiculo_datos}', 105, 'FIV'),
(415, 1, '', 'Nro Puertas', ' ', 4, 'no_num_pue_vehiculo_datos', '{num_pue_vehiculo_datos}', 105, 'FIV'),
(416, 2, '', 'Limitacion a la Propiedad', ' ', 7, 'no_lim_pro_vehiculo_datos', '{lim_pro_vehiculo_datos}', 105, 'FIV'),
(417, 11, '', 'Fecha de Matricula', ' ', 7, 'no_fec_mat_vehiculo_datos', '{fec_mat_vehiculo_datos}', 106, 'FIV'),
(418, 11, '', 'Fecha Expedicion de Licencia TT', ' ', 7, 'no_fec_exp_lic_vehiculo_datos', '{fec_exp_lic_vehiculo_datos}', 106, 'FIV'),
(419, 11, '', 'fecha de Vencimiento de Licencia TT', ' ', 7, 'no_fec_ven_vehiculo_datos', '{fec_ven_vehiculo_datos}', 106, 'FIV'),
(420, 2, '', 'Organismo de Transito de registro', ' ', 7, 'no_tt_vehiculo_datos', '{tt_vehiculo_datos}', 106, 'FIV'),
(421, 1, '', 'Nro Lateral', 'Opcional', 7, 'no_lat_vehiculo_datos', '{lat_vehiculo_datos}', 106, 'FIV'),
(422, 10, '', 'cod_vehiculo', ' ', 7, 'no_cod_vehiculo', '{cod_vehiculo}', 106, 'FIO'),
(423, 5, 'hue_tip_documento', 'Tipo Documento', 'Documento', 4, 'no_cod_documento_documento', '{cod_documento_documento}', 107, 'FIV'),
(424, 1, '', 'Nro Documento', 'Nro Documento', 4, 'no_num_vehiculo_documento', '{num_vehiculo_documento}', 107, 'FIV'),
(425, 1, '', 'Fecha de Registro', 'Fecha Registro', 2, 'no_fec_vehiculo_documento', '{fec_vehiculo_documento}', 107, 'FIV'),
(426, 1, '', 'Fecha Vencimiento', 'Fecha Vencimiento', 2, 'no_fec_ven_vehiculo_documento', '{fec_ven_vehiculo_documento}', 107, 'FIV'),
(427, 10, '', 'cod_cliente', ' ', 7, 'cod_cliente', '{cod_cliente}', 109, 'FIO'),
(428, 1, '', 'Nombre', ' ', 7, 'nom_cliente', '{nom_cliente}', 109, 'FIV'),
(429, 1, '', 'Nro Identidad', ' ', 7, 'nit_cliente', '{nit_cliente}', 109, 'FIV'),
(430, 1, '', 'huella Dactilar', ' ', 7, 'huella_cliente', '{huella_cliente}', 109, 'FIV'),
(431, 1, '', 'Direccion', ' ', 7, 'dir_cliente', '{dir_cliente}', 109, 'FIV'),
(432, 1, '', 'Correo Electronico', ' ', 7, 'email_cliente', '{email_cliente}', 109, 'FIV'),
(433, 1, '', 'Telefono 1', ' ', 7, 'tel_cliente', '{tel_cliente}', 109, 'FIV'),
(434, 1, '', 'Telefono 2', ' ', 7, 'tel1_cliente', '{tel1_cliente}', 109, 'FIV'),
(435, 1, '', 'Nro Fax', ' ', 7, 'fax_cliente', '{fax_cliente}', 110, 'FIV'),
(436, 1, '', 'Nro Movil', ' ', 7, 'cel_cliente', '{cel_cliente}', 110, 'FIV'),
(437, 2, '', 'Observaciones', ' ', 7, 'obs_cliente', '{obs_cliente}', 110, 'FIV'),
(438, 5, 'sys_ciudad', 'Ciudad', ' ', 7, 'cod_ciudad', '{cod_ciudad}', 110, 'FIV'),
(439, 5, 'sys_empresa', 'Empresa', ' ', 7, 'cod_empresa', '{cod_empresa}', 110, 'FIV'),
(440, 10, '', 'cod_cliente_vehiculo', ' ', 7, 'cod_cliente_vehiculo', '{cod_cliente_vehiculo}', 111, 'FIO'),
(441, 5, 'hue_cliente', 'Cliente', ' ', 7, 'cod_cliente', '{cod_cliente}', 111, 'FIV'),
(442, 5, 'hue_vehiculo', 'Vehiculo', ' ', 7, 'cod_vehiculo', '{cod_vehiculo}', 111, 'FIV'),
(443, 11, '', 'Fecha entrega', ' ', 7, 'fec_registro', '{fec_registro}', 111, 'FIV'),
(444, 11, '', 'Fecha Devolucion', ' ', 7, 'fec_entrega', '{fec_entrega}', 111, 'FIV'),
(445, 11, '', 'Fecha Entrega Retraso', ' ', 7, 'fec_entrega_final', '{fec_entrega_final}', 111, 'FIV'),
(446, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 112, 'FIV'),
(447, 1, '', 'Huella', ' ', 7, 'huella_cliente_vehiculo', '{huella_cliente_vehiculo}', 112, 'FIV'),
(448, 2, '', 'Consideraciones', ' ', 7, 'obs_cliente_vehiculo', '{obs_cliente_vehiculo}', 112, 'FIV'),
(449, 10, '', 'cod_usuario', ' ', 7, 'cod_usuario', '{cod_usuario}', 112, 'FIO'),
(450, 5, 'sys_estado', 'Estado', ' ', 7, 'cod_estado', '{cod_estado}', 110, 'FIV');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_empresa`
--

CREATE TABLE IF NOT EXISTS `sys_empresa` (
  `cod_empresa` int(4) NOT NULL AUTO_INCREMENT,
  `nom_empresa` varchar(45) DEFAULT NULL,
  `nit_empresa` varchar(45) DEFAULT NULL,
  `rep_empresa` varchar(45) DEFAULT NULL,
  `tel_empresa` varchar(45) DEFAULT NULL,
  `dir_empresa` varchar(45) DEFAULT NULL,
  `web_empresa` varchar(45) DEFAULT NULL,
  `email_empresa` varchar(45) DEFAULT NULL,
  `img_empresa` varchar(45) DEFAULT NULL,
  `cod_ciudad` int(6) DEFAULT NULL COMMENT 'FK DESDE SYS_CIUDAD, PARA SABER EN QUE CIUDAD ESTA LOCALIZADA LA EMPRESA',
  `cod_regimen` int(8) DEFAULT NULL COMMENT 'TIPO DE REGIMEN DE LA EMPRESA',
  `cod_moneda` int(9) DEFAULT NULL COMMENT 'TIPO DE MONEDA DE LA EMPRESA',
  PRIMARY KEY (`cod_empresa`),
  KEY `fk_emp_ciud_idx` (`cod_ciudad`),
  KEY `fk_emp_reg_idx` (`cod_regimen`),
  KEY `fk_emp_mon_idx` (`cod_moneda`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LAS EMPRESAS QUE CONTRATAN MODULER' AUTO_INCREMENT=17 ;

--
-- Volcado de datos para la tabla `sys_empresa`
--

INSERT INTO `sys_empresa` (`cod_empresa`, `nom_empresa`, `nit_empresa`, `rep_empresa`, `tel_empresa`, `dir_empresa`, `web_empresa`, `email_empresa`, `img_empresa`, `cod_ciudad`, `cod_regimen`, `cod_moneda`) VALUES
(1, 'mi empresa', '220122222', 'rrr', '888', '999', 'http://www.laaaaaa.com', 'llll@hotmail.com', 'mi_empresa2014-10-06-1412610605.jpg', 5002, 2, 0),
(15, 'RUBIK GROUP', '001111235', 'EDWIN VALENCIA', '8895877', 'MILAN', 'http://www.rubikgroup.com.co', 'r.comercial@rubikgroup.com.co', 'RUBIK_GROUP2014-07-22-1406060683.png', 52001, 2, 1),
(16, 'LACUCHARA', '123546879', 'JUAN CARLOS CESPEDES', '8795485', 'LA ARBOLEDA', 'http://www.lacuchara.com.co', 'gerencia@lacuchara.com.co', 'LACUCHARA2014-07-22-1406060626.png', 5001, 2, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_empresa_contrato`
--

CREATE TABLE IF NOT EXISTS `sys_empresa_contrato` (
  `cod_empresa_contrato` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMETO DE LA TABLA',
  `cod_empresa` int(4) NOT NULL COMMENT 'FK DESDE SYS SISTEMA PARA DETERMINAR LA EMPRESA',
  `cod_contrato` int(4) NOT NULL COMMENT 'FK DESDE SYS CONTRATO PARA RELACIONAR UN CONTRATO A DETERMINADA EMPRESA',
  `cod_estado` varchar(3) NOT NULL DEFAULT 'AAA' COMMENT 'ESTADO DEL CONTRATO - AUDITORIA',
  `cod_modulo` int(4) NOT NULL,
  `fec_inicio_empresa_contrato` date NOT NULL COMMENT 'FECHA EN QUE INICIA EL CONTRATO',
  `fec_baja_empresa_contrato` date NOT NULL COMMENT 'FECHA DE BAJA DEL CONTRATO',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DEL USUARIO QUE REALIZO LA TRANSACCION',
  PRIMARY KEY (`cod_empresa_contrato`),
  KEY `fk_empcon_emp_idx` (`cod_empresa`),
  KEY `fk_empcon_con_idx` (`cod_contrato`),
  KEY `fk_empcon_est_idx` (`cod_estado`),
  KEY `fk_empcon_usu_idx` (`cod_usuario`),
  KEY `fk_empcon_mod_idx` (`cod_modulo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA RELACIONAR LAS EMPRESA, EL CONTRATO QUE TIENEN VIGENTE, ADEMAS EL MODULO QUE SE ACTIVA CON ESTE CONTRATO' AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `sys_empresa_contrato`
--

INSERT INTO `sys_empresa_contrato` (`cod_empresa_contrato`, `cod_empresa`, `cod_contrato`, `cod_estado`, `cod_modulo`, `fec_inicio_empresa_contrato`, `fec_baja_empresa_contrato`, `cod_usuario`) VALUES
(1, 15, 1, 'AAA', 1, '2014-08-21', '3000-08-21', 1),
(2, 15, 1, 'AAA', 2, '2014-01-01', '3000-01-01', 1),
(3, 15, 1, 'AAA', 5, '2014-01-01', '3000-01-01', 1),
(4, 15, 1, 'AAA', 3, '2014-01-01', '3000-01-01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_estado`
--

CREATE TABLE IF NOT EXISTS `sys_estado` (
  `cod_estado` varchar(3) NOT NULL COMMENT 'CODIGO DEL ESTADO',
  `des_estado` varchar(45) DEFAULT NULL COMMENT 'DESCRIPCION DEL ESTADO',
  `mod_estado` varchar(6) DEFAULT NULL COMMENT 'PREFIJO DEL MODULO AL QUE PERTENCE EL ESTADO',
  `fec_estado` date DEFAULT NULL COMMENT 'FECHA DE ALTA DEL ESTADO',
  `hora_estado` varchar(45) DEFAULT NULL COMMENT 'HORA DE ALTA DEL ESTADO',
  PRIMARY KEY (`cod_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS TIPOS DE ESTADO, APLICA PARA TODAS LAS TABLAS';

--
-- Volcado de datos para la tabla `sys_estado`
--

INSERT INTO `sys_estado` (`cod_estado`, `des_estado`, `mod_estado`, `fec_estado`, `hora_estado`) VALUES
('1', 'FACTURA ACTIVA PRIMER CONTACTO', '2', NULL, NULL),
('2', 'FACTURA ACTIVA', '2', NULL, NULL),
('AAA', 'ACTIVO', '1', NULL, NULL),
('BBB', 'BAJA', '1', NULL, NULL),
('BTA', 'BOTON ACTIVO', '1', NULL, NULL),
('BTI', 'BOTON INACTIVO', '1', NULL, NULL),
('CAA', 'CONFIGURACION DE FACTURACION ACTIVA', '2', NULL, NULL),
('CBB', 'CONFIGURACION DE FACTURACION DE BAJA', '2', NULL, NULL),
('CVA', 'cliente con vehiculo en alquiler', '6', NULL, NULL),
('DAA', 'DESCUENTO ACTIVO', '2', NULL, NULL),
('DBB', 'DESCUENTO DE INACTVO', '2', NULL, NULL),
('DES', 'DESACTIVADO', '1', NULL, NULL),
('ELI', 'ELIMINADO', '1', NULL, NULL),
('FAA', 'FACTURACION ACTIVA', '2', NULL, NULL),
('FIO', 'FORMULARIO INPUT OCULTO', '1', NULL, NULL),
('FIV', 'FORMULARIO INPUT VISIBLE', '1', NULL, NULL),
('MAA', 'MENSAJE ACTIVA', '1', NULL, NULL),
('MDE', 'MENSAJE DESACTIVADO', '1', NULL, NULL),
('NAA', 'NOTIFICACION ACTIVA', '1', NULL, NULL),
('NDE', 'NOTIFICACION DESACTIVADA', '1', NULL, NULL),
('SAA', 'servicio service desk activo', '5', NULL, NULL),
('SCC', 'servicio service desk cerrado', '5', NULL, NULL),
('STR', 'servicio service desk en tramite', '5', NULL, NULL),
('VAA', 'vehiculo activo en alquiler', '6', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_formulario`
--

CREATE TABLE IF NOT EXISTS `sys_formulario` (
  `cod_formulario` int(9) NOT NULL AUTO_INCREMENT,
  `nom_formulario` varchar(45) NOT NULL,
  `tit_formulario` varchar(45) DEFAULT NULL,
  `des_formulario` varchar(10000) DEFAULT NULL,
  `nom_tabla` varchar(45) NOT NULL,
  `fec_formulario` date DEFAULT NULL,
  `hora_formulario` varchar(45) DEFAULT NULL,
  `cod_usuario` int(8) NOT NULL,
  `tip_formulario` int(1) NOT NULL DEFAULT '0' COMMENT 'TIPO DE FORMULARIO PARALA VISTA',
  `dat_formulario` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_formulario`),
  KEY `fk_form_usu_idx` (`cod_usuario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS FORMULARIOS DEL SISTEMA' AUTO_INCREMENT=114 ;

--
-- Volcado de datos para la tabla `sys_formulario`
--

INSERT INTO `sys_formulario` (`cod_formulario`, `nom_formulario`, `tit_formulario`, `des_formulario`, `nom_tabla`, `fec_formulario`, `hora_formulario`, `cod_usuario`, `tip_formulario`, `dat_formulario`) VALUES
(1, 'nuevaUsuario', 'Registro de Usuarios', 'En este proceso usted podrÃ¡ crear los usuarios para el sistema, cada usuario tendrÃ¡ el alcance dentro del sistema segÃºn la configuraciÃ³n que usted le registre, tenga en cuenta que los permisos se relacionan a los perfiles creados en la opciÃ³n Sys ConfiguraciÃ³n->Perfiles, y los puede relacionar en la opciÃ³n Sys ConfiguraciÃ³n->Perfil Vs MÃ©todos, estos mÃ©todos son los que definen si un usuario puede: Agregar, Modificar, Consultar o eliminar dentro de cada uno de los procesos restantes, Para poder visualizar un proceso en especifico es necesario asignarlos a cada usuario, esta opciÃ³n la puede encontrar en el siguiente formulario en el panel MenÃºs y sub MenÃºs, las listas son de mÃºltiple selecciÃ³n, seleccione todas las opciones que quiere que un usuario visualice en su secciÃ³n de menÃºs presionando control y con el mouse cliqueando cada uno de los menÃºs, por ultimo relacione la empresa a la cual pertenece y guarde la informaciÃ³n.\r\n\r\n<p></p><strong>Nota: la opcion Activar Ayuda mostrara la explicacion de cada uno de los formularios a cada usuario, para habilitar la ayuda seleccione esta opcion.</strong>', 'sys_usuario', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(2, 'nuevaEmpresa', 'Registro de Empresas', 'En el siguiente proceso usted podrÃ¡ registrar empresas en el sistema, dÃ¡ndose el caso de contratar alguno de los mÃ³dulos con un tercero, con esta opciÃ³n usted podrÃ¡ registrar las empresas y los usuarios de estas siempre y cuando no tengan un administrador que lo haga, todos los mÃ³dulos funcionaran independientes a cada empresa, todos los registros y transacciones se cargaran por empresa que registra de esta forma, usted podrÃ¡ manejar el mismo modulo para mÃºltiples empresas sin percances. todos los campos deben ser verÃ­dicos pues estos se imprimirÃ¡n el en contrato fÃ­sico que arroja el sistema. luego de registrar la empresa usted podrÃ¡ asignar los mÃ³dulos en la opciÃ³n Sys Empresa->Empresa Contrato, con esto el sistema des habilitara automÃ¡ticamente el acceso cuando el contrato se venza por fecha, usted tambiÃ©n puede caducarlo manualmente si surge algÃºn problema con el cumplimiento de algÃºn apartado del contrato', 'sys_empresa', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(3, 'no aplica', NULL, NULL, 'no aplica', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(4, 'MenuUsuario', 'Asignacion de Menus', 'Este proceso se creo independiente para la asignaciÃ³n individual de los menÃºs de los usuarios, recuerde que usted configura el alcance de cada uno de los usuarios del sistema, cada lista en el formulario a continuaciÃ³n es multi selectiva, puede seleccionar todos usuario que manejen un menÃº con las mismas condiciones y luego cliquear el botÃ³n seleccionar todo, el sistema seleccionara automÃ¡ticamente todos los menÃºs de la lista, a continuaciÃ³n guarde la informaciÃ³n, si el usuario ya tiene asignado el menÃº, este no lo registrara nuevamente.', 'sys_usuario_menu', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(5, 'subMenuUsuario', 'Asignacion de Sub Menus', 'Este proceso se creo independiente para la asignaciÃ³n individual de los sub menÃºs de los usuarios, recuerde que usted configura el alcance de cada uno de los usuarios del sistema, cada lista en el formulario a continuaciÃ³n es multi selectiva, puede seleccionar todos los usuarios que manejen los mismos sub menÃºs con las mismas condiciones y luego cliquear el botÃ³n seleccionar todo, el sistema seleccionara automÃ¡ticamente todos los sub menÃºs de la lista, a continuaciÃ³n guarde la informaciÃ³n, si el usuario ya tiene asignado el sub menÃº, este no lo registrara nuevamente, recuerde que los sub menÃºs son las opciones que conforman cada menÃº padre de esta secciÃ³n.', 'sys_usuario_menu_sub', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(6, 'gestionUsuario', 'Gestion General de Usuarios', 'En la anterior tabla, se listan todos los usuarios configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un usuario en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo usuario, de click en nuevo, el sistema lo redireccionara al formulario de registro de usuario. \r\n<p><p/>La tabla tiene opciones de filtrado, por favor, si necesita ubicar un usuario en especifico digitelo en la casilla Filtro, el sistema listara los usuarios segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada usuario, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_usuario', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(7, 'nuevaEmpresaVsContrato', 'Asignacion de Contrato por Empresa', 'En el siguiente proceso, usted podrÃ¡ asignar los contratos que se configuran en el menÃº Sys Contrato a las empresas que utilizan la aplicaciÃ³n, la vigencia de los mÃ³dulos dependen exclusivamente del tiempo de cada contrato, si el contrato caduca, se deberÃ¡ cambiar su fecha o en su defecto asignar otra clase de contrato a la empresa por la utilizaciÃ³n de cualquier modulo.', 'sys_empresa_contrato', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(8, 'editarPerfil', NULL, NULL, 'sys_usuario', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(9, 'gestionEmpresa', 'Gestion General de Empresas', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro.\r\n\r\nLa tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_empresa', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(10, 'nuevaContrato', 'Registro de Contrato', 'En este proceso, usted podrÃ¡ registrar todos los contratos que desee dependiendo de la duraciÃ³n, la descripciÃ³n se visualizara en el contrato imprimible cuando se contrate con otra empresa, seleccione el numero de meses del contrato y guarde la informaciÃ³n. es la opciÃ³n Sys Empresa->Empresa Contrato se cargara la lista de contratos registrados.', 'sys_contrato', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(11, 'gestionContrato', 'Gestion General de Contratos', 'En la anterior tabla, se listan todos los contratos configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un contrato en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo contrato, de click en nuevo, el sistema lo redireccionara al formulario de registro de contrato. \r\n<p><p/>La tabla tiene opciones de filtrado, por favor, si necesita ubicar un usuario en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_contrato', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(12, 'mensajes', NULL, NULL, 'sys_mensajes', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(13, 'gestionMensajes', NULL, NULL, 'sys_mensajes', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(14, 'nuevaFacConfig', NULL, NULL, 'fa_config', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(15, 'nuevaTipoImpuesto', NULL, NULL, 'fa_tipoimpuesto', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(16, 'nuevaImpuesto', NULL, NULL, 'fa_impuesto', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(17, 'nuevaTipoPago', NULL, NULL, 'fa_tipopago', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(18, 'nuevaUniMedida', NULL, NULL, 'fa_unimedida', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(19, 'nuevaMoneda', NULL, NULL, 'fa_moneda', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(20, 'nuevaNumeracion', NULL, NULL, 'fa_numeracion', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(21, 'nuevaRegimen', NULL, NULL, 'fa_regimen', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(22, 'Gestion Configuracion Facturacion', NULL, NULL, 'fa_config', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(23, 'Gestion tipoImpuesto', NULL, NULL, 'fa_tipoimpuesto', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(24, 'Gestion Impuestos', NULL, NULL, 'fa_impuesto', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(25, 'Gestion Tipo Pago', NULL, NULL, 'fa_tipopago', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(26, 'Gestion Unidades de Medida', NULL, NULL, 'fa_unimedida', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(27, 'Gestion de Monedas', NULL, NULL, 'fa_moneda', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(28, 'Gestion Numeraciones', NULL, NULL, 'fa_numeracion', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(29, 'Gestion Regimen', NULL, NULL, 'fa_regimen', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(30, 'Gestion Facturas', 'Gestion General Cotizaciones', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro.\r\n\r\nLa tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'fa_factura', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(31, 'nuevaFactura', 'Registro de Cotizaciones', 'En este proceso usted podrÃ¡ registrar las cotizaciones de su empresa, se debe tener en cuenta que una cotizaciÃ³n no es una factura oficial, pero puede cambiar el estado una vez se apruebe la orden de compra por el cliente, para realizar el proceso de registro , es importante saber que todos los campos son obligatorios, la numeraciÃ³n es cargada de acuerdo a la configuraciÃ³n de la misma, el cliente igualmente y segÃºn su configuraciÃ³n en el registro, cargara automÃ¡ticamente las fechas de pago segÃºn su tipo de pago, esto con el fin de agilizar el proceso, las observaciones no son imprimibles con la cotizaciÃ³n pero si las notas, y el campo asigna a. es para asignar la cotizaciÃ³n que se esta registrando a un usuario de la empresa para hacer seguimiento a la misma mediante el modulo service desk, usted podrÃ¡ tambiÃ©n cargar todos los items que desee a una cotizaciÃ³n, y luego modificarlos o aplicar descuentos o impuesto adicionales segÃºn la negociaciÃ³n con el cliente. al seleccionar el Ã­tem, este cargara por defecto: referencia, valor sin impuestos, 1 cantidad y calculara el valor automÃ¡ticamente, si usted desea, podrÃ¡ cambiar estos valores posteriormente. ', 'fa_factura', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(32, 'Gestion Items', NULL, NULL, 'fa_item', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(33, 'nuevaItem', NULL, NULL, 'fa_item', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(34, 'Gestion Inventario', NULL, NULL, 'fa_inventario', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(35, 'nuevaInventario', NULL, NULL, 'fa_inventario', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(36, 'Gestion Cliente', NULL, NULL, 'fa_cliente', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(37, 'nuevaCliente', NULL, NULL, 'fa_cliente', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(38, 'Gestion Estados', 'Gestion General Estados/Actuaciones', 'En la anterior tabla, se listan todos los estados configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un estado en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo estado, de click en nuevo, el sistema lo redireccionara al formulario de registro de contrato. \r\n<p><p/>La tabla tiene opciones de filtrado, por favor, si necesita ubicar un usuario en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_estado', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(39, 'nuevaEstado', 'Registro de Estado/Actuacion', 'Registre todos los estados que se desea utilizar para asignar a un registro, el estado AAA,BAA,FIV,FIO son utilizados en procesos internos del sistema, por este motivo, no se pueden modificar, ni tampoco eliminar, usted podrÃ¡ crear un estado propio para ejecutar el registro de un proceso especifico, por ejemplo, para asignar un estado a una cotizaciÃ³n usted podrÃ¡ crear un estado CAA, la primer sigla significa el proceso que se esta manejando CotizaciÃ³n y las otras dos el estado Activa, de esta forma se podrÃ¡n manejar estados por cada proceso.', 'sys_estado', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(40, 'Gestion Descuentos', NULL, NULL, 'fa_descuentos', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(41, 'nuevaDescuento', NULL, NULL, 'fa_descuentos', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(42, 'Gestion de Cuentas', NULL, NULL, 'con_cuenta', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(43, 'nuevaCuenta', NULL, NULL, 'con_cuenta', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(44, 'Gestion Restaurante', NULL, NULL, 'lc_restaurantes', '2014-07-14', '2014-05-21 15:01:10', 1, 0, 0),
(45, 'nuevaRestaurante', 'Registro de Restaurantes', 'En esta seccion usted podra registrar los restaurantes y configurarlos para vizualizarlos en su app, para este proceso se deben tener en cuenta los siguientes apartados.<p><p><li>Todos los campos son obligatorios para el proceso</li><li>El campo Estado siempre debe ser AAA</li><p>A continuacion Guarde la informacion y siga con el siguiente menu</p>', 'lc_restaurantes', '2014-07-14', '2014-05-21 15:01:10', 1, 0, 1),
(46, 'Gestion Imagenes', NULL, NULL, 'lc_restaurantes_img', '2014-07-14', '2014-05-21 15:01:10', 1, 0, 0),
(47, 'restaurantes_img', 'Registro de Imagenes', 'En esta seccion se pueden registrar las imagenes de los restaurantes, estas mismas pueden ser configuradas para que se puedan visualizar en los distintos apartados de la app, para registrar y configurar las imagenes tenga en cuenta los siguiente: <p><p><li>Todos los campos son obligatorios para el proceso</li><li>Img App: esta imagen se visualizara en el filtro principal de la app</li><li>Img Header: esta imagen se visualizara en el encabezado de la informacion del restaurante</li><li>Img Footer: esta imagen se visualizara en el pie de pagina de la informacion del restaurante</li><li>Img Publicidad: esta es la imagen para el banner de publicidad del la pagina principal de la app</li><p></p>Para finalizar el estado siempre debe ser AAA', 'lc_restaurantes_img', '2014-07-14', '2014-05-21 15:01:10', 1, 0, 1),
(48, 'Gestion Ciudades', 'Gestion General de Ciudades', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_ciudad', '2014-07-14', '2014-05-21 15:01:10', 1, 0, 1),
(49, 'nuevaCiudad', 'Registro de Ciudades', 'Utilice el siguiente proceso para registrar las ciudades por departamentos, las cuales se visualizaran en los formularios donde sea requerido este campo, el primer dato es obligatorio y se puede encontrar en los registros DANE, el segundo el municipio y por ultimo el departamento al cual pertenece.', 'sys_ciudad', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(50, 'Gestion Perfiles', 'Gestion General Perfiles', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registros en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo contrato, de click en nuevo, el sistema lo redireccionara al formulario de registro de contrato. \r\n<p><p/>La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_perfil', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(51, 'nuevaPerfil', 'Registro de Perfiles', 'Teniendo en cuenta, el papel que debe desempeÃ±ar cada uno de los usuarios en el sistema, debe ser asignado un perfil que no sobrepase su alcance en los procesos criticos, en el siguiente formulario, usted podra registrar todos los perfiles que considere necesarios para luego asignarlos a un usuario determinado, a continuacion asigne los metodos que puede realizar este usuario en el menu: Sys Configuraion->Perfil Vs Metodo.', 'sys_perfil', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(52, 'Gestion Metodos', 'Gestion General Metodos', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registros en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo contrato, de click en nuevo, el sistema lo redireccionara al formulario de registro de contrato.\r\n\r\nLa tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_metodos', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(53, 'nuevaMetodo', 'Registro de Metodos', 'El siguiente proceso es creado para el administrador del sistema, debido a la complejidad de la programaciÃ³n para cada uno de los mÃ©todos, los existentes ya funcionan internamente y pueden ser asignados a los perfiles sin ningÃºn problema, por favor colÃ³quese en contacto con el administrador para la creaciÃ³n de otros mÃ©todos.', 'sys_metodos', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(54, 'Gestion Perifl Metodos', 'Gestion General Perfil Vs Metodos', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_prefil_metodos', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(55, 'nuevaPerfilMetodo', 'Asignacion de Metodos a un Perfil', 'Puede asignar los mÃ©todos a los cuales tiene alcance un perfil determinado segÃºn las restricciones que usted considere pertinentes, antes de llegar a este proceso, se debe crear el perfil ubicando la opciÃ³n Perfiles-nuevo y posteriormente asignar los mÃ©todos que puede ver este perfil, con este proceso, usted puede asegurar el sistema para que cada usuario con un perfil determinado, desarrolle las tareas concernientes a su desempeÃ±o o cargo.', 'sys_perfil_metodos', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(56, 'Gestion Formularios', 'Gestion General Formularios', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_formulario', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(57, 'nuevaFormulario', 'Registro de Formularios', 'Luego de crear el esquema en la base de datos, cree el formulario que gestionara cada una de las tablas de dicho esquema, con este proceso, podrÃ¡ gestionar mÃ©todos como agregar, borrar, desactivar entre otros para una sola tabla. el nombre esquema debe ser exactamente igual al nombre de la tabla que esta gestionando de la base de datos. Igualmente puede registrar la documentaciÃ³n de cada uno de los formularios del sistema donde se especifica la funcionalidad y el proceso que realiza cada uno, esta ayuda se activa cada usuario en la opciÃ³n modificar usuario y chequeando el indicador de ayuda', 'sys_formulario', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(58, 'Gestion Formulario Metodos', 'Gestion General Formulario Vs Metodos', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_formulario_metodos', '2014-07-23', '07:59:21', 1, 0, 1),
(59, 'nuevaFormularioMetodo', 'Asignacion de Metodos a un Formulario', 'En este proceso usted pude asociar determinados metodos segun la funcion especifica de un formulario, al momento del usuario entrar a realizar cualquier acciÃ³n, se visualizaran los mÃ©todos des formulario y los restringirÃ¡ a los que tiene asociado el perfil del usuario, de esta forma, se podrÃ¡ tener control sobre las acciones que cada usuario realiza sobre un proceso.', 'sys_formulario_metodos', '2014-07-23', '08:00:30', 1, 0, 1),
(60, 'Gestion Frame', 'Gestion General Frames', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_frame', '2014-07-23', '08:50:53', 1, 0, 1),
(61, 'nuevaFrame', 'Registro de Frames', 'En  el siguiente proceso usted tendrÃ¡ acceso a los frames encargados de gestionar la informaciÃ³n de cada formulario, en los frames usted encontrara los inputs que lo componen, puede adicionar cuantos frames considere necesario segun la distribuciÃ³n y la forma de recolecciÃ³n de informaciÃ³n que usted emplee en su empresa, igualmente cambiar los nombres de los frames para hacerlos mas amenos a los usuarios, los frames son adaptativos y funcionan en tamaÃ±os de 1 a 12 para distribuirse a travÃ©s de la pantalla. recuerde luego de crear el frame, asociarlo al formulario al cual pertenece.', 'sys_frame', '2014-07-23', '08:51:03', 1, 0, 1),
(62, 'Gestion Detalle Frame', 'Gestion General Detalle Frame', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_detframe', '2014-07-23', '09:49:55', 1, 0, 1),
(63, 'nuevaDetFrame', 'Detalle del frame', 'En el siguiente proceso, usted podra registrar, modificar y eliminar todos los componentes de un frame de un formulario determinado, los componentes de los frames son los encargados de recoger la informacion que se desea insertar en las tablas del sistema, entre estos se encuentran: cajas de texto, combos, checkbox, radios, textarea, los componentes pueden ser difinidos por tamaÃ±os para su presentacion en la interfaz, partiendo de 1 pÃ ra un caracter visible hasta 12, estos seran distribuidos y cargados en los frames en el orden en que sean guardados, si desea agregar mas componentes, pulse agregar item, caso contrario, pulse eliminar item, si el componente es un combo o lista, debe seleccionar la tabla referencia para que es sistema llene este componente con los datos de dicha tabla.', 'sys_detframe', '2014-07-23', '09:50:17', 1, 0, 1),
(64, 'gestion Carta', NULL, NULL, 'lc_carta', '2014-07-23', '09:50:17', 1, 0, 1),
(65, 'nuevaCarta', 'Registro de Cartas', 'En este proceso se podra llevar a cabo el registro de cartas de cada uno de los restaurantes, antes de ejecutar en registro de los mencionado anteriormente, se debe registrar el restaurante con toda su configuracion, las cartas son dividas en tres modalidades (Entrantes, Platos, Postres), una carta puede ser configurada con las tres modalidades, pero no se pueden realizar registros sin elegir al menos una de estas, por este motivo, todos los campos son obligatorios para el proceso, luego de el registro debe verificar la informacion desde la app.', 'lc_carta', '2014-07-23', '09:50:17', 1, 0, 1),
(66, 'gestion Categorias', NULL, NULL, 'lc_categorias', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(67, 'nuevaCategorias', 'Registro de Categorias', 'restaurante(Comidas Rapidas, Comida Italiana, Comida Oriental, Comida francesa), todos los campos son obligatorios para el proceso, despues de registrar todas las categorias, proceda a registrar las sub categorias pertenecientes  para la clasificacion de las comidas de los restaurantes.', 'lc_categorias', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(68, 'gestion Sub Categorias', NULL, NULL, 'lc_categorias_sub', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(69, 'nuevaCategoriasSub', 'Registro de Sub Categorias', 'Las sub categorias son necesarias para la clasificacion de los restaurantes, un restaurantes puede pertencer a varias sub cateogiras, entre mas completa sea la informacion de cada restaurante, sera encontrado con mayor relevancia en el filtro de la app, todos los restaurantes muestran en forma de tag las sub categorias a las cuales pertenecen en la informacion general, tambien se pueden encontrar en tipos de comida, el cual desglosa de manera facil, todos los restaurantes por sub categorias. todos los campos son obligatorios para este proceso, el finalizar este proceso, relacione los restaurantes por sub categorias en la opcion del menu.', 'lc_categorias_sub', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(70, 'gestion Cliente', NULL, NULL, 'lc_categorias', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(71, 'nuevaCliente', '', '', 'lc_cliente', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(72, 'gestion Comentario', NULL, NULL, 'lc_comentario', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(73, 'nuevaComentario', '', '', 'lc_comentario', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(76, 'gestion Palabras Clave', NULL, NULL, 'lc_palabrasclave', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(77, 'nuevaPalabrasClave', '', '', 'lc_palabrasclave', '2014-08-13', '2014-08-13 17:19:06', 1, 0, 1),
(78, 'nuevaNaturalezaJuridica', NULL, NULL, 'dat_naturaleza_juridica', '2014-07-23', '09:50:17', 1, 0, 1),
(79, 'gestion Naturaleza Juridica', NULL, NULL, 'dat_naturaleza_juridica', '2014-07-23', '09:50:17', 1, 0, 1),
(90, 'gestion Restaurante Categoria Sub', 'Registro de Restaurantes', 'descipcion', 'lc_restaurantes_categoria_sub', '2014-07-23', '09:50:17', 1, 0, 1),
(91, 'nuevaRestauranteCategoriaSub', 'Clasificacion de Restaurantes', 'En este proceso usted podra relacionar todos los restaurantes a una sub Categoria para clasificar su diversidad de comida, esta informacion se vera reflejada en la app desde su dispositivo, es esencial que la informacion se concordante con el restaurante para poder tener exito en la busqueda de un restaurante.', 'lc_restaurantes_categoria_sub', '2014-07-23', '09:50:17', 1, 0, 1),
(92, 'gestion Empresa Vs Contrato', 'Gestion General de Contratos por Empresa', 'En la anterior tabla, se listan todos los contratos asignados a una empresa configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo contrato, de click en nuevo, el sistema lo redireccionara al formulario de registro de contrato. \r\n<p><p/>La tabla tiene opciones de filtrado, por favor, si necesita ubicar un usuario en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'sys_empresa_contrato', '2014-07-23', '09:50:17', 1, 0, 1),
(94, 'Gestion Facturacion', 'Gestion General Facturacion', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro.\r\n\r\nLa tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'fa_factura', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(95, 'nuevaFacturacion', 'Registro de Facturas', 'En este proceso usted podrÃ¡ registrar las facturas de su empresa, se debe tener en cuenta que una cotizaciÃ³n no es una factura oficial, pero puede cambiar el estado una vez se apruebe la orden de compra por el cliente, para realizar el proceso de registro , es importante saber que todos los campos son obligatorios, la numeraciÃ³n es cargada de acuerdo a la configuraciÃ³n de la misma, el cliente igualmente y segÃºn su configuraciÃ³n en el registro, cargara automÃ¡ticamente las fechas de pago segÃºn su tipo de pago, esto con el fin de agilizar el proceso, las observaciones no son imprimibles con la cotizaciÃ³n pero si las notas, y el campo asigna a. es para asignar la cotizaciÃ³n que se esta registrando a un usuario de la empresa para hacer seguimiento a la misma mediante el modulo service desk, usted podrÃ¡ tambiÃ©n cargar todos los items que desee a una cotizaciÃ³n, y luego modificarlos o aplicar descuentos o impuesto adicionales segÃºn la negociaciÃ³n con el cliente. al seleccionar el Ã­tem, este cargara por defecto: referencia, valor sin impuestos, 1 cantidad y calculara el valor automÃ¡ticamente, si usted desea, podrÃ¡ cambiar estos valores posteriormente. ', 'fa_factura', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(96, 'Gestion Nota', NULL, NULL, 'con_nota', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(97, 'nuevaNota', 'Transaccion Contable', 'En este proceso usted podrÃ¡ registrar las facturas de su empresa, se debe tener en cuenta que una cotizaciÃ³n no es una factura oficial, pero puede cambiar el estado una vez se apruebe la orden de compra por el cliente, para realizar el proceso de registro , es importante saber que todos los campos son obligatorios, la numeraciÃ³n es cargada de acuerdo a la configuraciÃ³n de la misma, el cliente igualmente y segÃºn su configuraciÃ³n en el registro, cargara automÃ¡ticamente las fechas de pago segÃºn su tipo de pago, esto con el fin de agilizar el proceso, las observaciones no son imprimibles con la cotizaciÃ³n pero si las notas, y el campo asigna a. es para asignar la cotizaciÃ³n que se esta registrando a un usuario de la empresa para hacer seguimiento a la misma mediante el modulo service desk, usted podrÃ¡ tambiÃ©n cargar todos los items que desee a una cotizaciÃ³n, y luego modificarlos o aplicar descuentos o impuesto adicionales segÃºn la negociaciÃ³n con el cliente. al seleccionar el Ã­tem, este cargara por defecto: referencia, valor sin impuestos, 1 cantidad y calculara el valor automÃ¡ticamente, si usted desea, podrÃ¡ cambiar estos valores posteriormente. ', 'con_nota', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(98, 'Gestion Orden', NULL, NULL, 'fa_orden', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(99, 'nuevaOrden', 'Orden de Trabajo', '', 'fa_orden', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(100, 'Gestion Clases de Vehiculo', 'Gestion General de Clases de Vehiculos', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'hue_vehiculo_clase', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 1),
(101, 'nuevaClaseVehiculo', 'Registro de Clases de Vehiculo', 'En este proceso usted podrÃ¡ registrar las clases de vehÃ­culos existentes para segmentar con mayor organizaciÃ³n la informaciÃ³n de sus vehÃ­culos y poder hacer filtros de consulta por clase de vehÃ­culo, llene todos los campos obligatorios, seleccione su empresa y por ultimo seleccione el estado AAA para que pueda aparecer en el registro de automÃ³viles.', 'hue_vehiculo_clase', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 1),
(102, 'Gestion Tipo de Servicio', 'Gestion General de tipos de Servicio', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'hue_tip_servicio', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(103, 'nuevaTipoServicio', 'Registro de Tipos de Servicio', 'En el siguiente proceso se pueden registrar los diferentes tipos de servicio de un automÃ³vil, des particular hasta gubernamental para ampliar mejor la informaciÃ³n y quede desglosada de forma mas entendible para los usuarios, rellene todos los campos obligatorios y por ultimo seleccione su empresa y el estado AAA para que pueda ser visualizado en el proceso de registro de vehiculos', 'hue_tip_servicio', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 1),
(104, 'Gestion Tipo de Documento', 'Gestion General de Tipos de Documento', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'hue_tip_documento', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0);
INSERT INTO `sys_formulario` (`cod_formulario`, `nom_formulario`, `tit_formulario`, `des_formulario`, `nom_tabla`, `fec_formulario`, `hora_formulario`, `cod_usuario`, `tip_formulario`, `dat_formulario`) VALUES
(105, 'nuevaTipoDocumento', 'Registro de Tipos de Documento', 'En el siguiente proceso usted podrÃ¡ registrar todos los diferentes tipos de documentos que pueden ir asociados a un vehÃ­culo desde SOAT hasta seguro contra todo o pÃ³liza, en el proceso de registro de vehÃ­culos, podrÃ¡ visualizar los diferentes tipo de documentos y asociarlos a el vehÃ­culo que esta registrando, si las fechar se encuentran bien registradas. el sistema notificara si el documento se encuentra vencido, rellene todos los campos obligatorios para el proceso, posteriormente seleccione su empresa y el estado AAA para que pueda ser visualizado en el proceso de registro de automÃ³viles.', 'hue_tip_documento', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 1),
(106, 'Gestion Combustible', 'Gestion General de tipos de Combustible', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'hue_combustible', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(107, 'nuevaCombustible', 'Registro de Tipos de combustible', 'En el siguiente proceso usted pude registrar los diferentes tipos de combustible que existen para asociarlos a un vehÃ­culo y conocer su funcionamiento, de esta forma se puede mejorar los filtros de bÃºsqueda y segmentar la informaciÃ³n, rellene los campos obligatorios para este proceso, posteriormente, seleccione su empresa y el estado AAA para poder visualizarlo en el proceso de registro de vehÃ­culos.', 'hue_combustible', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 1),
(108, 'Gestion Vehiculos', 'Gestion General de Vehiculos', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'hue_vehiculo', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(109, 'nuevaVehiculo', 'Registro de vehiculos', 'En el siguiente proceso usted puede registrar toda la informaciÃ³n concerniente a un automÃ³vil, desde el tipo de servicio, clase, combustible entro otros, ademÃ¡s, pude registrar informaciÃ³n sobresaliente del mismo, potencia, numero de motor, capacidad, por ultimo puede agregar la documentaciÃ³n del vehiculo en el ultimo formulario, si desea agregar mas documentos asociados al vehiculo, de click en agregar Ã­tem y rellene los campos, al finalizar, guarde la informaciÃ³n.', 'hue_vehiculo', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0),
(110, 'Gestion Clientes', 'Gestion General de Clientes', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'hue_cliente', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(111, 'nuevaClienteHue', 'Registro de Clientes', 'En este proceso se pueden registrar los clientes para asociar a un vehiculo, la informaciÃ³n el obligatoria y debe ser confirmada, todos los campos son obligatorios, ,los cliente que estÃ¡n registrados aparecerÃ¡n en el proceso de alquiler, la huella se actualizara automÃ¡ticamente en el proceso de alquiler y se mostrara allÃ­ mismo, el estado siempre debe ser AAA.', 'hue_cliente', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 1),
(112, 'Gestion Alquiler', 'Gestion General de Alquiler de Vehiculos', 'En la anterior tabla, se listan todos los registros configurados en el sistema, con estos usted podrÃ¡ realizar las opciones activadas segÃºn su perfil(Modificar, Eliminar, Activar-Desactivar, Agregar Nuevo), para ejecutar una acciÃ³n directa sobre un registro en especifico, selecciÃ³nelo con el marcador de la primer columna de la tabla, y a continuaciÃ³n cliquee la opciÃ³n que desea ejecutar, si desea agregar un nuevo registro, de click en nuevo, el sistema lo redireccionara al formulario de registro. La tabla tiene opciones de filtrado, por favor, si necesita ubicar un registro en especifico digitelo en la casilla Filtro, el sistema listara los registros segÃºn las palabras que usted digite, tambiÃ©n puede seleccionar las columnas de la tabla que desea visualizar, de click en la opciÃ³n columnas y seleccione las que desea ver, el sistema quitara las que no estÃ¡n seleccionadas, de esta forma puede acceder a informaciÃ³n especifica de cada registro, igualmente puede listar el numero de registros que desea ver por pagina, de click en la lista donde se encuentra por defecto el numero 10 y seleccione el numero de filas que desea ver, en la parte inferior de la tabla estÃ¡n el numero de paginas por las cuales puede navegar para visualizar los registros.', 'hue_cliente_vehiculo', '2014-05-21', '2014-05-21 15:01:10', 1, 0, 0),
(113, 'nuevaClienteVehiculo', 'Registro de Alquiler de Vehiculos', 'El siguiente proceso esta diseÃ±ado para asociar un vehÃ­culo a un Ãºnico cliente, el sistema verifica por cada registro que el cliente no se encuentre con otro vehÃ­culo en alquiler, para poder proceder a rentar el vehÃ­culo, cuando el cliente retorne el vehÃ­culo Ãºnicamente modifique el estado por BAA y la fecha de entrega final, para que el sistema evalÃºe si se retraso o es normal la entrega,  para cada registro el estado debe ser AAA. Para obtener la huella, por favor, seleccione el cliente y posteriormente de click en la opciÃ³n  Agregar Huella y solicite a su cliente que coloque el dedo indice en el lector de huellas, posteriormente guarde la informaciÃ³n.', 'hue_cliente_vehiculo', '2014-05-05', '2014-05-21 15:01:10', 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_formulario_config`
--

CREATE TABLE IF NOT EXISTS `sys_formulario_config` (
  `cod_formulario_config` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK UNICO DE LA TABLA AUTOINCREMENTABLE',
  `head_formulario_config` varchar(45) DEFAULT NULL,
  `title_formulario_config` varchar(45) DEFAULT NULL,
  `nom_form_formulario_config` varchar(45) DEFAULT NULL,
  `controller_formulario_config` varchar(45) DEFAULT NULL,
  `view_form_formulario_config` varchar(45) DEFAULT NULL,
  `metodo_formulario_config` varchar(45) DEFAULT NULL,
  `num_form_formulario_config` varchar(45) DEFAULT NULL,
  `form_ant_formulario_config` varchar(45) DEFAULT NULL,
  `num_form_ant_formulario_config` varchar(45) DEFAULT NULL,
  `view_form_ant_formulario_config` varchar(45) DEFAULT NULL,
  `form_formulario_config` varchar(45) DEFAULT NULL,
  `met_edti_formulario_config` varchar(45) DEFAULT NULL,
  `met_new_formulario_config` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cod_formulario_config`),
  UNIQUE KEY `head_formulario_config_UNIQUE` (`head_formulario_config`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA GUARDAR LA CONFIGURACION DE LOS FORMULARIO, METODOS, INDICADORES Y VISTAS' AUTO_INCREMENT=117 ;

--
-- Volcado de datos para la tabla `sys_formulario_config`
--

INSERT INTO `sys_formulario_config` (`cod_formulario_config`, `head_formulario_config`, `title_formulario_config`, `nom_form_formulario_config`, `controller_formulario_config`, `view_form_formulario_config`, `metodo_formulario_config`, `num_form_formulario_config`, `form_ant_formulario_config`, `num_form_ant_formulario_config`, `view_form_ant_formulario_config`, `form_formulario_config`, `met_edti_formulario_config`, `met_new_formulario_config`) VALUES
(1, 'usuario', 'Gestion de Usuarios', 'usuario', 'sistema', '', NULL, '1', NULL, NULL, NULL, 'forms/view_table', 'editaUsuario*1', 'nuevaUsuario*1'),
(2, 'nuevaUsuario', 'Registro de Usuarios', 'usuario', 'sistema', NULL, 'nuevoRegistro', NULL, 'usuario', '6', 'usuario', 'forms/view_form', NULL, NULL),
(3, 'editaUsuario', 'Edicion de usuarios', 'usuario', 'sistema', '', 'editaRegistro', NULL, 'usuario', '6', 'usuario', 'forms/view_form', NULL, NULL),
(4, 'empresa', 'Gestion de Empresa', 'empresa', 'sistema', '', NULL, '2', NULL, NULL, NULL, 'forms/view_table', 'editaEmpresa*6', 'nuevaEmpresa*6'),
(5, 'nuevaEmpresa', 'Registro de Empresas', 'empresa', 'sistema', '', 'nuevoRegistro', NULL, 'empresa', '9', 'empresa', 'forms/view_form', NULL, NULL),
(6, 'editaEmpresa', 'Edicion de Empresas', 'empresa', 'sistema', NULL, 'editaRegistro', NULL, 'empresa', '9', 'empresa', 'forms/view_form', NULL, NULL),
(7, 'MenuUsuario', 'Asignar Menu Usuario', 'form_menu_usuario', 'sistema', NULL, 'nuevoRegistro', '', NULL, NULL, NULL, 'forms/view_form', NULL, NULL),
(8, 'subMenuUsuario', 'Asignar Sub Menu Usuario', 'form_menu_usuario', 'sistema', NULL, 'nuevoRegistro', NULL, NULL, NULL, NULL, 'forms/view_form', NULL, NULL),
(9, 'editarPerfil', 'Editar Perfil', 'form_edita_perfil', 'sistema', NULL, 'nuevoRegistro', NULL, NULL, NULL, NULL, 'forms/view_form', NULL, NULL),
(10, 'empresaContrato', 'Asignar Contrato a Empresa', 'form_contrato_empresa', 'sistema', NULL, 'nuevoRegistro', NULL, NULL, NULL, NULL, 'forms/view_form', NULL, NULL),
(11, 'contrato', 'Gestion de Contrato', 'contrato', 'sistema', NULL, NULL, '10', NULL, '', NULL, 'forms/view_table', 'editaContrato*11', 'nuevaContrato*11'),
(12, 'nuevaContrato', 'Registro de Contratos', 'contrato', 'sistema', NULL, 'nuevoRegistro', NULL, 'contrato', '11', 'contrato', 'forms/view_form', NULL, NULL),
(13, 'editaContrato', 'Edicion de Contratos', 'contrato', 'sistema', NULL, 'editaRegistro', '', 'contrato', '11', 'contrato', 'forms/view_form', NULL, NULL),
(14, 'ciudad', 'Gestion de Ciudades', 'ciudad', 'sistema', NULL, NULL, '49', NULL, NULL, NULL, 'forms/view_table', 'editaCiudad*65', 'nuevaCiudad*65'),
(15, 'nuevaCiudad', 'Registro de Ciudades', 'ciudad', 'sistema', NULL, 'nuevoRegistro', NULL, 'ciudad', '48', 'ciudad', 'forms/view_form', NULL, NULL),
(16, 'editaCiudad', 'Edicion de Ciudad', 'ciudad', 'sistema', NULL, 'editaRegistro', NULL, 'ciudad', '48', 'ciudad', 'forms/view_form', NULL, NULL),
(17, 'mensajes', 'Nuevo Mensaje', 'form_mensaje', 'sistema', NULL, '', NULL, NULL, NULL, NULL, 'forms/view_form', NULL, NULL),
(18, 'gestionMensajes', 'Administracion de Mensajes', 'form_gesion_mensajes', 'sistema', NULL, NULL, NULL, NULL, NULL, NULL, 'forms/view_table', NULL, NULL),
(19, 'configFacturacion', 'Configuracion Facturacion', 'configFacturacion', 'facturacion', NULL, NULL, '14', NULL, NULL, NULL, 'forms/view_table', 'editaFacConfig*74', 'nuevaFacConfig*74'),
(20, 'nuevaFacConfig', 'Nueva Configuracion Facturacion', 'configFacturacion', 'facturacion', NULL, 'nuevoRegistro', NULL, 'configFacturacion', '22', 'configFacturacion', 'forms/view_form', NULL, NULL),
(21, 'editaFacConfig', 'Edicion de Configuracion de facturacion', 'configFacturacion', 'facturacion', NULL, 'editaRegistro', NULL, 'configFacturacion', '22', 'configFacturacion', 'forms/view_form', NULL, NULL),
(22, 'tipoImpuesto', 'Administracion Tipos de Impuesto', 'tipoImpuesto', 'facturacion', NULL, NULL, '15', NULL, NULL, NULL, 'forms/view_table', 'editaTipoImpuesto*75', 'nuevaTipoImpuesto*75'),
(23, 'nuevaTipoImpuesto', 'Registro de impuestos', 'tipoImpuesto', 'facturacion', NULL, 'nuevoRegistro', NULL, 'tipoImpuesto', '23', 'tipoImpuesto', 'forms/view_form', NULL, NULL),
(24, 'editaTipoImpuesto', 'Edicion de tipo de impuestos', 'tipoImpuesto', 'facturacion ', NULL, 'editaRegistro', NULL, 'tipoImpuesto', '23', 'tipoImpuesto', 'forms/view_form', NULL, NULL),
(25, 'impuestos', 'Registro de Impuesto', 'impuestos', 'facturacion', 'nuevaImpuesto', NULL, '16', NULL, NULL, NULL, 'forms/view_table', 'editaImpuesto*76', 'nuevaImpuesto*76'),
(26, 'nuevaImpuesto', 'Administracion de impuestos', 'impuestos', 'facturacion', NULL, 'nuevoRegistro', NULL, 'impuestos', '24', 'impuestos', 'forms/view_form', NULL, NULL),
(27, 'editaImpuesto', 'Edicion de Impuestos', 'impuestos', 'facturacion', NULL, 'editaRegistro', NULL, 'impuestos', '24', 'impuestos', 'forms/view_form', NULL, NULL),
(28, 'tipopago', 'Administracion de Tipos de pago', 'tipoPago', 'facturacion', 'nuevaTipoPago', NULL, '17', NULL, NULL, NULL, 'forms/view_table', 'editaTipoPago*77', 'nuevaTipoPago*77'),
(29, 'nuevaTipoPago', 'Registro de tipo pago', 'tipoPago', 'facturacion', '', 'nuevoRegistro', NULL, 'tipoPago', '25', 'tipoPago', 'forms/view_form', NULL, NULL),
(30, 'editaTipoPago', 'Edicion de Tipo pago', 'tipoPago', 'facturacion', NULL, 'editaRegistro', NULL, 'tipoPago', '25', 'tipoPago', 'forms/view_form', NULL, NULL),
(31, 'uniMedidas', 'Administracion Unidades de Medida', 'uniMedidas', 'facturacion', 'nuevaUniMedida', NULL, '18', NULL, NULL, NULL, 'forms/view_table', 'editaUniMedida*78', 'nuevaUniMedida*78'),
(32, 'nuevaUniMedida', 'Registro de Unidades de Medida', 'uniMedidas', 'facturacion', NULL, 'nuevoRegistro', NULL, 'uniMedidas', '26', 'uniMedidas', 'forms/view_form', NULL, NULL),
(33, 'editaUniMedida', 'Edicion de Unidades de Medida', 'uniMedidas', 'facturacion', NULL, 'editaRegistro', NULL, 'uniMedidas', '26', 'uniMedidas', 'forms/view_form', NULL, NULL),
(34, 'moneda', 'Administracion Monedas', 'moneda', 'facturacion', 'nuevaMoneda', NULL, '19', NULL, NULL, NULL, 'forms/view_table', 'editaMoneda*79', 'nuevaMoneda*79'),
(35, 'nuevaMoneda', 'Registro de Monedas', 'moneda', 'facturacion', NULL, 'nuevoRegistro', NULL, 'moneda', '27', 'moneda', 'forms/view_form', NULL, NULL),
(36, 'editaMoneda', 'Registro de Monedas', 'moneda', 'facturacion', NULL, 'editaRegistro', NULL, 'moneda', '27', 'moneda', 'forms/view_form', NULL, NULL),
(37, 'numeracion', 'Administracion Numeraciones', 'numeracion', 'facturacion', 'nuevaNumeracion', NULL, '20', NULL, NULL, NULL, 'forms/view_table', 'editaNumeracion*80', 'editaNumeracion*80'),
(38, 'nuevaNumeracion', 'Registro de Numeraciones', 'numeracion', 'facturacion', NULL, 'nuevoRegistro', NULL, 'numeracion', '28', 'numeracion', 'forms/view_form', NULL, NULL),
(39, 'editaNumeracion', 'Edicionde Numeraciones', 'numeracion', 'facturacion', NULL, 'editaRegistro', NULL, 'numeracion', '28', 'numeracion', 'forms/view_form', NULL, NULL),
(40, 'regimen', 'Administracion Regimen', 'regimen', 'facturacion', 'nuevaRegimen', NULL, '21', NULL, NULL, NULL, 'forms/view_table', 'editaRegimen*81', 'nuevaRegimen*81'),
(41, 'nuevaRegimen', 'Registro de Regimen', 'regimen', 'facturacion', NULL, 'nuevoRegistro', NULL, 'regimen', '29', 'regimen', 'forms/view_form', NULL, NULL),
(42, 'editaRegimen', 'Edicion de Regimen', 'regimen', 'facturacion', NULL, 'editaRegistro', NULL, 'regimen', '29', 'regimen', 'forms/view_form', NULL, NULL),
(43, 'factura', 'Administracion Facturas', 'factura', 'facturacion', 'nuevaFactura', NULL, '31', NULL, NULL, NULL, 'forms/view_table', 'editaFactura*83', 'nuevaFactura*83'),
(44, 'nuevaFactura', 'Registro de Facturas', 'factura', 'facturacion', NULL, 'nuevoRegistro', NULL, 'factura', '30', 'factura', 'forms/view_form', NULL, NULL),
(45, 'editaFactura', 'Edicion de Facturas', 'factura', 'facturacion', NULL, 'editaRegistro', NULL, 'factura', '30', 'factura', 'forms/view_form', NULL, NULL),
(46, 'item', 'Administracion Item', 'item', 'facturacion', 'nuevaItem', '', '33', NULL, NULL, NULL, 'forms/view_table', 'editaItem', 'nuevaItem'),
(47, 'nuevaItem', 'Registro de Items', 'item', 'facturacion', NULL, 'nuevoRegistro', NULL, 'item', '32', 'item', 'forms/view_form', NULL, NULL),
(48, 'editaItem', 'Edicion de Items', 'item', 'facturacion', NULL, 'editaRegistro', NULL, 'item', '32', 'item', 'forms/view_form', NULL, NULL),
(49, 'inventario', 'Administracion Inventario', 'inventario', 'facturacion', 'nuevaInventario', NULL, '35', NULL, NULL, NULL, 'forms/view_table', 'editaInventario', 'nuevaInventario'),
(50, 'nuevaInventario', 'Registro de Inventario', 'inventario', 'facturacion', NULL, 'nuevoRegistro', NULL, 'inventario', '34', 'inventario', 'forms/view_form', NULL, NULL),
(51, 'editaInventario', 'Edicion de Inventarios', 'inventario', 'facturacion', NULL, 'editaRegistro', NULL, 'inventario', '34', 'inventario', 'forms/view_form', NULL, NULL),
(52, 'cliente', 'Administracion cliente', 'cliente', 'facturacion', 'nuevaCliente', NULL, '37', NULL, '', NULL, 'forms/view_table', 'editaCliente*86', 'nuevaCliente*86'),
(53, 'nuevaCliente', 'Registro de Clientes', 'cliente', 'facturacion', NULL, 'nuevoRegistro', NULL, 'cliente', '36', 'cliente', 'forms/view_form', NULL, NULL),
(54, 'editaCliente', 'Edicion de Clientes', 'cliente', 'facturacion', NULL, 'editaRegistro', NULL, 'cliente', '36', 'cliente', 'forms/view_form', NULL, NULL),
(55, 'estado', 'Gestion de Estados/Actuaciones', 'estado', 'sistema', 'nuevaEstado', NULL, '39', NULL, NULL, NULL, 'forms/view_table', 'editaEstado*66', 'nuevaEstado*66'),
(56, 'nuevaEstado', 'Registro de Estados/Actuaciones', 'estado', 'sistema', NULL, 'nuevoRegistro', NULL, 'estado', '38', 'estado', 'forms/view_form', NULL, NULL),
(57, 'editaEstado', 'Edicion de Estados/Actuaciones', 'estado', 'sistema', NULL, 'editaRegistro', NULL, 'estado', '38', 'estado', 'forms/view_form', NULL, NULL),
(58, 'perfil', 'Gestion de Perfiles/Roles', 'perfil', 'sistema', 'nuevaPerfil', NULL, '51', NULL, NULL, NULL, 'forms/view_table', 'editaPerfil*67', 'nuevaPerfil*67'),
(59, 'nuevaPerfil', 'Registro de Perfiles/Roles', 'perfil', 'sistema', NULL, 'nuevoRegistro', NULL, 'perfil', '50', 'perfil', 'forms/view_form', NULL, NULL),
(60, 'editaPerfil', 'Edicion de Perfiles/Roles', 'perfil', 'sistema', NULL, 'editaRegistro', NULL, 'perfil', '50', 'perfil', 'forms/view_form', NULL, NULL),
(61, 'metodo', 'Gestion de Metodos', 'metodo', 'sistema', 'nuevaMetodo', NULL, '53', NULL, NULL, NULL, 'forms/view_table', 'editaMetodo*68', 'nuevaMetodo*68'),
(62, 'nuevaMetodo', 'Registro de Metodos', 'metodo', 'sistema', NULL, 'nuevoRegistro', NULL, 'metodo', '52', 'metodo', 'forms/view_form', NULL, NULL),
(63, 'editaMetodo', 'Edicion de Metodos', 'metodo', 'sistema', NULL, 'editaRegistro', NULL, 'metodo', '52', 'metodo', 'forms/view_form', NULL, NULL),
(64, 'perfilMetodo', 'Gestion de Perfil Vs Metodos', 'perfilMetodo', 'sistema', 'nuevaPerfilMetodo', NULL, '55', NULL, NULL, NULL, 'forms/view_table', 'editaPerfilMetodo*69', 'nuevaPerfilMetodo*69'),
(65, 'nuevaPerfilMetodo', 'Registro de Perifl Vs Metodos', 'perfilMetodo', 'sistema', NULL, 'nuevoRegistro', NULL, 'perfilMetodo', '54', 'perfilMetodo', 'forms/view_form', NULL, NULL),
(66, 'editaPerfilMetodo', 'Edicion de Perfil Vs Metodos', 'perfilMetodo', 'sistema', NULL, 'editaRegistro', NULL, 'perfilMetodo', '54', 'perfilMetodo', 'forms/view_form', NULL, NULL),
(67, 'formulario', 'Gestion de Formularios', 'formulario', 'sistema', 'nuevaFormulario', NULL, '57', NULL, NULL, NULL, 'forms/view_table', 'editaFormulario*70', 'nuevaFormulario*70'),
(68, 'nuevaFormulario', 'Registro de Formularios', 'formulario', 'sistema', NULL, 'nuevoRegistro', NULL, 'formulario', '56', 'formulario', 'forms/view_form', NULL, NULL),
(69, 'editaFormulario', 'Edicion de Formularios', 'formulario', 'sistema', NULL, 'editaRegistro', NULL, 'formulario', '56', 'formulario', 'forms/view_form', NULL, NULL),
(70, 'formularioMetodo', 'Gestion de Formularios Vs Metodos', 'formularioMetodo', 'sistema', 'nuevaFormularioMetodo', NULL, '59', NULL, NULL, NULL, 'forms/view_table', 'editaFormularioMetodo*71', 'nuevaFormularioMetodo*71'),
(71, 'nuevaFormularioMetodo', 'Registro de Formularios Vs Metodo', 'formularioMetodo', 'sistema', NULL, 'nuevoRegistro', NULL, 'formularioMetodo', '58', 'formularioMetodo', 'forms/view_form', NULL, NULL),
(72, 'editaFormularioMetodo', 'Edicion de Formularios Vs Metodo', 'formularioMetodo', 'sistema', NULL, 'editaRegistro', NULL, 'formularioMetodo', '58', 'formularioMetodo', 'forms/view_form', NULL, NULL),
(73, 'frame', 'Gestion de Frames', 'frame', 'sistema', 'nuevaFrame', NULL, '61', NULL, NULL, NULL, 'forms/view_table', 'editaFrame*72', 'nuevaFrame*72'),
(74, 'nuevaFrame', 'Registro de Frames', 'frame', 'sistema', NULL, 'nuevoRegistro', NULL, 'frame', '60', 'frame', 'forms/view_form', NULL, NULL),
(75, 'editaFrame', 'Edicion de Frames', 'frame', 'sistema', NULL, 'editaRegistro', NULL, 'frame', '60', 'frame', 'forms/view_form', NULL, NULL),
(76, 'detframe', 'Gestion de Detalle de Frames', 'detframe', 'sistema', 'nuevaDetFrame', NULL, '63', NULL, NULL, NULL, 'forms/view_table', 'editaDetFrame*73', 'nuevaDetFrame*73'),
(77, 'nuevaDetFrame', 'Registro de Detalel de Frames', 'detframe', 'sistema', NULL, 'nuevoRegistro', NULL, 'detframe', '62', 'detframe', 'forms/view_form', NULL, NULL),
(78, 'editaDetFrame', 'Edicion de Detalel de Frames', 'detframe', 'sistema', NULL, 'editaRegistro', NULL, 'detframe', '62', 'detframe', 'forms/view_form', NULL, NULL),
(79, 'descuento', 'Administracion de descuentos', 'descuento', 'facturacion', 'nuevaDescuento', NULL, '41', NULL, NULL, NULL, 'forms/view_table', 'editaDescuento*82', 'nuevaDescuento*82'),
(80, 'nuevaDescuento', 'Registro de descuentos', 'descuento', 'facturacion', NULL, 'nuevoRegistro', NULL, 'descuento', '40', 'descuento', 'forms/view_form', NULL, NULL),
(81, 'editaDescuento', 'Edicion de descuentos', 'descuento', 'facturacion', NULL, 'editaRegistro', NULL, 'descuento', '40', 'descuento', 'forms/view_form', NULL, NULL),
(82, 'cuenta', 'Administracion de cuentas contables', 'cuenta', 'contabilidad', 'nuevaCuenta', NULL, '43', NULL, NULL, NULL, 'forms/view_table', 'editaCenta', 'nuevaCuenta'),
(83, 'nuevaCuenta', 'Registro de cuentas contables', 'cuenta', 'contabilidad', NULL, 'nuevoRegistro', NULL, 'cuenta', '42', 'cuenta', 'forms/view_form', NULL, NULL),
(84, 'editaCuenta', 'Edicion de Cuentas', 'cuenta', 'contabilidad', NULL, 'editaRegistro', NULL, 'cuenta', '42', 'cuenta', 'forms/view_form', NULL, NULL),
(85, 'empresaVsContrato', 'Administracion Empresa Vs Contrato', 'empresaVsContrato', 'sistema', 'nuevaEmpresaVsContrato', NULL, '7', NULL, NULL, NULL, 'forms/view_table', 'editaEmpresaVsContrato*7', 'nuevaEmpresaVsContrato*7'),
(86, 'nuevaEmpresaVsContrato', 'Registro de Empresa Vs Contrato', 'empresaVsContrato', 'sistema', NULL, 'nuevoRegistro', '', 'empresaVsContrato', '92', 'empresaVsContrato', 'forms/view_form', NULL, NULL),
(87, 'editaEmpresaVsContrato', 'Edicion de Empresa Vs Contrato', 'empresaVsContrato', 'sistema', NULL, 'editaRegistro', NULL, 'empresaVsContrato', '92', 'empresaVsContrato', 'forms/view_form', NULL, NULL),
(88, 'login', 'Inicio de Sesi&oacute;n', 'inicioSesion', 'sistema', NULL, 'login', '', NULL, NULL, NULL, 'forms/view_login', NULL, NULL),
(89, 'index', 'Dashboard', 'form_usuario', 'sistema', NULL, NULL, NULL, NULL, NULL, NULL, 'forms/view_dashboard', NULL, NULL),
(90, 'facturacion', 'Administracion Facturacion', 'facturacion', 'facturacion', 'nuevaFacturacion', NULL, '95', NULL, NULL, NULL, 'forms/view_table', 'editaFacturacion*85', 'nuevaFacturacion*85'),
(91, 'nuevaFacturacion', 'Registro de Facturacion', 'facturacion', 'facturacion', NULL, 'nuevoRegistro', NULL, 'facturacion', '94', 'facturacion', 'forms/view_form', NULL, NULL),
(92, 'editaFacturacion', 'Edicion de Facturacion', 'facturacion', 'facturacion', NULL, 'editaRegistro', NULL, 'facturacion', '94', 'facturacion', 'forms/view_form', NULL, NULL),
(93, 'orden', 'Administracion Ordenes de Trabajo', 'orden', 'facturacion', 'nuevaOrden', NULL, '99', NULL, NULL, NULL, 'forms/view_table', 'editaOrden*91', 'nuevaOrden*91'),
(94, 'nuevaOrden', 'Registro de Facturacion', 'orden', 'facturacion', NULL, 'nuevoRegistro', NULL, 'orden', '98', 'orden', 'forms/view_form', NULL, NULL),
(95, 'editaOrden', 'Edicion de Facturacion', 'orden', 'facturacion', NULL, 'editaRegistro', NULL, 'orden', '98', 'orden', 'forms/view_form', NULL, NULL),
(96, 'claseVehiculo', 'Administracion Clases de vehiculos', 'claseVehiculo', 'huella', 'nuevaClaseVehiculo', NULL, '101', NULL, NULL, NULL, 'forms/view_table', 'editaClaseVehiculo*93', 'nuevaClaseVehiculo*93'),
(97, 'nuevaClaseVehiculo', 'Registro de Clase Vehiculo', 'claseVehiculo', 'huella', NULL, 'nuevoRegistro', NULL, 'claseVehiculo', '100', 'claseVehiculo', 'forms/view_form', NULL, NULL),
(98, 'editaClaseVehiculo', 'Edicion de Clase Vehiculo', 'claseVehiculo', 'huella', NULL, 'editaRegistro', NULL, 'claseVehiculo', '100', 'claseVehiculo', 'forms/view_form', NULL, NULL),
(99, 'tipoServicio', 'Administracion Tipo Servicio', 'tipoServicio', 'huella', 'nuevaTipoServicio', NULL, '103', NULL, NULL, NULL, 'forms/view_table', 'editaTipoServicio*95', 'nuevaTipoServicio*95'),
(100, 'nuevaTipoServicio', 'Registro de Tipo Servicio', 'tipoServicio', 'huella', NULL, 'nuevoRegistro', NULL, 'tipoServicio', '102', 'tipoServicio', 'forms/view_form', NULL, NULL),
(101, 'editaTipoServicio', 'Edicion de Tipo Servicio', 'tipoServicio', 'huella', NULL, 'editaRegistro', NULL, 'tipoServicio', '102', 'tipoServicio', 'forms/view_form', NULL, NULL),
(102, 'tipoDocumento', 'Administracion Tipos Documento', 'tipoDocumento', 'huella', 'nuevaTipoDocumento', NULL, '105', NULL, NULL, NULL, 'forms/view_table', 'editaTipoDocumento*97', 'nuevaTipoDocumento*97'),
(103, 'nuevaTipoDocumento', 'Registro de Tipos Documento', 'tipoDocumento', 'huella', NULL, 'nuevoRegistro', NULL, 'tipoDocumento', '104', 'tipoDocumento', 'forms/view_form', NULL, NULL),
(104, 'editaTipoDocumento', 'Edicion de Tipos Documento', 'tipoDocumento', 'huella', NULL, 'editaRegistro', NULL, 'tipoDocumento', '104', 'tipoDocumento', 'forms/view_form', NULL, NULL),
(105, 'combustible', 'Administracion Tipos de Combustible', 'combustible', 'huella', 'nuevaCombustible', NULL, '107', NULL, NULL, NULL, 'forms/view_table', 'editaCombustible*99', 'nuevaCombustible*99'),
(106, 'nuevaCombustible', 'Registro Tipos de Combustible', 'combustible', 'huella', NULL, 'nuevoRegistro', NULL, 'combustible', '106', 'combustible', 'forms/view_form', NULL, NULL),
(107, 'editaCombustible', 'Edicion Tipos de Combustible', 'combustible', 'huella', NULL, 'editaRegistro', NULL, 'combustible', '106', 'combustible', 'forms/view_form', NULL, NULL),
(108, 'vehiculo', 'Administracion  de Vehiculos', 'vehiculo', 'huella', 'nuevaVehiculo', NULL, '109', NULL, NULL, NULL, 'forms/view_table', 'editaVehiculo*101', 'nuevaVehiculo*101'),
(109, 'nuevaVehiculo', 'Registro de Vehiculos', 'vehiculo', 'huella', NULL, 'nuevoRegistro', NULL, 'vehiculo', '108', 'vehiculo', 'forms/view_form', NULL, NULL),
(110, 'editaVehiculo', 'Edicion de Vehiculos', 'vehiculo', 'huella', NULL, 'editaRegistro', NULL, 'vehiculo', '108', 'vehiculo', 'forms/view_form', NULL, NULL),
(111, 'clienteHue', 'Administracion Clientes', 'clienteHue', 'huella', 'nuevaClienteHue', NULL, '111', NULL, NULL, NULL, 'forms/view_table', 'editaClienteHue*103', 'nuevaClienteHue*103'),
(112, 'nuevaClienteHue', 'Registro de Clientes', 'clienteHue', 'huella', NULL, 'nuevoRegistro', NULL, 'clienteHue', '110', 'clienteHue', 'forms/view_form', NULL, NULL),
(113, 'editaClienteHue', 'Edicion de Clientes', 'clienteHue', 'huella', NULL, 'editaRegistro', NULL, 'clienteHue', '110', 'clienteHue', 'forms/view_form', NULL, NULL),
(114, 'clienteVehiculo', 'Administracion Alquileres', 'clienteVehiculo', 'huella', 'nuevaClienteVehiculo', NULL, '113', NULL, NULL, NULL, 'forms/view_table', 'editaClienteVehiculo*105', 'nuevaClienteVehiculo*105'),
(115, 'nuevaClienteVehiculo', 'Registro de Alquiler', 'clienteVehiculo', 'huella', NULL, 'nuevoRegistro', NULL, 'clienteVehiculo', '112', 'clienteVehiculo', 'forms/view_form', NULL, NULL),
(116, 'editaClienteVehiculo', 'Edicion de Alquiler', 'clienteVehiculo', 'huella', NULL, 'editaRegistro', NULL, 'clienteVehiculo', '112', 'clienteVehiculo', 'forms/view_form', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_formulario_metodos`
--

CREATE TABLE IF NOT EXISTS `sys_formulario_metodos` (
  `cod_formulario_metodos` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK DE LA TABLA AUTOINCREMENTO',
  `cod_formulario` int(9) NOT NULL COMMENT 'CODIGO DEL FORMULARIO',
  `cod_metodos` int(4) NOT NULL COMMENT 'CODIGO DEL METODO',
  PRIMARY KEY (`cod_formulario_metodos`),
  KEY `fk_frmet_frm_idx` (`cod_formulario`),
  KEY `fk_frmet_met_idx` (`cod_metodos`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS METODOS DE LOS FORMULARIOS' AUTO_INCREMENT=325 ;

--
-- Volcado de datos para la tabla `sys_formulario_metodos`
--

INSERT INTO `sys_formulario_metodos` (`cod_formulario_metodos`, `cod_formulario`, `cod_metodos`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 2),
(5, 4, 1),
(6, 4, 2),
(7, 4, 5),
(8, 5, 1),
(9, 5, 2),
(10, 5, 5),
(11, 6, 3),
(12, 6, 4),
(13, 7, 1),
(14, 7, 2),
(15, 8, 1),
(16, 8, 2),
(17, 9, 3),
(18, 9, 4),
(19, 10, 1),
(20, 10, 2),
(21, 11, 3),
(22, 11, 4),
(23, 12, 1),
(24, 12, 2),
(25, 14, 1),
(26, 14, 2),
(27, 15, 1),
(28, 15, 2),
(29, 16, 1),
(30, 16, 2),
(31, 17, 1),
(32, 17, 2),
(33, 18, 1),
(34, 18, 2),
(35, 19, 1),
(36, 19, 2),
(37, 20, 1),
(38, 20, 2),
(39, 21, 1),
(40, 21, 2),
(41, 22, 2),
(42, 22, 3),
(43, 22, 4),
(44, 22, 6),
(45, 14, 7),
(46, 23, 3),
(47, 23, 4),
(48, 23, 6),
(49, 15, 7),
(50, 16, 7),
(51, 24, 3),
(52, 24, 4),
(53, 24, 6),
(54, 17, 7),
(55, 25, 3),
(56, 25, 4),
(57, 25, 6),
(66, 18, 7),
(67, 19, 7),
(68, 26, 3),
(69, 26, 4),
(70, 26, 6),
(71, 27, 3),
(72, 27, 4),
(73, 27, 6),
(78, 20, 7),
(79, 28, 3),
(80, 28, 4),
(81, 28, 6),
(82, 21, 7),
(83, 29, 3),
(84, 29, 4),
(85, 29, 6),
(86, 30, 3),
(87, 30, 4),
(88, 30, 6),
(89, 31, 1),
(90, 31, 2),
(91, 31, 8),
(93, 32, 3),
(94, 32, 4),
(95, 32, 6),
(96, 33, 1),
(97, 33, 2),
(98, 33, 7),
(99, 34, 3),
(100, 34, 4),
(101, 34, 6),
(102, 35, 1),
(103, 35, 2),
(104, 35, 7),
(105, 36, 3),
(106, 36, 4),
(107, 36, 6),
(108, 37, 1),
(109, 37, 2),
(111, 37, 8),
(112, 37, 7),
(114, 31, 9),
(117, 31, 7),
(118, 38, 3),
(119, 38, 4),
(120, 38, 6),
(121, 39, 1),
(122, 39, 2),
(123, 39, 7),
(124, 37, 9),
(125, 40, 3),
(126, 40, 4),
(127, 40, 6),
(128, 41, 1),
(129, 41, 2),
(130, 41, 7),
(133, 30, 10),
(134, 30, 11),
(135, 44, 3),
(136, 44, 4),
(137, 44, 6),
(138, 45, 1),
(139, 45, 2),
(140, 45, 7),
(141, 46, 3),
(142, 46, 4),
(143, 46, 6),
(144, 47, 1),
(145, 47, 2),
(146, 47, 7),
(147, 6, 6),
(148, 1, 7),
(149, 9, 6),
(150, 2, 7),
(151, 11, 6),
(152, 10, 7),
(153, 49, 1),
(154, 49, 2),
(155, 49, 7),
(156, 48, 3),
(157, 48, 4),
(158, 48, 6),
(159, 51, 1),
(160, 51, 2),
(161, 51, 7),
(162, 50, 3),
(163, 50, 4),
(164, 50, 6),
(165, 53, 1),
(166, 53, 2),
(167, 53, 7),
(168, 52, 3),
(169, 52, 4),
(170, 52, 6),
(171, 55, 1),
(172, 55, 2),
(173, 55, 7),
(174, 54, 3),
(175, 54, 4),
(176, 54, 6),
(177, 57, 1),
(178, 57, 2),
(179, 57, 7),
(180, 56, 3),
(181, 56, 4),
(182, 56, 6),
(183, 59, 1),
(184, 59, 2),
(185, 59, 7),
(186, 58, 3),
(187, 58, 4),
(188, 58, 6),
(189, 60, 6),
(190, 60, 4),
(191, 60, 3),
(192, 61, 7),
(193, 61, 2),
(194, 61, 1),
(195, 62, 6),
(196, 62, 3),
(197, 62, 4),
(198, 63, 1),
(199, 63, 2),
(200, 63, 7),
(201, 64, 3),
(202, 64, 4),
(203, 64, 6),
(204, 65, 1),
(205, 65, 2),
(206, 65, 7),
(207, 78, 3),
(208, 78, 4),
(209, 78, 6),
(210, 79, 1),
(211, 79, 2),
(212, 79, 7),
(213, 66, 3),
(214, 66, 4),
(215, 66, 6),
(216, 67, 1),
(217, 67, 2),
(218, 67, 7),
(225, 68, 3),
(226, 68, 4),
(227, 68, 6),
(228, 69, 1),
(229, 69, 2),
(230, 69, 7),
(231, 90, 3),
(232, 90, 4),
(233, 90, 6),
(234, 91, 1),
(235, 91, 2),
(236, 91, 7),
(237, 92, 3),
(238, 92, 4),
(239, 92, 6),
(240, 7, 7),
(241, 63, 8),
(242, 63, 9),
(243, 70, 3),
(244, 70, 4),
(245, 70, 6),
(246, 71, 1),
(247, 71, 2),
(248, 71, 7),
(249, 72, 3),
(250, 72, 4),
(251, 73, 1),
(252, 73, 2),
(253, 73, 7),
(254, 76, 3),
(255, 76, 4),
(256, 77, 1),
(257, 77, 2),
(258, 77, 7),
(259, 94, 3),
(260, 94, 4),
(261, 94, 6),
(262, 94, 10),
(263, 94, 11),
(264, 95, 1),
(265, 95, 2),
(266, 95, 8),
(267, 95, 9),
(268, 95, 7),
(269, 70, 10),
(270, 70, 11),
(271, 36, 10),
(272, 98, 3),
(273, 98, 4),
(274, 98, 6),
(275, 99, 1),
(276, 99, 2),
(277, 99, 7),
(278, 100, 3),
(279, 100, 4),
(280, 100, 6),
(281, 101, 1),
(282, 101, 2),
(283, 101, 7),
(284, 102, 3),
(285, 102, 4),
(286, 102, 6),
(287, 103, 1),
(288, 103, 2),
(289, 103, 7),
(290, 104, 3),
(291, 104, 4),
(292, 104, 6),
(293, 105, 1),
(294, 105, 2),
(295, 105, 7),
(296, 106, 3),
(297, 106, 4),
(298, 106, 6),
(299, 107, 1),
(300, 107, 2),
(301, 107, 7),
(302, 108, 3),
(303, 108, 4),
(304, 108, 6),
(305, 108, 10),
(307, 109, 1),
(308, 109, 2),
(309, 109, 8),
(310, 109, 9),
(311, 109, 7),
(312, 110, 3),
(313, 110, 4),
(314, 110, 6),
(315, 111, 1),
(316, 111, 2),
(317, 111, 7),
(318, 112, 3),
(319, 112, 4),
(320, 112, 6),
(321, 113, 1),
(322, 113, 2),
(323, 113, 7),
(324, 113, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_frame`
--

CREATE TABLE IF NOT EXISTS `sys_frame` (
  `cod_frame` int(8) NOT NULL AUTO_INCREMENT COMMENT 'AUTOINCREMENTO DE LA TABLA PK',
  `nom_frame` varchar(45) NOT NULL COMMENT 'NOMBRE DEL FORMULARIO',
  `nom_tabla` varchar(45) DEFAULT NULL,
  `id_frame` int(1) NOT NULL DEFAULT '1',
  `fec_frame` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `hora_frame` varchar(45) DEFAULT NULL COMMENT 'HORA DE REGISTRO',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DE USUARIO QUE REGISTRA',
  `cod_formulario` int(8) NOT NULL,
  `ind_enlinea` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cod_frame`),
  KEY `fk_frame_usuario_idx` (`cod_usuario`),
  KEY `fk_frame_form_idx` (`cod_formulario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS FORMULARIOS DE LAS VISTAS SEGUN EL CONTROLLER' AUTO_INCREMENT=113 ;

--
-- Volcado de datos para la tabla `sys_frame`
--

INSERT INTO `sys_frame` (`cod_frame`, `nom_frame`, `nom_tabla`, `id_frame`, `fec_frame`, `hora_frame`, `cod_usuario`, `cod_formulario`, `ind_enlinea`) VALUES
(1, 'Datos Basicos', 'usuario', 6, NULL, NULL, 1, 1, 0),
(2, 'Perfil de Usuario', 'sys_usuario_perfil', 6, NULL, NULL, 1, 1, 0),
(3, 'Datos Empresa', 'sys_empresa', 6, NULL, NULL, 1, 2, 0),
(4, 'Otros Datos', 'sys_empresa', 6, NULL, NULL, 1, 2, 0),
(5, 'Menu/Sub Menu Asociado', 'sys_usuario_menu', 6, NULL, NULL, 1, 1, 0),
(8, 'Empresas Asociadas al Usuario', 'sys_usuario_empresa', 6, NULL, NULL, 1, 1, 0),
(9, 'Asignar menu a usuario', 'sys_usuario_menu', 6, NULL, NULL, 1, 4, 0),
(10, 'Asignar Submenu a usuario', 'sys_usuario_menu_sub', 6, NULL, NULL, 1, 5, 0),
(11, 'Contrato de la Empresa', 'sys_empresa_contrato', 6, NULL, NULL, 1, 7, 0),
(12, 'Editar Perfil', 'sys_usuario', 7, NULL, NULL, 1, 8, 0),
(13, 'Datos del Contrato', 'sys_contrato', 7, NULL, NULL, 1, 10, 0),
(14, 'Escribir mensaje', 'sys_mensajes', 12, NULL, NULL, 1, 12, 0),
(15, 'Datos Basicos Facturacion', 'fa_config', 6, NULL, NULL, 1, 14, 0),
(16, 'Datos Internos Estabilizacion', 'fa_config', 6, NULL, NULL, 1, 14, 0),
(17, 'Tipo de Impuestos aplicables', 'fa_tipoimpuesto', 6, NULL, NULL, 1, 15, 0),
(18, 'Detalle del Impuesto', 'fa_impuesto', 7, NULL, NULL, 1, 16, 0),
(19, 'Detalle de los tipos de pago', 'fa_tipopago', 12, NULL, NULL, 1, 17, 0),
(20, 'Unidades de medida para Inventario', 'fa_unimedidad', 7, NULL, NULL, 1, 18, 0),
(21, 'Monedas', 'fa_moneda', 7, NULL, NULL, 1, 19, 0),
(22, 'Numeraciones Facturacion', 'fa_numeracion', 7, NULL, NULL, 1, 20, 0),
(23, 'Configuracion', 'fa_numeracion', 5, NULL, NULL, 1, 20, 0),
(24, 'Regimen', 'fa_regimen', 7, NULL, NULL, 1, 21, 0),
(26, 'Nueva Factura de Venta', 'fa_factura', 6, NULL, NULL, 1, 31, 0),
(27, 'Otros Datos', 'fa_factura', 6, NULL, NULL, 1, 31, 0),
(28, 'Items', 'fa_factura', 12, NULL, NULL, 1, 31, 1),
(30, 'Items', 'fa_item', 6, NULL, NULL, 1, 33, 0),
(31, 'Inventario', 'fa_inventario', 6, NULL, NULL, 1, 35, 0),
(32, 'Datos Basicos', 'fa_cliente', 6, NULL, NULL, 1, 37, 0),
(33, 'Otros Datos', 'fa_cliente', 6, NULL, NULL, 1, 37, 0),
(34, 'Adicionar Cliente asociado', 'fa_cliente_asociado', 12, NULL, NULL, 1, 37, 1),
(35, 'Registrar estados y Actuaciones', 'sys_estado', 8, NULL, NULL, 1, 39, 0),
(36, 'Datos del Descuento', 'fa_descuentos', 7, NULL, NULL, 1, 41, 0),
(37, 'Datos del restaurante', 'lc_restaurantes', 6, NULL, NULL, 1, 45, 0),
(38, 'Redes Sociales y otros', 'lc_restaurantes', 6, NULL, NULL, 1, 45, 0),
(39, 'Datos de la Imagen', 'lc_restaurantes_img', 7, NULL, NULL, 1, 47, 0),
(40, 'Configuracion', 'lc_restaurantes_img', 5, NULL, NULL, 1, 47, 0),
(41, 'Ciudades', 'sys_ciudad', 7, NULL, NULL, 1, 49, 0),
(42, 'Perfiles del sistema', 'sys_perfil', 7, NULL, NULL, 1, 51, 0),
(43, 'Metodos del sistema', 'sys_metodos', 6, NULL, NULL, 1, 53, 0),
(44, 'Otros Datos', 'sys_metodos', 6, NULL, NULL, 1, 53, 0),
(45, 'Perfiles Vs Metodos', 'sys_perfil_metodos', 7, NULL, NULL, 1, 55, 0),
(46, 'Formularios del Sistema', 'sys_formulario', 7, NULL, NULL, 1, 57, 0),
(47, 'Metodos Vs Formularios', 'sys_formulario_metodos', 7, NULL, NULL, 1, 59, 0),
(48, 'Frames', 'sys_frame', 7, NULL, NULL, 1, 61, 0),
(49, 'Detalle Frames', 'sys_detframe', 12, NULL, NULL, 1, 63, 1),
(50, 'Detalle de la Carta', 'lc_carta', 7, NULL, NULL, 1, 65, 0),
(51, 'Configuracion', 'lc_carta', 5, NULL, NULL, 1, 65, 0),
(52, 'Naturaleza Juridica', 'dat_naturaleza_juridica', 7, NULL, NULL, 1, 79, 0),
(53, 'Detalle Categorias', 'lc_categorias', 7, NULL, NULL, 1, 67, 0),
(54, 'Detalle Sub Categorias', 'lc_categorias_sub', 7, NULL, NULL, 1, 69, 0),
(90, 'Restaurantes Vs Sub Categorias', 'lc_restaurantes_categoria_sub', 7, NULL, NULL, 1, 91, 0),
(91, 'Catos Basicos', 'lc_cliente', 6, NULL, NULL, 1, 71, 0),
(92, 'Configuracion y otros', 'lc_cliente', 6, NULL, NULL, 1, 71, 0),
(93, 'Comentarios', 'lc_comentario', 6, NULL, NULL, 1, 73, 0),
(94, 'Palabras Clave', 'lc_palabrasclave', 7, NULL, NULL, 1, 77, 0),
(95, 'Nueva Factura de Venta', 'fa_factura', 6, NULL, NULL, 1, 95, 0),
(96, 'Otros Datos', 'fa_factura', 6, NULL, NULL, 1, 95, 0),
(97, 'Items', 'fa_factura', 12, NULL, NULL, 1, 95, 1),
(98, 'Datos de Orden de Compra', 'fa_orden', 7, NULL, NULL, 1, 99, 0),
(99, 'Datos de Clase de vehiculo', 'hue_vehiculo_clase', 7, NULL, NULL, 1, 101, 0),
(100, 'Datos tipo de Servicio', 'hue_tip_servicio', 7, NULL, NULL, 1, 103, 0),
(101, 'Datos Tipo de Documento', 'hue_tip_documento', 7, NULL, NULL, 1, 105, 0),
(102, 'Datos Tipo de Combustible', 'hue_combustible', 7, NULL, NULL, 1, 107, 0),
(103, 'Datos Basicos', 'hue_vehiculo', 6, NULL, NULL, 1, 109, 0),
(104, 'Generalidades del Vehiculo', 'hue_vehiculo', 6, NULL, NULL, 1, 109, 0),
(105, 'Informacion General', 'hue_vehiculo', 6, NULL, NULL, 1, 109, 0),
(106, 'Informacion de consideracion', 'hue_vehiculo', 6, NULL, NULL, 1, 109, 0),
(107, 'Documentos Asociados', 'hue_veniculo', 12, NULL, NULL, 1, 109, 1),
(109, 'Datos Basicos', 'hue_cliente', 6, NULL, NULL, 1, 111, 0),
(110, 'Otros Datos', 'hue_cliente', 6, NULL, NULL, 1, 111, 0),
(111, 'Datos Generales', 'hue_cliente_vehiculo', 6, NULL, NULL, 1, 113, 0),
(112, 'Otros Datos', 'hue_cliente_vehiculo', 6, NULL, NULL, 1, 113, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_mensajes`
--

CREATE TABLE IF NOT EXISTS `sys_mensajes` (
  `cod_mensajes` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK AUTOINCREMENTO DE LA TABLA',
  `a_cod_usuario` int(8) NOT NULL COMMENT 'A QUIEN VA DIRIGIDO EL MENSAJE',
  `de_cod_usuario` int(8) NOT NULL COMMENT 'QUIEN ESCRIBE EL MENSAJE',
  `asu_mensajes` varchar(120) DEFAULT NULL COMMENT 'ASUNTO DEL MENSAJE',
  `des_mensajes` varchar(1000) DEFAULT NULL COMMENT 'DESCRIPCION DEL MENSAJE',
  `img_mensajes` varchar(150) DEFAULT NULL COMMENT 'IMAGEN ADJUNTA CON EL MENSAJE',
  `fec_mensajes` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO',
  `hora_mensajes` time DEFAULT NULL COMMENT 'HORA DE REGISTRO',
  `cod_estado` varchar(3) NOT NULL DEFAULT '0' COMMENT 'INDICA EL ESTADO DEL MENSAJE',
  PRIMARY KEY (`cod_mensajes`),
  KEY `fk_msj_usu_idx` (`a_cod_usuario`),
  KEY `fk_msj_usu_1_idx` (`de_cod_usuario`),
  KEY `fk_msj_est_idx` (`cod_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALAMCENAR LOS MENSAJES INTERNOS DE LA APP' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_menu`
--

CREATE TABLE IF NOT EXISTS `sys_menu` (
  `cod_menu` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DEL MENU PADRE',
  `nom_menu` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL MENU PADRE',
  `des_menu` varchar(120) DEFAULT NULL COMMENT 'DESCRIPCION DEL MENU PADRE',
  `met_menu` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL METODO QUE EJECUTA EN EL CONTROLLER',
  `icon_menu` varchar(45) DEFAULT NULL,
  `tip_menu` varchar(45) DEFAULT NULL COMMENT 'TIPO DE MENU (D:DESPLEGABLE, E:ESTATICO)',
  `cod_modulo` int(4) DEFAULT NULL,
  PRIMARY KEY (`cod_menu`),
  KEY `fk_mod_menu_idx` (`cod_modulo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA DE MENU CABECERA O PADRE' AUTO_INCREMENT=17 ;

--
-- Volcado de datos para la tabla `sys_menu`
--

INSERT INTO `sys_menu` (`cod_menu`, `nom_menu`, `des_menu`, `met_menu`, `icon_menu`, `tip_menu`, `cod_modulo`) VALUES
(1, 'Sys Usuarios', 'Menu para la administracion de usuarios del sistema', '', 'fa fa-lg fa-fw fa-user', 'D', 1),
(2, 'Sys Empresa', 'Menu para la administracion de las Empresas', '', 'fa fa-lg fa-fw fa-windows', 'D', 1),
(3, 'Sys Contrato', 'Menu para la administracion de los contratos', '', 'fa fa-lg fa-fw fa-reorder', 'D', 1),
(4, 'Sys Mensajes', 'Menu para la administracion de la bandeja de entrada de mensajes internos', '', 'fa fa-lg fa-fw fa-inbox', 'D', 1),
(5, 'Sys Configuracion', 'Menu para la configuracion del sistema', '', 'fa fa-lg fa-fw fa-wrench', 'D', 1),
(9, 'Fa Configuracion', 'Menu para la configuracion de la facturacion', '', 'fa fa-lg fa-fw fa-wrench', 'D', 2),
(10, 'no aplica', 'menu invisible para las opciones fuera del menu', ' ', NULL, 'E', 1),
(11, 'Fa Facturacion', 'menu para la gestion de facturacion', '', 'fa fa-lg fa-fw fa-money', 'D', 2),
(12, 'Fa Inventario', 'menu para la gesion del inventario', '', 'fa fa-lg fa-fw fa-list-alt', 'D', 2),
(13, 'Co Contabilidad', 'menu para la gesion contable de la empresa', '', 'fa fa-lg fa-fw fa-bar-chart-o', 'D', 3),
(15, 'Hd Help Desk', 'menu para la gestion de servicios', ' ', 'fa fa-lg fa-fw fa-puzzle-piece', 'D', 5),
(16, 'Rv Renta Vehiculo', 'menu para la gestion del alquiler de vehiculos', ' ', 'fa fa-lg fa-fw fa-circle-o', 'D', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_menu_sub`
--

CREATE TABLE IF NOT EXISTS `sys_menu_sub` (
  `cod_menu_sub` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DEL SUB MENU',
  `nom_menu_sub` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL SUBMENU QUE APARECERA EN LA VISTA DE MENU',
  `des_menu_sub` varchar(120) DEFAULT NULL COMMENT 'DESCRIPCION DEL SUBMENU',
  `met_menu_sub` varchar(100) DEFAULT NULL,
  `icon_menu_sub` varchar(45) DEFAULT NULL COMMENT 'ICONO DEL MENU',
  `ind_header` int(1) DEFAULT '0',
  `bg_color` varchar(45) DEFAULT NULL,
  `cod_menu` int(4) DEFAULT NULL COMMENT 'FK DEL MENU PADRE AL CUAL PERTENECE',
  `cod_formulario` int(9) DEFAULT '0' COMMENT 'CODGIO DEL FORMULARIO AL CUAL PERTENECE',
  `cod_formulario_asociado` int(9) DEFAULT '0',
  `ind_visible` int(1) DEFAULT '1',
  `cod_indice` int(2) DEFAULT '0',
  PRIMARY KEY (`cod_menu_sub`),
  KEY `fk_mensub_menu_idx` (`cod_menu`),
  KEY `fk_mensub_frm_idx` (`cod_formulario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA RELACIONAR LOS POSIBLES SUBMENUS QUE PUEDAN CONTENER UN MENU PADRE' AUTO_INCREMENT=106 ;

--
-- Volcado de datos para la tabla `sys_menu_sub`
--

INSERT INTO `sys_menu_sub` (`cod_menu_sub`, `nom_menu_sub`, `des_menu_sub`, `met_menu_sub`, `icon_menu_sub`, `ind_header`, `bg_color`, `cod_menu`, `cod_formulario`, `cod_formulario_asociado`, `ind_visible`, `cod_indice`) VALUES
(0, ' Movimiento Contable', 'MENU PARA LOS MOVIMIENTOS CONTABLES A FACTURAS O CUENTAS', '?app=contabilidad&met=movContable&arg=2,96,movContable', 'fa-bar-chart-o', 1, 'purple', 13, 97, 96, 1, 0),
(1, ' Nuevo Usuario', 'MENU PARA CREAR NUEVOS USUARIOS', '?app=sistema&met=usuario&arg=1,1,nuevaUsuario', 'fa fa-user', 0, 'blue', 1, 6, 1, 1, 1),
(2, ' Menu Usuario', 'MENU PARA GESTIONAR EL MENU DEL USUARIO', '?app=sistema&met=MenuUsuario&arg=1,4,MenuUsuario', 'fa fa-user', 0, 'pinkDark', 1, 4, 4, 1, 2),
(3, ' Sub Menu Usuario', 'MENU PARA GESTIONAR EL SUB MENU DEL USUARIO', '?app=sistema&met=subMenuUsuario&arg=1,5,subMenuUsuario', 'fa fa-user', 0, 'blueDark', 1, 5, 5, 1, 3),
(4, ' Gestion Usuario', 'MENU PARA LA ADMINISTRACION DE USUARIOS', '?app=sistema&met=usuario&arg=2,6,usuario', 'fa fa-user', 1, 'greenLight', 1, 1, 6, 1, 4),
(6, ' Nueva Empresa', 'MENU PARA EL REGISTRO DE EMPRESAS', '?app=sistema&met=empresa&arg=1,2,nuevaEmpresa', 'fa fa-windows', 0, 'orangeDark', 2, 2, 2, 1, 1),
(7, ' Empresa Contrato', 'MENU PARA RELACIONAR LA EMPRESA Y EL CONTATO', '?app=sistema&met=empresaVsContrato&arg=1,7,nuevaEmpresaVsContrato', 'fa fa-windows', 0, 'blue', 2, 7, 7, 1, 2),
(8, ' Gestion Empresas', 'MENU PARA LA ADMINSTRACION DE LAS EMPRESAS', '?app=sistema&met=empresa&arg=2,9,empresa', 'fa fa-windows', 1, 'pinkDark', 2, 9, 9, 1, 3),
(10, ' Editar Perfil', 'MENU INVISIBLE PARA LAS OPCIONES FUERA DEL MENU', NULL, NULL, 0, 'purple', 10, 8, 0, 1, 0),
(11, ' Nuevo Contrato', 'MENU PARA EL REGISTRO DE CONTRATOS DEL SISTEMA', '?app=sistema&met=contrato&arg=1,10,nuevaContrato', 'fa fa-reorder', 0, 'greenLight', 3, 11, 10, 1, 1),
(12, ' Gestion Contratos', 'MENU PARA GESTIONAR LOS CONTRATOS DEL SISTEMA', '?app=sistema&met=contrato&arg=2,11,contrato', 'fa fa-reorder', 0, 'blue', 3, 10, 11, 1, 2),
(13, ' Escribir Mensaje', 'MENU PARA REGISTRAR LOS MENSAJES INTERNOS DEL SISTEMA', '?app=sistema&met=mensaje', 'fa fa-inbox', 0, 'orangeDark', 4, 12, 0, 1, 1),
(14, ' Bandeja Entrada', 'MENU PARA GESIONAR INBOX', '?app=sistema&met=gestionMensajes', 'fa fa-inbox', 0, 'pinkDark', 4, 13, 0, 1, 2),
(15, ' Ciudades', 'MENU PARA REGISTRAR CIUDADES Y DPTOS', '?app=sistema&met=ciudad&arg=2,48,ciudad', 'fa fa-wrench', 0, 'purple', 5, 49, 48, 1, 1),
(16, ' Estados', 'MENU PARA GESTIONAR LOS ESTADOS', '?app=sistema&met=estado&arg=2,38,estado', 'fa fa-wrench', 0, 'blueDark', 5, 39, 38, 1, 2),
(17, ' Perfiles', 'MENU PARA GESTIONAR LOS PERFILES', '?app=sistema&met=perfil&arg=2,50,perfil', 'fa fa-wrench', 0, 'purple', 5, 51, 50, 1, 3),
(18, ' Metodos', 'MENU PARA GESTIONAR LOS METODOS DEL SISTEMA', '?app=sistema&met=metodo&arg=2,52,metodo', 'fa fa-wrench', 0, 'blue', 5, 53, 52, 1, 4),
(19, ' Perfil Vs Metodo', 'MENU PARA RELACIONAR LOS PERFILES CON LOS METODOS', '?app=sistema&met=perfilMetodo&arg=2,54,perfilMetodo', 'fa fa-wrench', 0, 'pinkDark', 5, 55, 54, 1, 5),
(20, ' Formularios', 'MENU PARA GESTIONAR FORMULARIOS', '?app=sistema&met=formulario&arg=2,56,formulario', 'fa fa-wrench', 0, 'blueDark', 5, 57, 56, 1, 6),
(21, ' Formulario Vs Metodo', 'MENU PARA RELACIONAR LOS METODOS DE CADA FORMULARIO', '?app=sistema&met=formularioMetodo&arg=2,58,formularioMetodo', 'fa fa-wrench', 0, 'blue', 5, 59, 58, 1, 7),
(22, ' Frames', 'MENU PARA GESTIONAR LOS FRAMES QUE CONFORMAN UN MENU', '?app=sistema&met=frame&arg=2,60,frame', 'fa fa-wrench', 0, 'blueDark', 5, 61, 60, 1, 8),
(23, ' Inputs Frame', 'MENU PARA GESTIONAR LOS INPUT QUE COMPONEN EL FRAME DEL FORMULARIO', '?app=sistema&met=detframe&arg=2,62,detframe', 'fa fa-wrench', 0, 'purple', 5, 63, 62, 1, 9),
(24, ' Gestion Configuracion', 'MENU PARA LA ADMINISTRACION DE LAS PARAMETRIZACIONES ANTERIORES', '?app=sistema&met=gestionConfiguracion', 'fa fa-wrench', 0, 'blue', 5, 0, 0, 1, 10),
(25, ' Dasboard Facturacion', 'MENU PARA EL PANEL DE ESTADISTICAS DE FACTURACION', '?app=facturacion&met=indexFacturacion', 'fa fa-wrench', 0, 'orangeDark', 9, 0, 0, 1, 1),
(26, ' Datos Generales', 'MENU PARA LA CONFIGURACION GENERAL DE FACTURACION', '?app=facturacion&met=configFacturacion&arg=2,22,configFacturacion', 'fa fa-wrench', 1, 'purple', 9, 14, 22, 1, 2),
(33, ' Tipo Impuestos', 'MENU PARA LA GESTION DE LOS TIPOS DE IMPUESTOS DE FACTURACION', '?app=facturacion&met=tipoImpuesto&arg=2,23,tipoImpuesto', 'fa fa-wrench', 0, 'blue', 9, 15, 23, 1, 3),
(34, ' Impuestos', 'MENU PARA LA GESTION DE LOS IMPUESTOS DE FACTURACION', '?app=facturacion&met=impuestos&arg=2,24,impuestos', 'fa fa-wrench', 0, 'purple', 9, 16, 24, 1, 4),
(35, ' Tipo Pago', 'MENU PARA LA GESTION DE LOS TIPOS DE PAGOS DE FACTURACION', '?app=facturacion&met=tipopago&arg=2,25,tipopago', 'fa fa-wrench', 1, 'greenLight', 9, 17, 25, 1, 5),
(36, ' Unidades Medida', 'MENU PARA LA GESTION DE LOS TIPOS DE UNIDADES DE MEDIDA', '?app=facturacion&met=uniMedidas&arg=2,26,uniMedidas', 'fa fa-wrench', 0, 'purple', 9, 18, 26, 1, 6),
(37, ' Monedas', 'MENU PARA LA GESTION DE TIPO DE MONEDAS', '?app=facturacion&met=moneda&arg=2,27,moneda', 'fa fa-wrench', 0, 'blueDark', 9, 19, 27, 1, 7),
(38, ' Numeraciones', 'MENU PARA LAS NUMERACIONES DE FACTURACION', '?app=facturacion&met=numeracion&arg=2,28,numeracion', 'fa fa-wrench', 1, 'blue', 9, 20, 28, 1, 8),
(39, ' Regimen', 'MENU PARA LOS DIFERENTES TIPO DE REGIMEN SEGUN DIAN EN COLOMBIA', '?app=facturacion&met=regimen&arg=2,29,regimen', 'fa fa-wrench', 0, 'purple', 9, 21, 29, 1, 9),
(40, ' Cotizacion', 'MENU PARA REGISTRAR LAS FACTURAS DE VENTA', '?app=facturacion&met=factura&arg=2,30,factura', 'fa fa-money', 1, 'greenLight', 11, 31, 30, 1, 1),
(41, ' Fact Recurrentes', 'MENU PARA RESGISTRAR FACUTRAS RECURRENTES', '?app=facturacion&met=facturaRecurrente', 'fa fa-money', 0, 'orangeDark', 11, 0, 0, 0, 3),
(43, ' Pagos Recibidos', 'MENU PARA GESTIONAR LOS PAGOS RECIBIDOS POR FACTURA', '?app=facturacion&met=pagosRecibidos', 'fa fa-money', 0, 'pinkDark', 11, 0, 0, 1, 4),
(44, ' Notas Credito', 'MENU PARA EL REGISTRO DE NOTAS CREDITO', '?app=facturacion&met=notasCredito', 'fa fa-money', 0, 'blue', 11, 0, 0, 1, 5),
(45, ' Gestion Item', 'MENU PARA LOS ITEMS', '?app=facturacion&met=item&arg=2,32,item', 'fa fa-list-alt', 1, 'greenLight', 12, 33, 32, 1, 0),
(46, ' Gestion Inventario', 'MENU PARA LOS INVENTARIOS', '?app=facturacion&met=inventario&arg=2,34,inventario', 'fa fa-list-alt', 0, 'pinkDark', 12, 35, 34, 1, 0),
(47, ' Gestion Cliente', 'MENU GESTIONAR LOS CLIENTES', '?app=facturacion&met=cliente&arg=2,36,cliente', 'fa fa-money', 0, 'blue', 11, 37, 36, 1, 6),
(48, ' Descuentos', 'MENU PARA LOS DESCUENTOS', '?app=facturacion&met=descuento&arg=2,40,descuento', 'fa fa-wrench', 0, 'greenLight', 9, 41, 40, 1, 10),
(49, ' Cuentas / Bancos', 'MENU PARA GESTIONAR LAS CUENTAS CONTABLES', '?app=contabilidad&met=cuenta&arg=2,42,cuenta', 'fa fa-bar-chart-o', 0, 'blueDark', 13, 43, 42, 1, 0),
(63, ' Empresas Vs Contratos', 'MENU PARA LA ADMINSTRACION DE LOS CONTRATOS POR EMPRESAS', '?app=sistema&met=empresaVsContrato&arg=2,92,empresaVsContrato', 'fa fa-windows', 1, 'pinkDark', 2, 92, 91, 1, 4),
(64, ' Dasboard Sistema', 'MENU PARA EL PANEL DE ESTADISTICAS DEL SISTEMA', '?app=sistema&met=indexSistema', 'fa fa-wrench', 0, 'orangeDark', 5, 0, 0, 0, 11),
(65, ' Nueva Ciudad', 'MENU PARA CREAR NUEVAS CIUDADES', '?app=sistema&met=ciudad&arg=1,49,nuevaCiudad', 'fa fa-wrench', 0, 'blue', 5, 48, 49, 0, 12),
(66, ' Nuevo Estado', 'MENU PARA CREAR NUEVOS ESTADOS', '?app=sistema&met=estado&arg=1,39,nuevaEstado', 'fa fa-wrench', 0, 'blue', 5, 38, 39, 0, 13),
(67, ' Nuevo Perfil', 'MENU PARA CREAR NUEVOS PERFILES', '?app=sistema&met=perfil&arg=1,51,nuevaPerfil', 'fa fa-wrench', 0, 'blue', 5, 50, 51, 0, 14),
(68, ' Nuevo Metodo', 'MENU PARA CREAR NUEVOS METODOS', '?app=sistema&met=metodo&arg=1,53,nuevaMetodo', 'fa fa-wrench', 0, 'blue', 5, 52, 53, 0, 15),
(69, ' Nueva Metodo Vs Perfil', 'MENU PARA RELACIONAR LOS METODOS Y ALCANCE DE UN PERFIL', '?app=sistema&met=perfilMetodo&arg=1,55,nuevaPerfilMetodo', 'fa fa-wrench', 0, 'blue', 5, 54, 55, 0, 16),
(70, ' Nuevo Formulario', 'MENU PARA CREAR FORMULARIO', '?app=sistema&met=formulario&arg=1,57,nuevaFormulario', 'fa fa-wrench', 0, 'blue', 5, 56, 57, 0, 17),
(71, ' Nuevo Formulario Vs Metodo', 'MENU PARA RELACIONAR LOS METODOS DE UN FORMULARIO', '?app=sistema&met=formularioMetodo&arg=1,59,nuevaFormularioMetodo', 'fa fa-wrench', 0, 'blue', 5, 58, 59, 0, 18),
(72, ' Nuevo Frame', 'MENU PARA CREAR LOS FRAMES DE UN FORMULARIO', '?app=sistema&met=frame&arg=1,61,nuevaFrame', 'fa fa-wrench', 0, 'blue', 5, 60, 61, 0, 19),
(73, ' Nuevo Detalle del Frame', 'MENU PARA CREAR LOS INPUTS QUE COMPONEN UN FORMULARIO POR FRAME', '?app=sistema&met=detframe&arg=1,63,nuevaDetFrame', 'fa fa-wrench', 0, 'blue', 5, 62, 63, 0, 20),
(74, ' Nueva Configuracion Facturacion', 'MENU PARA CONFIGURAR LOS DATOS GENERALES DE FACTURACION', '?app=facturacion&met=configFacturacion&arg=1,14,nuevaFacConfig', 'fa fa-wrench', 0, 'blue', 9, 22, 14, 0, 11),
(75, ' Nuevo Tipo de Impuesto', 'MENU PARA CREAR TIPOS DE IMPUESTO', '?app=facturacion&met=tipoImpuesto&arg=1,15,nuevaTipoPago', 'fa fa-wrench', 0, 'blue', 9, 23, 15, 0, 12),
(76, ' Nuevo Impuesto', 'MENU PARA CREAR IMPUESTO', '?app=facturacion&met=impuestos&arg=1,16,nuevaImpuesto', 'fa fa-wrench', 0, 'blue', 9, 24, 16, 0, 13),
(77, ' NuevoTipo de Pago', 'MENU PARA CREAR TIPOS DE PAGO', '?app=facturacion&met=tipopago&arg=1,17,nuevaTipoPago', 'fa fa-wrench', 0, 'blue', 9, 25, 17, 0, 14),
(78, ' Nueva Unidad de Medida', 'MENU PARA CREAR UNIDADES DE MEDIDA', '?app=facturacion&met=uniMedidas&arg=1,18,nuevaUniMedida', 'fa fa-wrench', 0, 'blue', 9, 26, 18, 0, 15),
(79, ' Nueva Moneda', 'MENU PARA CREAR TIPOS DE MONEDAS', '?app=facturacion&met=moneda&arg=1,19,nuevaMoneda', 'fa fa-wrench', 0, 'blue', 9, 27, 19, 0, 16),
(80, ' Nueva Numeracion', 'MENU PARA CREAR NUMERACIONES PARA FACTURACION', '?app=facturacion&met=numeracion&arg=1,20,nuevaNumeracion', 'fa fa-wrench', 0, 'blue', 9, 28, 20, 0, 17),
(81, ' Nuevo Regimen', 'MENU PARA CREAR REGIMEN', '?app=facturacion&met=regimen&arg=1,21,nuevaRegimen', 'fa fa-wrench', 0, 'blue', 9, 29, 21, 0, 18),
(82, ' Nuevo Decuento', 'MENU PARA CREAR DESCUENTOS', '?app=facturacion&met=descuento&arg=1,41,nuevaDescuento', 'fa fa-wrench', 0, 'blue', 9, 40, 41, 0, 19),
(83, ' Nuevo Cotizacion - Factura', 'MENU PARA CREAR COTIZACIONES Y FACTURAS', '?app=facturacion&met=factura&arg=1,31,nuevaFactura', 'fa fa-wrench', 0, 'blue', 11, 30, 31, 0, 20),
(84, ' Facturacion', 'MENU PARA GETIONAR LA FACTURACION DE LA EMPRESA', '?app=facturacion&met=facturacion&arg=2,94,facturacion', 'fa fa-money', 1, 'greenLight', 11, 95, 94, 1, 2),
(85, ' Nuevo Cotizacion - Factura', 'MENU PARA CREAR COTIZACIONES Y FACTURAS', '?app=facturacion&met=facturacion&arg=1,95,nuevaFactura', 'fa fa-money', 0, 'blue', 11, 94, 95, 0, 21),
(86, ' Nuevo Cliente Asociado', 'MENU PARA CREAR CLIENTE ASOCIADOS A UN CLIENTE PRINCIPAL', '?app=facturacion&met=cliente&arg=1,37,nuevaCliente', 'fa fa-wrench', 0, 'blue', 13, 36, 37, 0, 22),
(87, ' Movimiento Contable', 'MENU PARA LOS MOVIMIENTOS CONTABLES A FACTURAS O CUENTAS', '?app=contabilidad&met=movContable&arg=2,96,movContable', 'fa fa-bar-chart-o', 1, 'purple', 13, 97, 96, 1, 1),
(88, ' Nuevo Movimiento Contable', 'MENU PARA LOS MOVIMIENTOS CONTABLES A FACTURAS O CUENTAS', '?app=contabilidad&met=movContable&arg=1,97,movContable', 'fa fa-bar-chart-o', 0, 'purple', 13, 96, 97, 0, 2),
(89, ' Dasboard Contabilidad', 'MENU PARA EL PANEL DE ESTADISTICAS DE CONTABILIDAD', '?app=contabilidad&met=indexContabilidad', 'fa fa-bar-chart-o', 0, 'orangeDark', 13, 0, 0, 1, 0),
(90, ' Orden de Compra', 'MENU PARA GETIONAR LAS ORDENES DE COMPRA', '?app=facturacion&met=orden&arg=2,98,orden', 'fa fa-money', 1, 'greenLight', 11, 99, 98, 1, 7),
(91, ' Nuevo Orden Compra', 'MENU PARA CREAR ORDENES DE COMPRA Y ADJUNTAR EL COMPROBANTE', '?app=facturacion&met=orden&arg=1,99,nuevaOrden', 'fa fa-money', 0, 'greenLight', 11, 98, 99, 0, 8),
(92, ' Clases de Vehiculo', 'MENU PARA CREAR LAS CLASES DE VEHICULOS EXISTENTES', '?app=huella&met=claseVehiculo&arg=2,100,claseVehiculo', 'fa fa-circle-o', 1, 'greenLight', 16, 101, 100, 1, 0),
(93, ' Nueva Clases de Vehiculo', 'MENU PARA CREAR LAS CLASES DE VEHICULOS EXISTENTES', '?app=huella&met=claseVehiculo&arg=1,101,nuevaClaseVehiculo', 'fa fa-circle-o', 0, 'greenLight', 16, 100, 101, 0, 1),
(94, ' Tipos de Servicio', 'MENU PARA CREAR LOS DIFERENTES TIPOS DE SERVICIO DE LOS VEHICULOS', '?app=huella&met=tipoServicio&arg=2,102,tipoServicio', 'fa fa-circle-o', 1, 'blue', 16, 103, 102, 1, 2),
(95, ' Nueva Tipos de Servicio', 'MENU PARA CREAR LOS DIFERENTES TIPOS DE SERVICIO DE LOS VEHICULOS', '?app=huella&met=tipoServicio&arg=1,103,nuevaTipoServicio', 'fa fa-circle-o', 0, 'blue', 16, 102, 103, 0, 3),
(96, ' Tipos de Documento', 'MENU PARA CREAR LOS DIFERENTES TIPOS DE DOCUMENTOS', '?app=huella&met=tipoDocumento&arg=2,104,tipoDocumento', 'fa fa-circle-o', 1, 'pinkDark', 16, 105, 104, 1, 4),
(97, ' Nuevos Tipos de Documento', 'MENU PARA CREAR LOS DIFERENTES TIPOS DE DOCUMENTOS', '?app=huella&met=tipoDocumento&arg=1,105,nuevaTipoDocumento', 'fa fa-circle-o', 0, 'pinkDark', 16, 104, 105, 0, 5),
(98, ' Tipos de Combustible', 'MENU PARA CREAR LOS DIFERENTES TIPOS DE COMBUSTIBLE', '?app=huella&met=combustible&arg=2,106,combustible', 'fa fa-circle-o', 1, 'blueDark', 16, 107, 106, 1, 6),
(99, ' Nuevos Tipos de Combustible', 'MENU PARA CREAR LOS DIFERENTES TIPOS DE COMBUSTIBLE', '?app=huella&met=combustible&arg=1,107,nuevaCombustible', 'fa fa-circle-o', 0, 'blueDark', 16, 106, 107, 0, 7),
(100, ' Vehiculos', 'MENU PARA CREAR LOS DIFERENTES VEHICULO', '?app=huella&met=vehiculo&arg=2,108,vehiculo', 'fa fa-circle-o', 1, 'purple', 16, 109, 108, 1, 8),
(101, ' Nuevo vehiculo', 'MENU PARA CREAR LOS DIFERENTES VEHICULO', '?app=huella&met=vehiculo&arg=1,109,nuevaVehiculo', 'fa fa-circle-o', 0, 'purple', 16, 108, 109, 0, 9),
(102, ' Clientes', 'MENU PARA CREAR LOS DIFERENTES CLIENTES', '?app=huella&met=clienteHue&arg=2,110,clienteHue', 'fa fa-circle-o', 1, 'orangeDark', 16, 111, 110, 1, 10),
(103, ' Nuevo Cliente', 'MENU PARA CREAR LOS DIFERENTES CLIENTES', '?app=huella&met=clienteHue&arg=1,111,nuevaClienteHue', 'fa fa-circle-o', 0, 'orangeDark', 16, 110, 111, 0, 11),
(104, ' Alquiler Vehiculo', 'MENU PARA CREAR LOS ALQUILERES DE LOS VEHICULOS', '?app=huella&met=clienteVehiculo&arg=2,112,clienteVehiculo', 'fa fa-circle-o', 1, 'blueDark', 16, 113, 112, 1, 12),
(105, ' Nuevo Alquiler', 'MENU PARA CREAR LOS ALQUILERES DE LOS VEHICULOS', '?app=huella&met=clienteVehiculo&arg=1,113,nuevaClienteVehiculo', 'fa fa-circle-o', 0, 'blueDark', 16, 112, 113, 0, 13);

--
-- Disparadores `sys_menu_sub`
--
DROP TRIGGER IF EXISTS `sys_menu_sub_AINS`;
DELIMITER //
CREATE TRIGGER `sys_menu_sub_AINS` AFTER INSERT ON `sys_menu_sub`
 FOR EACH ROW BEGIN
  INSERT INTO sys_usuario_menu_sub 
          SET cod_usuario  = 1, 
              cod_menu_sub = NEW.cod_menu_sub;
 END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_metodos`
--

CREATE TABLE IF NOT EXISTS `sys_metodos` (
  `cod_metodos` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DE LA TABLA',
  `nom_metodos` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DE LA ACCIONES QUE PUEDE EJECUTAR EL USUARIO',
  `des_metodos` varchar(120) DEFAULT NULL,
  `btn_metodos` varchar(45) DEFAULT NULL,
  `ico_metodos` varchar(45) DEFAULT NULL COMMENT 'ICONO DEL BOTON',
  `tip_metodos` varchar(45) DEFAULT NULL COMMENT 'TIPO DE BOTON',
  `cla_metodos` varchar(45) DEFAULT NULL COMMENT 'CLASE PARA EL COMPORTAMIENTO DEL BOTON',
  `href_metodos` varchar(100) DEFAULT NULL,
  `fec_metodos` date DEFAULT NULL,
  `hora_metodos` varchar(45) DEFAULT NULL,
  `func_metodos` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cod_metodos`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA DONDE SE ALMACENAN LOS DIFERENTES TIPOS DE METODOS QUE SE PUEDEN EJECUTAR EN LOS MODULOS INSTALADOS' AUTO_INCREMENT=13 ;

--
-- Volcado de datos para la tabla `sys_metodos`
--

INSERT INTO `sys_metodos` (`cod_metodos`, `nom_metodos`, `des_metodos`, `btn_metodos`, `ico_metodos`, `tip_metodos`, `cla_metodos`, `href_metodos`, `fec_metodos`, `hora_metodos`, `func_metodos`) VALUES
(1, 'AGREGAR', 'METODO DE AGREGADO DE DATOS', 'Guardar', 'icomoon-icon-disk white', 'submit', 'btn btn-labeled  btn-primary', ' ', NULL, NULL, NULL),
(2, 'BORRAR', 'METODO DE BORRADO DE DATOS', 'Cancelar', 'icomoon-icon-close white', 'reset', 'btn  btn-labeled btn-danger', '', NULL, NULL, NULL),
(3, 'MODIFICAR', 'METODO DE MODIFICADO DE DATOS', 'Modificar', 'icomoon-icon-spinner-8', 'link', 'btn  btn-labeled btn-success', '{FORM_CONTROLLER}&met={NOM_FORM}&arg=3,{NUM_FORM},{MET_EDIT}', NULL, NULL, NULL),
(4, 'DESACTIVAR', 'METODO DE DESACTIVADO DE DATOS', 'Activar/Desactivar', 'icomoon-icon-switch', 'button', 'btn  btn-labeled btn-warning', ' ', NULL, NULL, NULL),
(5, 'SELECCIONART', 'METODO PARA SELECCIONAR TODO DE UNA LISTA', 'Seleccionar Todo', 'icomoon-icon-clipboard', 'button', 'btn  btn-labeled btn-success', ' ', NULL, NULL, NULL),
(6, 'NUEVO', 'METODO PARA AGREGAR NUEVO REGISTRO', 'Nuevo', 'icomoon-icon-plus', 'link', 'btn  btn-labeled btn-info', '{FORM_CONTROLLER}&met={NOM_FORM}&arg=1,{NUM_FORM},{MET_NEW}', NULL, NULL, NULL),
(7, 'REGRESAR', 'METODO PARA REGRESAR A EL FORMULARIO ANTERIOR', 'Regresar', 'icomoon-icon-arrow-left-7', 'link', 'btn  btn-labeled btn-info', '{FORM_CONTROLLER}&met={NOM_FORM_ANT}&arg=2,{NUM_FORM_ANT},{VIEW_FORM_ANT}', NULL, NULL, NULL),
(8, 'AGREGAR ITEM', 'METODO PARA AGREGAR LINEAS', 'Agregar Item', 'icomoon-icon-plus', 'button', 'btn  btn-labeled btn-primary', NULL, NULL, NULL, NULL),
(9, 'ELIMINAR FILA', 'METODO PARA ELIMINAR ITEMS', 'Eliminar Item', 'icomoon-icon-remove-4', 'button', 'btn  btn-labeled btn-warning', NULL, NULL, NULL, NULL),
(10, 'DETALLES', 'METODO PARA VER LOS DETALLES DE UN REGISTRO', 'Detalles', 'icomoon-icon-search-3', 'link', 'btn  btn-labeled btn-info', '{FORM_CONTROLLER}&met={NOM_FORM}&arg=3,{NUM_FORM},{MET_EDIT}', '0000-00-00', 'now()', NULL),
(11, 'AGREGAR PAGO', 'METODO PARA AGREGAR PAGO DE FACTURA', 'Agregar Pago', 'icomoon-icon-plus-circle', 'link', 'btn  btn-labeled btn-primary', ' ', NULL, NULL, NULL),
(12, 'AGREGAR HUELLA', 'METODO PARA AGREGAR LA HUELLA', 'Agregar Huella', 'icomoon-icon-podcast', 'button', 'btn  btn-labeled btn-warning', NULL, NULL, NULL, 'onclick = "Initialize()"');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_notificacion`
--

CREATE TABLE IF NOT EXISTS `sys_notificacion` (
  `cod_notificacion` int(4) NOT NULL AUTO_INCREMENT,
  `des_notificacion` varchar(100) DEFAULT NULL COMMENT 'DESCRIPCION DE LA NOTIFICACION',
  `fecha_notificacion` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO DE NOTIFICACION',
  `hora_notificacion` varchar(45) DEFAULT NULL COMMENT 'HORA DEL REGISTRO DE LA NOTIFICACION',
  `cod_estado` varchar(3) NOT NULL DEFAULT '0' COMMENT 'INDICA EL ESTADO DE LA NOTIFICACION',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DE USUARIO PARA LA NOTIFICACION',
  PRIMARY KEY (`cod_notificacion`),
  KEY `fk_noti_usu_idx` (`cod_usuario`),
  KEY `fk_noti_est_idx` (`cod_estado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LAS NOTIFICACIONES DEL SISTEMA' AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `sys_notificacion`
--

INSERT INTO `sys_notificacion` (`cod_notificacion`, `des_notificacion`, `fecha_notificacion`, `hora_notificacion`, `cod_estado`, `cod_usuario`) VALUES
(1, 'aaaaa', NULL, NULL, 'NAA', 1),
(2, 'bbb', NULL, NULL, 'NAA', 1),
(3, 'cccc', NULL, NULL, 'NAA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_perfil`
--

CREATE TABLE IF NOT EXISTS `sys_perfil` (
  `cod_perfil` int(4) NOT NULL AUTO_INCREMENT,
  `nom_perfil` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL PERFIL DE USUARIO',
  `des_perfil` varchar(120) DEFAULT NULL COMMENT 'DESCRIPCION CORTA DEL PERFIL DE USUARIO',
  `fec_perfil` date DEFAULT NULL COMMENT 'FECHA DE REGISTRO DEL PERFIL',
  `hora_perfil` varchar(45) DEFAULT NULL COMMENT 'HORA DEL REGISTRO DEL PERFIL',
  PRIMARY KEY (`cod_perfil`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS DIFERENTES TIPOS DE PERFIL PARA EL ACCESO AL SISTEMA' AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `sys_perfil`
--

INSERT INTO `sys_perfil` (`cod_perfil`, `nom_perfil`, `des_perfil`, `fec_perfil`, `hora_perfil`) VALUES
(1, 'ROOT', 'SUPER ADMINISTRADOR', '0000-00-00', NULL),
(2, 'ROOT NIVEL 1', 'USUARIO RESTRINGIDO PARA ADMINISTRACION', NULL, NULL),
(3, 'USUARIO', 'METODO: CONSULTA, REGISTRO, MODIFICACION, CON LIMITES PARA ADMINISTRACION.', NULL, NULL),
(4, 'USUARIO NIVEL 1', 'METODO DE CONSULTA', NULL, NULL),
(5, 'USUARIO CONSULTA', 'USUARIO PARA CONSULTA Y SOPORTE', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_perfil_metodos`
--

CREATE TABLE IF NOT EXISTS `sys_perfil_metodos` (
  `cod_perfil_metodos` int(4) NOT NULL AUTO_INCREMENT,
  `cod_perfil` int(4) DEFAULT NULL,
  `cod_metodos` int(4) DEFAULT NULL,
  `cod_modulo` int(4) DEFAULT NULL,
  `uri_perfil_metodos` varchar(140) DEFAULT NULL COMMENT 'URL DE LA PETICION DEL METODO',
  `met_perfil_metodos` varchar(45) DEFAULT NULL COMMENT 'TIPO DE METODO: GET, POST_ REQUEST',
  `cod_estado` varchar(3) DEFAULT NULL COMMENT 'ESTADO DEL BOTON',
  PRIMARY KEY (`cod_perfil_metodos`),
  KEY `fk_permet_metod_idx` (`cod_metodos`),
  KEY `fk_permet_perfil_idx` (`cod_perfil`),
  KEY `fk_permet_est_idx` (`cod_estado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA RELACIONAR LOS METODOS EJECUTABLES DE LOS MODULOS CON LOS DIFERENTES TIPOS DE PERFILES' AUTO_INCREMENT=32 ;

--
-- Volcado de datos para la tabla `sys_perfil_metodos`
--

INSERT INTO `sys_perfil_metodos` (`cod_perfil_metodos`, `cod_perfil`, `cod_metodos`, `cod_modulo`, `uri_perfil_metodos`, `met_perfil_metodos`, `cod_estado`) VALUES
(5, 1, 1, 1, '     ', ' ', 'BTA'),
(6, 1, 2, 1, ' ', ' ', 'BTA'),
(7, 1, 3, 1, ' ', ' ', 'BTA'),
(8, 1, 4, 1, ' ', ' ', 'BTA'),
(9, 1, 5, 1, ' ', ' ', 'BTA'),
(10, 1, 6, 1, ' ', ' ', 'BTA'),
(11, 1, 7, 1, 'no aplica', 'no aplica', 'BTA'),
(12, 1, 8, 2, ' ', ' ', 'BTA'),
(13, 1, 9, 2, ' ', ' ', 'BTA'),
(14, 1, 10, 2, ' ', ' ', 'BTA'),
(15, 1, 11, 2, ' ', ' ', 'BTA'),
(16, 2, 1, 1, ' ', ' ', 'BTA'),
(17, 2, 2, 1, ' ', ' ', 'BTA'),
(18, 2, 3, 1, ' ', ' ', 'BTA'),
(19, 2, 4, 1, ' ', ' ', 'BTA'),
(30, 5, 3, 1, 'no', 'no', 'BTA'),
(31, 1, 12, 6, ' ', ' ', 'BTA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_tablareferencia`
--

CREATE TABLE IF NOT EXISTS `sys_tablareferencia` (
  `cod_tablareferencia` int(8) NOT NULL AUTO_INCREMENT,
  `nom_referencia` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cod_tablareferencia`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA GUARDAR LAS TABLAS QUE ARMAN LOS COMBOR EN LA FUNCION fbArmaCombo' AUTO_INCREMENT=33 ;

--
-- Volcado de datos para la tabla `sys_tablareferencia`
--

INSERT INTO `sys_tablareferencia` (`cod_tablareferencia`, `nom_referencia`) VALUES
(1, 'sys_ciudad'),
(2, 'fa_regimen'),
(3, 'sys_contrato'),
(4, 'mod_modulo'),
(5, 'fa_moneda'),
(6, 'sys_perfil'),
(7, 'sys_empresa'),
(8, 'sys_menu'),
(9, 'sys_menu_sub'),
(10, 'sys_usuario'),
(11, 'sys_estado'),
(12, 'fa_tipoimpuesto'),
(13, 'fa_cliente'),
(14, 'fa_proveedor'),
(15, 'fa_tipopago'),
(16, 'fa_item'),
(17, 'con_cuenta'),
(18, 'fa_item_imventario'),
(19, 'fa_unimedida'),
(20, 'fa_impuesto'),
(21, 'fa_descuentos'),
(22, 'lc_restaurantes'),
(23, 'sys_metodos'),
(24, 'sys_formulario'),
(25, 'sys_tipoinput'),
(26, 'sys_tablareferencia'),
(27, 'sys_frame'),
(28, 'lc_restaurantes'),
(29, 'lc_categorias'),
(30, 'lc_categorias_sub'),
(31, 'fa_cotizacion'),
(32, 'fa_factura');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_tipoinput`
--

CREATE TABLE IF NOT EXISTS `sys_tipoinput` (
  `cod_tipoInput` int(8) NOT NULL AUTO_INCREMENT COMMENT 'AUTOINCREMENTO DE TABLA PK',
  `nom_tipoInput` varchar(45) DEFAULT NULL COMMENT 'NOMBRE DEL TIPO DE INPUT',
  `esp_tipoInput` varchar(45) DEFAULT NULL COMMENT 'ESPECIFICACION SEGUN HTML5',
  PRIMARY KEY (`cod_tipoInput`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA ALMACENAR LOS DIFERENTES TIPOS DE INPUTS PARA ARMAR LOS FORMULARIOS PARTIENDO DE LAS COLUMNAS' AUTO_INCREMENT=16 ;

--
-- Volcado de datos para la tabla `sys_tipoinput`
--

INSERT INTO `sys_tipoinput` (`cod_tipoInput`, `nom_tipoInput`, `esp_tipoInput`) VALUES
(1, 'text', NULL),
(2, 'textarea', NULL),
(3, 'ckeckbox', NULL),
(4, 'radio', NULL),
(5, 'select', 'simple'),
(6, 'select', 'multiselect'),
(7, 'select', 'multiselect est'),
(8, 'password', NULL),
(9, 'file', NULL),
(10, 'hidden', NULL),
(11, 'fecha', NULL),
(12, 'hora', NULL),
(13, 'select', 'select lista'),
(14, 'spiner', NULL),
(15, 'porcentaje', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_usuario`
--

CREATE TABLE IF NOT EXISTS `sys_usuario` (
  `cod_usuario` int(8) NOT NULL COMMENT 'AUTOINCREMENTO PK DE TABLA',
  `nom_usuario` varchar(120) DEFAULT NULL COMMENT 'NOMBRE DEL USUARIO',
  `ape_usuario` varchar(120) DEFAULT NULL COMMENT 'APELLIDO DEL USUARIO',
  `dir_usuario` varchar(120) DEFAULT NULL COMMENT 'DIRECCION DEL USUARIO',
  `tel_usuario` varchar(30) DEFAULT NULL COMMENT 'TELEFONO DEL USUARIO',
  `email_usuario` varchar(100) DEFAULT NULL COMMENT 'DIRECCION DE CORREO DEL USUARIO',
  `fec_usuaio` date DEFAULT NULL COMMENT 'FECHA DE ALTA EN BASE DE DATOS',
  `hora_usuario` varchar(45) DEFAULT NULL COMMENT 'HORA DE ALTA EN BASE DE DATOS',
  `img_usuario` varchar(150) DEFAULT NULL COMMENT 'IMAGEN DE USUARIO',
  `usuario_usuario` text NOT NULL COMMENT 'USUARIO DE CONEXION A BASE DE DATOS',
  `password_usuario` text NOT NULL COMMENT 'PASSWORD DE CONEXION',
  `cod_estado` varchar(3) NOT NULL DEFAULT 'AAA',
  `ind_ayuda` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cod_usuario`),
  KEY `fk_usu_est_idx` (`cod_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS USUARIOS DEL SISTEMA';

--
-- Volcado de datos para la tabla `sys_usuario`
--

INSERT INTO `sys_usuario` (`cod_usuario`, `nom_usuario`, `ape_usuario`, `dir_usuario`, `tel_usuario`, `email_usuario`, `fec_usuaio`, `hora_usuario`, `img_usuario`, `usuario_usuario`, `password_usuario`, `cod_estado`, `ind_ayuda`) VALUES
(1, 'DAVID RICARDO', 'GONZALEZ ZAPATA', 'MANIZALES', '3207262467', 'ING.RICARDO.GONZALEZ@HOTMAIL.COM', NULL, NULL, 'DAVID_RICARDO2014-07-22-1406048055.jpg', 'RICARDO.GONZALEZ', '5b5771cd6dda232e23386b8fe308cced', 'AAA', 1),
(2, 'JUAN CARLOS', 'CESPEDES RUGE', 'CENTRO', '8765542', 'johes@yahoo.es', NULL, NULL, 'JUAN_CARLOS2014-07-22-1406061250.png', 'juan.cespedes', 'bc6276958bcb24bb9d547317e2b7a0b4', 'AAA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_usuario_empresa`
--

CREATE TABLE IF NOT EXISTS `sys_usuario_empresa` (
  `cod_usuario_empresa` int(8) NOT NULL AUTO_INCREMENT COMMENT 'PK DE LA TABLA AUTOINCREMENTO',
  `cod_usuario` int(8) NOT NULL COMMENT 'CODIGO DEL USUARIO',
  `cod_empresa` int(4) NOT NULL COMMENT 'CODIGO DE LA EMPRESA',
  PRIMARY KEY (`cod_usuario_empresa`),
  KEY `fk_usuemp_usu_idx` (`cod_usuario`),
  KEY `fk_usuemp_emp_idx` (`cod_empresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='TABLA PARA RELACIONAR LAS EMPRESAS QUE PERTENECEN A UN USUARIO' AUTO_INCREMENT=55 ;

--
-- Volcado de datos para la tabla `sys_usuario_empresa`
--

INSERT INTO `sys_usuario_empresa` (`cod_usuario_empresa`, `cod_usuario`, `cod_empresa`) VALUES
(46, 1, 15),
(47, 1, 16),
(54, 2, 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_usuario_menu`
--

CREATE TABLE IF NOT EXISTS `sys_usuario_menu` (
  `cod_usuario_menu` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO AUTOINCREMENTO DE LA TABLA',
  `cod_usuario` int(4) DEFAULT NULL COMMENT 'CODIGO DEL USUARIO',
  `cod_menu` int(4) DEFAULT NULL COMMENT 'CODIGO DEL MENU',
  PRIMARY KEY (`cod_usuario_menu`),
  KEY `fk_usumen_usu_idx` (`cod_usuario`),
  KEY `fk_usumen_men_idx` (`cod_menu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA ALMACENAR LOS MENUS QUE PUEDE VER UN USUARIO' AUTO_INCREMENT=230 ;

--
-- Volcado de datos para la tabla `sys_usuario_menu`
--

INSERT INTO `sys_usuario_menu` (`cod_usuario_menu`, `cod_usuario`, `cod_menu`) VALUES
(203, 1, 1),
(204, 1, 2),
(205, 1, 3),
(206, 1, 4),
(207, 1, 5),
(208, 1, 9),
(209, 1, 10),
(210, 1, 11),
(211, 1, 12),
(212, 1, 13),
(213, 1, 14),
(214, 1, 15),
(215, 1, 16),
(229, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_usuario_menu_sub`
--

CREATE TABLE IF NOT EXISTS `sys_usuario_menu_sub` (
  `cod_usuario_menu_sub` int(4) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DE LA TABLA AUTOINCREMENTO',
  `cod_usuario` int(4) DEFAULT NULL,
  `cod_menu_sub` int(4) DEFAULT NULL,
  PRIMARY KEY (`cod_usuario_menu_sub`),
  KEY `fk_usumens_usu_idx` (`cod_usuario`),
  KEY `fk_usumens_mensub_idx` (`cod_menu_sub`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA RELACIONAR LOS SUB MENUS QUE PUEDE VER UN USUARIO' AUTO_INCREMENT=533 ;

--
-- Volcado de datos para la tabla `sys_usuario_menu_sub`
--

INSERT INTO `sys_usuario_menu_sub` (`cod_usuario_menu_sub`, `cod_usuario`, `cod_menu_sub`) VALUES
(410, 1, 1),
(411, 1, 2),
(412, 1, 3),
(413, 1, 4),
(414, 1, 6),
(415, 1, 7),
(416, 1, 8),
(417, 1, 10),
(418, 1, 11),
(419, 1, 12),
(420, 1, 13),
(421, 1, 14),
(422, 1, 15),
(423, 1, 16),
(424, 1, 17),
(425, 1, 18),
(426, 1, 19),
(427, 1, 20),
(428, 1, 21),
(429, 1, 22),
(430, 1, 23),
(431, 1, 24),
(432, 1, 25),
(433, 1, 26),
(434, 1, 33),
(435, 1, 34),
(436, 1, 35),
(437, 1, 36),
(438, 1, 37),
(439, 1, 38),
(440, 1, 39),
(441, 1, 40),
(442, 1, 41),
(443, 1, 43),
(444, 1, 44),
(445, 1, 45),
(446, 1, 46),
(447, 1, 47),
(448, 1, 48),
(449, 1, 49),
(450, 1, 51),
(451, 1, 52),
(452, 1, 53),
(453, 1, 54),
(454, 1, 55),
(455, 1, 56),
(456, 1, 57),
(457, 1, 58),
(458, 1, 59),
(459, 1, 60),
(460, 1, 61),
(461, 1, 62),
(462, 1, 63),
(489, 2, 1),
(490, 1, 64),
(491, 1, 65),
(492, 1, 66),
(493, 1, 67),
(494, 1, 68),
(495, 1, 69),
(496, 1, 70),
(497, 1, 71),
(498, 1, 72),
(499, 1, 73),
(500, 1, 74),
(501, 1, 75),
(502, 1, 76),
(503, 1, 77),
(504, 1, 78),
(505, 1, 79),
(506, 1, 80),
(507, 1, 81),
(508, 1, 82),
(509, 1, 83),
(510, 1, 84),
(511, 1, 85),
(512, 1, 86),
(513, 1, 87),
(514, 1, 88),
(515, 1, 87),
(516, 1, 89),
(517, 1, 90),
(518, 1, 91),
(519, 1, 92),
(520, 1, 93),
(521, 1, 94),
(522, 1, 95),
(523, 1, 96),
(524, 1, 97),
(525, 1, 98),
(526, 1, 99),
(527, 1, 100),
(528, 1, 101),
(529, 1, 102),
(530, 1, 103),
(531, 1, 104),
(532, 1, 105);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sys_usuario_perfil`
--

CREATE TABLE IF NOT EXISTS `sys_usuario_perfil` (
  `cod_usuario_perfil` int(11) NOT NULL AUTO_INCREMENT COMMENT 'CODIGO DE LA TABLA, AUTOINCREMENTO',
  `cod_usuario` int(4) DEFAULT NULL COMMENT 'CODIGO DEL USUARIO',
  `cod_perfil` int(4) DEFAULT NULL COMMENT 'CODIGO DEL PERFIL',
  PRIMARY KEY (`cod_usuario_perfil`),
  KEY `fk_usuper_usu_idx` (`cod_usuario`),
  KEY `fk_usuper_per_idx` (`cod_perfil`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='TABLA PARA RELACIONAR EL PERFIL AL CUAL PERTENECE UN USUARIO' AUTO_INCREMENT=35 ;

--
-- Volcado de datos para la tabla `sys_usuario_perfil`
--

INSERT INTO `sys_usuario_perfil` (`cod_usuario_perfil`, `cod_usuario`, `cod_perfil`) VALUES
(27, 1, 1),
(34, 2, 2);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_ciudad`
--
CREATE TABLE IF NOT EXISTS `sys_view_ciudad` (
`Cod` varchar(113)
,`Codigo` int(6)
,`Nombre` varchar(45)
,`Departamento` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_contrato`
--
CREATE TABLE IF NOT EXISTS `sys_view_contrato` (
`Codigo` varchar(115)
,`Nombre` varchar(45)
,`Descripcion del contrato` varchar(45)
,`Vigencia` int(2)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_detframe`
--
CREATE TABLE IF NOT EXISTS `sys_view_detframe` (
`Cod` varchar(115)
,`Codigo` int(8)
,`Input` varchar(45)
,`Tabla Referencia` varchar(45)
,`Nombre Campo` varchar(45)
,`Holder Campo` varchar(45)
,`Tam Campo` int(2)
,`Detalle Campo` varchar(45)
,`Valor Campo` varchar(45)
,`Frame Padre` varchar(45)
,`Estado` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_empresa`
--
CREATE TABLE IF NOT EXISTS `sys_view_empresa` (
`Cod` varchar(114)
,`Codigo` int(4)
,`Nombre` varchar(45)
,`Nit` varchar(45)
,`Representante` varchar(45)
,`Telefono` varchar(45)
,`Direccion` varchar(45)
,`Pagina Web` varchar(45)
,`Email` varchar(45)
,`Imagen` varchar(120)
,`Ciudad - Dpto` varchar(93)
,`Perfil` varchar(45)
,`Moneda` varchar(52)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_empresa_contrato`
--
CREATE TABLE IF NOT EXISTS `sys_view_empresa_contrato` (
`Cod` varchar(122)
,`Codigo` int(4)
,`Empresa` varchar(45)
,`Contrato` varchar(45)
,`Estado Actual` varchar(45)
,`Modulo Contratado` varchar(45)
,`Fecha Inicio` date
,`Fecha Cierre` date
,`Usuario` text
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_estado`
--
CREATE TABLE IF NOT EXISTS `sys_view_estado` (
`Cod` varchar(120)
,`Codigo` varchar(3)
,`Descripcion del estado` varchar(45)
,`Modulo` varchar(45)
,`Fecha Registro` date
,`Hora Registro` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_formulario`
--
CREATE TABLE IF NOT EXISTS `sys_view_formulario` (
`Cod` varchar(117)
,`Codigo` int(9)
,`Nombre` varchar(45)
,`Esquema` varchar(45)
,`En Linea` varchar(43)
,`Trae Datos` varchar(43)
,`Tiene Ayuda` varchar(43)
,`Fecha Registro` date
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_formulario_metodos`
--
CREATE TABLE IF NOT EXISTS `sys_view_formulario_metodos` (
`Cod` varchar(125)
,`Codigo` int(8)
,`Formulario` varchar(45)
,`Metodos` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_frame`
--
CREATE TABLE IF NOT EXISTS `sys_view_frame` (
`Cod` varchar(112)
,`Codigo` int(8)
,`Nombre` varchar(45)
,`Esquema` varchar(45)
,`Tam` int(1)
,`Usuario Registra` text
,`Fomulario Padre` varchar(45)
,`En Linea` varchar(43)
,`Fecha Registro` date
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_mensajes`
--
CREATE TABLE IF NOT EXISTS `sys_view_mensajes` (
`Codigo` varchar(115)
,`De` varchar(240)
,`Avatar` varchar(225)
,`Asunto` varchar(120)
,`Descripcion` varchar(1000)
,`Adjuntos` varchar(242)
,`Fecha` date
,`Hora` time
,`Para` int(8)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_metodo`
--
CREATE TABLE IF NOT EXISTS `sys_view_metodo` (
`Cod` varchar(114)
,`Codigo` int(4)
,`Nombre` varchar(45)
,`Descripcion` varchar(120)
,`Boton` varchar(45)
,`icono` varchar(61)
,`Tipo` varchar(45)
,`clase` varchar(45)
,`Link/Url` varchar(100)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_perfil`
--
CREATE TABLE IF NOT EXISTS `sys_view_perfil` (
`Cod` varchar(113)
,`Codigo` int(4)
,`Nombre` varchar(45)
,`Descripcion` varchar(120)
,`Fecha Registro` date
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_perfil_metodos`
--
CREATE TABLE IF NOT EXISTS `sys_view_perfil_metodos` (
`Cod` varchar(121)
,`Codigo` int(4)
,`Perfil` varchar(45)
,`Metodo` varchar(45)
,`Modulo` varchar(45)
,`Url` varchar(140)
,`Metodo Interno` varchar(45)
,`Estado` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `sys_view_usuario`
--
CREATE TABLE IF NOT EXISTS `sys_view_usuario` (
`Cod` varchar(114)
,`Nombre` varchar(120)
,`Apellido` varchar(120)
,`Direccion` varchar(120)
,`Telefono` varchar(30)
,`Email` varchar(100)
,`Imagen` varchar(225)
,`Usuario` text
,`Estado` varchar(51)
,`Perfil` varchar(168)
);
-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_cliente`
--
DROP TABLE IF EXISTS `fa_view_cliente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_cliente` AS (select concat('<input name="id_dato"  data="fa_cliente" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_cliente`.`cod_cliente`,'" >') AS `Cod`,`fa_cliente`.`cod_cliente` AS `Codigo`,`fa_cliente`.`nom_cliente` AS `Nombre`,`fa_cliente`.`nit_cliente` AS `Nit`,`fa_cliente`.`dir_cliente` AS `Direccion`,`fa_cliente`.`email_cliente` AS `Email`,`fa_cliente`.`tel_cliente` AS `Telefono`,`fa_cliente`.`tel1_cliente` AS `Telefono 1`,`fa_cliente`.`fax_cliente` AS `Fax`,`fa_cliente`.`cel_cliente` AS `Celular`,concat(if((`fa_cliente`.`ind_cliente_cliente` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Cliente`,concat(if((`fa_cliente`.`ind_proveedor_cliente` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Proveedor`,`fa_cliente`.`obs_cliente` AS `Observaciones`,concat(`sys_ciudad`.`nom_ciudad`,'-',`sys_ciudad`.`dpt_ciudad`) AS `Ciudad`,`fa_tipopago`.`nom_tipopago` AS `Tipo Pago` from ((`fa_cliente` join `sys_ciudad`) join `fa_tipopago`) where ((`fa_cliente`.`cod_tipopago` = `fa_tipopago`.`cod_tipopago`) and (`fa_cliente`.`cod_ciudad` = `sys_ciudad`.`cod_ciudad`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_config`
--
DROP TABLE IF EXISTS `fa_view_config`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_config` AS (select concat('<input name="id_dato" class = "radio" data="fa_config" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_config`.`cod_config`,'" >') AS `Cod`,`fa_config`.`nom_config` AS `Nombre`,`fa_config`.`tyc_config` AS `Terminos y Condiciones`,`fa_config`.`not_config` AS `Notas Facturacion`,concat(if((`fa_config`.`ind_retenciones_config` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Apl. Retencion`,`fa_config`.`num_sig_recibocaja` AS `# Recibo Caja`,`fa_config`.`num_sig_compago` AS `# Comprobante Pago`,`sys_estado`.`des_estado` AS `Estado`,`sys_usuario`.`usuario_usuario` AS `Registro`,`fa_config`.`fec_config` AS `Fecha Registro`,`fa_config`.`hora_config` AS `Hora Registro`,`fa_config`.`fec_modifica_config` AS `Ult. Modificacion` from ((`fa_config` join `sys_estado`) join `sys_usuario`) where ((`fa_config`.`cod_usuario` = `sys_usuario`.`cod_usuario`) and (`fa_config`.`cod_estado` = `sys_estado`.`cod_estado`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_descuentos`
--
DROP TABLE IF EXISTS `fa_view_descuentos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_descuentos` AS (select concat('<input name="id_dato" class = "radio" data="fa_descuentos" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_descuentos`.`cod_descuentos`,'" >') AS `Cod`,`fa_descuentos`.`cod_descuentos` AS `Codigo`,`fa_descuentos`.`nom_descuento` AS `Nombre Descuento`,`fa_descuentos`.`des_descuento` AS `Descripcion`,`fa_descuentos`.`prc_descuento` AS `Porcentaje`,`fa_descuentos`.`fec_inicio_descuento` AS `Fecha de Inicio`,`fa_descuentos`.`fec_cierre_descuento` AS `Fecha Fin`,concat(`sys_estado`.`cod_estado`,' - ',`sys_estado`.`des_estado`) AS `Estado`,`sys_usuario`.`usuario_usuario` AS `Usuario` from ((`fa_descuentos` join `sys_estado`) join `sys_usuario`) where ((`fa_descuentos`.`cod_estado` = `sys_estado`.`cod_estado`) and (`fa_descuentos`.`cod_usuario` = `sys_usuario`.`cod_usuario`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_factura`
--
DROP TABLE IF EXISTS `fa_view_factura`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_factura` AS (select concat('<input name="id_dato"  data="fa_factura" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_factura`.`cod_factura`,'" >') AS `Cod`,`fa_factura`.`cod_factura` AS `Interno`,concat(if((`fa_factura`.`ind_cotizacion` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Cotizacion`,concat(`fa_factura`.`cod_numeracion`,'-',`fa_numeracion`.`pre_numeracion`) AS `Numeracion`,`fa_factura`.`num_factura` AS `Numero Factura`,`fa_cliente`.`nom_cliente` AS `Cliente`,`fa_factura`.`fec_alta_factura` AS `Fecha Registro`,`fa_factura`.`fec_vencimiento_factura` AS `Fecha Vencimiento`,concat(`fa_tipopago`.`num_dias_tipopago`,' ','Dias') AS `Plazo Dias`,concat('$ ',format(`fa_factura`.`sub_total_factura`,2)) AS `Sub Total`,concat('$ ',format(`fa_factura`.`sub_totaldes_factura`,2)) AS `Con Descuento`,concat('$ ',format(`fa_factura`.`imp_factura`,2)) AS `Importe`,concat('$ ',format(`fa_factura`.`imp_adeudado`,2)) AS `Adeudado`,concat('$ ',format(`fa_factura`.`imp_cancelado`,2)) AS `Cancelado`,`fa_factura`.`obs_factura` AS `Observaciones`,`fa_factura`.`not_factura` AS `Notas`,concat(if((`fa_factura`.`ind_recurrente_factura` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Recurrente`,`sys_estado`.`des_estado` AS `Estado Factura`,`fa_config`.`nom_config` AS `Configuracion` from (((((`fa_factura` join `fa_numeracion`) join `fa_cliente`) join `sys_estado`) join `fa_config`) join `fa_tipopago`) where ((`fa_factura`.`cod_numeracion` = `fa_numeracion`.`cod_numeracion`) and (`fa_factura`.`cod_cliente` = `fa_cliente`.`cod_cliente`) and (`fa_factura`.`cod_estado` = `sys_estado`.`cod_estado`) and (`fa_factura`.`cod_config` = `fa_config`.`cod_config`) and (`fa_factura`.`cod_tipopago` = `fa_tipopago`.`cod_tipopago`) and (`fa_factura`.`ind_cotizacion` = 1)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_facturacion`
--
DROP TABLE IF EXISTS `fa_view_facturacion`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_facturacion` AS (select concat('<input name="id_dato"  data="fa_factura" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_factura`.`cod_factura`,'" >') AS `Cod`,`fa_factura`.`cod_factura` AS `Interno`,concat(if((`fa_factura`.`ind_cotizacion` = 0),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Factura Venta`,concat(`fa_factura`.`cod_numeracion`,'-',`fa_numeracion`.`pre_numeracion`) AS `Numeracion`,`fa_factura`.`num_factura` AS `Numero Factura`,`fa_cliente`.`nom_cliente` AS `Cliente`,`fa_factura`.`fec_alta_factura` AS `Fecha Registro`,`fa_factura`.`fec_vencimiento_factura` AS `Fecha Vencimiento`,concat(`fa_tipopago`.`num_dias_tipopago`,' ','Dias') AS `Plazo Dias`,concat('$ ',format(`fa_factura`.`sub_total_factura`,2)) AS `Sub Total`,concat('$ ',format(`fa_factura`.`sub_totaldes_factura`,2)) AS `Con Descuento`,concat('$ ',format(`fa_factura`.`imp_factura`,2)) AS `Importe`,concat('$ ',format(`fa_factura`.`imp_adeudado`,2)) AS `Adeudado`,concat('$ ',format(`fa_factura`.`imp_cancelado`,2)) AS `Cancelado`,`fa_factura`.`obs_factura` AS `Observaciones`,`fa_factura`.`not_factura` AS `Notas`,concat(if((`fa_factura`.`ind_recurrente_factura` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Recurrente`,`sys_estado`.`des_estado` AS `Estado Factura`,`fa_config`.`nom_config` AS `Configuracion` from (((((`fa_factura` join `fa_numeracion`) join `fa_cliente`) join `sys_estado`) join `fa_config`) join `fa_tipopago`) where ((`fa_factura`.`cod_numeracion` = `fa_numeracion`.`cod_numeracion`) and (`fa_factura`.`cod_cliente` = `fa_cliente`.`cod_cliente`) and (`fa_factura`.`cod_estado` = `sys_estado`.`cod_estado`) and (`fa_factura`.`cod_config` = `fa_config`.`cod_config`) and (`fa_factura`.`cod_tipopago` = `fa_tipopago`.`cod_tipopago`) and (`fa_factura`.`ind_cotizacion` = 0)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_impuesto`
--
DROP TABLE IF EXISTS `fa_view_impuesto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_impuesto` AS (select concat('<input name="id_dato" class = "radio" data="fa_impuesto" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_impuesto`.`cod_impuesto`,'" >') AS `Cod`,`fa_impuesto`.`nom_impuesto` AS `Nombre`,`fa_impuesto`.`por_impuesto` AS `Porcentaje`,`fa_impuesto`.`des_impuesto` AS `Descripcion`,`fa_tipoimpuesto`.`nom_tipoimpuesto` AS `Tipo Impuesto`,`sys_usuario`.`usuario_usuario` AS `Registro`,`fa_impuesto`.`fec_impuesto` AS `Fecha Registro`,`fa_impuesto`.`hora_impuesto` AS `Hora Registro` from ((`fa_impuesto` join `fa_tipoimpuesto`) join `sys_usuario`) where ((`fa_impuesto`.`cod_usuario` = `sys_usuario`.`cod_usuario`) and (`fa_impuesto`.`cod_tipoimpuesto` = `fa_tipoimpuesto`.`cod_tipoimpuesto`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_inventario`
--
DROP TABLE IF EXISTS `fa_view_inventario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_inventario` AS (select concat('<input name="id_dato" class = "radio" data="fa_inventario" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_inventario`.`cod_inventario`,'" >') AS `Cod`,concat(`fa_item`.`nom_item`,'-',`fa_item`.`ref_item`) AS `Item`,concat(`fa_unimedida`.`nom_unimedida`,'-',`fa_unimedida`.`pre_unimedida`) AS `Unidad Medida`,`fa_inventario`.`can_ini_inventario` AS `Cantidad Inicial`,`fa_inventario`.`imp_uni_inventario` AS `Importe X Uni` from ((`fa_inventario` join `fa_item`) join `fa_unimedida`) where ((`fa_inventario`.`cod_item` = `fa_item`.`cod_item`) and (`fa_inventario`.`cod_unimedida` = `fa_unimedida`.`cod_unimedida`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_item`
--
DROP TABLE IF EXISTS `fa_view_item`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_item` AS (select concat('<input name="id_dato" class = "radio" data="fa_item" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_item`.`cod_item`,'" >') AS `Cod`,`fa_item`.`cod_item` AS `Codigo`,`fa_item`.`nom_item` AS `Nombre Item`,`fa_item`.`ref_item` AS `Referencia`,`fa_item`.`des_item` AS `Descripcion`,`fa_item`.`imp_compra_item` AS `Importe compra`,`fa_item`.`inc_porcen_item` AS `Incremento x Utilidad`,`fa_item`.`imp_venta` AS `Importe Venta`,concat(if((`fa_item`.`ind_inventario_item` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Inventario`,`sys_usuario`.`usuario_usuario` AS `Registro`,`fa_impuesto`.`nom_impuesto` AS `Impuesto`,`con_cuenta`.`nom_cuenta` AS `Categoria` from (((`fa_item` join `fa_impuesto`) join `con_cuenta`) join `sys_usuario`) where ((`fa_item`.`cod_impuesto` = `fa_impuesto`.`cod_impuesto`) and (`fa_item`.`cod_cuenta` = `con_cuenta`.`cod_cuenta`) and (`fa_item`.`cod_usuario` = `sys_usuario`.`cod_usuario`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_moneda`
--
DROP TABLE IF EXISTS `fa_view_moneda`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_moneda` AS (select concat('<input name="id_dato" class = "radio" data="fa_moneda" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_moneda`.`cod_moneda`,'" >') AS `Cod`,`fa_moneda`.`nom_moneda` AS `Nombre`,`fa_moneda`.`des_moneda` AS `Descripcion`,`fa_moneda`.`abr_moneda` AS `Nom Abreviado` from `fa_moneda`);

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_numeracion`
--
DROP TABLE IF EXISTS `fa_view_numeracion`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_numeracion` AS (select concat('<input name="id_dato" class = "radio" data="fa_numeracion" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_numeracion`.`cod_numeracion`,'" >') AS `Cod`,`fa_numeracion`.`nom_numeracion` AS `Nombre`,`fa_numeracion`.`pre_numeracion` AS `Prefijo`,`fa_numeracion`.`num_inicial_numeracion` AS `Numero Inicial`,`fa_numeracion`.`num_final_numeracion` AS `Numero Final`,`fa_numeracion`.`num_sig_numeracion` AS `Numero Siguiente`,`fa_numeracion`.`res_numeracion` AS `Resolucion`,concat(if((`fa_numeracion`.`ind_preferida_numeracion` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Prefierida`,concat(if((`fa_numeracion`.`ind_auto_numeracion` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `AutoIncremento`,`sys_estado`.`des_estado` AS `Estado`,`sys_usuario`.`usuario_usuario` AS `Registro`,`fa_numeracion`.`fec_numeracion` AS `Fecha Registro`,`fa_numeracion`.`hora_numeracion` AS `Hora Registro` from ((`fa_numeracion` join `sys_estado`) join `sys_usuario`) where ((`fa_numeracion`.`cod_usuario` = `sys_usuario`.`cod_usuario`) and (`fa_numeracion`.`cod_estado` = `sys_estado`.`cod_estado`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_orden`
--
DROP TABLE IF EXISTS `fa_view_orden`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_orden` AS (select concat('<input name="id_dato"  data="fa_orden" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_orden`.`cod_orden`,'" >') AS `Cod`,`fa_orden`.`cod_orden` AS `Codigo`,`fa_factura`.`num_factura` AS `Factura`,`fa_orden`.`nom_orden` AS `Nombre`,`fa_orden`.`des_orden` AS `Descripcion`,concat(if((`fa_orden`.`img_orden` <> ''),concat('<a hrerf="modules/sistema/adjuntos/',`fa_orden`.`img_orden`,'"><span class="icon24 icomoon-icon-file-download"></span></a>'),'<span class="icon24 icomoon-icon-file-remove"></span>')) AS `Soporte`,`fa_orden`.`fec_orden` AS `Fecha Registro`,`sys_usuario`.`usuario_usuario` AS `Usuario`,`sys_empresa`.`nom_empresa` AS `nom_empresa`,`fa_orden`.`cod_empresa` AS `cod_empresa` from (((`fa_orden` join `sys_usuario`) join `fa_factura`) join `sys_empresa`) where ((`fa_orden`.`cod_usuario` = `sys_usuario`.`cod_usuario`) and (`fa_orden`.`cod_factura` = `fa_factura`.`cod_factura`) and (`fa_orden`.`cod_empresa` = `sys_empresa`.`cod_empresa`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_regimen`
--
DROP TABLE IF EXISTS `fa_view_regimen`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_regimen` AS (select concat('<input name="id_dato" class = "radio" data="fa_regimen" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_regimen`.`cod_regimen`,'" >') AS `Cod`,`fa_regimen`.`nom_regimen` AS `Nombre`,`fa_regimen`.`des_regimen` AS `Descripcion` from `fa_regimen`);

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_tipoimpuesto`
--
DROP TABLE IF EXISTS `fa_view_tipoimpuesto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_tipoimpuesto` AS (select concat('<input name="id_dato" class = "radio" data="fa_tipoimpuesto" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_tipoimpuesto`.`cod_tipoimpuesto`,'" >') AS `Cod`,`fa_tipoimpuesto`.`nom_tipoimpuesto` AS `Nombre`,`fa_tipoimpuesto`.`des_tipoimpuesto` AS `Descripcion`,`sys_usuario`.`usuario_usuario` AS `Usuario`,`fa_tipoimpuesto`.`fec_tipoimpuesto` AS `Fecha Registro`,`fa_tipoimpuesto`.`hora_tipoimpuesto` AS `Hora Registro` from (`fa_tipoimpuesto` join `sys_usuario`) where (`fa_tipoimpuesto`.`cod_usuario` = `sys_usuario`.`cod_usuario`));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_tipopago`
--
DROP TABLE IF EXISTS `fa_view_tipopago`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_tipopago` AS (select concat('<input name="id_dato" class = "radio" data="fa_tipopago" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_tipopago`.`cod_tipopago`,'" >') AS `Cod`,`fa_tipopago`.`nom_tipopago` AS `Nombre`,`fa_tipopago`.`num_dias_tipopago` AS `# Dias`,`sys_estado`.`des_estado` AS `Estado`,`sys_usuario`.`usuario_usuario` AS `Usuario`,`fa_tipopago`.`fec_tipopago` AS `Fecha Registro`,`fa_tipopago`.`hora_tipopago` AS `Hora Registro` from ((`fa_tipopago` join `sys_usuario`) join `sys_estado`) where ((`fa_tipopago`.`cod_usuario` = `sys_usuario`.`cod_usuario`) and (`fa_tipopago`.`cod_estado` = `sys_estado`.`cod_estado`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `fa_view_unimedida`
--
DROP TABLE IF EXISTS `fa_view_unimedida`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `fa_view_unimedida` AS (select concat('<input name="id_dato" class = "radio" data="fa_unimedida" data-rel="optAcciones" type="radio" id="btoAccion" value="',`fa_unimedida`.`cod_unimedida`,'" >') AS `Cod`,`fa_unimedida`.`nom_unimedida` AS `Nombre`,`fa_unimedida`.`des_unimedida` AS `Descripcion`,`fa_unimedida`.`pre_unimedida` AS `Prefijo`,`sys_usuario`.`usuario_usuario` AS `Usuario`,`fa_unimedida`.`fec_unimedida` AS `Fecha Registro`,`fa_unimedida`.`hora_unimedida` AS `Hora Registro` from (`fa_unimedida` join `sys_usuario`) where (`fa_unimedida`.`cod_usuario` = `sys_usuario`.`cod_usuario`));

-- --------------------------------------------------------

--
-- Estructura para la vista `hue_view_cliente`
--
DROP TABLE IF EXISTS `hue_view_cliente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hue_view_cliente` AS (select concat('<input name="id_dato"  data="hue_cliente" data-rel="optAcciones" type="radio" id="btoAccion" value="',`hue_cliente`.`cod_cliente`,'" >') AS `Cod`,`hue_cliente`.`cod_cliente` AS `Codigo`,`hue_cliente`.`nom_cliente` AS `Nombre`,`hue_cliente`.`nit_cliente` AS `Nit`,`hue_cliente`.`dir_cliente` AS `Direccion`,`hue_cliente`.`email_cliente` AS `Email`,`hue_cliente`.`tel_cliente` AS `Telefono`,`hue_cliente`.`tel1_cliente` AS `Telefono 1`,`hue_cliente`.`fax_cliente` AS `Fax`,`hue_cliente`.`cel_cliente` AS `Celular`,`hue_cliente`.`obs_cliente` AS `Observaciones`,concat(`sys_ciudad`.`nom_ciudad`,'-',`sys_ciudad`.`dpt_ciudad`) AS `Ciudad`,`sys_empresa`.`nom_empresa` AS `Empresa`,`hue_cliente`.`cod_empresa` AS `cod_empresa` from ((`hue_cliente` join `sys_ciudad`) join `sys_empresa`) where ((`hue_cliente`.`cod_ciudad` = `sys_ciudad`.`cod_ciudad`) and (`hue_cliente`.`cod_empresa` = `sys_empresa`.`cod_empresa`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `hue_view_cliente_vehiculo`
--
DROP TABLE IF EXISTS `hue_view_cliente_vehiculo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hue_view_cliente_vehiculo` AS (select concat('<input name="id_dato"  data="hue_cliente_vehiculo" data-rel="optAcciones" type="radio" id="btoAccion" value="',`hue_cliente_vehiculo`.`cod_cliente_vehiculo`,'" >') AS `Cod`,`hue_cliente_vehiculo`.`cod_cliente_vehiculo` AS `Codigo`,concat(if((`hue_cliente_vehiculo`.`fec_entrega_final` <> '0000-00-00 00:00:00'),if((date_format(`hue_cliente_vehiculo`.`fec_entrega`,'%Y-%m-%d 00:00:00') < date_format(now(),'%Y-%m-%d 23:59:59')),'Retraso','Alquilado'),'Normal')) AS `Alerta`,concat(`hue_cliente`.`nom_cliente`,' - ',`hue_cliente`.`nit_cliente`) AS `Cliente`,concat(`hue_vehiculo`.`marca_vehiculo`,' - ',`hue_vehiculo`.`placa_vehiculo`) AS `Vehiculo`,`hue_cliente_vehiculo`.`fec_registro` AS `Fecha registro`,`hue_cliente_vehiculo`.`fec_entrega` AS `Fecha Vencimiento`,`sys_estado`.`des_estado` AS `Estado`,concat(if((`hue_cliente_vehiculo`.`huella_cliente_vehiculo` <> ''),concat('<img src="modules/huella/adjuntos/huellas/',`hue_cliente_vehiculo`.`huella_cliente_vehiculo`,'" style="max-width:40px;" class="image">'),'<span class="icon24 icomoon-icon-image-2"></span>')) AS `Huella`,`hue_cliente_vehiculo`.`obs_cliente_vehiculo` AS `Observaciones`,(to_days(`hue_cliente_vehiculo`.`fec_entrega`) - to_days(`hue_cliente_vehiculo`.`fec_entrega_final`)) AS `Dias Retraso`,`sys_usuario`.`usuario_usuario` AS `Usuario`,`sys_empresa`.`cod_empresa` AS `cod_empresa` from (((((`hue_cliente_vehiculo` join `hue_cliente`) join `hue_vehiculo`) join `sys_estado`) join `sys_usuario`) join `sys_empresa`) where ((`hue_cliente_vehiculo`.`cod_cliente` = `hue_cliente`.`cod_cliente`) and (`hue_cliente_vehiculo`.`cod_vehiculo` = `hue_vehiculo`.`cod_vehiculo`) and (convert(`hue_cliente_vehiculo`.`cod_estado` using utf8) = `sys_estado`.`cod_estado`) and (`hue_cliente_vehiculo`.`cod_usuario` = `sys_usuario`.`cod_usuario`) and (`hue_cliente`.`cod_empresa` = `sys_empresa`.`cod_empresa`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `hue_view_combustible`
--
DROP TABLE IF EXISTS `hue_view_combustible`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hue_view_combustible` AS (select concat('<input name="id_dato"  data="hue_combustible" data-rel="optAcciones" type="radio" id="btoAccion" value="',`hue_combustible`.`cod_combustible`,'" >') AS `Cod`,`hue_combustible`.`cod_combustible` AS `Codigo`,`hue_combustible`.`nom_combustible` AS `Combustible`,`hue_combustible`.`des_combustible` AS `Descripcion`,`hue_combustible`.`fec_combustible` AS `Fecha`,`sys_empresa`.`nom_empresa` AS `Empresa`,`sys_estado`.`des_estado` AS `Estado`,`sys_usuario`.`usuario_usuario` AS `Usuario`,`hue_combustible`.`cod_empresa` AS `cod_empresa` from (((`hue_combustible` join `sys_usuario`) join `sys_empresa`) join `sys_estado`) where ((`hue_combustible`.`cod_empresa` = `sys_empresa`.`cod_empresa`) and (convert(`hue_combustible`.`cod_estado` using utf8) = `sys_estado`.`cod_estado`) and (`hue_combustible`.`cod_usuario` = `sys_usuario`.`cod_usuario`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `hue_view_tip_documento`
--
DROP TABLE IF EXISTS `hue_view_tip_documento`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hue_view_tip_documento` AS (select concat('<input name="id_dato"  data="hue_tip_documento" data-rel="optAcciones" type="radio" id="btoAccion" value="',`hue_tip_documento`.`cod_tip_documento`,'" >') AS `Cod`,`hue_tip_documento`.`cod_tip_documento` AS `Codigo`,`hue_tip_documento`.`nom_tip_documento` AS `Documento`,`hue_tip_documento`.`des_tip_documento` AS `Descripcion`,`hue_tip_documento`.`fec_tip_documento` AS `Fecha`,`sys_empresa`.`nom_empresa` AS `Empresa`,`sys_usuario`.`usuario_usuario` AS `Usuario`,`sys_estado`.`des_estado` AS `Estado`,`hue_tip_documento`.`cod_empresa` AS `cod_empresa` from (((`hue_tip_documento` join `sys_empresa`) join `sys_estado`) join `sys_usuario`) where ((`hue_tip_documento`.`cod_empresa` = `sys_empresa`.`cod_empresa`) and (convert(`hue_tip_documento`.`cod_estado` using utf8) = `sys_estado`.`cod_estado`) and (`hue_tip_documento`.`cod_usuario` = `sys_usuario`.`cod_usuario`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `hue_view_tip_servicio`
--
DROP TABLE IF EXISTS `hue_view_tip_servicio`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hue_view_tip_servicio` AS (select concat('<input name="id_dato"  data="hue_tip_servicio" data-rel="optAcciones" type="radio" id="btoAccion" value="',`hue_tip_servicio`.`cod_tip_servicio`,'" >') AS `Cod`,`hue_tip_servicio`.`cod_tip_servicio` AS `Codigo`,`hue_tip_servicio`.`nom_tip_servicio` AS `Servicio`,`hue_tip_servicio`.`des_tip_servicio` AS `Descripcion`,`hue_tip_servicio`.`fec_tip_servicio` AS `Fecha`,`sys_empresa`.`nom_empresa` AS `Empresa`,`sys_usuario`.`usuario_usuario` AS `Usuario`,`sys_estado`.`des_estado` AS `Estado`,`hue_tip_servicio`.`cod_empresa` AS `cod_empresa` from (((`hue_tip_servicio` join `sys_empresa`) join `sys_estado`) join `sys_usuario`) where ((`hue_tip_servicio`.`cod_empresa` = `sys_empresa`.`cod_empresa`) and (convert(`hue_tip_servicio`.`cod_estado` using utf8) = `sys_estado`.`cod_estado`) and (`hue_tip_servicio`.`cod_usuario` = `sys_usuario`.`cod_usuario`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `hue_view_vehiculo`
--
DROP TABLE IF EXISTS `hue_view_vehiculo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hue_view_vehiculo` AS (select concat('<input name="id_dato"  data="hue_vehiculo" data-rel="optAcciones" type="radio" id="btoAccion" value="',`hue_vehiculo`.`cod_vehiculo`,'" >') AS `Cod`,`hue_vehiculo`.`cod_vehiculo` AS `Codigo`,`hue_vehiculo`.`placa_vehiculo` AS `Placa`,`hue_vehiculo`.`marca_vehiculo` AS `Marca`,`hue_vehiculo`.`linea_vehiculo` AS `Linea`,`hue_vehiculo`.`modelo_vehiculo` AS `Modelo`,`hue_vehiculo`.`lic_trans_vehiculo` AS `Lic Transito`,`hue_vehiculo`.`cc_vehiculo` AS `Cilindraje`,`hue_vehiculo`.`color_vehiculo` AS `Color`,`hue_vehiculo`.`tipo_carro_vehiculo` AS `Carroceria`,`hue_vehiculo`.`cap_vehiculo` AS `Capacidad`,`hue_vehiculo`.`num_motor_vehiculo` AS `Nro Motor`,`hue_vehiculo`.`num_serie_vehiculo` AS `Nro Serie`,`hue_vehiculo`.`num_chasis_vehiculo` AS `Nro Chasis`,concat(if((`hue_vehiculo`.`img_vehiculo` <> ''),concat('<img src="modules/huella/adjuntos/',`hue_vehiculo`.`img_vehiculo`,'" style="max-width:40px;" class="image">'),'<span class="icon24 icomoon-icon-image-2"></span>')) AS `Imagen`,`hue_vehiculo_clase`.`nom_vehiculo_clase` AS `clase`,`hue_tip_servicio`.`nom_tip_servicio` AS `Tipo Servicio`,`hue_combustible`.`nom_combustible` AS `Combustible`,`sys_empresa`.`nom_empresa` AS `Empresa`,`sys_estado`.`des_estado` AS `Estado`,`hue_vehiculo`.`cod_empresa` AS `cod_empresa` from (((((`hue_vehiculo` join `hue_vehiculo_clase`) join `hue_tip_servicio`) join `hue_combustible`) join `sys_empresa`) join `sys_estado`) where ((`hue_vehiculo`.`cod_vehiculo_clase` = `hue_vehiculo_clase`.`cod_vehiculo_clase`) and (`hue_vehiculo`.`cod_tip_servicio` = `hue_tip_servicio`.`cod_tip_servicio`) and (`hue_vehiculo`.`cod_combustible` = `hue_combustible`.`cod_combustible`) and (`hue_vehiculo`.`cod_empresa` = `sys_empresa`.`cod_empresa`) and (convert(`hue_vehiculo`.`cod_estado` using utf8) = `sys_estado`.`cod_estado`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `hue_view_vehiculo_clase`
--
DROP TABLE IF EXISTS `hue_view_vehiculo_clase`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hue_view_vehiculo_clase` AS (select concat('<input name="id_dato"  data="hue_vehiculo_clase" data-rel="optAcciones" type="radio" id="btoAccion" value="',`hue_vehiculo_clase`.`cod_vehiculo_clase`,'" >') AS `Cod`,`hue_vehiculo_clase`.`cod_vehiculo_clase` AS `Codigo`,`hue_vehiculo_clase`.`nom_vehiculo_clase` AS `Clase`,`hue_vehiculo_clase`.`des_vehiculo_clase` AS `Descripcion`,`hue_vehiculo_clase`.`fec_vehiculo_clase` AS `Fecha`,`sys_empresa`.`nom_empresa` AS `Empresa`,`sys_estado`.`des_estado` AS `Estado`,`sys_usuario`.`usuario_usuario` AS `Usuario`,`hue_vehiculo_clase`.`cod_empresa` AS `cod_empresa` from (((`hue_vehiculo_clase` join `sys_usuario`) join `sys_empresa`) join `sys_estado`) where ((`hue_vehiculo_clase`.`cod_empresa` = `sys_empresa`.`cod_empresa`) and (convert(`hue_vehiculo_clase`.`cod_estado` using utf8) = `sys_estado`.`cod_estado`) and (`hue_vehiculo_clase`.`cod_usuario` = `sys_usuario`.`cod_usuario`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `hue_view_vehiculo_datos`
--
DROP TABLE IF EXISTS `hue_view_vehiculo_datos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hue_view_vehiculo_datos` AS (select concat('<input name="id_dato"  data="hue_vehiculo_datos" data-rel="optAcciones" type="radio" id="btoAccion" value="',`hue_vehiculo_datos`.`cod_vehiculo_datos`,'" >') AS `Cod`,`hue_vehiculo_datos`.`cod_vehiculo_datos` AS `Codigo`,concat(`hue_vehiculo`.`marca_vehiculo`,' - ',`hue_vehiculo`.`placa_vehiculo`) AS `Vehiculo`,`hue_vehiculo_datos`.`res_mov_vehiculo_datos` AS `Restriccion de Movilidad`,concat(if((`hue_vehiculo_datos`.`blindaje_vehiculo_datos` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Blindaje`,`hue_vehiculo_datos`.`pot_vehiculo_datos` AS `Potencia`,`hue_vehiculo_datos`.`num_pue_vehiculo_datos` AS `Nro Puertas`,`hue_vehiculo_datos`.`lim_pro_vehiculo_datos` AS `Limitaciones de propiedad`,`hue_vehiculo_datos`.`fec_mat_vehiculo_datos` AS `Fecha de Matricula`,`hue_vehiculo_datos`.`fec_exp_lic_vehiculo_datos` AS `Expedicion de licencia`,`hue_vehiculo_datos`.`fec_ven_vehiculo_datos` AS `Fecha de Vencimiento`,`hue_vehiculo_datos`.`tt_vehiculo_datos` AS `Organismo de transito`,`hue_vehiculo_datos`.`lat_vehiculo_datos` AS `Nro Lateral`,`hue_vehiculo`.`cod_empresa` AS `cod_empresa` from (`hue_vehiculo_datos` join `hue_vehiculo`) where (`hue_vehiculo_datos`.`cod_vehiculo` = `hue_vehiculo`.`cod_vehiculo`));

-- --------------------------------------------------------

--
-- Estructura para la vista `hue_view_vehiculo_documentos`
--
DROP TABLE IF EXISTS `hue_view_vehiculo_documentos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `hue_view_vehiculo_documentos` AS (select concat('<input name="id_dato"  data="hue_vehiculo_documentos" data-rel="optAcciones" type="radio" id="btoAccion" value="',`hue_vehiculo_documentos`.`cod_vehiculo_documentos`,'" >') AS `Cod`,`hue_vehiculo_documentos`.`cod_vehiculo_documentos` AS `Codigo`,concat(`hue_vehiculo`.`marca_vehiculo`,' - ',`hue_vehiculo`.`placa_vehiculo`) AS `Vehiculo`,`hue_tip_documento`.`nom_tip_documento` AS `Documento`,`hue_vehiculo_documentos`.`num_vehiculo_documento` AS `Nro Documento`,`hue_vehiculo_documentos`.`fec_vehiculo_documento` AS `Fecha de Registro`,`hue_vehiculo_documentos`.`fec_ven_vehiculo_documento` AS `Vigencia Hasta`,concat(if((date_format(`hue_vehiculo_documentos`.`fec_ven_vehiculo_documento`,'%Y-%m-%d 00:00:00') < date_format(now(),'%Y-%m-%d 23:59:59')),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Vencido`,`hue_vehiculo`.`cod_empresa` AS `cod_empresa` from ((`hue_vehiculo_documentos` join `hue_vehiculo`) join `hue_tip_documento`) where ((`hue_vehiculo_documentos`.`cod_vehiculo` = `hue_vehiculo`.`cod_vehiculo`) and (`hue_vehiculo_documentos`.`cod_tip_documento` = `hue_tip_documento`.`cod_tip_documento`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_ciudad`
--
DROP TABLE IF EXISTS `sys_view_ciudad`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_ciudad` AS (select concat('<input name="id_dato"  data="sys_ciudad" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_ciudad`.`cod_ciudad`,'" >') AS `Cod`,`sys_ciudad`.`cod_ciudad` AS `Codigo`,`sys_ciudad`.`nom_ciudad` AS `Nombre`,`sys_ciudad`.`dpt_ciudad` AS `Departamento` from `sys_ciudad`);

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_contrato`
--
DROP TABLE IF EXISTS `sys_view_contrato`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_contrato` AS (select concat('<input name="id_dato"  data="sys_contrato" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_contrato`.`cod_contrato`,'" >') AS `Codigo`,`sys_contrato`.`nom_contrato` AS `Nombre`,`sys_contrato`.`des_contrato` AS `Descripcion del contrato`,`sys_contrato`.`num_meses_contrato` AS `Vigencia` from `sys_contrato`);

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_detframe`
--
DROP TABLE IF EXISTS `sys_view_detframe`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_detframe` AS (select concat('<input name="id_dato"  data="sys_detframe" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_detframe`.`cod_detframe`,'" >') AS `Cod`,`sys_detframe`.`cod_detframe` AS `Codigo`,`sys_tipoinput`.`nom_tipoInput` AS `Input`,`sys_detframe`.`nom_tablaref` AS `Tabla Referencia`,`sys_detframe`.`nom_campo` AS `Nombre Campo`,`sys_detframe`.`holder_campo` AS `Holder Campo`,`sys_detframe`.`tam_campo` AS `Tam Campo`,`sys_detframe`.`det_campo` AS `Detalle Campo`,`sys_detframe`.`val_campo` AS `Valor Campo`,`sys_frame`.`nom_frame` AS `Frame Padre`,`sys_estado`.`des_estado` AS `Estado` from (((`sys_detframe` join `sys_tipoinput`) join `sys_frame`) join `sys_estado`) where ((`sys_detframe`.`cod_tipoinput` = `sys_tipoinput`.`cod_tipoInput`) and (`sys_detframe`.`cod_frame` = `sys_frame`.`cod_frame`) and (convert(`sys_detframe`.`cod_estado` using utf8) = `sys_estado`.`cod_estado`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_empresa`
--
DROP TABLE IF EXISTS `sys_view_empresa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_empresa` AS (select concat('<input name="id_dato"  data="sys_empresa" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_empresa`.`cod_empresa`,'" >') AS `Cod`,`sys_empresa`.`cod_empresa` AS `Codigo`,`sys_empresa`.`nom_empresa` AS `Nombre`,`sys_empresa`.`nit_empresa` AS `Nit`,`sys_empresa`.`rep_empresa` AS `Representante`,`sys_empresa`.`tel_empresa` AS `Telefono`,`sys_empresa`.`dir_empresa` AS `Direccion`,`sys_empresa`.`web_empresa` AS `Pagina Web`,`sys_empresa`.`email_empresa` AS `Email`,concat(if((`sys_empresa`.`img_empresa` <> ''),concat('<img src="modules/sistema/adjuntos/',`sys_empresa`.`img_empresa`,'" style="max-width:40px;" class="image">'),'<span class="icon32 icomoon-icon-image-2"></span>')) AS `Imagen`,concat(`sys_ciudad`.`nom_ciudad`,' - ',`sys_ciudad`.`dpt_ciudad`) AS `Ciudad - Dpto`,`fa_regimen`.`nom_regimen` AS `Perfil`,concat(`fa_moneda`.`abr_moneda`,' - ',`fa_moneda`.`nom_moneda`) AS `Moneda` from (((`sys_empresa` join `sys_ciudad`) join `fa_regimen`) join `fa_moneda`) where ((`sys_empresa`.`cod_ciudad` = `sys_ciudad`.`cod_ciudad`) and (`sys_empresa`.`cod_regimen` = `fa_regimen`.`cod_regimen`) and (`sys_empresa`.`cod_moneda` = `fa_moneda`.`cod_moneda`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_empresa_contrato`
--
DROP TABLE IF EXISTS `sys_view_empresa_contrato`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_empresa_contrato` AS (select concat('<input name="id_dato" data="sys_empresa_contrato" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_empresa_contrato`.`cod_empresa_contrato`,'" >') AS `Cod`,`sys_empresa_contrato`.`cod_empresa_contrato` AS `Codigo`,`sys_empresa`.`nom_empresa` AS `Empresa`,`sys_contrato`.`nom_contrato` AS `Contrato`,`sys_estado`.`des_estado` AS `Estado Actual`,`mod_modulo`.`nom_modulo` AS `Modulo Contratado`,`sys_empresa_contrato`.`fec_inicio_empresa_contrato` AS `Fecha Inicio`,`sys_empresa_contrato`.`fec_baja_empresa_contrato` AS `Fecha Cierre`,`sys_usuario`.`usuario_usuario` AS `Usuario` from (((((`sys_empresa_contrato` join `sys_empresa`) join `sys_contrato`) join `mod_modulo`) join `sys_usuario`) join `sys_estado`) where ((`sys_empresa_contrato`.`cod_empresa` = `sys_empresa`.`cod_empresa`) and (`sys_empresa_contrato`.`cod_contrato` = `sys_contrato`.`cod_contrato`) and (`sys_empresa_contrato`.`cod_modulo` = `mod_modulo`.`cod_modulo`) and (`sys_empresa_contrato`.`cod_usuario` = `sys_usuario`.`cod_usuario`) and (`sys_empresa_contrato`.`cod_estado` = `sys_estado`.`cod_estado`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_estado`
--
DROP TABLE IF EXISTS `sys_view_estado`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_estado` AS (select concat('<input name="id_dato" class = "radio" data="sys_estado" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_estado`.`cod_estado`,'" >') AS `Cod`,`sys_estado`.`cod_estado` AS `Codigo`,`sys_estado`.`des_estado` AS `Descripcion del estado`,`mod_modulo`.`nom_modulo` AS `Modulo`,`sys_estado`.`fec_estado` AS `Fecha Registro`,`sys_estado`.`hora_estado` AS `Hora Registro` from (`sys_estado` join `mod_modulo`) where (`sys_estado`.`mod_estado` = `mod_modulo`.`cod_modulo`));

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_formulario`
--
DROP TABLE IF EXISTS `sys_view_formulario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_formulario` AS (select concat('<input name="id_dato"  data="sys_formulario" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_formulario`.`cod_formulario`,'" >') AS `Cod`,`sys_formulario`.`cod_formulario` AS `Codigo`,`sys_formulario`.`nom_formulario` AS `Nombre`,`sys_formulario`.`nom_tabla` AS `Esquema`,concat(if((`sys_formulario`.`tip_formulario` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `En Linea`,concat(if((`sys_formulario`.`dat_formulario` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Trae Datos`,concat(if((`sys_formulario`.`tit_formulario` is not null),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `Tiene Ayuda`,`sys_formulario`.`fec_formulario` AS `Fecha Registro` from `sys_formulario`);

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_formulario_metodos`
--
DROP TABLE IF EXISTS `sys_view_formulario_metodos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_formulario_metodos` AS (select concat('<input name="id_dato"  data="sys_formulario_metodos" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_formulario_metodos`.`cod_formulario_metodos`,'" >') AS `Cod`,`sys_formulario_metodos`.`cod_formulario_metodos` AS `Codigo`,`sys_formulario`.`nom_formulario` AS `Formulario`,`sys_metodos`.`nom_metodos` AS `Metodos` from ((`sys_formulario_metodos` join `sys_formulario`) join `sys_metodos`) where ((`sys_formulario_metodos`.`cod_formulario` = `sys_formulario`.`cod_formulario`) and (`sys_formulario_metodos`.`cod_metodos` = `sys_metodos`.`cod_metodos`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_frame`
--
DROP TABLE IF EXISTS `sys_view_frame`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_frame` AS (select concat('<input name="id_dato"  data="sys_frame" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_frame`.`cod_frame`,'" >') AS `Cod`,`sys_frame`.`cod_frame` AS `Codigo`,`sys_frame`.`nom_frame` AS `Nombre`,`sys_frame`.`nom_tabla` AS `Esquema`,`sys_frame`.`id_frame` AS `Tam`,`sys_usuario`.`usuario_usuario` AS `Usuario Registra`,`sys_formulario`.`nom_formulario` AS `Fomulario Padre`,concat(if((`sys_frame`.`ind_enlinea` = 1),'<i class="icomoon-icon-checkmark blue"></i>','<i class="icomoon-icon-close red"></i>')) AS `En Linea`,`sys_frame`.`fec_frame` AS `Fecha Registro` from ((`sys_frame` join `sys_usuario`) join `sys_formulario`) where ((`sys_frame`.`cod_usuario` = `sys_usuario`.`cod_usuario`) and (`sys_frame`.`cod_formulario` = `sys_formulario`.`cod_formulario`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_mensajes`
--
DROP TABLE IF EXISTS `sys_view_mensajes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_mensajes` AS (select concat('<input name="id_dato"  data="sys_mensajes" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_mensajes`.`cod_mensajes`,'" >') AS `Codigo`,concat(`sys_usuario`.`nom_usuario`,`sys_usuario`.`ape_usuario`) AS `De`,concat(if((`sys_usuario`.`img_usuario` <> ''),concat('<img src="modules/sistema/adjuntos/',`sys_usuario`.`img_usuario`,'" style="max-width:40px;" class="image">'),'<span class="icon32 icomoon-icon-image-2"></span>')) AS `Avatar`,`sys_mensajes`.`asu_mensajes` AS `Asunto`,`sys_mensajes`.`des_mensajes` AS `Descripcion`,concat(if((`sys_mensajes`.`img_mensajes` <> ''),concat('<a href="modules/sistema/adjuntos/',`sys_usuario`.`img_usuario`,'"><span class="icon24 icomoon-icon-attachment"></span></a>'),'')) AS `Adjuntos`,`sys_mensajes`.`fec_mensajes` AS `Fecha`,`sys_mensajes`.`hora_mensajes` AS `Hora`,`sys_mensajes`.`a_cod_usuario` AS `Para` from (`sys_mensajes` join `sys_usuario`) where (`sys_mensajes`.`de_cod_usuario` = `sys_usuario`.`cod_usuario`));

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_metodo`
--
DROP TABLE IF EXISTS `sys_view_metodo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_metodo` AS (select concat('<input name="id_dato"  data="sys_metodos" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_metodos`.`cod_metodos`,'" >') AS `Cod`,`sys_metodos`.`cod_metodos` AS `Codigo`,`sys_metodos`.`nom_metodos` AS `Nombre`,`sys_metodos`.`des_metodos` AS `Descripcion`,`sys_metodos`.`btn_metodos` AS `Boton`,concat('<i class="',`sys_metodos`.`ico_metodos`,'"></i>') AS `icono`,`sys_metodos`.`tip_metodos` AS `Tipo`,`sys_metodos`.`cla_metodos` AS `clase`,`sys_metodos`.`href_metodos` AS `Link/Url` from `sys_metodos`);

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_perfil`
--
DROP TABLE IF EXISTS `sys_view_perfil`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_perfil` AS (select concat('<input name="id_dato"  data="sys_perfil" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_perfil`.`cod_perfil`,'" >') AS `Cod`,`sys_perfil`.`cod_perfil` AS `Codigo`,`sys_perfil`.`nom_perfil` AS `Nombre`,`sys_perfil`.`des_perfil` AS `Descripcion`,`sys_perfil`.`fec_perfil` AS `Fecha Registro` from `sys_perfil`);

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_perfil_metodos`
--
DROP TABLE IF EXISTS `sys_view_perfil_metodos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_perfil_metodos` AS (select concat('<input name="id_dato"  data="sys_perfil_metodos" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_perfil_metodos`.`cod_perfil_metodos`,'" >') AS `Cod`,`sys_perfil_metodos`.`cod_perfil_metodos` AS `Codigo`,`sys_perfil`.`nom_perfil` AS `Perfil`,`sys_metodos`.`nom_metodos` AS `Metodo`,`mod_modulo`.`nom_modulo` AS `Modulo`,`sys_perfil_metodos`.`uri_perfil_metodos` AS `Url`,`sys_perfil_metodos`.`met_perfil_metodos` AS `Metodo Interno`,`sys_estado`.`des_estado` AS `Estado` from ((((`sys_perfil_metodos` join `sys_perfil`) join `sys_metodos`) join `mod_modulo`) join `sys_estado`) where ((`sys_perfil_metodos`.`cod_perfil` = `sys_perfil`.`cod_perfil`) and (`sys_perfil_metodos`.`cod_metodos` = `sys_metodos`.`cod_metodos`) and (`sys_perfil_metodos`.`cod_estado` = `sys_estado`.`cod_estado`) and (`sys_perfil_metodos`.`cod_modulo` = `mod_modulo`.`cod_modulo`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `sys_view_usuario`
--
DROP TABLE IF EXISTS `sys_view_usuario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sys_view_usuario` AS (select concat('<input name="id_dato"  data="sys_usuario" data-rel="optAcciones" type="radio" id="btoAccion" value="',`sys_usuario`.`cod_usuario`,'" >') AS `Cod`,`sys_usuario`.`nom_usuario` AS `Nombre`,`sys_usuario`.`ape_usuario` AS `Apellido`,`sys_usuario`.`dir_usuario` AS `Direccion`,`sys_usuario`.`tel_usuario` AS `Telefono`,`sys_usuario`.`email_usuario` AS `Email`,concat(if((`sys_usuario`.`img_usuario` <> ''),concat('<img src="modules/sistema/adjuntos/',`sys_usuario`.`img_usuario`,'" style="max-width:40px;" class="image">'),'<span class="icon24 icomoon-icon-image-2"></span>')) AS `Imagen`,`sys_usuario`.`usuario_usuario` AS `Usuario`,concat(`sys_usuario`.`cod_estado`,' - ',`sys_estado`.`des_estado`) AS `Estado`,concat(`sys_perfil`.`nom_perfil`,' - ',`sys_perfil`.`des_perfil`) AS `Perfil` from (((`sys_usuario` join `sys_usuario_perfil`) join `sys_perfil`) join `sys_estado`) where ((`sys_usuario`.`cod_usuario` = `sys_usuario_perfil`.`cod_usuario`) and (`sys_usuario_perfil`.`cod_perfil` = `sys_perfil`.`cod_perfil`) and (`sys_usuario`.`cod_estado` = `sys_estado`.`cod_estado`)));

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aud_tablas`
--
ALTER TABLE `aud_tablas`
  ADD CONSTRAINT `fk_aud_met` FOREIGN KEY (`cod_metodos`) REFERENCES `sys_metodos` (`cod_metodos`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_aud_usu` FOREIGN KEY (`cod_usuario`) REFERENCES `sys_usuario` (`cod_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `con_cuenta`
--
ALTER TABLE `con_cuenta`
  ADD CONSTRAINT `fk_cue_tipcue` FOREIGN KEY (`cod_tipocuenta`) REFERENCES `con_tipocuenta` (`cod_tipocuenta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cue_usu` FOREIGN KEY (`cod_usuario`) REFERENCES `sys_usuario` (`cod_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `con_detnota`
--
ALTER TABLE `con_detnota`
  ADD CONSTRAINT `fk_detnot_nota` FOREIGN KEY (`cod_nota`) REFERENCES `con_nota` (`cod_nota`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `con_mov_contable`
--
ALTER TABLE `con_mov_contable`
  ADD CONSTRAINT `fk_movcon_cuen` FOREIGN KEY (`cod_cuenta`) REFERENCES `con_cuenta` (`cod_cuenta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movcon_usu` FOREIGN KEY (`cod_usuario`) REFERENCES `sys_usuario` (`cod_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `con_nota`
--
ALTER TABLE `con_nota`
  ADD CONSTRAINT `fk_notcre_cli` FOREIGN KEY (`cod_cliente`) REFERENCES `fa_cliente` (`cod_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notcre_usu` FOREIGN KEY (`cod_usuario`) REFERENCES `sys_usuario` (`cod_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_not_tipnot` FOREIGN KEY (`cod_tiponota`) REFERENCES `con_tiponota` (`cod_tiponota`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `dat_universidad`
--
ALTER TABLE `dat_universidad`
  ADD CONSTRAINT `fk_datuniv_datnatur` FOREIGN KEY (`cod_naturaleza_juridica`) REFERENCES `dat_naturaleza_juridica` (`cod_naturaleza_juridica`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_datuniv_sysest` FOREIGN KEY (`cod_estado`) REFERENCES `sys_estado` (`cod_estado`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `sys_empresa_contrato`
--
ALTER TABLE `sys_empresa_contrato`
  ADD CONSTRAINT `fk_empcon_con` FOREIGN KEY (`cod_contrato`) REFERENCES `sys_contrato` (`cod_contrato`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_empcon_emp` FOREIGN KEY (`cod_empresa`) REFERENCES `sys_empresa` (`cod_empresa`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_empcon_usu` FOREIGN KEY (`cod_usuario`) REFERENCES `sys_usuario` (`cod_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
