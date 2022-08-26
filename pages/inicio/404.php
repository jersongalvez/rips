<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
/////////////////////////       ARCHIVO DE ERROR        ////////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
///////////////////////////  AMBITO: TODO EL PROYECTO  /////////////////////////
///////      VISTA CUANDO NO SE ENCUENTRA UNA PANTALLA EN EL PROYECTO     //////
////////////////////////////////////////////////////////////////////////////////



require 'pages/header.php';

require 'pages/menu.php';
?>

<div class="section" id="wrapper">
    <div class="container">
        <div class="columns">
            <div class="column is-12 has-text-centered" style="margin-top: 100px;">
                <figure class="image is-128x128 is-inline-block">
                    <img src="public/img/logo_pijaos.png">
                </figure>

                <p class="title is-3">Pijaos Salud EPSI</p>
                <p class="subtitle is-5">
                    Sistema de transacciones WEB para prestadores
                </p>
            </div>
        </div>

        <div class="columns has-text-centered" style="margin-top: 50px;">
            <div class="column is-3 is-hidden-mobile">&nbsp;</div>
            <div class="column is-6">
                <p class="title is-1" style="font-size: 100px;">Error 404</p>
                <p class="subtitle is-4">La página a la que intenta acceder no existe.</p>
            </div>
            <div class="column is-3 is-hidden-mobile">&nbsp;</div>            
        </div>
    </div>
</div>

<?php require 'pages/footer.php'; ?>



