////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////              JS BUSCAR_RIPS              ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////  AMBITO: PROCESAMIENTO RIPS  /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////


//Variables globales datatables
var tabla;
var tabla_prestadores;
var tabla_facturas;

//Función que se ejecuta al inicio
function init() {

    //Cargo el metodo de consulta para abrir la conexion ajax
    carga_vacia();
    carga_vacia_prestador();

    //Cargo el data range
    cargarDatarange();

    //Deshabilito el boton de consultas, se habilitara hasta el momento que se haga una consulta
    $('#nueva').attr("disabled", true);
}


//Metodo que carga el datatables de remisiones en null
function carga_vacia() {

    tabla = $('#tbllistado').dataTable({

        dom: 'Bfrtip', //Definimos los elementos del control de tabla
        buttons: [
            'copyHtml5',
            'excelHtml5'
        ]

    }).DataTable();
}


//Metodo que carga el datatables prestadores en null
function carga_vacia_prestador() {

    tabla_prestadores = $('#tbllistadoprestador').dataTable({

        dom: 'Bfrtip', //Definimos los elementos del control de tabla
        buttons: []

    }).DataTable();
}



//Metodo que lista los rips registrados en un periodo de tiempo
function listar() {

    if (validar_campos() === 1) {

        //Deshabilito el boton de consultas, se debe resear la vista 
        //antes de consultar de nuevo
        $("#filtrar").html("Consultando...");
        $('#filtrar').attr("disabled", true);

        var rango_fechas = $('#rango_fechas').val();
        var rango = rango_fechas.split(' / ');
        var fecha_inicial = rango[0];
        var fecha_final = rango[rango.length - 1];
        var nit_prestador = $("#nit_prestador").val();
        var modalidad = $("#smodalidad").val();

        tabla = $('#tbllistado').dataTable({

            "aProcessing": true, //Activamos el procesamiento del datatables
            "aServerSide": true, //Paginación y filtrado realizados por el servidor
            dom: 'Bfrtip', //Definimos los elementos del control de tabla
            buttons: [
                'copyHtml5',
                'excelHtml5'
            ],
            columnDefs: [
                {className: "dt-body-center", "targets": [0, 1, 2, 3, 4, 5]}
            ],

            "ajax":
                    {
                        url: '../../controladores/rips.php?op=buscar_remisiones',
                        data: {fecha_inicial: fecha_inicial, fecha_final: fecha_final, nit_prestador: nit_prestador, smodalidad: modalidad},
                        type: "get",
                        dataType: "json",
                        error: function (e) {
                            console.log(e.responseText);
                        }

                    },
            "initComplete": function (settings, json) {

                //si data trae resultados dejo graficar
                if (json.aaData.length > 0) {

                    //Habilito el reset de los input, si hay datos
                    $('#nueva').removeAttr("disabled");

                    //Deshabilito el boton de consultas, se debe resear la vista 
                    //antes de consultar de nuevo
                    $("#filtrar").html("<i class='zmdi zmdi-search-in-file'></i> &nbsp; Buscar");

                } else {

                    $('#filtrar').removeAttr("disabled");
                    $("#filtrar").html("<i class='zmdi zmdi-search-in-file'></i> &nbsp; Buscar");
                }

            },
            "bDestroy": true,
            "iDisplayLength": 10, //Paginación
            "order": [[2, "desc"]]//Ordenar (columna,orden)

        }).DataTable();

    }
}



//Metodo que valida si los campos del formulario de consultas tienen datos
function validar_campos() {

    var respuesta = 1;

    if ($("#nit_prestador").val() === "") {

        $("#nit_prestador").focus();
        respuesta = 0;
    } else if ($("#smodalidad").val() === "") {

        $("#smodalidad").focus();
        respuesta = 0;
    }

    return respuesta;

}


