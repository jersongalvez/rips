<?php

////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////          CONTROLADOR PRESTADOR           ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////  AMBITO: PROCESAMIENTO PRESTADORES  ////////////////////
////////////////////////////////////////////////////////////////////////////////


session_start();


require_once '../modelos/General.php';


$general = new General();
$nit_prestador  = isset($_POST["nit_prestador"]) ? $prestador->limpiarCadena($_POST["nit_prestador"]) : "";
$ni_prest       = isset($_POST["ni_prest"]) ? $prestador->limpiarCadena($_POST["ni_prest"]) : "";
$cod_usuario    = isset($_POST["cod_usuario"]) ? $prestador->limpiarCadena($_POST["cod_usuario"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? $prestador->limpiarCadena($_POST["tipo_documento"]) : "";
$num_documento  = isset($_POST["num_documento"]) ? $prestador->limpiarCadena($_POST["num_documento"]) : "";
$nom_usuario    = isset($_POST["nom_usuario"]) ? $prestador->limpiarCadena($_POST["nom_usuario"]) : "";
$password       = isset($_POST["password"]) ? $prestador->limpiarCadena($_POST["password"]) : "";


switch ($_GET["op"]) {

	case 'contratos':
      $contratos = $general->contratos();
      echo json_encode($contratos);
    break;
    
}


