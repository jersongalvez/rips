////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////             JS VISTA INICIO            /////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////        AMBITO: INICIO.PHP       ////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////

//Función que se ejecuta al inicio
function init() {

    $("#formulario").submit(function (e) {

        if ($("#clave1").val() !== $("#clave2").val()) {

            $("#clave1").focus();
            alertify.alert()
                    .setting({
                        'title': "Estado de la solicitud",
                        'label': 'Aceptar',
                        'message': '<i class="zmdi zmdi-info"></i> Las contraseñas no son iguales, verifique e intente de nuevo.'
                    }).show();

            return false;
        } else if ($("#clave1").val() === $("#cod_usuario").val()) {

            $("#clave1").focus();
            alertify.alert()
                    .setting({
                        'title': "Estado de la solicitud",
                        'label': 'Aceptar',
                        'message': '<i class="zmdi zmdi-info"></i> La contraseña no puede ser igual al código del usuario.'
                    }).show();
            return false;
        } else {

            guardaryeditar(e);
            return true;
        }
    });
}


//Metodo que limpia los datos declarados en el formulario html
function limpiar() {

    $("#clave1").val("");
    $("#clave2").val("");
}


//Metodo para actualizar la contraseña de usuario
function guardaryeditar(e) {

    e.preventDefault(); //no se activara la accion predeterminada del evento
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({

        url: '../../controladores/prestador.php?op=actualizarclave',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (datos) {

            alertify.alert()
                    .setting({
                        'title': "Estado de la solicitud",
                        'label': 'Aceptar',
                        'message': '<i class="zmdi zmdi-info"></i> ' + datos + '.',
                        'onshow': null, onclose: function () {
                            window.location.href = "../../controladores/prestador.php?op=salir";
                        }
                    }).show();
        }
    });

    limpiar();
}


//Ejecuto la funcion inicial
init();



 