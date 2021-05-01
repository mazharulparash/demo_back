<?php
use App\models\Order;
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

$order = new Order($con);

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
}

?>



