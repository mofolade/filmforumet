<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        $page_title = "Filmforumet";
        include_once 'views/head.php'; 

        include_once 'src/DB/CategoriesClass.php';
        $category = new CategoriesClass();

        include_once 'src/ACLSettingsClass.php';
        $ACLSettings = new ACLSettingsClass();    

        if($ACLSettings->categories('GET', 0) == true){
            $allCategories = $category->getAllCategories(); 
        }

    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div id="topics-cover">
                        <?php
                        foreach ($allCategories as $category){
                            echo '<div class="topic-card">
                                <div class="topic-item-container">
                                    <div class="topic-card-little-picture">
                                        <a href="./category.php?id='.$category['id'].'">
                                            <img class="topic-img" src="'.$category['image_path'].'" alt="'.$category['name'].'">
                                        </a>
                                    </div>
                                    <div class="d-flex flex-direction-column align-items-center">
                                        <div>'.$category['name'].'</div>
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
