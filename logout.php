<?php
    require_once('src/DB/SessionClass.php');
    
    session_start();
    $_SESSION["user_id"] = "";
    
    session_destroy();
    header("Location: ./");
    exit();
?>