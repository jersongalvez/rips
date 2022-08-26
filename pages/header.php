<?php
////////////////////////////////////////////////////////////////////////////////
/////////////////////////         SISTEMA RIPS         /////////////////////////
/////////////////////////      PIJAOS SALUD EPSI      //////////////////////////
///////////////////                 HEADER                 ////////////////////
/////////////////////////  DEPARTAMENTO DE DESARROLLO  /////////////////////////
/////////////////////////   AMBITO: TODO EL PROYECTO  //////////////////////////
/////////////             VISTA PRINCIPAL DEL SISTEMA              /////////////
////////////////////////////////////////////////////////////////////////////////

//se valida que no halla una session creada, de ser asi, se crea una nueva session
if (strlen(session_id()) < 1) {
    
    session_start();
}
?>

<html lang="es">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Sistema de transacciones - Pijaos Salud EPSI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="../../public/icon/favicon.png?v=<?php echo $_SESSION['web_version']; ?>">
        <link rel="stylesheet" type="text/css" href="../../public/css/main.min.css?v=<?php echo $_SESSION['web_version']; ?>">      
        <link rel="stylesheet" type="text/css" href="../../public/css/style.min.css?v=<?php echo $_SESSION['web_version']; ?>">
        <link rel="stylesheet" type="text/css" href="../../public/daterangepicker/daterangepicker.min.css" />
        <link rel="stylesheet" type="text/css" href="../../public/css/alertify.min.css?v=<?php echo $_SESSION['web_version']; ?>">
        <link rel="stylesheet" type="text/css" href="../../public/css/bootstrap.min.css?v=<?php echo $_SESSION['web_version']; ?>">
        <link rel="stylesheet" type="text/css" href="../../public/css/overhang.min.css?v=<?php echo $_SESSION['web_version']; ?>">
        <link rel="stylesheet" type="text/css" href="../../public/icon/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" type="text/css" href="../../public/icon/css/material-design-color-palette.min.css">
        <script src="https://kit.fontawesome.com/1a8797c7c0.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" href="../../public/datatables/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="../../public/datatables/buttons.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="../../public/datatables/responsive.dataTables.min.css">
        <script type="text/javascript" src="../../public/js/jquery-3.5.1.min.js"></script>
    </head>

    <body>
        
      



