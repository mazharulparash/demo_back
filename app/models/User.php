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

    /*** for registration process ***/

    public function reg_user($name,$username,$password,$email, $type){

        $password = md5($password);
        $sql="SELECT * FROM users WHERE uname='$username' OR uemail='$email'";

        //checking if the username or email is available in db
        $check =  $this->db->query($sql) ;
        $count_row = $check->num_rows;

        //if the username is not in db then insert to the table
        if ($count_row == 0){
            try{
                $stmt = $this->db->prepare("INSERT INTO users(uname,upass,fullname,uemail,utype) 
                                                       VALUES(:uname, :upass, :fullname, :uemail, :utype)");

                // bind values
                $stmt->bindParam(":fullname", $name);
                $stmt->bindParam(":uname", $username);
                $stmt->bindParam(":upass", $password);
                $stmt->bindParam(":uemail", $email);
                $stmt->bindParam(":utype", $type);

                $stmt->execute();

                return $stmt;
            }
            catch(PDOException $e)
            {
                echo $e->getMessage();
            }
        }
        else { return false;}
    }

    /*** for login process ***/
    public function check_login($request, $type){

        $password = md5($request->password);

        $stmt = $this->db->prepare("SELECT id,utype from users WHERE uname=:username and upass=:upass and utype=:utype");
        //checking if the username is available in the table

        $this->validate($request);
        if (count($this->errors) > 0) {
            return ['errors' => $this->errors];
        }
        else{
            // bind values
            $stmt->bindParam(":username", $request->username);
            $stmt->bindParam(":upass", $password);
            $stmt->bindParam(":utype", $type);

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

    /*** for showing the username or fullname ***/
    public function get_fullname($uid){
        $stmt=$this->db->prepare("SELECT fullname FROM users WHERE id=:uid");

        $stmt->bindParam(":uid", $uid);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo $row['fullname'];
    }

    /*** starting the session ***/
    public function get_session(){
        return $_SESSION['login'];
    }

    public function user_logout() {
        $_SESSION['login'] = FALSE;
        session_destroy();
    }
}
?>