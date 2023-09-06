<?php 

    require_once("globals.php"); 
    require_once("db.php");
    require_once("models/Movie.php");
    require_once("models/Review.php");
    require_once("models/Message.php");
    require_once("dao/MovieDAO.php");
    require_once("dao/UserDAO.php");
    require_once("dao/reviewDAO.php");

    $message = new Message($BASE_URL); 
    $userDao = new UserDAO($conn, $BASE_URL);
    $movieDao = new MovieDAO($conn, $BASE_URL);
    $reviewDao = new ReviewDAO($conn, $BASE_URL);

    // Resgata o tipo do formulário
    $type = filter_input(INPUT_POST, "type");

    // Resgata dados do usuário e verifica se está logado
    $userData = $userDao->verifyToken();

    if($type === "create"){

        // Receber dados do post
        $rating = filter_input(INPUT_POST, "rating");
        $review = filter_input(INPUT_POST, "review");
        $movies_id = filter_input(INPUT_POST, "movies_id");
        $users_id = $userData->id;

        $reviewObject = new Review(); // Cria objeto do review

        $movieData = $movieDao->findById($movies_id);

        // Verifica se existe filme
        if($movieData){

            // Verifica dados minimos
            if(!empty($rating) && !empty($review) && !empty($movies_id)){

                // Criação do review
                $reviewObject->rating = $rating;
                $reviewObject->review = $review;
                $reviewObject->movies_id = $movies_id;
                $reviewObject->users_id = $users_id;

                $reviewDao->create($reviewObject);

            } else{

                // Mensagem de erro caso algum dos dados acima esteja vazio
                $message->setMessage("Preencha todos os campos!", "error", "back");
            }


        } else{

            $message->setMessage("Ocorreu um erro no sistema.", "error", "index.php");
        }

    } else{

        $message->setMessage("Ocorreu um erro no sistema.", "error", "index.php");
    }

?>