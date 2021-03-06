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

        include_once 'src/ACLSettingsClass.php';
        $ACLSettings = new ACLSettingsClass();    

        if(isset($_GET['id']) && $ACLSettings->categories('GET', 0) == true){
            $allTopics = $topic->getAllTopicsByCategoryId($_GET['id']); 
            include_once 'src/DB/CategoriesClass.php';
            $category = new CategoriesClass();
            $categoryInfo=$category->getCategory($_GET['id']);
            $categoryInfo=json_decode($categoryInfo,true);
        }

        include_once 'src/DB/TopicCommentClass.php';
        $topicComment = new TopicCommentClass();
        

    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div id="topics-container">
                        <?php
                        if(isset($_GET['id'])){
                            echo '  <div class="topic-info-box justify-content-center">
                                        <div style="width: 230px;">
                                            <div class="topic-card-little-picture">
                                                <img class="topic-img" src="'.$categoryInfo['image_path'].'" alt="">
                                            </div>
                                            <div>    
                                                <h2>'.$categoryInfo['name'].'</h2>
                                            </div>
                                        </div>
                                </div>';
                        }
                        echo '<div id="topics-cover">';
                        foreach ($allTopics as $topic){                                  
                            $commentInfo = $topicComment->getCommentsInfo($topic['id']);
                            $commentInfo= json_decode($commentInfo,true);
                            echo '<div class="topic-card">
                                <div class="topic-item-container">
                                    <div class="topic-card-little-picture">
                                        <a href="./topic.php?id='.$topic['id'].'">
                                            <img class="topic-img" src="'.$topic['image_path'].'" alt="">
                                        </a>
                                    </div>
                                    <div class="d-flex flex-direction-column align-items-center">
                                        <div>'.$topic['name'].'</div>
                                        <div>'.$topic['year'].'</div>
                                        <label>Antal inlägg: '.$commentInfo['sumComments'].'</label>
                                        <label>Senaste inlägg: '.$commentInfo['maxCreated'].'</label>
                                    </div>
                                </div>
                            </div>';
                        }
                        echo '</div>';
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
