<?php 
    require_once("templates/header.php");
    require_once("dao/UserDAO.php");
    require_once("dao/MovieDAO.php");
    require_once("models/User.php");

    // Verifica se o usuário está logado para ter acesso a essa página
    $user = new User();
    $userDao = new UserDAO($conn, $BASE_URL);
    $movieDao = new MovieDAO($conn, $BASE_URL);

    // Pega o id do usuário
    $id = filter_input(INPUT_GET, "id");

    // Verifica se o id está vazio
    if(empty($id)){ 

        if(!empty($userData)){
            
            $id = $userData->id;

        } else{

            $message->setMessage("O usuário não foi encontrado.", "error", "index.php");
        } 
    } else{

        $userData = $userDao->findById($id);

        if(!$userData){

            $message->setMessage("O usuário não foi encontrado.", "error", "index.php");
        }
    }

    // Recebe o nome do usuário completo
    $fullName = $user->getFullName($userData);

    // Função que atribui uma imagem padrão de usuário caso ele não tenha
    if($userData->image == ""){
        $userData->image = "user.png";
    }

    // Resgata os filmes que o usuário adicionou
    $userMovies = $movieDao->getMoviesByUserId($id);
?>
    
    <div id="main-container" class="container-fluid">
        <div class="col-md-8 offset-md-2">
            <div class="row profile-container">
                <div class="col-md-12 about-container">
                    <h1 class="page-title"><?= $fullName ?></h1>
                    <div id="profile-image-container" class="profile-image" style="background-image: url('<?= $BASE_URL ?>img/users/<?= $userData->image ?>');"></div>
                    <h3 class="about-title">Sobre:</h3>
                    <?php if(!empty($userData->bio)): ?>
                        <p class="profile-description"><?= $userData->bio ?></p>
                    <?php else: ?>
                        <p class="profile-description">Usuário não possui sobre.</p>
                    <?php endif; ?>
                </div>

                <div class="col-md-12 added-movies-container">
                       <h3>Filmes enviados:</h3>
                       <div class="movies-container">
                           <?php foreach($userMovies as $movie): ?>
                                <?php require("templates/movie_card.php"); ?>
                            <?php endforeach; ?>
                            <?php if(count($userMovies) === 0): ?>
                                <p class="empty-list">Usuário não possui filmes.</p>
                            <?php endif; ?>
                       </div>
                </div>
            </div>
        </div>
    </div>

<?php
    require_once("templates/footer.php");
?>