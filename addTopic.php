<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        $page_title = "Filmforumet - Ny Topic";
        include_once 'views/head.php'; 

        include_once 'src/ACLSettingsClass.php';
        $ACLSettings = new ACLSettingsClass();

        $adminRoleId=0;
        $currentUserRoles=[];
        $currentUser=null;
        $allCategories=[];

        if (!empty($_SESSION["user_id"])) {
            include_once 'src/DB/UserClass.php';
            $user = new UserClass();
    
            $currentUser = $user->getUser($_SESSION["user_id"]);
            $currentUser = json_decode($currentUser, true);
            
            include_once 'src/DB/UserXRoleClass.php';
            $userRole = new UserXRoleClass();
            
            $currentUserRoles = $userRole->getUserRole($_SESSION["user_id"]);
            if(in_array(1,$currentUserRoles)){
                $adminRoleId=1;
            }

            include_once 'src/DB/CategoriesClass.php';
            $categories = new CategoriesClass();
            $allCategories = $categories->getAllCategories();
        }
        else{
            echo "<script>window.location.href='./login.php';</script>";
            exit;
        }
    
        if(isset($_POST['newTopic']) 
            && !empty($_SESSION["user_id"])
            && $ACLSettings->topics('POST', '', $_SESSION["user_id"]) == true
            ) {
            $newTopic = $_POST['newTopic'];
            
            // Check if name has been entered
            if(empty($newTopic['name'])) {
                $errName= 'Please enter topic name';
            }        
            elseif(empty($newTopic['imdbId'])) {
                $errPsw= 'Please enter imdb id';
            }
            elseif (strlen($newTopic['name']) < 101 && strlen($newTopic['description']) < 501){
                include_once('src/General.php');
                $general = new General();
                $newTopic['name'] = $general->remove_emoji($newTopic['name']);
                $newTopic['description'] = $general->remove_emoji($newTopic['description']);

                include_once 'src/DB/TopicClass.php';
                $topic = new TopicClass();
                $newTopicResp = $topic->addTopic($newTopic);

                $newTopicResp = json_decode($newTopicResp, true);
                //var_dump($loginResp);
                if($newTopicResp['success'] == 0){
                    $errorMessage = $newTopicResp['msg'];
                }
                elseif($newTopicResp['success'] == 1){
                    $message = 'Success';
                    include_once 'src/DB/UploaderClass.php'; 
                    $uploader = new UploaderClass();

                    if($check!== false){
                        //set max file size to be allowed in MB//
                        if($uploader->uploadFile($_FILES['fileToUpload'])){   //txtFile is the filebrowse element name //     
                            $imageName  =   $uploader->getUploadName(); //get uploaded file name, renames on upload//                            
                            $updateTopicResp = $topic->updateTopicImage($newTopicResp['topicId'],$imageName);
                            echo $newTopicResp['topicId'].' * * * * '.$imageName;
                            //echo $uploader->getMessage();
                        }else{//upload failed
                            $uploader->getMessage(); //get upload error message 
                            //echo $uploader->getMessage();
                        }
                    }
                    
                    echo "<script>window.location.href='./';</script>";
                    exit;
                }
            }
            else{
                $errorMessage='Topic Not Inserted!';
            }
        }
    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div class="new-topic-container">
                        
                    <?php 
                        if($errorMessage){ 
                            echo('<div class="alert-danger" role="alert"> '.$errorMessage.'  </div>');
                        }
                        if($message){ 
                            echo('<div class="alert-success" role="alert"> '.$message.'  </div>');
                        }
                        echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">
                            <div class="">
                                <h2 style="text-align:center">Ny topic</h2>';
                        echo '      <div class="topic-col">
                                        <label>Topic namn</label>
                                        <input type="text" name="newTopic[name]"   id="name" maxlength="200" required>
                                        <label>Categories</label>
                                        <select name="newTopic[categoryId]" id="categoryId">';
                                        foreach ($allCategories as $category){
                                            echo '<option value="'.$category['id'].'">'.$category['name'].'</option>';
                                        }
                        echo'           </select>
                                        <label>Imdb id</label>
                                        <input type="text"  name="newTopic[imdbId]"  id="imdbId" required>
                                        <label>År</label>
                                        <input type="number"  name="newTopic[year]"   id="year" maxlength="4" required>
                                        <label>Film beskrivning (max 500 character)</label>
                                        <textarea name="newTopic[description]"  id="description" 
                                            maxlength="500" required="required"></textarea>
                                        <div class="img-upload-container">
                                            <div class="file-upload-form">
                                                <div class="file-upload">
                                                    <label>Välj en JPEG-fil att ladda upp. Max 1 MB filstorlek.</label>
                                                    <input type="file" name="fileToUpload" id="fileToUpload" class="file-btn" accept="image/jpeg">
                                                </div>
                                            </div>    
                                        </div>
                                        <input type="submit" value="Skapa ny topic">
                                    </div>';  
                        echo'    </div>
                            </form>';
                        ?>     
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