//Metodo que muestra un formulario para hacer la busqueda de prestadores
function mostrar_bprestador(status) {

    if (status) {

        $("#modal_bprestador").addClass("is-active");
    } else {

        //Limpio el tbody del datatables
        $('#tbllistadoprestador').dataTable().fnClearTable();
        $("#nomb_prestador").val('');
        $("#modal_bprestador").removeClass("is-active");
    }

}


//Metodo que lista los rips registrados en un periodo de tiempo
function listar_bprestador() {

    if ($("#nomb_prestador").val().trim() !== "") {

        //Deshabilito el boton de consultas, se debe resear la vista 
        //antes de consultar de nuevo
        $('#filtrar_prestador').attr("disabled", true);

        var nom_prestador = $("#nomb_prestador").val();

        tabla_prestadores = $('#tbllistadoprestador').dataTable({

            "aProcessing": true, //Activamos el procesamiento del datatables
            "aServerSide": true, //Paginación y filtrado realizados por el servidor
            dom: 'Bfrtip', //Definimos los elementos del control de tabla
            buttons: [],

            columnDefs: [
                {className: "dt-body-center", "targets": [0, 1, 2, 4, 5]},
                {"width": "5%", "targets": 0},
                {"width": "10%", "targets": 1},
                {"width": "10%", "targets": 2}
            ],

            "ajax": {

                url: '../../controladores/rips.php?op=buscar_prestador',
                data: {nomb_prestador: nom_prestador},
                type: "get",
                dataType: "json",
                error: function (e) {
                    console.log(e.responseText);
                }

            },
            "initComplete": function () {

                $('#filtrar_prestador').removeAttr("disabled");
            },
            "bDestroy": true,
            "iDisplayLength": 5, //Paginación
            "order": [[2, "desc"]]//Ordenar (columna,orden)

        }).DataTable();

    } else {

        $("#nomb_prestador").focus();
    }
}


//Metodo que asigna el nit elegido en el formulario de busqueda de prestadores al input nit_prestador
function asignar_nit(nit) {

    $("#nit_prestador").val(nit);
    mostrar_bprestador(false);
}



//Metodo que lista el detallado de una remision
function mostrar_reporte(nit_prestador, num_remision) {

    mostrarform(true);

    //Muestro el encabezado de la remision
    encabezado_factura(nit_prestador, num_remision);

    //Muestro los datos del archivo de control
    datos_control(nit_prestador, num_remision);

    //Muestro la facturacion asociada a la remision
    listar_facturas(nit_prestador, num_remision);

    //Valores netos de la remision
    valores_neto(nit_prestador, num_remision);
}


//Metodo que muestra el encabezado de una remision
function encabezado_factura(nit_prestador, num_remision) {

    $.post("../../controladores/rips.php?op=mostrar_encabezado", {nit_prestador: nit_prestador, num_remision: num_remision}, function (data, status) {

        data = JSON.parse(data);

        $("#cod_prestador").val(data.COD_PRESTADOR);
        $("#ni_prestador").val(data.NUM_ENTIDAD);
        $("#n_remision").val(data.NUM_REMISION);
        $("#modalidad").val((data.MOD_CONTRATO == null) ? "-- No Aplica --" : data.MOD_CONTRATO);
        $("#fec_remision").val(data.F_REMISION);
        $("#no_prestador").val(data.NOM_PRESTADOR);
        $("#fec_registro").val(data.F_CARGUE);

    });
}


//Metodo que muestra los datos del CT
function datos_control(nit_prestador, num_remision) {

    $.post("../../controladores/rips.php?op=datos_control", {nit_prestador: nit_prestador, num_remision: num_remision}, function (data, status) {

        data = JSON.parse(data);

        $("#datos_ct").html("");

        for (var i = 0; i < data.length; i++) {

            var tr = '<tr>' +
                    '<td colspan="2" class="has-text-centered">' + data[i].ID_ARCHIVO + '</td>' +
                    '<td class="has-text-centered">' + data[i].COD_ARCHIVO + '</td>' +
                    '<td class="has-text-centered">' + data[i].F_REMISION + '</td>' +
                    '<td class="has-text-centered">' + data[i].TOTAL_ARCHIVOS + '</td>' +
                    '</tr>';

            $("#datos_ct").append(tr);
        }
    });
}


