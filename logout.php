<?php
    include 'src/DB/SessionClass.php';
    
    session_start();
    $user_id = $_SESSION["user_id"];
    include 'src/DB/AuthClass.php';
    $auth = new AuthClass();
    $logout = $auth->deleteSessionDb($user_id);

    $_SESSION["user_id"] = "";
    session_unset();
    session_destroy();

    header("Location: ./");
    exit();
?>