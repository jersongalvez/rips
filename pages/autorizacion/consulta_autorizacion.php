<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////       VISTA CONSULTA AUTORIZACION        ///////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////  AMBITO: PROCESAMIENTO AUTORIZACIONES  /////////////////////
////////     VISTA PRINCIPAL PARA PROCESAR AUTORIZACIONES POR AFILIADO     /////
////////////////////////////////////////////////////////////////////////////////

session_start();

if (!isset($_SESSION['COD_USUARIO'])) {

    header("Location: ../login.php");
} else {

    require '../header.php';
    require '../menu.php';

    if ($_SESSION["autorizaciones"] == 1 && $_SESSION['PWD_USER'] == 0) {
        ?>

        <div id="modal_autorizacion"></div>

        <div class="section" id="wrapper">
            <div class="container">
                <div class="columns" style="margin-top: 30px;">
                    <div class="column is-6">
                        <p class="title is-3">Autorizaciones por afiliado</p>
                        <p class="subtitle is-5">Pijaos Salud EPSI</p>
                    </div>

                    <div class="column is-6">
                        <nav class="breadcrumb is-right" aria-label="breadcrumbs"> 
                            <ul>
                                <li>
                                    <a>
                                        <span class="icon is-small">
                                            <i class="zmdi zmdi-case-check" aria-hidden="true"></i>
                                        </span>
                                        <span>Autorización</span>
                                    </a>
                                </li>

                                <li class="is-active">
                                    <a>
                                        <span class="icon is-small">
                                            <i class="zmdi zmdi-assignment-o"></i>
                                        </span>
                                        <span>Consultar autorizaciones</span>
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


                <div class="columns" style="margin-top: 10px;">
                    <div class="column is-4">
                        <div class="field">
                            <label class="label has-text-left">NIT del prestador (*):</label>
                            <div class="control has-icons-left">
                                <input class="input is-hovered" type="text" name="nit_prestador" id="nit_prestador" 
                                       value="<?php echo ($_SESSION['NIT_PRESTADOR'] === '809008362') ? '' : $_SESSION["NIT_PRESTADOR"] ?>" 
                                       <?php echo ($_SESSION['NIT_PRESTADOR'] === '809008362') ? '' : 'readonly' ?> required>
                                <span class="icon is-small is-left">
                                    <i class="zmdi zmdi-attachment-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="column is-4">
                        <div class="field">
                            <label class="label has-text-left">Tipo Documento (*):</label>
                            <div class="control has-icons-left">
                                <div class="select is-fullwidth">
                                    <select name="tip_documento" id="tip_documento">
                                        <option selected value="">Elige una opcion</option>
                                        <option value="AS">Adulto Sin Identificación</option>
                                        <option value="CC">Cedula De Ciudadanía</option>
                                        <option value="CE">Cedula De Extranjería</option>
                                        <option value="CN">Certificado De Nacido Vivo</option>
                                        <option value="MS">Menor Sin Identificación</option>
                                        <option value="PA">Pasaporte</option>
                                        <option value="PE">Permiso Especial De Permanencia</option>
										<option value="PT">Permiso Temporal</option>
                                        <option value="RC">Registro Civil</option>
                                        <option value="SC">Salvo Conducto</option>
                                        <option value="TI">Tarjeta De Identidad</option>                                        
										

                                    </select>
                                </div>
                                <div class="icon is-small is-left">
                                    <i class="zmdi zmdi-assignment-o"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="column is-4">
                        <div class="field">
                            <label class="label has-text-left">Número de documento (*):</label>
                            <div class="control has-icons-left">
                                <input class="input is-hovered" type="number" id="numd_afiliado" name="numd_afiliado">
                                <span class="icon is-small is-left">
                                    <i class="zmdi zmdi-account-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="columns">
                    <div class="column is-6">
                        <button class="button is-info is-fullwidth" type="submit" onclick="buscar_datosaut()" id="filtrar">
                            <span class="icon is-small">
                                <i class="zmdi zmdi-search-in-file"></i>
                            </span>
                            <span>
                                Buscar
                            </span>
                        </button> 
                    </div>

                    <div class="column is-6">
                        <button class="button is-primary is-fullwidth" id="nueva" onclick="limpiar()">
                            <span class="icon is-small">
                                <i class="zmdi zmdi-plus"></i>
                            </span>
                            <span>
                                Nuevo
                            </span>
                        </button>
                    </div>
                </div>

                <div class="hide-div" id="datos_afiliado">
                    <div class="columns" style="margin-top: 10px;">
                        <div class="column is-12 has-background-light">
                            <div class="columns" id="d_afiliado"> </div>
                        </div>
                    </div>
                </div>

                <div class="hide-div" id="autorizaciones_afiliado">
                    <div class="columns" style="margin-top: 20px;">
                        <div class="column is-12" id="listadoregistros">
                            <div class="table-container">
                                <table id="tbllistado" class="table is-striped is-hoverable is-fullwidth" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="has-text-centered">Opciones</th>
                                            <th class="has-text-centered">Nº Autorización</th>
                                            <th class="has-text-centered">Nº Solicitud</th>
                                            <th class="has-text-centered">Fecha Inicio Vigencia</th>
                                            <th class="has-text-centered">Fecha Vencimiento</th>
											<th class="has-text-centered">Prestador</th>
                                            <th class="has-text-centered">Estado</th>
                                        </tr>
                                    </thead>

                                    <tbody> </tbody>

                                    <tfoot>
                                        <tr>
                                            <th class="has-text-centered">Opciones</th>
                                            <th class="has-text-centered">Nº Autorización</th>
                                            <th class="has-text-centered">Nº Solicitud</th>
                                            <th class="has-text-centered">Fecha Inicio Vigencia</th>
                                            <th class="has-text-centered">Fecha Vencimiento</th>
											<th class="has-text-centered">Prestador</th>
                                            <th class="has-text-centered">Estado</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    } else {

        require '../inicio/noacceso.php';
    }

    require '../footer.php';
    ?>

    <script type="text/javascript" src="../../public/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="../../public/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="../../public/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/buttons.html5.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/buttons.colVis.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/jszip.min.js"></script>
    <script type="text/javascript" src="../../public/datatables/vfs_fonts.js"></script>
    <script type="text/javascript" src="../scripts/buscar_aut_afiliado.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
    <?php
}








