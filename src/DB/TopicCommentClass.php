<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class TopicCommentClass extends MySQL{
    private $table_name = "topic_comments";

    public function __construct(){
        parent::__construct();
    }

    public function getAllCommentsByTopicId($topicId){
        $row='';
        if($topicId > 0){
            $topicId = mysqli_real_escape_string($this->connection, trim($topicId));
            $sql = $this->connection -> prepare('SELECT DISTINCT * FROM topic_comments WHERE topic_id = ? ORDER BY created');
            $sql -> bind_param('i', $topicId);
            $row = $this->Execute($sql);
        }
        return $row;
    }

    public function addComment($newComment){
        $now = new DateTime();
        $currentDate = $now->format('Y-m-d H:i:s');
        $newCommentId=0;

        $stmtInsert = $this->connection -> prepare('INSERT INTO topic_comments(comment,topic_id,user_id,created) VALUES(?,?,?,?)');
        $stmtInsert -> bind_param('siis', $newComment['imdbId'], $newComment['topicId'], $newComment['userId'], $currentDate);
        if($stmtInsert -> execute()){
            $newCommentId = mysqli_insert_id($this->connection);
            $stmtInsert->close();
            return json_encode(["success" => 1, "msg"=> "Success", "newCommentId" => $newCommentId]);
        }
        return json_encode(["success"=>0,"msg"=>"Comment Not Inserted!"]);
    }
}
?>