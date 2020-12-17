<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class TopicCommentClass extends MySQL{
    private $table_name = "topic_comments";

    public function __construct(){
        parent::__construct();
    }

    public function getAllCommentsByTopicId($topicId){
        $comment='';
        $user_id=0;
        $created='';
        $picture_url='';
        $id=0;
        $name='';
        $getComment=[];
        $comments=[];

        if($topicId > 0){
            $stmt = $this->connection -> prepare('SELECT DISTINCT t.id,t.comment,t.user_id,t.created,u.name,u.picture_url
                                                    FROM topic_comments t,
                                                         users u
                                                    WHERE t.user_id = u.id
                                                      AND topic_id = ? ORDER BY created desc');
            $stmt -> bind_param('i', $topicId);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($id,$comment,$user_id,$created,$name,$picture_url);
            while ($stmt->fetch()) {
                $getComment = array("id" => $id,
                                    "comment" => $comment,
                                    "user_id" => $user_id,
                                    "created" => $created,
                                    "name" => $name,
                                    "picture_url" => $picture_url);
                array_push($comments, $getComment);
            }
            $stmt->close();
        }
        return $comments;
    }

    public function getCommentsInfo($topicId){
        $sumComments=0;
        $maxCreated='';

        if($topicId > 0){
            $stmt = $this->connection -> prepare('SELECT DISTINCT count(id) sumComments,max(created) maxCreated
                                                    FROM topic_comments
                                                    WHERE topic_id = ?');
            $stmt -> bind_param('i', $topicId);
            $stmt -> execute();
            $stmt -> bind_result($sumComments,$maxCreated);
            $stmt -> fetch();
            $sumComments = $sumComments;
            $maxCreated=$maxCreated;
            return json_encode(["success" => 1, "sumComments"=> $sumComments, "maxCreated" => $maxCreated]);
        }
        return json_encode(["success" => 0, "sumComments"=> 0, "maxCreated" => '']);
    }

    public function addComment($newComment){
        $now = new DateTime();
        $currentDate = $now->format('Y-m-d H:i:s');
        $newCommentId=0;

        $stmtInsert = $this->connection -> prepare('INSERT INTO topic_comments(comment,topic_id,user_id,created) VALUES(?,?,?,?)');
        $stmtInsert -> bind_param('siis', $newComment['description'], $newComment['topicId'], $newComment['userId'], $currentDate);
        if($stmtInsert -> execute()){
            $newCommentId = mysqli_insert_id($this->connection);
            $stmtInsert->close();
            return json_encode(["success" => 1, "msg"=> "Success", "newCommentId" => $newCommentId]);
        }
        return json_encode(["success"=>0,"msg"=>"Comment Not Inserted!"]);
    }
}
?>