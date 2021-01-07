<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class AuthClass extends MySQL{
 
    private $table_name = "users";

    public function __construct(){
        parent::__construct();
    }


    /**
     * Login the user based on login and password parameters 
     * @return boolean
     */
    public function login($login) {
        $userName = '';
        $userId=0;
        $password = '';
        $roleId=0;

        $stmt = $this->connection -> prepare('SELECT id, name, password FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt -> bind_param('s', $login['email']);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($userId, $userName, $password);
        $stmt -> fetch();
        if($userId > 0){            
            //if(password_verify($login['password'], $password)){
            if(password_verify($login['password'], $password)){
                $this->setSession($userId);
                $this->setSessionDb($userId);
                return json_encode(["success" => 1,
                                    "user_id" => $userId,
                                    "name" => $userName,
                                    "msg"=> "You are now logged in."]);
            }
        }
        return json_encode(["success"=> 0,"msg"=> "Login failed."]);
    }

    private function setSession($userId) {
        $_SESSION['user_id'] = $userId;
    }

    private function setSessionDb($userId) {
        $session_id=session_id();
        $ip = $this->getIp();
        $stmtInsert = $this->connection -> prepare('INSERT INTO `users_session` (`user_id`, `session_id`, `host`, `time`) 
                    VALUES (?, ?, ?, ?)');
        $stmtInsert -> bind_param('isss', $userId,$session_id,$ip,time());
        if($stmtInsert -> execute()){
            $newSession = mysqli_insert_id($this->connection);
            $stmtInsert->close();
        }    
    }

    public function deleteSessionDb($userId){
        $session_id=session_id();
        $stmt = $this->connection -> prepare('DELETE FROM users_session WHERE session_id = ? AND user_id = ? ');
        $stmt -> bind_param('si', $session_id,$userId);
        if($stmt -> execute()){
            $stmt->close();
        }
    }

    public function checkUser($userId){
        $session_id=session_id();
        $ip = $this->getIp();
        $stmt = $this->connection -> prepare('SELECT user_id FROM users_session WHERE user_id = ? AND session_id = ? AND host =?');

        $stmt -> bind_param('iss', $userId,$session_id,$ip);
        if($stmt -> execute()){
            $stmt -> store_result();
            $stmt -> bind_result($userId);
            $userId = $userId;
            $stmt -> fetch();
            return json_encode(["success"=>1,"msg"=>"User is logged in."]);
        }  
        return json_encode(["success"=>0,"msg"=>"User is not logged in!"]);
    }

    public function getIp() {
        if ($this->validIp($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
            if ($this->validIp(trim($ip))) {
                return $ip;
            }
        }
        if ($this->validIp($_SERVER["HTTP_PC_REMOTE_ADDR"])) {
                return $_SERVER["HTTP_PC_REMOTE_ADDR"];
        } elseif ($this->validIp($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif ($this->validIp($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif ($this->validIp($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }
    
    public  function validIp($ip) {
        if (!empty($ip) && ip2long($ip)!=-1) {
            $reserved_ips = array (
            array('0.0.0.0','2.255.255.255'),
            array('10.0.0.0','10.255.255.255'),
            array('127.0.0.0','127.255.255.255'),
            array('169.254.0.0','169.254.255.255'),
            array('172.16.0.0','172.31.255.255'),
            array('192.0.2.0','192.0.2.255'),
            array('192.168.0.0','192.168.255.255'),
            array('255.255.255.0','255.255.255.255')
            );
      
            foreach ($reserved_ips as $r) {
                $min = ip2long($r[0]);
                $max = ip2long($r[1]);
                if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
            }
            return true;
        } else {
            return false;
        }
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