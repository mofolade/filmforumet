<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class AuthClass extends MySQL{
 
    private $table_name = "users";

    public function __construct(){
        parent::__construct();
    }

    public function getUser($userID){
        $userId = 0; 
        $userName = '';
        $email = '';

        $stmt = $this->connection -> prepare('SELECT u.id, 
                                                     u.name, 
                                                     u.email
                                               FROM users u
                                              WHERE u.id = ?
                                                AND u.is_active = 1 LIMIT 1');
        $stmt -> bind_param('i', $userID);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($userId, $userName, $email);
        $stmt -> fetch();
        
        return json_encode(["user_id"       => $userId,
                            "name"          => $userName,
                            "email"         => $email]);
       

    }

    /**
     * Login the user based on login and password parameters 
     * @return boolean
     */
    public function login($login) {
        $userName = '';
        $userId=0;
        $password = '';
        $role='';

        $stmt = $this->connection -> prepare('SELECT id, name, password, role FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt -> bind_param('s', $login['email']);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($userId, $userName, $password, $role);
        $stmt -> fetch();
        if($userId > 0){
            if(password_verify($login['password'], $password)){
                $this->setSession($userId);
                return json_encode(["success" => 1,
                                    "user_id" => $userId,
                                    "name" => $userName,
                                    "role" => $role,
                                    "msg"=> "You are now logged in."]);
            }
            else{
                return json_encode(["success"=> 0,"msg"=> "Login failed."]);
            }
        }else{
            return json_encode(["success"=> 0,"msg"=> "Login failed."]);
        }
        return json_encode(["success"=> 0,"msg"=> "Login failed."]);
    }

    private function setSession($userId) {
        $_SESSION['user_id'] = $userId;
    }

    /**
     * Returns the session based on the key
     * @param type $key
     * @return type
     */
    public function getSession($key){
        return null;
    }

    /**
     * Kill the session instances.
     */
    static function logout() {
        unset($_SESSION['id']);
        unset($_SESSION['login']);
        unset($_SESSION['password']);
    }

    public function find(){

    }



 
}
?>