<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyb3rC1ph3r signUp</title>
    <link rel="stylesheet" href="files/css/signUp.css">
    <style>
    .errormsg{
        color:red;
        text-align: center;
        width:100%;
        height:40px;
        line-height: 40px;
        font-size:16px;
    }
  </style>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="registration grid-container">
            <span class="pointed-span">SIGNUP</span>

       <div class="row">
            <div class="ok">
                <form id="signup-form" method="POST" action="<?php echo $route->urlFor('signup'); ?>">
                <?php if(isset($_SESSION["error_message"])){ ?> 
            <div class="errormsg">
                <?php echo $_SESSION["error_message"]; unset($_SESSION["error_message"]); ?>
            </div>        
        <?php } ?>
                    <div class="input">
                    
                        <input type="text" id="full_name" name="full_name" placeholder="Full Name">
                        <input type="text" id="email" name="email" placeholder="Email">
                        <input type="password" id="password" name="password" placeholder="Password">
                        <input type="password" id="repassword" name="repassword" name="" placeholder="Re-enter password"><br>
                    <div class="g-recaptcha" data-sitekey="<?php echo Config::CAPTCHA_SITE; ?>"></div>
                        <?=$csrf->input('signup-form', -1, 1);?>
                    <button class="signup-button">Signup</button>  
                    </div>  
                </form>     
            </div>
       </div>

    </div>
    
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="files/js/jquery.validate.min.js"></script>
<script>
    $("#signup-form").validate({
        rules: {
            
            full_name: {
      required: true
    },
    password: {
      required: true,
            minlength: 5
    },
    email: {
      required: true,
      email: true
    },
    repassword: {
      required: true,
            minlength: 5,
            equalTo: "#password"
        }
  },

  submitHandler: function(form) {

    form.submit();

  }
 });
 </script>
</body>
</html>

