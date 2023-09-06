<?php 

    require_once("models/Movie.php");
    require_once("models/Message.php");
    require_once("dao/ReviewDAO.php");

    // Classe para cadastrar o usuário na tabela do banco de dados
    class MovieDAO implements MovieDAOInterface{
        private $conn;
        private $url;
        private $message;

        // Construtor que já realiza a conexão com o db
        public function __construct(PDO $conn, $url){
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        // Função que cria um filme, passando todos os dados, e o retorna
        public function buildMovie($data){
            $movie = new Movie();

            $movie->id = $data['id'];
            $movie->title = $data['title'];
            $movie->description = $data['description'];
            $movie->image = $data['image'];
            $movie->trailer = $data['trailer'];
            $movie->category = $data['category'];
            $movie->length = $data['length'];
            $movie->users_id = $data['users_id'];

            $reviewDao = new ReviewDao($this->conn, $this->url);
            $rating = $reviewDao->getRatings($movie->id);
            $movie->rating = $rating;

            return $movie;
        }

        public function findAll(){

        }

        // Função que lista os últimos filmes adicionados
        public function getLatestMovies(){
            $movies = [];
            $stmt = $this->conn->query("SELECT * FROM movies ORDER BY id DESC");
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $moviesArray = $stmt->fetchAll();

                foreach($moviesArray as $movie){
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        }  

         // Função que lista os filmes pela sua categoria
        public function getMoviesByCategory($category){
            $movies = [];
            $stmt = $this->conn->prepare("SELECT * FROM movies WHERE category = :category ORDER BY id DESC");
            $stmt->bindParam(":category", $category);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $moviesArray = $stmt->fetchAll();

                foreach($moviesArray as $movie){
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        } 

        public function getMoviesByUserId($id){
            $movies = [];
            $stmt = $this->conn->prepare("SELECT * FROM movies WHERE users_id = :users_id");
            $stmt->bindParam(":users_id", $id);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $moviesArray = $stmt->fetchAll();

                foreach($moviesArray as $movie){
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;
        } 

        // Função que procura um filme pelo id dele
        public function findById($id){
            $movie = [];
            $stmt = $this->conn->prepare("SELECT * FROM movies WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $movieData = $stmt->fetch();

                $movie = $this->buildMovie($movieData);

                return $movie;
            } else{
                return false;
            }
        }

        // Procura o filme pelo seu titulo
        public function findByTitle($title){
            $movies = [];
            $stmt = $this->conn->prepare("SELECT * FROM movies WHERE title LIKE :title");
            // Procura o filme que tiver a palavra no seu title (Usa um método diferente)
            $stmt->bindValue(":title", '%'.$title.'%');
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $moviesArray = $stmt->fetchAll();

                foreach($moviesArray as $movie){
                    $movies[] = $this->buildMovie($movie);
                }
            }

            return $movies;

        }

        // Função que de fato cria o filme no banco de dados
        public function create(Movie $movie){
            // Prepara a query de SQL do banco de dados
            $stmt = $this->conn->prepare("INSERT INTO movies(
                title, description, image, trailer, category, length, users_id
                ) VALUES (
                :title, :description, :image, :trailer, :category, :length, :users_id)");

            // Brinda os parametros da query values com os dados
            $stmt->bindParam(":title", $movie->title);
            $stmt->bindParam(":description", $movie->description);
            $stmt->bindParam(":image", $movie->image);
            $stmt->bindParam(":trailer", $movie->trailer);
            $stmt->bindParam(":category", $movie->category);
            $stmt->bindParam(":length", $movie->length);
            $stmt->bindParam(":users_id", $movie->users_id);
            // Executa a query
            $stmt->execute();

            // Exibe mensagem de sucesso e redireciona para a página principal
            $this->message->setMessage("Filme adicionado com sucesso.", "success", "index.php");
        }

        // Função que atualiza o filme no banco de dados
        public function update(Movie $movie){
            // Prepara a query de SQL do banco de dados
            $stmt = $this->conn->prepare("UPDATE movies SET
                title = :title, 
                description = :description, 
                image = :image, 
                trailer = :trailer, 
                category = :category, 
                length = :length
                WHERE id = :id
                ");

            // Brinda os parametros da query values com os dados
            $stmt->bindParam(":title", $movie->title);
            $stmt->bindParam(":description", $movie->description);
            $stmt->bindParam(":image", $movie->image);
            $stmt->bindParam(":trailer", $movie->trailer);
            $stmt->bindParam(":category", $movie->category);
            $stmt->bindParam(":length", $movie->length);
            $stmt->bindParam(":id", $movie->id);
            // Executa a query
            $stmt->execute();

            // Exibe mensagem de sucesso e redireciona para a dashboard
            $this->message->setMessage("Filme atualizado com sucesso.", "success", "dashboard.php");
        }

        // Função que deleta o filme no banco de dados
        public function destroy($id){
            // Prepara a query de SQL do banco de dados
            $stmt = $this->conn->prepare("DELETE FROM movies WHERE id = :id");

            // Brinda os parametros da query values com os dados
            $stmt->bindParam(":id", $id);

            // Executa a query
            $stmt->execute();

            // Exibe mensagem de sucesso e redireciona para a dashboard
            $this->message->setMessage("Filme removido com sucesso.", "success", "dashboard.php");
        } 
    }
?>