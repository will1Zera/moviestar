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

    // Resgata o tipo do formulário (já que temos dois forms)
    $type = filter_input(INPUT_POST, "type");

    // Resgata dados do usuário e verifica se está logado
    $userData = $userDao->verifyToken();

    // Cria o filme
    if ($type === "create"){

        // Receber dados do post
        $title = filter_input(INPUT_POST, "title");
        $description = filter_input(INPUT_POST, "description");
        $trailer = filter_input(INPUT_POST, "trailer");
        $category = filter_input(INPUT_POST, "category");
        $length = filter_input(INPUT_POST, "length");

        $movie = new Movie(); // Cria objeto do filme

        if(!empty($title) && !empty($description) && !empty($category)){

            // Criação do filme
            $movie->title = $title;
            $movie->description = $description;
            $movie->trailer = $trailer;
            $movie->category = $category;
            $movie->length = $length;
            $movie->users_id = $userData->id;

            // Lógica para inserir uma imagem
            if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){

                // Pega a imagem e verifica os tipos dela
                $image = $_FILES["image"];
                $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
                $jpgArray = ["image/jpeg", "image/jpg"];

                // Verifica o tipo de imagem
                if(in_array($image["type"], $imageTypes)){

                    // Verifica se é jpg
                    if(in_array($image["type"], $jpgArray)){

                        $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                    } else{ // Imagem png

                        $imageFile = imagecreatefrompng($image["tmp_name"]);
                    }

                    // Gera um nome para a imagem
                    $imageName = $movie->imageGenerateName();

                    // Cria uma imagem jpeg com as informações geradas até aqui na pasta correspondente
                    imagejpeg($imageFile, "./img/movies/" . $imageName, 100);

                    // Associa essa imagem criada a imagem do filme
                    $movie->image = $imageName;

                } else{

                    $message->setMessage("Tipo inválido de imagem (apenas PNG ou JPG).", "error", "back");
                }
            }

            // Realiza a criação do filme
            $movieDao->create($movie);
            
        } else{

            // Mensagem de erro caso algum dos dados acima esteja vazio
            $message->setMessage("Preencha pelo menos o título, categoria e descrição.", "error", "back");
        }

    } elseif($type === "delete"){

        // Recebe e procura o id do filme
        $id = filter_input(INPUT_POST, "id");
        $movie = $movieDao->findById($id);

        if($movie){
            // Verifica se o filme é do usuário
            if($movie->users_id === $userData->id){
                
                $movieDao->destroy($movie->id);
            } else{

                $message->setMessage("Ocorreu um erro no sistema.", "error", "index.php");
            }

        } else{

            $message->setMessage("Ocorreu um erro no sistema.", "error", "index.php");
        }

    } elseif($type === "update"){

        // Receber dados do post
        $title = filter_input(INPUT_POST, "title");
        $description = filter_input(INPUT_POST, "description");
        $trailer = filter_input(INPUT_POST, "trailer");
        $category = filter_input(INPUT_POST, "category");
        $length = filter_input(INPUT_POST, "length");
        $id = filter_input(INPUT_POST, "id");

        $movieData = $movieDao->findById($id);

        if($movieData){
            // Verifica se o filme é do usuário
            if($movieData->users_id === $userData->id){

                if(!empty($title) && !empty($description) && !empty($category)){
                    // Realiza a edição do filme
                    $movieData->title = $title;
                    $movieData->description = $description;
                    $movieData->trailer = $trailer;
                    $movieData->category = $category;
                    $movieData->length = $length;

                    // Lógica para atualizar a imagem
                    if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){

                        // Pega a imagem e verifica os tipos dela
                        $image = $_FILES["image"];
                        $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
                        $jpgArray = ["image/jpeg", "image/jpg"];

                        // Verifica o tipo de imagem
                        if(in_array($image["type"], $imageTypes)){

                            // Verifica se é jpg
                            if(in_array($image["type"], $jpgArray)){

                                $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                            } else{ // Imagem png

                                $imageFile = imagecreatefrompng($image["tmp_name"]);
                            }

                            // Gera um nome para a imagem
                            $movie = new Movie();
                            $imageName = $movie->imageGenerateName();

                            // Cria uma imagem jpeg com as informações geradas até aqui na pasta correspondente
                            imagejpeg($imageFile, "./img/movies/" . $imageName, 100);

                            // Associa essa imagem criada a imagem do filme
                            $movieData->image = $imageName;

                        } else{

                            $message->setMessage("Tipo inválido de imagem (apenas PNG ou JPG).", "error", "back");
                        }
                    }

                    // Realiza a atualização do filme
                    $movieDao->update($movieData);


                } else{

                    // Mensagem de erro caso algum dos dados acima esteja vazio
                    $message->setMessage("Preencha pelo menos o título, categoria e descrição.", "error", "back");
                }
            } else{

                $message->setMessage("Ocorreu um erro no sistema.", "error", "index.php");
            }
        } else{

            $message->setMessage("Ocorreu um erro no sistema.", "error", "index.php");
        }
    } else{

        $message->setMessage("Ocorreu um erro no sistema.", "error", "index.php");
    }
?>