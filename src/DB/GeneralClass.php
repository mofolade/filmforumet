<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class GeneralClass extends MySQL{
 
    public function __construct(){
        parent::__construct();
    }

    public function addTopic($name){
        $now = new DateTime();
        $currentDate = $now->format('Y-m-d H:i:s');
        $newTopicId=0;

        $stmtInsert = $this->connection -> prepare('INSERT INTO topics(name,created) 
                                                VALUES(?,?)');
        $stmtInsert -> bind_param('ss', $name, $currentDate);
        if($stmtInsert -> execute()){
            $newTopicId = mysqli_insert_id($this->connection);
            $stmtInsert->close();
            return json_encode(["success" => 1, "msg"=> "Success", "topicId" => $newTopicId]);
        }

        return json_encode(["success" => 0, "msg"=> "Not inserted", "topicId" => $newTopicId]);
    }

    public function getTopic($topicId){
        if($topicId){
            $topicId = mysqli_real_escape_string($this->connection, trim($topicId));
            $sql = $this->connection -> prepare('SELECT DISTINCT * FROM topics WHERE id = ?');
            $sql -> bind_param('i', $topicId);
        }
        $row = $this->Execute($sql);
        return $row;
    }
 
}
?>