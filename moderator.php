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
    
    include_once 'src/ACLSettingsClass.php';
    $ACLSettings = new ACLSettingsClass();

    $errorMessage = '';
    $message = '';
    $adminRoleId=0;
    $moderatorRoleId=0;
    $currentUserRoles=[];
    $usersRoles=[];
    $currentUser=null;
    $moderatorUser=null;
    $moderatorUserRoles=[];
    $moderatorCategoriesRights=[];
    $allUsers=[];

    if (!empty($_SESSION["user_id"])) {
        include_once 'src/DB/UserXRoleClass.php';
        $userRole = new UserXRoleClass();

        
        $currentUser = $user->getUser($_SESSION["user_id"]);
        $currentUser = json_decode($currentUser, true);        
        $currentUserRoles = $userRole->getUserRole($_SESSION["user_id"]);

        if(in_array(1,$currentUserRoles)){
            $adminRoleId=1;
        } else if(in_array(2,$currentUserRoles)){
            $moderatorRoleId=2;
        }

        if($ACLSettings->moderator('GET', $adminRoleId) == true  || $ACLSettings->moderator('GET', $moderatorRoleId) == true){
            if(isset($_GET['id'])){
                $moderatorUser = $user->getUser($_GET['id']);
            }
            elseif(in_array(2,$currentUserRoles)){
                $moderatorUser = $user->getUser($_SESSION["user_id"]);
            }
            $moderatorUser = json_decode($moderatorUser, true);

            $moderatorUserRoles = $userRole->getUserRole($_GET['id']);

            if(in_array(1,$currentUserRoles) || in_array(2,$currentUserRoles)){
                include_once 'src/DB/CategoriesClass.php';
                $category = new CategoriesClass();    
                $allCategories = $category->getAllCategories();

                include_once 'src/DB/ModeratorXCategoryClass.php';
                $moderatorRight = new ModeratorXCategoryClass();
                if(isset($_GET['id'])){
                    $moderatorCategoryRights = $moderatorRight->getModeratorCategoriesRights($_GET['id']);
                }
                elseif(in_array(2,$currentUserRoles)){
                    $moderatorCategoryRights = $moderatorRight->getModeratorCategoriesRights($_SESSION["user_id"]);
                }

                if(isset($_POST['categoryId'])
                    && ($_POST['action'] == 'addRight')
                    && ($ACLSettings->moderator('POST', $adminRoleId) == true) ){
                    $resp = $moderatorRight -> addModeratorCategoryRight($_GET['id'], $_POST['categoryId']);
                    echo "<script>window.location.href='./moderator.php?id=".$_GET['id']."';</script>";
                    exit;
                }else if(isset($_POST['categoryId']) 
                    && ($_POST['action'] == 'deleteRight')
                    && ($ACLSettings->moderator('POST', $adminRoleId) == true)){
                    $resp = $moderatorRight -> deleteModeratorCategoryRight($_GET['id'], $_POST['categoryId']);
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
                            echo '<div class="topic-info-box">';
                            echo '<h2>'.$moderatorUser['name'].'</h2>';
                            echo '</div>';
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
                                        <th>Name</th>
                                        <th>Moderator rights</th>
                                        <th>Edit right</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                <?php                                      
                                    //if (!empty($_SESSION["user_id"])) {
                                    if(in_array(1,$currentUserRoles) || in_array(2,$currentUserRoles)){
                                        $tableRow = 0;
                                        foreach($allCategories as $category) {
                                            $isAdmin=false;
                                            $isModerator=false;
                                            $tableRow=$tableRow+1;
                                            echo '<tr class="datatr">
                                                <th scope="row">'.$tableRow.'</th>
                                                <th><img class="comment-img" src="'.$category['image_path'].'"></th>
                                                <th><a href="./category.php?id='.$category['id'].'">'.$category['name'].'</a></th>';
                                            echo '        </td>
                                                <th>';
                                            echo '      <span  class="badge  ';
                                                if(in_array($category['id'],$moderatorCategoryRights)){
                                                    echo ' text-white  bg-success " ';
                                                    echo '" >';
                                                    echo 'active';
                                                }
                                                else if(!in_array($category['id'],$moderatorCategoryRights)){
                                                    echo ' text-white  bg-warning " ';
                                                    echo '" >';
                                                    echo 'none';
                                                }
                                                else{
                                                    echo '" >';
                                                }        
                                            echo '      </span>';
                                            echo '</th>
                                                <th>
                                                    <div class="btn-group btn-group-sm" role="group">';
                                                if(isset($_GET['id']) && in_array(1,$currentUserRoles)){
                                                    if ($isAdmin==false && !in_array($category['id'],$moderatorCategoryRights)) { 
                                                        echo '<span style="margin-right:5px;"><form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                                                                <input type="hidden" id="categoryId" name="categoryId" value="'.$category['id'].'">
                                                                <input type="hidden" id="action" name="action" value="addRight">
                                                                <button type="submit" class="btn-primary btn-sm" ><i class="fa fa-plus visible"> Moderator</i></button>
                                                            </form></span>';
                                                    }else{
                                                        echo '<span style="margin-right:5px;"><form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                                                                <input type="hidden" id="categoryId" name="categoryId" value="'.$category['id'].'">
                                                                <input type="hidden" id="action" name="action" value="deleteRight">
                                                                <button type="submit" class="btn-danger btn-sm" ><i class="fa fa-minus visible"> Moderator</i></button>
                                                            </form></span>';
                                                    }
                                                }
                                                echo '</div>
                                                </th>
                                            </tr>';
                                        }
                                    }
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