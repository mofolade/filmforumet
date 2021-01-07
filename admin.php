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
    $currentUserRoles=[];
    $usersRoles=[];
    $currentUser=null;
    $allUsers=[];
    $oneUser=[];

    if (!empty($_SESSION["user_id"])) {
        include_once 'src/DB/UserXRoleClass.php';
        $userRole = new UserXRoleClass();

        $currentUser = $user->getUser($_SESSION["user_id"]);
        $currentUser = json_decode($currentUser, true);
        
        $currentUserRoles = $userRole->getUserRole($_SESSION["user_id"]);

        $usersRoles = $userRole -> getAllUserWithRoles();

        if(in_array(1,$currentUserRoles)){
            $allUsers = $user -> getAllUser();
            //
            if(isset($_POST['InactiveUserId']) && $ACLSettings->admin('POST', 1) == true ) {
                $resp = $user -> deactivateUser($_POST['InactiveUserId']);
                $resp = json_decode($resp, true);
 
                if($resp['success'] == 0){
                    $errorMessage = $resp['msg'];
                }
                elseif($resp['success'] == 1){
                    $message = $resp['msg'];
                    echo "<script>window.location.href='./admin.php';</script>";
                    exit;
                }
            }else if(isset($_POST['userId']) 
                        && ($_POST['method'] == 'addRole' || $_POST['method'] == 'deleteRole')
                        && $ACLSettings->admin('POST', 1) == true ){
                $updateRoles = $userRole -> updateUserRoles($_POST['userId'], $_POST['roleId'],$_POST['method']);
                echo "<script>window.location.href='./admin.php';</script>";
                exit;
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
                                <table class="table table-sm table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                        <th></th>
                                        <th>User id</th>
                                        <th>Role</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>IsActive</th>
                                        <th>Inactivation</th>
                                        <th>Edit roles</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                <?php                                      
                                    //if (!empty($_SESSION["user_id"])) {
                                if(!empty($_SESSION["user_id"]) && in_array(1,$currentUserRoles)){
                                    $tableRow = 0;
                                    foreach($allUsers as $oneUser) {
                                        $isAdmin=false;
                                        $isModerator=false;
                                        $tableRow=$tableRow+1;
                                        echo '<tr class="datatr">
                                            <td>'.$tableRow.'</td>
                                            <td>'.$oneUser['id'].'</td>';
                                        //Roles
                                        echo '    <td>';
                                        if (isset($usersRoles)) {                                                    
                                            foreach ($usersRoles as $subarray) {
                                                if($subarray['user_id'] == $oneUser['id']){
                                                    echo '      <span  class="badge  ';
                                                    if($subarray['role_name'] == 'admin'){
                                                        echo ' text-white  bg-warning " ';
                                                        echo '" >';
                                                        echo 'admin';                                                                
                                                        $isAdmin=true;
                                                    }
                                                    else if($subarray['role_name'] == 'moderator'){
                                                        echo ' text-white  bg-success " ';
                                                        echo '" >';
                                                        echo 'moderator';                                                                
                                                        $isModerator=true; 
                                                    }
                                                    else if($subarray['role_name'] == 'user'){
                                                        echo ' text-white  bg-primary " ';
                                                        echo '" >';
                                                        echo 'user';
                                                    }
                                                    else{
                                                        echo '" >';
                                                    }        
                                                    echo '      </span>';
                                                }
                                            }
                                        }
                                        
                                        echo '        </td>
                                            <td>';
                                        if($isModerator==true){
                                            echo '<a href="./moderator.php?id='.$oneUser['id'].'">';
                                            echo $oneUser['name'];
                                            echo '</a>';
                                        }
                                        echo '</td>
                                            <td>'.$oneUser['email'].'</td>';
                                        echo '    <td>';
                                        echo '      <span  class="badge  ';
                                            if($oneUser['is_active'] == 1){
                                                echo ' text-white  bg-success " ';
                                                echo '" >';
                                                echo 'active';
                                            }
                                            else if($oneUser['is_active'] == 0){
                                                echo ' text-white  bg-warning " ';
                                                echo '" >';
                                                echo 'inaktiv';
                                            }
                                            else{
                                                echo '" >';
                                            }        
                                        echo '      </span>';
                                        echo '</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">';
                                            if($oneUser['is_active'] == 1){
                                                echo '  <button class="btn btn-danger" data-toggle="modal" data-target="#modalAdminUser" onClick="modalAdminUser('.$oneUser['id'].')"))>
                                                            <i class="fa fa-trash visible"></i>
                                                        </button>';
                                            }
                                            echo '</div>
                                            </td>
                                            <td class="d-flex">';
                                            if ($isAdmin==false) { 
                                                echo '<span style="margin-right:5px;"><form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                                                        <input type="hidden" id="roleId" name="roleId" value="1">
                                                        <input type="hidden" id="userId" name="userId" value="'.$oneUser['id'].'">
                                                        <input type="hidden" id="method" name="method" value="addRole">
                                                        <button type="submit" class="btn-warning btn-sm" ><i class="fa fa-plus visible"> Admin</i></button>
                                                    </form></span>';
                                            }else{
                                                echo '<span style="margin-right:5px;"><form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                                                        <input type="hidden" id="roleId" name="roleId" value="1">
                                                        <input type="hidden" id="userId" name="userId" value="'.$oneUser['id'].'">
                                                        <input type="hidden" id="method" name="method" value="deleteRole">
                                                        <button type="submit" class="btn-danger btn-sm" ><i class="fa fa-minus visible"> Admin</i></button>
                                                    </form></span>';
                                            }
                                            if ($isModerator==false) { 
                                                echo '<span><form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                                                        <input type="hidden" id="roleId" name="roleId" value="2">
                                                        <input type="hidden" id="userId" name="userId" value="'.$oneUser['id'].'">
                                                        <input type="hidden" id="method" name="method" value="addRole">
                                                        <button type="submit" class="btn-success btn-sm" ><i class="fa fa-plus visible"> Moderator</i></button>
                                                    </form></span>';
                                            }else{
                                                echo '<span style="margin-right:5px;"><form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                                                        <input type="hidden" id="roleId" name="roleId" value="2">
                                                        <input type="hidden" id="userId" name="userId" value="'.$oneUser['id'].'">
                                                        <input type="hidden" id="method" name="method" value="deleteRole">
                                                        <button type="submit" class="btn-danger btn-sm" ><i class="fa fa-minus visible"> Moderator</i></button>
                                                    </form></span>';
                                            }
                                        echo '</td>
                                        </tr>';
                                    }
                                }
                                    //}
                                ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php 
                                if (!empty($_SESSION["user_id"])) {
                                    echo '<div class="modal fade" id="modalAdminUser" tabindex="-1" role="dialog" aria-labelledby="modalAdminUserLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header danger">
                                                <h5 class="modal-title" id="modalAdminUserLabel">Wait!</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to inactivation this user?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <form role="form" action="'.$_SERVER['REQUEST_URI'].'" method="POST">
                                                    <input type="hidden" id="InactiveUserId" name="InactiveUserId" value="">
                                                    <button type="submit" id="inactiveBtn" class="danger btn btn-danger" >Inactivation</button>
                                                </form>
                                            </div>
                                            </div>
                                        </div>
                                    </div>';
                                }                                    
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'src/views/footer.php'; ?>
            </main>
        </div>
        <?php include 'src/scripts.php'; ?>
    </body>

</html>