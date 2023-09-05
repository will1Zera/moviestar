<?php 

    require_once("models/Review.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");

    // Classe para cadastrar o review na tabela do banco de dados
    class ReviewDAO implements ReviewDAOInterface{
        private $conn;
        private $url;
        private $message;

        // Construtor que já realiza a conexão com o db
        public function __construct(PDO $conn, $url){
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        // Função que cria um review, passando todos os dados, e o retorna
        public function buildReview($data){
            $reviewObject = new Review();

            $reviewObject->id = $data['id'];
            $reviewObject->rating = $data['rating'];
            $reviewObject->review = $data['review'];
            $reviewObject->users_id = $data['users_id'];
            $reviewObject->movies_id = $data['movies_id'];

            return $reviewObject;
        }

        public function create(Review $review){

        } 

        public function getMoviesReview($id){

        }

        public function getRatings($id){

        }

        public function hasAlreadyReviewed($id){

        }
    }

?>