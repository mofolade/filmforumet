<?php
    session_start();
    
    include_once './src/DB/AuthClass.php';
    $auth = new AuthClass();


    if (!empty($_SESSION["user_id"])) {
        $user = $auth->getUser($_SESSION["user_id"]);
        $user = json_decode($user, true);
    }
    echo ' <header class="bg">';
    echo '<nav class="navbar">';
    echo '<a href="./">Filmforumet</a>';     
    echo '<div id="navbar-r">';
    if (empty($_SESSION["user_id"]))
    {
        echo '<a href="./about.php">Om oss</a>';
        echo '<a href="./signIn.php">Bli medlem</a>';             
        echo '<a href="./login.php" id="sign-in-link">Logga in</a>';
    }    
    else
    {
        echo '<div class="profil-box-header">
                <div style="margin-right: 10px;">
                <a href="./mypage.php" id="profil-link" class="notification">';
        echo $user['name'];
        echo '</a></div>
                <div class="header-message-avatar">
                    <img src=".'.$user['picture_url'].'" alt="">
                </div>
                </a>
            </div>                
            <a href="./logout.php">Logga ut</a>';
    }   
        echo ' </div>
        </nav>
    </header>    
    ';
?>