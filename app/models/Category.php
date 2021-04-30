<?php


namespace App\models;


use PDO;

class Category
{

    // database connection and table name
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    function getAll() {
        $query = "SELECT
                id, name
            FROM
                categories";
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $categories;
    }
}