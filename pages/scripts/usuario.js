////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////                JS USUARIO                ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////     AMBITO: USUARIO.PHP      /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////


// variable global
var tabla;


//funcion que se ejecuta al inicio (al cargar la vista por primera vez)
function init() {

    listar();

}


//Metodo que lista los usuarios
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

            url: '../../controladores/prestador.php?op=listar_Totalusuarios',
            type: "post",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 20 //paginacion cada 20 registros

    }).DataTable();
}



//Ejecuto la funcion inicial
init();
