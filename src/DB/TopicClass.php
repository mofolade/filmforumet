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
        $year=null;
        $is_open=0;
        if($topicId > 0){
            //$topicId = mysqli_real_escape_string($this->connection, trim($topicId));
            $stmt = $this->connection -> prepare('SELECT name, image_path, description, year, isOpen FROM topics WHERE id = ?');
            $stmt -> bind_param('i', $topicId);
            $stmt -> execute(); // get the mysqli result
            $stmt -> store_result(); 
            $stmt -> bind_result($name, $image_path, $description,$year,$is_open);
            $stmt -> fetch();
            $stmt -> free_result(); 
            return json_encode(["success"=>1,"name"=>$name, "image_path"=>$image_path, "description"=>$description, "year" => $year, "is_open" => $is_open]);
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

            $stmtInsert = $this->connection -> prepare('INSERT INTO topics(imdb_id,name,description,year,created) 
                                                    VALUES(?,?,?,?,?)');
            $stmtInsert -> bind_param('sssis', $newTopic['imdbId'], $newTopic['name'], $newTopic['description'], $newTopic['year'], $currentDate);
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

    public function closureTopic($topicId){
        $stmt = $this->connection -> prepare('UPDATE topics SET isOpen = 0 WHERE id = ? ;');
        $stmt -> bind_param('i', $topicId);
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