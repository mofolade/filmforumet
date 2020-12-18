<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class UserXRoleClass extends MySQL{
    private $table_name = "user_role";

    public function __construct(){
        parent::__construct();
    }

    function getAllUserWithRoles(){
        $user_id=0;
        $role_id=0;
        $is_active=0;
        $created='';
        $role_name='';
        $user_role_id=0;
        $roles=[];
        $getRoles=[];
        //select all data
        $stmt =$this->connection -> prepare("SELECT ur.user_id,r.id role_id, ur.is_active, ur.created, r.name role_name,ur.id user_role_id
                 FROM userxrole ur,
                      roles r
                WHERE ur.role_id = r.id
             ORDER BY ur.user_id, ur.id");
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($user_id,$role_id,$is_active,$created,$role_name,$user_role_id);

        while ($stmt->fetch()) {
            $getRoles = array("user_id"         => $user_id,
                              "role_id"         => $role_id,
                              "is_active"       => $is_active,
                              "created"         => $created,
                              "role_name"       => $role_name,
                              "user_role_id"    => $user_role_id);
            array_push($roles, $getRoles);
        }
        $stmt->close();
            
        return $roles;
    }
    

    public function getUserRole($userId){
        $role_id=0;
        $roles=[];
        $stmt = $this->connection -> prepare('SELECT role_id FROM userxrole WHERE user_id = ?');
        $stmt -> bind_param('i', $userId);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($role_id);
        while ($stmt->fetch()) {
            array_push($roles, $role_id);
        }
        $stmt->close();
        return $roles;
    }

    public function addUserRole($userId,$roleId){
        $userRoleId=0;
        $stmt = $this->connection -> prepare('SELECT id FROM userxrole WHERE user_id = ? AND role_id = ?');
        $stmt -> bind_param('ii', $userId, $roleId);
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

            $stmtInsert = $this->connection -> prepare('INSERT INTO userxrole(user_id, role_id ,created) 
                                                    VALUES(?,?,?)');
            $stmtInsert -> bind_param('iis', $userId, $roleId, $currentDate);
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

    public function updateUserRoles($userId, $roleId,$method){

        if($method == 'addRole' || $method == 'deleteRole'){
            $userRoleId = 0;
            $stmt = $this->connection -> prepare('SELECT id FROM userxrole WHERE user_id = ? AND role_id = ?');
            $stmt -> bind_param('ii', $userId, $roleId);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($userRoleId);
            $userRoleId = $userRoleId;
            $stmt -> fetch();
            $stmt -> close();

            if($method == 'addRole' && $userRoleId == 0){
                $newRole= array();
                $resp = $this-> addUserRole($userId,$roleId);
            }
            else if ($method == 'deleteRole' && $userRoleId > 0){
                $stmt = $this->connection -> prepare('DELETE FROM userxrole WHERE user_id = ? AND role_id = ?');
                $stmt -> bind_param('ii', $userId, $roleId);
                $stmt -> execute();
            }
        }
        return null;
    }
}
?>