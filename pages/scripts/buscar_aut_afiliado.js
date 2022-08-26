////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////          JS BUSCAR AUTORIZACION          ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
////////////////////  AMBITO: PROCESAMIENTO AUTORIZACIONES  ////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////

//Variable global datatables
var tabla;

//Metodo que se ejecuta al inicio
function init() {

    //Cargo el metodo de consulta para abrir la conexion ajax
    carga_vacia();

    //Deshabilito el boton de consultas, se habilitara hasta el momento que se haga una consulta
    $('#nueva').attr("disabled", true);
}


//Metodo que carga el datatables en null
function carga_vacia() {

    tabla = $('#tbllistado').dataTable({

        dom: 'Bfrtip', //Definimos los elementos del control de tabla
        buttons: [],

        columnDefs: [
            {className: "dt-body-center", "targets": [0, 1, 2, 3, 4, 5]},

            {
                "targets": [2],
                "visible": false,
                "searchable": false
            }
        ]

    }).DataTable();
}

//Metodo que busca invoca el metodo buscar_afiliadoSub() 
function buscar_datosaut() {

    if (validar_campos() === 1) {

        //Deshabilito el boton de consultas, se debe resear la vista 
        //antes de consultar de nuevo
        $("#filtrar").html("Consultando...");
        $('#filtrar').attr("disabled", true);

        //Hago la busqueda del afiliado
        buscar_afiliadoSub();
    }

}

//Metodo que muestra el nombre y regimen de un afiliado
function buscar_afiliadoSub() {

    var tip_documento = $("#tip_documento").val();
    var numd_afiliado = $("#numd_afiliado").val();

    $.post("../../controladores/autorizacion.php?op=consultar_afiliadoSub", {tip_documento: tip_documento, numd_afiliado: numd_afiliado}, function (data, status) {

        //Valido si hay conexion con el servidor

        try {

            data = JSON.parse(data);

            if (data !== false) {

                if (data[0].REGIMEN == 'C') {

                    $("#d_afiliado").append('<div class="column is-5"> <div class="field"> <strong>Nombre del afiliado:</strong> <label>' + data[0].NOMBRES + '</label> </div> </div>');
                    $("#d_afiliado").append('<div class="column is-2"> <div class="field"> <strong>Regimen:</strong> <label> MOVILIDAD </label> </div> </div>');
                    $("#d_afiliado").append('<div class="column is-2"> <div class="field"> <strong>Nivel:</strong> <label>' + data[0].NIVEL + '</label> </div> </div>');
                    $("#d_afiliado").append('<div class="column is-3"> <div class="field"> <strong>Tipo usuario:</strong> <label>' + data[0].TIPO_AFILIADO + '</label> </div> </div>');
                } else if (data[0].REGIMEN == 'S') {

                    $("#d_afiliado").append('<div class="column is-6"> <div class="field"> <strong>Nombre del afiliado:</strong> <label>' + data[0].NOMBRES + '</label> </div> </div>');
                    $("#d_afiliado").append('<div class="column is-3"> <div class="field"> <strong>Regimen:</strong> <label> SUBSIDIADO </label> </div> </div>');
                    $("#d_afiliado").append('<div class="column is-3"> <div class="field"> <strong>Nivel:</strong> <label>' + data[0].NIVEL + '</label> </div> </div>');
                }


                //Si el afiliado existe, busco las autorizaciones
                listar_aut();

                //Muestro las secciones donde se visualiza la informacion
                $("#datos_afiliado").show();
                $("#autorizaciones_afiliado").show();

                //Muestro nuevamente la leyenda inicial en el boton de consultas
                $("#filtrar").html("<i class='zmdi zmdi-search-in-file'></i> &nbsp; Buscar");

                //Habilito el reset de los input, si hay datos
                $('#nueva').removeAttr("disabled");

                //Deshabilito el boton de consultas, se debe resear la vista 
                //antes de consultar de nuevo
                $('#filtrar').attr("disabled", true);

            } else {

                $("#modal_autorizacion").html("<div class='modal is-active'> \n\
                                                <div class='modal-background'></div> \n\
                                                <div class='modal-card'> \n\
                                                    <header class='modal-card-head'> \n\
                                                        <p class='modal-card-title'>\n\
                                                            <strong>Usuario no encontrado</strong>\n\
                                                        </p>  \n\
                                                            <button class='delete' aria-label='close'></button> \n\
                                                    </header> \n\
                                                    <section class='modal-card-body'> \n\
                                                        <p class='has-text-justified'> El usuario no está registrado en la base de datos única de la entidad. </p> \n\
                                                    </section> \n\
                                                    <footer class='modal-card-foot has-text-centered'> \n\
                                                        <button class='button is-info' id='closeModal'>\n\
                                                             <span class='icon is-small'> <i class='fas fa-exclamation-triangle'></i></span>   \n\
                                                             <span> Aceptar </span> \n\
                                                        </button> \n\
                                                    </footer> \n\
                                                </div>\n\
                                            </div>");

                //cerrar modales
                $(".delete").click(function () {
                    $(".modal").removeClass("is-active");
                });

                $("#closeModal").click(function () {
                    $(".modal").removeClass("is-active");
                });

                //Habilito el boton de consultas
                $("#filtrar").html("<i class='zmdi zmdi-search-in-file'></i> &nbsp; Buscar");
                $('#filtrar').removeAttr("disabled");
            }
        } catch (exception) {

            //Envio un mensaje indicando al usuario que no es posible hacer la busqueda del usuario
            $("#modal_autorizacion").html("<div class='modal is-active'> \n\
                                                <div class='modal-background'></div> \n\
                                                <div class='modal-card'> \n\
                                                    <header class='modal-card-head'> \n\
                                                        <p class='modal-card-title'>\n\
                                                            <strong>Sin conexión al servidor</strong>\n\
                                                        </p>  \n\
                                                            <button class='delete' aria-label='close'></button> \n\
                                                    </header> \n\
                                                    <section class='modal-card-body'> \n\
                                                        <p class='has-text-justified'> No es posible consultar las autorizaciones asociadas al usuario en estos momentos. </p> \n\
                                                    </section> \n\
                                                    <footer class='modal-card-foot has-text-centered'> \n\
                                                        <button class='button is-info' id='closeModal'>\n\
                                                             <span class='icon is-small'> <i class='fas fa-exclamation-triangle'></i></span>   \n\
                                                             <span> Aceptar </span> \n\
                                                        </button> \n\
                                                    </footer> \n\
                                                </div>\n\
                                            </div>");

            //cerrar modales
            $(".delete").click(function () {
                $(".modal").removeClass("is-active");
            });

            $("#closeModal").click(function () {
                $(".modal").removeClass("is-active");
            });

            //Habilito el boton de consultas
            $("#filtrar").html("<i class='zmdi zmdi-search-in-file'></i> &nbsp; Buscar");
            $('#filtrar').removeAttr("disabled");
        }
    });
}


