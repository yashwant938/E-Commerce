<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email
{
    public static $db;

    public static function constructStatic($db){
        self::$db = $db;
    }   
	public static function sendEmail($user_id,$code){
        

        $user = User::fetchUser($user_id);
        $subject = "OTP Verification";
        $msg="Hello ".$user["full_name"]."<br>You OTP is ".$code;

        $mail = new PHPMailer(true);
        try {
            // Server settings
            //  $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Port = 465;
            $mail->Username = 'fcs.cyb3rc1ph3r@gmail.com'; // YOUR email username
            $mail->Password = 'frhw ifpg aqyr qnxw'; // YOUR email password
        
            // Sender and recipient settings
            $mail->setFrom('fcs.cyb3rc1ph3r@gmail.com', 'Cyb3rc1ph3r');
            
            $mail->addAddress($user["email"], $user["full_name"]);
        
            // Setting the email content
            $mail->IsHTML(true);
            $mail->Subject = $subject;
        
        
            $mail->Body = $msg;
            $ismailsent = $mail->send();
        
            if(!$ismailsent) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}

?>