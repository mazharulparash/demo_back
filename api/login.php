<?php
// Turn off error reporting
error_reporting(0);
use App\models\User;
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

$user = new User($con);


$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
//$input = json_decode(file_get_contents('php://input'),true);

if (!$con) {
    die("Connection failed! ");
}

if ($method) {
    if ($method == 'POST' && $request[0] == "") {
        $request = file_get_contents('php://input');
        $check = $user->check_login(json_decode($request));
        $errors = [];
        $session = [false, '', ''];
        if (array_key_exists('login', $check)) {
            $msg = 'done';
            session_start();
            $session = $check['login'];
        }
        else {
            $msg = 'failed';
            $errors = $check['errors'];
        }
        echo json_encode([
            "authenticate" => $msg,
            "errors" => $errors,
            "session" => $session
        ]);
    }
    if ($method == 'POST' && $request[0] == "register") {
        $request = file_get_contents('php://input');
        $reg = $user->reg_user(json_decode($request), 'customer');
          echo json_encode([
            "success" => $reg
        ]);
    }
}

?>



