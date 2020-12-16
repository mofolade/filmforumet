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
             ORDER BY name";
        $select = $this->Execute($sql);

        return $select;
    }

    public function allUser(){
        $row='';
        $select = $this->Select($this->table_name,"WHERE is_active = 1","name","","");
        echo json_encode($select);
        if ($result = $this->conn->query($select)) {

            if($result) $row = mysqli_fetch_assoc($result);
        }
        return $row;
    }

    public function addUser($newUser){
        $userId=0;
        $adminErrMsg=0;
        $stmt = $this->connection -> prepare('SELECT id FROM users WHERE name = ? AND is_active = 1 LIMIT 1');
        $stmt -> bind_param('s', $newUser['username']);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($userId);
        $stmt -> fetch();

        if($userId > 0){
            return json_encode(["success"=>0,"msg"=> "Sorry... username already taken"]);
        }
        else{

            $stmt = $this->connection -> prepare('SELECT id FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
            $stmt -> bind_param('s', $newUser['email']);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($userId);
            $stmt -> fetch();

            if($userId > 0){
                return json_encode(["success"=>0,"msg"=> "Sorry... email already taken"]);
            }
            else{
                $hash = password_hash($newUser['password'], PASSWORD_DEFAULT);
                $stmt = $this->connection -> prepare('INSERT INTO users(name,email,password) VALUES(?,?,?)');
                $stmt -> bind_param('sss', $newUser['username'], 
                                            $newUser['email'], 
                                            $hash);
                if($stmt -> execute()){
                    $last_id = mysqli_insert_id($this->connection);
                    $stmt->close();

                    return json_encode(["success"=>1,"msg"=>"User Inserted.","id"=>$last_id, "adminErrMsg" => $adminErrMsg]);
                }
                else{
                    return json_encode(["success"=>0,"msg"=>"User Not Inserted!"]);
                }
            }
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
                
                $stmt = $this->connection -> prepare('UPDATE users SET name = ? , email = ?, is_club_admin = ? WHERE id = ? ;');
                $stmt -> bind_param('ssii', $updateUser['username'], 
                                           $updateUser['email'],
                                           $updateUser['isClubAdmin'],
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