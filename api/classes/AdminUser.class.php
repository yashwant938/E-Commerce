<?php
class AdminUser
{
    public static $db;

    public static function constructStatic($db)
    {
        self::$db = $db;
    }

    public static function fetchUserByUsername($username){        
        $db = self::$db;
        try{          
            $sql="SELECT * FROM admin_users WHERE username = :username LIMIT 1";
            $stmt = $db->prepare($sql);

            $params=array(
                'username' => $username
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
    
    public static function addUserAuth($admin_id,$user_ip){        
        $db = self::$db;
        try
        {         
            $login_on = date("Y-m-d H:i:s", time());    
            $sql="INSERT INTO admin_auth SET admin_id = :admin_id, login_on = :login_on, user_ip = :user_ip";
            $stmt = $db->prepare($sql);

            $params=array(
                'admin_id' => $admin_id,
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