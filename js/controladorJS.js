"use strict";
(function () {

    //Nodos
    var calendar2 = $('#calendar2');
    var calProfesores = $('#calProfesores');
    var calAulas = $('#calAulas');
    var divLogin = $("#divLogin");
    var divIndex = $("#divIndex");
    var divTutoria = $("#divTutoria");
    var divProfesores = $("#divProfesores");
    var divAulas = $("#divAulas");
    var divNombre = $("#divNombre");
    var divAula = $("#divAula");
    var login = $("#login");
    var clave = $("#clave");
    var btlogout = $("#btlogout");
    var bthorario = $(".bthorario");
    var bttutoria = $(".bttutoria");
    var btprofesores = $(".btprofesores");
    var btaulas = $(".btaulas");
    var btaddprofesor = $(".btaddprofesor");
    var btaddaula = $(".btaddaula");
    var nombre = "";
    var aula = "";


    //---------------------------------Horario---------------------------------//

    bthorario.on("click", function () {
        ocultarAlerts();
        calendar.fullCalendar('removeEvents');
        divLogin.hide();
        divTutoria.hide();
        divIndex.show();
        btlogout.show();
        cargarHorario();
        calendar2.fullCalendar('removeEvents');
    });

    var anadirRecreo = function (calend) {
        calend = typeof calend !== 'undefined' ? calend : calendar;
        for (var i = 0; i < 6; i++) {
            calend.fullCalendar('renderEvent',
                    {
                        title: "Break",
                        start: new Date(y, m, d - day + i, 11, 0),
                        end: new Date(y, m, d - day + i, 12, 0),
                        allDay: false,
                        color: '#00AA00'
                    }, true);
        }

    }

    var anadirAula = function (id, titulo, dia, inicio, fin) {
        calendar.fullCalendar('renderEvent',
                {
                    id: id,
                    title: titulo,
                    start: new Date(y, m, d - day + dia, inicio, 0),
                    end: new Date(y, m, d - day + dia, fin, 0),
                    allDay: false,
                    color: '#D9776F'
                }, true);
    }

    var cargarHorario = function () {
        anadirRecreo();
        $.ajax({
            url: "index.php?action=get&target=horario",
            success: function (respuesta) {
                if (respuesta.clases) {
                    var clases = respuesta.clases;
                    for (var i = 0; i < clases.length; i++) {
                        anadirAula(
                                clases[i].id,
                                clases[i].aula + " - " + clases[i].asignatura,
                                parseInt(clases[i].dia),
                                parseInt(clases[i].inicio),
                                parseInt(clases[i].fin)
                                );
                    }
                } else {
                    alert("No hay asignaturas");
                }
            }
        });
    }

    //tutoria

    var anadirTutoria = function () {
        calendar2.fullCalendar({
            defaultView: 'agendaWeek',
            selectable: true,
            selectHelper: true,
            weekNumberCalculation: false,
            columnFormat: {
                month: 'ddd',
                week: 'ddd',
                day: 'dddd M/d'
            },
            axisFormat: 'HH:mm',
            timeFormat: {
                agenda: 'H:mm{ - h:mm}'
            },
            select: function (start, end, allDay) {
                
                $.ajax({
                    url: "index.php?action=get&target=profesoresselect",
                    success: function(respuesta){
                        if (respuesta.select) {
                            
                            var profesor;
                            var clase;
                            bootbox.dialog({
                                    title: "New Class",
                                    message: '<div class="row">  ' +
                                        '<div class="col-md-12"> ' +
                                        '<form class="form-horizontal"> ' +
                                        '<div class="form-group"> ' +
                                        '<label class="col-md-4 control-label" for="nombreClase">Class Name &nbsp;</label> ' +
                                        '<div class="col-md-4"> ' +
                                        '<input id="nombreClase" name="nombreClase" type="text" class="form-control input-md"> ' +
                                        '</div> ' +
                                        '<div class="form-group"> ' +
                                        '<label class="col-md-4 control-label" for="nombreClase">Teacher Name &nbsp;</label> ' +
                                        '<div class="col-md-4" id="select2">'+
                                        '</div> </div>' +
                                        '</form> </div>  </div>',
                                buttons: {
                                    success: {
                                        label: "Save",
                                        className: "btn-success",
                                        callback: function () {
                                            clase = $('#nombreClase').val();
                                            profesor = $("[name='profesor']").find(":selected").text();

                                            $.ajax({
                                                url: "index.php?action=set&target=clase2&profesor=" + encodeURI(profesor) + "&clase=" + encodeURI(clase)
                                                        + "&inicio=" + encodeURI(start) + "&fin=" + encodeURI(end) + "&aula=" + encodeURI(aula),
                                                success: function (respuesta) {
                                                    
                                                    Example2.init({
                                                        "selector": ".bb-alert2"
                                                    });

                                                    if(respuesta.ok){
                                                        Example2.show("<b>"+profesor+"</b> ha añadido la clase <b>" + clase + "</b> en el aula <b>" + aula + "</b>", "alert-success");
                                                        calendar2.fullCalendar('renderEvent',
                                                        {
                                                            id: respuesta.id,
                                                            title: profesor + " - " + clase,
                                                            start: start,
                                                            end: end,
                                                            allDay: allDay
                                                        },
                                                        true // make the event "stick"
                                                        );
                                                    }else{
                                                        Example2.show("Este profesor no tiene tutoria", "alert-danger");
                                                    }
                                                            
                                                }
                                            });

                                        }
                                    }
                                }
                            }

                        );
                        $("#select2").html(respuesta.select);
                        calendar.fullCalendar('unselect');
                        } else {
                            bootbox.alert("No hay profesores");
                        }
                    }
                });
            },
            eventClick: function(calEvent) {
                bootbox.confirm("Deleting... are you sure?", function(result) {

                    if (result) {
                        $.ajax({
                           url: "index.php?action=delete&target=clase&id="+calEvent.id,
                           success: function(respuesta){
                               
                                Example2.init({
                                    "selector": ".bb-alert2"
                                });
                               calendar2.fullCalendar('removeEvents', calEvent.id);
                               Example2.show("Se ha borrado la clase correctamente", "alert-success");
                           }
                       });
                   }
                });
            },
            editable: true,
            disableDragging: true,
            disableResizing: true,
            weekends: false,
            allDaySlot: false,
            minTime: "08:00:00",
            maxTime: "15:00:00",
            duration: '01:00:00',
            slotMinutes: 60,
            slotDuration: '01:00:00',
            slotLabelInterval: '01:00:00'
        });
    }
    anadirTutoria();

    var anadirProfesor = function (id, titulo, dia, inicio, fin) {
        calendar2.fullCalendar('renderEvent',
                {
                    id: id,
                    title: titulo,
                    start: new Date(y, m, d - day + dia, inicio, 0),
                    end: new Date(y, m, d - day + dia, fin, 0),
                    allDay: false,
                    color: '#D9776F'
                }, true);
    }

    var setAula = function () {
        divAula.html("classroom <h1 style='display: inline-block'>" + aula + "</h1>");
    }

    var cargarTutoria = function () {
        if (aula != "") {
            $.ajax({
                url: "index.php?action=get&target=tutoria&aula=" + aula,
                success: function (respuesta) {
                    if (respuesta.profesores) {
                        var profesores = respuesta.profesores;
                        for (var i = 0; i < profesores.length; i++) {
                            anadirProfesor(
                                    profesores[i].id,
                                    profesores[i].profesor + " - " + profesores[i].asignatura,
                                    parseInt(profesores[i].dia),
                                    parseInt(profesores[i].inicio),
                                    parseInt(profesores[i].fin)
                                    );
                        }
                        anadirRecreo(calendar2);
                    } else {
                        bootbox.alert("No hay asignaturas");
                    }
                }
            });
        }
    }

    var getAula = function () {
        $.ajax({
            url: "index.php?action=get&target=aula",
            success: function (respuesta) {
                if (respuesta.aula) {
                    aula = respuesta.aula;
                } else {
                    aula = "Sin Tutoria";
                }
                setAula();
                cargarTutoria();
            }
        });
    }

    bttutoria.on("click", function () {
        ocultarAlerts();
        calendar.fullCalendar('removeEvents');
        calendar2.fullCalendar('removeEvents');
        divLogin.hide();
        divIndex.hide();
        divTutoria.show();
        btlogout.show();
        getAula();
    });


    //Admin

    //Teachers
    
    btaddprofesor.on("click", function () {
        Example3.init({
            "selector": ".bb-alert3"
        });
        bootbox.dialog({
                title: "New Teacher",
                message: '<div class="row">  ' +
                    '<div class="col-md-12"> ' +
                    '<form class="form-horizontal"> ' +
                    '<div class="form-group"> ' +
                    '<label class="col-md-4 control-label" for="loginProfesor">Login &nbsp;</label> ' +
                    '<div class="col-md-4"> ' +
                    '<input id="loginProfesor" name="loginProfesor" type="text" class="form-control input-md"> ' +
                    '</div> ' +
                    '<div class="form-group"> ' +
                    '<label class="col-md-4 control-label" for="claveProfesor">Password &nbsp;</label> ' +
                    '<div class="col-md-4"> ' +
                    '<input id="claveProfesor" name="claveProfesor" type="password" class="form-control input-md"> ' +
                    '</div> ' +
                    '</form> </div>  </div>',
                buttons: {
                    success: {
                        label: "Save",
                        className: "btn-success",
                        callback: function () {
                            login = $('#loginProfesor').val();
                            clave = $('#claveProfesor').val();

                            $.ajax({
                                url: "index.php?action=add&target=profesor&login="+login+"&clave="+clave,
                                success: function(respuesta){
                                    if(respuesta.profe){
                                        Example3.show("Se ha añadido el profesor <b>" + login, "alert-success");
                                        borrarEventos();
                                        divAulas.hide();
                                        anadirProfesores();
                                    }else{
                                        bootbox.alert("No se ha podido crear");
                                    }
                                }
                            });
                        }
                    }
                }
            }
        );
    });
    
    var anadirHorarios = function (profesor) {

        calProfesores.append("<div class='row eventos'><div class='span12'><div style='text-align: right'><h3>" + profesor.login + "</h3></div><div class='widget widget-nopad'><div class='widget-content'><div id='" + profesor.login + "'></div></div></div></div></div>");
        var cal = $("#" + profesor.login);
        
        var aula;
        var clase;
        cal.fullCalendar({
            defaultView: 'agendaWeek',
            selectable: true,
            selectHelper: true,
            weekNumberCalculation: false,
            columnFormat: {
                month: 'ddd',
                week: 'ddd',
                day: 'dddd M/d'
            },
            axisFormat: 'HH:mm',
            timeFormat: {
                agenda: 'H:mm{ - h:mm}'
            },
            select: function (start, end, allDay) {
                $.ajax({
                        url: "index.php?action=get&target=aulasselect",
                        success: function(respuesta){
                            
                        Example3.init({
                            "selector": ".bb-alert3"
                        });
                            if (respuesta.select) {
                                bootbox.dialog({
                                        title: "New Class",
                                        message: '<div class="row">  ' +
                                            '<div class="col-md-12"> ' +
                                            '<form class="form-horizontal"> ' +
                                            '<div class="form-group"> ' +
                                            '<label class="col-md-4 control-label" for="nombreClase">Class Name &nbsp;</label> ' +
                                            '<div class="col-md-4"> ' +
                                            '<input id="nombreClase" name="nombreClase" type="text" class="form-control input-md"> ' +
                                            '</div> ' +
                                            '<div class="form-group"> ' +
                                            '<label class="col-md-4 control-label" for="nombreClase">Classroom Number &nbsp;</label> ' +
                                            '<div class="col-md-4" id="select1">'+
                                            '</div> </div>' +
                                            '</form> </div>  </div>',
                                        buttons: {
                                            success: {
                                                label: "Save",
                                                className: "btn-success",
                                                callback: function () {
                                                    clase = $('#nombreClase').val();
                                                    aula = $("[name='aula']").find(":selected").text();

                                                    $.ajax({
                                                        url: "index.php?action=set&target=clase2&aula=" + encodeURI(aula) + "&clase=" + encodeURI(clase)
                                                        + "&inicio=" + encodeURI(start)+ "&fin=" + encodeURI(end)+ "&profesor=" + encodeURI(profesor.login),
                                                        success: function(respuesta){
                                                            cal.fullCalendar('renderEvent',
                                                            {
                                                                id: respuesta.id,
                                                                title: aula+" - "+clase,
                                                                start: start,
                                                                end: end,
                                                                allDay: allDay
                                                            },
                                                            true // make the event "stick"
                                                            );
                                                        }
                                                    });


                                                Example3.show("Se ha añadido la clase <b>" + clase + "</b> en el aula <b>" + aula + "</b>", "alert-success");
                                                }
                                            }
                                        }
                                    }

                            );
                            $("#select1").html(respuesta.select);

                            cal.fullCalendar('unselect');
                            } else {
                                bootbox.alert("No hay aulas");
                            }
                        }
                    });
            },
            eventClick: function(calEvent) {
                bootbox.confirm("Deleting... are you sure?", function(result) {

                    if (result) {
                        $.ajax({
                           url: "index.php?action=delete&target=clase&id="+calEvent.id,
                           success: function(respuesta){
                               
                                Example3.init({
                                    "selector": ".bb-alert3"
                                });
                               cal.fullCalendar('removeEvents', calEvent.id);
                               Example3.show("Se ha borrado la clase correctamente", "alert-success");
                               event.preventDefault();
                           }
                       });
                   }
                });
            },
            editable: true,
            disableDragging: true,
            disableResizing: true,
            weekends: false,
            allDaySlot: false,
            minTime: "08:00:00",
            maxTime: "15:00:00",
            duration: '01:00:00',
            slotMinutes: 60,
            slotDuration: '01:00:00',
            slotLabelInterval: '01:00:00'
        });

        anadirRecreo(cal);

        var anadirAula2 = function (id, titulo, dia, inicio, fin) {
            cal.fullCalendar('renderEvent',
                    {
                        id: id,
                        title: titulo,
                        start: new Date(y, m, d - day + dia, inicio, 0),
                        end: new Date(y, m, d - day + dia, fin, 0),
                        allDay: false,
                        color: '#D9776F'
                    }, true);
        }

        $.ajax({
            url: "index.php?action=get&target=horarioadmin&profesor=" + profesor.login,
            success: function (respuesta) {
                if (respuesta.clases) {
                    var clases = respuesta.clases;
                    for (var i = 0; i < clases.length; i++) {
                        anadirAula2(
                                clases[i].id,
                                clases[i].aula + " - " + clases[i].asignatura,
                                parseInt(clases[i].dia),
                                parseInt(clases[i].inicio),
                                parseInt(clases[i].fin)
                                );
                    }
                } else {
                    alert("No hay asignaturas");
                }
            }
        });
    }


    var anadirProfesores = function () {
        $.ajax({
            url: "index.php?action=get&target=profesores",
            success: function (respuesta) {
                if (respuesta.profesores) {
                    var profesores = respuesta.profesores;
                    for (var i = 0; i < profesores.length; i++) {
                        anadirHorarios(profesores[i]);
                    }
                } else {
                    alert("No hay asignaturas");
                }
            }
        });
    }

    var borrarEventos = function () {
        $(".eventos").remove();
    }

    btprofesores.on("click", function () {
        ocultarAlerts();
        calendar.fullCalendar('removeEvents');
        calendar2.fullCalendar('removeEvents');
        divLogin.hide();
        divTutoria.hide();
        divIndex.hide();
        btlogout.show();
        divProfesores.show();
        borrarEventos();
        divAulas.hide();
        anadirProfesores();
    });


    //Aulas
    
    
    
    btaddaula.on("click", function () {
        Example4.init({
            "selector": ".bb-alert4"
        });
        var numero;
        var tutor;
        $.ajax({
            url: "index.php?action=get&target=profesoresselect",
            success: function(respuesta){
                if (respuesta.select) {
                    bootbox.dialog({
                            title: "New Classroom",
                            message: '<div class="row">  ' +
                                '<div class="col-md-12"> ' +
                                '<form class="form-horizontal"> ' +
                                '<div class="form-group"> ' +
                                '<label class="col-md-4 control-label" for="nombreClase">Number &nbsp;</label> ' +
                                '<div class="col-md-4"> ' +
                                '<input id="nombreClase" name="nombreClase" type="number" class="form-control input-md"> ' +
                                '</div> ' +
                                '<div class="form-group"> ' +
                                '<label class="col-md-4 control-label" for="nombreClase">Teacher Name &nbsp;</label> ' +
                                '<div class="col-md-4" id="select3">'+
                                '</div> </div>' +
                                '</form> </div>  </div>',
                        buttons: {
                            success: {
                                label: "Save",
                                className: "btn-success",
                                callback: function () {
                                    numero = $('#nombreClase').val();
                                    tutor = $("[name='profesor']").find(":selected").text();

                                    $.ajax({
                                        url: "index.php?action=add&target=aula&numero="+numero+"&tutor="+tutor,
                                        success: function(respuesta){
                                            if(respuesta.aula){
                                                Example4.show("Se ha añadido el aula <b>" + numero, "alert-success");
                                                borrarEventos();
                                                divProfesores.hide();
                                                anadirAulas();
                                            }else{
                                                bootbox.alert("No se ha podido crear");
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }

                );
                $("#select3").html(respuesta.select);
                } else {
                    bootbox.alert("No hay profesores");
                }
            }
        });
    });


    var anadirTutorias = function (aula) {

        calAulas.append("<div class='row eventos'><div class='span12'><div style='text-align: right'><h3>" + aula.numero + "</h3></div><div class='widget widget-nopad'><div class='widget-content'><div id='" + aula.numero + "'></div></div></div></div></div>");
        var cal = $("#" + aula.numero);

        cal.fullCalendar({
            defaultView: 'agendaWeek',
            selectable: true,
            selectHelper: true,
            weekNumberCalculation: false,
            columnFormat: {
                month: 'ddd',
                week: 'ddd',
                day: 'dddd M/d'
            },
            axisFormat: 'HH:mm',
            timeFormat: {
                agenda: 'H:mm{ - h:mm}'
            },
            select: function (start, end, allDay) {
                
                var profesor;
                var clase;
                $.ajax({
                    url: "index.php?action=get&target=profesoresselect",
                    success: function(respuesta){
                        if (respuesta.select) {
                            bootbox.dialog({
                                    title: "New Class",
                                    message: '<div class="row">  ' +
                                        '<div class="col-md-12"> ' +
                                        '<form class="form-horizontal"> ' +
                                        '<div class="form-group"> ' +
                                        '<label class="col-md-4 control-label" for="nombreClase">Class Name &nbsp;</label> ' +
                                        '<div class="col-md-4"> ' +
                                        '<input id="nombreClase" name="nombreClase" type="text" class="form-control input-md"> ' +
                                        '</div> ' +
                                        '<div class="form-group"> ' +
                                        '<label class="col-md-4 control-label" for="nombreClase">Teacher Name &nbsp;</label> ' +
                                        '<div class="col-md-4" id="select2">'+
                                        '</div> </div>' +
                                        '</form> </div>  </div>',
                                buttons: {
                                    success: {
                                        label: "Save",
                                        className: "btn-success",
                                        callback: function () {
                                            clase = $('#nombreClase').val();
                                            profesor = $("[name='profesor']").find(":selected").text();

                                            $.ajax({
                                                url: "index.php?action=set&target=clase2&profesor=" + encodeURI(profesor) + "&clase=" + encodeURI(clase)
                                                        + "&inicio=" + encodeURI(start) + "&fin=" + encodeURI(end) + "&aula=" + encodeURI(aula.numero),
                                                success: function (respuesta) {
                                                        Example4.init({
                                                            "selector": ".bb-alert4"
                                                        });
                                                        cal.fullCalendar('renderEvent',
                                                        {
                                                            id: respuesta.id,
                                                            title: profesor + " - " + clase,
                                                            start: start,
                                                            end: end,
                                                            allDay: allDay
                                                        },
                                                        true // make the event "stick"
                                                        );
                                                }
                                            });


                                        Example4.show("<b>"+profesor+"</b> ha añadido la clase <b>" + clase + "</b> en el aula <b>" + aula.numero + "</b>", "alert-success");
                                        }
                                    }
                                }
                            }

                        );
                        $("#select2").html(respuesta.select);
                        cal.fullCalendar('unselect');
                        } else {
                            bootbox.alert("No hay profesores");
                        }
                    }
                });
            },
            eventClick: function(calEvent) {
                bootbox.confirm("Deleting... are you sure?", function(result) {

                    if (result) {
                        $.ajax({
                           url: "index.php?action=delete&target=clase&id="+calEvent.id,
                           success: function(respuesta){
                               
                                Example4.init({
                                    "selector": ".bb-alert4"
                                });
                               cal.fullCalendar('removeEvents', calEvent.id);
                               Example4.show("Se ha borrado la clase correctamente", "alert-success");
                           }
                       });
                   }
                });
            },
            editable: true,
            disableDragging: true,
            disableResizing: true,
            weekends: false,
            allDaySlot: false,
            minTime: "08:00:00",
            maxTime: "15:00:00",
            duration: '01:00:00',
            slotMinutes: 60,
            slotDuration: '01:00:00',
            slotLabelInterval: '01:00:00'
        });

        anadirRecreo(cal);

        var anadirAula2 = function (id, titulo, dia, inicio, fin) {
            cal.fullCalendar('renderEvent',
                    {
                        id: id,
                        title: titulo,
                        start: new Date(y, m, d - day + dia, inicio, 0),
                        end: new Date(y, m, d - day + dia, fin, 0),
                        allDay: false,
                        color: '#D9776F'
                    }, true);
        }

        $.ajax({
            url: "index.php?action=get&target=tutoriaadmin&aula=" + aula.numero,
            success: function (respuesta) {
                if (respuesta.profesores) {
                    var profesores = respuesta.profesores;
                    for (var i = 0; i < profesores.length; i++) {
                        anadirAula2(
                                profesores[i].id,
                                profesores[i].profesor + " - " + profesores[i].asignatura,
                                parseInt(profesores[i].dia),
                                parseInt(profesores[i].inicio),
                                parseInt(profesores[i].fin)
                        );
                    }
                } else {
                    alert("No hay profesores");
                }
            }
        });
    }

    var anadirAulas = function () {
        $.ajax({
            url: "index.php?action=get&target=aulas",
            success: function (respuesta) {
                if (respuesta.aulas) {
                    var aulas = respuesta.aulas;
                    for (var i = 0; i < aulas.length; i++) {
                        anadirTutorias(aulas[i]);
                    }
                } else {
                    alert("No hay asignaturas");
                }
            }
        });
    }

    btaulas.on("click", function () {
        ocultarAlerts();
        calendar.fullCalendar('removeEvents');
        calendar2.fullCalendar('removeEvents');
        divLogin.hide();
        divTutoria.hide();
        divIndex.hide();
        btlogout.show();
        divAulas.show();
        borrarEventos();
        divProfesores.hide();
        anadirAulas();
    });



    //Login
    var getNombre = function () {
        $.ajax({
            url: "index.php?action=get&target=nombre",
            success: function (respuesta) {
                if (respuesta.nombre) {
                    nombre = respuesta.nombre;
                } else {
                    nombre = "Sin nombre";
                }
                divNombre.html("teacher <h1 style='display: inline-block'>" + nombre + "</h1>");
            }
        });
    }

    var loguear = function () {
        ocultarAlerts();
        divTutoria.hide();
        divProfesores.hide();
        divAulas.hide();
        divLogin.hide();
        btlogout.show();
        divIndex.show();
        getNombre();
        cargarHorario();
    };

    var loguearAdmin = function () {
        ocultarAlerts();
        divTutoria.hide();
        divLogin.hide();
        btlogout.show();
        divIndex.hide();
        divAulas.hide();
        divProfesores.show();
        anadirProfesores();
    };

    var desloguear = function () {
        ocultarAlerts();
        calendar.fullCalendar('removeEvents');
        calendar2.fullCalendar('removeEvents');
        divProfesores.hide();
        btlogout.hide();
        divTutoria.hide();
        divIndex.hide();
        divProfesores.hide();
        divAulas.hide();
        divLogin.show();
        divNombre.text("");
        borrarEventos();
    };

    var isLogueado = function (respuesta) {
        if (respuesta.login) {
            if (respuesta.admin == 1) {
                loguearAdmin();
            } else {
                loguear();
            }
        } else {
            desloguear();
            divNombre.text("");
        }
    };
    $.ajax({
        url: "index.php?action=is&target=logueado",
        success: isLogueado
    });

    $("#btlogin").on("click", function () {
        $.ajax({
            url: "index.php?action=login&target=user&login=" + encodeURI(login.val()) + "&clave=" + encodeURI(clave.val()),
            success: function (respuesta) {
                if (respuesta.login) {
                    if (respuesta.admin == 1) {
                        loguearAdmin();
                    } else {
                        loguear();
                    }
                    if (!$("#recordar").is(':checked')) {
                        login.val("");
                        clave.val("");
                    }
                } else {
                    
                    Example0.init({
                        "selector": ".bb-alert0"
                    });
                    Example0.show("Login incorrecto", "alert-danger");
                }
            }
        });
    });

    btlogout.on("click", function () {
        $.ajax({
            url: "index.php?action=do&target=logout",
            success: function (respuesta) {
                if (!respuesta.login) {
                    Example0.init({
                        "selector": ".bb-alert0"
                    });
                    desloguear();
                    Example0.show("Hasta luego!", "alert-info");
                }
            }
        });
    });

})();