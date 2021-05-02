<?php
namespace App\models;
use PDO;

class User{

    // database connection and table name
    private $db;
    private $errors;

    public function __construct($db){
        $this->db = $db;
    }

    function getAll() {
        $query = "SELECT
                id,fullname
            FROM
                users
            ORDER BY
                id ASC";
        $stmt = $this->db->prepare( $query );
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    /*** for registration process ***/

    public function reg_user($request, $type){

        $password = md5($request->password);
        $sql="SELECT * FROM users WHERE uname='$request->username' OR uemail='$request->email'";

        //checking if the username or email is available in db
        $check =  $this->db->query($sql) ;
        $count_row = $check->num_rows;

        //if the username is not in db then insert to the table
        if ($count_row == 0){
            $validate = $this->validateReg($request);
            if ($validate) {
                $stmt = $this->db->prepare("INSERT INTO users(uname,upass,fullname,uemail,utype) 
                                                   VALUES(:uname, :upass, :fullname, :uemail, :utype)");

                // bind values
                $this->bindParam($stmt,$request, $password, $type);

                $stmt->execute();

                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /*** for login process ***/
    public function check_login($request){

        $this->validate($request);
        if (count($this->errors) > 0) {
            return ['errors' => $this->errors];
        }
        else{
            $password = md5($request->password);
            $stmt = $this->db->prepare("SELECT id,utype from users WHERE uname=:username and upass=:upass");
            // bind values
            $stmt->bindParam(":username", $request->username);
            $stmt->bindParam(":upass", $password);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stmt->rowCount() > 0) {
                return ['login' => [true, $row['id'], $row['utype']]];
            }
        }
    }

    private function validate($row) {
        $this->errors = [];
        if (!$row->username || $row->username == "") {
            array_push($this->errors, 'Username Required');
        };
        if (!$row->password || $row->password == "") {
            array_push($this->errors, 'Password Required');
        };
    }

    private function validateReg($row) {
        if (!$row->fullname || $row->fullname == "") {
            return false;
        };
        if (!$row->username || $row->username == "") {
            return false;
        };
        if (!$row->username || $row->username == "") {
            return false;
        };
        if (!$row->email || $row->email == "") {
            return false;
        };
        return true;
    }

    private function bindParam($stmt,$request, $password, $type) {
        $stmt->bindParam(":fullname", $request->fullname);
        $stmt->bindParam(":uname", $request->username);
        $stmt->bindParam(":upass", $password);
        $stmt->bindParam(":uemail", $request->email);
        $stmt->bindParam(":utype", $type);
    }
}
?>
