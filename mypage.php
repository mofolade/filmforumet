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


    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div id="topics-cover">
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
