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
    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div id="topics-cover">
                        <?php
                        foreach ($allTopics as $topic){
                            echo '<div class="topic-card">
                                <div class="topic-item-container">
                                    <div class="topic-card-little-picture">
                                        <a href="./topic?id='.$topic['id'].'">
                                            <img class="topic-img" src="'.$topic['image_path'].'">
                                        </a>
                                    </div>
                                    <div class="d-flex flex-direction-column align-items-center mt-5">
                                        <div>'.$topic['name'].'</div>
                                        
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
