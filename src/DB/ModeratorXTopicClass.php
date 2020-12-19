<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class ModeratorXTopicClass extends MySQL{
    private $table_name = "user_role";

    public function __construct(){
        parent::__construct();
    }

    function getModeratorTopicsRights($moderatorId){
        $topic_id=0;
        $rights=[];
        //select all data
        $stmt =$this->connection -> prepare("SELECT topic_id
                                               FROM moderatorxtopic
                                              WHERE user_id = ? ");
        $stmt -> bind_param('i', $moderatorId);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($topic_id);

        while ($stmt->fetch()) {
            array_push($rights, $topic_id);
        }
        $stmt->close();
            
        return $rights;
    }
    

    public function addModeratorTopicRight($moderatorId,$topicId){
        $moderatorRightId=0;
        $stmt = $this->connection -> prepare('SELECT id FROM moderatorxtopic WHERE user_id = ? AND topic_id = ?');
        $stmt -> bind_param('ii', $moderatorId, $topicId);
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

            $stmtInsert = $this->connection -> prepare('INSERT INTO moderatorxtopic(user_id, topic_id ,created) 
                                                    VALUES(?,?,?)');
            $stmtInsert -> bind_param('iis', $moderatorId, $topicId, $currentDate);
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

    public function deleteModeratorTopicRight($moderatorId,$topicId){

        $moderatorRightId = 0;
        $stmt = $this->connection -> prepare('SELECT id FROM moderatorxtopic WHERE user_id = ? AND topic_id = ?');
        $stmt -> bind_param('ii', $moderatorId, $topicId);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($moderatorRightId);
        $moderatorRightId = $moderatorRightId;
        $stmt -> fetch();
        $stmt -> close();

        if ($moderatorRightId > 0){
            $stmt = $this->connection -> prepare('DELETE FROM moderatorxtopic WHERE user_id = ? AND topic_id = ?');
            $stmt -> bind_param('ii', $moderatorId, $topicId);
            $stmt -> execute();
        }
        return null;
    }
}
?>