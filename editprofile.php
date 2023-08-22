<?php
    require_once("templates/header.php");
    require_once("dao/UserDAO.php");

    // Verifica se o usuário está logado para ter acesso a essa página
    $userDao = new UserDAO($conn, $BASE_URL);
    $userData = $userDao->verifyToken(true);
?>
    
    <div id="main-container" class="container-fluid">
        <h1>Edit profile</h1>
    </div>

<?php
    require_once("templates/footer.php");
?>  