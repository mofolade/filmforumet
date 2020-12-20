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
        $antecedent_comment_id=0;
        $isSpecial=0;
        $isSpoiler=0;
        $picture_url='';
        $id=0;
        $name='';
        $getComment=[];
        $comments=[];
        $antecedent='';

        if($topicId > 0){
            $stmt = $this->connection -> prepare('SELECT DISTINCT t.id,
                                                                  t.comment,
                                                                  t.antecedent_comment_id,
                                                                  t.user_id,
                                                                  t.created,
                                                                  t.isSpecial,
                                                                  t.isSpoiler,
                                                                  u.name,
                                                                  u.picture_url,
                                                                  (SELECT tc.comment
                                                                     FROM topic_comments tc
                                                                    WHERE tc.id = t.antecedent_comment_id
                                                                    Limit 1) as antecedent
                                                    FROM topic_comments t,
                                                         users u
                                                    WHERE t.user_id = u.id
                                                      AND t.topic_id = ? 
                                                      AND t.isActive = 1
                                                 ORDER BY created desc');
            $stmt -> bind_param('i', $topicId);
            $stmt -> execute();
            $stmt -> store_result();
            $stmt -> bind_result($id,$comment,$antecedent_comment_id,$user_id,$created,$isSpecial,$isSpoiler,$name,$picture_url,$antecedent);
            while ($stmt->fetch()) {
                $getComment = array("id"                    => $id,
                                    "comment"               => $comment,
                                    "antecedent_comment_id" => $antecedent_comment_id,
                                    "user_id"               => $user_id,
                                    "created"               => $created,
                                    "isSpecial"             => $isSpecial,
                                    "isSpoiler"             => $isSpoiler,
                                    "name"                  => $name,
                                    "picture_url"           => $picture_url,
                                    "antecedent"            => $antecedent);
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
        $isSpecial = 0;

        if(isset($newComment['isSpecial'])){
            $isSpecial = 1;
        }
        else{
            $isSpecial = 0;
        }

        $stmtInsert = $this->connection -> prepare('INSERT INTO topic_comments(comment,topic_id,user_id,antecedent_comment_id,isSpecial,created) 
                                                    VALUES(?,?,?,?,?,?)');
        $stmtInsert -> bind_param('siiiis', $newComment['description'], $newComment['topicId'], $newComment['userId'], $newComment['commentId'], $isSpecial, $currentDate);
        if($stmtInsert -> execute()){
            $newCommentId = mysqli_insert_id($this->connection);
            $stmtInsert->close();
            return json_encode(["success" => 1, "msg"=> "Success", "newCommentId" => $newCommentId]);
        }
        return json_encode(["success"=>0,"msg"=>"Comment Not Inserted!"]);
    }

    public function setSpoilerComment($commentId){
        $stmt = $this->connection -> prepare('UPDATE topic_comments SET isSpoiler = 1 WHERE id = ? ;');
        $stmt -> bind_param('i', $commentId);
        if($stmt -> execute()){
            $stmt->close();                    
            return json_encode(["success"=>1,"msg"=>"Success."]);
        }
        else{
            return json_encode(["success"=>0,"msg"=>"Not Updated!"]);
        }
    }

    public function setInactiveComment($commentId){
        $stmt = $this->connection -> prepare('UPDATE topic_comments SET isActive = 0 WHERE id = ? ;');
        $stmt -> bind_param('i', $commentId);
        if($stmt -> execute()){
            $stmt->close();                    
            return json_encode(["success"=>1,"msg"=>"Success."]);
        }
        else{
            return json_encode(["success"=>0,"msg"=>"Not Updated!"]);
        }
    }
}
?>