<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Psr7\Response as Psr7Response;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$app->post('/user/signup', function (Request $request, Response $response) use ($app,$csrf) {
    $data = $request->getParsedBody();

    if (!$csrf->validate('signup-form')) {
        $_SESSION["error_message"]="Invalid Token, Refresh page";         
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('signUpForm'))->withStatus(302);
    }

    if (!isset($_POST['g-recaptcha-response'])){
        $_SESSION["error_message"]="Captcha Missing";      
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('signUpForm'))->withStatus(302);
    }

    $recaptchaSecretKey = Config::CAPTCHA_SERVER;
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $verificationUrl = "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecretKey}&response={$recaptchaResponse}";
    $verification = json_decode(file_get_contents($verificationUrl));
    if (!$verification->success) {
        $_SESSION["error_message"]="Invalid Captcha";      
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('signUpForm'))->withStatus(302);
    }
    
    if(!isset($data["full_name"]) || !isset($data["email"]) || !isset($data["password"]) || trim($data["full_name"])=="" || trim($data["email"])=="" || trim($data["password"]=="")){        
        $_SESSION["error_message"]="All fields are required";         
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('signUpForm'))->withStatus(302);
    }


    if(!Functions::isValidEmail($data["email"])){      
        $_SESSION["error_message"]="Invalid Email";         
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('signUpForm'))->withStatus(302);
    }



    $full_name = $data["full_name"];
    $email = $data["email"];
    $password = password_hash($data["password"], PASSWORD_DEFAULT);
    
    $db = Database::getInstance();
    User::constructStatic($db);

    if(User::isExist($email)){
        
        $_SESSION["error_message"]="User Already exist";
        
    return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('signUpForm'))->withStatus(302);

    }

    $userId=User::addUser($full_name,$email,$password);

    if($userId){
        $code = rand (10000,99999);
        $expire_on = date("Y-m-d H:i:s", strtotime("+15 minutes", time()));

        User::addEmailOTP($userId,$code,$expire_on,"0");
        $_SESSION["isLoggedIn"]=true;
        $_SESSION["full_name"]=$full_name;
        $_SESSION["email"]=$email;
        $_SESSION["userId"]=$userId;
        $_SESSION["isKYC"]=0;
        $_SESSION["address"]="";
        $_SESSION["isActive"]=1;
        $_SESSION["addhar_file_name"]="";
        $_SESSION["isEmailVerified"]=0;

        
         return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);

    }else{
        
        $_SESSION["error_message"]="Error while creating user";
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('signUpForm'))->withStatus(302);

    }
    
    return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('signUpForm'))->withStatus(302);

//    return $response->withJson(array("message"=>$message,"data"=>array()))->withStatus($status);    
})->setName('signup');



