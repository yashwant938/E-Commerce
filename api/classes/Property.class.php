<?php
class Property
{
    public static $db;

    public static function constructStatic($db)
    {
        self::$db = $db;
    }
    public static function fetchProperty($property_id){        
        $db = self::$db;
        try{          
            $sql="SELECT p.*, u.full_name AS owner_name, bu.full_name AS buyer_name, l.location_name AS `location`, p.location AS location_id
            FROM property p
            LEFT JOIN users u ON u.user_id = p.created_by
            LEFT JOIN users bu ON bu.user_id = p.buyer_id
            LEFT JOIN locations l ON l.location_id = p.location         
            WHERE p.property_id = :property_id AND p.isActive=1
            ";
            $stmt = $db->prepare($sql);

            $params=array(
                'property_id' => $property_id
            );
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				return $result;
			}

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function fetchPropertyByUID($property_uid){        
        $db = self::$db;
        try{          
            $sql="SELECT p.*, 
            u.full_name AS owner_name,
            u.address AS owner_address,
            u.email AS owner_email,            
            bu.full_name AS buyer_name,
            bu.address AS buyer_address, 
            bu.email AS buyer_email, 
            l.location_name AS `location`, 
            p.location AS location_id
            FROM property p
            LEFT JOIN users u ON u.user_id = p.created_by
            LEFT JOIN users bu ON bu.user_id = p.buyer_id  
            LEFT JOIN locations l ON l.location_id = p.location            
            WHERE p.property_uid = :property_uid AND p.isActive=1
            ";
            $stmt = $db->prepare($sql);

            $params=array(
                'property_uid' => $property_uid
            );
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				return $result;
			}

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    
    public static function fetchAllProperty($user_id,$type,$min_budget,$max_budget,$avi_date,$location_arr,$amenity_arr,$search){        
        $db = self::$db;
        try{          
//            $sql="SELECT * FROM property WHERE created_by != :user_id AND isSold=0";
            $sql="SELECT p.*, 
            l.location_name AS `location`, 
            p.location AS location_id
            FROM property p
            LEFT JOIN locations l ON l.location_id = p.location 
            WHERE p.location IN (".implode(',', $location_arr).") 
            AND (p.amenity LIKE '%".implode("%' OR p.amenity LIKE '%", $amenity_arr)."%' )
            AND (p.property_type IN (".implode(',', $type).") )
            AND (DATE(p.available_from) <= :avi_date )
            AND ((p.price >= :min_budget OR :min_budget='') AND (p.price <= :max_budget OR :max_budget='')) 
            AND (p.address LIKE :search)
            AND p.isActive=1
            ORDER BY p.property_id DESC
            ";
            $stmt = $db->prepare($sql);
/*
            $params=array(
                'user_id' => $user_id
            );
            */
            $params=array(
                'search'=>"%".$search."%",
                'avi_date'=>$avi_date,
                'min_budget'=>$min_budget,
                'max_budget'=>$max_budget,
            );
          //  echo PdoDebugger::show($sql,$params);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				return $result;
			}

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function fetchAllAdminProperty(){        
        $db = self::$db;
        try{          
            $sql="SELECT p.*, 
            u.full_name AS owner_name,
            mu.full_name AS modified_name,
            u.address AS owner_address,
            u.email AS owner_email,            
            bu.full_name AS buyer_name,
            bu.address AS buyer_address, 
            bu.email AS buyer_email, 
            l.location_name AS `location`, 
            p.location AS location_id
            FROM property p
            LEFT JOIN users u ON u.user_id = p.created_by
            LEFT JOIN users mu ON mu.user_id = p.modified_by
            LEFT JOIN users bu ON bu.user_id = p.buyer_id  
            LEFT JOIN locations l ON l.location_id = p.location  
            ORDER BY p.property_id DESC
            ";
            $stmt = $db->prepare($sql);

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				return $result;
			}

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function fetchAllMyProperty($user_id){        
        $db = self::$db;
        try{          
            $sql="SELECT p.*, u.full_name AS owner_name, bu.full_name AS buyer_name,
            l.location_name AS `location`, 
            p.location AS location_id 
            FROM property p
            LEFT JOIN users u ON u.user_id = p.created_by
            LEFT JOIN users bu ON bu.user_id = p.buyer_id
            LEFT JOIN locations l ON l.location_id = p.location 
            WHERE (p.created_by = :user_id OR p.buyer_id = :user_id) AND p.isActive=1
            ORDER BY p.property_id DESC
            ";
            $stmt = $db->prepare($sql);

            $params=array(
                'user_id' => $user_id
            );
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				return $result;
			}

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    
    public static function fetchAllAmenity(){        
        $db = self::$db;
        try{          
            $sql="SELECT * FROM amenity";
            $stmt = $db->prepare($sql);

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				return $result;
			}

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function fetchAllLocations(){        
        $db = self::$db;
        try{          
            $sql="SELECT * FROM locations";
            $stmt = $db->prepare($sql);

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if($result){
				return $result;
			}

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    
    public static function addProperty($property_uid,$location,$property_size,$price,$property_type,$description,$images,$address,$amenity,$available_from,$property_ownership,$lease_months,$user_id){        
        $db = self::$db;
        try
        {         
            $created_on = date("Y-m-d H:i:s", time());    
            $sql="INSERT INTO property SET `location` = :property_location, property_size = :property_size, price = :price, property_type = :property_type, `description` = :property_description, images = :images, `address` = :address, created_by = :created_by, created_on = :created_on, amenity = :amenity, available_from = :available_from, property_uid = :property_uid, property_ownership = :property_ownership, lease_months = :lease_months";
            $stmt = $db->prepare($sql);

            $params=array(
                'property_uid' => $property_uid,
                'property_ownership' => $property_ownership,
                'address' => $address,
                'property_location' => $location,
                'property_size' => $property_size,
                'price' => $price,
                'property_type'=>$property_type,
                'property_description'=>$description,
                'lease_months'=>$lease_months,
                'images'=>$images,
                'created_by'=>$user_id,
                'created_on'=>$created_on,
                'amenity'=>$amenity,
                'available_from'=>$available_from,

            );
//            echo PdoDebugger::show($sql,$params);
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function addPayment($property_id,$receiver_id,$sender_id,$amount,$receipt_id,$razorpay_order_id,$status){        
        $db = self::$db;
        try
        {         
            $created_on = date("Y-m-d H:i:s", time());    
            $sql="INSERT INTO payment SET `property_id` = :property_id, receiver_id = :receiver_id, sender_id = :sender_id, amount = :amount, `receipt_id` = :receipt_id, razorpay_order_id = :razorpay_order_id, `status` = :status, created_on = :created_on";
            $stmt = $db->prepare($sql);

            $params=array(
                'property_id' => $property_id,
                'receiver_id' => $receiver_id,
                'sender_id' => $sender_id,
                'amount' => $amount,
                'receipt_id' => $receipt_id,
                'razorpay_order_id'=>$razorpay_order_id,
                'status'=>$status,
                'created_on'=>$created_on
            );
//            echo PdoDebugger::show($sql,$params);
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function updatePayment($razorpay_order_id,$razorpay_payment_id,$razorpay_signature,$kyc_email,$status){        
        $db = self::$db;
        try
        {         
            $modified_on = date("Y-m-d H:i:s", time());    
            $sql="UPDATE payment SET `razorpay_payment_id` = :razorpay_payment_id, razorpay_signature = :razorpay_signature, status = :status, modified_on = :modified_on, kyc_email = :kyc_email WHERE razorpay_order_id = :razorpay_order_id";
            $stmt = $db->prepare($sql);

            $params=array(
                'razorpay_order_id' => $razorpay_order_id,
                'razorpay_payment_id' => $razorpay_payment_id,
                'razorpay_signature' => $razorpay_signature,
                'status' => $status,
                'modified_on' => $modified_on,
                'kyc_email'=> $kyc_email
            );
//            echo PdoDebugger::show($sql,$params);
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    
    public static function updateProperty($property_uid,$location,$property_size,$price,$property_type,$description,$address,$amenity,$available_from,$lease_months,$user_id){        
        $db = self::$db;
        try
        {         
            $modified_on = date("Y-m-d H:i:s", time());    
            $sql="UPDATE property SET `location` = :property_location, property_size = :property_size, price = :price, property_type = :property_type, `description` = :property_description, `address` = :address, modified_by = :modified_by, modified_on = :modified_on, amenity = :amenity, available_from = :available_from, lease_months = :lease_months WHERE property_uid = :property_uid";
            $stmt = $db->prepare($sql);

            $params=array(
                'lease_months' => $lease_months,
                'property_uid' => $property_uid,
                'address' => $address,
                'property_location' => $location,
                'property_size' => $property_size,
                'price' => $price,
                'property_type'=>$property_type,
                'property_description'=>$description,
                'modified_by'=>$user_id,
                'modified_on'=>$modified_on,
                'amenity'=>$amenity,
                'available_from'=>$available_from,

            );
//            echo PdoDebugger::show($sql,$params);
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    
    public static function buyProperty($property_uid,$user_id){        
        $db = self::$db;
        try
        {         
            $modified_on = date("Y-m-d H:i:s", time());    
            $buy_date = date("Y-m-d", time());   
            $sql="UPDATE property SET isSold = 1, buyer_id = :buyer_id , modified_by = :modified_by, modified_on = :modified_on, buy_date = :buy_date WHERE property_uid = :property_uid";
            $stmt = $db->prepare($sql);

            $params=array(
                'property_uid' => $property_uid,
                'buyer_id'=>$user_id,
                'modified_by'=>$user_id,
                'modified_on'=>$modified_on,
                'buy_date'=>$buy_date,
            );
            
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    
    public static function removeProperty($property_uid,$user_id){        
        $db = self::$db;
        try
        {         
            $modified_on = date("Y-m-d H:i:s", time());    
            $sql="UPDATE property SET isActive = 0, modified_by = :modified_by, modified_on = :modified_on WHERE property_uid = :property_uid";
            $stmt = $db->prepare($sql);

            $params=array(
                'property_uid' => $property_uid,
                'modified_by'=>$user_id,
                'modified_on'=>$modified_on,
            );
            
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function removeAdmin($property_id){        
        $db = self::$db;
        try
        {         
            $modified_on = date("Y-m-d H:i:s", time());    
            $sql="UPDATE property SET isActive = IF(isActive=1, 0, 1), modified_on = :modified_on WHERE property_id = :property_id";
            $stmt = $db->prepare($sql);

            $params=array(
                'property_id' => $property_id,
                'modified_on'=>$modified_on,
            );
            
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
}

?>