<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        $page_title = "Filmforumet";
        include_once 'views/head.php'; 

        include_once 'src/ACLSettingsClass.php';
        $ACLSettings = new ACLSettingsClass();

        if (!empty($_SESSION["user_id"])) {
            include_once './src/DB/UserClass.php';
            $user = new UserClass();
            $currentUser = $user->getUser($_SESSION["user_id"]);
            $currentUser = json_decode($currentUser, true);
        }


    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div id="profile-cover">
                        <div class="profile">
                            <?php
                            if(!empty($_SESSION["user_id"])){
                                echo '<img src=".'.$currentUser['picture_url'].'" alt="" class="profile_image">';
                                echo '<div class="profile_name">'.$currentUser['name'].'</div>';
                                echo '<div class="profile_email">'.$currentUser['email'].'</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
