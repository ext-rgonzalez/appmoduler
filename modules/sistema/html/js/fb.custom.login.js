$(document).ready(function() {
	var Err = $("#err").attr('data');
	if(Err == 0 || Err == 1 || Err == 2 || Err == 3){
		var msj = $("#err").attr('data_msj');
		alert_data('Mensaje App','error',msj,'picon icon24 typ-icon-cancel white');
	}
	
	$("#loginForm").validate({
		
		rules: {
			username: {
				required: true,
				minlength: 4
			},
			password: {
				required: true,
				minlength: 6
			}  
		},
		messages: {
			username: {
				required: "Porfavor ingrese el password",
				minlength: "El Usuario debe tener mas de 4 caracteres"
			},
			password: {
				required: "Porfavor ingrese el password",
				minlength: "El password debe tener mas de 6 caractares"
			}
		}   
	});
	
	function alert_data(tipo,titulo,descrip,icono){
		$.smallBox({
			title : titulo,
			content : descrip,
			color : "#296191",
			iconSmall : "fa fa-thumbs-up bounce animated",
			timeout : 4000
		});

	}
	
});