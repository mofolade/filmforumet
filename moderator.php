<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php 
    $page_title = "Filmforumet - Admin";
    include 'views/head.php'; 

    include_once 'src/DB/UserClass.php';
    $user = new UserClass();
    $errorMessage = '';
    $message = '';
    $currentUserRoles=[];
    $usersRoles=[];
    $currentUser=null;
    $moderatorUser=null;
    $moderatorUserRoles=[];
    $moderatorTopicRights=[];
    $allUsers=[];

    if (!empty($_SESSION["user_id"])) {
        include_once 'src/DB/UserXRoleClass.php';
        $userRole = new UserXRoleClass();

        $currentUser = $user->getUser($_SESSION["user_id"]);
        $currentUser = json_decode($currentUser, true);
        
        $currentUserRoles = $userRole->getUserRole($_SESSION["user_id"]);

        if(isset($_GET['id'])){
            $moderatorUser = $user->getUser($_GET['id']);
            $moderatorUser = json_decode($moderatorUser, true);

            $moderatorUserRoles = $userRole->getUserRole($_GET['id']);

            if(in_array(1,$currentUserRoles)){
                include_once 'src/DB/TopicClass.php';
                $topic = new TopicClass();    
                $allTopics = $topic->getAllTopics();

                include_once 'src/DB/ModeratorXTopicClass.php';
                $moderatorRight = new ModeratorXTopicClass();

                $moderatorTopicRights = $moderatorRight->getModeratorTopicsRights($_GET['id']);

                if(isset($_POST['topicId']) && ($_POST['method'] == 'addRight')){
                    $resp = $moderatorRight -> addModeratorTopicRight($_GET['id'], $_POST['topicId']);
                    echo "<script>window.location.href='./moderator.php?id=".$_GET['id']."';</script>";
                    exit;
                }else if(isset($_POST['topicId']) && ($_POST['method'] == 'deleteRight')){
                    $resp = $moderatorRight -> deleteModeratorTopicRight($_GET['id'], $_POST['topicId']);
                    echo "<script>window.location.href='./moderator.php?id=".$_GET['id']."';</script>";
                    exit;
                }

                
            }
            
        }
    }

?>

<body>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="app">
        <main>
            <?php include 'views/header.php';?>
            <div class="wrapper">
                <div class="admin-container">
                    <?php
                        //moderator name
                        if(isset($_GET['id'])){
                            echo '<h2>'.$moderatorUser['name'].'</h2>';
                        }
                    ?>
                    <div class="d-flex flex-grow-1 h-100">
                        <div class="content d-flex justify-content-center">
                            <?php
                            if($errorMessage){ 
                                echo('<div class="alert alert-danger" role="alert"> '.$errorMessage.'  </div>');
                            }
                            if($message){ 
                                echo('<div class="alert alert-success" role="alert"> '.$message.'  </div>');
                            }
                            ?>
                            <div class="table-responsive">
                            <div class="form-group pull-right">
                                <input type="text" class="search form-control" placeholder="Search">
                            </div>
                            <span class="counter pull-right"></span>
                                <table class="table table-sm table-striped table-bordered table-hover results" 
                                id="table"
                                data-show-pagination-switch="true"
                                data-pagination="true"
                                data-id-field="id"
                                data-page-list="[10, 25, 50, 100, all]"
                                data-show-footer="true"
                                data-side-pagination="server"
                                data-response-handler="responseHandler">
                                    <thead>
                                        <tr>
                                        <th></th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Imdb Id</th>
                                        <th>Moderator rights</th>
                                        <th>Inactivation</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                <?php                                      
                                    //if (!empty($_SESSION["user_id"])) {
                                    //    if($currentUser['isAdmin'] == 1){
                                            $tableRow = 0;
                                            foreach($allTopics as $topic) {
                                                $isAdmin=false;
                                                $isModerator=false;
                                                $tableRow=$tableRow+1;
                                                echo '<tr class="datatr">
                                                    <th scope="row">'.$tableRow.'</th>
                                                    <th><img class="comment-img" src="'.$topic['image_path'].'"></th>
                                                    <th>'.$topic['name'].'</th>
                                                    <th>'.$topic['imdb_id'].'</th>';
                                                echo '        </td>
                                                    <th>';
                                                /*echo '      <span  class="badge  ';
                                                    if($user['is_active'] == 1){
                                                        echo ' text-white  bg-success " ';
                                                        echo '" >';
                                                        echo 'active';
                                                    }
                                                    else if($user['is_active'] == 0){
                                                        echo ' text-white  bg-warning " ';
                                                        echo '" >';
                                                        echo 'inaktiv';
                                                    }
                                                    else{
                                                        echo '" >';
                                                    }        
                                                echo '      </span>';*/
                                                echo '</th>
                                                    <th>
                                                        <div class="btn-group btn-group-sm" role="group">';
                                                    if(isset($_GET['id']) && in_array(1,$currentUserRoles)){
                                                        if ($isAdmin==false && !in_array($topic['id'],$moderatorTopicRights)) { 
                                                            echo '<span style="margin-right:5px;"><form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                                                                    <input type="hidden" id="topicId" name="topicId" value="'.$topic['id'].'">
                                                                    <input type="hidden" id="method" name="method" value="addRight">
                                                                    <button type="submit" class="btn btn-primary btn-sm" ><i class="fa fa-plus visible"> Moderator</i></button>
                                                                </form></span>';
                                                        }else{
                                                            echo '<span style="margin-right:5px;"><form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                                                                    <input type="hidden" id="topicId" name="topicId" value="'.$topic['id'].'">
                                                                    <input type="hidden" id="method" name="method" value="deleteRight">
                                                                    <button type="submit" class="btn btn-danger btn-sm" ><i class="fa fa-minus visible"> Moderator</i></button>
                                                                </form></span>';
                                                        }
                                                    }
                                                    echo '</div>
                                                    </th>
                                                </tr>';
                                            }
                                    //    }
                                    //}
                                ?>
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'views/footer.php'; ?>
            </main>
        </div>
        <?php include 'src/scripts.php'; ?>
    </body>

</html>