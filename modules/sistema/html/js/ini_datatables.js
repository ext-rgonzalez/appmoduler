$(document).ready( function(){
	
	// PAGE RELATED SCRIPTS

	/* remove previous elems */
	
	if($('.DTTT_dropdown.dropdown-menu').length){
		$('.DTTT_dropdown.dropdown-menu').remove();
	}

	loadDataTableScripts();
	function loadDataTableScripts() {

		loadScript("js/plugin/datatables/jquery.dataTables-cust.min.js", dt_2);

		function dt_2() {
			loadScript("js/plugin/datatables/ColReorder.min.js", dt_3);
		}

		function dt_3() {
			loadScript("js/plugin/datatables/FixedColumns.min.js", dt_4);
		}

		function dt_4() {
			loadScript("js/plugin/datatables/ColVis.min.js", dt_5);
		}

		function dt_5() {
			loadScript("js/plugin/datatables/ZeroClipboard.js", dt_6);
		}

		function dt_6() {
			loadScript("js/plugin/datatables/media/js/TableTools.min.js", dt_7);
		}

		function dt_7() {
			loadScript("js/plugin/datatables/DT_bootstrap.js", runDataTables);
		}

	}

	function runDataTables() {

		/*
		 * BASIC
		 */
		$('#dt_basic').dataTable({
			"sPaginationType" : "bootstrap_full",
                        "scrollX": true,
                        "sScrollX": "1200px"
		});

		/* END BASIC */

		/* Add the events etc before DataTables hides a column */
		$("#datatable_fixed_column thead input").keyup(function() {
			oTable.fnFilter(this.value, oTable.oApi._fnVisibleToColumnIndex(oTable.fnSettings(), $("thead input").index(this)));
		});

		$("#datatable_fixed_column thead input").each(function(i) {
			this.initVal = this.value;
		});
		$("#datatable_fixed_column thead input").focus(function() {
			if (this.className == "search_init") {
				this.className = "";
				this.value = "";
			}
		});
		$("#datatable_fixed_column thead input").blur(function(i) {
			if (this.value == "") {
				this.className = "search_init";
				this.value = this.initVal;
			}
		});		
		

		var oTable = $('#datatable_fixed_column').dataTable({
			"sDom" : "<'dt-top-row'><'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
			//"sDom" : "t<'row dt-wrapper'<'col-sm-6'i><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'>>",
			"oLanguage" : {
				"sSearch" : "Search all columns:"
			},
			"bSortCellsTop" : true,
                        "scrollX": true
		});		
		


		/*
		 * COL ORDER
		 */
		$('#datatable_col_reorder').dataTable({
                    "pagingType": "scrolling",
                    "sPaginationType" : "bootstrap",
                    "sScrollX": "100%",
                    "bDeferRender": true,
                    "sDom" : "R<'dt-top-row'Clf>r<'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
                    "fnInitComplete" : function(oSettings, json) {
                            $('.ColVis_Button').addClass('btn btn-default btn-sm').html('Columnas <i class="fa fa-chevron-down"></i>');
                            oSettings.oScroller.fnScrollToRow();
                    }
		});
		
		/* END COL ORDER */

		/* TABLE TOOLS */
		$('#datatable_tabletools').dataTable({
                    // "bAutoWidth": true,
                    // 'bUseRendered' : false,
                    // "sScrollX": "auto",
                    "scrollX":true,
                    "aaSorting":[[1,"desc"]],
                    stateSave: true,
                    "iDisplayLength": 25,
                    // "sScrollX":"auto",
                    "sDom" : "R<'dt-top-row'Tlf>r<'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
                    "oTableTools" : {
                        "aButtons" : ["copy", "print", {
                            "sExtends" : "collection",
                            "sButtonText" : 'Guardar Como <span class="caret" />',
                            "aButtons" : ["csv", "xls", "pdf"],
                            
                        }],
                        "sSwfPath" : "modules/sistema/html/js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
                    },
                    "fnInitComplete" : function(oSettings, json) {
                            
                        $(this).closest('#dt_table_tools_wrapper').find('.DTTT.btn-group').addClass('table_tools_group').children('a.btn').each(function() {
                                $(this).addClass('btn-sm btn-default');
                        });
                    }
		});
		
		/* END TABLE TOOLS */

	}

});