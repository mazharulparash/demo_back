<?php
use App\models\Product;
use App\models\Category;
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
require '../vendor/autoload.php';
//use App\config\Database;
include_once '../app/config/Database.php';

// get database connection
$database = new Database();
$con = $database->getConnection();

$product = new Product($con);
$category = new Category($con);


$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
//$input = json_decode(file_get_contents('php://input'),true);

if (!$con) {
    die("Connection failed! ");
}

if ($method) {
    $categories = $category->getAll();
    if ($method == 'GET' && $request[0] == "") {
        $products = $product->getAll();

        echo json_encode([
           "products" => $products,
           "categories" => $categories,
        ]);
    }
    elseif ($method == 'GET' && $request[0] != "") {
        $product = $product->getOne($request[0]);

        echo json_encode([
            "product" => $product,
            "categories" => $categories,
        ]);
    }

    elseif ($method == 'POST' && $request[0] == "") {
        $request = file_get_contents('php://input');
        $create = $product->create(json_decode($request));
        $errors = [];
        if ($create == 'done') {
            $msg = 'The Product was created successfully!';
        }
        else {
            $msg = 'Failed to create The Product!';
            $errors = $create;
        }
        echo json_encode([
            "message" => $msg,
            "errors" => $errors
        ]);
    }

    elseif ($method == 'PUT') {
        $request = file_get_contents('php://input');
        $update = $product->update(json_decode($request));
        $errors = [];
        if ($update == 'done') {
            $msg = 'The Product was updated successfully!';
        }
        else {
            $msg = 'Failed to update The Product!';
            $errors = $update;
        }
        echo json_encode([
            "message" => $msg,
            "errors" => $errors
        ]);
    }

    elseif ($method == 'DELETE') {
        $delete = $product->delete(json_decode($request[0]));
        if ($delete) {
            $msg = 'The Product was deleted successfully!';
        }
        else {
            $msg = 'Failed to delete The Product!';
        }
        echo json_encode([
            "message" => $msg
        ]);
    }
}

?>



