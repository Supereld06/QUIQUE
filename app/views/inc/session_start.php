<?php

/*=============================================
INICIAR SESIÓN
=============================================*/

if (session_status() === PHP_SESSION_NONE) {

    session_name("QUIQUE_SESSION");

    session_start();
}