$app->post('/user/signin', function (Request $request, Response $response) use ($app,$csrf) {
    $data = $request->getParsedBody();

    if (!$csrf->validate('login-form')){
        $_SESSION["error_message"]="Invalid Token, Refresh page";      
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    if (!isset($_POST['g-recaptcha-response'])){
        $_SESSION["error_message"]="Captcha Missing";      
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }

    $recaptchaSecretKey = Config::CAPTCHA_SERVER;
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $verificationUrl = "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecretKey}&response={$recaptchaResponse}";
    $verification = json_decode(file_get_contents($verificationUrl));
    if (!$verification->success) {
        $_SESSION["error_message"]="Invalid Captcha";      
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }

    //Validation
    if(!isset($data["email"]) || !isset($data["password"]) || trim($data["email"])=="" || trim($data["password"]=="")){
        $_SESSION["error_message"]="All fields are required";
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
//        return $response->withJson(array("message"=>"All fields are required","data"=>array()))->withStatus(400);
    }

    $email = $data["email"];
    $password = $data["password"];
    
    $db = Database::getInstance();
    User::constructStatic($db);
    Functions::constructStatic($db);
    
    $user = User::fetchUserByEmail($email); 
    if(!$user){
        $_SESSION["error_message"]="Incorrect email or password";
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);

//        return $response->withJson(array("message"=>"Incorrect email or password","data"=>array()))->withStatus(400); 
    }
    $encypted_password = $user["password"];
    if(password_verify($password, $encypted_password)) {
        
        $session_uid = Functions::GetUniqueID("session_uid","auth_token",8);
        $auth_token = Functions::GetUniqueIDSmall("auth_token","auth_token",100);

        $user_ip = $request->getServerParam('REMOTE_ADDR');
        if(User::addUserAuth($session_uid,$user["user_id"],$auth_token,$user_ip)){
            
            $_SESSION["isLoggedIn"]=true;
            $_SESSION["full_name"]=$user["full_name"];
            $_SESSION["email"]=$user["email"];
            $_SESSION["userId"]=$user["user_id"];
            $_SESSION["isKYC"]=$user["isKYC"];
            $_SESSION["address"]=$user["address"];
            $_SESSION["isActive"]=$user["isActive"];
            $_SESSION["isEmailVerified"]=$user["isEmailVerified"];
            
            $_SESSION["addhar_file_name"]=$user["addhar_file"];

//            return $response->withJson(array("message"=>"User LoggedIn","data"=>array("token"=>$auth_token)))->withStatus(200);            
        }else{
            $_SESSION["error_message"]="Error in login";
        }
//        return $response->withJson(array("message"=>"Error while generating token"))->withStatus(400);
    }else{        
        $_SESSION["error_message"]="Incorrect email or password";
    }

    
    return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    
//    return $response->withJson(array("message"=>"Incorrect email or password","data"=>array()))->withStatus(400);    
})->setName('signin');




