////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////              JS CARGAR_RIPS              ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////  AMBITO: PROCESAMIENTO RIPS  /////////////////////////
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

    if (!solo_numeros.test($("#txtNremision").val()) || $("#txtNremision").val() === '') {

        $("#txtNremision").focus();
        alerta('Remisión no válida', 'Debe ingresar un valor de tipo entero');
        return false;
    } else if ($("#smodalidad option:selected").val() !== "C" && $("#smodalidad option:selected").val() !== "E") {

        $('#smodalidad').focus();
        alerta('Modalidad no válida', 'Debe elegir una opción Cápita / Evento');
        return false;
    } else if ($("#archivo").val() === '') {

        $("#archivo").focus();
        alerta('Archivo no válido', 'Debe cargar un fichero');
        return false;
    }

    if (val_peso_ext() === 1) {

        $("#archivo").focus();
        alerta('Archivo no válido', 'Debe cargar un archivo comprimido en formato .ZIP');
        return false;
    } else if (val_peso_ext() === 2) {

        $("#archivo").focus();
        alerta('Archivo no válido', 'Tamaño de archivo no permitido, debe ser menor a 10 MB');
        return false;
    }



    if ($("#archivo").prop('files')[0].name.slice(0, -4) !== $("#txtNremision").val()) {

        $("#txtNremision").focus();
        alerta('Datos no válidos', 'La remisión digitada no corresponde a la relacionada en el archivo');
        return false;
    }


    modal_procesamiento();
    $("#proceso").addClass("is-loading");

}


//Metodo que valida la extencion y tamaño de archivo
function val_peso_ext() {

    var archivo = document.getElementsByName("archivo");
    var extension = archivo[0].files[0].name.split('.').pop();
    var tamanio = archivo[0].files[0].size;
    const MAXIMO_TAMANIO_BYTES = 10000000; // 1MB = 1 millón de bytes
    extension = extension.toLowerCase();

    if (extension !== "zip") {

        return 1;
    } else if (tamanio > MAXIMO_TAMANIO_BYTES) {

        return 2;
    } else {

        return 0;
    }

}


//modal que se ejecuta cuando un rips se esta procesando
function modal_procesamiento() {
    $("#modal_rips").addClass("is-active");
    $("#modal_rips_msg").html("Esta tarea puede tardar dependiendo de la cantidad de registros a validar.");
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



//Metodo que muestra un dialogo de comfirmacion antes de grabar el rips
function envio_rips(remision, modalidad) {

    alertify.confirm('<i class="zmdi zmdi-notifications-active"></i> Esta remisión se grabara en modalidad <strong>' + modalidad + '</strong>. ¿Desea continuar?.')
            .set({title: "¿Esta seguro de grabar la remisión: " + remision + "?"})
            .set({'labels': {ok: 'Aceptar', cancel: 'Cancelar'}})
            .set('onok', function () {
                $('<form action="grabar_rips.php" method="post">\n\
                    <input type="hidden" name="datos_grabar" value="1"></input>\n\
                </form>').appendTo('body').submit().remove();

                $("#grabar").attr("disabled", true);
                $("#modal_rips").addClass("is-active");
                $("#modal_rips_msg").html("Preparando la información para iniciar con la inserción de datos, espere un momento.");

            })
            .set('onclose', function () {
                $('#grabar').focus();
            })
            .set('defaultFocus', 'cancel');
}


//Metodo que envia a la vista un mensaje de error
function envio_novedades(cod_error) {

    var mensaje;

    switch (cod_error) {
        case 1:
            mensaje = 'La extensión, tamaño del archivo o número de remisión no son correctos.';
            break;

        case 2:
            mensaje = 'Esta remisión ya se encuentra registrada en el sistema. Verifique e intente de nuevo.';
            break;

        case 3:
            mensaje = 'Los datos de la remisión no corresponde con el del prestador actual.';
            break;

        case 4:
            mensaje = 'El código Reps o Nit del prestador no estan registrados en el sistema.';
            break;

        case 5:
            mensaje = 'Ocurrió un error y el archivo no se pudo validar y/o formato no es ZIP.';
            break;

        case 6:
            mensaje = 'Este archivo intento cargarse en un envío anterior, inicie el proceso de nuevo.';
            break;

        case 7:
            mensaje = 'No se encuentran los datos requeridos para iniciar el proceso de validación.';
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

    $('<form action="../../reportes/log_errores.php" method="post" target="_blank">\n\
                <input type="hidden" name="values_array" value="' + array + '"></input>\n\
            </form>').appendTo('body').submit().remove();

}



//metodo que recarga la pagina y lleva al inicio
function recarga(flag) {

    switch (flag) {
        case 0:

            $('#carga').hide();
            $('#total_error').hide();
            $('#pantalla_rips').show();
            $("#txtNremision").focus();
            break;

        case 1:

            $('#msg_val_inicial').hide();
            $('#val_inicial').hide();
            $("#txtNremision").focus();
            break;

        case 2:

            $('#carga').hide();
            $('#total_error').hide();
            $('#grabar_datos').hide();
            $('#pantalla_rips').show();
            $("#txtNremision").focus();
            break;
    }

}

//TICKET ASOCIACION DE RIPS 914
$("#smodalidad").on("change", function () {
    if($("#smodalidad").val() == "C"){
      $("#hidden_contrato").attr("hidden", false);
      $.get("../../controladores/general.php?op=contratos", function(data) {
        data = JSON.parse(data);
          contratos = data.map(function(datos) {
            return '<option value="'+ datos.NUM_CONTRATO +'">'+datos.NUM_CONTRATO+'</option>';
        })
		
       $("#cont_vista").html('<option value="">Seleccione un Contrato</option>' + contratos);
     });
   }
   else {
    $("#hidden_contrato").attr("hidden", true);
    $("#cont_vista").html('<option value="SIN CONTRATO">Sin Contrato</option>');
   }
});

//Ejecuto la funcion al cargar el archivo
init();
