//Metodo que lista las facturas de una remision
function listar_facturas(nit_prestador, num_remision) {

    tabla_facturas = $('#tblinfoaf').dataTable({

        "aProcessing": true, //activamos el procesamiento del datatables
        "aServerSide": true, //paginacion y filtrados realizados por el servidor
        dom: 'Bfrtip', //definimos los elementos del control de tabla
        buttons: [

            'copy',
            'excelHtml5'
        ],
        columnDefs: [
            {className: "dt-body-center", "targets": [0, 1, 2, 3, 4, 5]},

            //Formateo el valor total de la factura
            {
                "targets": [2, 3, 4, 5],
                "render": $.fn.dataTable.render.number('.', ',', 2, '$')
            }
        ],

        "ajax": {

            url: '../../controladores/rips.php?op=listar_facturas',
            data: {nit_prestador: nit_prestador, num_remision: num_remision},
            type: "post",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10 //paginacion cada 5 registros

    }).DataTable();
}



//Metodo que muestra los valores netos de una remision
function valores_neto(nit_prestador, num_remision) {

    $.post("../../controladores/rips.php?op=valores_neto", {nit_prestador: nit_prestador, num_remision: num_remision}, function (data, status) {

        data = JSON.parse(data);

        $("#copagos").text(formatear_valor(data.COPAGO));
        $("#comisiones").text(formatear_valor(data.COMISION));
        $("#descuento").text(formatear_valor(data.DESCUENTO));
        $("#neto_rips").text(formatear_valor(data.TOTAL_NETO));

    });
}


//Metodo que formatea un valor en pesos colombianos
function formatear_valor(valor) {

    var formateado = new Intl.NumberFormat("es-CO", {

        style: 'currency',
        currency: "COP",
        minimumFractionDigits: 2
    }).format(valor);

    return formateado;
}


//Metodo que muestra formulario con la informacion del prestador
function mostrarform(flag) {

    limpiar_detallado();

    if (flag) {

        $("#formulario_busqueda").hide();
        $("#informacio_af").show();

    } else {

        $("#formulario_busqueda").show();
        $("#informacio_af").hide();
    }

}


//Metodo que limpia el tbody en el datatables
function limpiar() {

    //Limpio el tbody del datatables
    $('#tbllistado').dataTable().fnClearTable();

    //Habilito el boton de consultas
    $('#filtrar').removeAttr("disabled");

    //Deshabilito el boton de consultas, se habilitara hasta el momento que se haga una consulta
    $('#nueva').attr("disabled", true);

}


//Medoto que limpia la informacion del detallado de la remision
function limpiar_detallado() {

    $("#cod_prestador").val("");
    $("#ni_prestador").val("");
    $("#n_remision").val("");
    $("#modalidad").val("");
    $("#fec_remision").val("");
    $("#no_prestador").val("");
    $("#fec_registro").val("");

    $("#datos_ct").html("");

    $('#tblinfoaf').dataTable().fnClearTable();

    $("#copagos").text("");
    $("#comisiones").text("");
    $("#descuento").text("");
    $("#neto_rips").text("");

}


//Metodo que carga el data range con los valores por defecto
function cargarDatarange() {

    $('#rango_fechas').daterangepicker({

        "locale": {
            "format": "YYYY-MM-DD",
            "separator": " / ",
            "applyLabel": "Guardar",
            "cancelLabel": "Cancelar",
            "fromLabel": "Desde",
            "toLabel": "Hasta",
            "customRangeLabel": "Personalizar",
            "daysOfWeek": [
                "Do",
                "Lu",
                "Ma",
                "Mi",
                "Ju",
                "Vi",
                "Sa"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Setiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            "firstDay": 1
        },
        "opens": "right"
    });
}


//Ejecuto la funcion al cargar el archivo
init();


