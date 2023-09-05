<?php 

    require_once("globals.php"); 
    require_once("db.php");
    require_once("models/Movie.php");
    require_once("models/Message.php");
    require_once("dao/MovieDAO.php");
    require_once("dao/UserDAO.php");

    $message = new Message($BASE_URL); 
    $userDao = new UserDAO($conn, $BASE_URL);
    $movieDao = new MovieDAO($conn, $BASE_URL);

    // Resgata o tipo do formulário
    $type = filter_input(INPUT_POST, "type");

    // Resgata dados do usuário e verifica se está logado
    $userData = $userDao->verifyToken();

    if($type === "create"){

    }

?>