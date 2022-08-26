////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////                 JS MENU                  ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////      AMBITO: HEADER.PHP      /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////

//Metodo que se ejecuta al inicio
function init() {

    mostrarformM(false);

    //Actualizacion de la contraseña de usuario
    $("#formulario_clave").submit(function (e) {

        if ($("#clave1_actualizar").val() !== $("#clave2_actualizar").val()) {

            $("#clave1_actualizar").focus();
            alertify.alert()
                    .setting({
                        'title': "Estado de la solicitud",
                        'label': 'Aceptar',
                        'message': '<i class="zmdi zmdi-info"></i> Las contraseñas no son iguales, verifique e intente de nuevo.'
                    }).show();
            return false;
        } else if ($("#clave1_actualizar").val() === $("#cod_usuario_sesion").val()) {

            $("#clave1_actualizar").focus();
            alertify.alert()
                    .setting({
                        'title': "Estado de la solicitud",
                        'label': 'Aceptar',
                        'message': '<i class="zmdi zmdi-info"></i> La contraseña no puede ser igual al código del usuario.'
                    }).show();
            return false;
        } else {

            actualizarClave(e);
            return true;
        }
    });

}


//Metodo para actualizar la contraseña de usuario
function actualizarClave(e) {

    e.preventDefault(); //no se activara la accion predeterminada del evento
    $("#btn_actualizar_clave").prop("disabled", true);
    var formData = new FormData($("#formulario_clave")[0]);

    $.ajax({

        url: '../../controladores/prestador.php?op=actualizarclave_session',
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


//Metodo que valida el cierre de sesion de un usuario
function salir() {

    $("body").overhang({
        type: "confirm",
        primary: "#e1edd7",
        accent: "#256B22",
        yesMessage: "Si",
        yesColor: "#1d75b4",
        noColor: "#C63947",
        textColor: "#256B22",
        message: "¿Desea salir del sistema?",
        closeConfirm: "true",
        overlay: true,
        callback: function (value) {
            var response = value ? "Yes" : "No";

            if (response === "Yes") {
                
                window.location.href = "../../controladores/prestador.php?op=salir";
            }
        }
    });
}


//Metodo que muestra el perfil del usuario que esta logueado en el momento
function mostrar_perfil(status) {

    if (status) {

        $("#modal_perfil").addClass("is-active");
        mostrarformM(false);
    } else {

        $("#modal_perfil").removeClass("is-active");
    }

}



//funcion mostrar formulario con la informacion del prestador
//y de los usuarios
function mostrarformM(flag) {
    
    limpiarFrm();

    if (flag) {

        $("#datos_prestador").hide();
        $("#cambioClave").show();
    } else {

        $("#datos_prestador").show();
        $("#cambioClave").hide();
    }

}


//Metodo que limpia los 
function limpiarFrm() {

    $("#clave1_actualizar").val("");
    $("#clave2_actualizar").val("");
}


//Metodo que se ejecuta al llamar el archivo
init();



