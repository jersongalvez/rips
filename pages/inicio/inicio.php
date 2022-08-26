<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////       ARCHIVO DE INICIO       ////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
///////      VISTA INICIAL CUANDO SE CARGA LA PAGINA POR PRIMERA VEZ      //////
////////////////////////////////////////////////////////////////////////////////

session_start();

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    require '../header.php';
    require '../menu.php';
    ?>

    <div class="section" id="wrapper">
        <div class="container">
            <div class="columns">
                <div class="column is-12 has-text-centered" style="margin-top: 50px;">
                    <figure class="image is-128x128 is-inline-block">
                        <img src="../../public/img/logo_pijaos.png">
                    </figure>

                    <p class="title is-3">Pijaos Salud EPSI</p>
                    <p class="subtitle is-5">
                        Sistema de transacciones WEB para prestadores
                    </p>
                </div>
            </div>

            <div class="columns">
                <div class="column is-12 has-text-centered">
                    <p class="has-text-weight-semibold" style="margin-top: 30px;">
                        <?php echo $_SESSION["NOM_PRESTADOR"]; ?>
                    </p>

                    <p class="has-text-weight-semibold">
                        <?php echo $_SESSION["CLA_PRESTADOR"]; ?>
                    </p>
                </div>
            </div>

            <?php if ($_SESSION['PWD_USER'] == 0) { ?>
                <div class="columns has-text-centered" style="margin-top: 30px;">
                    <div class="column is-3 has-background-white-ter">
                        <p>
                            <a class="has-text-grey-darker" <?php echo ($_SESSION["val_rips"] == 1) ? "href='../rips/rips.php'" : "" ?>>
                                <i class="zmdi zmdi-file-text zmdi-hc-4x <?php echo ($_SESSION["val_rips"] == 0) ? "mdc-text-grey" : "" ?>"></i><br> 
                                Validación Rips
                            </a>
                        </p>
                    </div>

                    &nbsp;

                    <div class="column is-3 has-background-white-ter">
                        <p>
                            <a class="has-text-grey-darker" <?php echo ($_SESSION["consultar_prefactura"] == 1) ? "href='../liquidacion_capita/liquidacion_mensual.php'" : "" ?>>
                                <i class="zmdi zmdi-file-plus zmdi-hc-4x <?php echo ($_SESSION["consultar_prefactura"] == 0) ? "mdc-text-grey" : "" ?>"></i><br> 
                                Liquidación Cápita
                            </a>
                        </p>
                    </div>

                    &nbsp;

                    <div class="column is-3 has-background-white-ter">
                        <p>
                            <a class="has-text-grey-darker" <?php echo ($_SESSION["consulta_afi"] == 1) ? "href='../afiliado/consulta_afiliado.php'" : "" ?>>
                                <i class="zmdi zmdi-account-box zmdi-hc-4x <?php echo ($_SESSION["consulta_afi"] == 0) ? "mdc-text-grey" : "" ?>"></i><br> 
                                Consultar afiliados
                            </a>
                        </p>
                    </div>

                    &nbsp; 

                    <div class="column is-3 has-background-white-ter">
                        <p>
                            <a class="has-text-grey-darker" <?php echo ($_SESSION["autorizaciones"] == 1) ? "href='../autorizacion/consulta_autorizacion.php'" : "" ?>>
                                <i class="zmdi zmdi-case-check zmdi-hc-4x <?php echo ($_SESSION["autorizaciones"] == 0) ? "mdc-text-grey" : "" ?>"></i><br> 
                                Consultar autorizaciones
                            </a>
                        </p>
                    </div>
                </div>
            <?php } else { ?>

                <div class="columns">
                    <div class="column is-12">
                        <article class="message is-danger">
                            <div class="message-body has-text-centered">
                                El <strong>usuario y/o contraseña</strong> no pueden ser el mismo. Digite una nueva clave entre cuatro y quince caracteres e inicie sesión nuevamente.
                            </div>
                        </article>

                        <form name="formulario" id="formulario" method="POST">
                            <div class="columns">
                                <div class="column">
                                    <div class="columns">
                                        <div class="column is-5">
                                            <input type="hidden" name="cod_usuario" id="cod_usuario" value="<?php echo $_SESSION['COD_USUARIO'] ?>"> 
                                            <div class="field">
                                                <label class="label has-text-left">Contraseña (*):</label>
                                                <div class="control has-icons-right">
                                                    <input class="input is-hovered" type="password" name="clave1" id="clave1" minlength="4" maxlength="15" autocomplete="off" required>
                                                    <span class="icon is-small is-right">
                                                        <i class="zmdi zmdi-key"></i>
                                                    </span>
                                                </div>
                                                <p class="help has-text-left">Ingrese la contraseña</p>
                                            </div>
                                        </div>

                                        <div class="column is-5">
                                            <div class="field">
                                                <label class="label has-text-left">Confirmar contraseña (*):</label>
                                                <div class="control has-icons-right">
                                                    <input class="input is-hovered" type="password" name="clave2" id="clave2" minlength="4" maxlength="15" autocomplete="off" required>
                                                    <span class="icon is-small is-right">
                                                        <i class="zmdi zmdi-key"></i>
                                                    </span>
                                                </div>
                                                <p class="help has-text-left">Confirme la contraseña</p>
                                            </div>
                                        </div>

                                        <div class="column">
                                            <p class="buttons">
                                                <button class="button is-info is-hovered is-fullwidth" type="submit" style="margin-top: 30px;" id="btnGuardar">
                                                    <i class="zmdi zmdi-edit"></i> &nbsp; Actualizar
                                                </button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php
    require '../footer.php';
    ?>

    <script type="text/javascript" src="../scripts/inicio.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>

    <?php
}








