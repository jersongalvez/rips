////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////                JS PERMISO                ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////     AMBITO: PERMISO.PHP      /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////


// variable global
var tabla;


//funcion que se ejecuta al inicio (al cargar la vista por primera vez)
function init() {

    listar();
   
    $('#formulario').on("submit", function (e) {

        guardaryeditar(e);
    });
}


//Metodo que lista los permisos
function listar() {

    tabla = $('#tbllistado').dataTable({

        "aProcessing": true, //activamos el procesamiento del datatables
        "aServerSide": true, //paginacion y filtrados realizados por el servidor
        dom: 'Bfrtip', //definimos los elementos del control de tabla
        buttons: [

            'copy',
            'excelHtml5'
        ],
        columnDefs: [
            {className: "dt-body-center", "targets": [0, 1, 3]},
            {className: "dt-body-left", "targets": [2]}
        ],

        "ajax": {

            url: '../../controladores/permiso.php?op=listar',
            type: "post",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 20 //paginacion cada 5 registros

    }).DataTable();
}


//Metodo que activa un permiso
function activar(cod_permiso) {

    alertify.confirm('<i class="zmdi zmdi-notifications-active"></i> ¿Desea activar el permiso?')
            .set({title: "Activar permiso"})
            .set({'labels': {ok: 'Aceptar', cancel: 'Cancelar'}})
            .set('onok', function (closeEvent) {

                $.post("../../controladores/permiso.php?op=activar", {cod_permiso: cod_permiso}, function (e) {

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


//Metodo que activa un permiso
function desactivar(cod_permiso) {

    alertify.confirm('<i class="zmdi zmdi-notifications-active"></i> ¿Desea desactivar el permiso?')
            .set({title: "Desactivar permiso"})
            .set({'labels': {ok: 'Aceptar', cancel: 'Cancelar'}})
            .set('onok', function (closeEvent) {

                $.post("../../controladores/permiso.php?op=desactivar", {cod_permiso: cod_permiso}, function (e) {

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


//Metodo para guardar y editar permisos
function guardaryeditar(e) {

    e.preventDefault(); //no se activara la accion predeterminada del evento
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({

        url: '../../controladores/permiso.php?op=guardaryeditar',
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
            mostrarform(false);
            tabla.ajax.reload();
        }
    });

    limpiar();
}


//Metodo que lista los datos de un permiso para su edicion 
function mostrar(cod_permiso) {

    $.post("../../controladores/permiso.php?op=mostrar", {cod_permiso: cod_permiso}, function (data, status) {

        data = JSON.parse(data);
        mostrarform(true);

        $("#cod_permiso").val(data.COD_MENU);
        $("#nombre").val(data.NOMBRE);
        $("#descripcion").val(data.DESCRIPCION);
    });

}


//funcion mostrar formulario
function mostrarform(flag) {

    limpiar();
    if (flag) {

        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
    } else {

        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }

}


//Metodo que limpia los datos declarados en el formulario html
function limpiar() {

    $("#cod_permiso").val("");
    $("#nombre").val("");
    $("#descripcion").val("");

}


//Metodo que oculta el formulario
function cancelarform() {

    limpiar();
    mostrarform(false);
}



//Ejecuto la funcion inicial
init();
