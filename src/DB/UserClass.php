<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class UserClass extends MySQL{
 
    private $table_name = "users";

    public function __construct(){
        parent::__construct();
    }

    function getAllUser(){
        //select all data
        $sql ="SELECT u.*
                 FROM users u
             ORDER BY u.name";
        $select = $this->Execute($sql);

        return $select;
    }

    public function allUser(){
        $row='';
        $row = $this->Select($this->table_name,"WHERE is_active = 1","name","","");
        return $row;
    }

    public function getUser($userID){
        $userId = 0; 
        $userName = '';
        $email = '';
        $picture_url = '';

        $stmt = $this->connection -> prepare('SELECT u.id, 
                                                     u.name, 
                                                     u.email,
                                                     u.picture_url
                                               FROM users u
                                              WHERE u.id = ?
                                              LIMIT 1');
        $stmt -> bind_param('i', $userID);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($userId, $userName, $email, $picture_url);
        $stmt -> fetch();
        
        return json_encode(["user_id"       => $userId,
                            "name"          => $userName,
                            "email"         => $email,
                            "picture_url"   => $picture_url]);
       

    }

    public function addUser($newUser){
        $userId=0;
        $adminErrMsg=0;
        $stmt = $this->connection -> prepare('SELECT id FROM users WHERE name = ? LIMIT 1');
        $stmt -> bind_param('s', $newUser['username']);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($userId);
        $stmt -> fetch();

        if($userId > 0){
            return json_encode(["success"=>0,"msg"=> "Sorry... username already taken"]);
        }
        else{

            $stmt = $this->connection -> prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt -> bind_param('s', $newUser['email']);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($userId);
            $stmt -> fetch();

            if($userId > 0){
                return json_encode(["success"=>0,"msg"=> "Sorry... email already taken"]);
            }
            else{
                /*
                salt (string) - to manually provide a salt to use when hashing the password. 
                Note that this will override and prevent a salt from being automatically generated.
                If omitted, a random salt will be generated by password_hash() for each password hashed. 
                This is the intended mode of operation.
                Warning
                The salt option has been deprecated as of PHP 7.0.0. It is now preferred to simply use 
                the salt that is generated by default.
                */
                /*file_get_contents('salt.json')*/
                $salt = json_decode(file_get_contents('salt.json'));
                $options = [
                    'salt' => hash('sha256', $salt), //write your own code to generate a suitable salt                    
                    'cost' => 12 // the default cost is 10                    
                    ];
                $hash = password_hash($newUser['password'],PASSWORD_DEFAULT, $options);
                //$hash = password_hash($newUser['password'], PASSWORD_DEFAULT);
                $now = new DateTime();
                $currentDate = $now->format('Y-m-d H:i:s');
                $stmt = $this->connection -> prepare('INSERT INTO users(name,email,password,created) VALUES(?,?,?,?)');
                $stmt -> bind_param('ssss', $newUser['username'], 
                                            $newUser['email'], 
                                            $hash,
                                            $currentDate);
                if($stmt -> execute()){
                    $last_id = mysqli_insert_id($this->connection);
                    $stmt->close();

                    return json_encode(["success"=>1,"msg"=>"User Inserted.","id"=>$last_id]);
                }
                else{
                    return json_encode(["success"=>0,"msg"=>"User Not Inserted!"]);
                }
            }
            $stmt->close();
        }
        return json_encode(["success"=>0,"msg"=>"User Not Inserted!"]);
    }

    public function updateUser($updateUser,$userId){
        $oUserId = 0;
        $stmt = $this->connection -> prepare('SELECT id FROM users WHERE name = ? AND id != ? AND is_active = 1 LIMIT 1');
        $stmt -> bind_param('si', $updateUser['username'], $userId);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($oUserId);
        $stmt -> fetch();

        if($oUserId > 0){
            return json_encode(["success"=>0,"msg"=> "Sorry... username already taken"]);
        }
        else{

            $stmt = $this->connection -> prepare('SELECT id FROM users WHERE email = ? AND id != ? AND is_active = 1 LIMIT 1');
            $stmt -> bind_param('si', $updateUser['email'], $userId);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($oUserId);
            $stmt -> fetch();

            if($oUserId > 0){
                return json_encode(["success"=>0,"msg"=> "Sorry... email already taken"]);
            }
            else{
                
                $stmt = $this->connection -> prepare('UPDATE users SET name = ? , email = ? WHERE id = ? ;');
                $stmt -> bind_param('ssi', $updateUser['username'], 
                                           $updateUser['email'],
                                           $userId);
                if($stmt -> execute()){
                    $stmt->close();                    
                    return json_encode(["success"=>1,"msg"=>"Success."]);
                }
                else{
                    return json_encode(["success"=>0,"msg"=>"Not Updated!"]);
                }
            }
        }
        return null;
    }

    public function deactivateUser($userId){
        $stmt = $this->connection -> prepare('UPDATE users SET is_active = 0 WHERE id = ? ;');
        $stmt -> bind_param('i', $userId);
        if($stmt -> execute()){
            $stmt->close();
            return json_encode(["success"=>1,"msg"=>"Success."]);
        }
        else{
            return json_encode(["success"=>0,"msg"=>"Error!"]);
        }
    }  
 
}
?>