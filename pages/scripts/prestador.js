////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////              JS PRESTADOR                ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////   AMBITO: PRESTADOR.PHP      /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////


//variable global
var tabla;
var tabla_usuarios;


//funcion que se ejecuta al inicio (al cargar la vista por primera vez)
function init() {

    //Oculto el frm de registro de usuarios
    mostrarform_usuario(false);
    
    //Listo los prestadores
    listar_prestador();
    
    //Mostramos los permisos
    $.post("../../controladores/prestador.php?op=permisos&id= ", function (r) {
        $("#permisos").html(r);
    });
   
    
    //Asignar el codigo de usuario como contraseña al registrar 
    //por primera vez
    $("#cod_usuario").keyup(function () {

        if (!($(this).is('[readonly]'))) {

            var value = $(this).val();
            $("#password").val(value);
        }
    });


    //Si el checkbox esta seleccionado asigno el nombre de usuario como contraseña
    $('.cambioC').on('click', function () {
        if ($(this).is(':checked')) {

            cambioClave(true);
        } else {

            cambioClave(false);
        }
    });


    //Guardar o editar los usuarios
    $('#formulario').on("submit", function (e) {

        guardaryeditar(e);
    });

}



//Metodo que lista los prestadores
function listar_prestador() {

    tabla = $('#tbllistado').dataTable({

        "aProcessing": true, //activamos el procesamiento del datatables
        "aServerSide": true, //paginacion y filtrados realizados por el servidor
        dom: 'Bfrtip', //definimos los elementos del control de tabla
        buttons: [

            'copy',
            'excelHtml5'
        ],
        columnDefs: [
            {className: "dt-body-center", "targets": [0, 1, 2, 4, 5, 6, 7, 8]},
            {className: "dt-body-left", "targets": [3]}
        ],

        "ajax": {

            url: '../../controladores/prestador.php?op=listar_prestador',
            type: "post",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 18 //paginacion cada 18 registros

    }).DataTable();
}


//Metodo que activa un prestador
function activar_prestador(nit_prestador) {

    alertify.confirm('<i class="zmdi zmdi-notifications-active"></i> ¿Desea activar el prestador ' + nit_prestador + '?')
            .set({title: "Activar prestador"})
            .set({'labels': {ok: 'Aceptar', cancel: 'Cancelar'}})
            .set('onok', function (closeEvent) {

                $.post("../../controladores/prestador.php?op=activar_prestador", {nit_prestador: nit_prestador}, function (e) {

                    alertify.alert()
                            .setting({
                                'title': "Estado de la solicitud",
                                'label': 'Aceptar',
                                'message': '<i class="zmdi zmdi-info"></i> ' + e + '.'
                            }).show();
                    tabla.ajax.reload();
                });

            });
}


//Metodo que desactiva un prestador
function desactivar_prestador(nit_prestador) {

    alertify.confirm('<i class="zmdi zmdi-notifications-active"></i> ¿Desea desactivar el prestador ' + nit_prestador + '?')
            .set({title: "Desactivar prestador"})
            .set({'labels': {ok: 'Aceptar', cancel: 'Cancelar'}})
            .set('onok', function (closeEvent) {

                $.post("../../controladores/prestador.php?op=desactivar_prestador", {nit_prestador: nit_prestador}, function (e) {

                    alertify.alert()
                            .setting({
                                'title': "Estado de la solicitud",
                                'label': 'Aceptar',
                                'message': '<i class="zmdi zmdi-info"></i> ' + e + '.'
                            }).show();
                    tabla.ajax.reload();
                });

            });
}


//Metodo que lista los datos de un prestador y de los usuarios registrados al mismo
function mostrar(nit_prestador) {

    mostrar_prestador(nit_prestador);

    listar_usuarios(nit_prestador);

    mostrarform_prestador(true);

}


//Metodo que lista los datos de un prestador
function mostrar_prestador(nit_prestador) {

    $.post("../../controladores/prestador.php?op=mostrar", {nit_prestador: nit_prestador}, function (data, status) {

        data = JSON.parse(data);

        $("#nit_prestador").val(data.NIT_PRESTADOR);
        $("#nom_prestador").val(data.NOM_PRESTADOR);

    });

}


//Metodo que lista los usuarios asociados a un prestador
function listar_usuarios(nit_prestador) {

    tabla_usuarios = $('#tblusuarios').dataTable({

        "aProcessing": true, //activamos el procesamiento del datatables
        "aServerSide": true, //paginacion y filtrados realizados por el servidor
        dom: 'Bfrtip', //definimos los elementos del control de tabla
        buttons: [

            'copy',
            'excelHtml5'
        ],
        columnDefs: [
            {className: "dt-body-center", "targets": [0, 1, 2, 3, 4, 5, 6]}
        ],

        "ajax": {

            url: '../../controladores/prestador.php?op=listar_usuarios',
            data: {nit_prestador: nit_prestador},
            type: "post",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5 //paginacion cada 5 registros

    }).DataTable();
}


//Metodo que activa un usuario
function activar_usuario(cod_usuario) {

    alertify.confirm('<i class="zmdi zmdi-notifications-active"></i> ¿Desea activar el usuario ' + cod_usuario + '?')
            .set({title: "Activar usuario"})
            .set({'labels': {ok: 'Aceptar', cancel: 'Cancelar'}})
            .set('onok', function (closeEvent) {

                $.post("../../controladores/prestador.php?op=activar_usuario", {cod_usuario: cod_usuario}, function (e) {

                    alertify.alert()
                            .setting({
                                'title': "Estado de la solicitud",
                                'label': 'Aceptar',
                                'message': '<i class="zmdi zmdi-info"></i> ' + e + '.'
                            }).show();
                    tabla_usuarios.ajax.reload();
                });

            });
}


//Metodo que desactiva un usuario
function desactivar_usuario(cod_usuario) {

    alertify.confirm('<i class="zmdi zmdi-notifications-active"></i> ¿Desea desactivar el usuario ' + cod_usuario + '?')
            .set({title: "Desactivar usuario"})
            .set({'labels': {ok: 'Aceptar', cancel: 'Cancelar'}})
            .set('onok', function (closeEvent) {

                $.post("../../controladores/prestador.php?op=desactivar_usuario", {cod_usuario: cod_usuario}, function (e) {

                    alertify.alert()
                            .setting({
                                'title': "Estado de la solicitud",
                                'label': 'Aceptar',
                                'message': '<i class="zmdi zmdi-info"></i> ' + e + '.'
                            }).show();
                    tabla_usuarios.ajax.reload();
                });

            });
}


//Funcion para guardar y editar usuarios del sistema
function guardaryeditar(e) {

    e.preventDefault(); //no se activara la accion predeterminada del evento
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({

        url: '../../controladores/prestador.php?op=guardaryeditar',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (datos) {

            alertify.alert()
                    .setting({
                        'title': "Estado de la solicitud",
                        'label': 'Aceptar',
                        'message': '<i class="zmdi zmdi-info"></i> ' + datos + '.'
                    }).show();
            mostrarform_usuario(false);
            tabla_usuarios.ajax.reload();
        }
    });

    limpiar_usuario();
}


//Metodo que lista los datos de un usuario para su edicion 
function mostrar_usuario(cod_usuario) {

    $.post("../../controladores/prestador.php?op=mostrar_usuario", {cod_usuario: cod_usuario}, function (data, status) {

        data = JSON.parse(data);
        mostrarform_usuario(true);

        $("#ni_prest").val("");
        $("#cambiarContrasena").show();
        $("#password").attr("disabled", "disabled");
        $("#cod_usuario").prop('readonly', true);
        $("#tipo_documento").val(data.TIP_DOCUMENTO);
        $("#num_documento").val(data.NUM_DOCUMENTO);
        $("#cod_usuario").val(data.COD_USUARIO);
        $("#nom_usuario").val(data.NOM_USUARIO);

    });


    //Mostrar permisos del usuario
    $.post("../../controladores/prestador.php?op=permisos&id=" + cod_usuario, function (r) {
        $("#permisos").html(r);
    });

}


//funcion mostrar formulario con la informacion del prestador
//y de los usuarios
function mostrarform_prestador(flag) {

    limpiar_prestador();
    if (flag) {

        $("#listadoregistros").hide();
        $("#formularioregistros").show();
    } else {

        $("#listadoregistros").show();
        $("#formularioregistros").hide();
    }

}


//Metodo que limpia los datos declarados en el formulario de informacion del prestador
function limpiar_prestador() {

    $("#nit_prestador").val("");
    $("#nom_prestador").val("");
}


//Metodo que oculta el formulario
function cancelarform() {

    limpiar_prestador();
    mostrarform_prestador(false);
}



//funcion mostrar formulario con la informacion del prestador
//y de los usuarios
function mostrarform_usuario(flag) {

    limpiar_usuario();
    if (flag) {

        $("#listadousuarios").hide();
        $("#btnSalir").hide();
        $("#formulario").show();
        $("#ni_prest").val($("#nit_prestador").val());
        $("#cambiarContrasena").hide();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
    } else {

        $("#listadousuarios").show();
        $("#btnSalir").show();
        $("#formulario").hide();
        $("#btnagregar").show();
    }

}

//Habilita el cambio de clave para un usuario
function cambioClave(flag) {

    if (flag) {

        //Hago editable la contraseña y asigno el usuario gema para la misma
        $("#password").removeAttr("disabled");
        $("#password").val($("#cod_usuario").val());
    } else {

        $("#password").attr("disabled", "disabled");
        $("#password").val("");
    }

}


//Metodo que limpia los datos declarados en el formulario html
function limpiar_usuario() {

    $("#ni_prest").val("");
    $('#tipo_documento option:first').prop('selected', true);
    $("#num_documento").val("");
    $("#cod_usuario").val("");
    $("#nom_usuario").val("");
    $("#cod_usuario").prop('readonly', false);
    $("#password").val("");
    $("#password").removeAttr("disabled");
    $('input[type=checkbox]').prop('checked', false);
}


//Metodo que oculta el formulario
function cancelarform_usuario() {

    limpiar_usuario();
    mostrarform_usuario(false);
}



//Ejecuto la funcion inicial
init();
