////////////////////////////////////////////////////////////////////////////////
/////////////////////////       SISTEMA RIPS           /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////           VALIDAR INACTIVIDAD          /////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
////////          METODOS JS PARA EL PROCESAMIENTO DE INFORMACION        ///////
////////////////////////////////////////////////////////////////////////////////

//Valida el tiempo de inactividad en el sitio web
function inactividad() {

    var tiempo;
    window.onload = resetTimer;
    window.onmousemove = resetTimer;
    window.onmousedown = resetTimer;  // catches touchscreen presses as well      
    window.ontouchstart = resetTimer; // catches touchscreen swipes as well 
    window.onclick = resetTimer;      // catches touchpad clicks as well
    window.onkeypress = resetTimer;
    window.addEventListener('scroll', resetTimer, true);

    //Metodo que cierra la sesion cuendo ha expirado
    function cerrar_sesion() {
        
        //Se crea una cookie para validar en el login si el cierre de sesion
        //fue por inactividad del usuario
        document.cookie = "salida=0; max-age=3600; path=/";
        window.location.href = "../../controladores/prestador.php?op=salir";
    }

    //Metodo que limpia y asigna el tiempo de sesion por inactividad
    function resetTimer() {
        
        clearTimeout(tiempo);
        tiempo = setTimeout(cerrar_sesion, 900000);  // milliseconds //15 Min
    }
    
    //console.log(document.cookie); 
}

//Inicio el metodo para ir validando la inactividad en la app
inactividad();

