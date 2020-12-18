<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        $currentUserRoles=[];
        $page_title = "Filmforumet - Topic";
        include_once 'views/head.php'; 

        if (!empty($_SESSION["user_id"])) {
            include_once './src/DB/UserClass.php';
            $user = new UserClass();
            $currentUser = $user->getUser($_SESSION["user_id"]);
            $currentUser = json_decode($currentUser, true);

            include_once 'src/DB/UserXRoleClass.php';
            $userRole = new UserXRoleClass();
            $currentUserRoles = $userRole->getUserRole($_SESSION["user_id"]);
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
            $newCommentResp = $topicComment->addComment($newComment);
            echo "<script>window.location.href='./topic.php?id=".$_GET['id']."';</script>";
            exit;
        }

        if(isset($_POST['method'])) {
            if($_POST['method'] == 'setSpoiler'){
                $newCommentResp = $topicComment->setSpoilerComment($_POST['commentId']);
            }
            elseif($_POST['method'] == 'setInactiveComment'){
                $newCommentResp = $topicComment->setInactiveComment($_POST['commentId']);
            }
            echo "<script>window.location.href='./topic.php?id=".$_GET['id']."';</script>";
            exit;
        }
    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div class="topic-container">
                    <?php
                    if(isset($_GET['id'])){
                        echo '  <div class="topic-info-box">
                                    <div>
                                        <img class="comment-img" src="'.$getTopic['image_path'].'">
                                    </div>
                                    <div>    
                                        <h2>'.$getTopic['name'].'</h2><h3>('.$getTopic['year'].')</h3>         
                                        <span class="">'.$getTopic['description'].'</span>
                                    </div>
                            </div>';
                        if(!empty($_SESSION["user_id"])){
                            echo '<div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="fa fa-plus visible"></i> New comment
                                        </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="comment-card">
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                        }                        
                        echo '<div  id="comments-cover">';
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
                                    <div class="comment-box">';
                            if(in_array(1,$currentUserRoles) || in_array(2,$currentUserRoles)){
                                //admin or moderator
                                    echo'            <div class="edit-links">';
                                        if($comment['isSpoiler'] == 0){
                                            echo ' <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
                                                    <input type="hidden" id="commentId" name="commentId" value="'.$comment['id'].'">
                                                    <input type="hidden" name="method" value="setSpoiler">
                                                    <button type="submit" class="btn-dark" type="submit">Spoiler</button>
                                                </form>';
                                        }
                                    echo '      <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
                                                    <input type="hidden" id="commentId" name="commentId" value="'.$comment['id'].'">
                                                    <input type="hidden" name="method" value="setInactiveComment">
                                                    <button type="submit" class="btn-delete" type="submit">Ta bort</button>
                                                </form>
                                            </div>';
                            }
                            echo '          <div class="comment-body">';
                            if($comment['isSpoiler'] == 1){
                                echo '<input type="checkbox"  id="spoiler'.$comment['id'].'" /> 
                                    <label for="spoiler'.$comment['id'].'" >Spoiler</label>
                                        <div class="spoiler">';
                            }
                            echo $comment['comment'];
                            if($comment['isSpoiler'] == 1){
                                echo '</div>';
                            }
                            echo ' </div>
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
