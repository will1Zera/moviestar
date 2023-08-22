<?php 
    // Classe que manipula as mensagens do sistema e redireciona a página se for preciso
    class Message{
        private $url;

        public function __construct($url){
            $this->url = $url;
        }

        // Define a mensagem
        public function setMessage($msg, $type, $redirect = "index.php"){
            $_SESSION["msg"] = $msg;
            $_SESSION["type"] = $type;

            if($redirect != "back"){
                header("Location: $this->url" . $redirect); // Redireciona para a página escolhida
            } else{
                header("Location: " . $_SERVER["HTTP_REFERER"]); // Redireciona para a última página
            }
        }

        // Exibe a mensagem
        public function getMessage(){
            if(!empty($_SESSION["msg"])){
                return [ // Retorna os valores da mensagem na SESSION em um array
                    "msg" => $_SESSION["msg"],
                    "type" => $_SESSION["type"]
                ];
            } else{
                return false;
            }
        }

        // Limpa a mensagem
        public function clearMessage(){
            // Remove os dados da sessão
            $_SESSION["msg"] = "";
            $_SESSION["type"] = "";
        }
    }
?>