$app->get('/signUp', function (Request $request, Response $response) use ($app,$csrf) {    
    $renderer = new PhpRenderer('pages');
    return $renderer->render($response, "signUp.php",['route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);  
})->setName('signUpForm');


$app->get('/ekyc', function (Request $request, Response $response) use ($app,$csrf) {    
    if (isset($_SESSION['isLoggedIn'])) {
        if($_SESSION["isKYC"]==1){
            $response = new Psr7Response();
                return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
        }
        
    if($_SESSION["isEmailVerified"]==0){
        $response = new Psr7Response();
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('verifyEmailForm'))->withStatus(302);
    }
        return $this->get('renderer')->render($response, 'ekyc.php',['route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }
    $response = new Psr7Response();
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
})->setName('ekyc');

$app->post('/ekyc', function (Request $request, Response $response) use ($app,$csrf) {  
    if (!isset($_SESSION['isLoggedIn'])) {
        $response = new Psr7Response();
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    
    if($_SESSION["isKYC"]==1){
        $response = new Psr7Response();
            return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    
    if($_SESSION["isEmailVerified"]==0){
        $response = new Psr7Response();
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('verifyEmailForm'))->withStatus(302);
    }
    if (!$csrf->validate('ekyc-form')){        
        return $this->get('renderer')->render($response, 'ekyc.php', ['message' => "Invalid Token, Refresh page",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }
    $data = $request->getParsedBody();

    if(!isset($data["kyc_full_name"]) || !isset($data["ekyc_address"]) || !isset($data["kyc_email"]) || !isset($data["kyc_password"]) || trim($data["kyc_full_name"])=="" || trim($data["ekyc_address"])=="" || trim($data["kyc_email"])=="" || trim($data["kyc_password"]=="")){
        return $this->get('renderer')->render($response, 'ekyc.php', ['message' => "All fields are required",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }


    $db = Database::getInstance();
    User::constructStatic($db);
    Functions::constructStatic($db);

    $full_name=$data["kyc_full_name"];
    $address=$data["ekyc_address"];

    $kyc_email=$data["kyc_email"];
    $kyc_password=$data["kyc_password"];
    

    $addhar_file="";
    $user_id=$_SESSION["userId"];

    if(!isset($request->getUploadedFiles()["addhar_file"])
    || trim($request->getUploadedFiles()["addhar_file"]->getFilePath())==""){   
        return $this->get('renderer')->render($response, 'ekyc.php', ['message' => "Aadhar File Required",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]); 
    }

    $file = $request->getUploadedFiles()["addhar_file"];
    
    $file_name = $file->getClientFilename();
    $FileType = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));
    
    $kycData='{
        "email": "'.$kyc_email.'",
        "password": "'.$kyc_password.'"
       }';
//    $apiData=API::PostData(Config::API_LINK,$kycData);

    //   $apiData=array("status"=>"success");
    if(true){
        if($FileType!="pdf"){
            $message="Invalid File";
        }
        else{
            $addhar_file_name = Functions::generateRandomString(100).".".$FileType;    
            //var_dump($file);
            //exit();

            $file->moveTo("files/upload/aadhar/".$addhar_file_name);

        
            $message="";
        
         
            if(User::updateEKYC($_SESSION["userId"],$full_name,$addhar_file_name,$address,$kyc_email)){
                $message="KYC data submited"; 
                $_SESSION["full_name"]=$full_name;
                $_SESSION["isKYC"]=1;
                $_SESSION["address"]=$address;
                $_SESSION["addhar_file_name"]=$addhar_file_name;
                $response = new Psr7Response();   
                return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
            }else{
                $message="Error while submiting data";
            }
        }
    }else{
        $message="Invalid KYC email or password";
    }


 
    return $this->get('renderer')->render($response, 'ekyc.php', ['message' => $message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);

})->setName('addEkyc');



$app->post('/verifyEkyc', function (Request $request, Response $response) use ($app,$csrf) {  

    $db = Database::getInstance();
    User::constructStatic($db);
    Functions::constructStatic($db);

    $data = $request->getParsedBody();

    
    if (!$csrf->validate('paymentcheckkyc-form')) {
        return $response->withJson(array("message"=>"Invalid Token, Refresh page","status"=>"400"))->withStatus(200);  
    }

    
    if(!isset($data["kyc_email"]) || !isset($data["kyc_password"]) || trim($data["kyc_email"])=="" || trim($data["kyc_password"]=="")){
        return $response->withJson(array("message"=>"All fields are required","status"=>"400"))->withStatus(200);
    }


    $kyc_email=$data["kyc_email"];
    $kyc_password=$data["kyc_password"];
    

    $user_id=$_SESSION["userId"];


    
    $kycData='{
        "email": "'.$kyc_email.'",
        "password": "'.$kyc_password.'"
       }';
    
//    $apiData=API::PostData(Config::API_LINK,$kycData);
    
//    return $response->withJson(array("message"=>"Valid","status"=>"200"))->withStatus(200);  

    if(true){
        return $response->withJson(array("message"=>"Valid","status"=>"200"))->withStatus(200);  
    }
    return $response->withJson(array("message"=>"Invalid email or password","status"=>"400"))->withStatus(200);  

})->setName('verifyEkyc')->add($authMiddleware);


$app->post('/aadhar/update', function (Request $request, Response $response) use ($app,$csrf) {  

    $db = Database::getInstance();
    User::constructStatic($db);
    Functions::constructStatic($db);

    $data = $request->getParsedBody();

    
    if (!$csrf->validate('aadharupdate-form')){
        $_SESSION["message"]="Invalid Token, Refresh page";
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('profile'))->withStatus(302);

    }

    if(!isset($request->getUploadedFiles()["addhar_file"])
    || trim($request->getUploadedFiles()["addhar_file"]->getFilePath())==""){   
        $_SESSION["message"]="Aadhar File Required";
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('profile'))->withStatus(302);
    }

    $addhar_file="";
    $user_id=$_SESSION["userId"];

    $file = $request->getUploadedFiles()["addhar_file"];
    
    $file_name = $file->getClientFilename();
    $FileType = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));


    if($FileType!="pdf"){
        $message="Invalid File";
    }
    else{
        $addhar_file_name = Functions::generateRandomString(100).".".$FileType;    
        $file->moveTo("files/upload/aadhar/".$addhar_file_name);
    
        $message="";
    
        
        if(User::updateAadhar($_SESSION["userId"],$addhar_file_name)){
            $message="Aadhar updated"; 

            unlink("files/upload/aadhar/".$_SESSION["addhar_file_name"]);

            $_SESSION["addhar_file_name"]=$addhar_file_name;

            
        }else{
            $message="Error while submiting data";
        }
    }

    $_SESSION["message"]=$message;
    return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('profile'))->withStatus(302);

})->setName('updateAadhar')->add($authMiddleware);;






$app->get('/reset', function (Request $request, Response $response) use ($app,$csrf) {  

    $db = Database::getInstance();
    User::constructStatic($db);

    $renderer = new PhpRenderer('pages');
    return $renderer->render($response, "forgot.php",['type'=>'0','route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);

})->setName('resetPassword');


$app->post('/reset', function (Request $request, Response $response) use ($app,$csrf) {  

    $db = Database::getInstance();
    User::constructStatic($db);
    
    $renderer = new PhpRenderer('pages');
    
    if (!$csrf->validate('sendotp-form')){
        return $renderer->render($response, "forgot.php",['type'=>'0','message'=>"Invalid Token, Refresh page",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    $data = $request->getParsedBody();
    $reset_email = $data["reset_email"];

    if(!isset($data["reset_email"]) || trim($data["reset_email"])==""){
        $message="Email required";
        return $renderer->render($response, "forgot.php",['type'=>'0','message'=>$message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }
    
    $user = User::fetchUserByEmail($reset_email); 
    
    if(!$user){
        return $renderer->render($response, "forgot.php",['type'=>'0','message'=>"Email doesn't exist",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);        
    }

    $userId=$user["user_id"];
    $code = rand (10000,99999);
    $expire_on = date("Y-m-d H:i:s", strtotime("+15 minutes", time()));

    if(User::addEmailOTP($userId,$code,$expire_on,"1")){
        
        $message="OTP send on Email";
        return $renderer->render($response, "forgot.php",['type'=>'1','message'=>$message,'route'=>$app->getRouteCollector()->getRouteParser(),'reset_email'=>$reset_email,'csrf'=>$csrf]);
    }

    $message="Error while sending email";
    return $renderer->render($response, "forgot.php",['type'=>'0','message'=>$message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);

})->setName('sendResetOTP');


$app->post('/verifyOTP', function (Request $request, Response $response) use ($app,$csrf) {  

    
    $data = $request->getParsedBody();
    $reset_email = $data["reset_email"];

    
    $renderer = new PhpRenderer('pages');


    if(!isset($data["reset_email"]) || trim($data["reset_email"])==""){
        return $renderer->render($response, "forgot.php",['type'=>'1','message'=>"Email required",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf,'reset_email'=>""]);
    }
    if(!isset($data["reset_otp"]) || trim($data["reset_otp"])==""){
        return $renderer->render($response, "forgot.php",['type'=>'1','message'=>"OTP required",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf,'reset_email'=>$data["reset_email"]]);
    }


    $db = Database::getInstance();
    User::constructStatic($db);
    Functions::constructStatic($db);

    
    $user = User::fetchUserByEmail($reset_email); 
    
    if(!$user){
        return $renderer->render($response, "forgot.php",['type'=>'0','message'=>"Email doesn't exist",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);        
    }

    if (!$csrf->validate('verifyotp-form')){
        return $renderer->render($response, "forgot.php",['type'=>'1','message'=>"Invalid Token, Refresh page",'route'=>$app->getRouteCollector()->getRouteParser(),'reset_email'=>$reset_email,'csrf'=>$csrf]);
    }


    $reset_otp = $data["reset_otp"];
    $user_id=$user["user_id"];

    $emailData = User::fetchEmailOTP($user_id,"1");
    if($emailData){
        if($emailData["code"]==$reset_otp){
            if(strtotime($emailData["expire_on"])>=time()){
                $link=Functions::generateRandomString(50);
                $link_expire_on = date("Y-m-d H:i:s", strtotime("+15 minutes", time()));
                if(User::verifyResetOTP($user_id,$emailData["otp_id"],$link,$link_expire_on)){    
                    
                    return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('resetPassLink',array("link"=>$link)))->withStatus(302);
                    
                }
            }else{
                $message="OTP Expired";
            }
        }else{
            $message="Incorrect OTP";
        }
        
    }else{
        $message="Please follow process";
    }

    return $renderer->render($response, "forgot.php",['type'=>'1','message'=>$message,'route'=>$app->getRouteCollector()->getRouteParser(),'reset_email'=>$reset_email,'csrf'=>$csrf]);

})->setName('verifyResetOTP');


$app->get('/reset/{link}', function (Request $request, Response $response, $args) use ($app,$csrf) {    
    $link = $args['link'];

    $db = Database::getInstance();
    User::constructStatic($db);

    $userLink = User::fetchResetLink($link);
    
    $renderer = new PhpRenderer('pages');
    
    if (!$userLink) {
        return $response->withStatus(404)->write('Invalid Link');
    }
    if(strtotime($userLink["link_expire_on"])<time()){
        return $response->withStatus(404)->write('Link Expired');
    }

    if($userLink["isLinkUsed"]==1){
        return $response->withStatus(404)->write('Link Already Used');
    }
    $user = User::fetchUser($userLink["user_id"]); 

    return $renderer->render($response, 'newpass.php', ['user' => $user,'route'=>$app->getRouteCollector()->getRouteParser(),'link'=>$link,'csrf'=>$csrf]);
    
})->setName('resetPassLink');


$app->post('/reset/{link}', function (Request $request, Response $response, $args) use ($app,$csrf) {    
    $link = $args['link'];

    $db = Database::getInstance();
    User::constructStatic($db);

    $userLink = User::fetchResetLink($link);
    
    $renderer = new PhpRenderer('pages');
    
    if (!$userLink) {
        return $response->withStatus(404)->write('Invalid Link');
    }
    if(strtotime($userLink["link_expire_on"])<time()){
        return $response->withStatus(404)->write('Link Expired');
    }

    if($userLink["isLinkUsed"]==1){
        return $response->withStatus(404)->write('Link Already Used');
    }

    $user = User::fetchUser($userLink["user_id"]); 
    
    if (!$csrf->validate('newpass-form')){
        return $renderer->render($response, 'newpass.php', ['user' => $user,'status'=>'0','route'=>$app->getRouteCollector()->getRouteParser(),'link'=>$link,'csrf'=>$csrf]);
    }


    
    $data = $request->getParsedBody();

    
    if(!isset($data["new_pass"]) || trim($data["new_pass"])==""){
        return $renderer->render($response, 'newpass.php', ['user' => $user,'status'=>'0','route'=>$app->getRouteCollector()->getRouteParser(),'link'=>$link,'csrf'=>$csrf]);
    }

    $new_pass = $data["new_pass"];

    $new_pass=password_hash($new_pass, PASSWORD_DEFAULT);

    if(User::resetPass($userLink["user_id"],$new_pass)){
        User::markLinkDone($link);
        return $renderer->render($response, 'newpass.php', ['user' => $user,'status'=>'1','route'=>$app->getRouteCollector()->getRouteParser(),'link'=>$link,'csrf'=>$csrf]);
    }
    return $renderer->render($response, 'newpass.php', ['user' => $user,'status'=>'0','route'=>$app->getRouteCollector()->getRouteParser(),'link'=>$link,'csrf'=>$csrf]);

    
})->setName('updatePass');

$app->get('/resendOTP', function (Request $request, Response $response) use ($app) {    
    if (isset($_SESSION['isLoggedIn'])) {
        $db = Database::getInstance();
        User::constructStatic($db);

        $userId=$_SESSION["userId"];

        $userOTPCount=User::fetchOTPCount($userId,"0");
        if($userOTPCount){
            if($userOTPCount["otp_count"]>10){
                $_SESSION["message"]="Maximum 10 OTP request has been used";
                $response = new Psr7Response();
                return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('verifyEmailForm'))->withStatus(302);
            }
        }

        $code = rand(10000,99999);
        $expire_on = date("Y-m-d H:i:s", strtotime("+15 minutes", time()));

        if(User::addEmailOTP($userId,$code,$expire_on,"0")){
            if($userOTPCount){
                $_SESSION["message"]="OTP Resend, ".(10-$userOTPCount["otp_count"])." OTP request remaining";
            }else{
                $_SESSION["message"]="OTP Resend";
            }

            $response = new Psr7Response();
            return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('verifyEmailForm'))->withStatus(302);
        }
    }
    $response = new Psr7Response();
    return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
})->setName('resendEmailOTP');



$app->get('/verifyEmail', function (Request $request, Response $response) use ($app,$csrf) {    
        
    if (isset($_SESSION['isLoggedIn'])) {
        if($_SESSION["isEmailVerified"]==1){
            $response = new Psr7Response();
            return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
        }
        
        return $this->get('renderer')->render($response, 'verifyEmail.php',['route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }
    $response = new Psr7Response();
    return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
})->setName('verifyEmailForm');

$app->post('/verifyEmail', function (Request $request, Response $response) use ($app,$csrf) {  
    if (!isset($_SESSION['isLoggedIn'])) {
        $response = new Psr7Response();
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }
    if($_SESSION["isEmailVerified"]==1){
        $response = new Psr7Response();
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
    }

    if (!$csrf->validate('verifyemail-form')){
        return $this->get('renderer')->render($response, 'verifyEmail.php', ['message' => "Invalid Token, Refresh page",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    $db = Database::getInstance();
    User::constructStatic($db);
    Functions::constructStatic($db);

    $data = $request->getParsedBody();

    
    if(!isset($data["otp_code"]) || trim($data["otp_code"])==""){
        $message="OTP Required";
        return $this->get('renderer')->render($response, 'verifyEmail.php', ['message' => $message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    $otp_code=$data["otp_code"];
    $user_id=$_SESSION["userId"];

    $emailData = User::fetchEmailOTP($user_id,"0");
    if($emailData){
        if($emailData["code"]==$otp_code){
            if(strtotime($emailData["expire_on"])>=time()){
                if(User::verifyEmail($user_id,$emailData["otp_id"])){
                    $_SESSION["isEmailVerified"]=1;                 
                    $response = new Psr7Response();
                    return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'))->withStatus(302);
                }
            }else{
                $message="OTP Expired";
            }
        }else{
            $message="Incorrect OTP";
        }
        
    }else{
        $message="Please follow process";
    }

    return $this->get('renderer')->render($response, 'verifyEmail.php', ['message' => $message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);

})->setName('verifyEmail');





$app->get('/profile', function (Request $request, Response $response) use ($app,$csrf) {    
    return $this->get('renderer')->render($response, 'profile.php',['route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
})->setName('profile')->add($authMiddleware);


$app->post('/profile', function (Request $request, Response $response) use ($app,$csrf) {  

    $db = Database::getInstance();
    User::constructStatic($db);
    Functions::constructStatic($db);

    
    if (!$csrf->validate('profiledetails-form')){
        return $this->get('renderer')->render($response, 'profile.php', ['message' => "Invalid Token, Refresh page",'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    $data = $request->getParsedBody();
    $full_name=$data["profile_full_name"];
    $address=$data["profile_address"];

    if(!isset($data["profile_full_name"]) || trim($data["profile_full_name"])=="" || !isset($data["profile_address"]) || trim($data["profile_address"])==""){
        $message="All fields Required";
        return $this->get('renderer')->render($response, 'profile.php', ['message' => $message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }

    if(User::updateUserDetails($_SESSION["userId"],$full_name,$address)){
        $message="Profile updated"; 
        $_SESSION["full_name"]=$full_name;
        $_SESSION["isKYC"]=1;
        $_SESSION["address"]=$address;
    }else{
        $message="Error while submiting data";
    }

    return $this->get('renderer')->render($response, 'profile.php', ['message' => $message,'route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);

})->setName('updateProfile')->add($authMiddleware);;


$app->get('/logout', function (Request $request, Response $response){    
    if(isset($_SESSION['isLoggedIn'])){
        session_unset();
        session_destroy();
    }
    return $response->withStatus(302)->withHeader('Location', pathinfo($request->getUri(), PATHINFO_DIRNAME));
})->setName('logout');

?>