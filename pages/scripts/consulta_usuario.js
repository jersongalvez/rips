////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////          JS CONSULTA AFILIADOS           ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////  AMBITO: CONSULTA DE AFILIADOS  ////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////


//Metodo que se ejecuta al cargar la pagina
function init() {
    
    //Evita que un formulario se reenvie varias veces
    reenvio();

    //cerrar modales
    $(".delete").click(function () {
        $(".modal").removeClass("is-active");
    });

    $("#closeModal").click(function () {
        $(".modal").removeClass("is-active");
    });


    //Abrir y cerrar el acordion
    document.addEventListener('DOMContentLoaded', function () {
        let cardToggles = document.getElementsByClassName('card-toggle');
        for (let i = 0; i < cardToggles.length; i++) {
            cardToggles[i].addEventListener('click', e => {
                e.currentTarget.parentElement.parentElement.childNodes[3].classList.toggle('is-hidden');
            });
        }
    });


}


//Metodo que evita el reenvio de un formulario
function reenvio() {

    if (window.history.replaceState) { // verificamos disponibilidad
        window.history.replaceState(null, null, window.location.href);
    }
}


//Metodo que valida si los campos cumplen con el formato requerido para iniciar la consulta
function validarConsulta() {

    var solo_numeros = /^\d*$/;

    if ($('#tipdoc').val().trim() === '') {

        $('#tipdoc').focus();
        alerta('Tipo de documento no válido', 'Seleccione un tipo de documento');
        return false;
    } else if (!solo_numeros.test($("#txtNumdoc").val()) || $("#txtNumdoc").val() === '') {

        $("#txtNumdoc").focus();
        alerta('Documento no válido', 'Número de documento no válido');
        return false;
    } else if (!grecaptcha.getResponse()) {

        alerta('No soy un robot', 'Complete la CAPTCHA');
        return false;
    }


    $("#consultar_usu").html("Consultando...");
    $("#consultar_usu").attr("disabled", true);
}


//Metodo que muestra una alert con un mensaje
function alerta(titulo, mensaje) {

    alertify.alert()
            .setting({
                'title': titulo,
                'label': 'Aceptar',
                'message': '<i class="fas fa-exclamation-triangle"></i> ' + mensaje + '.'
            }).show();
}


//Imprime un area determinada
function imprimir() {

    $("#wrapper").printArea();
}


//Metodo que envia un frm  a la vista y ejecuta un modal indicando el error presentado
function envio_error(parametro) {

    $('<form action="consulta_afiliado.php" method="post">\n\
                <input type="hidden" name="novedad" value="' + parametro + '"></input>\n\
            </form>').appendTo('body').submit().remove();
}

$("#validar").on("click", function () {
  var url1 = "../../controladores/contrareferencia.php?op=validar_usuario_cambio",
  tipdoc = $("#tipdoc").val(),
  txtNumdoc = $("#txtNumdoc").val();

    if(tipdoc === '') {
        alerta('', 'Seleccione un tipo de documento');
        $("#tipdoc").focus();
    }
    else if (txtNumdoc === '') {
        alerta('', 'Digite el numero de documento');
        $("#txtNumdoc").focus();
    }
    else {
      $.post(url1,{txtNumdoc: txtNumdoc}, function(data) {
        data = JSON.parse(data);
          if (data == 0){
            $("#consultar_usu").attr("disabled", false);
            $("#validar").attr("disabled", true);
            $("#consultar_usu").attr("hidden", false);
          }
          else {
            alertify.alert()
              .setting({
              'title': "Los datos del usuario cambiaron",
              'label': 'Aceptar',
              'message': '<i class="zmdi zmdi-info"></i> El tipo de documento nuevo del usuario:  ' + data[0].HCD_TIP_DOCUMENTO_NV + '<br> <i class="zmdi zmdi-info"></i> El numero de documento nuevo del usuario: ' + data[0].HCD_NUM_DOCUMENTO_NV + '.'
              }).show();
            $("#consultar_usu").attr("disabled", false);
            $("#consultar_usu").attr("hidden", false);

            //PASAMOS LOS DATOS A EL FORMULARIO
            $("#tipdoc").val(data[0].HCD_TIP_DOCUMENTO_NV).attr("selected", true);
            $("#txtNumdoc").val(data[0].HCD_NUM_DOCUMENTO_NV);
          }
      });
    }
});


//Ejecuto la funcion al cargar el archivo
init();



































