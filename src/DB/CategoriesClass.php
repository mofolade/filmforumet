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

    public function getCategory($categoryID){
        $id = 0; 
        $name = '';
        $image_path = '';

        $stmt = $this->connection -> prepare('SELECT c.id,
                                                     c.name,
                                                     c.image_path
                                               FROM categories c
                                              WHERE c.id = ?
                                              LIMIT 1');
        $stmt -> bind_param('i', $categoryID);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($id, $name, $image_path);
        $stmt -> fetch();
        return json_encode(["id"            => $id,
                            "name"          => $name,
                            "image_path"    => $image_path]);
       

    }

}
?>