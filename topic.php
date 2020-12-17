<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        $page_title = "Filmforumet - Topic";
        include_once 'views/head.php'; 

        if (!empty($_SESSION["user_id"])) {
            include_once './src/DB/AuthClass.php';
            $auth = new AuthClass();
            $currentUser = $auth->getUser($_SESSION["user_id"]);
            $currentUser = json_decode($currentUser, true);
        }

        include_once 'src/DB/TopicCommentClass.php';
        $topicComment = new TopicCommentClass();    

        if(isset($_GET['id'])){
            $topicId = $_GET['id'];
            include_once 'src/DB/TopicClass.php';
            $topic = new TopicClass();        
            $getTopic = $topic->getTopic($topicId);
            $getTopic = json_decode($getTopic, true);
                    
            $allComments = $topicComment->getAllCommentsByTopicId($topicId);
    
        }

        if(isset($_POST['newComment'])) {
            $newComment = $_POST['newComment'];
            $topicComment = new TopicCommentClass();
            $newCommentResp = $topicComment->addComment($newComment);
            echo "<script>window.location.href='./topic.php?id=".$_GET['id']."';</script>";
            exit;
        }
    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div class="container">
                    <?php
                    if(isset($_GET['id'])){
                        echo '<div class="topic-info-box">
                                <h2>'.$getTopic['name'].'</h2>                      
                                <span class="">'.$getTopic['description'].'</span>
                            </div>';
                        echo '<div  id="comments-cover">';
                        if(!empty($_SESSION["user_id"])){
                            echo '  <div class="comment-card">
                                        <div class="comment-item-container">
                                            <div class="user-profil-box">
                                                <a href="./profil?id='.$currentUser['user_id'].'" >
                                                    <div class="comment-avatar">
                                                        <img src=".'.$currentUser['picture_url'].'" title="">
                                                    </div>
                                                </a>
                                                <span class="">'.$currentUser['name'].'</span>
                                            </div>
                                            <div class="comment-box">
                                                <div style="height: 80%;">
                                                    <form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">
                                                        <textarea name="newComment[description]"  id="description" required="required"></textarea>
                                                        <input type="hidden" name="newComment[topicId]" value="'.$_GET['id'].'">
                                                        <input type="hidden" name="newComment[userId]" value="'.$currentUser['user_id'].'">
                                                        <input type="submit" class="btn-edit" value="Skicka" style="margin-bottom: 5px">
                                                        </form>
                                                </div>                                 
                                            </div>
                                        </div>
                                    </div>';
                        }
                        foreach ($allComments as $comment){
                            echo '<div class="comment-card" style="height: 150px;">
                                <div class="comment-item-container">
                                    <div class="user-profil-box">
                                        <a href="./profil?id='.$comment['user_id'].'" >
                                            <div class="comment-avatar">
                                                <img src=".'.$comment['picture_url'].'" title="'.$comment['name'].'">
                                            </div>
                                        </a>';
                            echo    '<span class="">'.$comment['name'].'</span>';
                            echo '  </div>
                                    <div class="comment-box">
                                        <div class="edit-links">
                                            <a href="">
                                                <button class="btn-edit" type="submit">Redigera
                                                </button>
                                            </a>
                                            <a href="">
                                                <button class="btn-delete" type="submit">Ta bort
                                                </button>
                                            </a>
                                        </div>
                                        <div style="height: 80%;">'.$comment['comment'].'</div>
                                        <div><label>'.$comment['created'].'</label></div>                                         
                                    </div>
                                </div>
                            </div>';
                        }
                        echo '</div>';
                    }
                    ?>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
