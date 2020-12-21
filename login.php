<?php
  //phpinfo();
  //include 'src/DB/SessionClass.php';
  //$session = new SessionClass();
  
  session_start();

?>
<!DOCTYPE html>
  <html lang="en">
    <?php
        $page_title = "Filmforumet";
        include_once 'views/head.php'; 
    ?>
    <body>
    <?php
      include_once 'src/DB/AuthClass.php';
      $login = array("username" => '', "password" => '');
      
      $username = '';
      $password = '';
      $errorMessage = '';
      $message = '';
      $loginResp=[];
      $errPsw='';
      $errName='';

      if(isset($_POST['login'])) {
        $login = $_POST['login'];
        //echo('<script>console.log("'.$login['username'].' '.$login['password'].'")</script>');
        // Check if name has been entered
        if(empty($login['email'])) {
          $errName= 'Please enter your email';
        }        
        elseif(empty($login['password'])) {
          $errPsw= 'Please enter your password';
        }
        else{
          $auth = new AuthClass();
          $loginResp = $auth->login($login);
          $loginResp = json_decode($loginResp, true);
          //var_dump($loginResp);
          if($loginResp['success'] == 0){
            $errorMessage = $loginResp['msg'];
          }
          elseif($loginResp['success'] == 1){
            //AuthClassban van a setsession
            //$session->write($session['id'],$loginResp['user_id']);
            $_SESSION['user_id'] = $loginResp['user_id'];
            $message = $loginResp['msg'];
            echo "<script>window.location.href='./';</script>";
            exit;
          }
        }
      }
    ?>
      <div id="app">
        <main>
            <?php include 'views/header.php';?>
            <div class="wrapper">
              <div class="content">
                <div class="login-container">
                  <div class="">
                    <h2 style="text-align:center">Logga in</h2>
                    <div class="login-col">
                      <?php 
                        echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
                        //if (empty($_SESSION["user_id"])){
                          
                          if($errorMessage){ 
                            echo('<div class="alert-danger" role="alert"> '.$errorMessage.'  </div>');
                          }
                          if($message){ 
                            echo('<div class="alert-success" role="alert"> '.$message.'  </div>');
                          }
                        //}
                        echo '<input type="text" name="login[email]" id="email" name="email" placeholder="Email" required>';
                        echo '<input type="password" name="login[password]" id="password" name="password" placeholder="Lösenord" required>';
                        echo '<button data-test-submit-button="" data-bid-submit-button="" 
                                class="bid-btn btn btn-lg btn-fluid mb-4 " type="submit"> 
                              Logga in
                              </button>
                          </form>';

                      /*<div class="hl">
                        <span class="hl-innertext">eller</span>
                      </div>

                      <button  onClick="signInButton" class="google btn"><i class="fa fa-google fa-fw">
                      </i> Logga in med Google
                      </button>*/
                      ?>
                      
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </main>
      </div>
    </body>
</html>