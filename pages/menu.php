<!--Inicio banner-->
<nav class="navbar is-primary is-fixed-top" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <div class="navbar-item" href="../inicio/inicio.php">
            <span class="icon is-small" style="margin-left: 10px">
                <i class="fas fa-laptop fa-2x"></i>
            </span>

            <p class="subtitle is-6 is-size-7-mobile has-text-white-bis has-text-weight-semibold" style="margin-left: 20px">
                Sistema de Transacciones
            </p>
        </div>


        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">

        <div class="navbar-start" style="margin-left: 30px;">
            <a class="navbar-item" href="../inicio/inicio.php" id="inicio">
                <span class="icon is-small">
                    <i class="fas fa-home"></i>
                </span>
                &nbsp; Inicio
            </a>

            <?php
            if ($_SESSION['PWD_USER'] == 0) {

                if ($_SESSION["acceso"] == 1) {
                    ?>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" id="acceso">
                            <span class="icon is-small">
                                <i class="fas fa-folder-open"></i>
                            </span>
                            &nbsp; Acceso
                        </a>

                        <div class="navbar-dropdown">
                            <a class="navbar-item" href="../acceso/prestador.php">
                                <span class="icon is-small">
                                    <i class="fas fa-clinic-medical" aria-hidden="true"></i>
                                </span>
                                &nbsp;&nbsp; Prestadores
                            </a>

                            <a class="navbar-item" href="../acceso/permiso.php">
                                <span class="icon is-small">
                                    <i class="fa fa-suitcase" aria-hidden="true"></i>
                                </span>
                                &nbsp;&nbsp; Permisos
                            </a>

                            <a class="navbar-item" href="../acceso/usuario.php">
                                <span class="icon is-small">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                </span>
                                &nbsp;&nbsp; Usuarios
                            </a>
                        </div>
                    </div>
                <?php } ?>


                <?php if ($_SESSION["val_rips"] == 1 || $_SESSION["filtrar_remision"] == 1) { ?>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" id="val_rips">
                            <span class="icon is-small">
                                <i class="fas fa-file-alt" aria-hidden="true"></i>
                            </span>
                            &nbsp; Validación Rips
                        </a>

                        <div class="navbar-dropdown">
                            <?php if ($_SESSION["val_rips"] == 1) { ?>
                                <a class="navbar-item" href="../rips/rips.php">
                                    <span class="icon is-small">
                                        <i class="far fa-file-archive"></i>
                                    </span>
                                    &nbsp;&nbsp; Cargar remisión
                                </a>
                            <?php } ?>

                            <?php if ($_SESSION["filtrar_remision"] == 1) { ?>
                                <a class="navbar-item" href="../rips/buscar_rips.php">
                                    <span class="icon is-small">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    &nbsp;&nbsp; Buscar Rips
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($_SESSION["autorizaciones"] == 1) { ?>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" id="afiliado">
                            <span class="icon is-small">
                                <i class="zmdi zmdi-case-check"></i>
                            </span>
                            &nbsp; Autorización
                        </a>

                        <div class="navbar-dropdown">
                            <?php if ($_SESSION["autorizaciones"] == 1) { ?>
                                <a class="navbar-item" href="../autorizacion/consulta_autorizacion.php">
                                    <span class="icon is-small">
                                        <i class="zmdi zmdi-assignment-o"></i>
                                    </span>
                                    &nbsp;&nbsp; Consultar autorizaciones
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>



                <?php if ($_SESSION["cargar_capita"] == 1 || $_SESSION["consultar_prefactura"] == 1) { ?>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" id="afiliado">
                            <span class="icon is-small">
                                <i class="zmdi zmdi-file-plus"></i>
                            </span>
                            &nbsp; Liquidación Cápita
                        </a>

                        <div class="navbar-dropdown">
                            <?php if ($_SESSION["cargar_capita"] == 1) { ?>
                                <a class="navbar-item" href="../liquidacion_capita/cargar_archivo_capita.php">
                                    <span class="icon is-small">
                                        <i class="zmdi zmdi-cloud-upload"></i>
                                    </span>
                                    &nbsp;&nbsp; Cargar archivo
                                </a>
                            <?php } ?>

                            <?php if ($_SESSION["consultar_prefactura"] == 1) { ?>
                                <a class="navbar-item" href="../liquidacion_capita/liquidacion_mensual.php">
                                    <span class="icon is-small">
                                        <i class="zmdi zmdi-search-in-page"></i>
                                    </span>
                                    &nbsp;&nbsp; Liquidación mensual contratos capitados
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>



                <?php if ($_SESSION["consulta_afi"] == 1) { ?>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" id="afiliado">
                            <span class="icon is-small">
                                <i class="fas fa-user-friends"></i>
                            </span>
                            &nbsp; Afiliados
                        </a>

                        <div class="navbar-dropdown">
                            <?php if ($_SESSION["consulta_afi"] == 1) { ?>
                                <a class="navbar-item" href="../afiliado/consulta_afiliado.php">
                                    <span class="icon is-small">
                                        <i class="zmdi zmdi-account-box"></i>
                                    </span>
                                    &nbsp;&nbsp; Buscar afiliado
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>

        <div class="navbar-end">
            <div class="navbar-item">

                <div class="navbar-item has-dropdown is-hoverable">
                    <button class="button is-success is-hovered " aria-haspopup="true" aria-controls="dropdown-menu" style="margin-right: 0px;">
                        <span class="icon is-small">
                            <i class="zmdi zmdi-menu"></i>
                        </span>
                        <span>Menú de opciones</span>
                        <span class="icon is-small">
                            <i class="zmdi zmdi-chevron-down"></i>
                        </span>
                    </button>
                    <div class="navbar-dropdown">
                        <a class="navbar-item" onclick="mostrar_perfil(true)">
                            <span class="icon is-small">
                                <i class="zmdi zmdi-account-o"></i>
                            </span>
                            &nbsp; Perfil prestador
                        </a>

                        <a class="navbar-item" href="../../documentos/Manual_usuario_V104.pdf" target="_blank">
                            <span class="icon is-small">
                                <i class="zmdi zmdi-book-image"></i>
                            </span>
                            &nbsp; Manual de usuario
                        </a>

                        <hr class="dropdown-divider">

                        <a class="navbar-item" onclick="salir()">
                            <span class="icon is-small">
                                <i class="fas fa-times-circle"></i>
                            </span>
                            &nbsp; Cerrar sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</nav>

<div class="modal" id="modal_perfil">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title"><strong>Información del prestador</strong></p>
            <button class="delete" aria-label="close" onclick="mostrar_perfil(false)"></button>
        </header>

        <section class="modal-card-body" id="datos_prestador">
            <p class="has-text-justified">
                La siguiente información hace referencia a lo reportado por el prestador al momento de su registro con la entidad, 
                si encuentra alguna inconsistencia repórtelo a la menor brevedad para corregir la información. 
            </p>

            <div class="field" style="margin-top: 30px;">
                <label class="label has-text-left">NIT prestador:</label>
                <div class="control has-icons-left">
                    <input class="input is-hovered" value="<?php echo (int) $_SESSION["NIT_PRESTADOR"]; ?>" readonly>
                    <span class="icon is-small is-left">
                        <i class="zmdi zmdi-assignment-o"></i>
                    </span>
                </div>
            </div>

            <div class="field" style="margin-top: 20px;">
                <label class="label has-text-left">Nombre prestador:</label>
                <div class="control has-icons-left">
                    <input class="input is-hovered" value="<?php echo $_SESSION["NOM_PRESTADOR"]; ?>" readonly>
                    <span class="icon is-small is-left">
                        <i class="zmdi zmdi-city"></i>
                    </span>
                </div>
            </div>

            <div class="field" style="margin-top: 20px;">
                <label class="label has-text-left">Funcionario:</label>
                <div class="control has-icons-left">
                    <input class="input is-hovered" value="<?php echo $_SESSION["COD_USUARIO"] . ' - ' . $_SESSION["NOM_USUARIO"] ?>" readonly>
                    <span class="icon is-small is-left">
                        <i class="zmdi zmdi-pin-account"></i>   
                    </span>
                </div>
            </div>

            <div class="field" style="margin-top: 20px;">
                <label class="label has-text-left">Clase prestador:</label>
                <div class="control has-icons-left">
                    <input class="input is-hovered" value="<?php echo $_SESSION["CLA_PRESTADOR"] ?>" readonly>
                    <span class="icon is-small is-left">
                        <i class="zmdi zmdi-assignment-check"></i>
                    </span>
                </div>
            </div>
        </section>

        <section class="modal-card-body" id="cambioClave">
            <p class="has-text-justified">
                <strong>Actualizar Contraseña: </strong>Digite una nueva clave entre cuatro y quince caracteres, recuerde que esta no puede ser igual al código de usuario. 
                En el próximo inicio de sesión se verán reflejados los cambios.
            </p>

            <form name="formulario" id="formulario_clave" method="POST">
                <div class="column is-12">
                    <input type="hidden" name="cod_usuario_sesion" id="cod_usuario_sesion" value="<?php echo $_SESSION['COD_USUARIO'] ?>"> 
                    <div class="field">
                        <label class="label has-text-left">Contraseña (*):</label>
                        <div class="control has-icons-right">
                            <input class="input is-hovered" type="password" name="clave1_actualizar" id="clave1_actualizar" minlength="4" maxlength="15" autocomplete="off" required>
                            <span class="icon is-small is-right">
                                <i class="zmdi zmdi-key"></i>
                            </span>
                        </div>
                        <p class="help has-text-left">Ingrese la contraseña</p>
                    </div>
                </div>

                <div class="column is-12">
                    <div class="field">
                        <label class="label has-text-left">Confirmar contraseña (*):</label>
                        <div class="control has-icons-right">
                            <input class="input is-hovered" type="password" name="clave2_actualizar" id="clave2_actualizar" minlength="4" maxlength="15" autocomplete="off" required>
                            <span class="icon is-small is-right">
                                <i class="zmdi zmdi-key"></i>
                            </span>
                        </div>
                        <p class="help has-text-left">Confirme la contraseña</p>
                    </div>
                </div>

                <div class="column is-12">
                    <p class="buttons">
                        <button class="button is-info is-hovered is-fullwidth" type="submit" id="btn_actualizar_clave">
                            <i class="zmdi zmdi-edit"></i> &nbsp; Actualizar
                        </button>
                    </p>
                </div>
            </form>
        </section>

        <footer class="modal-card-foot has-text-centered">
            <div class="field has-addons">
                <p class="control">
                    <button class="button is-primary" onclick="mostrarformM(false)">
                        <span class="icon is-small">
                            <i class="zmdi zmdi-folder-person"></i>
                        </span>
                        <span> Datos prestador </span>
                    </button>
                </p>

                <p class="control">
                    <button class="button is-info" id="btnRegresar" onclick="mostrarformM(true)">
                        <span class="icon is-small">
                            <i class="zmdi zmdi-key"></i>
                        </span>
                        <span> Cambio contraseña </span>
                    </button>
                </p>
            </div>
        </footer>
    </div>
</div>  

<script type="text/javascript" src="../scripts/menu.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
