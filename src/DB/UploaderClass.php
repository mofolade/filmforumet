<?php

class UploaderClass{

    private $errorMessage;
    private $maxSize = .5 * (1024*1024);
    public $name='Uploader';
    public $useTable    =false;



    function setMaxSize($sizeMB){
        $this->maxSize  =   $sizeMB * (1024*1024);
    }

    function setExtensions($options){
        $this->extensions   =   $options;
    }

    function setMessage($message){
        $this->errorMessage =   $message;
    }

    function getMessage(){
        return $this->errorMessage;
    }

    function getUploadName(){
        return $this->uploadName;
    }
    function setSequence($seq){
        $this->imageSeq =   $seq;
    }

    function getRandom(){
        return strtotime(date('Y-m-d H:i:s')).rand(1111,9999).rand(11,99).rand(111,999);
    }
    function sameName($true){
        $this->sameName =   $true;
    }
    function uploadFile($fileBrowse){
        $result =   false;
        //$size   =   $_FILES[$fileBrowse]["size"];
        //$name   =   $_FILES[$fileBrowse]["name"];
        
        $this->setMessage("extensions ".$fileBrowse["name"]);
        $ext=strtolower(end(explode('.',$fileBrowse["name"])));
        //$extensions = array('jpg','jpeg','png','gif');
        $this->setMessage("extensions ".$ext);

        $uploadName='';
        /*if($size > $this->maxSize){
            $this->setMessage("Too large file !");
        }else */
        
        /*if(in_array($ext,$extensions)){*/
            $uploadName = $this->imageSeq."-".substr(md5(rand(1111,9999)),0,8).$this->getRandom().rand(1111,1000).rand(99,9999).".".$ext;
            $this->setMessage("Succes!");
            if(move_uploaded_file($fileBrowse["tmp_name"],'./uploads/'.$uploadName)){
                $result =   true;
                $this->uploadName='uploads/'.$uploadName;
                $this->setMessage("uploadname ".$uploadName. '***');
            }else{
                $this->setMessage("Upload failed , try later !");
            }
       /* }
        else{
            $this->setMessage("Invalid file format ! ".$ext."***");
        }*/
        return $result;
    }

    function deleteUploaded(){
        unlink($this->destinationPath.$this->uploadName);
    }

}
?>