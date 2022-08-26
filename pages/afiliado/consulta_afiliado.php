<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////         VISTA CONSULTA AFILIADOS         ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////  AMBITO: CONSULTA DE AFILIADOS  ////////////////////////
////////     VISTA PRINCIPAL PARA CONSULTAR EL ESTADO DE UN AFILIADO       /////
////////////////////////////////////////////////////////////////////////////////

session_start();

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    require '../header.php';
    require '../menu.php';


    if ($_SESSION["consulta_afi"] == 1 && $_SESSION['PWD_USER'] == 0) {

        $clave_vps = "6Lex0CwaAAAAAJrIg9hThN4V2ltKJzkuNejUeZH9";
        //LocalHost
        //$clave_vps = "6LfK0KYZAAAAACRbLhoCwWMwirivJrnPO2TQvO9z";
        //$clave_local = "6LfK0KYZAAAAACRbLhoCwWMwirivJrnPO2TQvO9z";

        require_once '../../controladores/funciones_generales.php';

        //Compruebo si vienen errores
        isset($_POST['novedad']) ? errorRips($_POST['novedad']) : "";
        ?>

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

        <style type="text/css">
            div.g-recaptcha {
                margin: 0 auto;
                width: 304px;
            }
        </style>


        <div class="section" id="wrapper">
            <div class="container" style="margin-top: 40px;">
                <div class="columns">
                    <div class="column is-6">
                        <p class="title is-3">Consulta de afiliados</p>
                        <p class="subtitle is-5">Pijaos Salud EPSI</p>
                    </div>

                    <div class="column is-6">
                        <nav class="breadcrumb is-right" aria-label="breadcrumbs"> 
                            <ul>
                                <li>
                                    <a>
                                        <span class="icon is-small">
                                            <i class="fas fa-user-friends"></i>
                                        </span>
                                        <span>Afiliados</span>
                                    </a>
                                </li>

                                <li class="is-active">
                                    <a>
                                        <span class="icon is-small">
                                            <i class="zmdi zmdi-account-box"></i>
                                        </span>
                                        <span>Buscar afiliado</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-12">
                        <p class="has-text-justified is-size-12">
                            <strong>PIJAOS SALUD EPS INDÍGENA</strong> dentro de sus lineamientos y políticas internas a fines de salvaguardar los derechos que protegen los datos 
                            personales, se acoge a la normatividad y disposiciones que regulan el tema en materia, dando un manejo diligente y responsable al 
                            tratamiento de información en relación a nuestros usuarios y prestadores, entendiendo lo valioso de su intimidad, garantizándole de 
                            esta manera al titular, el pleno y efectivo ejercicio del derecho de habeas data consagrado en la <strong>ley estatutaria 1266 de 2008.</strong>
                        </p>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>

                    <div class="column is-4 has-text-centered"> 
                        <form action="buscar_afiliado.php" method="POST" name="frm_consulta_usu" id="frm_consulta_usu" onsubmit="return validarConsulta()">
                            <figure class="image is-128x128 is-inline-block">
                                <img src="../../public/img/logo_pijaos.png">
                            </figure>

                            <div class="field">
                                <label class="label  has-text-left">Tipo documento</label>
                                <div class="control has-icons-left">
                                    <div class="select is-fullwidth">
                                        <select name="tipdoc" id="tipdoc" autofocus>
                                            <option selected value="">Elige una opción</option>
                                            <option value="AS">Adulto Sin Identificación</option>
                                            <option value="CC">Cedula De Ciudadanía</option>
                                            <option value="CE">Cedula De Extranjería</option>
                                            <option value="CN">Certificado De Nacido Vivo</option>
                                            <option value="MS">Menor Sin Identificación</option>
                                            <option value="PA">Pasaporte</option>
                                            <option value="PE">Permiso Especial De Permanencia</option>                                                                                   <option value="PT">Permiso Proteccion Temporal</option>
                                            <option value="RC">Registro Civil</option>
                                            <option value="SC">Salvo Conducto</option>
                                            <option value="TI">Tarjeta De Identidad</option>
                                        </select>
                                    </div>
                                    <div class="icon is-small is-left">
                                        <i class="zmdi zmdi-assignment"></i>
                                    </div>
                                </div>
                                <p class="help has-text-left">Seleccione el documento</p>
                            </div>

                            <div class="field">
                                <label class="label has-text-left">Número de documento</label>
                                <div class="control has-icons-right">
                                    <input class="input is-hovered" type="text" name="txtNumdoc" id="txtNumdoc">
                                    <span class="icon is-small is-right">
                                        <i class="zmdi zmdi-account-circle"></i>
                                    </span>
                                </div>
                                <p class="help has-text-left">Digite el documento</p>
                            </div>

                            <div class="g-recaptcha" data-sitekey="<?php echo $clave_vps ?>"></div>

                            <button class="button  is-danger" type="button" style="margin-top: 25px;" id="validar">Validar</button>
                            <button class="button  is-info" type="submit" style="margin-top: 25px;" id="consultar_usu" disabled>
                                <span class="icon is-small">
                                    <i class="zmdi zmdi-search"></i>
                                </span>
                                <span>
                                    Consultar
                                </span>
                            </button>
                        </form>
                    </div>

                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>

                    <div class="column is-5">
                        <p class="title is-5" style="margin-top: 67px;">Normatividad:</p>

                        <p class="has-text-justified is-size-7" style="margin-top: 15px;">
                            Nos permitimos recordarles la responsabilidad de mantener la reserva del uso de esta información, que solo debe ser utilizada para 
                            fines relacionados con la verificación del estado de afiliación de las personas que se encuentran dentro de las bases de datos de los 
                            regímenes Contributivo y Subsidiado, de conformidad con lo establecido en los Artículos 4 y 14 de la Resolución 4622 de 2016, 
                            que expresan:
                        </p>

                        <p class="has-text-justified is-size-7" style="margin-top: 15px;">
                            <strong>Artículo 14. Tratamiento de la información.</strong> Las entidades que participen en el flujo y consolidación de la información, 
                            serán responsables del cumplimiento del régimen de protección de datos y demás aspectos relacionados con el tratamiento de información, 
                            que les sea aplicable en el marco de la Ley Estatutaria 1581 de 2012, la Ley 1712 de 2014, el Capítulo 25 del Título 2 del Libro 2 de la 
                            Parte 2 del Decreto 1074 de 2015 y las normas que las modifiquen, reglamenten o sustituyan, en virtud de lo cual se hacen responsables de 
                            la privacidad, seguridad y confidencialidad y veracidad de la información suministrada y sobre los datos a los cuales tiene acceso.
                        </p>

                        <p class="has-text-justified is-size-7" style="margin-top: 15px;">
                            <strong>Parágrafo.</strong> La actualización de la Base de Datos Única de Afiliados - BDUA, no exime a las entidades que administran las 
                            afiliaciones en los distintos regímenes, planes voluntarios de salud y al INPEC, de la responsabilidad de mantener actualizadas sus bases 
                            de datos con la totalidad de la información generada desde el momento de la afiliación o celebración del contrato y de reportar de manera 
                            oportuna al Administrador Fiduciario de los recursos del Fondo de Solidaridad y Garantía - FOSYGA o la entidad que haga sus veces.  
                            (Ministerio de Salud y protección social, art. 14 de la resolución 4622 de 2016).
                        </p>
                    </div>

                    <div class="column is-1 is-hidden-mobile">&nbsp;</div>
                </div>
            </div>
        </div>
        <?php
    } else {

        require '../inicio/noacceso.php';
    }

    require '../footer.php';
    ?>

    <script type="text/javascript" src="../scripts/consulta_usuario.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
    <?php
}
