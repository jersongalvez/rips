<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////        ARCHIVO DE TAREAS PROGRAMADAS         /////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////      AMBITO: HOSTING       /////////////////////////
/////    FUNCIONES QUE SE EJECUTAN MEDIANTE TAREAS PROGRAMADAS EN EL VPS  /////
////////////////////////////////////////////////////////////////////////////////

/**
 * Metodo que mediante una tarea programada, elimina el contenido del directorio 
 * 'ficheros_temporales'
 * @param string $dir
 */
function vaciar_Ftemporales($dir) {
    $count = 0;

    //se obtiene la ruta del directorio, es importante que venga con "/"
    $dir = rtrim($dir, "/\\") . "/";


    $results = scandir($dir);


    //se valida si existen subdirectorios, de lo contrario no se hace nada
    if (count($results) > 2) {

        // se añaden los elementos a una lista
        $list = dir($dir);

        // almacena el nombre de los archivos hasta que este este vacio
        while (($file = $list->read()) !== false) {
            if ($file === "." || $file === "..")
                continue;
            if (is_file($dir . $file)) {
                unlink($dir . $file);
                $count++;
            } elseif (is_dir($dir . $file)) {
                $count += vaciar_Ftemporales($dir . $file);
            }
        }

        // se elimina el directorio de archivos
        rmdir($dir);
    }
}

//llamado del metodo y paso de la ruta en el vps
vaciar_Ftemporales('C:/Inetpub/vhosts/pijaossalud.online/prestadores.pijaossalud.online/ficheros_temporales/');
