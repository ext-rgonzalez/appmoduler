 
	// DO NOT REMOVE : GLOBAL FUNCTIONS!
	pageSetUp();
	
	/*
	 * PAGE RELATED SCRIPTS
	 */
	
	$(".js-status-update a").click(function () {
	    var selText = $(this).text();
	    var $this = $(this);
	    $this.parents('.btn-group').find('.dropdown-toggle').html(selText + ' <span class="caret"></span>');
	    $this.parents('.dropdown-menu').find('li').removeClass('active');
	    $this.parent().addClass('active');
	});
	
	/*
	 * TODO: add a way to add more todo's to list
	 */
	
	// initialize sortable
	$(function () {
	    $("#sortable1, #sortable2").sortable({
	        handle: '.handle',
	        connectWith: ".todo",
	        update: countTasks
	    }).disableSelection();
	});
	
	// check and uncheck
	$('.todo .checkbox > input[type="checkbox"]').click(function () {
	    var $this = $(this).parent().parent().parent();
	
	    if ($(this).prop('checked')) {
	        $this.addClass("complete");
	
	        // remove this if you want to undo a check list once checked
	        //$(this).attr("disabled", true);
	        $(this).parent().hide();
	
	        // once clicked - add class, copy to memory then remove and add to sortable3
	        $this.slideUp(500, function () {
	            $this.clone().prependTo("#sortable3").effect("highlight", {}, 800);
	            $this.remove();
	            countTasks();
	        });
	    } else {
	        // insert undo code here...
	    }
	
	})
	// count tasks
	function countTasks() {
	
	    $('.todo-group-title').each(function () {
	        var $this = $(this);
	        $this.find(".num-of-tasks").text($this.next().find("li").size());
	    });
	
	}

	/*
	 * VECTOR MAP
	 */

	data_array = {
	    "US": 4977,
	    "AU": 4873,
	    "IN": 3671,
	    "BR": 2476,
	    "TR": 1476,
	    "CN": 146,
	    "CA": 134,
	    "BD": 100
	};
	

	/*
	 * FULL CALENDAR JS
	 */
	
	// Load Calendar dependency then setup calendar
	loadScript("js/plugin/fullcalendar/jquery.fullcalendar.min.js", setupCalendar);
	var _event=[];
	var _render=false;
	function setupCalendar() {
	    if ($("#calendar").length) {
	        var date = new Date();
	        var d = date.getDate();
	        var m = date.getMonth();
	        var y = date.getFullYear();

	        var calendar = $('#calendar').fullCalendar({
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
             	monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
             	dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles','Jueves', 'Viernes', 'Sábado'],
             	dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
	            selectable: true,
	            editable: true,
	            minTime: '06:00:00',
	            maxtime: '20:00:00',
	            defaultView: 'agendaWeek',
	            slotDuration: '00:20:00',
	            firstHour:8,
	            header: {
	                left: 'title',
	                center: 'prev, next, today',
	                right: 'month, agendaWeek, agenDay'
	            },
	            select: function (start, end, allDay) {
	            	var _endtime = $.fullCalendar.formatDate(end,'h:mm tt');
          			var _starttime = $.fullCalendar.formatDate(start,'ddd, MMM d, h:mm tt');
          			var _cuando = _starttime + ' - ' + _endtime;
	                $('#no_agendamiento').val(_cuando);
	                $('#no_ini_agenda').val(start);
	                $('#no_fin_agenda').val(end);
	                $('#myModalLabel').html("Agendar Cita / Evento");
	                $('#bto_enviar').attr({'disabled':false});
	    			$('#bto_editar').attr({'disabled':true});
	    			$('#bto_eliminar').attr({'disabled':true});
	    			$('#no_nom_agenda').val('');
	    			$('textarea[name="no_des_agenda"]').val('');
	    			$('input[name="no_ind_confirma"]').prop('checked', false);
		         	$('#remoteModal').modal();
	                calendar.fullCalendar('unselect');
	            },
	            events: function(start, end, timezone, callback) {
			        $.ajax({
			            dataType:"json",
                		data:"dTabla=sys_agenda&cCase=consultaAgenda",
                		url: "modules/sistema/controller/sistemaJsonController.php",
			            success: function(data) {
			                var events = [];_render=true;
			                calendar.fullCalendar( 'removeEvents');
			                $.each(data, function(k,v){
			                	calendar.fullCalendar('renderEvent', {
						            id: v.cod_agenda,
			                        title: v.nom_evento_agenda+'|'+v.des_evento_agenda,
			                        start: new Date(v.start_evento_agenda),
			                        end: new Date(v.end_evento_agenda),
			                        allDay: false,
			                        cod_empresa: v.cod_empresa,
			                        ind_confirma: v.ind_confirmado,
		            				className: ["event", v.color_agenda]
						        },true);
			                });
			                calendar.fullCalendar('rerenderEvents');
			            }
			        });
			    },
	            eventRender: function (event, element, icon) {
	                element.attr('href', 'javascript:void(0);');
	                _event=event;
	                console.log(event);
			        element.click(function() {
			        	var _endtime = $.fullCalendar.formatDate(event.end,'h:mm tt');
          				var _starttime = $.fullCalendar.formatDate(event.start,'ddd, MMM d, h:mm tt');
          				var _cuando = _starttime + ' - ' + _endtime;
          				var result = event.title.split('|');
			        	$('#myModalLabel').html("Agendar Cita / Evento");
		        	 	$('#no_agendamiento').val(_cuando);
	                	$('#no_ini_agenda').val(event.start);
	                	$('#no_fin_agenda').val(event.end);
                	 	$('#no_nom_agenda').val(result[0]);
		    			$('textarea[name="no_des_agenda"]').val(result[1]);
		    			$('#no_cod_agenda').val(event.id);
		    			$('#bto_enviar').attr({'disabled':true});
		    			$('#bto_editar').attr({'disabled':false});
		    			$('#bto_eliminar').attr({'disabled':false});
		    			$('#no_cod_empresa').select2('destroy');
		    			$('#no_cod_empresa option[value="'+event.cod_empresa+'"]').prop('selected',true);
		    			if( event.ind_confirma==1){$('input[name="no_ind_confirma"]').prop('checked', true);}else{$('input[name="no_ind_confirma"]').prop('checked', false);};
		    			$('#no_cod_empresa').select2();
		         		$('#remoteModal').modal();
			        });
	            },
	            eventResize: function(event, element, icon) {
	            	$.ajax({
			            dataType:"json",
                		data:"dTabla=sys_agenda&cCase=edita_drag_agenda&start_evento_agenda="+event.start+"&end_evento_agenda="+event.end+"&cod_agenda="+event.id,
                		url: "modules/sistema/controller/sistemaJsonController.php",
			            success: function(data) {
			            }
				    });
	            },
	            eventDrop: function (event, element, icon) {
	            	$.ajax({
			            dataType:"json",
                		data:"dTabla=sys_agenda&cCase=edita_drag_agenda&start_evento_agenda="+event.start+"&end_evento_agenda="+event.end+"&cod_agenda="+event.id,
                		url: "modules/sistema/controller/sistemaJsonController.php",
			            success: function(data) {
			            	//console.log(data);
			            }
				    });
		 		}
	        });

	    };

	    $('#bto_enviar').on('click', function(e){
		    e.preventDefault();
		    doSubmit();
	  	});

	    $('#bto_editar').on('click', function(e){
		    e.preventDefault();
		    doEdit();
	  	});

	  	$('#bto_eliminar').on('click', function(e){
		    e.preventDefault();
		    doDelete();
	  	});

	  	function doSubmit(){
		    $("#remoteModal").modal('hide');
		    var _start 	  = $('#no_ini_agenda').val();
		    var _end   	  = $('#no_fin_agenda').val();
		    var _name     = $('#no_nom_agenda').val();
		    var _des      = $('textarea[name="no_des_agenda"]').val();
		    var _confirma = $('input[name="no_ind_confirma"]').prop('checked') ? 1 : 0;
		    var _color    = _confirma==1 ? "bg-color-blue" : "bg-color-darken";
		    var _opt      = $('#no_cod_empresa').find('option:selected');
		    var _codEmp   = _opt.val();
		    $.ajax({
                type: "POST",
                dataType: "json",
                data:"dTabla=sys_agenda&cCase=nueva_agenda&nom_evento_agenda="+_name+"&des_evento_agenda="+_des+"&start_evento_agenda="+_start+"&end_evento_agenda="+_end+"&ind_confirmado="+_confirma+"&color="+_color+"&cod_empresa="+_codEmp,
                url: "modules/sistema/controller/sistemaJsonController.php",
                success : function(data) {
			        $("#calendar").fullCalendar('renderEvent', {
			        	id:data.row,
			            title: _name+' | '+_des,
			            start: new Date(_start),
			            end: new Date(_end),
			            allDay: false,
		            	className: ["event", _color]
			        },true);
			    }
		     });
   		}

   		function doEdit(){

		    $("#remoteModal").modal('hide');
		    var _codAge   = $('#no_cod_agenda').val();
		    console.log(_codAge);
		    var _start 	  = $('#no_ini_agenda').val();
		    var _end   	  = $('#no_fin_agenda').val();
		    var _name     = $('#no_nom_agenda').val();
		    var _des      = $('textarea[name="no_des_agenda"]').val();
		    var _confirma = $('input[name="no_ind_confirma"]').prop('checked') ? 1 : 0;
		    var _color    = _confirma==1 ? "bg-color-blue" : "bg-color-darken";
		    var _opt      = $('#no_cod_empresa').find('option:selected');
		    var _codEmp   = _opt.val();
		    $.ajax({
                type: "POST",
                dataType: "json",
                data:"dTabla=sys_agenda&cCase=edita_agenda&cod_agenda="+_codAge+"&nom_evento_agenda="+_name+"&des_evento_agenda="+_des+"&start_evento_agenda="+_start+"&end_evento_agenda="+_end+"&ind_confirmado="+_confirma+"&color="+_color+"&cod_empresa="+_codEmp,
                url: "modules/sistema/controller/sistemaJsonController.php",
                success : function(data) {
                	console.log(data);
		    		$('#calendar').fullCalendar("refetchEvents");
			    }
		     });
   		}

   		function doDelete(){
		    $("#remoteModal").modal('hide');
		    var _codAge   = $('#no_cod_agenda').val();
		    $.ajax({
                type: "POST",
                dataType: "json",
                data:"dTabla=sys_agenda&cCase=elimina_agenda&cod_agenda="+_codAge,
                url: "modules/sistema/controller/sistemaJsonController.php",
                success : function(data) {
                	$('#calendar').fullCalendar("refetchEvents");
			    }
		     });
   		}

	    /* hide default buttons */
	    $('.fc-header-right, .fc-header-center').hide();

	}

	// calendar prev
	$('#calendar-buttons #btn-prev').click(function () {
	    $('.fc-button-prev').click();
	    return false;
	});

	// calendar next
	$('#calendar-buttons #btn-next').click(function () {
	    $('.fc-button-next').click();
	    return false;
	});

	// calendar today
	$('#calendar-buttons #btn-today').click(function () {
	    $('.fc-button-today').click();
	    return false;
	});

	// calendar month
	$('#mt').click(function () {
	    $('#calendar').fullCalendar('changeView', 'month');
	});

	// calendar agenda week
	$('#ag').click(function () {
	    $('#calendar').fullCalendar('changeView', 'agendaWeek');
	});

	// calendar agenda day
	$('#td').click(function () {
	    $('#calendar').fullCalendar('changeView', 'agendaDay');
	});
	/*
	 * CHAT
	 */
	
	if ($('#chat-container').length) {
		$.filter_input = $('#filter-chat-list');
		$.chat_users_container = $('#chat-container > .chat-list-body')
		$.chat_users = $('#chat-users')
		$.chat_list_btn = $('#chat-container > .chat-list-open-close');
		$.chat_body = $('#chat-body');
		
		/*
		 * LIST FILTER (CHAT)
		 */
		
		// custom css expression for a case-insensitive contains()
		jQuery.expr[':'].Contains = function (a, i, m) {
			return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
		};
	
		function listFilter(list) { // header is any element, list is an unordered list
			// create and add the filter form to the header
		
			$.filter_input.change(function () {
				var filter = $(this).val();
				if (filter) {
					// this finds all links in a list that contain the input,
					// and hide the ones not containing the input while showing the ones that do
					$.chat_users.find("a:not(:Contains(" + filter + "))").parent().slideUp();
					$.chat_users.find("a:Contains(" + filter + ")").parent().slideDown();
				} else {
					$.chat_users.find("li").slideDown();
				}
				return false;
			}).keyup(function () {
				// fire the above change event after every letter
				$(this).change();
		
			});
		
		}
	
		// on dom ready
		listFilter($.chat_users);
	
	// open chat list
		$.chat_list_btn.click(function () {
			$(this).parent('#chat-container').toggleClass('open');
		})
	
		$.chat_body.animate({
			scrollTop: $.chat_body[0].scrollHeight
		}, 500);
	}
