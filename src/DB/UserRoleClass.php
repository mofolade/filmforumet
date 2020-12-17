<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class UserRoleClass extends MySQL{
    private $table_name = "user_role";

    public function __construct(){
        parent::__construct();
    }

    public function getAllUserRole($userId){
        $row='';
        $select = $this->connection -> prepare('SELECT id FROM user_role WHERE user_id = ?');
        $select -> bind_param('i', $userId);
        $select -> execute();
        if ($result = $this->conn->query($select)) {

            if($result) $row = mysqli_fetch_assoc($result);
        }
        return $row;
    }

    public function addUserRole($newRole){
        $userRoleId=0;
        $stmt = $this->connection -> prepare('SELECT id FROM user_role WHERE user_id = ? AND role_id = ?');
        $stmt -> bind_param('ii', $newRole['user_id'], $newRole['role_id']);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($userRoleId);
        $stmt -> fetch();

        if($userRoleId > 0){
            return json_encode(["success"=>0,"msg"=> "Sorry... role already exists"]);
        }
        else{
            $now = new DateTime();
            $currentDate = $now->format('Y-m-d H:i:s');
            $newTopicId=0;

            $stmtInsert = $this->connection -> prepare('INSERT INTO user_role(user_id, role_id ,created) 
                                                    VALUES(?,?,?)');
            $stmtInsert -> bind_param('iis', $newRole['user_id'], $newRole['role_id'], $currentDate);
            if($stmtInsert -> execute()){
                $newUserRoleId = mysqli_insert_id($this->connection);
                $stmtInsert->close();
                return json_encode(["success" => 1, "msg"=> "Success", "userRoleId" => $newUserRoleId]);
            }
            else{
                return json_encode(["success"=>0,"msg"=>"User role Not Inserted!"]);
            }
        }
        return json_encode(["success"=>0,"msg"=>"User role Not Inserted!"]);
    }
}
?>