$(document).ready(function() { 	
    $(".selectMultiple").each(function(i) {
        $(this).select2({
            //width: 'resolve'
        });    
    });
    $(".selectFiltro").each(function(i) {
        $(this).select2({ 
            width: 'resolve',
            dropdownAutoWidth : false
        });    
    });
    $(".spinerInput").each(function(i) {
        $(this).spinner();    
    });
    $(".selectFecha").each(function(i) {
        if($(this).length) {
            $(this).datetimepicker({
                format: 'yyyy-mm-dd',
                showTimepicker: false,
                minView: 2,
                changeYear:true,
            });
        }    
    });
    $(".selectFechaHora").each(function(i) {
        if($(this).length) {
            $(this).datetimepicker({format: 'yy-mm-dd hh:ii'});
        }    
    });
    $(".spinnerDecimal").each(function(i) {
        $(this).mask("99,99"); 
//            if($(this).length) {
//                $(this).spinner({
//		    step: 0.01,
//		    numberFormat: "0,00"
//		});
//            }
    });
    $(".selectHora").each(function(i) {
    	$(this).timeEntry({
            show24Hours: true,
            ampmPrefix: '',
            spinnerImage: ''
        });
        $(this).timeEntry('setTime', new Date());
    });
    //------------- Masked input -------------//
    $("#mask-phone").mask("(999) 999-9999", {
        completed:function(){alert("Callback action after complete");}
    });
    $("#mask-phoneExt").mask("(999) 999-9999? x99999");
    $("#mask-phoneInt").mask("+40 999 999 999");
    $("#mask-date").mask("99/99/9999");
    $("#mask-ssn").mask("999-99-9999");
    $("#mask-productKey").mask("a*-999-a999", { placeholder: "*" });
    $("#mask-eyeScript").mask("~9.99 ~9.99 999");
    $(".mask-percent").each(function(i){
        $(this).mask("999");  
    });
    $(".mask-datetimepicker").each(function(i){
        $(this).mask("9999/99/99 99:99:00 aa");
    });
});