//Metodo que lista las autorizaciones de un afiliado
function listar_aut() {

    var nit_prestador = $("#nit_prestador").val();
    var tip_documento = $("#tip_documento").val();
    var numd_afiliado = $("#numd_afiliado").val();

    tabla = $('#tbllistado').dataTable({

        "aProcessing": true, //Activamos el procesamiento del datatables
        "aServerSide": true, //Paginación y filtrado realizados por el servidor
        dom: 'Bfrtip', //Definimos los elementos del control de tabla
        buttons: [],

        columnDefs: [
            {className: "dt-body-center", "targets": [0, 1, 2, 3, 4, 5]},

            {
                "targets": [2],
                "visible": false,
                "searchable": false
            }
        ],

        "ajax":
                {
                    url: '../../controladores/autorizacion.php?op=buscar_autafiliado',
                    data: {nit_prestador: nit_prestador, tip_documento: tip_documento, numd_afiliado: numd_afiliado},
                    type: "get",
                    dataType: "json",
                    error: function (e) {
                        console.log(e.responseText);
                    }

                },

        "bDestroy": true,
        "iDisplayLength": 20, //Paginación
        "order": [[5, "asc"]]//Ordenar (columna,orden)

    }).DataTable();


    //Dando clic en '+' consulto los servicios autorizados de esa solicitud
    $('#tbllistado tbody').on('click', '.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tabla.row(tr);

        if (row.child.isShown()) {

            row.child.hide();
            tr.removeClass('shown');
            //Cambio los estilos del boton
            $("#" + row.data()[2]).removeClass('is-danger');
            $("#" + row.data()[2]).addClass('is-link');
            $("#" + row.data()[2]).html('<span class="icon is-small"> <i class="zmdi zmdi-plus"></i>');
        } else {

            row.child(buscar_sau(row.data())).show();
            tr.addClass('shown');
            //Cambio los estilos del boton
            $("#" + row.data()[2]).removeClass('is-link');
            $("#" + row.data()[2]).addClass('is-danger');
            $("#" + row.data()[2]).html('<span class="icon is-small"> <i class="zmdi zmdi-minus"></i>');
        }
    });

}


