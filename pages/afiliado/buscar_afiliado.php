<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////           VISTA DATOS AFILIADOS          ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////  AMBITO: CONSULTA DE AFILIADOS  ////////////////////////
////////     VISTA PRINCIPAL DONDE SE MUESTRAN LOS DATOS DEL AFILIADO      /////
////////////////////////////////////////////////////////////////////////////////

session_start();

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    require '../header.php';

    $clave_vps = "6Lex0CwaAAAAANKe2Cq2h6M2dsFOYH3hx3Z0Srpw";
    //LocalHost
    //$clave_vps = "6LfK0KYZAAAAAG_VQZXhyBcJJgNx1ruNfAXG-bix";
    //$clave_local = "6LfK0KYZAAAAAG_VQZXhyBcJJgNx1ruNfAXG-bix";
    //zona horaria colombia
    date_default_timezone_set("America/Bogota");

    //validadores de los campos
    require_once '../../controladores/funciones_generales.php';

    //consulta a la tabla AFILIADOSSUB
    require_once '../../modelos/Consulta_afiliado.php';


    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST["txtNumdoc"]) && isset($_POST["tipdoc"])) {

            $response = $_POST["g-recaptcha-response"];


            if (!empty($response)) {

                $secret = $clave_vps;
                $ip = $_SERVER['REMOTE_ADDR'];
                $respuestaValidación = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$ip");

                //var_dump($respuestaValidación);

                $jsonResponde = json_decode($respuestaValidación);

                if ($jsonResponde->success) {

                    //entrará aquí cuando todo sea correcto	
                    $nu_documento = validar_campo($_POST["txtNumdoc"]);
                    $tip_doc = validar_campo($_POST["tipdoc"]);
                    $time = time();

                    $fila = Consulta_afiliado::getAfiliado($nu_documento, $tip_doc);

                    if ($fila === false) {

                        //si el usuario no esta registrado se envia el error al formulario de consulta y se despliega un modal 
                        echo "<script>$(function () {  envio_error(7); });</script>";
                    } else {

                        require '../menu.php';
                        ?>

                        <div class="section has-background-white-ter" id="wrapper" style="margin-top: 30px;">
                            <div class="container">
                                <div class="columns">
                                    <div class="column is-12 has-text-centered">
                                        <figure class="image is-96x96 is-inline-block">
                                            <img src="../../public/img/logo_pijaos.png">
                                        </figure>

                                        <p class="title is-4">Pijaos Salud EPSI</p>
                                        <p class="subtitle is-5">
                                            Información de afiliados en la base de datos única de la entidad 
                                        </p>
                                    </div> 
                                </div>

                                <div class="columns" style="margin-top: 15px;">
                                    <div class="column is-2 is-hidden-mobile">&nbsp;</div>

                                    <div class="column is-8">
                                        <p class="title is-6 has-text-grey-dark">
                                            <span class="icon is-small">
                                                <i class="fas fa-user-tag"></i>
                                            </span>
                                            &nbsp; Información básica del afiliado:
                                        </p>
                                    </div> 

                                    <div class="column is-2 is-hidden-mobile">&nbsp;</div>
                                </div>

                                <div class="columns">   
                                    <div class="column is-2 is-hidden-mobile">&nbsp;</div>

                                    <div class="column is-8">
                                        <div class="table-container">
                                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                                <tbody>
                                                    <tr class="has-background-primary">
                                                        <th class="has-text-centered has-text-success">Columnas</th>
                                                        <th class="has-text-centered has-text-success">Datos Usuario</th>
                                                    </tr>
                                                    <tr>
                                                        <td>TIPO DE IDENTIFICACIÓN</td>
                                                        <td><?php echo $fila["TIP_DOCUMENTO_BEN"]; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>NÚMERO DE IDENTIFICACION</td>
                                                        <td><?php echo $fila["NUM_DOCUMENTO_BEN"]; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>NOMBRES</td>
                                                        <td><?php echo $fila["NOMBRES"]; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>APELLIDOS</td>
                                                        <td><?php echo $fila["APELLIDOS"]; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>DEPARTAMENTO</td>
                                                        <td><?php echo $fila["DEPARTAMENTO"]; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>CIUDAD</td>
                                                        <td><?php echo $fila["CIUDAD"]; ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>   
                                    </div>

                                    <div class="column is-2 is-hidden-mobile">&nbsp;</div>
                                </div>

                                <!--
                                <?php //$prestador = Consulta_afiliado::getIpsprimaria($nu_documento, $tip_doc);  ?>

                                <div class="columns" style="margin-top: 15px;">   
                                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>

                                    <div class="column is-10">


                                        <div class="container">
                                            <div class="card is-fullwidth">
                                                <header class="card-header">
                                                    <p class="card-header-title has-text-weight-semibold">
                                                        <span class="icon is-small">
                                                            <i class="fas fa-stethoscope"></i>
                                                        </span>
                                                        &nbsp; IPS primarias asignadas:
                                                    </p>
                                                    <a class="card-header-icon card-toggle">
                                                        <i class="fa fa-angle-down"></i>
                                                    </a>
                                                </header>
                                                <div class="card-content has-background-white-ter is-hidden">
                                                    <div class="content">


                                                        <div class="table-container">
                                                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                                                <thead>
                                                                    <tr class="has-background-primary">
                                                                        <th class="has-text-centered has-text-success">Nombre Prestador</th>
                                                                        <th class="has-text-centered has-text-success">Dirección</th>
                                                                    </tr>
                                                                </thead>

                                <?php /* if ($prestador) {
                                  foreach ((array) $prestador as $tabla) {
                                  ?>
                                  <tbody>
                                  <td><?php echo $tabla['NOM_PRESTADOR']; ?></td>
                                  <td><?php echo $tabla['DIRECCION']; ?></td>
                                  </tbody>
                                  <?php
                                  }
                                  } else {
                                  ?>
                                  <tbody>
                                  <td colspan="2" class="has-text-centered">SIN RESULTADOS</td>
                                  </tbody>
                                  <?php } */ ?>
                                                            </table>
                                                        </div> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>
                                </div>
                                -->
                            </div>

                            <div class="container" style="margin-top: 30px;">
                                <div class="columns">
                                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>

                                    <div class="column is-10">
                                        <p class="title is-6 has-text-grey-dark">
                                            <span class="icon is-small">
                                                <i class="fas fa-hospital-user"></i>
                                            </span>
                                            &nbsp; Datos de afiliación:
                                        </p>
                                    </div> 

                                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>
                                </div>

                                <div class="columns">   
                                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>

                                    <div class="column is-10">
                                        <div class="table-container">
                                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                                <tbody>
                                                    <tr class="has-background-primary">
                                                        <th class="has-text-centered has-text-success">Estado</th>
                                                        <th class="has-text-centered has-text-success">Regimen</th>
                                                        <?php if ($fila["TIPO_AFILIADO"] !== null) { ?>
                                                            <th class="has-text-centered has-text-success">Tipo Afiliado</th>
                                                        <?php } ?>
                                                        <th class="has-text-centered has-text-success">Nivel</th>
                                                        <th class="has-text-centered has-text-success">Fecha Impresión</th>
                                                        <th class="has-text-centered has-text-success">Estación Origen</th>
                                                    </tr>

                                                    <tr>
                                                        <td class="has-text-centered"><?php echo estado($fila["EST_AFILIADO"], $fila["CARTERA"]); ?></td>
                                                        <td class="has-text-centered"><?php echo regimen($fila["REGIMEN"]); ?></td>
                                                        <?php if ($fila["TIPO_AFILIADO"] !== null) { ?>
                                                            <td class="has-text-centered"><?php echo $fila["TIPO_AFILIADO"]; ?></td>
                                                        <?php } ?>
                                                        <td class="has-text-centered"><?php echo $fila["NOM_ESTRATO_AFILIADO"]; ?></td>
                                                        <td class="has-text-centered"><?php echo date("d/m/Y H:i:s", $time); ?></td> 
                                                        <td class="has-text-centered"><?php echo getRealIP(); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>   
                                    </div>

                                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>
                                </div>
                            </div>

                            <div class="container" style="margin-top: 10px;">
                                <div class="columns">
                                    <div class="column is-12">
                                        <p class="has-text-justified is-size-7">
                                            Esta información se debe utilizar por parte de las entidades y los prestadores de 
                                            servicios de salud, como complemento al marco legal y técnico definido y nunca como 
                                            motivo para denegar la prestación de los servicios de salud a los usuarios.
                                            Si usted encuentra una inconsistencia en la información publicada en ésta página, por 
                                            favor remítase a las oficinas de <strong>PIJAOS SALUD EPS INDÍGENA</strong> y solicite la 
                                            corrección de la información inconsistente sobre su afiliación. Una vez realizada 
                                            esta actividad, la EPS debe remitir la novedad correspondiente a la ADRES, conforme 
                                            lo establece la normatividad vigente.
                                        </p>
										<br>
										<p class="has-text-justified is-size-7 font-weight-bold text-success">
										  <strong>
											En cumplimiento del articulo 2.1.1.10 - Deberes de las Personas - La informacion
											registrada en esta pagina es reflejo de lo reportado por nuestros afiliados. Los
											datos consultados debido a las novedades en los procesos de aseguramiento y la dinamica
											de la base de datos esta sujeta a cambios que pueden alterar el estado del usuario de manera
											retroactiva a lo reflejado en la consulta.
											<br>
											Si usted encuentra una inconsistencia en la informacion publicada en esta pagina
											por favor informar a la EPSI de acuerdo a lo reglamentado en el anexo tecnico 1
											del decreto 3047 de 2008 y/o  informar al afiliado para que se acerque a la oficina
											de la EPSI mas cercana a su lugar de residencia.
									      </strong>
										</p>
                                        <p class="has-text-centered" style="margin-top: 10px;">                                       
                                            <a href="consulta_afiliado.php">
                                                <span class="icon is-small">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                                Nueva consulta
                                            </a>

                                            &nbsp;

                                            <a onclick="imprimir();">
                                                <span class="icon is-small">
                                                    <i class="zmdi zmdi-print"></i>
                                                </span>
                                                Imprimir página
                                            </a>
                                        </p>
                                    </div> 
                                </div>
                            </div>
                        </div>

                        <?php
                        require '../footer.php';
                    }
                } else {

                    //si hay un error en la captcha se envia el error al formulario de consulta y se despliega un modal 
                    echo "<script>$(function () {  envio_error(8); });</script>";
                }
            } else {

                //si hay un error en la captcha se envia el error al formulario de consulta y se despliega un modal 
                echo "<script>$(function () {  envio_error(8); });</script>";
            }
        } else {

            //si los input cambian de name se envia el error al formulario de consulta y se despliega un modal 
            echo "<script>$(function () {  envio_error(9); });</script>";
        }
    } else {
        //si el metodo de envio es  se envia el error al formulario de consulta y se despliega un modal 
        echo "<script>$(function () {  envio_error(9); });</script>";
    }
}
?>


<script type="text/javascript" src="../../public/js/jquery.PrintArea.js"></script>
<script type="text/javascript" src="../scripts/consulta_usuario.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>


