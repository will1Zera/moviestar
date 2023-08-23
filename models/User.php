<?php 
    // Classe para pegar todos os atributos do usuário
    class User{
        public $id;
        public $name;
        public $lastname;
        public $email;
        public $password;
        public $image;
        public $bio;
        public $token;

        // Função que gera o token randomicamente
        public function generateToken(){
            return bin2hex(random_bytes(50));
        }

        // Função que gera o hash de uma senha
        public function generatePassword($password){
            return password_hash($password, PASSWORD_DEFAULT);
        }
    }

    interface UserDAOInterface{
        public function buildUser($data);
        public function create(User $user, $authUser= false);
        public function update(User $user, $redirect = true);
        public function verifyToken($protected = false);
        public function setTokenToSession($token, $redirect = true);    
        public function authenticateUser($email, $password);
        public function findByToken($token);
        public function findByEmail($email);    
        public function findById($id);
        public function destroyToken();
        public function changePassword(User $user);    
    }

?>