
<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////            VISTA LOGIN USUARIO          ////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////   AMBITO: TODO EL PROYECTO  //////////////////////////
/////////////       VISTA PRINCIPAL PARA INGRESAR AL SISTEMA       /////////////
////////////////////////////////////////////////////////////////////////////////

//header('location: inicio/mtto.php');

//se valida que no halla una session creada, de ser asi, se crea una nueva session
if (strlen(session_id()) < 1) {
    
    session_start();    
}


//valida que halla sesion de usuario iniciada y redirecciona a prestador.php
(isset($_SESSION['COD_USUARIO'])) ? header("Location: inicio/inicio.php") : "";
?>

<html lang="es">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Sistema de transacciones - Pijaos Salud EPSI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="../public/icon/favicon.png?v=1.0.1.10">
        <link rel="stylesheet" type="text/css" href="../public/css/main.min.css?v=1.0.1.10">      
        <link rel="stylesheet" type="text/css" href="../public/css/style.min.css?v=1.0.1.10">
        <link rel="stylesheet" type="text/css" href="../public/css/overhang.min.css?v=1.0.1.10">
        <link rel="stylesheet" type="text/css" href="../public/icon/css/material-design-iconic-font.min.css">
        <script src="https://kit.fontawesome.com/1a8797c7c0.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="../public/js/jquery-3.5.1.min.js"></script> 
    </head>

    <body>
        <div class="section fondo" id="wrapper">
            <div class="container ">
                <div class="column is-4 is-offset-4 has-text-centered">
                    <div class="card" style="margin-top: 70px;">
                        <div class="card-content">
                            <div class="content">
                                <form method="POST" role="form" id="loginForm">
                                    <figure class="image is-128x128 is-inline-block is-marginless">
                                        <img src="../public/img/logo_pijaos.png">
                                    </figure>

                                    <div class="field">
                                        <label class="label has-text-left">Usuario</label>
                                        <div class="control has-icons-right">
                                            <input class="input is-hovered" type="text" name="logina" id="logina" style="text-transform:uppercase;" 
                                                   onkeyup="javascript:this.value = this.value.toUpperCase();" autofocus autocomplete="off" required>
                                            <span class="icon is-small is-right">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </div>
                                        <p class="help has-text-left">Ingrese el código de prestador</p>
                                    </div>

                                    <div class="field" style="margin-top: 20px;">
                                        <label class="label has-text-left">Contraseña</label>
                                        <div class="control has-icons-right">
                                            <input class="input is-hovered" type="password" id="clavea" name="clavea" autocomplete="off" required>
                                            <span class="icon is-small is-right">
                                                <i class="fas fa-unlock-alt"></i>
                                            </span>
                                        </div>
                                        <p class="help has-text-left">Ingrese la contraseña de usuario</p>
                                    </div>

                                    <a class="has-text-grey-dark" href="https://forms.gle/AZcMurQUwjiMDhrP7" target="_blank">
                                        <span class="icon is-small">
                                            <i class="fas fa-user-plus"></i>
                                        </span>
                                        <span>
                                            &nbsp; Solicitar cuenta de usuario 
                                        </span>
                                    </a>

                                    <button class="button is-fullwidth is-primary" type="submit" style="margin-top: 20px;">
                                        <span class="icon is-small">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </span>
                                        <span>
                                            Iniciar sesión
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer has-text-white-bis piepage">
            <nav class="level">
                <div class="level-item has-text-centered">
                    <div>
                        <P>
                            <span class="icon is-small">
                                <i class="fas fa-hand-holding-medical"></i>
                            </span>
                            EPS indígena con identidad propia
                        </p>
                    </div>
                </div>

                <div class="level-item has-text-centered">
                    <div>
                        <p>
                            <span class="tag is-light">
                                <span class="icon is-small">
                                    <i class="fas fa-tags"></i>
                                </span>
                                <a href="https://www.pijaossalud.com/" target="_blank" class="has-text-black">Pijaos Salud</a> 
                            </span>
                            &copy; todos los derechos reservados  
                        </p>

                    </div>
                </div>

                <div class="level-item has-text-centered">
                    <div>
                        <p>
                            <span class="icon is-small">
                                <i class="fas fa-phone-alt"></i>
                            </span>
                            Línea telefónica - 01 8000 186 764
                        </p>
                    </div>
                </div>
            </nav>
        </footer>

        <script type="text/javascript" src="../public/js/jquery-ui.min.js?v=1.0.1.10"></script>
        <script type="text/javascript" src="../public/js/overhang.min.js?v=1.0.1.10"></script>
        <script type="text/javascript" src="scripts/login.min.js?v=1.0.1.9"></script>
    </body>
</html>


