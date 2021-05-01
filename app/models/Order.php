<?php
namespace App\models;
use PDO;

class Order{

    // database connection and table name
    private $db;
    private $tracking_no;
    private $product_id;
    private $customer_id;
    private $status;
    private $created_at;

    public function __construct($db){
        $this->db = $db;
    }

    /*** for registration process ***/

    public function place_order($request){

        //write query
        $query = "INSERT INTO
                    orders
                SET
                    tracking_no=:tracking_no, product_id=:product_id, customer_id=:customer_id, status=:status, created_at=:created_at";
        $stmt = $this->db->prepare($query);
        $tracking_no = $this->getTrackingNo();
        $created_at = date('Y-m-d H:i:s');
        $this->setValue($request, $tracking_no, $created_at);
        $this->bindParam($stmt);
        $stmt->execute();

        return true;
    }

    private function setValue($request, $tracking_no, $created_at) {
        $this->tracking_no = $tracking_no;
        $this->product_id = $request->pid;
        $this->customer_id = $request->cid;
        $this->status = 'processing';
        $this->created_at = $created_at;
    }

    private function bindParam($stmt) {
        $stmt->bindParam(':tracking_no', $this->tracking_no);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':created_at', $this->created_at);
    }

    private function getTrackingNo() {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 9; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
?>
