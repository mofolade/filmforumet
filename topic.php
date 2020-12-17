<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        $page_title = "Filmforumet - Topic";
        include_once 'views/head.php'; 

        if(isset($_GET['id'])){
            $topicId = $_GET['id'];
            include_once 'src/DB/TopicClass.php';
            $topic = new TopicClass();        
            $getTopic = $topic->getTopic($topicId);
            $getTopic = json_decode($getTopic, true);

            include_once 'src/DB/TopicCommentClass.php';
            $topicComment = new TopicCommentClass();            
            $allComments = $topicComment->getAllCommentsByTopicId($topicId);
    
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
                    
                        foreach ($allComments as $comment){
                            echo '<div class="comment-card">
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
                                                <button data-bid-submit-button="" class="btn-edit" type="submit">Redigera
                                                </button>
                                            </a>
                                            <a href="">
                                                <button data-bid-submit-button="" class="btn-delete" type="submit">Ta bort
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
