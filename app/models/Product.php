<?php
namespace App\models;
use PDO;

class Product{

    // database connection and table name
    private $conn;

    // object properties
    public $id;
    public $name;
    public $sku;
    public $price;
    public $description;
    public $category_id;
    public $image;
    public $timestamp;
    public $errors;

    public function __construct($db){
        $this->conn = $db;
    }

    function getAll() {
        $query = "SELECT
                id, name, sku, description, price, category_id, image
            FROM
                products
            ORDER BY
                id ASC";
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $products;
    }

    function getOne($id){

        $query = "SELECT id, name, sku, price, description, category_id, image
        FROM products
        WHERE id = ?
        LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->setValues($row);

        return $row;
    }

// create product
    function create($request){

        //write query
        $query = "INSERT INTO
                    products
                SET
                    name=:name, price=:price, description=:description, category_id=:category_id, sku=:sku, image=:image, created=:created";

        $stmt = $this->conn->prepare($query);

        $this->validate($request);
        if (count($this->errors) > 0) {
            return $this->errors;
        }
        else {
            $this->setValues($request);
            // to get time-stamp for 'created' field
            $this->timestamp = date('Y-m-d H:i:s');

            // bind values
            $this->bindParam($stmt);
            $stmt->bindParam(":created", $this->timestamp);

            if($stmt->execute()){
                return 'done';
            }
        }
    }

    function update($request) {
        $query = "UPDATE
                products
            SET
                name = :name,
                sku = :sku,
                price = :price,
                description = :description,
                category_id  = :category_id,
                image=:image
            WHERE
                id = :id";

        $stmt = $this->conn->prepare($query);

        $this->validate($request);
        if (count($this->errors) > 0) {
            return $this->errors;
        }
        else {
            $this->setValues($request);
            $this->id= $request->id;

            // bind parameters
            $this->bindParam($stmt);
            $stmt->bindParam(':id', $this->id);

            // execute the query
            if($stmt->execute()){
                return 'done';
            }
        }
    }

    function delete($id) {
        $query = "DELETE FROM products WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if($result = $stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    private function setValues($row) {
        $this->name = $row->name;
        $this->sku = $row->sku;
        $this->price = $row->price;
        $this->description = $row->description;
        $this->category_id = $row->category_id;
        $this->image = $row->image;
    }

    private function validate($row) {
        $this->errors = [];
        if (!$row->name || $row->name == "") {
            array_push($this->errors, 'Name Required');
        };
        if (!$row->sku || $row->sku == "") {
            array_push($this->errors, 'SKU Required');
        };
        if (!$row->price || $row->price == "") {
            array_push($this->errors, 'Price Required');
        };
        if (!$row->category_id || $row->category_id == "") {
            array_push($this->errors, 'Category Required');
        };
    }

    private function bindParam($stmt) {
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':sku', $this->sku);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':image', $this->image);
    }
}
?>

