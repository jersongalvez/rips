////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////          JS GRABAR PRE FACTURA            ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
////////////////////////  AMBITO: PROCESAMIENTO CAPITA  /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////


//Metodo que envia un frm  a la vista y ejecuta un modal indicando el error presentado
function envio_error(parametro) {

    $('<form action="cargar_archivo_capita.php" method="post">\n\
                <input type="hidden" name="novedad" value="' + parametro + '"></input>\n\
            </form>').appendTo('body').submit().remove();
}



//Metodo que se ejecuta cuando se hace el commit en sql server
function exito() {

    $("#estado").html("<article class='message is-info'> \n\
                            <div class='message-body'>  \n\
                                <span class='icon is-small'><i class='zmdi zmdi-notifications'></i></span> Los datos asociados a este archivo se han grabado con éxito. \n\
                                De clic en el botón “Registrar un nuevo periodo” para iniciar un nuevo cargue.</div> \n\
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

    window.location.href = 'cargar_archivo_capita.php';
}









