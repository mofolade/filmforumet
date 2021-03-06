<?php
    session_start();
    
    include_once './src/DB/UserClass.php';
    $user = new UserClass();

    include_once './src/DB/AuthClass.php';
    $checkLogin = new AuthClass();
    $checkLogin->checkUser($_SESSION["user_id"]);
    $checkLogin = json_decode($checkLogin, true);


    if (!empty($_SESSION["user_id"]) || $checkLogin['success'] == 0) {
        $currentUser = $user->getUser($_SESSION["user_id"]);
        $currentUser = json_decode($currentUser, true);
        include_once 'src/DB/UserXRoleClass.php';
        $userRole = new UserXRoleClass();
        $currentUserRoles = $userRole->getUserRole($_SESSION["user_id"]);
    }
    echo ' <header class="bg">';
    echo '<nav class="navbar">';
    echo '<a href="./">Movie forum</a>';     
    echo '<div id="navbar-r">';
    echo '<a href="./about.php">About</a>';
    if (!empty($_SESSION["user_id"]))
    {
        echo '<a href="./addTopic.php">Add topic</a>';
    }
    if(in_array(1,$currentUserRoles)){
        echo '<a href="./admin.php">Admin</a>';
    }
    if(in_array(2,$currentUserRoles)){
        echo '<a href="./moderator.php">Moderator</a>';

    }
    if (empty($_SESSION["user_id"]))
    {
        echo '<a href="./signIn.php">Sign up</a>';             
        echo '<a href="./login.php" id="sign-in-link">Login</a>';
    }    
    else
    {
        echo '<div class="profil-box-header">
                <div style="margin-right: 10px;">
                <a href="./mypage.php" id="profil-link" class="notification">';
        echo $currentUser['name'];
        echo '</a></div>
                <div class="header-message-avatar">
                    <img src=".'.$currentUser['picture_url'].'" alt="">
                </div>
                </a>
            </div>                
            <a href="./logout.php">Logout</a>';
    }   
        echo ' </div>
        </nav>
    </header>    
    ';
?>