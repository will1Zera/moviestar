<?php

    require_once("globals.php"); 
    require_once("db.php");
    require_once("models/User.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");

    $message = new Message($BASE_URL); // Cria o objeto da mensagem passando a url
    $userDao = new UserDAO($conn, $BASE_URL); // Objeto do usuário passando a conexão e a url

    // Resgata o tipo do formulário (já que temos dois forms)
    $type = filter_input(INPUT_POST, "type");

    // Atualiza dados do usuário
    if ($type === "update"){

        // Resgata dados do usuário
        $userData = $userDao->verifyToken();

        // Receber dados do post
        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $bio = filter_input(INPUT_POST, "bio");

        // Cria um objeto do usuário
        $user = new User();

        //Preenche o usuário com os dados atualizados
        $userData->name = $name;
        $userData->lastname = $lastname;
        $userData->email = $email;
        $userData->bio = $bio;

        // Lógica para inserir uma imagem
        if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){

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
                $imageName = $user->imageGenerateName();

                // Cria uma imagem jpeg com as informações geradas até aqui
                imagejpeg($imageFile, "./img/users/" . $imageName, 100);

                // Associa essa imagem criada a imagem do usuário
                $userData->image = $imageName;

            } else{
                $message->setMessage("Tipo inválido de imagem (apenas PNG ou JPG).", "error", "back");
            }
        }

        // Realiza o update do usuário
        $userDao->update($userData);

      // Atualiza a nova senha do usuário  
    } else if ($type == "changepassword"){

        // Receber dados do post
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");
        
        // Resgata dados do usuário
        $userData = $userDao->verifyToken();

        $id = $userData->id;

        // Lógica se verifica se as senhas são iguais
        if($password == $confirmpassword){

            $user = new User();

            // Modifica para a nova senha
            $finalPassword = $user->generatePassword($password);
            $user->password = $finalPassword;
            $user->id = $id;
            $userDao->changePassword($user);

        } else{

            $message->setMessage("As senhas não são iguais, tente novamente.", "error", "back");
        }

    } else{

        $message->setMessage("Ocorreu um erro no sistema.", "error", "index.php");
    }
?>