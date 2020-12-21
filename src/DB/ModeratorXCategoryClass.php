<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class ModeratorXCategoryClass extends MySQL{
    private $table_name = "user_role";

    public function __construct(){
        parent::__construct();
    }

    function getModeratorCategoriesRights($moderatorId){
        $category_id=0;
        $rights=[];
        //select all data
        $stmt =$this->connection -> prepare("SELECT category_id
                                               FROM moderatorxcategory
                                              WHERE user_id = ? ");
        $stmt -> bind_param('i', $moderatorId);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($category_id);

        while ($stmt->fetch()) {
            array_push($rights, $category_id);
        }
        $stmt->close();
            
        return $rights;
    }
    

    public function addModeratorCategoryRight($moderatorId,$categoryId){
        $moderatorRightId=0;
        $stmt = $this->connection -> prepare('SELECT id FROM moderatorxcategory WHERE user_id = ? AND category_id = ?');
        $stmt -> bind_param('ii', $moderatorId, $categoryId);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($moderatorRightId);
        $stmt -> fetch();

        if($moderatorRightId > 0){
            return json_encode(["success"=>0,"msg"=> "Sorry... right already exists"]);
        }
        else{
            $now = new DateTime();
            $currentDate = $now->format('Y-m-d H:i:s');
            $newRightId=0;

            $stmtInsert = $this->connection -> prepare('INSERT INTO moderatorxcategory(user_id, category_id ,created) 
                                                    VALUES(?,?,?)');
            $stmtInsert -> bind_param('iis', $moderatorId, $categoryId, $currentDate);
            if($stmtInsert -> execute()){
                $newRightId = mysqli_insert_id($this->connection);
                $stmtInsert->close();
                return json_encode(["success" => 1, "msg"=> "Success", "moderatorRightId" => $newRightId]);
            }
            else{
                return json_encode(["success"=>0,"msg"=>"User role Not Inserted!"]);
            }
        }
        return json_encode(["success"=>0,"msg"=>"User role Not Inserted!"]);
    }

    public function deleteModeratorTopicRight($moderatorId,$categoryId){

        $moderatorRightId = 0;
        $stmt = $this->connection -> prepare('SELECT id FROM moderatorxcategory WHERE user_id = ? AND category_id = ?');
        $stmt -> bind_param('ii', $moderatorId, $categoryId);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($moderatorRightId);
        $moderatorRightId = $moderatorRightId;
        $stmt -> fetch();
        $stmt -> close();

        if ($moderatorRightId > 0){
            $stmt = $this->connection -> prepare('DELETE FROM moderatorxcategory WHERE user_id = ? AND category_id = ?');
            $stmt -> bind_param('ii', $moderatorId, $categoryId);
            $stmt -> execute();
        }
        return null;
    }
}
?>