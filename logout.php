<?php 

    require_once("templates/header.php");

    // Verifica se o usuário está logado
    if($userDao){
        // Desloga o usuário
        $userDao->destroyToken();
    }

?>