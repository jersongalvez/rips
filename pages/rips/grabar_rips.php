<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////      ARCHIVO DE INSERCION DE DATOS       ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
///////////   GRABA LA INFORMACION CONTENIDA EN LOS ARCHIVOS PLANOS  ///////////
////////////////////////////////////////////////////////////////////////////////
//valida que halla sesion de usuario iniciada

session_start();

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    //datos de conexion
    require_once '../../config/Conexion.php';

    //validadores generales
    require_once '../../controladores/funciones_generales.php';

    //zona horaria colombia
    date_default_timezone_set("America/Bogota");

    require '../header.php';



    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST["datos_grabar"]) && isset($_SESSION["datos_rips"]) && isset($_SESSION["archivos"])) {

            //datos recobidos de la matriz
            $ruta_archivos = $_SESSION["datos_rips"]["f_temporal"];
            $cod_prestador = $_SESSION["datos_rips"]["cod_prestador"];
            $remision      = $_SESSION["datos_rips"]["remision"];
            $tip_entidad   = "NIT";
            $num_entidad   = $_SESSION["datos_rips"]["nit"];
            $nom_prestador = substr($_SESSION["datos_rips"]["nom_prestador"], 0, 50);
            $fec_remision  = $_SESSION["datos_rips"]["fec_remision"];
            $modalidad     = $_SESSION["datos_rips"]["modalidad"];
			$cont_vista     = $_SESSION["datos_rips"]["cont_vista"];
            $archivos      = $_SESSION["archivos"];
            $fec_actual    = time();
            $cod_usuario   = $_SESSION["COD_USUARIO"];


            //elimino las variables de session
            unset($_SESSION["datos_rips"]);
            unset($_SESSION["archivos"]);


            //verifico que el directorio no exista, si es el caso se elimina
            if (is_dir($ruta_archivos)) {

                require '../menu.php';
                ?>

                <div class="container" id="wrapper" style="margin-bottom: 30px;">

                    <div class="column has-background-light" style="margin-top: 70px;">
                        <div class="columns is-12" style="padding: 13px;">
                            <figure class="image is-96x96">
                                <img src="../../public/img/logo_pijaos.png">
                            </figure>

                            <p class="title is-4" style="margin-top: 35px;">
                                &nbsp; Registro de datos de la remisión:
                            </p>
                        </div>
                    </div>

                    <div class="columns">       
                        <div class="column is-12"> 
                            <div class="columns is-gap" style="margin-top: 10px;">
                                <div class="column">
                                    <div class="field">
                                        <label class="label has-text-left">Código del prestador</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" value="<?php echo $cod_prestador; ?>" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-archive"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="column">
                                    <div class="field">
                                        <label class="label has-text-left">NIT del prestador</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" value="<?php echo (int) $num_entidad; ?>" readonly="true">
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
                                            <input class="input has-background-light is-focused" type="text" value="<?php echo $tip_entidad; ?>" readonly="true">
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
                                            <input class="input has-background-light is-focused" type="text" value="<?php echo $nom_prestador; ?>" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-city"></i>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="column">
                                    <div class="field">
                                        <label class="label has-text-left">Fecha de registro</label>
                                        <div class="control has-icons-left">
                                            <input class="input has-background-light is-focused" type="text" value="<?php echo date("d/m/Y H:i:s", $fec_actual); ?>" readonly="true">
                                            <span class="icon is-small is-left">
                                                <i class="zmdi zmdi-calendar"></i>
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <p class="subtitle is-5 has-text-weight-semibold has-text-centered" style="margin-top: 15px; margin-bottom: 25px;">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-format-list-bulleted"></i>
                                </span>
                                Archivos relacionados
                            </p>
                        </div>
                    </div>

                    <div class="columns">   
                        <div class="column is-1 is-hidden-mobile">&nbsp;</div>

                        <div class="column is-10">
                            <div class="columns is-gap">
                                <div class="column">
                                    <p>
                                        <span class="icon is-small">
                                            <i class="zmdi zmdi-forward"></i>
                                        </span>
                                        Registro archivo de control:
                                    </p> 
                                </div>

                                <div class="column">
                                    <div class="barra">
                                        <div class="progreso" id="bct"> <div class="porcentaje" id="pct"></div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns is-gap">
                                <div class="column">
                                    <p>
                                        <span class="icon is-small">
                                            <i class="zmdi zmdi-forward"></i>
                                        </span>
                                        Registro archivo de transacciones:
                                    </p> 
                                </div>

                                <div class="column">
                                    <div class="barra">
                                        <div class="progreso" id="baf"> <div class="porcentaje" id="paf"></div></div>
                                    </div>
                                </div>
                            </div>


                            <div class="columns is-gap">
                                <div class="column">
                                    <p>
                                        <span class="icon is-small">
                                            <i class="zmdi zmdi-forward"></i>
                                        </span>
                                        Registro archivo de usuarios:
                                    </p> 
                                </div>

                                <div class="column">
                                    <div class="barra">
                                        <div class="progreso" id="bus"> <div class="porcentaje" id="pus"></div></div>
                                    </div>
                                </div>
                            </div>


                            <?php if (in_array('AC', $archivos)) { ?>
                                <div class="columns is-gap">
                                    <div class="column">
                                        <p>
                                            <span class="icon is-small">
                                                <i class="zmdi zmdi-forward"></i>
                                            </span>
                                            Registro archivo de consultas:
                                        </p> 
                                    </div>

                                    <div class="column">
                                        <div class="barra">
                                            <div class="progreso" id="bac"> <div class="porcentaje" id="pac"></div></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <?php if (in_array('AD', $archivos)) { ?>
                                <div class="columns is-gap">
                                    <div class="column">
                                        <p>
                                            <span class="icon is-small">
                                                <i class="zmdi zmdi-forward"></i>
                                            </span>
                                            Registro archivo de datos agrupados:
                                        </p> 
                                    </div>

                                    <div class="column">
                                        <div class="barra">
                                            <div class="progreso" id="bad"> <div class="porcentaje" id="pad"></div></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <?php if (in_array('AH', $archivos)) { ?>
                                <div class="columns is-gap">
                                    <div class="column">
                                        <p>
                                            <span class="icon is-small">
                                                <i class="zmdi zmdi-forward"></i>
                                            </span>
                                            Registro archivo de hospitalizaciones:
                                        </p> 
                                    </div>

                                    <div class="column">
                                        <div class="barra">
                                            <div class="progreso" id="bah"> <div class="porcentaje" id="pah"></div></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <?php if (in_array('AM', $archivos)) { ?>
                                <div class="columns is-gap">
                                    <div class="column">
                                        <p>
                                            <span class="icon is-small">
                                                <i class="zmdi zmdi-forward"></i>
                                            </span>
                                            Registro archivo de medicamentos:
                                        </p> 
                                    </div>

                                    <div class="column">
                                        <div class="barra">
                                            <div class="progreso" id="bam"> <div class="porcentaje" id="pam"></div></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <?php if (in_array('AN', $archivos)) { ?>
                                <div class="columns is-gap">
                                    <div class="column">
                                        <p>
                                            <span class="icon is-small">
                                                <i class="zmdi zmdi-forward"></i>
                                            </span>
                                            Registro archivo de nacimientos:
                                        </p> 
                                    </div>

                                    <div class="column">
                                        <div class="barra">
                                            <div class="progreso" id="ban"> <div class="porcentaje" id="pan"></div></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <?php if (in_array('AP', $archivos)) { ?>
                                <div class="columns is-gap">
                                    <div class="column">
                                        <p>
                                            <span class="icon is-small">
                                                <i class="zmdi zmdi-forward"></i>
                                            </span>
                                            Registro archivo de procedimientos:
                                        </p> 
                                    </div>

                                    <div class="column">
                                        <div class="barra">
                                            <div class="progreso" id="bap"> <div class="porcentaje" id="pap"></div></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <?php if (in_array('AT', $archivos)) { ?>
                                <div class="columns is-gap">
                                    <div class="column">
                                        <p>
                                            <span class="icon is-small">
                                                <i class="zmdi zmdi-forward"></i>
                                            </span>
                                            Registro archivo de otros servicios:
                                        </p> 
                                    </div>

                                    <div class="column">
                                        <div class="barra">
                                            <div class="progreso" id="bat"> <div class="porcentaje" id="pat"></div></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <?php if (in_array('AU', $archivos)) { ?>
                                <div class="columns is-gap">
                                    <div class="column">
                                        <p>
                                            <span class="icon is-small">
                                                <i class="zmdi zmdi-forward"></i>
                                            </span>
                                            Registro archivo de urgencias:
                                        </p> 
                                    </div>

                                    <div class="column">
                                        <div class="barra">
                                            <div class="progreso" id="bau"> <div class="porcentaje" id="pau"></div></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="column is-1 is-hidden-mobile">&nbsp;</div>
                    </div>

                    <div class="columns" style="margin-top: 15px;">
                        <div class="column is-12 has-text-centered" id="estado">
                        </div>
                    </div>

                    <div class="columns" style="margin-top: 15px;">
                        <div class="column is-12 has-text-centered">
                            <button class="button is-info" disabled="true" id="reporte" onclick="generar_pdf('<?php echo $cod_prestador ?>', '<?php echo $remision ?>')">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-format-list-bulleted"></i>
                                </span>
                                <span> Informe de resultados </span>
                            </button>

                            &nbsp;&nbsp;


                            <button class="button  is-primary" disabled="true" id="nuevo_rips" onclick="nuevo()">
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-home"></i>
                                </span>
                                <span>  Registrar otro archivo </span>
                            </button>
                        </div>
                    </div>
                </div>

                <?php
                require '../footer.php';

                //archivos a Grabar
                //asigno y cuento el total de registros del CT
                $control = file($ruta_archivos . "/CT" . $remision . ".txt");
                $total_ct = count($control);
                //asigno y cuento el total de registros del AF
                $transacicones = file($ruta_archivos . "/AF" . $remision . ".txt");
                $total_af = count($transacicones);
                //asigno y cuento el total de registros del US
                $usuarios = file($ruta_archivos . "/US" . $remision . ".txt");
                $total_us = count($usuarios);
                //Asigno el resto de archivos siempre y cuando esten relacionados en el array $archivos
                (in_array('AC', $archivos)) ? $consultas         = file($ruta_archivos . "/AC" . $remision . ".txt") : "";
                (in_array('AD', $archivos)) ? $datos_agrupados   = file($ruta_archivos . "/AD" . $remision . ".txt") : "";
                (in_array('AH', $archivos)) ? $hospitalizaciones = file($ruta_archivos . "/AH" . $remision . ".txt") : "";
                (in_array('AM', $archivos)) ? $medicamentos      = file($ruta_archivos . "/AM" . $remision . ".txt") : "";
                (in_array('AN', $archivos)) ? $r_nacidos         = file($ruta_archivos . "/AN" . $remision . ".txt") : "";
                (in_array('AP', $archivos)) ? $procedimientos    = file($ruta_archivos . "/AP" . $remision . ".txt") : "";
                (in_array('AT', $archivos)) ? $otros_servicios   = file($ruta_archivos . "/AT" . $remision . ".txt") : "";
                (in_array('AU', $archivos)) ? $urgencias         = file($ruta_archivos . "/AU" . $remision . ".txt") : "";

                //elimino el directorio temporal
                eliminar_directorio($ruta_archivos);

                //instancia a la clase conexion
                $conexion = new conexion();

                //invoco el metodo conectar
                $transaccion = $conexion->conexionPDO();

                //añadir la excepcion
                $transaccion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //permitir la insercion de acentos y eñes
                $transaccion->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_SYSTEM);

                //inicio la transaccion
                $transaccion->beginTransaction();


                try {

                    //Insert informacion de los rips en: RECEPCIONRIPS
                    $queryrr = "INSERT INTO RECEPCIONRIPS WITH(ROWLOCK)(COD_PRESTADOR, NUM_REMISION, TIPO_ENTIDAD, NUM_ENTIDAD, NOM_PRESTADOR, "
                            . "FEC_REMISION, COD_USUARIO, FEC_CARGUE, EST_VALIDACION, MOD_CONTRATO, NUM_CONTRATOPRES_PLATAFORMA) VALUES (:cod_prestador, :remision, :tip_entidad, "
                            . ":num_entidad, :nom_prestador, :fec_remision, :cod_usuario, :fec_cargue, 'SE', :mod_contrato, :cont_vista);";

                    $resultadorr = $transaccion->prepare($queryrr);

                    $fecha = date("d/m/Y H:i:s", $fec_actual);

                    $resultadorr->bindParam(":cod_prestador", $cod_prestador);
                    $resultadorr->bindParam(":remision", $remision);
                    $resultadorr->bindParam(":tip_entidad", $tip_entidad);
                    $resultadorr->bindParam(":num_entidad", $num_entidad);
                    $resultadorr->bindParam(":nom_prestador", $nom_prestador);
                    $resultadorr->bindParam(":fec_remision", $fec_remision);
                    $resultadorr->bindParam(":cod_usuario", $cod_usuario);
                    $resultadorr->bindParam(":fec_cargue", $fecha);
                    $resultadorr->bindParam(":mod_contrato", $modalidad);
					$resultadorr->bindParam(":cont_vista", $cont_vista);
                    $resultadorr->execute();

                    //Insert archivo de control en: ARC_CONTROL
                    foreach ($control as $posicion => $linea) {

                        $linea = trim($linea);
                        $datact = explode(',', $linea);

                        $queryct = "INSERT INTO ARC_CONTROL WITH(ROWLOCK)(COD_PRESTADOR, NUM_REMISION, COD_ARCHIVO, FECHA_REMISION, TOTAL_ARCHIVOS) "
                                . "VALUES (:cod_prestador, :remision, :cod_archivo, :fecha_remision, :total_archivos);";


                        $resultadoct = $transaccion->prepare($queryct);

                        $resultadoct->bindParam(":cod_prestador", $datact[0]);
                        $resultadoct->bindParam(":remision", $remision);
                        $resultadoct->bindParam(":cod_archivo", $datact[2]);
                        $resultadoct->bindParam(":fecha_remision", $datact[1]);
                        $resultadoct->bindParam(":total_archivos", $datact[3]);

                        $resultadoct->execute();

                        $posicion++;
                        $porcentaje = round(($posicion / $total_ct) * 100, 0);
                        ?>
                        <script>
                            $("#bct").width("<?php echo $porcentaje; ?>%");
                            $("#pct").html("<?php echo $porcentaje; ?>%");
                        </script>
                        <?php
                        flush_buffers();
                        usleep(5000);
                    }


                    // Insert archivo de trasacciones en: TRANSACCION_SERV 
                    foreach ($transacicones as $posicion => $linea) {

                        $linea = trim($linea);
                        $dataaf = explode(',', $linea);


                        $queryaf = "INSERT INTO TRANSACCION_SERV WITH(ROWLOCK)(COD_PRESTADOR, NUM_REMISION, CODIGO_ENTIDAD, COD_CONTRATO, "
                                . "FECHA_FACTURA, RAZON_SOCIAL, NUM_FACTURA, FECHA_INICIO, NOMBRE_ENTIDAD, NUM_DOC_PRES, "
                                . "TIPO_DOC_PRES, FECHA_FINAL, VAL_COPAGO, VAL_COMISION, VAL_DESCUENTO, VAL_PAGO_ENTIDAD, "
                                . "PLAN_BENEFICIOS, NUMERO_POLIZA) VALUES (:cod_prestador, :num_remision, :codigo_entidad, "
                                . ":cod_contrato, :fecha_factura, :razon_social, :num_factura, :fecha_inicio, :nombre_entidad, "
                                . ":num_doc_pres, :tipo_doc_pres, :fecha_final, :val_copago, :val_comision, :val_descuento, "
                                . ":val_pago_entidad, :plan_beneficios, :numero_poliza)";


                        $resultadoaf = $transaccion->prepare($queryaf);

                        $resultadoaf->bindParam(":cod_prestador", $dataaf[0]);
                        $resultadoaf->bindParam(":num_remision", $remision);
                        $resultadoaf->bindParam(":codigo_entidad", $dataaf[8]);
                        $resultadoaf->bindParam(":cod_contrato", $dataaf[10]);
                        $resultadoaf->bindParam(":fecha_factura", $dataaf[5]);
                        $resultadoaf->bindParam(":razon_social", $dataaf[1]);
                        $resultadoaf->bindParam(":num_factura", $dataaf[4]);
                        $resultadoaf->bindParam(":fecha_inicio", $dataaf[6]);
                        $resultadoaf->bindParam(":nombre_entidad", $dataaf[9]);
                        $resultadoaf->bindParam(":num_doc_pres", $dataaf[3]);
                        $resultadoaf->bindParam(":tipo_doc_pres", $dataaf[2]);
                        $resultadoaf->bindParam(":fecha_final", $dataaf[7]);
                        $resultadoaf->bindParam(":val_copago", $dataaf[13]);
                        $resultadoaf->bindParam(":val_comision", $dataaf[14]);
                        $resultadoaf->bindParam(":val_descuento", $dataaf[15]);
                        $resultadoaf->bindParam(":val_pago_entidad", $dataaf[16]);
                        $resultadoaf->bindParam(":plan_beneficios", $dataaf[11]);
                        $resultadoaf->bindParam(":numero_poliza", $dataaf[12]);

                        $resultadoaf->execute();

                        $posicion++;
                        $porcentaje = round(($posicion / $total_af) * 100, 0);
                        ?>
                        <script>
                            $("#baf").width("<?php echo $porcentaje; ?>%");
                            $("#paf").html("<?php echo $porcentaje; ?>%");
                        </script>
                        <?php
                        flush_buffers();
                        usleep(5000);
                    }


                    // Insert archivo de usuarios en: ARCHIVO_USUARIO 
                    foreach ($usuarios as $posicion => $linea) {

                        $linea = trim($linea);
                        $dataus = explode(',', $linea);


                        $queryus = "INSERT INTO ARCHIVO_USUARIO WITH(ROWLOCK)(COD_PRESTADOR, NUM_REMISION, TIPO_DOC_USUARIO, "
                                . "NUM_DOC_USUARIO, CODIGO_ENTIDAD, COD_DEPTO, COD_MUNICIPIO, EDAD, OTRO_NOM, "
                                . "PRIMER_APEL, PRIMER_NOM, SEGUNDO_APEL, SEXO, TIPO_USUARIO, UNIDAD_MED_EDAD, "
                                . "ZONA_RES) "
                                . "VALUES (:cod_prestador, :remision, :tip_doc, :num_doc, :cod_entidad, :cod_depto, "
                                . ":cod_muni, :edad, :seg_nom, :pri_ape, :pri_nom, :seg_ape, :sexo, :tip_usu, "
                                . ":u_med_edad, :zona_res);";


                        $resultadous = $transaccion->prepare($queryus);

                        $resultadous->bindParam(":cod_prestador", $cod_prestador);
                        $resultadous->bindParam(":remision", $remision);
                        $resultadous->bindParam(":tip_doc", $dataus[0]);
                        $resultadous->bindParam(":num_doc", $dataus[1]);
                        $resultadous->bindParam(":cod_entidad", $dataus[2]);
                        $resultadous->bindParam(":cod_depto", $dataus[11]);
                        $resultadous->bindParam(":cod_muni", $dataus[12]);
                        $resultadous->bindParam(":edad", $dataus[8]);
                        $resultadous->bindParam(":seg_nom", $dataus[7]);
                        $resultadous->bindParam(":pri_ape", $dataus[4]);
                        $resultadous->bindParam(":pri_nom", $dataus[6]);
                        $resultadous->bindParam(":seg_ape", $dataus[5]);
                        $resultadous->bindParam(":sexo", $dataus[10]);
                        $resultadous->bindParam(":tip_usu", $dataus[3]);
                        $resultadous->bindParam(":u_med_edad", $dataus[9]);
                        $resultadous->bindParam(":zona_res", $dataus[13]);

                        $resultadous->execute();


                        $posicion++;
                        $porcentaje = round(($posicion / $total_us) * 100, 0);
                        ?>
                        <script>
                            $("#bus").width("<?php echo $porcentaje; ?>%");
                            $("#pus").html("<?php echo $porcentaje; ?>%");
                        </script>
                        <?php
                        flush_buffers();
                        usleep(5000);
                    }


                    // Insert archivo de consultas en: ARCH_CONSULTA 
                    if (isset($consultas)) {

                        $total_ac = count($consultas);

                        foreach ($consultas as $posicion => $linea) {

                            $linea = trim($linea);
                            $dataac = explode(',', $linea);


                            $queryac = "INSERT INTO ARCH_CONSULTA WITH(ROWLOCK)(TIPO_DOC_USUARIO, NUM_DOC_USUARIO, COD_PRESTADOR, "
                                    . "NUM_REMISION, FINALIDAD_CONS, CAUS_EXTERNA, COD_DIAG_PPAL, COD_DIAG_RE1, COD_DIAG_RE2, "
                                    . "COD_DIAG_RE3, TIPO_DIAG_PPAL, VAL_CONSULTA, VAL_CUO_MOD, VAL_PAGAR, NUM_AUTORIZACION, "
                                    . "NUM_FACTURA, FECHA_CONSULTA, COD_CONSULTA) "
                                    . "VALUES (:tip_doc, :num_doc, :cod_prestador, :remision, :fin_consulta, :cau_ext, :cod_diaprin, "
                                    . ":cod_diarel, :cod_diare2, :cod_diare3, :tip_diagpp, :val_consulta, :val_cuomod, :val_neto,"
                                    . ":num_aut, :num_factura, :fec_consulta, :cod_consulta);";


                            $resultadoac = $transaccion->prepare($queryac);

                            $resultadoac->bindParam(":tip_doc", $dataac[2]);
                            $resultadoac->bindParam(":num_doc", $dataac[3]);
                            $resultadoac->bindParam(":cod_prestador", $dataac[1]);
                            $resultadoac->bindParam(":remision", $remision);
                            $resultadoac->bindParam(":fin_consulta", $dataac[7]);
                            $resultadoac->bindParam(":cau_ext", $dataac[8]);
                            $resultadoac->bindParam(":cod_diaprin", $dataac[9]);
                            $resultadoac->bindParam(":cod_diarel", $dataac[10]);
                            $resultadoac->bindParam(":cod_diare2", $dataac[11]);
                            $resultadoac->bindParam(":cod_diare3", $dataac[12]);
                            $resultadoac->bindParam(":tip_diagpp", $dataac[13]);
                            $resultadoac->bindParam(":val_consulta", $dataac[14]);
                            $resultadoac->bindParam(":val_cuomod", $dataac[15]);
                            $resultadoac->bindParam(":val_neto", $dataac[16]);
                            $resultadoac->bindParam(":num_aut", $dataac[5]);
                            $resultadoac->bindParam(":num_factura", $dataac[0]);
                            $resultadoac->bindParam(":fec_consulta", $dataac[4]);
                            $resultadoac->bindParam(":cod_consulta", $dataac[6]);

                            $resultadoac->execute();

                            $posicion++;
                            $porcentaje = round(($posicion / $total_ac) * 100, 0);
                            ?>
                            <script>
                                $("#bac").width("<?php echo $porcentaje; ?>%");
                                $("#pac").html("<?php echo $porcentaje; ?>%");
                            </script>
                            <?php
                            flush_buffers();
                            usleep(5000);
                        }
                    }


                    // Insert archivo desc agrupada en: DECRIP_SERV 
                    if (isset($datos_agrupados)) {

                        $total_ad = count($datos_agrupados);

                        foreach ($datos_agrupados as $posicion => $linea) {

                            $linea = trim($linea);
                            $dataad = explode(',', $linea);


                            $queryad = "INSERT INTO DECRIP_SERV WITH(ROWLOCK)(COD_PRESTADOR, NUM_REMISION, COD_CONCEPTO, NUM_FACTURA, CANTIDAD, VAL_UNITARIO, VAL_CONCEPTO) "
                                    . "VALUES (:cod_prestador, :remision, :cod_concepto, :num_factura, :cantidad, :val_unitario, :val_total);";


                            $resultadoad = $transaccion->prepare($queryad);

                            $resultadoad->bindParam(":cod_prestador", $dataad[1]);
                            $resultadoad->bindParam(":remision", $remision);
                            $resultadoad->bindParam(":cod_concepto", $dataad[2]);
                            $resultadoad->bindParam(":num_factura", $dataad[0]);
                            $resultadoad->bindParam(":cantidad", $dataad[3]);
                            $resultadoad->bindParam(":val_unitario", $dataad[4]);
                            $resultadoad->bindParam(":val_total", $dataad[5]);

                            $resultadoad->execute();

                            $posicion++;
                            $porcentaje = round(($posicion / $total_ad) * 100, 0);
                            ?>
                            <script>
                                $("#bad").width("<?php echo $porcentaje; ?>%");
                                $("#pad").html("<?php echo $porcentaje; ?>%");
                            </script>
                            <?php
                            flush_buffers();
                            usleep(5000);
                        }
                    }


                    // Insert archivo hospitalizaciones en: ARCH_HOSPITALIZACION 
                    if (isset($hospitalizaciones)) {

                        $total_ah = count($hospitalizaciones);

                        foreach ($hospitalizaciones as $posicion => $linea) {

                            $linea = trim($linea);
                            $dataah = explode(',', $linea);


                            $queryah = "INSERT INTO ARCH_HOSPITALIZACION WITH(ROWLOCK)(TIPO_DOC_USUARIO, NUM_DOC_USUARIO, COD_PRESTADOR, NUM_REMISION, NUM_FACTURA, VIA_INGRESO, "
                                    . "CAUS_EXTERNA, COD_DIAG_PPAL_ING, COD_DIAG_PPAL_EGR, COD_DIAG_EGR1, COD_DIAG_EGR2, COD_DIAG_EGR3, COD_DIAG_COMP, ESTADO_SALIDA, "
                                    . "COD_DIAG_MUERTE, FECHA_EGRE_INST, NUM_AUTORIZACION, FECHA_INGRESO_INST, HORA_INGRESO, HORA_EGRESO) VALUES (:tip_doc, :num_documento,"
                                    . ":cod_prestador, :remision, :num_factura, :via_ingreso, :cau_externa, :diag_priin, :diag_prineg, :diag_egre1, :diag_egre2, :diag_egre3,"
                                    . ":diag_comp, :est_salida, :cod_muerte, :fec_salida, :num_autorizacion, :fec_ingreso, :hora_ingreso, :hora_salida);";


                            $resultadoah = $transaccion->prepare($queryah);

                            $resultadoah->bindParam(":tip_doc", $dataah[2]);
                            $resultadoah->bindParam(":num_documento", $dataah[3]);
                            $resultadoah->bindParam(":cod_prestador", $dataah[1]);
                            $resultadoah->bindParam(":remision", $remision);
                            $resultadoah->bindParam(":num_factura", $dataah[0]);
                            $resultadoah->bindParam(":via_ingreso", $dataah[4]);
                            $resultadoah->bindParam(":cau_externa", $dataah[8]);
                            $resultadoah->bindParam(":diag_priin", $dataah[9]);
                            $resultadoah->bindParam(":diag_prineg", $dataah[10]);
                            $resultadoah->bindParam(":diag_egre1", $dataah[11]);
                            $resultadoah->bindParam(":diag_egre2", $dataah[12]);
                            $resultadoah->bindParam(":diag_egre3", $dataah[13]);
                            $resultadoah->bindParam(":diag_comp", $dataah[14]);
                            $resultadoah->bindParam(":est_salida", $dataah[15]);
                            $resultadoah->bindParam(":cod_muerte", $dataah[16]);
                            $resultadoah->bindParam(":fec_salida", $dataah[17]);
                            $resultadoah->bindParam(":num_autorizacion", $dataah[7]);
                            $resultadoah->bindParam(":fec_ingreso", $dataah[5]);
                            $resultadoah->bindParam(":hora_ingreso", $dataah[6]);
                            $resultadoah->bindParam(":hora_salida", $dataah[18]);

                            $resultadoah->execute();

                            $posicion++;
                            $porcentaje = round(($posicion / $total_ah) * 100, 0);
                            ?>
                            <script>
                                $("#bah").width("<?php echo $porcentaje; ?>%");
                                $("#pah").html("<?php echo $porcentaje; ?>%");
                            </script>
                            <?php
                            flush_buffers();
                            usleep(5000);
                        }
                    }

                    // Insert archivo medicamentos en: ARCH_MEDICAMENTOS 
                    if (isset($medicamentos)) {

                        $total_am = count($medicamentos);

                        foreach ($medicamentos as $posicion => $linea) {

                            $linea = trim($linea);
                            $dataam = explode(',', $linea);


                            $queryam = "INSERT INTO ARCH_MEDICAMENTOS WITH(ROWLOCK)(TIPO_DOC_USUARIO, NUM_DOC_USUARIO, COD_PRESTADOR, NUM_REMISION, COD_MEDICAMENTO, CONCENTRACION_MED, "
                                    . "FORMA_FARMA, NOMBRE_MED, UNIDAD_MED_MED, VAL_UNI_MED, TIPO_MEDICAMENTO, NUM_FACTURA, NUM_UNIDADES , VAL_TOTAL_MED, NUM_AUTORIZACION) "
                                    . "VALUES (:tip_doc, :num_doc, :cod_prestador, :remision, :cod_medicamento, :concentracion, :forma_farma, :nom_medicamento, :uni_medida, "
                                    . ":val_uni_med, :tip_medicamento, :num_factura, :num_unidades, :val_tot_med, :num_autorizacion);";


                            $resultadoam = $transaccion->prepare($queryam);

                            $resultadoam->bindParam(":tip_doc", $dataam[2]);
                            $resultadoam->bindParam(":num_doc", $dataam[3]);
                            $resultadoam->bindParam(":cod_prestador", $dataam[1]);
                            $resultadoam->bindParam(":remision", $remision);
                            $resultadoam->bindParam(":cod_medicamento", $dataam[5]);
                            $resultadoam->bindParam(":concentracion", $dataam[9]);
                            $resultadoam->bindParam(":forma_farma", $dataam[8]);
                            $resultadoam->bindParam(":nom_medicamento", $dataam[7]);
                            $resultadoam->bindParam(":uni_medida", $dataam[10]);
                            $resultadoam->bindParam(":val_uni_med", $dataam[12]);
                            $resultadoam->bindParam(":tip_medicamento", $dataam[6]);
                            $resultadoam->bindParam(":num_factura", $dataam[0]);
                            $resultadoam->bindParam(":num_unidades", $dataam[11]);
                            $resultadoam->bindParam(":val_tot_med", $dataam[13]);
                            $resultadoam->bindParam(":num_autorizacion", $dataam[4]);

                            $resultadoam->execute();

                            $posicion++;
                            $porcentaje = round(($posicion / $total_am) * 100, 0);
                            ?>
                            <script>
                                $("#bam").width("<?php echo $porcentaje; ?>%");
                                $("#pam").html("<?php echo $porcentaje; ?>%");
                            </script>
                            <?php
                            flush_buffers();
                            usleep(5000);
                        }
                    }


                    // Insert archivo R nacidos en: ARCH_NACIDOS 
                    if (isset($r_nacidos)) {

                        $total_an = count($r_nacidos);

                        foreach ($r_nacidos as $posicion => $linea) {

                            $linea = trim($linea);
                            $dataan = explode(',', $linea);

                            $queryan = "INSERT INTO ARCH_NACIDOS WITH(ROWLOCK)(COD_PRESTADOR, NUM_REMISION, NUM_FACTURA, EDAD_GESTACION, CONTROL_PRENATAL, SEXO ,PESO, COD_DIAG_NACIDO, "
                                    . "CAUS_MUER_NACIDO, FECHA_MUERTE_NACIDO, FECHA_NACIMIENTO, NUM_DOC_MADRE, TIP_DOC_MADRE, HORA_NACIMIENTO, HORA_MUERTE) VALUES ("
                                    . ":cod_prestador, :remision, :num_factura, :edad_gest, :cont_prenatal, :sexo, :peso, :diagnostico, :cau_bas_muerte, :fec_muerte, :fec_nacimiento,"
                                    . ":num_documento, :tip_documento, :hora_nac, :hora_muerte);";


                            $resultadoan = $transaccion->prepare($queryan);

                            $resultadoan->bindParam(":cod_prestador", $dataan[1]);
                            $resultadoan->bindParam(":remision", $remision);
                            $resultadoan->bindParam(":num_factura", $dataan[0]);
                            $resultadoan->bindParam(":edad_gest", $dataan[6]);
                            $resultadoan->bindParam(":cont_prenatal", $dataan[7]);
                            $resultadoan->bindParam(":sexo", $dataan[8]);
                            $resultadoan->bindParam(":peso", $dataan[9]);
                            $resultadoan->bindParam(":diagnostico", $dataan[10]);
                            $resultadoan->bindParam(":cau_bas_muerte", $dataan[11]);
                            $resultadoan->bindParam(":fec_muerte", $dataan[12]);
                            $resultadoan->bindParam(":fec_nacimiento", $dataan[4]);
                            $resultadoan->bindParam(":num_documento", $dataan[3]);
                            $resultadoan->bindParam(":tip_documento", $dataan[2]);
                            $resultadoan->bindParam(":hora_nac", $dataan[5]);
                            $resultadoan->bindParam(":hora_muerte", $dataan[13]);

                            $resultadoan->execute();

                            $posicion++;
                            $porcentaje = round(($posicion / $total_an) * 100, 0);
                            ?>
                            <script>
                                $("#ban").width("<?php echo $porcentaje; ?>%");
                                $("#pan").html("<?php echo $porcentaje; ?>%");
                            </script>
                            <?php
                            flush_buffers();
                            usleep(5000);
                        }
                    }


                    // Insert archivo procedimientos en: ARCH_PROCEDIMIENTOS
                    if (isset($procedimientos)) {

                        $total_ap = count($procedimientos);

                        foreach ($procedimientos as $posicion => $linea) {

                            $linea = trim($linea);
                            $dataap = explode(',', $linea);


                            $queryap = "INSERT INTO ARCH_PROCEDIMIENTOS WITH(ROWLOCK)(TIPO_DOC_USUARIO, NUM_DOC_USUARIO, COD_PRESTADOR, NUM_REMISION, AMBITO_RELACION, FINALIDAD_PROC, "
                                    . "PERSONAL_SALUD, COD_DIAG_PPAL, COD_DIAG_RELA, FORMA_ACTO_QUIR, VAL_PROCEDIMIETO, NUM_FACTURA, COD_PROCEDIMIENTO, FECHA_PROC, NUM_AUTORIZACION, "
                                    . "COD_COMPLICACION) VALUES (:tip_doc, :num_doc, :cod_prestador, :remision, :ambito, :finalidad, :per_atiende, :diag_prin, :diag_rel, :acto_quirurgico,"
                                    . ":val_procedimiento, :num_factura, :cod_procedimiento, :fec_procedimiento, :num_autorizacion, :complicacion);";


                            $resultadoap = $transaccion->prepare($queryap);

                            $resultadoap->bindParam(":tip_doc", $dataap[2]);
                            $resultadoap->bindParam(":num_doc", $dataap[3]);
                            $resultadoap->bindParam(":cod_prestador", $dataap[1]);
                            $resultadoap->bindParam(":remision", $remision);
                            $resultadoap->bindParam(":ambito", $dataap[7]);
                            $resultadoap->bindParam(":finalidad", $dataap[8]);
                            $resultadoap->bindParam(":per_atiende", $dataap[9]);
                            $resultadoap->bindParam(":diag_prin", $dataap[10]);
                            $resultadoap->bindParam(":diag_rel", $dataap[11]);
                            $resultadoap->bindParam(":acto_quirurgico", $dataap[13]);
                            $resultadoap->bindParam(":val_procedimiento", $dataap[14]);
                            $resultadoap->bindParam(":num_factura", $dataap[0]);
                            $resultadoap->bindParam(":cod_procedimiento", $dataap[6]);
                            $resultadoap->bindParam(":fec_procedimiento", $dataap[4]);
                            $resultadoap->bindParam(":num_autorizacion", $dataap[5]);
                            $resultadoap->bindParam(":complicacion", $dataap[12]);

                            $resultadoap->execute();

                            $posicion++;
                            $porcentaje = round(($posicion / $total_ap) * 100, 0);
                            ?>
                            <script>
                                $("#bap").width("<?php echo $porcentaje; ?>%");
                                $("#pap").html("<?php echo $porcentaje; ?>%");
                            </script>
                            <?php
                            flush_buffers();
                            usleep(5000);
                        }
                    }


                    // Insert archivo procedimientos en: ARCH_SERVICIOS 
                    if (isset($otros_servicios)) {

                        $total_at = count($otros_servicios);

                        foreach ($otros_servicios as $posicion => $linea) {

                            $linea = trim($linea);
                            $dataat = explode(',', $linea);


                            $queryat = "INSERT INTO ARCH_SERVICIOS WITH(ROWLOCK)(TIPO_DOC_USUARIO, NUM_DOC_USUARIO, COD_PRESTADOR, NUM_REMISION, VAL_UNI_MATERIAL, NUM_FACTURA, TIPO_SERVICIO, "
                                    . "COD_SERVICIO, NOM_SERVICIO, CANTIDAD, VAL_TOTAL_MATERIAL, NUM_AUTORIZACION) VALUES (:tip_doc, :num_doc, :cod_prestador, :remision, "
                                    . ":val_unitario, :num_factura, :tip_servicio, :cod_servicio, :nom_servicio, :cantidad, :val_total, :num_autorizacion);";


                            $resultadoat = $transaccion->prepare($queryat);

                            $resultadoat->bindParam(":tip_doc", $dataat[2]);
                            $resultadoat->bindParam(":num_doc", $dataat[3]);
                            $resultadoat->bindParam(":cod_prestador", $dataat[1]);
                            $resultadoat->bindParam(":remision", $remision);
                            $resultadoat->bindParam(":val_unitario", $dataat[9]);
                            $resultadoat->bindParam(":num_factura", $dataat[0]);
                            $resultadoat->bindParam(":tip_servicio", $dataat[5]);
                            $resultadoat->bindParam(":cod_servicio", $dataat[6]);
                            $resultadoat->bindParam(":nom_servicio", $dataat[7]);
                            $resultadoat->bindParam(":cantidad", $dataat[8]);
                            $resultadoat->bindParam(":val_total", $dataat[10]);
                            $resultadoat->bindParam(":num_autorizacion", $dataat[4]);

                            $resultadoat->execute();

                            $posicion++;
                            $porcentaje = round(($posicion / $total_at) * 100, 0);
                            ?>
                            <script>
                                $("#bat").width("<?php echo $porcentaje; ?>%");
                                $("#pat").html("<?php echo $porcentaje; ?>%");
                            </script>
                            <?php
                            flush_buffers();
                            usleep(5000);
                        }
                    }


                    // Insert archivo urgencias en: ARCH_URGENCIAS 
                    if (isset($urgencias)) {

                        $total_au = count($urgencias);

                        foreach ($urgencias as $posicion => $linea) {

                            $linea = trim($linea);
                            $dataau = explode(',', $linea);

                            $queryau = "INSERT INTO ARCH_URGENCIAS WITH(ROWLOCK)(TIPO_DOC_USUARIO, NUM_DOC_USUARIO ,COD_PRESTADOR, NUM_REMISION, NUM_FACTURA, FECHA_ING_OBSERV, "
                                    . "CAUS_EXTERNA, COD_DIAG_SALIDA, DESTINO_SALIDA, ESTADO_SALIDA, CAUS_MUERTE_URGENCIA, FECHA_SALIDA_OBS, COD_DIAG_RE1_SAL, "
                                    . "COD_DIAG_RE2_SAL, COD_DIAG_RE3_SAL, NUM_AUTORIZACION, HORA_INGRESO, HORA_SALIDA) VALUES (:tip_doc, :num_doc, :cod_prestador,"
                                    . ":remision, :num_factura, :fec_consulta, :cau_externa, :diag_salida, :destino_usu, :est_salida, :cau_bas_muerte, :fec_salida,"
                                    . ":diag_sal1, :diag_sal2, :diag_sal3, :num_autorizacion, :hora_ingreso, :hora_salida);";


                            $resultadoau = $transaccion->prepare($queryau);

                            $resultadoau->bindParam(":tip_doc", $dataau[2]);
                            $resultadoau->bindParam(":num_doc", $dataau[3]);
                            $resultadoau->bindParam(":cod_prestador", $dataau[1]);
                            $resultadoau->bindParam(":remision", $remision);
                            $resultadoau->bindParam(":num_factura", $dataau[0]);
                            $resultadoau->bindParam(":fec_consulta", $dataau[4]);
                            $resultadoau->bindParam(":cau_externa", $dataau[7]);
                            $resultadoau->bindParam(":diag_salida", $dataau[8]);
                            $resultadoau->bindParam(":destino_usu", $dataau[12]);
                            $resultadoau->bindParam(":est_salida", $dataau[13]);
                            $resultadoau->bindParam(":cau_bas_muerte", $dataau[14]);
                            $resultadoau->bindParam(":fec_salida", $dataau[15]);
                            $resultadoau->bindParam(":diag_sal1", $dataau[9]);
                            $resultadoau->bindParam(":diag_sal2", $dataau[10]);
                            $resultadoau->bindParam(":diag_sal3", $dataau[11]);
                            $resultadoau->bindParam(":num_autorizacion", $dataau[6]);
                            $resultadoau->bindParam(":hora_ingreso", $dataau[5]);
                            $resultadoau->bindParam(":hora_salida", $dataau[16]);

                            $resultadoau->execute();

                            $posicion++;
                            $porcentaje = round(($posicion / $total_au) * 100, 0);
                            ?>
                            <script>
                                $("#bau").width("<?php echo $porcentaje; ?>%");
                                $("#pau").html("<?php echo $porcentaje; ?>%");
                            </script>
                            <?php
                            flush_buffers();
                            usleep(5000);
                        }
                    }

                    $transaccion->commit();

                    //habilito opciones si el insert se completa
                    echo "<script>$(function () { exito();  });</script>";
                } catch (PDOException $error) {

                    $transaccion->rollBack();
                    //echo $error->getMessage();
                    //Muestro un error si no se pueden grabar los datos
                    echo "<script>$(function () { error();  });</script>";
                }
            } else {
                //no se encuentra el directorio temporal de archivos
                echo "<script>$(function () {  envio_error(10); });</script>";
            }
        } else {

            //no se encuenta el input oculto
            echo "<script>$(function () {  envio_error(6); });</script>";
        }
    } else {
        //no se encuenta el method general
        echo "<script>$(function () {  envio_error(6); });</script>";
    }
    ?>  <script type="text/javascript" src="../scripts/grabar_rips.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script> <?php
}
//////////////////// METODOS DEL ARCHIVO GRABAR_RIPS.PHP ////////////////////////

/**
 * Metodos que limpia el buffer para ir actualizando la pagina y cargando las barras
 * de estado de registro
 */
function flush_buffers() {
    //ob_end_flush();
    ob_flush();
    flush();
    //ob_start();
}
