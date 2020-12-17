<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        $page_title = "Filmforumet";
        include_once 'views/head.php'; 

        include_once 'src/DB/TopicClass.php';
        $topic = new TopicClass();
    
        $allTopics = $topic->getAllTopics();

        include_once 'src/DB/TopicCommentClass.php';
        $topicComment = new TopicCommentClass();
    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div id="topics-cover">
                        <?php
                        foreach ($allTopics as $topic){
                                  
                            $commentInfo = $topicComment->getCommentsInfo($topic['id']);
                            $commentInfo= json_decode($commentInfo,true);
                            echo '<div class="topic-card">
                                <div class="topic-item-container">
                                    <div class="topic-card-little-picture">
                                        <a href="./topic.php?id='.$topic['id'].'">
                                            <img class="topic-img" src="'.$topic['image_path'].'">
                                        </a>
                                    </div>
                                    <div class="d-flex flex-direction-column align-items-center mt-5">
                                        <div>'.$topic['name'].'</div>
                                        <div>'.$topic['year'].'</div>
                                        <label>Antal inlägg: '.$commentInfo['sumComments'].'</label>
                                        <label>Senaste inlägg: '.$commentInfo['maxCreated'].'</label>
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
