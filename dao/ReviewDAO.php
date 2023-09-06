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

        // Função que de fato cria o review no banco de dados
        public function create(Review $review){
            // Prepara a query de SQL do banco de dados
            $stmt = $this->conn->prepare("INSERT INTO reviews(
                rating, review, movies_id, users_id
                ) VALUES (
                :rating, :review, :movies_id, :users_id)");

            // Brinda os parametros da query values com os dados
            $stmt->bindParam(":rating", $review->rating);
            $stmt->bindParam(":review", $review->review);
            $stmt->bindParam(":movies_id", $review->movies_id);
            $stmt->bindParam(":users_id", $review->users_id);

            // Executa a query
            $stmt->execute();

            // Exibe mensagem de sucesso e redireciona para a página principal
            $this->message->setMessage("Avaliação adicionada com sucesso.", "success", "index.php");
        } 

        public function getMoviesReview($id){
            $reviews = [];
            $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE movies_id = :movies_id");
            $stmt->bindParam(":movies_id", $id);
            $stmt->execute();

            if($stmt->rowCount() > 0){

                $reviewsData = $stmt->fetchAll();

                $userDao = new UserDao($this->conn, $this->url);

                foreach($reviewsData as $review){

                    $reviewObject = $this->buildReview($review);

                    // Chama os dados do usuário no review
                    $user = $userDao->findById($reviewObject->users_id);
                    $reviewObject->user = $user;

                    $reviews[] = $reviewObject;
                }
            }

            return $reviews;
        }

        public function getRatings($id){

        }

        public function hasAlreadyReviewed($id, $userId){
            $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE movies_id = :movies_id AND users_id = :users_id");
            $stmt->bindParam(":movies_id", $id);
            $stmt->bindParam(":users_id", $userId);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                return true;
            } else{
                return false;
            }
        }
    }

?>