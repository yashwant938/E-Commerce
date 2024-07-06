<?php
class User
{
    public static $db;

    public static function constructStatic($db)
    {
        self::$db = $db;
    }
    
    public static function fetchUser($user_id){        
        $db = self::$db;
        try{          
            $sql="SELECT * FROM users WHERE user_id = :user_id LIMIT 1";
            $stmt = $db->prepare($sql);

            $params=array(
                'user_id' => $user_id
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
    
    
    public static function fetchOTPCount($user_id,$otp_type)
	{  
        try{
            $db = self::$db;
            $sql="SELECT COUNT(otp_id) as otp_count FROM email_otp WHERE user_id = :user_id AND otp_type = :otp_type";
            $stmt = $db->prepare($sql);

            $params=array(
                "user_id"=>$user_id,
                "otp_type"=>$otp_type
            );
        
            $stmt->execute($params);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result){
                return $result;
            }
            return false;            
        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function fetchAllUser()
	{  
        try{
            $db = self::$db;
            $stmt = $db->prepare("SELECT * FROM users ORDER BY user_id DESC");
        
            $stmt->execute();

            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);
            if($result){
                return $result;
            }
            return false;            
        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function fetchAllUserWithAuth()
	{  
        try{
            $db = self::$db;
            $stmt = $db->prepare("SELECT 
            u.*,
            (SELECT 
                    login_on
                FROM
                    auth_token aut
                WHERE
                    aut.user_id = u.user_id
                ORDER BY session_id DESC
                LIMIT 1) AS loggedin_on,
            (SELECT 
                    user_ip
                FROM
                    auth_token aut
                WHERE
                    aut.user_id = u.user_id
                ORDER BY session_id DESC
                LIMIT 1) AS user_ip,
            (SELECT 
                    user_ip
                FROM
                    auth_token aut
                WHERE
                    aut.user_id = u.user_id
                ORDER BY session_id DESC
                LIMIT 1) AS user_ip,
            (SELECT 
                    COUNT(p.property_id)
                FROM
                    property p
                WHERE
                    p.created_by = u.user_id
                        OR p.buyer_id = u.user_id) AS contracts
        FROM
            users u
        ORDER BY u.user_id DESC");
        
            $stmt->execute();

            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);
            if($result){
                return $result;
            }
            return false;            
        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }

    public static function addUser($full_name,$email,$password){        
        $db = self::$db;
        try
        {         
            $created_on = date("Y-m-d H:i:s", time());    
            $sql="INSERT INTO users SET full_name = :full_name, email = :email, password = :password, created_on = :created_on, `address` = ''";
            $stmt = $db->prepare($sql);

            $params=array(
                'full_name'=>$full_name,
                'email' => $email,
                'password' =>$password,
                'created_on' => $created_on
            );
            $stmt->execute($params);
            if($stmt->rowCount()){
                return $db->lastInsertId();
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return false;
    }

    public static function addEmailOTP($user_id,$code,$expire_on,$otp_type){        
        $db = self::$db;
        try
        {         
            if(!Email::sendEmail($user_id,$code)){
                return false;
            }
            $created_on = date("Y-m-d H:i:s", time());
            $sql="INSERT INTO email_otp SET user_id = :user_id, code = :code, expire_on = :expire_on, created_on = :created_on, `otp_type` = :otp_type";
            $stmt = $db->prepare($sql);

            $params=array(
                'user_id'=>$user_id,
                'code' => $code,
                'expire_on' =>$expire_on,
                'created_on' => $created_on,
                'otp_type' => $otp_type
            );
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return false;
    }
    
    
    public static function updateAadhar($user_id,$addhar_file){        
        $db = self::$db;
        try
        {         
            $modified_on = date("Y-m-d H:i:s", time());    
            $sql="UPDATE users SET addhar_file = :addhar_file, modified_on = :modified_on WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);

            $params=array(
                'addhar_file' =>$addhar_file,
                'modified_on' => $modified_on,
                'user_id' => $user_id
            );
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return false;
    }
    
    public static function toggleActive($user_id){        
        $db = self::$db;
        try
        {         
            $modified_on = date("Y-m-d H:i:s", time());    
            $sql="UPDATE users SET isActive = IF(isActive=1, 0, 1), modified_on = :modified_on WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);

            $params=array(
                'modified_on' => $modified_on,
                'user_id' => $user_id
            );
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return false;
    }
    
    public static function updateEKYC($user_id,$full_name,$addhar_file,$address,$kyc_email){        
        $db = self::$db;
        try
        {         
            $modified_on = date("Y-m-d H:i:s", time());    
            $sql="UPDATE users SET full_name = :full_name, addhar_file = :addhar_file, `address` = :address, modified_on = :modified_on, isKYC = 1, isDocVerified = 1, kyc_email = :kyc_email WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);

            $params=array(
                'full_name'=>$full_name,
                'kyc_email' => $kyc_email,
                'address' => $address,
                'addhar_file' =>$addhar_file,
                'modified_on' => $modified_on,
                'user_id' => $user_id
            );
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return false;
    }
        
    public static function updateUserDetails($user_id,$full_name,$address){        
        $db = self::$db;
        try
        {         
            $modified_on = date("Y-m-d H:i:s", time());    
            $sql="UPDATE users SET full_name = :full_name, `address` = :address, modified_on = :modified_on WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);

            $params=array(
                'full_name'=>$full_name,
                'address' => $address,
                'modified_on' => $modified_on,
                'user_id' => $user_id
            );
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return false;
    }
    public static function fetchResetLink($link)
	{  
        try{
            $db = self::$db;
            $stmt = $db->prepare("SELECT * FROM email_otp WHERE link = :link");
            $params = array(
                "link"=>$link
            );
            $stmt->execute($params);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result){
                return $result;
            }
            return false;            
        }
        catch(PDOException $e){
            return false;
        }
        return false;
    }
    public static function verifyResetOTP($user_id,$otp_id,$link,$link_expire_on){        
        $db = self::$db;
        try
        {         
            $timestamp = date("Y-m-d H:i:s", time());    
            $sql="UPDATE email_otp SET isVerified = 1, verified_on = :verified_on, link = :link, link_expire_on = :link_expire_on WHERE otp_id = :otp_id";
            $stmt = $db->prepare($sql);

            $params=array(
                'verified_on' => $timestamp,
                'otp_id' => $otp_id,
                'link' => $link,
                'link_expire_on' => $link_expire_on
            );
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return false;
    }
    public static function verifyEmail($user_id,$otp_id){   


        $db = self::$db;
        try
        {         
            $timestamp = date("Y-m-d H:i:s", time());    
            $sql="UPDATE email_otp SET isVerified = 1, verified_on = :verified_on WHERE otp_id = :otp_id";
            $stmt = $db->prepare($sql);
            $params=array(
                'verified_on' => $timestamp,
                'otp_id' => $otp_id
            );
            $stmt->execute($params);
            if(!$stmt->rowCount()){
                return false;
            }

            $sql="UPDATE users SET isEmailVerified = 1 WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);
            $params=array(
                'user_id' => $user_id
            );
            $stmt->execute($params);
            if(!$stmt->rowCount()){
                return false;
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return true;
    }
    public static function markLinkDone($link){        
        $db = self::$db;
        try
        {         
            $timestamp = date("Y-m-d H:i:s", time());    
            $sql="UPDATE email_otp SET isLinkUsed = 1 WHERE link = :link";
            $stmt = $db->prepare($sql);

            $params=array(
                'link' => $link
            );
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return false;
    }
    
    public static function resetPass($user_id,$password){        
        $db = self::$db;
        try
        {         
            $timestamp = date("Y-m-d H:i:s", time());    
            $sql="UPDATE users SET `password` = :password, modified_on = :modified_on WHERE user_id = :user_id";
            $stmt = $db->prepare($sql);

            $params=array(
                'password' => $password,
                'modified_on' => $timestamp,
                'user_id' => $user_id
            );
//            echo PdoDebugger::show($sql,$params);
            $stmt->execute($params);
            if($stmt->rowCount()){
                return true;
            }

        }
        catch(PDOException $e)
        {
            return false;
        }
        return false;
    }
    

    public static function isExist($email){        
        $db = self::$db;
        try{          
            $sql="SELECT user_id FROM users WHERE email = :email LIMIT 1";
            $stmt = $db->prepare($sql);

            $params=array(
                'email' => $email
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
    
    public static function fetchUserByEmail($email){        
        $db = self::$db;
        try{          
            $sql="SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $db->prepare($sql);

            $params=array(
                'email' => $email
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
    
    public static function fetchEmailOTP($user_id,$otp_type){        
        $db = self::$db;
        try{          
            $sql="SELECT * FROM email_otp WHERE user_id = :user_id AND otp_type = :otp_type ORDER BY otp_id DESC LIMIT 1";
            $stmt = $db->prepare($sql);

            $params=array(
                'user_id' => $user_id,
                'otp_type' => $otp_type
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
    
    public static function addUserAuth($session_uid,$user_id,$auth_token,$user_ip){        
        $db = self::$db;
        try
        {         
            $login_on = date("Y-m-d H:i:s", time());    
            $sql="INSERT INTO auth_token SET session_uid = :session_uid, user_id = :user_id, auth_token = :auth_token, login_on = :login_on, user_ip = :user_ip";
            $stmt = $db->prepare($sql);

            $params=array(
                'session_uid'=>$session_uid,
                'user_id' => $user_id,
                'auth_token' =>$auth_token,
                'login_on' => $login_on,
                'user_ip' => $user_ip
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