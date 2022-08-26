////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////                 JS LOGIN                 ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////      AMBITO: LOGIN.PHP       /////////////////////////
////////            METODOS JS PARA VALIDAR EL INGRESO AL SISTEMA        ///////
////////////////////////////////////////////////////////////////////////////////


// Valida la conexion del login por medio de ajax
$(document).ready(function () {

// Funcion jquery que ejecuta la funcion de acuerdo al evento que se indica (submit)
    $("#loginForm").bind("submit", function () {

        // alert("Enviar formulario");
        $.ajax({
            type: $(this).attr("method"),
            url: '../controladores/prestador.php?op=verificar',
            data: $(this).serialize(),
            beforeSend: function () {
                $("#loginForm button[type = submit]").html("Enviando...");
                $("#loginForm button[type = submit]").attr("disabled", "disabled");
            },
            success: function (response) {
                //console.log(response);
                if (response !== "false") {
                    //alert("Conectado");
                    $("body").overhang({
                        custom: true,
                        primary: "#e1edd7",
                        accent: "#256B22",
                        textColor: "#256B22",
                        overlay: true,
                        duration: 0.5,
                        message: "Usuario encontrado, te estamos redirigiendo...",
                        callback: function () {
                            window.location.href = "inicio/inicio.php";
                        }
                    });

                    $("#loginForm button[type = submit]").html("<span class='icon is-small'> <i class='fas fa-sign-in-alt'></i> </span> <span> Iniciar sesión </span>");
                } else {
                    //alert("Error al conectar");
                    $("body").overhang({
                        custom: true,
                        primary: "#C63947",
                        accent: "#A1333E",
                        duration: 0.5,
                        message: "El usuario o clave no son correctos !!!",
                        overlay: true
                    });

                    $("#usuario").focus();
                    $("#loginForm button[type = submit]").html("<span class='icon is-small'> <i class='fas fa-sign-in-alt'></i> </span> <span> Iniciar sesión </span>");
                    $("#loginForm button[type = submit]").removeAttr("disabled");
                }

            },
            error: function () {
                //alert("Error al conectar");
                $("body").overhang({
                    type: "error",
                    message: "Error al conectar!"
                });
                $("#loginForm button[type = submit]").html("Verifique su conexión a Internet");
                $("#loginForm button[type = submit]").removeAttr("disabled");
            }
        });
        return false;
    });

});


//Comprobar si una cookie existe
function obtenerCookie(nombre) {

    var valor = document.cookie.match(RegExp('(?:^|;\\s*)' + nombre + '=([^;]*)'));
    return valor ? valor[1] : null;
}


//Eliminar una cookie determinada
function borrarCookie(nombre) {

    document.cookie = nombre + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
}


//Muestra un mensaje cuando la sesion se cierra por tiempo de inactividad
function msgInactividad() {

    $("body").overhang({
        custom: true,
        primary: "#C63947",
        accent: "#A1333E",
        message: "Cierre de sesión por inactividad de usuario !!!",
        closeConfirm: true,
        overlay: true
    });
}


//Funcion que valida si viene una cookie
function init() {

    if (obtenerCookie('salida') === '0') {

        //console.log('Modal');
        msgInactividad();
    }

    //console.log('Salida por primera vez: ' + obtenerCookie('salida'));
    borrarCookie('salida');
    //console.log('Salida segunda vez: ' + obtenerCookie('salida'));
    //console.log(document.cookie);
}

//Se ejecuta la funcion inicial
init();
