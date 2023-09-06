<?php 
    // Classe para pegar todos os atributos do review
    class Review{
        public $id;
        public $rating;
        public $review;
        public $users_id;
        public $movies_id;
        public $user;

        // Função que pega o nome completo do usuário
        public function getFullName($user){
            return $user->name . " " . $user->lastname;
        }

        // Função que gera um nome para a imagem
        public function imageGenerateName(){
            return bin2hex(random_bytes(60)) . ".jpg";
        }

        // Função que gera o token randomicamente
        public function generateToken(){
            return bin2hex(random_bytes(50));
        }

        // Função que gera o hash de uma senha
        public function generatePassword($password){
            return password_hash($password, PASSWORD_DEFAULT);
        }
    }

    interface ReviewDAOInterface{
        public function buildReview($data);
        public function create(Review $review);   
        public function getMoviesReview($id);
        public function getRatings($id);
        public function hasAlreadyReviewed($id, $userId);
    }

?>