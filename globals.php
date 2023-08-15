<?php
    // Inicia a sessão
    session_start();

    // URL Dinâmica do projeto para uso nos links
    $BASE_URL = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]."?") . "/";

?>