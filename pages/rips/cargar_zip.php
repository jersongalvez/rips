<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////       ARCHIVO DE PROCESAMIENTO ZIP       ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
///////  INVOCA LOS METODOS NECESARIOS PARA EL PROCESAMIENTO DE LOS RIPS  //////
////////////////////////////////////////////////////////////////////////////////

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    //Incluyo el validador de control
    require_once '../../controladores/control.php';

    //valido que llegue el archivo y numero de remision
    if (isset($_FILES["archivo"]) || isset($_POST['txtNremision'])) {


        //datos del arhivo
        $nombre_archivo  = $_FILES['archivo']['name'];
        $tipo_archivo    = $_FILES['archivo']['type'];
        $tamano_archivo  = $_FILES['archivo']['size'];
        $ruta            = $_FILES["archivo"]["tmp_name"];
        $remision        = validar_campo($_POST['txtNremision']);
        $modalidad       = validar_campo($_POST['smodalidad']);
		//TICKET DE CONTRATOS
		$cont_vista       = validar_campo($_POST['cont_vista']);
        $obtener_rem     = substr($nombre_archivo, 0, -4);
        $arc_encontrados = array();


        //compruebo si las características del archivo son las definidas
        if ((!strpos($tipo_archivo, "zip")) || ($tamano_archivo > 10000000) || ($obtener_rem !== $remision)) {

            //Envio el frm indicando que las caracteristicas del envio no son correctas
            echo "<script>$(function () { envio_novedades(1); });</script>";
        } else {

            //cambio de nombre del fichero para que sea unico
            $nombre_archivo = uniqid('RI_', true);


            //ruta en la que se descomprime los txt
            $ficheros_temporales = '../../ficheros_temporales/' . $nombre_archivo;

            //pregunto si la ruta existe, si es falso elimina el directorio temporal
            if (!is_dir($ficheros_temporales)) {

                //creo el directorio con el nombre del archivo zip cargado
                mkdir($ficheros_temporales, 0777);


                // Función descomprimir ficheros en formato ZIP
                //crear un array para guardar el nombre de los archivos que contiene el ZIP
                //$nombresFichZIP = array();
                $zip = new ZipArchive;

                //abro la ruta y descomprimo
                if ($zip->open($ruta) === TRUE) {

                    //en caso de algun error se descomenta para verificar las rutas donde se alamacenan los zip
                    /* for ($i = 0; $i < $zip->numFiles; $i++) {
                      //obtenemos ruta que tendrán los documentos cuando los descomprimamos
                      echo $nombresFichZIP['rut_temp'][$i] = $ficheros_temporales . '/' . $zip->getNameIndex($i) . '<br>';
                      } */

                    //descomprimimos zip
                    $zip->extractTo($ficheros_temporales);
                    $zip->close();

                    //cuento los archivos cargados
                    $archivos_detectados = 0;

                    //cuento los archivos principales
                    $archivos_principales = 0;

                    //cuento los errores del zip
                    $errorres_zip = 0;

                    // abro el directorio por primera vez y leo el  contenido de la carpeta
                    $directorio = opendir($ficheros_temporales);

                    //Inicio validacion inicial de archivos
                    ?>

                    <div class=" section container" style="padding-top: 0px !important;">
                        <div class="columns" id="msg_val_inicial">       
                            <div class="column is-12"> 
                                <?php
                                //ciclo recorre los archivos ya descomprimidos y valida su extencion y nombre
                                while ($archivo = readdir($directorio)) { //obtenemos un archivo y luego otro sucesivamente
                                    if (is_dir($archivo)) {//verificamos si es o no un directorio
                                        //echo "[" . $archivo . "] → "; //de ser un directorio lo envolvemos entre corchetes
                                    } else {

                                        if ($archivo !== '..') {

                                            $archivos_detectados++;

                                            //valido si todas las extenciones son txt
                                            if ((pathinfo($archivo, PATHINFO_EXTENSION) != 'txt') && (pathinfo($archivo, PATHINFO_EXTENSION) != 'TXT')) {
                                                echo '<p> - El archivo <strong>' . $archivo . '</strong> no tiene la extension requerida. </p>';
                                                $errorres_zip++;
                                            }

                                            //valido que los archivos principales esten
                                            if (comparar_idA(id_archivo($archivo))) {
                                                if (id_archivo($archivo) == 'CT' || id_archivo($archivo) == 'AF' || id_archivo($archivo) == 'US') {
                                                    $archivos_principales++;
                                                }

                                                //Asigno a un arreglo todas las facturas
                                                array_push($arc_encontrados, id_archivo($archivo));
                                            } else {
                                                echo '<p> - El archivo <strong>' . $archivo . '</strong> no tiene un ID valido. </p>';
                                                $errorres_zip++;
                                            }

                                            //valido que la remison de los archivos sea la misma
                                            if (get_remision($archivo) !== $remision) {
                                                echo '<p> - El archivo <strong>' . $archivo . '</strong> no tiene la remison relacionada. </p>';
                                                $errorres_zip++;
                                            }
                                        }
                                    }
                                }
                                ?>
                            </div> 
                        </div> 
                        <?php
                        //Fin validacion inicial de archivos
                        //cierro el directorio
                        closedir($directorio);

                        //si no hay errores, proceso los archivos principales
                        if ($archivos_detectados > 3 && $archivos_principales === 3 && $errorres_zip === 0) {

                            //se crea la instancia al archivo de control
                            $control = new Control_validador();

                            //abro el ct y valido si el prestador esta registrado
                            if (($prestador = $control->buscar_prestador($ficheros_temporales . '/CT' . $remision . '.txt', 
                                                                         $ficheros_temporales . '/AF' . $remision . '.txt'))) {

                                if ($control->validar_codprestador()) {

                                    //abro el ct y valido si la remision ya se registro
                                    if ($control->buscar_remision($_SESSION["ni_prestador"], $remision)) {

                                        eliminar_directorio($ficheros_temporales);

                                        //Envio el frm indicando que la remision ya esta registrada
                                        echo "<script>$(function () { envio_novedades(2); });</script>";
                                    } else {

                                        //cuento los errores encontrados en los archivos
                                        $total_errores = 0;

                                        //almacena todos los tipos de errores encontrados en 
                                        //los archivos
                                        $_SESSION ["logErrores"] = array();
                                        ?>

                                        <div class="columns">       
                                            <div class="column is-12" id="carga">
                                                <div class="has-text-centered">
                                                    <figure class="image is-96x96 is-inline-block">
                                                        <img src="../../public/img/logo_pijaos.png">
                                                    </figure>

                                                    <p class="title is-4 has-text-centered" style="margin-top: 3px;">
                                                        Resultados de la validación
                                                    </p>
                                                </div>

                                                <div class="columns is-gap" style="margin-top: 30px; ">
                                                    <div class="column">
                                                        <div class="field">
                                                            <label class="label has-text-left">Código del prestador</label>
                                                            <div class="control has-icons-left">
                                                                <input class="input has-background-light is-focused" type="text" value="<?php echo $prestador[0]; ?>" readonly="true">
                                                                <span class="icon is-small is-left">
                                                                    <i class="zmdi zmdi-archive"></i>
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="column">
                                                        <div class="field">
                                                            <label class="label has-text-left">Número de identificación</label>
                                                            <div class="control has-icons-left">
                                                                <input class="input has-background-light is-focused" type="text" value="<?php echo (int) $prestador[1]; ?>" readonly="true">
                                                                <span class="icon is-small is-left">
                                                                    <i class="zmdi zmdi-file"></i>
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="column">
                                                        <div class="field">
                                                            <label class="label has-text-left">Tipo de identificación</label>
                                                            <div class="control has-icons-left">
                                                                <input class="input has-background-light is-focused" type="text" value="NIT" readonly="true">
                                                                <span class="icon is-small is-left">
                                                                    <i class="zmdi zmdi-assignment-o"></i>
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="column">
                                                        <div class="field">
                                                            <label class="label has-text-left">Número de remisión</label>
                                                            <div class="control has-icons-left">
                                                                <input class="input has-background-light is-focused" type="text" value="<?php echo $remision; ?>" readonly="true">
                                                                <span class="icon is-small is-left">
                                                                    <i class="zmdi zmdi-attachment-alt"></i>
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="column">
                                                        <div class="field">
                                                            <label class="label has-text-left">Modalidad</label>
                                                            <div class="control has-icons-left">
                                                                <input class="input has-background-light is-focused" type="text" value="<?php echo modalidad($modalidad); ?>" readonly="true">
                                                                <span class="icon is-small is-left">
                                                                    <i class="zmdi zmdi-receipt"></i>
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="columns is-gap">
                                                    <div class="column is-9">
                                                        <div class="field">
                                                            <label class="label has-text-left">Nombre del prestador</label>
                                                            <div class="control has-icons-left">
                                                                <input class="input has-background-light is-focused" type="text" value="<?php echo $prestador[3]; ?>" readonly="true">
                                                                <span class="icon is-small is-left">
                                                                    <i class="zmdi zmdi-city"></i>
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="column">
                                                        <div class="field">
                                                            <label class="label has-text-left">Fecha de la remisión</label>
                                                            <div class="control has-icons-left">
                                                                <input class="input has-background-light is-focused" type="text" value="<?php echo $prestador[4]; ?>" readonly="true">
                                                                <span class="icon is-small is-left">
                                                                    <i class="zmdi zmdi-calendar"></i>
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <p class="has-text-justified" style="margin-top: 30px; margin-bottom: 30px;">
                                                    A continuación se detallan las novedades encontradas en el proceso de validación si es el caso. De ser así, 
                                                    por favor corrija todos los errores encontrados y cargue nuevamente el archivo comprimido; de lo contrario 
                                                    el sistema no lo dejara grabar la información.
                                                </p>

                                                <div class="tabs is-boxed is-fullwidth" id="encabezado_tabs">
                                                    <ul>
                                                        <?php if (in_array('CT', $arc_encontrados)) { ?>
                                                            <li class="tab is-active" onclick="openTab(event, 'CT')">
                                                                <a id="eticontrol">
                                                                    <span class="icon is-small">
                                                                        <i class="fa fa-tasks"></i>
                                                                    </span>
                                                                    <span>CT</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if (in_array('AF', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'AF')">
                                                                <a id="etitransaccion">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-file-invoice"></i>
                                                                    </span>
                                                                    <span>AF</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if (in_array('US', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'US')">
                                                                <a id="etiusuarios">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-hospital-user"></i>
                                                                    </span>
                                                                    <span>US</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if (in_array('AC', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'AC')">
                                                                <a id="eticonsultas">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-user-md"></i>
                                                                    </span>
                                                                    <span>AC</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if (in_array('AD', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'AD')">
                                                                <a id="etidagrupados">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-briefcase-medical"></i>
                                                                    </span>
                                                                    <span>AD</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if (in_array('AH', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'AH')">
                                                                <a id="etihospitalizacion">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-notes-medical"></i>
                                                                    </span>
                                                                    <span>AH</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if (in_array('AM', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'AM')">
                                                                <a id="etimedicamentos">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-prescription-bottle"></i>
                                                                    </span>
                                                                    <span>AM</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if (in_array('AN', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'AN')">
                                                                <a id="etinacimientos">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-baby-carriage"></i>
                                                                    </span>
                                                                    <span>AN</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>


                                                        <?php if (in_array('AP', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'AP')">
                                                                <a id="etiprocedimientos">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-syringe"></i>
                                                                    </span>
                                                                    <span>AP</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if (in_array('AT', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'AT')">
                                                                <a id="etioservicios">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-stethoscope"></i>
                                                                    </span>
                                                                    <span>AT</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if (in_array('AU', $arc_encontrados)) { ?>
                                                            <li class="tab" onclick="openTab(event, 'AU')">
                                                                <a id="etiurgencias">
                                                                    <span class="icon is-small">
                                                                        <i class="fas fa-procedures"></i>
                                                                    </span>
                                                                    <span>AU</span>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>

                                                <div id="cuerpo_tabs">
                                                    <?php if (in_array('CT', $arc_encontrados)) { ?>
                                                        <div id="CT" class="content-tab" >
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fa fa-tasks"></i>
                                                                </span>
                                                                &nbsp; Archivo de control
                                                            </p>
                                                            <?php
                                                            $Tecontrol = $control->val_control($ficheros_temporales . '/CT' . $remision . '.txt', $ficheros_temporales);
                                                            $total_errores = $total_errores + $Tecontrol;
                                                            echo ($Tecontrol > 0) ? "<script>$('#eticontrol').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if (in_array('AF', $arc_encontrados)) { ?>
                                                        <div id="AF" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-file-invoice"></i>
                                                                </span>
                                                                &nbsp; Archivo de transacciones
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/transaccion.php';
                                                            $transacciones = new Transaccion_validador();
                                                            $Tetransacciones = $transacciones->val_transacciones($ficheros_temporales . '/AF' . $remision . '.txt', $modalidad);
                                                            $total_errores = $total_errores + $Tetransacciones[0];
                                                            echo ($Tetransacciones[0] > 0) ? "<script>$('#etitransaccion').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if (in_array('US', $arc_encontrados)) { ?>
                                                        <div id="US" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-hospital-user"></i>
                                                                </span>
                                                                &nbsp; Archivo de usuarios
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/usuario.php';
                                                            $usuarios = new Usuario_validador();
                                                            $Teusuarios = $usuarios->val_usuarios($ficheros_temporales . '/US' . $remision . '.txt');
                                                            $total_errores = $total_errores + $Teusuarios;
                                                            echo ($Teusuarios > 0) ? "<script>$('#etiusuarios').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if (in_array('AC', $arc_encontrados)) { ?>
                                                        <div id="AC" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-user-md"></i>
                                                                </span>
                                                                &nbsp; Archivo de consultas
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/consulta.php';
                                                            $consultas = new Consulta_validador();
                                                            $Teconsulta = $consultas->val_consultas($ficheros_temporales . '/AC' . $remision . '.txt');
                                                            $total_errores = $total_errores + $Teconsulta;
                                                            echo ($Teconsulta > 0) ? "<script>$('#eticonsultas').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if (in_array('AD', $arc_encontrados)) { ?>
                                                        <div id="AD" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-briefcase-medical"></i>
                                                                </span>
                                                                &nbsp; Archivo descripción agrupada
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/descagrupada.php';
                                                            $dagrupadas = new Descagrupada_validador();
                                                            $Teagrupado = $dagrupadas->val_dagrupada($ficheros_temporales . '/AD' . $remision . '.txt');
                                                            $total_errores = $total_errores + $Teagrupado;
                                                            echo ($Teagrupado > 0) ? "<script>$('#etidagrupados').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if (in_array('AH', $arc_encontrados)) { ?>
                                                        <div id="AH" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-notes-medical"></i>
                                                                </span>
                                                                &nbsp; Archivo de hospitalizaciones
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/hospitalizacion.php';
                                                            $hospitalizaciones = new Hospitalizacion_validador();
                                                            $Tehospitalizacion = $hospitalizaciones->val_hospitalizacion($ficheros_temporales . '/AH' . $remision . '.txt');
                                                            $total_errores = $total_errores + $Tehospitalizacion;
                                                            echo ($Tehospitalizacion > 0) ? "<script>$('#etihospitalizacion').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if (in_array('AM', $arc_encontrados)) { ?>
                                                        <div id="AM" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-prescription-bottle"></i>
                                                                </span>
                                                                &nbsp; Archivo de medicamentos
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/medicamentos.php';
                                                            $medicamentos = new Medicamentos_validador();
                                                            $Temedicamento = $medicamentos->val_medicamentos($ficheros_temporales . '/AM' . $remision . '.txt');
                                                            $total_errores = $total_errores + $Temedicamento;
                                                            echo ($Temedicamento > 0) ? "<script>$('#etimedicamentos').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if (in_array('AN', $arc_encontrados)) { ?>
                                                        <div id="AN" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-baby-carriage"></i>
                                                                </span>
                                                                &nbsp; Archivo de nacimientos
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/rnacidos.php';
                                                            $nacidos = new Rnacidos_validador();
                                                            $Tenacimiento = $nacidos->val_rnacidos($ficheros_temporales . '/AN' . $remision . '.txt');
                                                            $total_errores = $total_errores + $Tenacimiento;
                                                            echo ($Tenacimiento > 0) ? "<script>$('#etinacimientos').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>

                                                    <?php } ?>

                                                    <?php if (in_array('AP', $arc_encontrados)) { ?>
                                                        <div id="AP" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-syringe"></i>
                                                                </span>
                                                                &nbsp; Archivo de procedimientos
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/procedimiento.php';
                                                            $procedimientos = new Procedimiento_validador();
                                                            $Teprocedimiento = $procedimientos->val_procedimientos($ficheros_temporales . '/AP' . $remision . '.txt');
                                                            $total_errores = $total_errores + $Teprocedimiento;
                                                            echo ($Teprocedimiento > 0) ? "<script>$('#etiprocedimientos').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if (in_array('AT', $arc_encontrados)) { ?>
                                                        <div id="AT" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-stethoscope"></i>
                                                                </span>
                                                                &nbsp; Archivo de otros servicios
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/oservicio.php';
                                                            $oservicios = new Oservicio_validador();
                                                            $Teservicio = $oservicios->val_oservicios($ficheros_temporales . '/AT' . $remision . '.txt');
                                                            $total_errores = $total_errores + $Teservicio;
                                                            echo ($Teservicio > 0) ? "<script>$('#etioservicios').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if (in_array('AU', $arc_encontrados)) { ?>
                                                        <div id="AU" class="content-tab" style="display:none">
                                                            <p class="title is-5 has-text-centered" style="margin-top: 20px;">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-procedures"></i>
                                                                </span>
                                                                &nbsp; Archivo de urgencias
                                                            </p>
                                                            <?php
                                                            require_once '../../controladores/urgencia.php';
                                                            $urgencias = new Urgencia_validador();
                                                            $Teurgencia = $urgencias->val_urgencias($ficheros_temporales . '/AU' . $remision . '.txt');
                                                            $total_errores = $total_errores + $Teurgencia;
                                                            echo ($Teurgencia > 0) ? "<script>$('#etiurgencias').addClass('has-text-danger');</script>" : "";
                                                            ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        if ($total_errores > 0) {

                                            //oculto el formulario
                                            echo "<script>$('#pantalla_rips').hide();</script>";
                                            //Posiciono el foco en la pantalla de resultados
                                            echo "<script> $(document).ready(function () { document.getElementById('carga').scrollIntoView({block: 'start', behavior: 'smooth'}); }); </script>";
                                            ?>
                                            <div class="columns" style="margin-top: 25px; margin-bottom: 10px;" id="total_error">  
                                                <div class="column is-half is-offset-one-quarter">
                                                    <article class="message is-danger">
                                                        <div class="message-body has-text-centered">

                                                            <p>Se encontraron <strong><?php echo $total_errores; ?></strong> errores en los archivos planos.</p>

                                                            <div class="columns is-centered" style="margin-top: 0.5px;">

                                                                <div class="column has-text-centered ">
                                                                    <button class="button is-danger is-small" onclick="log_txt('<?php echo implode(",", $_SESSION ["logErrores"]) ?>')">
                                                                        <span class="icon is-small">
                                                                            <i class="zmdi zmdi-file-text"></i>
                                                                        </span>
                                                                        <span> Descargar </span>
                                                                    </button>


                                                                    <button class="button is-info is-small" onclick="recarga(0)">
                                                                        <span class="icon is-small">
                                                                            <i class="zmdi zmdi-home"></i>
                                                                        </span>
                                                                        <span> Continuar </span> 
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </article>
                                                </div>
                                            </div>
                                            <?php
                                            limpiar_session();
                                            eliminar_directorio($ficheros_temporales);
                                        } else {

                                            #Comentar en produccion
                                            //eliminar_directorio($ficheros_temporales);

                                            echo "<script>$('#pantalla_rips').hide();</script>";
                                            echo "<script>$('#encabezado_tabs').hide();</script>";
                                            echo "<script>$('#cuerpo_tabs').hide();</script>";
                                            echo "<script>
                                                        $(document).ready(function () {
                                                            document.getElementById('carga').scrollIntoView(true);
                                                        });
                                                        </script>";

                                            $_SESSION ["datos_rips"] = array(
                                                "f_temporal"    => $ficheros_temporales,
                                                "cod_prestador" => $prestador[0],
                                                "remision"      => $remision,
                                                "nit"           => $prestador[1],
                                                "nom_prestador" => $prestador[3],
                                                "fec_remision"  => $prestador[4],
                                                "modalidad"     => $modalidad,
												"cont_vista"    => $cont_vista
                                            );

                                            $_SESSION ["archivos"] = $arc_encontrados;
                                            ?> 

                                            <div class="columns" style="margin-bottom: 10px;" id="grabar_datos">  
                                                <div class="column is-12">
                                                    <article class="message">
                                                        <div class="message-header">
                                                            <p>Esta remisión no presenta errores, verifique la siguiente información:</p>
                                                        </div>
                                                        <div class="message-body">
                                                            <p class="has-text-justified">
                                                                La remisión <strong><?php echo $remision ?></strong> no tiene novedades en su estructura y contenido, por favor verifique 
                                                                los siguientes valores y de clic en el botón <strong>“Grabar información”</strong>. Una vez grabados los datos no se podrán
                                                                modificar los mismos.
                                                            </p>

                                                            <div class="columns" style="margin-top: 5px;">   
                                                                <div class="column is-3 has-text-centered">
                                                                    <p><strong>Nº de facturas</strong></p>
                                                                    <?php echo count($_SESSION["facturas"]); ?>
                                                                </div>

                                                                <div class="column is-3 has-text-centered">
                                                                    <p><strong>Total Copagos </strong></p>
                                                                    <?php echo '$' . formatearNumero($Tetransacciones[1]); ?>
                                                                </div>

                                                                <div class="column is-3 has-text-centered">
                                                                    <p><strong>Total Comisiones </strong></p>
                                                                    <?php echo '$' . formatearNumero($Tetransacciones[2]); ?>
                                                                </div>

                                                                <div class="column is-3 has-text-centered">
                                                                    <p><strong>Total Descuentos </strong></p>
                                                                    <?php echo '$' . formatearNumero($Tetransacciones[3]); ?>
                                                                </div>
                                                            </div>

                                                            <div class="columns">   
                                                                <div class="column is-12 has-text-centered">
                                                                    <p><strong>Total neto según RIPS </strong></p>
                                                                    <?php echo '$' . formatearNumero($Tetransacciones[4]); ?>
                                                                </div>
                                                            </div>

                                                            <div class="columns">   
                                                                <div class="column is-12 has-text-centered">
                                                                    <button class="button is-info" onclick="envio_rips('<?php echo $remision; ?>', '<?php echo modalidad($modalidad); ?>');" id="grabar">
                                                                        <span class="icon is-small">
                                                                            <i class="zmdi zmdi-floppy"></i>
                                                                        </span>
                                                                        <span>
                                                                            Grabar información - Remisión: <?php echo $remision ?>
                                                                        </span>
                                                                    </button> 

                                                                    &nbsp; &nbsp;

                                                                    <button class="button is-primary" onclick="recarga(2)">
                                                                        <span class="icon is-small">
                                                                            <i class="zmdi zmdi-search-in-file"></i>
                                                                        </span>
                                                                        <span>
                                                                            Cancelar proceso - Nuevo envío
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </article>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                } else {

                                    eliminar_directorio($ficheros_temporales);

                                    //Envio el frm indicando que el codigo de prestador del usuario no corresponde al del rips
                                    echo "<script>$(function () { envio_novedades(3); });</script>";
                                }
                            } else {

                                eliminar_directorio($ficheros_temporales);

                                //Envio el frm indicando que el prestador no existe
                                echo "<script>$(function () { envio_novedades(4); });</script>";
                            }
                        } else {
                            ?>
                            <div class="columns" style="margin-top: 30px; margin-bottom: 30px;" id="val_inicial">       
                                <div class="column is-half is-offset-one-quarter">
                                    <article class="message is-danger">
                                        <div class="message-header">
                                            <p>Se presentan errores en la validación inicial. Revise los siguientes escenarios:</p>
                                        </div>
                                        <div class="message-body">
                                            <ul style="padding-left: 50px;">
                                                <li type=disc>Los archivos deben tener la extensión .txt</li>
                                                <li type=disc>Los archivos no deben estar contenidos en una o más carpetas</li>
                                                <li type=disc>Los archivos deben tener relacionado el número de la remisión</li>
                                                <li type=disc>El mínimo de archivos solicitados (CT, AF, US, y uno de datos)</li>
                                            </ul>

                                            <p class="has-text-centered" style="margin-top: 20px;">
                                                <a onclick="recarga(1)">
                                                    <span class="icon is-small">
                                                        <i class="zmdi zmdi-home"></i>
                                                    </span>
                                                    Continuar
                                                </a>
                                            </p>
                                        </div>
                                    </article>
                                </div>
                            </div>
                        </div>
                        <?php
                        eliminar_directorio($ficheros_temporales);
                    }
                }

                // validacion si no se puede procesar
                else {

                    eliminar_directorio($ficheros_temporales);

                    //Envio el frm indicando que no se pudo descomprimir el ZIP
                    echo "<script>$(function () { envio_novedades(5); });</script>";
                }
            } else {

                eliminar_directorio($ficheros_temporales);

                //Envio el frm indicando que ese archivo esta cargado en la carpeta temporal
                echo "<script>$(function () { envio_novedades(6); });</script>";
            }
        }
    } else {

        //Envio el frm indicando que no llegaron los datos para iniciar el proceso
        echo "<script>$(function () { envio_novedades(7); });</script>";
    }
}




























