<?php
session_start();
require_once "../api/autoload/init.php";
$msg="";

if(isset($_POST["username"])){

        $username=$_POST["username"];
        $password=$_POST["password"];
        $db = Database::getInstance();
        AdminUser::constructStatic($db);
        Functions::constructStatic($db);
        $user=AdminUser::fetchUserByUsername($username); 
        if($user){    
          $encypted_password = $user["password"];
          if(password_verify($password, $encypted_password)) {
          
            $user_ip = Functions::get_client_ip();
            AdminUser::addUserAuth($user["admin_id"],$user_ip);
            $_SESSION["login"]=true;
            $_SESSION["user_uid"]=$user["admin_id"];
            $_SESSION["full_name"]=$user["full_name"];
            $_SESSION["username"]=$user["username"];
            $_SESSION["is_super_user"]=1;
            

            header('Location: index.php');
            exit();
          }else{
            $msg="Incorrect username or password";
          }
  
        }else{
          $msg="Incorrect username or password";
        }


}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
	<style>
		* {
		    -webkit-box-sizing: border-box;
		    box-sizing: border-box;
		}
		html, body{
		    margin: 0px;
		    padding: 0px;
		    height: 100%;
		}
		.body{
		    height: 100%;
		    width: 100%;
		    margin-top: 0px;
		    font-family: 'Lato', sans-serif;
		}
		.body .layer{
		    width: 100%;
		    height: 100%;
		    display: -webkit-box;
		    display: -webkit-flex;
		    display: -ms-flexbox;
		    display: flex;
		    -webkit-box-align: center;
		    -webkit-align-items: center;
		    -ms-flex-align: center;
		    align-items: center;
		    -webkit-box-pack: center;
		    -webkit-justify-content: center;
		    -ms-flex-pack: center;
		    justify-content: center;
		}
		.error{
			margin-top: 15px;
			margin-bottom: -15px;
			letter-spacing: 2px;

		}
		.form{
		    position: relative;
		    margin: 20px 0px;
		    width: 90%;
		    max-width: 480px;
		    text-align: center;
		    background: #fff;
		    padding: 43px 40px;
		    border-radius: 2px;
		}
		.form .heading{
		    color: #000;
		    font-size: 25px;
		    margin: 0px 0 0px;
		    height: 30px;
		    line-height: 30px;
		    text-align: center;
		}
		.form .signup-form{
		    display: none;
		}
		.form .input{
		    width: 100%;
		    padding: 15px;
		    background: #f8f8f8;
		    border: 1px solid rgba(0, 0, 0, 0.075);
		    margin-bottom: 25px;
		    color: black !important;
		    font-size: 13px;
		    -webkit-transition: all 0.4s;
		    -moz-transition: all 0.4s;
		    -o-transition: all 0.4s;
		    transition: all 0.4s;
		    font-size: 17px;
		    letter-spacing: 2px;
		}
		.form .input:focus{
		    color:white;
		    outline:none;
		    border:1px solid #8BC3A3
		}
		.form .submit{
		    width: 100%;
		    padding: 0 20px;
		    line-height: 50px;
		    background: #1ebbf0;
		    color: #fff;
		    text-transform: uppercase;
		    letter-spacing: 2px;
		    font-weight: 700;
		    text-align: center;
		    border: none;
		    cursor: pointer;
		    font-size: 17px;
		}
		.form .submit:hover{
		    background-color: #1a9dc9;
		}
		.form .input-box{
		    margin-top: 40px;
		}
		.form .togglebtn {
		    color: blue;
		    background-color: transparent;
		    border: none;
		    outline: none;
		    cursor: pointer;
		    font-weight: 600;
		}
		::-webkit-input-placeholder{
		    color:black;
		    font-size:17px
		}
		:-moz-placeholder{
		    color:black;
		    font-size:17px
		}
		::-moz-placeholder{
		    color:black;
		    font-size:17px
		}
		:-ms-input-placeholder{
		    color:black;
		    font-size:17px
		}
		@media only screen and (max-width: 500px){
			.form{
				width: 100%;
				padding: 20px;
			}
		}
	</style>
</head>
<body>
	<div class="body">
        <div class="layer">
            <div class="form">
                <div class="login-form">
                    <div class="heading">Login to your <b>Account</b></div>
                    <div class="error"><?php echo $msg;?></div>
                    <div class="input-box">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES, "utf-8");?>" method="post">
                            <input type="text" class="input" placeholder="Your Username..." required name="username">
                            <input type="password" class="input" placeholder="Your password..." required name="password">
                            <input type="submit" class="submit" value="login">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>
</html>