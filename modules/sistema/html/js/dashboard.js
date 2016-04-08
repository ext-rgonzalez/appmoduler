 
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
	 * RUN PAGE GRAPHS
	 */
	
//	// Load FLOAT dependencies (related to page)
//	loadScript("js/plugin/flot/jquery.flot.cust.js", loadFlotResize);
//	
//	function loadFlotResize() {
//	    loadScript("js/plugin/flot/jquery.flot.resize.js", loadFlotToolTip);
//	}
	
//	function loadFlotToolTip() {
//	    loadScript("js/plugin/flot/jquery.flot.tooltip.js", generatePageGraphs);
//	}
	
//	function generatePageGraphs() {
//	
//	    /* TAB 1: UPDATING CHART */
//	    // For the demo we use generated data, but normally it would be coming from the server
//	
//	    var data = [],
//	        totalPoints = 200,
//	        $UpdatingChartColors = $("#updating-chart").css('color');
//	
//	    function getRandomData() {
//	        if (data.length > 0)
//	            data = data.slice(1);
//	
//	        // do a random walk
//	        while (data.length < totalPoints) {
//	            var prev = data.length > 0 ? data[data.length - 1] : 50;
//	            var y = prev + Math.random() * 10 - 5;
//	            if (y < 0)
//	                y = 0;
//	            if (y > 100)
//	                y = 100;
//	            data.push(y);
//	        }
//	
//	        // zip the generated y values with the x values
//	        var res = [];
//	        for (var i = 0; i < data.length; ++i)
//	            res.push([i, data[i]])
//	        return res;
//	    }
//	
//	    // setup control widget
//	    var updateInterval = 1500;
//	    $("#updating-chart").val(updateInterval).change(function () {
//	
//	        var v = $(this).val();
//	        if (v && !isNaN(+v)) {
//	            updateInterval = +v;
//	            $(this).val("" + updateInterval);
//	        }
//	
//	    });
//	
//	    // setup plot
//	    var options = {
//	        yaxis: {
//	            min: 0,
//	            max: 100
//	        },
//	        xaxis: {
//	            min: 0,
//	            max: 100
//	        },
//	        colors: [$UpdatingChartColors],
//	        series: {
//	            lines: {
//	                lineWidth: 1,
//	                fill: true,
//	                fillColor: {
//	                    colors: [{
//	                        opacity: 0.4
//	                    }, {
//	                        opacity: 0
//	                    }]
//	                },
//	                steps: false
//	
//	            }
//	        }
//	    };
	
	    //var plot = $.plot($("#updating-chart"), [getRandomData()], options);
	
