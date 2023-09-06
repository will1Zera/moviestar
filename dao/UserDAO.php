<?php 

    require_once("models/User.php");
    require_once("models/Message.php");

    // Classe para cadastrar o usuário na tabela do banco de dados
    class UserDAO implements UserDAOInterface{
        private $conn;
        private $url;
        private $message;

        // Construtor que já realiza a conexão com o db
        public function __construct(PDO $conn, $url){
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        // Função que cria um usuário, passando todos os dados, e o retorna
        public function buildUser($data){
            $user = new User();

            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->lastname = $data['lastname'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->image = $data['image'];
            $user->bio = $data['bio'];
            $user->token = $data['token'];

            return $user;
        }

        // Função que de fato cria o usuário no banco de dados
        public function create(User $user, $authUser= false){
            $stmt = $this->conn->prepare("INSERT INTO users(
                name, lastname, email, password, token
                ) VALUES (
                :name, :lastname, :email, :password, :token)");

            $stmt->bindParam(":name", $user->name);
            $stmt->bindParam(":lastname", $user->lastname);
            $stmt->bindParam(":email", $user->email);
            $stmt->bindParam(":password", $user->password);
            $stmt->bindParam(":token", $user->token);
            $stmt->execute();

            // Executa a autenticação do usuário baseado no token criado
            if($authUser){
                $this->setTokenToSession($user->token);
            }
        }

        // Função que atualiza o usuário no banco de dados
        public function update(User $user, $redirect = true){
            $stmt = $this->conn->prepare("UPDATE users SET
                name = :name,
                lastname = :lastname,
                email = :email,
                image = :image,
                bio = :bio,
                token = :token
                WHERE id = :id
            ");

            $stmt->bindParam(":name", $user->name);
            $stmt->bindParam(":lastname", $user->lastname);
            $stmt->bindParam(":email", $user->email);
            $stmt->bindParam(":image", $user->image);
            $stmt->bindParam(":bio", $user->bio);
            $stmt->bindParam(":token", $user->token);
            $stmt->bindParam(":id", $user->id);
            $stmt->execute();

            if($redirect){
                $this->message->setMessage("Dados atualizados com sucesso.", "success", "editprofile.php");
            }
        }

        // Função que verifica se o usuário está logado ou não
        public function verifyToken($protected = false){
            if(!empty($_SESSION["token"])){
                $token = $_SESSION["token"];
                $user = $this->findByToken($token);

                if($user){
                   return $user; 
                } else if($protected){ // Redireciona usuário não autenticado
                    $this->message->setMessage("Sem permissão de acesso.", "error", "index.php");
                }
            } else if($protected){ // Redireciona usuário não autenticado
                $this->message->setMessage("Sem permissão de acesso.", "error", "index.php");
            }
        }

        // Função que seta o token na sessão e redireciona o usuário
        public function setTokenToSession($token, $redirect = true){
            $_SESSION["token"] = $token;

            if($redirect){
                $this->message->setMessage("Usuário logado com sucesso.", "success", "editprofile.php");
            }
        }  

        // Função que autentica o email e a senha do usuário no login
        public function authenticateUser($email, $password){
            // Verifica o email corretamente
            $user = $this->findByEmail($email);

            if($user){
                // Verifica a senha corretamente
                if(password_verify($password, $user->password)){
                    // Manipula um token para login
                   $token = $user->generateToken();
                   $this->setTokenToSession($token, false); 
                   // Atualiza o token do usuário
                   $user->token = $token;
                   $this->update($user, false);

                   return true;
                } else{
                    return false;
                }
            } else{
                return false;
            }
        }

        // Função que faz toda a validação do token do usuário
        public function findByToken($token){

            if($token != ""){
                $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = :token");
                $stmt->bindParam(":token", $token);
                $stmt->execute();

                if($stmt->rowCount() > 0){

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;
                } else{
                    return false;
                }

            } else{
                return false;
            }
        }

        // Função que remove o token e redireciona para fazer o logout
        public function destroyToken(){
            $_SESSION["token"] = "";

            $this->message->setMessage("Logout realizado com sucesso.", "success", "index.php");
        }

        // Função que faz toda a validação do email do usuário
        public function findByEmail($email){

            if($email != ""){
                $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->bindParam(":email", $email);
                $stmt->execute();

                if($stmt->rowCount() > 0){

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;
                } else{
                    return false;
                }

            } else{
                return false;
            }
        }  

        public function findById($id){

            if($id != ""){
                $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->bindParam(":id", $id);
                $stmt->execute();

                if($stmt->rowCount() > 0){

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;
                } else{
                    return false;
                }

            } else{
                return false;
            }
        }

        // Função que atualiza a senha do usuário
        public function changePassword(User $user){
            $stmt = $this->conn->prepare("UPDATE users SET
                password = :password
                WHERE id = :id
            ");

            $stmt->bindParam(":password", $user->password);
            $stmt->bindParam(":id", $user->id);
            $stmt->execute();

            $this->message->setMessage("Senha alterada com sucesso.", "success", "editprofile.php");
        } 
    }

?>