<?php
require_once '../modelos/Contrareferencia1.php';
$contrareferencia = new Contrareferencia1();

switch($_GET["op"]) {
  case 'buscar_usuario':
    echo "como estas desde controlador";
    break;

  case 'ver':
    $diagnosticos = $contrareferencia->cargarDiagnosticos();
    $procedimientos = $contrareferencia->cargarProcedimientos();
    $municipios = $contrareferencia->cargarMunicipios();
    $data = [
       "diagnosticos" => $diagnosticos,
       "procedimientos" => $procedimientos,
       "municipio" => $municipios
    ];
    return $data;
    break;

  case 'get_procedimientos':
    print_r($_SESSION);
    break;

  case 'registrar_remision':
    print_r($_POST);
    $count = count($_FILES['archivos']['name']);
    for ($i = 0; $i < $count; $i++) {
        $files[] =  $_FILES['archivos']['name'][$i];
    }
    $tpdocumento = $_POST["tpdocumento"];
    $documento = $_POST["documento"];
    $fecha = $_POST["fecha"];
    $regimen = $_POST["regimen"];
    $papellido = $_POST["papellido"];
    $sapellido = $_POST["sapellido"];
    $pnombre = $_POST["pnombre"];
    $snombre = $_POST["snombre"];
    $nacimiento = $_POST["nacimiento"];
    $edad = $_POST["edad"];
    $sexo = $_POST["sexo"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $encargado = $_POST["encargado"];
    $tramite = $_POST["tramite"];
    $causaatencion = $_POST["causaatencion"];
    $observaciones = $_POST["observaciones"];

      $data = [
        "tpdocumento" => $tpdocumento,
        "documento" => $documento,
        "fecha" => $fecha,
        "regimen" => $regimen,
        "papellido" => $papellido,
        "sapellido" => $sapellido,
        "pnombre" => $pnombre,
        "snombre" => $snombre,
        "nacimiento" => $nacimiento,
        "edad" => $edad,
        "sexo" => $sexo,
        "direccion" => $direccion,
        "telefono" => $telefono,
        "encargado" => $encargado,
        "tramite" => $tramite,
        "causaatencion" => $causaatencion,
        "observaciones" => $observaciones,
      ];
    break;

  case 'listar_remision':
    echo "hola";
    break;
  case 'registrar_usuario':
      echo "hola";
      // $tpdocumento = $_POST["tpdocumento"];
      // $documento = $_POST["documento"];
      // $nombre = $_POST["nombre"];
      // $nit = $_POST["nit"];
      // $usuario = $_POST["usuario"];
      // $password1 = $_POST["password1"];

      // $data = [
      //   "tpdocumento" => $tpdocumento,
      //   "documento" => $documento,
      //   "nombre" => $nombre,
      //   "nit" => $nit,
      //   "usuario" => $usuario,
      //   "password1" => $password1
      // ];

    break;
    case 'validar_usuario_cambio':
      $documento = $_POST["txtNumdoc"];

      $consulta = $contrareferencia->validarUsuarioCambio($documento);
      if($consulta) {
        echo json_encode($consulta);
      }
      else {
        echo 0;
      }
    break;
}

?>