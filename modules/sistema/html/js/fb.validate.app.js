$(document).ready(function(){
    $.validator.addMethod("mynumber", function (value, element) {
        return this.optional(element) || /^(\d+|\d+,\d{1,2})$/.test(value);
    },"Decimal");
    $("#form").validate({
        rules: {
            nom_empresa:     {required: true},
            nit_empresa:     {required: true, number: true},
            rep_empresa:     {required: true},
            tel_empresa:     {required: true},
            email_empresa:   {required: false, email: true},
            web_empresa:     {required: false, url:   true},	
            dir_empresa:     {required: true},
            nom_usuario:     {required: true},
            ape_usuario:     {required: true},
            dir_usuario:     {required: true},
            usuario_usuario: {required: true, minlength: 12},
            password_usuario:{required: true, minlength: 8},
            no_password_usuario_config:{required: true, equalTo: "#password_usuario"},
            por_impuesto    :{required:true},
            nom_item        :{required:true},
            ref_item        :{required:true},
            imp_compra_item :{required:true,  number: true},
            inc_porcen_item :{required:true},
            nom_restaurantes:{required:true},
            des_restaurantes:{required:true},
            esl_restaurantes:{required:true},
            tel_restaurantes:{required:true},
            email_restaurantes:{required:true},
            fb_restaurantes:{required:true, url:   true},
            tw_restaurantes:{required:true, url:   true},
            cx_restaurantes:{required:true},
            cy_restaurantes:{required:true},
            cod_ciudad: 'required',
            cod_estado: 'required',
            nom_imagen:{required:true},
            des_imagen:{required:true},
            cod_restaurantes: 'required',
            nom_entrantes:{required:true},
            des_entrantes:{required:true},
            img_carta:{required:true},
            val_entrantes:{required:true,number: true},
            nom_categorias:{required:true},
            img_categorias:{required:true},
            nom_categorias_sub:{required:true},
            img_categorias_sub:{required:true},
            cod_categorias:{required:true},
            cod_categorias_sub:{required:true},
            nom_naturaleza_juridica:{required:true},
            nom_universidad:{required:true},
            dir_universidad:{required:true},
            tel1_universidad:{required:true, number:true},
            tel2_universidad:{required:true, number:true},
            email_universidad:{required:true,email:true},
            tipo_universidad:{required:true},
            tipo_ins_universidad:{required:true},
            nit_universidad:{required:true},
            cod_universidad_oficial:{required:true},
            nom_seccionales:{required:true},
            tipo_seccionales:{required:true},
            dir_seccionales:{required:true},
            email_seccionales:{required:true, email:true},
            web_seccionales:{required:true,url:true},
            tel1_seccionales:{required:true, number:true},
            tel2_seccionales:{required:true, number:true},
            cod_ciudad: 'required',
            cod_naturaleza_juridica:'required',
            cod_estado:'required',
            cod_universidad:'required',
            cod_estado: 'required',
            cod_empresa: 'required',
            cod_met_pago:'required',
            cod_cliente:'required',
            /*Campos requeridos helpdesk*/
            nit_servicio:    {required: true, number: true},
            email_servicio:  {required: false, email: true},
            tels_servicio:   {required: true},
            nom_servicio:    {required: true},
            cod_categorias:    'required',
            cod_referencia:    'required',
            cod_prioridad:     'required',
            cod_area_solicitud:'required',
            des_servicio:    {required: true},
            nom_config:    {required: true},
            host_envio_config:    {required: true},
            host_recepcion_config:    {required: true},
            email_envio_config: {required: true, email: true},
            email_recepcion_config: {required: true, email: true},
            pass_envio_config:  {required: true},
            no_pass_envio_config:{required: true, equalTo: "#pass_envio_config"},
            pass_recepcion_config:  {required: true},
            no_pass_recepcion_config:{required: true, equalTo: "#pass_recepcion_config"},
            from_config: {required:true},
            asunto_config: {required:true},
            asunto_envio_config: {required:true},
            cad_asunto_config: {required:true},
            textarea: {required:true},
            no_cod_estado: {required:true},
            port_envio_config:    {required: true, number: true},
            port_recepcion_config:    {required: true, number: true},
            /*Fin Campos requeridos helpdesk*/
            /*Campos requeridos sined*/
            'no_cuantitativa_notas[]': {required: true , range:[0.0,10.0]},
            'no_des_valoracion_descriptiva[]': {required: true},
            'no_com_soc_notas[]':{required: true , range:[0.0,10.0]},
            cod_usuario:'required',
            cod_carga_academica:'required',
            cod_grupo:'required',
            cod_periodo_academico:'required',
            cod_tipoimpresion:'required',
            /*Fin Campos requeridos sined*/
            /*Campos requeridos crm*/
            nom_productos: {required:true},
            des_productos: {required:true},
            'no_cod_usuario[]':{required:true},
            'no_cod_productos[]': {required:true},
            'no_nom_subproductos[]':{required:true},
            'no_des_subproductos[]':{required:true},
            'no_cod_estado[]':{required:true},
            'no_cod_empresa[]':{required:true},
            'no_cod_proveedor[]':{required:true},
            'no_des_medios[]':{required:true},
            'no_nom_medios[]':{required:true},
            nom_cliente: {required:true},
            /*nit_cliente: {required:true, number: true},
            dir_cliente:{required:true},*/
            email_cliente:{required:true,email: true},
            /*tel_cliente:{required:true, number: true},*/
            cel_cliente: {required:true, number: true},
            cod_tipopago: 'required',
            no_cod_usuario: 'required',
            no_cod_medios: 'required',
            'no_obs_contacto[]':{required: true},
            'no_fec_aprox_contacto[]':{required: true},
            'no_fec_prox_contacto[]':{required: true},
            'no_cod_subproductos[]':{required:true},
            'no_cod_usuario_asignacion[]':{required:true},
            /*Fin Campos requeridos crm*/
            /* campos endodoncia */
            imp_tratamiento_historia_clinica: {required: true, number:true},
            cod_paciente: {required: true}
            /* fin campos endodoncia */
        },
        messages: {
            nom_empresa:   {required: "Este campo es obligatorio"},
            nit_empresa:   {required: "Este campo es obligatorio",  number: "El campo debe ser numerico"},
            rep_empresa:   {required: "Este campo es obligatorio"},
            tel_empresa:   {required: "Este campo es obligatorio"},
            email_empresa: {email:    "Ingrese un email valido"},
            web_empresa:   {url:      "Ingrese una Url valida"},
            dir_empresa:   {required: "Este campo es obligatorio"},
            nom_usuario:   {required: "Este campo es obligatorio"},
            ape_usuario:   {required: "Este campo es obligatorio"},
            dir_usuario:   {required: "Este campo es obligatorio"},
            usuario_usuario:           {required: "Este campo es obligatorio",minlength: "Minimo 12 caracteres"},			
            password_usuario:          {required: "Este campo es obligatorio", minlength: "Minimo 8 caracteres"},
            no_password_usuario_config:   {required: "Este campo es obligatorio", equalTo: "Los campos no coinciden"},
            por_impuesto:   {required: "Este campo es obligatorio"},
            nom_item        :{required: "Este campo es obligatorio"},
            ref_item        :{required: "Este campo es obligatorio"},
            imp_compra_item :{required: "Este campo es obligatorio", number: "El campo debe ser numerico"},
            inc_porcen_item :{required: "Este campo es obligatorio"},
            nom_restaurantes:{required:"Este campo es obligatorio"},
            des_restaurantes:{required:"Este campo es obligatorio"},
            esl_restaurantes:{required:"Este campo es obligatorio"},
            tel_restaurantes:{required:"Este campo es obligatorio"},
            email_restaurantes:{required:"Este campo es obligatorio"},
            fb_restaurantes:{required:"Este campo es obligatorio", url:"Ingrese una Url valida"},
            tw_restaurantes:{required:"Este campo es obligatorio", url:"Ingrese una Url valida"},
            cx_restaurantes:{required:"Este campo es obligatorio"},
            cy_restaurantes:{required:"Este campo es obligatorio"},
            cod_ciudad: 'Este campo es obligatorio',
            cod_estado: 'Este campo es obligatorio',
            nom_imagen:{required:"Este campo es obligatorio"},
            des_imagen:{required:"Este campo es obligatorio"},
            cod_restaurantes: 'Este campo es obligatorio',
            nom_entrantes:{required:"Este campo es obligatorio"},
            des_entrantes:{required:"Este campo es obligatorio"},
            img_carta:{required:"Este campo es obligatorio"},
            val_entrantes:{required:"Este campo es obligatorio",number: "El campo debe ser numerico"},
            nom_categorias:{required:"Este campo es obligatorio"},
            img_categorias:{required:"Este campo es obligatorio"},
            nom_categorias_sub:{required:"Este campo es obligatorio"},
            img_categorias_sub:{required:"Este campo es obligatorio"},
            cod_categorias:{required:"Este campo es obligatorio"},
            cod_categorias_sub:{required:"Este campo es obligatorio"},
            nom_naturaleza_juridica:{required: "Este campo el obligatorio"},
            nom_universidad:{required:"Este campo es obligatorio"},
            dir_universidad:{required:"Este campo es obligatorio"},
            tel1_universidad:{required:"Este campo es obligatorio", number:"El campo debe ser numerico"},
            tel2_universidad:{required:"Este campo es obligatorio", number:"El campo debe ser numerico"},
            email_universidad:{required:"Este campo es obligatorio",email:"Ingrese un email valido"},
            tipo_universidad:{required:"Este campo es obligatorio"},
            tipo_ins_universidad:{required:"Este campo es obligatorio"},
            nit_universidad:{required:"Este campo es obligatorio"},
            cod_universidad_oficial:{required:"Este campo es obligatorio"},
            nom_seccionales:{required:"Este campo es obligatorio"},
            tipo_seccionales:{required:"Este campo es obligatorio"},
            dir_seccionales:{required:"Este campo es obligatorio"},
            email_seccionales:{required:"Este campo es obligatorio", email:"Ingrese un email valido"},
            web_seccionales:{required:"Este campo es obligatorio",url: "Ingrese una Url valida"},
            tel1_seccionales:{required:"Este campo es obligatorio", number:"El campo debe ser numerico"},
            tel2_seccionales:{required:"Este campo es obligatorio", number:"El campo debe ser numerico"},
            cod_ciudad: 'Este campo es obligatorio',
            cod_naturaleza_juridica:'Este campo es obligatorio',
            cod_estado:'Este campo es obligatorio',
            cod_universidad:'Este campo es obligatorio',
            cod_empresa:'Este campo es obligatorio',
            cod_met_pago:'Este campo es obligatorio',
            cod_cliente:'Este campo es obligatorio',
        /*Campos requeridos Helpdesk*/
            nit_servicio:       {required: 'Este campo es obligatorio', number: 'El campo debe ser numerico sin guion de verificacion'},
            email_servicio:     {required: 'Este campo es obligatorio', email:"Ingrese un email valido"},
            tels_servicio:      {required: 'Este campo es obligatorio'},
            nom_servicio:       {required: 'Este campo es obligatorio'},
            cod_categorias:     'Este campo es obligatorio',
            cod_referencia:     'Este campo es obligatorio',
            cod_prioridad:      'Este campo es obligatorio',
            cod_area_solicitud: 'Este campo es obligatorio',
            des_servicio:       {required:'Este campo es obligatorio'},
            nom_config:         {required: "Este campo es obligatorio"},
            host_envio_config:     {required: "Este campo es obligatorio"},
            host_recepcion_config: {required: "Este campo es obligatorio"},
            email_envio_config: {required: 'Este campo es obligatorio', email:"Ingrese un email valido"},
            pass_envio_config:  {required: "Este campo es obligatorio"},    
            no_pass_envio_config:{required: "Este campo es obligatorio", equalTo: "Los campos no coinciden"},
            pass_recepcion_config:  {required: "Este campo es obligatorio"},    
            no_pass_recepcion_config:{required: "Este campo es obligatorio", equalTo: "Los campos no coinciden"},
            from_config: {required: "Este campo es obligatorio"},
            asunto_config: {required: "Este campo es obligatorio"},
            asunto_envio_config: {required: "Este campo es obligatorio"},
            cad_asunto_config: {required: "Este campo es obligatorio"},
            textarea: {required: "Este campo es obligatorio"},
            no_cod_estado: {required: "Este campo es obligatorio"},
            port_envio_config:    {required:"Este campo es obligatorio", number:"El campo debe ser numerico"},
            port_recepcion_config:    {required:"Este campo es obligatorio", number:"El campo debe ser numerico"},
        /*Campos requeridos Helpdesk*/
        /*Campos requeridos sined*/
            'no_cuantitativa_notas[]': {required: "Obligatorio",range: "Entre 0-10"},
            'no_com_soc_notas[]': {required: "Obligatorio",range: "Entre 0-10"},
            'no_des_valoracion_descriptiva[]': {required: "Obligatorio"},
            cod_usuario: 'Este campo es obligatorio',
            cod_carga_academica: 'Este campo es obligatorio',
            cod_grupo: 'Este campo es obligatorio',
            cod_periodo_academico: '',
            cod_tipoimpresion:'Este campo es obligatorio',
        /*Campos requeridos crm*/
            nom_productos:'Este campo es obligatorio',
            des_productos: 'Este campo es obligatorio',
            'no_cod_usuario[]':'Este campo es obligatorio',
            'no_cod_productos[]': 'Este campo es obligatorio',
            'no_nom_subproductos[]':'Este campo es obligatorio',
            'no_des_subproductos[]':'Este campo es obligatorio',
            'no_cod_estado[]':'Este campo es obligatorio',
            'no_cod_empresa[]':'Este campo es obligatorio',
            'no_cod_proveedor[]':'Este campo es obligatorio',
            'no_des_medios[]':'Este campo es obligatorio',
            'no_nom_medios[]':'Este campo es obligatorio',
            nom_cliente: 'Este campo es obligatorio',
            /*nit_cliente: {required:"Este campo es obligatorio", number:"El campo debe ser numerico"},
            dir_cliente:'Este campo es obligatorio',*/
            email_cliente:{required: 'Este campo es obligatorio', email:"Ingrese un email valido"},
            /*tel_cliente:{required:"Este campo es obligatorio", number:"El campo debe ser numerico"},*/
            cel_cliente: {required:"Este campo es obligatorio", number:"El campo debe ser numerico"},
            cod_tipopago: 'Este campo es obligatorio',
            no_cod_medios: 'Este campo es obligatorio',
            'no_obs_contacto[]': 'Este campo es obligatorio',
            'no_fec_aprox_contacto[]':'Este campo es obligatorio',
            'no_fec_prox_contacto[]':'Este campo es obligatorio',
            'no_cod_subproductos[]':'Este campo es obligatorio',
            'no_cod_usuario_asignacion[]':'Este campo es obligatorio',
            no_cod_usuario:'Este campo es obligatorio',
        /* campos endodoncia*/
            imp_tratamiento_historia_clinica: {required: 'Este campo es obligatorio', number: 'El campos debe ser numerico'},
            cod_paciente: 'Este campo es obligatorio'
        }   
    });
});