//	    /* live switch */
//	    $('input[type="checkbox"]#start_interval').click(function () {
//	        if ($(this).prop('checked')) {
//	            $on = true;
//	            updateInterval = 1500;
//	            update();
//	        } else {
//	            clearInterval(updateInterval);
//	            $on = false;
//	        }
//	    });
//	
//	    function update() {
//	        if ($on == true) {
//	            plot.setData([getRandomData()]);
//	            plot.draw();
//	            setTimeout(update, updateInterval);
//	
//	        } else {
//	            clearInterval(updateInterval)
//	        }
//	
//	    }
//	
//	    var $on = false;
//	
//	    /*end updating chart*/
//	
//	    /* TAB 2: Social Network  */
//	
//	    $(function () {
//	        // jQuery Flot Chart
//	        var twitter = [
//	            [1, 27],
//	            [2, 34],
//	            [3, 51],
//	            [4, 48],
//	            [5, 55],
//	            [6, 65],
//	            [7, 61],
//	            [8, 70],
//	            [9, 65],
//	            [10, 75],
//	            [11, 57],
//	            [12, 59],
//	            [13, 62]
//	        ],
//	            facebook = [
//	                [1, 25],
//	                [2, 31],
//	                [3, 45],
//	                [4, 37],
//	                [5, 38],
//	                [6, 40],
//	                [7, 47],
//	                [8, 55],
//	                [9, 43],
//	                [10, 50],
//	                [11, 47],
//	                [12, 39],
//	                [13, 47]
//	            ],
//	            data = [{
//	                label: "Twitter",
//	                data: twitter,
//	                lines: {
//	                    show: true,
//	                    lineWidth: 1,
//	                    fill: true,
//	                    fillColor: {
//	                        colors: [{
//	                            opacity: 0.1
//	                        }, {
//	                            opacity: 0.13
//	                        }]
//	                    }
//	                },
//	                points: {
//	                    show: true
//	                }
//	            }, {
//	                label: "Facebook",
//	                data: facebook,
//	                lines: {
//	                    show: true,
//	                    lineWidth: 1,
//	                    fill: true,
//	                    fillColor: {
//	                        colors: [{
//	                            opacity: 0.1
//	                        }, {
//	                            opacity: 0.13
//	                        }]
//	                    }
//	                },
//	                points: {
//	                    show: true
//	                }
//	            }];
//	
//	        var options = {
//	            grid: {
//	                hoverable: true
//	            },
//	            colors: ["#568A89", "#3276B1"],
//	            tooltip: true,
//	            tooltipOpts: {
//	                //content : "Value <b>$x</b> Value <span>$y</span>",
//	                defaultTheme: false
//	            },
//	            xaxis: {
//	                ticks: [
//	                    [1, "JAN"],
//	                    [2, "FEB"],
//	                    [3, "MAR"],
//	                    [4, "APR"],
//	                    [5, "MAY"],
//	                    [6, "JUN"],
//	                    [7, "JUL"],
//	                    [8, "AUG"],
//	                    [9, "SEP"],
//	                    [10, "OCT"],
//	                    [11, "NOV"],
//	                    [12, "DEC"],
//	                    [13, "JAN+1"]
//	                ]
//	            },
//	            yaxes: {
//	
//	            }
//	        };
//	
//	        var plot3 = $.plot($("#statsChart"), data, options);
//	    });
//	
//	    // END TAB 2
//	
//	    // TAB THREE GRAPH //
//	    /* TAB 3: Revenew  */
//	
//	    $(function () {
//	
//	        var trgt = [
//	            [1354586000000, 153],
//	            [1364587000000, 658],
//	            [1374588000000, 198],
//	            [1384589000000, 663],
//	            [1394590000000, 801],
//	            [1404591000000, 1080],
//	            [1414592000000, 353],
//	            [1424593000000, 749],
//	            [1434594000000, 523],
//	            [1444595000000, 258],
//	            [1454596000000, 688],
//	            [1464597000000, 364]
//	        ],
//	            prft = [
//	                [1354586000000, 53],
//	                [1364587000000, 65],
//	                [1374588000000, 98],
//	                [1384589000000, 83],
//	                [1394590000000, 980],
//	                [1404591000000, 808],
//	                [1414592000000, 720],
//	                [1424593000000, 674],
//	                [1434594000000, 23],
//	                [1444595000000, 79],
//	                [1454596000000, 88],
//	                [1464597000000, 36]
//	            ],
//	            sgnups = [
//	                [1354586000000, 647],
//	                [1364587000000, 435],
//	                [1374588000000, 784],
//	                [1384589000000, 346],
//	                [1394590000000, 487],
//	                [1404591000000, 463],
//	                [1414592000000, 479],
//	                [1424593000000, 236],
//	                [1434594000000, 843],
//	                [1444595000000, 657],
//	                [1454596000000, 241],
//	                [1464597000000, 341]
//	            ],
//	            toggles = $("#rev-toggles"),
//	            target = $("#flotcontainer");
//	
//	        var data = [{
//	            label: "Target Profit",
//	            data: trgt,
//	            bars: {
//	                show: true,
//	                align: "center",
//	                barWidth: 30 * 30 * 60 * 1000 * 80
//	            }
//	        }, {
//	            label: "Actual Profit",
//	            data: prft,
//	            color: '#3276B1',
//	            lines: {
//	                show: true,
//	                lineWidth: 3
//	            },
//	            points: {
//	                show: true
//	            }
//	        }, {
//	            label: "Actual Signups",
//	            data: sgnups,
//	            color: '#71843F',
//	            lines: {
//	                show: true,
//	                lineWidth: 1
//	            },
//	            points: {
//	                show: true
//	            }
//	        }]
//	
//	        var options = {
//	            grid: {
//	                hoverable: true
//	            },
//	            tooltip: true,
//	            tooltipOpts: {
//	                //content: '%x - %y',
//	                //dateFormat: '%b %y',
//	                defaultTheme: false
//	            },
//	            xaxis: {
//	                mode: "time"
//	            },
//	            yaxes: {
//	                tickFormatter: function (val, axis) {
//	                    return "$" + val;
//	                },
//	                max: 1200
//	            }
//	
//	        };
//	
//	        plot2 = null;
//	
//	        function plotNow() {
//	            var d = [];
//	            toggles.find(':checkbox').each(function () {
//	                if ($(this).is(':checked')) {
//	                    d.push(data[$(this).attr("name").substr(4, 1)]);
//	                }
//	            });
//	            if (d.length > 0) {
//	                if (plot2) {
//	                    plot2.setData(d);
//	                    plot2.draw();
//	                } else {
//	                    plot2 = $.plot(target, d, options);
//	                }
//	            }
//	
//	        };
//	
//	        toggles.find(':checkbox').on('change', function () {
//	            plotNow();
//	        });
//	        plotNow()
//	
//	    });
//	
//	}
	
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
	
	// Load Map dependency 1 then call for dependency 2
	loadScript("js/plugin/vectormap/jquery-jvectormap-1.2.2.min.js", loadMapFile);
	
	// Load Map dependency 2 then rendeder Map
	function loadMapFile() {
	    loadScript("js/plugin/vectormap/jquery-jvectormap-world-mill-en.js", renderVectorMap);
	}
	
	function renderVectorMap() {
	    $('#vector-map').vectorMap({
	        map: 'world_mill_en',
	        backgroundColor: '#fff',
	        regionStyle: {
	            initial: {
	                fill: '#c4c4c4'
	            },
	            hover: {
	                "fill-opacity": 1
	            }
	        },
	        series: {
	            regions: [{
	                values: data_array,
	                scale: ['#85a8b6', '#4d7686'],
	                normalizeFunction: 'polynomial'
	            }]
	        },
	        onRegionLabelShow: function (e, el, code) {
	            if (typeof data_array[code] == 'undefined') {
	                e.preventDefault();
	            } else {
	                var countrylbl = data_array[code];
	                el.html(el.html() + ': ' + countrylbl + ' visits');
	            }
	        }
	    });
	}
	
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
