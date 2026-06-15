<?php
class UserController {
    private $user;

    public function __construct($db) {
        $this->user = new User($db);
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(!isset($data['username']) || empty($data['username'])) {
            echo json_encode(["success" => false, "message" => "Brak nazwy użytkownika"]);
            return;
        }
        if(!isset($data['email']) || empty($data['email'])) {
            echo json_encode(["success" => false, "message" => "Brak email"]);
            return;
        }
        if(!isset($data['password']) || empty($data['password'])) {
            echo json_encode(["success" => false, "message" => "Brak hasła"]);
            return;
        }
        if(strlen($data['password']) < 4) {
            echo json_encode(["success" => false, "message" => "Hasło musi mieć minimum 4 znaki"]);
            return;
        }
        
        $this->user->username = $data['username'];
        $this->user->email = $data['email'];
        $this->user->password = $data['password'];
        
        if($this->user->register()) {
            echo json_encode(["success" => true, "message" => "Rejestracja udana! Możesz się zalogować."]);
        } else {
            echo json_encode(["success" => false, "message" => "Użytkownik lub email już istnieje"]);
        }
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(!isset($data['username']) || empty($data['username'])) {
            echo json_encode(["success" => false, "message" => "Brak nazwy użytkownika lub email"]);
            return;
        }
        if(!isset($data['password']) || empty($data['password'])) {
            echo json_encode(["success" => false, "message" => "Brak hasła"]);
            return;
        }
        
        $this->user->username = $data['username'];
        $this->user->password = $data['password'];
        
        if($this->user->login()) {
            session_start();
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['username'] = $this->user->username;
            $_SESSION['email'] = $this->user->email;
            
            echo json_encode([
                "success" => true, 
                "message" => "Zalogowano pomyślnie!",
                "user" => [
                    "id" => $this->user->id,
                    "username" => $this->user->username,
                    "email" => $this->user->email
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Nieprawidłowa nazwa użytkownika lub hasło"]);
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        echo json_encode(["success" => true, "message" => "Wylogowano pomyślnie"]);
    }

    public function check() {
        session_start();
        if(isset($_SESSION['user_id'])) {
            echo json_encode([
                "success" => true,
                "logged_in" => true,
                "user" => [
                    "id" => $_SESSION['user_id'],
                    "username" => $_SESSION['username'],
                    "email" => $_SESSION['email']
                ]
            ]);
        } else {
            echo json_encode([
                "success" => true,
                "logged_in" => false
            ]);
        }
    }
}
?>