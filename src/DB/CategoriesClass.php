<?php
require_once(dirname(__FILE__).'/classMySQL.php');

class CategoriesClass extends MySQL{
    private $table_name = "categories";

    public function __construct(){
        parent::__construct();
    }

    public function getAllCategories(){
        $result = $this-> Select("categories");        
        return $result;
    }

}
?>