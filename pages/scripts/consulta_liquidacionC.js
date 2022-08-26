////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////         JS BUSCAR_LIQUIDACION CAP        ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////  AMBITO: PROCESAMIENTO CAPT  /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////


//Variables globales datatables
var tabla;
var tabla_prestadores;


//Función que se ejecuta al inicio
function init() {

    //Cargo el metodo de consulta para abrir la conexion ajax
    carga_vacia();
    carga_vacia_prestador();

    //Cargo el año actual
    cargar_vigencia();

    //Deshabilito el boton de consultas, se habilitara hasta el momento que se haga una consulta
    $('#nueva').attr("disabled", true);
}



//Metodo que carga el año actual
function cargar_vigencia() {

    var vigencia = document.querySelector('#vigencia');

    for (var i = new Date().getFullYear(); i >= 2020; i--) {

        var option = document.createElement("option");
        option.innerHTML = i;
        option.value = i;
        vigencia.appendChild(option);
    }

}


//Metodo que carga los meses en funcion del año seleccionado
function cargar_meses() {

    //Limpio el contenido del select
    $('#mes').empty();

    //Obtengo el año
    var vigencia = $("#vigencia").val();

    //Obtengo las propiedades del select
    var mostrar_mes = document.querySelector('#mes');


    if (vigencia !== '') {

        const ano = new Date();
        let mes = ['Elige una opción', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        let num_mes = ['', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        //Valido el año para hacer la asignacion de los meses
        var ciclo_mes = parseInt(vigencia) === parseInt(ano.getFullYear()) ? parseInt(ano.getMonth()) : 11;

        for (var i = 0; i <= ciclo_mes + 1; i++) {

            //Creo los elementos para el select
            var option = document.createElement("option");
            option.innerHTML = mes[i];
            option.value = num_mes[i];
            mostrar_mes.appendChild(option);
        }
    } else {

        //Creo un elemento vacio
        var option = document.createElement("option");
        option.innerHTML = 'Elige una opción';
        option.value = '';
        mostrar_mes.appendChild(option);
    }

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

        var nit_prestador = $("#nit_prestador").val();
        var vigencia = $("#vigencia").val();
        var mes = $("#mes").val();


        tabla = $('#tbllistado').dataTable({

            "aProcessing": true, //Activamos el procesamiento del datatables
            "aServerSide": true, //Paginación y filtrado realizados por el servidor
            dom: 'Bfrtip', //Definimos los elementos del control de tabla
            buttons: [
                'copyHtml5',
                'excelHtml5'
            ],
            columnDefs: [
                {className: "dt-body-center", "targets": [0, 1, 2, 3]},

                //Formateo el valor total de la factura
                {
                    "targets": 3,
                    "render": $.fn.dataTable.render.number('.', ',', 2, '$')
                }

            ],

            "ajax":
                    {
                        url: '../../controladores/prefactura.php?op=buscar_contrato',
                        data: {nit_prestador: nit_prestador, n_periodo: mes + '/' + vigencia},
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
            "order": [[1, "desc"]]//Ordenar (columna,orden)

        }).DataTable();

    }
}



//Metodo que valida si los campos del formulario de consultas tienen datos
function validar_campos() {

    var respuesta = 1;

    if ($("#nit_prestador").val() === "") {

        $("#nit_prestador").focus();
        respuesta = 0;
    } else if ($("#vigencia").val() === "") {

        $("#vigencia").focus();
        respuesta = 0;
    } else if ($("#mes").val() === "") {

        $("#mes").focus();
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



//Metodo que limpia el tbody en el datatables
function limpiar() {

    //Limpio el tbody del datatables
    $('#tbllistado').dataTable().fnClearTable();

    //Habilito el boton de consultas
    $('#filtrar').removeAttr("disabled");

    //Deshabilito el boton de consultas, se habilitara hasta el momento que se haga una consulta
    $('#nueva').attr("disabled", true);

}


//Ejecuto la funcion al cargar el archivo
init();


