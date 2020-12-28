<?php
    include 'src/DB/SessionClass.php';
    /*$session = new SessionClass();
    session_set_save_handler(array($session, 'open'),
                                    array($session, 'close'),
                                    array($session, 'read'),
                                    array($session, 'write'),
                                    array($session, 'destroy'),
                                    array($session, 'gc'));
    session_write_close();
    // the following prevents unexpected effects when using objects as save handlers
    register_shutdown_function('session_write_close');
    session_start();

    echo $_SESSION["user_id"];*/
    session_start();
    $_SESSION["user_id"] = "";
    session_unset();
    session_destroy();
    
    header("Location: ./");
    exit();
?>