//Metodo que busca y lista los servicios autorizados de una solicitud
function buscar_sau(no_solicitud) {

    var div = $('<div/>').addClass('loading').text('Consultando...');

    $.ajax({
        url: '../../controladores/autorizacion.php?op=buscar_servicioautorizado',
        data: {n_solicitud: no_solicitud[2]},
        dataType: 'json',
        success: function (data) {

            //Almaceno en un array las filas que retorna la consulta sql
            var fila = [];

            for (var i = 0; i < data.length; i++) {

                var tr = '<tr>' +
                        '<td class="has-text-centered">' + data[i].CD_SERVICIO + '</td>' +
                        '<td class="has-text-centered">' + data[i].CANTIDAD + '</td>' +
                        '<td class="has-text-justified">' + data[i].OBSERVACION + '</td>' +
                        '</tr>';

                fila.push(tr);
            }

            fila = fila.toString();
            fila = fila.replace(/,/g, '');

            //Envio la tabla de datos a la vista
            div.html('<table class="table is-bordered" style="width: 100%;"> \n\
                    <thead> \n\
                        <tr> \n\
                            <th colspan="3" class="has-text-centered">Servicios autorizados</th> \n\
                        </tr> \n\
                        <tr> \n\
                            <th class="has-text-centered">Codigo</th> \n\
                            <th class="has-text-centered">Cant.</th> \n\
                            <th class="has-text-centered">Descripci&oacute;n del servicio</th> \n\
                        </tr> \n\
                    </thead> \n\
                    <tbody>' + fila + '</tbody> \n\
                </table>').removeClass('loading');
        },
        error: function (e) {
            console.log(e.responseText);
        }
    });

    return div;

}




//Metodo que comfirma la aceptacion de los servicios autorizados
function aceptar_servicio(no_solicitud) {

    alertify.confirm('<i class="zmdi zmdi-notifications-active"></i> ¿Confirma que se prestaron los servicios asociados a esta autorización?')
            .set({title: "Confirmar servicio"})
            .set({'labels': {ok: 'Aceptar', cancel: 'Cancelar'}})
            .set('onok', function (closeEvent) {

                $.post("../../controladores/autorizacion.php?op=aceptar_servicio", {n_solicitud: no_solicitud}, function (e) {

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



//Metodo que valida si los campos del formulario de consultas tienen datos
function validar_campos() {

    var respuesta = 1;

    /*if ($("#nit_prestador").val() === "") {

        $("#nit_prestador").focus();
        respuesta = 0;
    }
	*/
	if ($("#tip_documento").val() === "") {

        $("#tip_documento").focus();
        respuesta = 0;
    } else if ($("#numd_afiliado").val() === "") {

        $("#numd_afiliado").focus();
        respuesta = 0;
    }

    return respuesta;

}


//Metodo que limpia el tbody en el datatables
function limpiar() {

    //Limpio las tablas creadas en la consulta de servicios autorizados
    $('#tbllistado tbody').remove();

    //Limpio el tbody del datatables
    $('#tbllistado').dataTable().fnClearTable();

    //Habilito el boton de consultas
    $('#filtrar').removeAttr("disabled");

    //Deshabilito el boton de consultas, se habilitara hasta el momento que se haga una consulta
    $('#nueva').attr("disabled", true);

    //Limpio los labels
    $('#nom_afiliado').text("");
    $('#reg_afiliado').text("");
    
    //Eliminos los div con los datos de usuario
    $("#d_afiliado").empty();

    //Oculto las secciones
    $("#datos_afiliado").hide();
    $("#autorizaciones_afiliado").hide();

}


//Ejecuto la funcion al cargar el archivo
init();


