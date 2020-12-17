<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class TopicClass extends MySQL{
    private $table_name = "topics";

    public function __construct(){
        parent::__construct();
    }

    public function getAllTopics(){
        $result = $this-> Select("topics");        
        return $result;
    }

    public function getTopic($topicId){
        $name='';
        $image_path='';
        $description='';
        if($topicId > 0){
            //$topicId = mysqli_real_escape_string($this->connection, trim($topicId));
            $stmt = $this->connection -> prepare('SELECT name, image_path, description FROM topics WHERE id = ?');
            $stmt -> bind_param('i', $topicId);
            $stmt -> execute(); // get the mysqli result
            $stmt -> store_result(); 
            $stmt -> bind_result($name, $image_path, $description);
            $stmt -> fetch();
            $stmt -> free_result(); 
            return json_encode(["success"=>1,"name"=>$name, "image_path"=>$image_path, "description"=>$description]);
        }
        return json_encode(["success"=>0,"msg"=>"Topic does not exist!"]);
    }

    public function addTopic($newTopic){
        $topicId=0;
        $stmt = $this->connection -> prepare('SELECT id FROM topics WHERE imdb_id = ? LIMIT 1');
        $stmt -> bind_param('s', $newTopic['imdbId']);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($topicId);
        $stmt -> fetch();

        if($topicId > 0){
            return json_encode(["success"=>0,"msg"=> "Sorry... topic already taken"]);
        }
        else{
            $now = new DateTime();
            $currentDate = $now->format('Y-m-d H:i:s');
            $newTopicId=0;

            $stmtInsert = $this->connection -> prepare('INSERT INTO topics(imdb_id,name,year,created) 
                                                    VALUES(?,?,?,?)');
            $stmtInsert -> bind_param('ssis', $newTopic['imdbId'], $newTopic['name'], $newTopic['year'], $currentDate);
            if($stmtInsert -> execute()){
                $newTopicId = mysqli_insert_id($this->connection);
                $stmtInsert->close();
                return json_encode(["success" => 1, "msg"=> "Success", "topicId" => $newTopicId]);
            }
        }
        return json_encode(["success"=>0,"msg"=>"Topic Not Inserted!"]);
    }

    public function updateTopicImage($topicId, $imagePath){
        $stmt = $this->connection -> prepare('UPDATE topics SET image_path = ? WHERE id = ? ;');
        $stmt -> bind_param('si', $imagePath,$topicId);
        if($stmt -> execute()){
            $stmt->close();                    
            return json_encode(["success"=>1,"msg"=>"Success."]);
        }
        else{
            return json_encode(["success"=>0,"msg"=>"Not Updated!"]);
        }
        return null;
    }
}
?>