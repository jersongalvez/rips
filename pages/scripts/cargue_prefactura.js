////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////          JS CARGAR_PREFACTURA            ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////  AMBITO: PROCESAMIENTO CAPITA  /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////


//variable global que valida si un input es numerico
var solo_numeros = /^\d*$/;


//funcion que se ejecuta al inicio
function init() {

    //Evita que un formulario se reenvie varias veces
    reenvio();

    //valido el input file que carga el archivo zip
    const fileInput = document.querySelector('#input-rips input[type=file]');
    fileInput.onchange = () => {
        if (fileInput.files.length > 0) {
            const fileName = document.querySelector('#input-rips .file-name');
            fileName.textContent = fileInput.files[0].name;
        }
    };


    //cerrar modales
    $(".delete").click(function () {
        $(".modal").removeClass("is-active");
    });

    $("#closeModal").click(function () {
        $(".modal").removeClass("is-active");
    });

}


//Metodo que evita el reenvio de un formulario
function reenvio() {

    if (window.history.replaceState) { // verificamos disponibilidad
        window.history.replaceState(null, null, window.location.href);
    }
}


//Ejecuta un modal de carga al procesar el frm
//Comentar este metodo en el vps
function modal_carga() {

    $(".loadingpage").fadeOut("slow");
}


//Metodo que valida si los campos cumplen con el formato requerido para iniciar 
//con el cargue del archivo .zip
function validarSubida() {

    if ($("#archivo").val() === '') {

        $("#archivo").focus();
        alerta('Archivo no válido', 'Debe cargar un archivo de texto simple');
        return false;
    } else if (validarFormatoFecha($("#archivo").prop('files')[0].name.slice(0, -4))) {

        $("#archivo").focus();
        alerta('Archivo no válido', 'El nombre del archivo es inválido, debe tener el formato mm-aaaa');
        return false;
    }


    modal_procesamiento();
    $("#proceso").addClass("is-loading");
}


//Metodo que valida si una fecha tiene el formato correcto
function validarFormatoFecha(campo) {

    var expresion = /^\d{2}\-\d{4}$/;

    if ((campo.match(expresion)) && (campo !== '')) {

        return false;
    } else {

        return true;
    }
}


//modal que se ejecuta cuando un rips se esta procesando
function modal_procesamiento() {
    $("#modal_factura").addClass("is-active");
    $("#modal_prefactura_msg").html("Esta tarea puede tardar dependiendo de la cantidad de registros a validar.");
}


//Metodo que muestra una alerta, de acuerdo con el mensaje enviado
function alerta(titulo, mensaje) {

    alertify.alert()
            .setting({
                'title': titulo,
                'label': 'Aceptar',
                'message': '<i class="fas fa-exclamation-triangle"></i> ' + mensaje + '.'
            }).show();
}



//Metodo que muestra un dialogo de comfirmacion antes de grabar el archivo
function envio_prefactura(periodo) {

    alertify.confirm('<i class="zmdi zmdi-notifications-active"></i> Los datos asociados no podrán ser modificados después de grabar.')
            .set({title: "¿Esta seguro de grabar el archivo: " + periodo + "?"})
            .set({'labels': {ok: 'Aceptar', cancel: 'Cancelar'}})
            .set('onok', function () {
                $('<form action="grabar_prefactura.php" method="post">\n\
                    <input type="hidden" name="datos_grabar" value="1"></input>\n\
                </form>').appendTo('body').submit().remove();

                $("#grabar").attr("disabled", true);
                $("#modal_factura").addClass("is-active");
                $("#modal_prefactura_msg").html("Preparando la información para iniciar con la inserción de datos, espere un momento.");

            })
            .set('onclose', function () {
                $('#grabar').focus();
            })
            .set('defaultFocus', 'cancel');
}


//Metodo que envia a la vista un mensaje de error
function envio_novedades(cod_error, valor_opcional) {

    var mensaje;
    //Si la variable en el metodo no trae nada, se declara.
    var valor_opcional = valor_opcional || "";

    switch (cod_error) {
        case 1:
            mensaje = 'La extensión o tamaño del archivo no son correctos.';
            break;

        case 2:
            mensaje = 'El periodo que desea registrar en el sistema, se cargó el: ' + valor_opcional + '.';
            break;
            
        case 3:
            mensaje = 'El archivo no tiene las especificaciones requeridas.';
            break;

    }


    $("#modal_novedad").html("<div class='modal is-active'> \n\
                                <div class='modal-background'></div> \n\
                                    <div class='modal-card'> \n\
                                        <header class='modal-card-head'> \n\
                                            <p class='modal-card-title'>\n\
                                                <strong>No se puede procesar el archivo</strong>\n\
                                            </p>  \n\
                                            <button class='delete' aria-label='close' onclick='cerrar_modal()'></button> \n\
                                        </header> \n\
                                        <section class='modal-card-body'> \n\
                                            <p class='has-text-justified'>" + mensaje + "</p> \n\
                                        </section> \n\
                                        <footer class='modal-card-foot has-text-centered'> \n\
                                            <button class='button is-info' onclick='cerrar_modal()'>\n\
                                                <span class='icon is-small'> <i class='fas fa-exclamation-triangle'></i></span>   \n\
                                                <span> Aceptar </span> \n\
                                            </button> \n\
                                        </footer> \n\
                                    </div>\n\
                                </div>");

}


//Cierra las ventanas modales
function  cerrar_modal() {

    $(".modal").removeClass("is-active");
}



//Metodo que envia un formulario para descargar un txt con los errores encontrados 
//en el proceso de validacion
function log_txt(array) {

    $('<form action="../../reportes/log_erroresCap.php" method="post" target="_blank">\n\
                <input type="hidden" name="values_array" value="' + array + '"></input>\n\
            </form>').appendTo('body').submit().remove();

}



//metodo que recarga la pagina y lleva al inicio
function recarga(flag) {

    switch (flag) {
        case 0:

            $('#carga').hide();
            $('#total_error').hide();
            $('#pantalla_prefactura').show();
            $("#archivo").focus();
            break;


        case 2:

            $('#carga').hide();
            $('#total_error').hide();
            $('#grabar_datos').hide();
            $('#pantalla_prefactura').show();
            $("#archivo").focus();
            break;
    }

}


//Ejecuto la funcion al cargar el archivo
init();
































