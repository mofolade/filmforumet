<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">

<?php 
    $page_title = "Filmforumet - Profile";
    include_once 'views/head.php'; 


    include_once 'src/DB/AuthClass.php';
    $auth = new AuthClass();

    include_once 'src/DB/GeneralClass.php';
    $general = new GeneralClass();
    $disabled='';

    /*if (!empty($_SESSION["user_id"])) {
        $user = $auth->getUser($_SESSION["user_id"]);
        $user = json_decode($user, true);
        $disabled = 'disabled';
    }*/

    $errorMessage='';
    $message='';
    $errPsw='';
    $errName='';

    include_once 'src/DB/UserClass.php';

    $newUser = array("username" => '', "password" => '' , "email" => '');
      
    $username = '';
    $password = '';
    $errorMessage = '';
    $message = '';
    $newUserResp=[];
    $button='';
    $hideForm='';

    if(isset($_POST['newUser'])) {
        $newUser = $_POST['newUser'];
        // Check if name has been entered
        if(empty($newUser['username'])) {
            $errName= 'Please enter your user name';
        }        
        elseif(empty($newUser['password'])) {
            $errPsw= 'Please enter your password';
        }
      else{
            $user = new UserClass();
            $newUserResp = $user->addUser($newUser);

            $newUserResp = json_decode($newUserResp, true);
            //var_dump($loginResp);
            if($newUserResp['success'] == 0){
                $errorMessage = $newUserResp['msg'];
            }
            elseif($newUserResp['success'] == 1){
                //insert role_id
                include_once 'src/DB/UserXRoleClass.php';
                $userRole = new UserXRoleClass();
                $resp = $userRole->addUserRole($newUserResp['id'],$newUser['roleId']);

                $message = 'Success';
                echo "<script>window.location.href='./login.php';</script>";
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
              <div class="content">
                <div class="login-container">
                    <div class="">
                        <h2 style="text-align:center">Registration</h2>
                        <div class="login-col">
                        <div class="alert" id="errorMsg">
                            <span class="closebtn" onClick="closeAlert()">×</span>
                            <span id="error-msg-text"></span>
                        </div>
                        <?php
                        if($errorMessage){ 
                            echo('<div class="alert alert-danger col-lg-6 col-sm-12 mx-auto" role="alert"> '.$errorMessage.'  </div>');
                        }
                        if($message){ 
                            echo('<div class="alert alert-success col-lg-6 col-sm-12 mx-auto" role="alert"> '.$message.'  </div>');
                        }

                        echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'"  id="signup-form" autocomplete="off">';
                        echo '<label>Name</label>';
                        echo '<input id="username"
                                    name="newUser[username]"
                                    required="" 
                                    type="text" 
                                    class="form-control">';
                        if($errName){
                            echo '<small  class="text-danger">'.$errName.'</small>';
                        }
                        echo '<label>Lösenord</label>';
                        echo '<input id="password"
                                name="newUser[password]"
                                required="" 
                                type="password"
                                class="form-control">';
                        if($errPsw){
                            echo '<small  class="text-danger">'.$errPsw.'</small>';
                        }
                        echo '<label>Email</label>';          
                        echo'
                            <input id="email"
                                name="newUser[email]"
                                required="" 
                                type="email" 
                                class="form-control" 
                                placeholder="';
                                if(!empty($_SESSION["user_id"])) echo($user['email']);
                        echo '">
                            <input type="hidden" id="roleId" name="newUser[roleId]" value="3">
                            <button data-test-submit-button="" data-bid-submit-button="" class="bid-btn btn btn-lg btn-fluid mb-4 " type="submit"> 
                            Skapa nytt konto
                            </button>
                                    <div class="ml-4 pt-2 text-danger"></div>
                                </div>
                            </form>';
                            echo $button;
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </main>
        <?php include 'views/footer.php'; ?>
        </div>
        <?php include 'views/scripts.php'; ?>
    </body>
</html>