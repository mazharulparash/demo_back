<?php
// Turn off error reporting
error_reporting(0);
use App\models\Order;
use App\models\Product;
use App\models\User;
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: GET, PUT, POST, PATCH, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
require '../vendor/autoload.php';
//use App\config\Database;
include_once '../app/config/Database.php';

// get database connection
$database = new Database();
$con = $database->getConnection();

$order = new Order($con);
$product = new Product($con);
$user = new User($con);

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
//$input = json_decode(file_get_contents('php://input'),true);

if (!$con) {
    die("Connection failed! ");
}

if ($method) {

    if ($method == 'POST' && $request[0] == "place") {
        $request = file_get_contents('php://input');
        $placed = false;
        $placed = $order->place_order(json_decode($request));
        echo json_encode([
            "placed" => $placed
        ]);
    }
    elseif ($method == 'GET' && $request[0] == "") {
        $orders = $order->getAll();
        $products = $product->getAll();
        $users = $user->getAll();
        echo json_encode([
            "orders" => $orders,
            "products" => $products,
            "users" => $users
        ]);
    }
    elseif ($method == 'GET' && $request[0] != "") {
        $orders = $order->getCus($request[0]);
        $products = $product->getAll();
        $users = $user->getAll();
        echo json_encode([
            "orders" => $orders,
            "products" => $products,
            "users" => $users
        ]);
    }
    elseif ($method == 'PATCH' && $request[0] != "") {
        $order = $order->getOrd($request[0]);
        echo json_encode([
            "order" => $order
        ]);
    }
    elseif ($method == 'PUT') {
        $request = file_get_contents('php://input');
        $update = $order->update(json_decode($request));
        $msg = "Failed to update Status";
        $errors = [];
        if ($update) {
            $msg = 'The Status was updated successfully!';
        }
        echo json_encode([
            "message" => $msg,
            "errors" => $errors
        ]);
    }
}

?>



