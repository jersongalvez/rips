<!--Inicio pie de pagina-->

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

<!--Fin pie de pagina-->

<script type="text/javascript" src="../../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../public/js/navbar.min.js"></script>
<script type="text/javascript" src="../../public/js/alertify.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
<script type="text/javascript" src="../../public/js/overhang.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
<script type="text/javascript" src="../scripts/inactividad.min.js?v=<?php echo $_SESSION['web_version']; ?>"></script>
<script type="text/javascript">
    //override defaults
    alertify.defaults.transition = "slide";
    alertify.defaults.theme.ok = "button is-info";
    alertify.defaults.theme.cancel = "button is-danger";
</script>

</body>
</html>
