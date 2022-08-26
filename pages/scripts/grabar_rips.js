////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////            JS GRABAR REMISION            ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////  AMBITO: PROCESAMIENTO RIPS  /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////


//Metodo que envia un frm  a la vista y ejecuta un modal indicando el error presentado
function envio_error(parametro) {

    $('<form action="rips.php" method="post">\n\
                <input type="hidden" name="novedad" value="' + parametro + '"></input>\n\
            </form>').appendTo('body').submit().remove();
}


//Metodo que envia el cod_prestador y la remision para generar un reporte en pdf
function generar_pdf(c_prestador, remision) {

    $('<form action="../../reportes/imprimir_sopval.php" method="get" target="_blank">\n\
            <input type="hidden" name="c_prestador" value="' + c_prestador + '"></input>\n\
            <input type="hidden" name="remision" value="' + remision + '"></input>\n\
        </form>').appendTo('body').submit().remove();

}


//Metodo que se ejecuta cuando se hace el commit en sql server
function exito() {

    $("#estado").html("<article class='message is-info'> \n\
                            <div class='message-body'>  \n\
                                <span class='icon is-small'><i class='zmdi zmdi-notifications'></i></span>\n\
                                Los datos asociados a esta remisión se han grabado con éxito. De clic en el botón “Informe de resultados” \n\
                                para obtener un comprobante de esta operación.  \n\
                            </div> \n\
                       </article>");

    $("#nuevo_rips").prop("disabled", false);
    $("#reporte").prop("disabled", false);
}



//Metodo que se ejecuta cuando no se pueden guardar los datos
function error() {

    $("#estado").html("<article class='message is-danger'> \n\
                            <div class='message-body'> \n\
                                <span class='icon is-small'><i class='zmdi zmdi-notifications'></i></span>\n\
                                Se presentó un error mientras se grababan los datos. Cargue el archivo nuevamente e inicie el proceso de registro. \n\
                            </div> \n\
                        </article>");

    $("#inicio").prop("disabled", false);
}


//Redirecciona a la vista de cargue de rips
function nuevo() {

    window.location.href = 'rips.php';
}









