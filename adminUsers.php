<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php 
    $page_title = "Filmforumet - Admin";
    include 'views/head.php'; 

    include_once 'src/DB/AuthClass.php';
    $auth = new AuthClass();
    $errorMessage = '';
    $message = '';   
    $clubs = [];     
    
    include_once 'src/DB/UserClass.php';
    $user = new UserClass();


    //if (!empty($_SESSION["user_id"])) {
        //$currentUser = $auth->getUser($_SESSION["user_id"]);
        //$currentUser = json_decode($currentUser, true);

        //if($currentUser['isAdmin'] == 1){
            include_once 'src/DB/GeneralClass.php';
            $general = new GeneralClass();
            

            if(isset($_POST['InactiveUserId'])) {
                $resp = $user -> deactivateUser($_POST['InactiveUserId']);
                $resp = json_decode($resp, true);
                //var_dump($loginResp);
                if($resp['success'] == 0){
                    $errorMessage = $resp['msg'];
                }
                elseif($resp['success'] == 1){
                    $message = $resp['msg'];
                }
            }
            $users = $user -> getAllUser();
        //}
    //}

?>

<body>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="app">
        <main>
            <?php include 'views/header.php';?>
            <div class="wrapper">
        <div class="d-flex flex-grow-1 h-100">
            <div class="content d-flex align-items-start">
                <div class="pl-5 pr-5 pb-3 d-flex flex-column w-100">
                    <div class="md-auto mb-3 d-flex justify-content-center">
                        <div class='col-md-offset-1 col-md-12'>
                            <div class='mt-2 panel-body table-responsive-md'>
                                <?php
                                if($errorMessage){ 
                                    echo('<div class="alert alert-danger" role="alert"> '.$errorMessage.'  </div>');
                                }
                                if($message){ 
                                    echo('<div class="alert alert-success" role="alert"> '.$message.'  </div>');
                                }
                                ?>
                                <div class="tab-content" id="myTabContent">
                                    <div>
                                        <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                <th></th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>IsActive</th>
                                                <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">
                                        <?php 
                                            //if (!empty($_SESSION["user_id"])) {
                                            //    if($currentUser['isAdmin'] == 1){
                                                    $tableRow = 0;
                                                    for ($row = 0; $row < count($users); $row++) {
                                                        $tableRow=$tableRow+1;
                                                        echo '<tr class="datatr">
                                                            <td>'.$tableRow.'</td>
                                                            <td id="'.$users[$row]['id'].'">'.$users[$row]['name'].'</td>
                                                            <td>'.$users[$row]['email'].'</td>
                                                            <td>'.$users[$row]['role'].'</td>
                                                            <td></td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <!--button class="btn btn-success mr-2" onClick=editMode(id)>
                                                                        <i class="fa fa-edit visible"></i>
                                                                    </button-->';
                                                            if($users[$row]['is_active'] == 1){
                                                                echo '  <button class="btn btn-danger" data-toggle="modal" data-target="#modalAdminUser" onClick="modalAdminUser('.$users[$row]['id'].')"))>
                                                                            <i class="fa fa-trash visible"></i>
                                                                        </button>';
                                                            }
                                                            echo '</div>
                                                            </td>
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
                    </div>
                </div>
            </div>
        </div>
        </div>
        <?php include 'src/views/footer.php'; ?>
        </main>
        </div>
        <?php include 'src/views/scripts.php'; ?>
    </body>

</html>