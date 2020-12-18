<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        $page_title = "Filmforumet - Ny Topic";
        include_once 'views/head.php'; 
    
        if(isset($_POST['newTopic'])) {
            $newTopic = $_POST['newTopic'];
            
            // Check if name has been entered
            if(empty($newTopic['name'])) {
                $errName= 'Please enter topic name';
            }        
            elseif(empty($newTopic['imdbId'])) {
                $errPsw= 'Please enter imdb id';
            }
            else{
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
        }
    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div class="new-topic-container">
                        
                    <?php 
                        echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">
                            <div class="">
                                <h2 style="text-align:center">Ny topic</h2>';
                        echo '      <div class="topic-col">
                                        <label>Topic namn</label>
                                        <input type="text" name="newTopic[name]"   id="name" maxlength="160" required>
                                        <label>Imdb id</label>
                                        <input type="text"  name="newTopic[imdbId]"   id="imdbId" required>
                                        <label>År</label>
                                        <input type="number"  name="newTopic[year]"   id="year" required>
                                        <label>Film beskrivning</label>
                                        <textarea name="newTopic[description]"  id="description" required="required"></textarea>
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
