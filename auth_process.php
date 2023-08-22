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

    // Verifica e realiza ações dependendo do tipo do formulário
    if ($type === "register"){ // Cria usuário

        // Resgata os dados dos campos do formulário
        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

        // Validação dos dados de registro
        if($name && $lastname && $email && $password){

            if($password === $confirmpassword){
                
                if($userDao->findByEmail($email) === false){
                    
                    $user = new User();
                    // Criação do token e hash da senha
                    $userToken = $user->generateToken();
                    $finalPassword = $user->generatePassword($password);

                    // Criação do usuário final
                    $user->name = $name;
                    $user->lastname = $lastname;
                    $user->email = $email;
                    $user->password = $finalPassword;
                    $user->token = $userToken;

                    $auth = true;
                    $userDao->create($user, $auth);

                } else{ // Mensagem de erro caso já exista o email
                    $message->setMessage("E-mail já cadastrado, tente novamente.", "error", "back");
                }

            } else{ // Mensagem de erro caso as senhas forem diferentes
                $message->setMessage("As senhas devem ser iguais!", "error", "back");
            }

        } else{
            // Mensagem de erro caso algum dos dados acima esteja vazio
            $message->setMessage("Preencha todos os campos!", "error", "back");
        }


    } else if ($type === "login"){ // Realiza o login

    }


?>