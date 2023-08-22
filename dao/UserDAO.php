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

        public function update(User $user){

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

        public function authenticateUser($email, $password){

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

        }

        public function changePassword(User $user){

        } 
    }

?>