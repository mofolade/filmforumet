<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class GeneralClass extends MySQL{
 
    public function __construct(){
        parent::__construct();
    }
 
}
?>