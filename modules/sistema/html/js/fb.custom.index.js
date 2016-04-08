$(document).ready(function() {
    var Err    = parseInt($("#err").attr('data'));
    var msj    = $("#err").attr('data_msj');
    var _data  = '';
    if(Err > 0 && Err < 5){
        alert_data('Mensaje App','Notificacion',msj,'#C46A69');
    }else if(Err > 5 && Err < 10){		
        alert_data('Mensaje App','Notificacion',msj,'#296191');
    }

    $('body').on('click', '#btoAccion', function(e) {
        var data     = $(this).attr('data');
        var _data    = $(this).attr('data');
        var data_rel = $(this).attr('data-rel');
        var _botton  = $(this);
        var _app, _met, _arg, _url;
        console.log("data: " + data + " data_rel: " + data_rel);
        switch (data_rel) {
            case ("mensajes"):
                $(this).parent().parent().remove();
                break;
            case ("notificaciones"):
                $(this).parent().remove();
                break;
            case("SELECCIONART"):
                $('.nostyle option').prop('selected', true);				
                break;
            case ('selectUsuario'):
                alert(data);
                break;
            case ('selectEmpresa'):
                alert($(this).val());
                break;
            case ('optAcciones'):
                switch(_type){
                    case('checkbox'):
                        var _marcado = $(this).prop("checked") ? true: false;
                        if(!_marcado){
                           $(this).prop('checked', true);
                            EstValoresRadio($(this),0);
                        }else{
                            $(this).prop('checked', false);
                            EstValoresRadio($(this),1);
                        }
                    break;
                    case('radio'):
                        $(this).prop('checked', true);
                        EstValoresRadio($(this),0);
                    break;
                }
                //EstValoresRadio($(this));
            break;
            case ('AGREGAR ITEM'):
                var _continua = true;
                switch($('input[name="no_esq_tabla"]').val()){
                    case('endodoncia_config_dental'):
                        var _eClonar = $("div.clonar:last");
                        var _id;
                        var _divDiente;
                        _eClonar = _eClonar.children('div');
                        _eClonar = _eClonar.children('div');
                        _id = _eClonar.children('#cod_imagen_dental').val();
                        if(_id==''){
                            alert_data('Mensaje App','Error','No ha seleccionado ningun diente para esta fila. ','#C46A69');
                            _continua = false;
                        }
                    break;
                }
                if(_continua){
                    $("input[id='cantidad']").each(function(i) {$(this).spinner('destroy');});
                    $(".selectFiltro").each(function(i){$(this).select2('destroy');});
                    $("input[id='descuento']").each(function(i) {$(this).mask("destroy");});
                    $(".selectFecha").each(function(i) {$(this).datepicker("destroy");});
                    var eClonar = $("div.clonar:last");
                    eClonar.after(eClonar.clone(true).hide().fadeIn('slow'));
                    var indice = eClonar.attr('data-indice');
                    var tipoContenedor = eClonar.attr('data-nombre');
                    indice++;
                    var nuevoElemento = $('div.clonar[data-nombre=' + tipoContenedor + ']:last');
                    nuevoElemento.attr('data-indice', indice);
                    nuevoElemento.find('#subTotal').remove();
                    nuevoElemento.find('input,select').val('');
                    $("input[id='descuento']").each(function(i) {$(this).mask("999");});
                    $("input[id='cantidad']").each(function(i) {$(this).spinner();});
                    $(".selectFiltro").each(function(i){$(this).select2();});
                    $(".selectFecha").each(function(i) {$(this).datepicker();});
                    var altura = $(document).height();
                    $("html, body").animate({scrollTop:altura+"px"});
                }
            break;
            case ('ELIMINAR FILA'):
                var iCount = 0;
                $("div.clonar").each(function(){iCount++;});
                if(iCount>1){
                    switch($('input[name="no_esq_tabla"]').val()){
                        case('fa_factura'):
                            var bTotal = 0;
                            $('.subTotal').each( function(){bTotal =  bTotal + parseFloat($(this).attr('data-total'));});
                            var subTotal = 0;
                            $('.subTotal').each( function(){subTotal =  subTotal + parseFloat($(this).attr('data-subtotal'));});
                            var subTotalDes = 0;
                            $('.subTotal').each( function(){subTotalDes =  subTotalDes + parseFloat($(this).attr('data-destotal'));});
                            var subTotalImp = 0;
                            $('.subTotal').each( function(){subTotalImp =  subTotalImp + parseFloat($(this).attr('data-imptotal'));});
                            $('input[name="sub_total_factura"]').val(bTotal.toFixed(2));
                            $('#SubTotal').remove();
                            $('#SubTotal').remove();
                            $('#SubTotal1').remove();
                            $('#SubTotal2').remove();
                            $('#SubTotal3').remove();
                            //lleno los input que se envian al insert en el modelo
                            $('input[name="sub_total_factura"]').val(subTotal.toFixed(2));
                            $('input[name="sub_totaldes_factura"]').val(subTotalDes.toFixed(2));
                            $('input[name="imp_factura"]').val(subTotalImp.toFixed(2));
                            $('input[name="imp_adeudado"]').val(bTotal.toFixed(2));
                            var pTotDiv = $('div.clonar').parent().parent().parent();
                            bTotal = formato_numero(bTotal,2,',','.');
                            subTotal = formato_numero(subTotal,2,',','.');
                            subTotalDes = formato_numero(subTotalDes,2,',','.');
                            subTotalImp = formato_numero(subTotalImp,2,',','.');
                            pTotDiv.after("<div class='row' id='SubTotal1'><div class='col-sm-8'></div><div class='col-sm-4'><div class='well well-sm  bg-color-white txt-color-black no-border'><div class='fa-lg'>Sub Total :<span class='ptotal pull-right'>$ "+subTotal+"</span></div></div></div></div><div class='row' id='SubTotal2'><div class='col-sm-8'></div><div class='col-sm-4'><div class='well well-sm  bg-color-white txt-color-black no-border'><div class='fa-lg'>Descuento :<span class='ptotal pull-right'>$ "+subTotalDes+"</span></div></div></div></div><div class='row' id='SubTotal3'><div class='col-sm-8'></div><div class='col-sm-4'><div class='well well-sm  bg-color-white txt-color-black no-border'><div class='fa-lg'>Impuesto :<span class='ptotal pull-right'>$ "+subTotalImp+"</span></div></div></div></div><div class='row' id='SubTotal'><div class='col-sm-8'></div><div class='col-sm-4'><div class='well well-sm  bg-color-darken txt-color-white no-border'><div class='fa-lg'>Total :<span class='ptotal pull-right'>$ "+bTotal+"</span></div></div></div></div>");
                        break;
                        case('endodoncia_config_dental'):
                            var _eClonar = $("div.clonar:last");
                            var _id;
                            var _divDiente;
                            _eClonar = _eClonar.children('div');
                            _eClonar = _eClonar.children('div');
                            _id = _eClonar.children('#cod_imagen_dental').val();
                            _divDiente = $('div[data="'+_id+'"]');
                            _divDiente.toggleClass('active1');
                        break;
                    }
                    $("div.clonar:last").remove();
                }
            break; 
            case ('MODIFICAR'):
                var _sel = false;
                $('input[name="id_dato"]').each(function(){if($(this).is(':checked')){_sel=true;}});
                if(!_sel){alert_data('Mensaje App','error','Debe seleccionar un registro','#C46A69');return false;}
            break;
            case ('MODIFICAR ADMIN'):
                var _sel = false;
                $('input[name="id_dato"]').each(function(){if($(this).is(':checked')){_sel=true;}});
                if(!_sel){alert_data('Mensaje App','error','Debe seleccionar un registro','#C46A69');return false;}
            break;
            case ('DETALLES'):
                var _sel = false;
                $('input[name="id_dato"]').each(function(){if($(this).is(':checked')){_sel=true;}});
                if(!_sel){alert_data('Mensaje App','error','Debe seleccionar un registro','#C46A69');return false;}    
            break;
            case ('GESTIONAR'):
                var _sel = false;
                $('input[name="id_dato"]').each(function(){if($(this).is(':checked')){_sel=true;}});
                if(!_sel){alert_data('Mensaje App','error','Debe seleccionar un registro','#C46A69');return false;}    
            break;
            case 'GENERARPDF':
                var _sel = false;
                $('input[name="id_dato"]').each(function(){if($(this).is(':checked')){_sel=true;}});
                if(!_sel){alert_data('Mensaje App','error','Debe seleccionar un registro','#C46A69');return false;} 
            break;
            case ('ELIMINAR'):
                var _sel = false;
                $('input[name="id_dato"]').each(function(){if($(this).is(':checked')){_sel=true;}});
                if(!_sel){
                    alert_data('Mensaje App','error','Debe seleccionar un registro','#C46A69');return false;
                }else{
                    var _tr = $('input[name=id_dato]:checked').parent().parent();
                    var _table = $(this).attr("data-table");
                    var _id    = $(this).attr("data-id");
                    var _controller = $.base64.decode($(this).attr("href").replace("?app=",""));
                    e.preventDefault();
                    $.SmartMessageBox({
                        title : "<i class='fa fa-exclamation-triangle' style='color:red'></i> Mensaje de Alerta",
                        content : " Seguro desea eliminar el registro ?",
                        buttons : '[No][Si]'
                    }, function(ButtonPressed) {
                        if (ButtonPressed == "Si") {
                            $.ajax({
                                type: "POST",
                                dataType: "json",
                                data:"controller="+_controller+"&cCase=deleteRegistro&dTabla=general&sValue="+_id+"&cTable="+_table,
                                url: "modules/" +_controller+ "/controller/" +_controller+ 'JsonController.php', 
                                success : function(data) {
                                    if(data.row==1){
                                        _tr.remove();
                                        alert_data('Mensaje App','Hecho','El registro ha sido eliminado.','#296191');
                                    }else{
                                        alert_data('Mensaje App','Hecho','El registro no ha podido ser eliminado, consulte al administrador. ','#C46A69');
                                    }
                                }
                            });
                        }
                    });
                    return false;
                }
            break;
            case ('DESACTIVAR'):
                var _sel = false;
                $('input[name="id_dato"]').each(function(){if($(this).is(':checked')){_sel=true;}});
                if(!_sel){
                    alert_data('Mensaje App','error','Debe seleccionar un registro','#C46A69');return false;
                }else{
                    var _tr    = $('input[name=id_dato]:checked').parent().parent();
                    var _est   = _tr.children("td#Estado").html();
                    var _clase; 
                    _est=='AAA' ? _est='BBB' : _est = 'AAA';
                    _est=='BBB' ? _clase='danger' : _clase = 'danger';
                    var _table = $(this).attr("data-table");
                    var _id    = $(this).attr("data-id");
                    var _controller = $.base64.decode($(this).attr("href").replace("?app=",""));
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        data:"controller="+_controller+"&cCase=inactiveRegistro&dTabla=general&sValue="+_id+"&cTable="+_table+"&sEst="+_est,
                        url: "modules/" +_controller+ "/controller/" +_controller+ 'JsonController.php', 
                        success : function(data) {
                            if(data.row==1){
                                _tr.children("td#Estado").html(_est);
                                _tr.toggleClass(_clase);
                                alert_data('Mensaje App','Hecho','El estado del registo se edito correctamente.','#296191');
                            }else{
                                alert_data('Mensaje App','Hecho','El registro no ha podido ser editado, consulte al administrador. ','#C46A69');
                            }
                        }
                     });
                    return false;
                }
            break;
            case 'ACTUALIZARCOMBO':
                var _i = $(this).children('i'); 
                var _parent = $(this).parent().parent('div');
                var _select = _parent.children('select')
                var _controller = $.base64.decode(obtenerVariables('app', window.location.href));
                var _name=_select.attr('name');
                _select.attr({'disabled':true});
                _i.addClass('fa-spin');
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data:"controller="+_controller+"&cCase=ActualizarCombo&dTabla=general&sValue="+data,
                    url: "modules/" +_controller+ "/controller/" +_controller+ 'JsonController.php', 
                    success : function(data) {
                        if(data!=''){
                            _select.empty();
                            _select.append("<option> </option>");
                            $.each(data, function(k,v){
                                _select.append("<option data-selected=\""+_name+"\" value="+v.col0+" data="+v.col3+">"+v.col1+ '-' +v.col2+"</option>");
                            });

                            setTimeout( function (){
                                _select.attr({'disabled':false});
                                _i.removeClass('fa-spin');
                                 alert_data('Mensaje App','Hecho','Los datos han sido Actualizados.','#296191');
                            },1000);
                        }else{
                            setTimeout( function (){
                                _select.attr({'disabled':false});
                                _i.removeClass('fa-spin');
                                 alert_data('Mensaje App','Error','Los datos no han podido ser Actualizados.','#C46A69');
                            },1000);
                        }
                    }
                });
            break;
            case('NUEVOREGISTRO'):
                var _url = $(this).attr('data-url');
                window.open(_url,'_blank');
            break;
            case('DIENTE'):
                var _id;
                var _divDiente;
                var _controller = $.base64.decode(obtenerVariables('app', window.location.href));
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data:"controller="+_controller+"&cCase=consultaOdontograma&dTabla=endodoncia_config_dental&sValue="+data,
                    url: "modules/" +_controller+ "/controller/" +_controller+ 'JsonController.php', 
                    success : function(data) {
                        if(data.row.result==1){
                            alert_data('Mensaje App','Error','Ya existe una configuracion para este diente, revise la informacion o contacte al administrador','#C46A69');
                        }else{
                            var _eClonar = $("div.clonar:last");
                            _eClonar = _eClonar.children('div');
                            _eClonar = _eClonar.children('div');
                            _id = _eClonar.children('#cod_imagen_dental').val();
                            if(_id!=''){
                                _divDiente = $('div[data="'+_id+'"]');
                                _divDiente.toggleClass('active1');
                            }
                            _eClonar = _eClonar.children('#cod_imagen_dental').val(_data);
                            _botton.toggleClass('active1');
                        }
                    }
                });
            break;
            case 'DIENTE_HISTORIA':
                $('input[name="ind_retratamiento"]').prop("checked",false);
                var _id, _panel, _desobturacion;
                var _divDiente;
                var _controller = $.base64.decode(obtenerVariables('app', window.location.href));
                var _value = $('#cod_paciente').val();
                if(_value!=''){
                    $('div[data-rel="DIENTE_HISTORIA"]').each(function(){
                        $(this).removeClass('active1');
                    });
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        data:"controller="+_controller+"&cCase=consultaDienteHistoria&dTabla=endodoncia_config_dental&sValue="+data+'&cod_paciente='+_value,
                        url: "modules/" +_controller+ "/controller/" +_controller+ 'JsonController.php', 
                        success : function(data) {
                            if(data.row.cod_config_dental==0){
                                alert_data('Mensaje App','Error','El diente seleccionado no ha sido parametrizado, revise la informacion o contacte al administrador','#C46A69');
                            }else{
                                $('#no_cod_config_dental').select2('destroy');
                                $("#no_cod_config_dental option").each(function(){
                                    if($(this).attr('value')==data.row.cod_config_dental){
                                        $(this).prop('selected', true);
                                    }
                                });
                                $('#cod_config_dental').val(data.row.cod_config_dental);
                                $('#no_cod_config_dental').select2();
                                _botton.toggleClass('active1');
                                _panel = $('#endodoncia_conductos');
                                _panel.html('');
                                _panel.append(data.panel.panel);
                                _desobturacion = $('#endodoncia_desobturacion'); 
                                _desobturacion.html('');
                                _desobturacion.append(data.desobturacion.desobturacion);
                                if(data.retratamiento.retratamiento>0){
                                    $.SmartMessageBox({
                                        title : "<i class='fa fa-exclamation-triangle' style='color:yallow'></i> Mensaje de Informacion",
                                        content : " Este diente ya ha sido tratado anteriormente, desea marcarlo como retratamiento por fracaso en el procedimiento ?",
                                        buttons : '[No][Si]'
                                    }, function(ButtonPressed) {
                                        if (ButtonPressed == "Si") {
                                            $('input[name="ind_retratamiento"]').prop("checked",true);
                                            $('#no_ult_evoluciones').attr({'disabled':false});
                                            $('#no_ult_evoluciones').val(data.evoluciones_diente.evoluciones_diente);
                                            var _imagenes = $('#endodoncia_registro_fotografico');
                                            _imagenes.html('');
                                            _imagenes.append(data.imagenes_diente.imagenes_diente);
                                            _imagenes.show('slow');
                                            $('#superbox').SuperBox();
                                        }
                                    });
                                }
                            }
                        }
                    });
                }else{
                    alert_data('Mensaje App','Informacion','Debe seleccionar primero al paciente','#C46A69');
                    $('#cod_paciente').focus();
                }
            break;
            case 'INFOIMG':
                $('.deleteImg').attr({'data': data});
            break;
            case 'ELIMINARIMG':
                var _controller = $.base64.decode(obtenerVariables('app', window.location.href));
                $.SmartMessageBox({
                    title : "<i class='fa fa-exclamation-triangle' style='color:red'></i> Mensaje de Alerta",
                    content : " Seguro desea ejeuctar esta accion ?",
                    buttons : '[No][Si]'
                }, function(ButtonPressed) {
                    if (ButtonPressed == "Si") {
                       $.ajax({
                            type: "POST",
                            dataType: "json",
                            data:"controller="+_controller+"&cCase=eliminarImg&dTabla=endodoncia_registro_imagenes&sValue="+data,
                            url: "modules/" +_controller+ "/controller/" +_controller+ 'JsonController.php', 
                            success : function(data) {
                                if(data.row==1){
                                    $('div[data-rel="INFOIMG"]').each( function(){
                                        if($(this).hasClass('active')){
                                            $(this).remove();
                                            $('.superbox-show').remove();
                                        }
                                    });   
                                }
                            }
                        });
                    }
                });
            break;
            case 'CLONARINPUT':
                var _parent, _div, _clonar, _input, _clone;var iCount=0;
                _parent = _botton.parent().parent().parent().parent('div');
                _input  = _parent.children().children().children('input').attr('name');
                _parent.after(_parent.clone(true).fadeIn('slow'));
                _div    = _botton.parent().parent().parent().parent('div:last');
                _div    = _div.children().children().children().children();
                _div.removeClass('btn-error').addClass('btn-danger').attr({'data-rel':'ELIMINARINPUT'});
                _div.children('i').removeClass('fa-plus').addClass('fa-minus');
                $('input[name="'+_input+'"]').each(function(){
                    iCount++;
                    $(this).attr({'name':_input.replace("[]","")+'[]'});
                });
                var altura = $(document).height();
                $("html, body").animate({scrollTop:altura+"px"});
            break;
            case 'ELIMINARINPUT':
                var _parent, _div, _clonar, _input;var iCount=0;
                $.SmartMessageBox({
                    title : "<i class='fa fa-exclamation-triangle' style='color:red'></i> Mensaje de Alerta",
                    content : " Seguro desea ejeuctar esta accion ?",
                    buttons : '[No][Si]'
                }, function(ButtonPressed) {
                    if (ButtonPressed == "Si") {
                        _parent = _botton.parent().parent().parent().parent('div').remove();
                        _input  = _parent.children().children().children('input').attr('name');
                        $('input[name="'+_input+'"]').each(function(){iCount++;});
                        if(iCount==1){ $('input[name="'+_input+'"]').attr({"name":_input.replace("[]","")}) }
                        var altura = $(document).height();
                        $("html, body").animate({scrollTop:altura+"px"});
                    }
                });
            break;
            case 'AGREGARHUELLA':
                alert_data('Mensaje App','Notificacion','Porfavor indique al su paciente colocar el dedo indice derecho en el finger.','#296191');
            break;
            case 'VALIDAODONTOLOGO':
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data:$('#form').serialize()+"&controller="+_controller+"&cCase=validarOdontologo&dTabla=endodoncia_odontologo",
                    url: "modules/endodoncia/controller/endodonciaJsonController.php", 
                    success : function(data) {
                        console.log(data.pacientes);
                        $("#cod_historia_clinica").empty();
                        $("#cod_historia_clinica").append("<option value=></option>");
                        $.each(data.pacientes, function(k,v){
                            $("#cod_historia_clinica").append("<option value=\""+v.cod_historia_clinica+"\">"+v.value+"</option>");
                        });
                        $("#cod_historia_clinica").attr({'disabled':false});
                    }
                });
            break;
            case 'tabs':
                switch (data){
                    case 'tab-notify':
                        $('#task').hide();
                        $('#notify').fadeIn(2000);
                    break;
                    case 'tab-task':
                        $('#notify').hide();
                        $('#task').fadeIn(2000);
                    break;
                }
            break;

        }
    }); 
    
    $("#cantidad,.spinerInput").on('blur', function(){
        var _form = $("input[name='no_nom_tabla']").val();
        var controller = $.base64.decode($("#form").attr('data'));
        var cCase      = 'num_inventario';
        var dTabla     = 'fa_factura';
        if(_form=='nuevaFactura' || _form=='nuevaFacturacion'){
            //variables para calcular los totales de la factura
            var cantidad,importe,descuento,impuesto = 0,item;
            var pDivChild   = $(this).parent().parent();
            var pDivChild1  = $(this).parent().parent().parent('div');
            var pDiv        = $(this).parent().parent().parent().parent('div');
            cantidad        = $(this).val();
            item            = pDiv.children().children().children().children("#no_cod_item").find('option:selected').attr('value');
            importe         = parseFloat(pDiv.children().children().children("input#no_imp").val());
            descuento       = pDiv.children().children().children().children("#no_cod_descuento").find('option:selected').attr('data');
            impuesto        = pDiv.children().children().children().children("#no_cod_impuesto").find('option:selected').attr('data');
            $.ajax({
                type: "POST",
                dataType: "json",
                data:"controller="+controller+"&cCase="+cCase+"&dTabla="+dTabla+"&sValue="+item,
                url: "modules/" + controller + "/controller/" + controller + 'JsonController.php', 
                success : function(data) {
                    if(cantidad>parseInt(data.numExistencias)){
                        alert_data('Mensaje App','error','La cantidad de items, sobrepasa a las existencias en inventario, revise el inventario para este item. ','#C46A69');
                    }else{
                        if(descuento===undefined){
                        descuento=0;
                        }
                        if(impuesto===undefined){
                            impuesto=0;
                        }
                        var subTotal    = parseFloat(importe*cantidad);
                        
                        var subTotalDes = (subTotal * descuento)/100;
                        var subTotalImp = (subTotal * impuesto)/100;
                        var total = (subTotal - subTotalDes)+subTotalImp;
                        var total1 = total;
                        pDivChild1.children('div#subTotal').remove();
                        total1 = formato_numero(total1,2,',','.');
                        //creo la capa inmediatamente despues del input de cantidad para colocal el total
                        pDivChild.after('<div class="subTotal col-sm-2" id="subTotal" data-subtotal='+subTotal+' data-destotal='+subTotalDes+' data-imptotal='+subTotalImp+' data-total='+total+'><strong>$ '+ total1 +'</strong></div>');
                        //Borro las capas con los resultados para reiniciarlos
                        $('#SubTotal').remove();
                        $('#SubTotal1').remove();
                        $('#SubTotal2').remove();
                        $('#SubTotal3').remove();
                        //localizo la capa padre para colocar las capas con los totales
                        var pTotDiv = $('div.clonar').parent().parent().parent();
                        //recorro las capas de subtotal por cada item y sumo los tatales para mostrarlos en la capa de Total
                        var bTotal = 0;
                        $('.subTotal').each( function(){
                            bTotal =  bTotal + parseFloat($(this).attr('data-total'));
                        });
                        //lleno los input que se envian al insert en el modelo
                        var subTotal = 0;
                        $('.subTotal').each( function(){
                            subTotal =  subTotal + parseFloat($(this).attr('data-subtotal'));
                        });
                        var subTotalDes = 0;
                        $('.subTotal').each( function(){
                            subTotalDes =  subTotalDes + parseFloat($(this).attr('data-destotal'));
                        });
                        var subTotalImp = 0;
                        $('.subTotal').each( function(){
                            subTotalImp =  subTotalImp + parseFloat($(this).attr('data-imptotal'));
                        });
                        $('input[name="sub_total_factura"]').val(subTotal.toFixed(2));
                        $('input[name="sub_totaldes_factura"]').val(subTotalDes.toFixed(2));
                        $('input[name="imp_factura"]').val(subTotalImp.toFixed(2));
                        $('input[name="imp_adeudado"]').val(bTotal.toFixed(2));
                        //Formateo los resultados para mostrarlos al usuario
                        bTotal = formato_numero(bTotal,2,',','.');
                        subTotal = formato_numero(subTotal,2,',','.');
                        subTotalDes = formato_numero(subTotalDes,2,',','.');
                        subTotalImp = formato_numero(subTotalImp,2,',','.');
                        //creo las capas para los totales y los imprimo en pantalla
                        pTotDiv.after("<div class='row' id='SubTotal1'><div class='col-sm-8'></div><div class='col-sm-4'><div class='well well-sm  bg-color-white txt-color-black no-border'><div class='fa-lg'>Sub Total :<span class='ptotal pull-right'>$ "+subTotal+"</span></div></div></div></div><div class='row' id='SubTotal2'><div class='col-sm-8'></div><div class='col-sm-4'><div class='well well-sm  bg-color-white txt-color-black no-border'><div class='fa-lg'>Descuento :<span class='ptotal pull-right'>$ "+subTotalDes+"</span></div></div></div></div><div class='row' id='SubTotal3'><div class='col-sm-8'></div><div class='col-sm-4'><div class='well well-sm  bg-color-white txt-color-black no-border'><div class='fa-lg'>Impuesto :<span class='ptotal pull-right'>$ "+subTotalImp+"</span></div></div></div></div><div class='row' id='SubTotal'><div class='col-sm-8'></div><div class='col-sm-4'><div class='well well-sm  bg-color-darken txt-color-white no-border'><div class='fa-lg'>Total :<span class='ptotal pull-right'>$ "+bTotal+"</span></div></div></div></div>");
                    }
                }
            });
        }
    });
       
    cargaEventoFormulario($("input[name='no_esq_tabla']").val(),$("input[name='no_nom_tabla']").val()); 

    $("select").change(function(){
        var _select    = $(this);
        $(this).select2('destroy');
        var _width     = $(this).css('width');
        var controller = $.base64.decode($("#form").attr('data'));
        var cCase      = $(this).attr('name');
        var dTabla     = $("input[name='no_esq_tabla']").val();
        var cTabla     = $(this).attr('name');
        var sOpt       = $(this).find('option:selected');
        var sValue     = sOpt.val();
        var sText      = $.trim(sOpt.text());
        var sTablaAct  = sOpt.attr('data_table');
        var id         = $(this).parent().parent().parent("div").attr("data-indice");
        var pDiv       = $(this).parent().parent().parent().parent("div");
        var txtRef     = pDiv.children().children().children("input#no_ref_item");
        var txtImp     = pDiv.children().children().children("input#no_imp");
        var txtCan     = pDiv.children().children().children(".ui-spinner"); 
        var valData    = sOpt.attr('data');
        var auxData    =0;

        if ($('#cod_usuario').length){
            var auxOpt = $("#cod_usuario").find('option:selected');
            auxData    = auxOpt.val();
        }
        var valorInput;
        console.log(dTabla + cCase+ dTabla + cTabla + sValue + auxData + ' - ' + sText);
        $.ajax({
            type: "POST",
            dataType: "json",
            data:"controller="+controller+"&cCase="+cCase+"&dTabla="+dTabla+"&cTabla="+cTabla+"&sValue="+sValue+"&sAux="+auxData,
            url: "modules/" + controller + "/controller/" + controller + 'JsonController.php', 
            success : function(data) {
                console.log(data);
                switch(dTabla){
                    case('fa_factura'):case('fa_facturacion'):
                        switch(cCase){
                            case ('cod_cliente'):
                                $("#fec_alta_factura").val(data.fecActual);
                                $("#fec_vencimiento_factura").val(data.fechaVencimiento);
                                $("#pla_factura").select2("destroy");
                                $("#cod_tipopago option[value=" + data.cod_tipopago + "]").attr("selected",true);
                                $("#cod_tipopago").select2();
                            break;
                            case('no_cod_item[]'):
                                txtRef.val(data.ref_item);
                                txtImp.val(data.imp_venta);
                                txtCan.children("input#cantidad").val(1);
                                txtCan.children("input#cantidad").focus();
                                txtCan.children("input#cantidad").trigger('blur');
                            break;
                            case('cod_tipopago'):
                                $("#fec_alta_factura").val(data.fecActual);
                                $("#fec_vencimiento_factura").val(data.fechaVencimiento);
                            break; 
                            case('no_cod_impuesto[]'):
                                txtCan.children("input#cantidad").val(1);
                                txtCan.children("input#cantidad").focus();
                                txtCan.children("input#cantidad").trigger('blur');
                            break;
                            case('no_cod_descuento[]'):
                                txtCan.children("input#cantidad").val(1);
                                txtCan.children("input#cantidad").focus();
                                txtCan.children("input#cantidad").trigger('blur');
                            break;
                            case ('cod_empresa'):
                                $("input[name='cod_numeracion']").val(data.cod_numeracion);
                                $("#num_factura").val(data.numeracion);
                                $('input[name="ind_cotizacion"]').prop("checked",true);
                            break;
                        }
                    break;
                    case('fa_pago'):  
                        switch(cCase){
                            case('no_cod_factura[]'):
                                $("input#no_nom_cliente").val(data.nom_cliente);
                                $("input[name='cod_cliente']").val(data.cod_cliente);
                                pDiv.children().children().children("input#no_imp_factura").val(data.imp_factura);
                                pDiv.children().children().children("input#no_pago_factura").val(data.imp_cancelado);
                                pDiv.children().children().children("input#no_adeudado_factura").val(data.imp_adeudado);
                                pDiv.children().children().children("input#no_impuesto").val(data.no_impuesto);
                            break;
                            case('cod_cliente'):
                                var _marcado = $('input[name="no_ind_factura"]').prop("checked") ? true: false;
                                var _name    = $('input[name="no_ind_factura"]').attr("name");
                                if(_marcado){$('input[name="no_ind_factura"]').prop("checked", false);$("#fa_asociada").fadeOut("slow");}
                                $("#no_cod_factura").empty();
                                $("#no_cod_factura").append("<option value=></option>");
                                $.each(data, function(k,v){
                                    $("#no_cod_factura").append("<option value=\""+v.cod_factura+"\">"+v.value+"</option>");
                                });
                            break;
                            case('cod_cliente_1'):
                                $("#cod_cliente_asociado").empty();
                                $("#cod_cliente_asociado").append("<option value=></option>");
                                $.each(data, function(k,v){
                                    $("#cod_cliente_asociado").append("<option value=\""+v.cod_cliente_asociado+"\">"+v.nom_cliente_asociado+" - "+v.email_cliente_asociado+"</option>");
                                });
                            break;
                            case ('cod_empresa'):
                                $('#num_sig_comp_ingreso').val(data.numComprobanteIngreso);
                                $('#num_sig_comp_egreso').val(data.numComprobanteEgreso);
                            break;
                        }
                    break;
                    case('fa_inventario'):
                        switch(cCase){
                            case('no_cod_impuesto'):
                                var _input = pDiv.children().children().children("input");
                                var _tInv  = parseFloat($("#no_total_inventariado").val()); 
                                isNaN(_tInv) ? _tInv=0 : _tInv=_tInv;
                                var _calculo = (_tInv*valData)/100;
                                _input.val(_calculo);
                                var _result = parseFloat($("#total_inventario").val()) + _calculo;
                                $("#total_inventario").val(_result);
                            break;
                            case('no_cod_retencion[]'):
                                var _input = pDiv.children().children().children("input");
                                var _tInv  = parseFloat($("#no_total_inventariado").val());
                                isNaN(_tInv) ? _tInv=0 : _tInv=_tInv;
                                var _calculo = (_tInv*valData)/100;
                                _input.val(_calculo);
                                 var _result = parseFloat($("#total_inventario").val()) - _calculo;
                                $("#total_inventario").val(_result);
                            break;
                        }
                    break;
                    case('fa_inventario_aud'):
                        switch(cCase){
                            case('no_cod_impuesto'):
                                var _input = pDiv.children().children().children("input");
                                var _tInv  = parseFloat($("#no_total_inventariado").val()); 
                                isNaN(_tInv) ? _tInv=0 : _tInv=_tInv;
                                var _calculo = (_tInv*valData)/100;
                                _input.val(_calculo);
                                var _result = parseFloat($("#no_total_inventario").val()) + _calculo;
                                $("#no_total_inventario").val(_result);
                            break;
                            case('no_cod_retencion[]'):
                                var _input = pDiv.children().children().children("input");
                                var _tInv  = parseFloat($("#no_total_inventariado").val());
                                isNaN(_tInv) ? _tInv=0 : _tInv=_tInv;
                                var _calculo = (_tInv*valData)/100;
                                _input.val(_calculo);
                                 var _result = parseFloat($("#no_total_inventario").val()) - _calculo;
                                $("#no_total_inventario").val(_result);
                            break;
                        }
                    break;
                    case('fa_inventario_ajuste'):
                        switch(cCase){
                            case('no_cod_impuesto'):
                                var _input = pDiv.children().children().children("input");
                                var _tInv  = parseFloat($("#no_total_inventariado").val()); 
                                isNaN(_tInv) ? _tInv=0 : _tInv=_tInv;
                                var _calculo = (_tInv*valData)/100;
                                _input.val(_calculo);
                                var _result = parseFloat($("#no_total_inventario").val()) + _calculo;
                                $("#no_total_inventario").val(_result);
                            break;
                            case('no_cod_retencion[]'):
                                var _input = pDiv.children().children().children("input");
                                var _tInv  = parseFloat($("#no_total_inventariado").val());
                                isNaN(_tInv) ? _tInv=0 : _tInv=_tInv;
                                var _calculo = (_tInv*valData)/100;
                                _input.val(_calculo);
                                 var _result = parseFloat($("#no_total_inventario").val()) - _calculo;
                                $("#no_total_inventario").val(_result);
                            break;
                        }
                    break;
                    case('fa_pago'):
                        switch(cCase){
                            case('no_cod_retencion[]'):
                                var _input = $("no_imp_detpago_cat");
                                if(_input!=''){
                                    _input.focus();
                                    _input.trigger('blur');
                                }
                            break;
                        }
                    break;
                    case ('sined_notas'):
                        switch(cCase){
                            case('cod_carga_academica'):
                                $("#cod_grupo").empty();
                                $("#cod_grupo").append("<option value=></option>");
                                $.each(data, function(k,v){
                                    $("#cod_grupo").append("<option value=\""+v.cod_grupo+"\">"+v.value+"</option>");
                                });
                                $("#cod_grupo").attr({'disabled':false});
                            break;
                            case ('cod_usuario'):
                                $("#cod_carga_academica").empty();
                                $("#cod_carga_academica").append("<option value=></option>");
                                $.each(data, function(k,v){
                                    $("#cod_carga_academica").append("<option value=\""+v.cod_materia+"\">"+v.value+"</option>");
                                });
                                $("#cod_carga_academica").attr({'disabled':false});
                            break;
                        }
                    break;
                    case ('sined_valoracion_descriptiva'):
                        switch(cCase){
                            case('cod_usuario'):
                                $("#cod_grupo").empty();
                                $("#cod_grupo").append("<option value=></option>");
                                $.each(data, function(k,v){
                                    $("#cod_grupo").append("<option value=\""+v.cod_grupo+"\">"+v.value+"</option>");
                                });
                                $("#cod_grupo").attr({'disabled':false});
                            break;
                        }
                    break;
                    case ('sined_boletines'):
                        switch(cCase){
                             case ('cod_grupo'):
                                $("#cod_periodo_academico").empty();
                                $("#cod_periodo_academico").append("<option value=></option>");
                                $.each(data, function(k,v){
                                    $("#cod_periodo_academico").append("<option value=\""+v.cod_periodo_academico+"\">"+v.value+"</option>");
                                });
                                $("#cod_periodo_academico").attr({'disabled':false});
                            break;
                        }
                    break;
                    case ('fa_cliente'):
                        switch(cCase){
                            case ('no_cod_productos'): case ('no_cod_productos[]'):
                                $("#no_cod_subproductos").empty();
                                $("#no_cod_subproductos").append("<option value=></option>");
                                $("#no_cod_medios").empty();
                                $("#no_cod_medios").append("<option value=></option>");
                                $.each(data.Productos, function(k,v){
                                    $("#no_cod_subproductos").append("<option value=\""+v.cod_subproductos+"\">"+v.value+"</option>");
                                });
                                $.each(data.Medios, function(k,v){
                                    $("#no_cod_medios").append("<option value=\""+v.cod_medios+"\">"+v.value+"</option>");
                                });
                                $("#no_cod_medios").attr({'disabled':false});
                                $("#no_cod_subproductos").attr({'disabled':false});
                            break;
                        }
                    break;
                    case ('crm_contacto'):
                        switch(cCase){
                            case ('no_cod_productos'):
                                $("#cod_subproductos").empty();
                                $("#cod_subproductos").append("<option value=></option>");
                                $("#cod_medios").empty();
                                $("#cod_medios").append("<option value=></option>");
                                $.each(data.Productos, function(k,v){
                                    $("#cod_subproductos").append("<option value=\""+v.cod_subproductos+"\">"+v.value+"</option>");
                                });
                                $.each(data.Medios, function(k,v){
                                    $("#cod_medios").append("<option value=\""+v.cod_medios+"\">"+v.value+"</option>");
                                });
                                $("#cod_medios").attr({'disabled':false});
                                $("#cod_subproductos").attr({'disabled':false});
                            break;
                        }
                    break;
                    case ('sined_alumno'):
                        switch(cCase){
                            case ('no_cod_grupo'):case 'cod_grupo':
                                $.each(data, function(k,v){
                                    if(v.value>0){
                                        alert_data('Disponibilidad de Cupo','Disponibilidad de Cupo','El grupo seleccionado cuenta con '+v.value+' cupos disponibles segun su configuracion.','#296191');
                                    }else{
                                        alert_data('Disponibilidad de Cupo','Disponibilidad de Cupo','El grupo seleccionado no cuenta con cupos disponibles segun su configuracion.','#C46A69');
                                    }
                                });
                            break;
                        }
                    break;
                    case 'sined_ficha_academica':
                        switch(cCase){
                            case ('cod_grupo'):
                                $("#cod_alumno").empty();
                                $("#cod_alumno").append("<option value=></option>");
                                $.each(data, function(k,v){
                                    $("#cod_alumno").append("<option value=\""+v.cod_alumno+"\">"+v.value+"</option>");
                                });
                                $("#cod_alumno").attr({'disabled':false});
                            break;
                        }
                    break;
                    case 'endodoncia_paciente_evolucion':
                        switch(cCase){
                            case 'cod_historia_clinica':
                                $.ajax({
                                    type: "POST",
                                    dataType: "json",
                                    data:"controller=endodoncia&cCase=consultaDienteHistoriaDatos&dTabla=endodoncia_config_dental&sValue="+data.cod_imagen_dental+"&cod_paciente="+$('#cod_paciente').val()+"&cod_historia_clinica="+sValue,
                                    url: "modules/endodoncia/controller/endodonciaJsonController.php", 
                                    success : function(data) {
                                        if(data.row.cod_config_dental==0){
                                            alert_data('Mensaje App','Error','El diente seleccionado no ha sido parametrizado, revise la informacion o contacte al administrador','#C46A69');
                                        }else{
                                            _panel = $('#endodoncia_conductos');
                                            _panel.html('');
                                            _panel.append(data.panel.panel);
                                            _desobturacion = $('#endodoncia_desobturacion'); 
                                            _desobturacion.html('');
                                            _desobturacion.append(data.desobturacion.desobturacion);
                                            _imagenes = $('#endodoncia_registro_fotografico');
                                            _imagenes.html('');
                                            _imagenes.append(data.imagenes.imagenes);
                                            _imagenes.show('slow');
                                            $('#superbox').SuperBox();
                                            if($('input[name="ind_desobturacion"]').val()==1){$('#endodoncia_desobturacion').show('slow');}
                                            $('#no_ult_evoluciones').attr({'disabled':false});
                                            $('#no_ult_evoluciones').val(data.evoluciones.evoluciones);
                                        }
                                    }
                                });
                            break;
                        }
                    break;
                    case ('endodoncia_historia_clinica'):
                        switch(cCase){
                             case ('no_cod_dia_1'):
                                $("#no_cod_dia").empty();
                                $("#no_cod_dia").append("<option value=></option>");
                                $.each(data, function(k,v){
                                    $("#no_cod_dia").append("<option value=\""+v.cod_config_diagnosticos+"\">"+v.value+"</option>");
                                });
                                $("#no_cod_dia").attr({'disabled':false});
                            break;
                            case 'no_cod_tej_bla[]':
                                var _input  = pDiv.children().children().children().children("input#otro_tejidos_blandos");
                                var _divCon = _input.parent().parent().parent();var r = false;
                                if(sText.indexOf('Otro')>=0){_divCon.fadeIn();_input.attr({'type':'text'}).focus();}else{$("#no_cod_tej_bla option").each(function(){var f = $(this).prop('selected')?true:false;var t = $.trim($(this).text());if(f && t=='Otro'){r=true;};});
                                if(!r){_input.attr({'type':'hidden'});_input.val('');_divCon.fadeOut();};}
                            break;
                            case 'no_cod_tej_den[]':
                                var _input  = pDiv.children().children().children().children("input#otro_tejidos_dentales");
                                var _divCon = _input.parent().parent().parent();
                                if(sText.indexOf('Otro')>=0){_divCon.fadeIn();_input.attr({'type':'text'}).focus();}else{$("#otro_tejidos_dentales option").each(function(){var f = $(this).prop('selected')?true:false;var t = $.trim($(this).text());if(f && t=='Otro'){r=true;};});
                                if(!r){_input.attr({'type':'hidden'});_input.val('');_divCon.fadeOut();};}
                            break;
                            case 'no_cod_tej_per[]':
                                var _input  = pDiv.children().children().children().children("input#otro_tejidos_periodontales");
                                var _divCon = _input.parent().parent().parent();
                                if(sText.indexOf('Otro')>=0){_divCon.fadeIn();_input.attr({'type':'text'}).focus();}else{$("#otro_tejidos_periodontales option").each(function(){var f = $(this).prop('selected')?true:false;var t = $.trim($(this).text());if(f && t=='Otro'){r=true;};});
                                if(!r){_input.attr({'type':'hidden'});_input.val('');_divCon.fadeOut();};}
                            break;
                            case 'no_cod_tej_peri[]':
                                var _input  = pDiv.children().children().children().children("input#otro_tejidos_perirradiculares");
                                var _divCon = _input.parent().parent().parent();
                                if(sText.indexOf('Otro')>=0){_divCon.fadeIn();_input.attr({'type':'text'}).focus();}else{$("#otro_tejidos_perirradiculares option").each(function(){var f = $(this).prop('selected')?true:false;var t = $.trim($(this).text());if(f && t=='Otro'){r=true;};});
                                if(!r){_input.attr({'type':'hidden'});_input.val('');_divCon.fadeOut();};}
                            break;
                            case 'no_cod_tej_pul[]':
                                var _input  = pDiv.children().children().children().children("input#otro_tejidos_pulpares");
                                var _divCon = _input.parent().parent().parent();
                                if(sText.indexOf('Otro')>=0){_divCon.fadeIn();_input.attr({'type':'text'}).focus();}else{$("#otro_tejidos_pulpares option").each(function(){var f = $(this).prop('selected')?true:false;var t = $.trim($(this).text());if(f && t=='Otro'){r=true;};});
                                if(!r){_input.attr({'type':'hidden'});_input.val('');_divCon.fadeOut();};}
                            break;
                        }
                    break;
                    case 'endodoncia_pago':
                        switch(cCase){
                            case ('cod_empresa'):
                                $('#num_sig_comp_ingreso').val(data.numComprobanteIngreso);
                                $('#num_sig_comp_egreso').val(data.numComprobanteEgreso);
                            break;
                            case('no_cod_paciente'):
                                $("#no_cod_historia_clinica").empty();
                                $("#no_cod_historia_clinica").append("<option value=></option>");
                                $.each(data.his, function(k,v){
                                    $("#no_cod_historia_clinica").append("<option value=\""+v.cod_historia_clinica+"\">"+v.value+"</option>");
                                });
                                $('#no_valor_total').css({'font-size':'12pt','color':'red'});
                                $('#no_valor_total').val(formato_numero(data.deuda.value,2,',','.'));
                                $("#no_cod_historia_clinica").attr({'disabled':false});
                            break;
                            case('no_cod_historia_clinica[]'):
                                pDiv.children().children().children("input#no_imp_total_historia_clinica").val(data.imp_total_historia_clinica);
                                pDiv.children().children().children("input#no_imp_total_historia_clinica").css({'font-size':'12pt','color':'red'});
                                pDiv.children().children().children("input#no_imp_adeu_historia_clinica").val(data.imp_adeu_historia_clinica);
                                pDiv.children().children().children("input#no_imp_adeu_historia_clinica").css({'font-size':'12pt','color':'green'});
                                pDiv.children().children().children("input#no_imp_canc_historia_clinica").val(data.imp_canc_historia_clinica);
                                pDiv.children().children().children("input#no_imp_canc_historia_clinica").css({'font-size':'12pt','color':'#8A4B08'});
                                pDiv.children().children().children("input#imp_pago").css({'font-size':'12pt'});
                                $('#imp_pago').focus();
                                $('#no_historial_pago').val(data.Historial.Historial);
                            break;
                        }
                    break;
                    case 'endodoncia_odontologo':
                        switch(cCase){
                            case 'cod_historia_clinica':
                                var _url = '?app=ZW5kb2RvbmNpYQ==&met=R2VuZXJhclJlbWlzaW9uQ2xpbmljYVBERg==&arg='+$.base64.encode('-'+sValue+',endodoncia_historia_clinica,'+0);
                                window.open(_url,'_blank');
                            break;
                        }
                    break;
                    case 'sined_certificados':
                        switch(cCase){
                            case 'cod_grupo':
                                $("#cod_alumno").empty();
                                $("#cod_alumno").append("<option value=> </option>");
                                $("#cod_periodo_academico").empty();
                                $("#cod_periodo_academico").append("<option value=> </option>");
                                $.each(data.Alumnos, function(k,v){
                                    $("#cod_alumno").append("<option value=\""+v.cod_alumno+"\">"+v.value+"</option>");
                                });
                                $.each(data.Periodo, function(k,v){
                                    $("#cod_periodo_academico").append("<option value=\""+v.cod_periodo_academico+"\">"+v.value+"</option>");
                                });
                                $("#cod_alumno").attr({'disabled':false});
                                $("#cod_periodo_academico").attr({'disabled':false});
                            break;
                        }
                    break;
                }  
            }
        });
        if(!$(this).hasClass('form-control')){
            $(this).select2({ width: _width });
        }
    });
    
    $('#no_imp,input[name="no_imp[]"]').on('blur', function(){
        //alert ($(this).attr('name'));
        var pDiv       = $(this).parent().parent().parent("div");
        var txtCan     = pDiv.children().children().children(".ui-spinner");
        txtCan.children("input#cantidad").val(1);
        txtCan.children("input#cantidad").focus();
        txtCan.children("input#cantidad").trigger('blur');
    });
    
    $('#no_imp_detpago_fac, #no_imp_detpago_cat').on('blur', function(){
        //Variables para calcular el total de el pago
        var valorRecibido,impAdeudado,retencion=0,impuesto=0,bTotal=0,vRet=0,vImp=0,subTotal=0,_divAd="",_marcado,_name,_aplicacion,_valorImpuesto=0;
        var pDivChild   = $(this).parent().parent();
        var pDivChild1  = $(this).parent().parent().parent('div');
        pDivChild1.children('div#subTotal').remove();
        $("input[type=checkbox]:checked").each(function(){
            _marcado =  true; 
            _name    = $(this).attr("name");
        });
        valorRecibido   = $(this).val();
        impAdeudado     = pDivChild.children().children().children("input#no_adeudado_factura").val();
        retencion       = pDivChild.children().children().children("#no_cod_retencion").find('option:selected').attr('data');
        _aplicacion     = pDivChild.children().children().children("#no_cod_retencion").find('option:selected').attr('data-aplicacion');
        _valorImpuesto  = pDivChild.children().children().children("input#no_impuesto").val();
        //console.log('retencion: ' + retencion  + ' Adeudado: ' +impAdeudado);
        _name=="no_ind_categoria" ? impuesto=pDivChild.children().children().children("#no_cod_impuesto").find('option:selected').attr('data') : impuesto=0;
        $('#SubTotal').remove();
        $('#SubTotal1').remove();
        $('#SubTotal2').remove();
        $('#SubTotal3').remove();
        $('#SubTotal4').remove();
        $("#no_sub_total").val();
            $("#no_retencion").val();
            $("#no_impuesto").val();
            $("#no_total").val();
        //localizo la capa padre para colocar las capas con los totales
        var pTotDiv = $('div.clonar:last').parent().parent().parent();
        if(parseFloat(valorRecibido)>parseFloat(impAdeudado)){
            alert_data('Mensaje App','error','El valor recibido es mayor al adedudado','#C46A69');
        }else{
            if(retencion===undefined){retencion=0;} 
            if(impuesto===undefined){impuesto=0;}
            _name=="no_ind_categoria" ? vImp= (valorRecibido * impuesto)/100 : vImp= parseFloat(_valorImpuesto); 
            subTotal= parseFloat(valorRecibido);
            switch(_aplicacion){
                case('1'):
                    vRet= (parseFloat(valorRecibido) * parseFloat(retencion))/100;
                break;
                case('2'):
                    vRet= (parseFloat(vImp) * parseFloat(retencion))/100;
                break;
                case('3'):
                    vRet = ((parseFloat(valorRecibido) - parseFloat(vImp)) * parseFloat(retencion))/100
                break;
            }
            if(_name=="no_ind_categoria"){
                vImp=parseFloat(vImp);
                bTotal = parseFloat(valorRecibido) - parseFloat(vRet) + parseFloat(vImp);
            }else{
                bTotal = parseFloat(subTotal);
                subTotal = parseFloat(valorRecibido) + parseFloat(vRet);
            }
            //Lleno los input con los valores para las operaciones contables
            $("#no_sub_total").val(subTotal.toFixed(2));
            $("#no_retencion").val(vRet.toFixed(2));
            $("#no_val_impuesto").val(vImp.toFixed(2));
            $("#no_total").val(bTotal.toFixed(2));
            //Formateo los resultados para mostrarlos al usuario
            bTotal = formato_numero(bTotal,2,',','.');
            subTotal = formato_numero(subTotal,2,',','.');
            vRet = formato_numero(vRet,2,',','.');
            vImp = formato_numero(vImp,2,',','.');
            _name=="no_ind_categoria" ? _divAd="<div class='row' id='SubTotal3'><div class='col-sm-8'></div><div class='col-sm-4'><div class='well well-sm  bg-color-white txt-color-black no-border'><div class='fa-lg'>Impuestos :<span class='ptotal pull-right'>$ "+vImp+"</span></div></div></div></div>" : _divAd="";
            pTotDiv.after(" <div class='row' id='SubTotal1'>\n\
                                <div class='col-sm-8'></div>\n\
                                <div class='col-sm-4'>\n\
                                    <div class='well well-sm  bg-color-white txt-color-black no-border'>\n\
                                        <div class='fa-lg'>Sub Total :\n\
                                            <span class='ptotal pull-right'>$ "+subTotal+"</span>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>"
                            +_divAd+"\
                            <div class='row' id='SubTotal2'>\n\
                                <div class='col-sm-8'></div>\n\
                                <div class='col-sm-4'>\n\
                                    <div class='well well-sm  bg-color-white txt-color-black no-border'>\n\
                                        <div class='fa-lg'>Retenciones :\n\
                                            <span class='ptotal pull-right'>$ - "+vRet+"</span>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                            <div class='row' id='SubTotal4'>\n\
                                <div class='col-sm-8'></div>\n\
                                <div class='row' id='SubTotal'>\n\
                                    <div class='col-sm-8'></div>\n\
                                        <div class='col-sm-4'>\n\
                                            <div class='well well-sm  bg-color-darken txt-color-white no-border'>\n\
                                                <div class='fa-lg'>Total :\n\
                                                    <span class='ptotal pull-right'>$ "+bTotal+"</span>\n\
                                                </div>\n\
                                            </div>\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>");
        }
    });
    
    $('#imp_pago').on('blur', function(){
        //Variables para calcular el total de el pago
        var valorRecibido,impAdeudado,retencion=0,impuesto=0,bTotal=0,vRet=0,vImp=0,subTotal=0,_divAd="",_marcado,_name,_aplicacion,_valorImpuesto=0;
        var pDivChild   = $(this).parent().parent();
        var pDivChild1  = $(this).parent().parent().parent('div');
        pDivChild1.children('div#subTotal').remove();
        valorRecibido   = $(this).val();
        impAdeudado     = pDivChild.children().children("input#no_imp_adeu_historia_clinica").val();
        $('#SubTotal').remove();
        $('#SubTotal1').remove();
        $('#SubTotal2').remove();
        $('#SubTotal3').remove();
        $('#SubTotal4').remove();
        $("#no_sub_total").val();
        $("#no_total").val();
        //localizo la capa padre para colocar las capas con los totales
        var pTotDiv = $('div.clonar:last').parent().parent().parent();
        if(parseFloat(valorRecibido)>parseFloat(impAdeudado)){
            alert_data('Mensaje App','error','El valor recibido es mayor al adedudado','#C46A69');
        }else{
            bTotal = parseFloat(valorRecibido);
            subTotal = parseFloat(valorRecibido);
            //Formateo los resultados para mostrarlos al usuario
            bTotal = formato_numero(bTotal,2,',','.');
            subTotal = formato_numero(subTotal,2,',','.');
            pTotDiv.after(" <div class='row' id='SubTotal1'> <div class='col-sm-8'></div> <div class='col-sm-4'> <div class='well well-sm  bg-color-white txt-color-black no-border'> <div class='fa-lg'>Sub Total : <span class='ptotal pull-right'>$ "+subTotal+"</span> </div> </div> </div> </div>  <div class='row' id='SubTotal4'> <div class='col-sm-8'></div> <div class='row' id='SubTotal'> <div class='col-sm-8'></div> <div class='col-sm-4'> <div class='well well-sm  bg-color-darken txt-color-white no-border'> <div class='fa-lg'>Total : <span class='ptotal pull-right'>$ "+bTotal+"</span> </div> </div> </div> </div> </div> </div>");
        }
    });
    
    $('#imp_uni_inventario').on('blur', function(){
        var impUni=0,canInv=0,valTotal=0;
        canInv = parseFloat($('#entrada_inventario').val());
        isNaN(canInv) ? canInv=0 : canInv=canInv;
        impUni = parseFloat($(this).val());
        valTotal = canInv * impUni; 
        $('#no_total_inventariado').val(valTotal);
        $('#total_inventario').val(valTotal);
    });
    
    $('#imp_inventario_aud').on('blur', function(){
        var impUni=0,canInv=0,valTotal=0;
        canInv = parseFloat($('#cantidad_inventario_aud').val());
        isNaN(canInv) ? canInv=0 : canInv=canInv;
        impUni = parseFloat($(this).val());
        valTotal = canInv * impUni; 
        $('#no_total_inventariado').val(valTotal);
        $('#no_total_inventario').val(valTotal);
    });
    
    $('#imp_inventario_ajuste').on('blur', function(){
        var impUni=0,canInv=0,valTotal=0;
        canInv = parseFloat($('#can_inventario_ajuste').val());
        isNaN(canInv) ? canInv=0 : canInv=canInv;
        impUni = parseFloat($(this).val());
        valTotal = canInv * impUni; 
        $('#no_total_inventariado').val(valTotal);
        $('#no_total_inventario').val(valTotal);
    });
    
    function alert_data(tipo,titulo,descrip,color){
        $.smallBox({
            title : titulo,
            content : descrip,
            color : color,
            iconSmall : "fa fa-thumbs-up bounce animated",
            timeout : 5000
        });
    }
    
    function obtenerVariables( name, url ){
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp ( regexS );
	var tmpURL = url;
	var results = regex.exec( tmpURL );
	if( results == null )
		return"";
	else
		return results[1];
    }
    
    function cargaEventoFormulario(dTabla,cCase){
        //normalizamos la habilitacion de los botones de nuevo y actualizar
        $('select').each( function(e,a){
            if($(this).is('[disabled]')){
                var _parentspan = $(this).parent();
                _parentspan = _parentspan.children('span');
                _parentspan = _parentspan.children('button');
                _parentspan.attr({'disabled':true});
            }
        });
        /*setTimeout( function(){
            $('select').each( function(e,a){
                $(this).select2('destroy');
                var _width = $(this).css('width');
                console.log(_width);
                if(!$(this).hasClass('form-control')){
                    $(this).attr({width: _width});
                    $(this).select2(width: 'resolve');
                }
            });
        },2000);*/
        if(dTabla!==undefined){
            if($('#form').length){
                var controller = $.base64.decode($("#form").attr('data'));
                console.log('controller: '+controller+' dtabla: '+dTabla+' ccase: '+cCase);
                var iCount = 0;
            
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data:"dTabla="+dTabla+"&cCase="+cCase,
                    url: "modules/" + controller + "/controller/" + controller + 'JsonController.php', 
                    success : function(data) {
                        //console.log(data);
                        switch(cCase){
    //                        case ('nuevaFactura'): case ('nuevaFacturacion'):
    //                            $("input[name='cod_numeracion']").val(data.cod_numeracion);
    //                            $("#num_factura").val(data.numeracion);
    //                            $('input[name="ind_cotizacion"]').prop("checked",true);
    //                        break;
    //                        case ('nuevaPagosRecibidos'):
    //                            $('#num_sig_recibocaja').val(data.numComprobante);
    //                        break;
    //                        case ('nuevaPagosRealizado'):
    //                            $('#num_sig_compago').val(data.numComprobante);
    //                        break;
                            case ('nuevaServicio'):
                                if($('#no_cod_estado').val()=='SCC' || $('#no_cod_estado').val()=='STR'){
                                    $('#no_cod_usuario_trasferido').attr({'disabled':true});
                                    $('#hd_gestion').show('slow');
                                }
                            break;
                            case 'NuevaConfigDental':
                                if($('input[name="ind_temporales"]').prop("checked")){ 
                                    $("#endodoncia_config_dental_temporales").fadeIn();
                                    $("#endodoncia_config_dental").fadeOut();
                                    $('input[name="no_ind_temporales"]').prop({'checked':false});
                                }
                            break;
                            case ('NuevaHistoriaClinica'):
                                if($('input[name="ind_temporales"]').prop("checked")){ 
                                    $("#endodoncia_config_dental_temporales").fadeIn();
                                    $("#endodoncia_config_dental").fadeOut();
                                    $('input[name="no_ind_temporales"]').prop({'checked':false});
                                }
                                $('#no_cod_config_medicamentos').attr({'hidden':true});
                                $('#cod_paciente').attr({'hidden':true});
                                //tratamos los select ajax segun corresponda
                                var formatSelection = function(e) {return e.text}
                                var formatResult = function(e) { return '<span class=""></span>'+e.text}
                                var initSelection = function(elem, cb) { return elem}
                                $('#cod_paciente').select2({
                                    minimumInputLength: 3,
                                    width: '100%',
                                    multiple: false,
                                    id: function (e) { return e == undefined ? null : e.id; },
                                    escapeMarkup: function (e) {return e; },
                                    formatResult: formatResult,
                                    formatSelection: formatSelection,
                                    initSelection: initSelection,
                                    ajax: {
                                        url: 'modules/endodoncia/controller/endodonciaJsonController.php?cCase=consultaPacientes&dTabla=endodoncia_paciente',
                                        dataType: 'json',
                                        type: "GET",
                                        cache: true,
                                        data: function (term) {return {term: term};},
                                        results: function (data,page) {return {results: $.map(data, function (item) {return {text: item.result,id: item.codigo}})};}
                                    }
                                });
                                var _parent = $('#cod_paciente').parent();
                                _parent = _parent.children('div');
                                _parent.removeClass('form-control'); 
                                // select para los medicamentos
                                var formatSelection_1 = function(e) {return e.text}
                                var formatResult_1 = function(e) { return '<span class=""></span>'+e.text}
                                var initSelection_1 = function(elem, cb) { return elem}
                                $('#no_cod_med').select2({
                                    minimumInputLength: 3,
                                    width: '100%',
                                    multiple: true,
                                    id: function (e) { return e == undefined ? null : e.id; },
                                    escapeMarkup: function (e) {return e; },
                                    formatResult: formatResult_1,
                                    formatSelection: formatSelection_1,
                                    initSelection: initSelection_1,
                                    ajax: {
                                        url: 'modules/endodoncia/controller/endodonciaJsonController.php?cCase=consultaMedicamentos&dTabla=endodoncia_config_medicamentos',
                                        dataType: 'json',
                                        type: "GET",
                                        cache: true,
                                        data: function (term) {return {term: term};},
                                        results: function (data,page) {return {results: $.map(data, function (item) {return {text: item.result,id: item.codigo}})};}
                                    }
                                });
                                var _parent_1 = $('#no_cod_med').parent();
                                _parent_1 = _parent_1.children('div');
                                _parent_1.removeClass('form-control');    

                                var _id, _panel, _desobturacion, _imagenes, _divDiente, _codHistoria;
                                var _controller = $.base64.decode(obtenerVariables('app', window.location.href));
                                _codHistoria = $('#cod_historia_clinica').val();
                                $('div[data-rel="DIENTE_HISTORIA"]').each( function(){
                                    if($(this).hasClass('active1')){_id = $(this).attr('data')}
                                });
                                if(_codHistoria!=''){
                                    $.ajax({
                                        type: "POST",
                                        dataType: "json",
                                        data:"controller="+_controller+"&cCase=consultaDienteHistoriaDatos&dTabla=endodoncia_config_dental&sValue="+_id+"&cod_paciente="+$('#no_cod_paciente').val()+"&cod_historia_clinica="+_codHistoria,
                                        url: "modules/" +_controller+ "/controller/" +_controller+ 'JsonController.php', 
                                        success : function(data) {
                                            if(data.row.cod_config_dental==0){
                                                alert_data('Mensaje App','Error','El diente seleccionado no ha sido parametrizado, revise la informacion o contacte al administrador','#C46A69');
                                            }else{
                                                _panel = $('#endodoncia_conductos');
                                                _panel.html('');
                                                _panel.append(data.panel.panel);
                                                _desobturacion = $('#endodoncia_desobturacion'); 
                                                _desobturacion.html('');
                                                _desobturacion.append(data.desobturacion.desobturacion);
                                                _imagenes = $('#endodoncia_registro_fotografico');
                                                _imagenes.html('');
                                                _imagenes.append(data.imagenes.imagenes);
                                                _imagenes.show('slow');
                                                $('#superbox').SuperBox();
                                                if($('input[name="ind_desobturacion"]').val()==1){$('#endodoncia_desobturacion').show('slow');}
                                            }
                                        }
                                    });
                                }
                            break;
                            case 'NuevaConsentimientosInfo':
                                $("#no_ruta_huella_config").val(data.ruta_huella_config);
                                $('#cod_paciente').attr({'hidden':true});
                                //tratamos los select ajax segun corresponda
                                var formatSelection = function(e) {
                                    $("#no_det_huella").val(e.text);
                                    $('[data="AGREGARHUELLA"]').attr({'disabled':false});
                                    $.ajax({
                                        type: "POST",
                                        dataType: "json",
                                        data:"controller=endodoncia&cCase=consultaHistoriaPaciente&dTabla=endodoncia_historia_clinica&sValue="+e.id,
                                        url: "modules/endodoncia/controller/endodonciaJsonController.php", 
                                        success : function(data) {
                                            $("#cod_historia_clinica").empty();
                                            $("#cod_historia_clinica").append("<option value=></option>");
                                            $.each(data, function(k,v){
                                                $("#cod_historia_clinica").append("<option value=\""+v.cod_historia_clinica+"\">"+v.value+"</option>");
                                            });
                                        }
                                    });        
                                    $('#cod_historia_clinica').attr({'disabled':false});
                                    return e.text};
                                var formatResult = function(e) { return e.text};
                                var initSelection = function(elem, cb) { return elem};
                                $('#cod_paciente').select2({
                                    minimumInputLength: 3,
                                    width: '100%',
                                    multiple: false,
                                    id: function (e) { return e == undefined ? null : e.id; },
                                    escapeMarkup: function (e) {return e; },
                                    formatResult: formatResult,
                                    formatSelection: formatSelection,
                                    initSelection: initSelection,
                                    ajax: {
                                        url: 'modules/endodoncia/controller/endodonciaJsonController.php?cCase=consultaPacientes&dTabla=endodoncia_paciente',
                                        dataType: 'json',
                                        type: "GET",
                                        quietMillis: 250,
                                        cache: true,
                                        delay: 250,
                                        data: function (term) {return {term: term};},
                                        results: function (data,page) {return {results: $.map(data, function (item) {return {text: item.result,id: item.codigo}})};}
                                    }
                                });
                                var _parent = $('#cod_paciente').parent();
                                _parent = _parent.children('div');
                                _parent.removeClass('form-control'); 

                            break;
                            case 'NuevaEvoluciones':
                                var _codUsu = $('#cod_paciente').val();
                                $.ajax({
                                    type: "POST",
                                    dataType: "json",
                                    data:"controller=endodoncia&cCase=consultaHistoriaPaciente&dTabla=endodoncia_historia_clinica&sValue="+_codUsu,
                                    url: "modules/endodoncia/controller/endodonciaJsonController.php", 
                                    success : function(data) {
                                        $("#cod_historia_clinica").empty();
                                        $("#cod_historia_clinica").append("<option value=></option>");
                                        $.each(data, function(k,v){
                                            $("#cod_historia_clinica").append("<option value=\""+v.cod_historia_clinica+"\">"+v.value+"</option>");
                                        });
                                    }
                                });    
                                $("#cod_historia_clinica").attr({'disabled':false});
                            break;
                            case 'NuevaAgendaMedica':
                                if($('#cod_agenda_medica').val()>0){
                                    $('#endodoncia_agenda_citas').show('slow');
                                }
                            break;
                            case 'NuevaIngresos':
                                var auxOpt       = $("#no_cod_paciente").find('option:selected');
                                var _codPaciente = auxOpt.val()
                                $.ajax({
                                    type: "POST",
                                    dataType: "json",
                                    data:"controller=endodoncia&cCase=no_cod_paciente_1&dTabla=endodoncia_pago&sValue="+_codPaciente,
                                    url: "modules/endodoncia/controller/endodonciaJsonController.php", 
                                    success : function(data) {
                                        $("#cod_historia_clinica").attr({'disabled':false});
                                        $("#no_cod_historia_clinica").empty();
                                        $("#no_cod_historia_clinica").append("<option value=></option>");
                                        $.each(data, function(k,v){
                                            $("#no_cod_historia_clinica").append("<option value=\""+v.cod_historia_clinica+"\">"+v.value+"</option>");
                                        });
                                        
                                        $("#no_cod_historia_clinica").select2('destroy');
                                        $("#no_cod_historia_clinica option").each(function(){
                                            if($(this).attr('value')==$("#no_cod_historia").val()){ $(this).prop('selected', true);}
                                        });
                                        $("#no_cod_historia_clinica").select2();
                                        $("#no_cod_historia_clinica").attr({'disabled':true});
                                    }
                                });    
                            break;
                        }
                    }
                });

                if($('input[name="no_ind_factura"]').prop("checked")){ 
                    $('#SubTotal').remove();
                    $('#SubTotal1').remove();
                    $('#SubTotal2').remove();
                    $('#SubTotal3').remove();
                    $('#SubTotal4').remove();
                    $("#fa_asociada").fadeIn("slow");
                    $('input[name="no_ind_categoria"]').prop("checked",false);
                    $("#cat_asociada").fadeOut("slow");
                    var _clon = $("#cat_asociada").children();
                    _clon = _clon.children().children('div').attr({"class":'clonar_no'});
                    var _clon = $("#fa_asociada").children();
                    _clon = _clon.children().children('div').attr({"class":'clonar'});
                    var _sel;
                    $('#no_cod_factura').find('option').each(function(){
                        _sel = $(this).prop('selected')?true:false;
                        if(_sel==false){$(this).remove();}
                    });
                    $('input[name="no_ind_categoria"]').attr({'disabled':'disabled'});
                    $('#no_imp_detpago_fac').focus();
                    setTimeout(function(){
                        $('#no_imp_detpago_fac').trigger('blur');
                    },1000);
                }
                if($('input[name="no_ind_categoria"]').prop("checked")){
                    $('#SubTotal').remove();
                    $('#SubTotal1').remove();
                    $('#SubTotal2').remove();
                    $('#SubTotal3').remove();
                    $('#SubTotal4').remove();
                    $("#cat_asociada").fadeIn("slow");
                    $('input[name="no_ind_factura"]').prop("checked",false);
                    $("#fa_asociada").fadeOut("slow");
                    var _clon = $("#fa_asociada").children();
                    _clon = _clon.children().children('div').attr({"class":'clonar_no'});
                    var _clon = $("#cat_asociada").children();
                    _clon = _clon.children().children('div').attr({"class":'clonar'});
                    $('#no_cod_categoria').find('option').each(function(){
                        _sel = $(this).prop('selected')?true:false;
                        if(_sel==false){$(this).remove();}
                    });
                    $('input[name="no_ind_factura"]').attr({'disabled':'disabled'});
                    $('#no_imp_detpago_cat').focus();
                    setTimeout(function(){
                        $('#no_imp_detpago_cat').trigger('blur');
                    },1000);
                }

                $('#cantidad,.spinerInput').each( function(){
                    if($(this).val()!=""){
                        $(this).focus();
                        $(this).trigger('blur');
                    }
                });
            }
        }
    }

    $('input[name="no_ind_factura"],input[name="no_ind_categoria"]').click( function(){
        var _marcado = $(this).prop("checked") ? true: false;
        var _name    = $(this).attr("name");
        var sOp      = $('#cod_cliente').find('option:selected');
        var sVal     = sOp.val();
        $('#SubTotal').remove();
        $('#SubTotal1').remove();
        $('#SubTotal2').remove();
        $('#SubTotal3').remove();
        $('#SubTotal4').remove();
        if(_marcado && _name=="no_ind_factura"){
            if(sVal==""){
                $('input[name="no_ind_factura"]').prop("checked",false);
                alert_data('Mensaje App','error','Debe seleccionar el cliente para asociar sus facturas','#C46A69');
            }else{
                $("#fa_asociada").fadeIn("slow");
                $('input[name="no_ind_categoria"]').prop("checked",false);
                $("#cat_asociada").fadeOut("slow");
                var _clon = $("#cat_asociada").children();
                _clon = _clon.children().children('div').attr({"class":'clonar_no'});
                var _clon = $("#fa_asociada").children();
                _clon = _clon.children().children('div').attr({"class":'clonar'});
            }
        }else if(_marcado && _name=="no_ind_categoria"){
             if(sVal==""){
                $('input[name="no_ind_categoria"]').prop("checked",false);
                alert_data('Mensaje App','error','Debe seleccionar el cliente para asociar el ingreso','#C46A69');
            }else{
                $("#cat_asociada").fadeIn("slow");
                $('input[name="no_ind_factura"]').prop("checked",false);
                $("#fa_asociada").fadeOut("slow");
                var _clon = $("#fa_asociada").children();
                _clon = _clon.children().children('div').attr({"class":'clonar_no'});
                var _clon = $("#cat_asociada").children();
                _clon = _clon.children().children('div').attr({"class":'clonar'});
            }
        }else{
            $("#fa_asociada").fadeOut("slow");
            $("#cat_asociada").fadeOut("slow");
        }
        var altura = $(document).height();
        $("html, body").animate({
            scrollTop:altura+"px"
        });
    });

    $('input[name="ind_desobturacion"]').click(function (){
        var _marcado = $(this).prop("checked") ? true: false;
        if(_marcado){
            $('#endodoncia_desobturacion').show('slow');
        }else{
            $('#endodoncia_desobturacion').hide('slow');
        }
    });

    $('body').on('click', '#ind_header', function(e){
        e.preventDefault();
        var controller = "sistema";
        var id         = $(this).attr("data-id");
        var head       = $(this).attr("data-header");
        var _icon      = $(this).attr("data-icon");
        var _color     = $(this).attr("data-color");
        var _nom       = $(this).attr("data-nombre");     
        var _href      = $(this).parent('a').attr("href");
        var _divP      = $("div#shortcut").children();      
        $(this).toggleClass('blue').toggleClass('blueDark');
        $(this).toggleClass('fa-sort-desc').toggleClass('fa-sort-asc');
        if(head==1){
            _divP.children("li:last").after('<li data-li-id="'+id+'"><a href="'+_href+'" class="jarvismetro-tile big-cubes bg-color-'+_color+'"><span class="iconbox"><i class="fa '+_icon+' fa-4x"></i><span> '+_nom+'</span></span></a></li>"');
            $(this).attr({"data-header":0});
        }else{
            _divP.children('li[data-li-id="'+id+'"]').remove();
            $(this).attr({"data-header":1});
        }
        $.ajax({
            type: "POST",
            dataType: "json",
            data:"controller=sistema&cCase=editMenuHeader&dTabla=sys_menu_sub&sValue="+id+"&sId="+head,
            url: "modules/" + controller + "/controller/" + controller + 'JsonController.php', 
            success : function(data) {
                if(data.a==1){
                    alert_data('Mensaje App','Hecho','La configuracion del menu ha sido editada exitosamente.','#296191');
                };
            }
        });
    });
    
    $('input[name="no_ind_reasignar"]').click( function(){
        var _marcado = $(this).prop("checked") ? true: false;
        if(_marcado){
            $("#no_cod_usuario").attr({'disabled':false});
        }else{
            $("#no_cod_usuario").attr({'disabled':true});
        }
    });
    
    $('input[name="ind_temporales"]').click( function(){
        var _marcado = $(this).prop("checked") ? true: false;
        if(!_marcado){
            $("#endodoncia_config_dental_temporales").fadeOut();
            $("#endodoncia_config_dental").fadeIn();
            $('input[name="no_ind_temporales"]').prop({'checked':false});
        }
    });

    $('input[name="no_ind_temporales"]').click( function(){
        var _marcado = $(this).prop("checked") ? true: false;
        if(_marcado){
            $("#endodoncia_config_dental_temporales").fadeIn();
            $("#endodoncia_config_dental").fadeOut();
            $('input[name="ind_temporales"]').prop({'checked':true});
        }
    });

    $('input[name="ind_factura"],input[name="ind_cotizacion"]').click(function(){
        var _marcado = $(this).prop("checked") ? true: false;
        var _name    = $(this).attr("name");
        if(_marcado && _name=='ind_factura'){
            $('input[name="ind_cotizacion"]').prop("checked",false);
        }else if(_marcado && _name=='ind_cotizacion'){
            $('input[name="ind_factura"]').prop("checked",false);
        }
    });
    
    $('input[name="no_ind_cambio_grupo"],input[name="no_ind_cambio_estado"]').click( function(){
        var _marcado = $(this).prop("checked") ? true: false;
        var _name    = $(this).attr("name");
        if(_marcado && _name=='no_ind_cambio_grupo'){
            $('#cod_grupo').attr({"disabled":false});
        }else if(!_marcado && _name=='no_ind_cambio_grupo'){
            $('#cod_grupo').attr({"disabled":true});
        }
        if(_marcado && _name=='no_ind_cambio_estado'){
            $('#no_cod_estado').attr({"disabled":false});
        }else if(!_marcado && _name=='no_ind_cambio_estado'){
            $('#no_cod_estado').attr({"disabled":true});
        }
    });

    $('body').on('click','tr', function(){
        var _td;
        _td = $(this).children('td').children('input[name="id_dato"]');
        _type = _td.attr('type');
        switch(_type){
            case('checkbox'):
                var _marcado = _td.prop("checked") ? true: false;
                if(!_marcado){
                   _td.prop('checked', true);
                    EstValoresRadio(_td,0);
                }else{
                    _td.prop('checked', false);
                    EstValoresRadio(_td,1);
                }
            break;
            case('radio'):
                _td.prop('checked', true);
                EstValoresRadio(_td,0);
            break;
        }
        /*_td.prop('checked', true);
        EstValoresRadio(_td);*/
    });

    $('input[name="no_ind_desobturacion"]').click( function (){
        var _marcado = $(this).prop("checked") ? true: false;
        if(_marcado){
            $('#endodoncia_desobturacion').fadeIn('slow');  
        }else{
            $('#endodoncia_desobturacion').fadeOut('slow');
        }
    });

    $('input[name="img_paciente_consentimiento"]').change(function(){
        readURL(this);
    });

    function EstValoresRadio(e,t){
        var _id    =","+e.attr("value");
        var _table =","+e.attr("data");
        var _this  =$("[data=MODIFICAR]");  
        var _href  =_this.attr("href");
        var _this1 =$("[data=DETALLES]") 
        var _href1 =_this1.attr("href");
        var _this2 =$("[data=ELIMINAR]");
        var _this3 =$("[data=DESACTIVAR]");
        var _this4 =$("[data=GESTIONAR]") 
        var _href4 =_this4.attr("href");
        var _this5 =$("[data=MODIFICARADMIN]");  
        var _href5 =_this5.attr("href");
        var _this6 =$("[data=GENERARPDF]");
        var _href6 =_this6.attr("href");
        var _this7 =$("[data=ENVIARODONTOLOGO]");
        var _href7 =_this7.attr("href");
        var _this8 =$("[data=ENVIARPACIENTE]");
        var _href8 =_this8.attr("href");
        switch(e.attr('type')){
            case('checkbox'):
                if(t==0){
                    _data= _data+e.attr("value")+'-';
                }else{
                    _data=_data.replace(e.attr("value"),"");
                }
            break;
        }
        _id = _data==''?_id:_data;
        _this.attr({"href":"","href":'?app='+obtenerVariables('app',_href)+'&met='+obtenerVariables('met',_href)+'&arg='+$.base64.encode($.base64.decode(obtenerVariables('arg',_href))+_id+_table+',0,M')});
        _this1.attr({"href":"","href":'?app='+obtenerVariables('app',_href1)+'&met='+obtenerVariables('met',_href1)+'&arg='+$.base64.encode($.base64.decode(obtenerVariables('arg',_href1))+_id+_table+',1,D')});
        _this2.attr({"data-table": _table.replace(",",""), "data-id": _id.replace(",","")});
        _this3.attr({"data-table": _table.replace(",",""), "data-id": _id.replace(",","")});
        _this4.attr({"href":"","href":'?app='+obtenerVariables('app',_href4)+'&met='+obtenerVariables('met',_href4)+'&arg='+$.base64.encode($.base64.decode(obtenerVariables('arg',_href4))+_id+_table+',0,M')});
        _this5.attr({"href":"","href":'?app='+obtenerVariables('app',_href5)+'&met='+obtenerVariables('met',_href5)+'&arg='+$.base64.encode($.base64.decode(obtenerVariables('arg',_href5))+_id+_table+',1,A')});
        _this6.attr({"href":"","href":'?app='+obtenerVariables('app',_href6)+'&met='+obtenerVariables('met',_href6)+'&arg='+$.base64.encode(_id.replace(",","")+_table+","+0),'target':'_blank'});
        _this7.attr({"href":"","href":'?app='+obtenerVariables('app',_href6)+'&met='+obtenerVariables('met',_href6)+'&arg='+$.base64.encode(_id.replace(",","")+_table+","+1)});
        _this8.attr({"href":"","href":'?app='+obtenerVariables('app',_href6)+'&met='+obtenerVariables('met',_href6)+'&arg='+$.base64.encode(_id.replace(",","")+_table+","+2)});
    }

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img_previa').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
        numero=parseFloat(numero);
        if(isNaN(numero)){
            return "";
        }
        if(decimales!==undefined){
            numero=numero.toFixed(decimales);
        }
        numero=numero.toString().replace(".", separador_decimal!==undefined ? separador_decimal : ",");

        if(separador_miles){
            var miles=new RegExp("(-?[0-9]+)([0-9]{3})");
            while(miles.test(numero)) {
                numero=numero.replace(miles, "$1" + separador_miles + "$2");
            }
        }
        return numero;
    }

});
