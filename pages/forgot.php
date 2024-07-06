<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyb3rC1ph3r</title>
    <script src="https://kit.fontawesome.com/2d91456f48.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://use.typekit.net/tpl8kgd.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <link rel="stylesheet" href="<?php echo $route->urlFor('home'); ?>files/css/main.css">

    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

</head>
<body>

<div class="form-ekyc">
    <?php if($type==0){ ?>
    <form action="<?php echo $route->urlFor('sendResetOTP'); ?>" method="post" enctype="multipart/form-data">
        <h1 class="h3 mb-3 fw-normal">Reset Password</h1>
        <?php if(isset($message)){ ?> 
            <div class="alert alert-secondary" role="alert">
                <?php echo $message; ?>
            </div>        
        <?php } ?>
        <?php if(isset($_SESSION["message"])){ ?> 
            <div class="alert alert-secondary" role="alert">
                <?php echo $_SESSION["message"]; unset($_SESSION["message"]); ?>
            </div>        
        <?php } ?>
        
        <div class="mb-3">
            <label for="kyc_full_name">Email</label>
            <input type="text" class="form-control" name="reset_email" id="reset_email" value="" required>
        </div>
        <?=$csrf->input('sendotp-form', -1, 1);?>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Send OTP</button>
        
    </form>
    <?php }else{ ?>
    <form action="<?php echo $route->urlFor('verifyResetOTP'); ?>" method="post" enctype="multipart/form-data">
        <h1 class="h3 mb-3 fw-normal">Reset Password</h1>
        <?php if(isset($message)){ ?> 
            <div class="alert alert-secondary" role="alert">
                <?php echo $message; ?>
            </div>        
        <?php } ?>
        <?php if(isset($_SESSION["message"])){ ?> 
            <div class="alert alert-secondary" role="alert">
                <?php echo $_SESSION["message"]; unset($_SESSION["message"]); ?>
            </div>        
        <?php } ?>
        
        <div class="mb-3">
            <label for="kyc_full_name">Email</label>
            <input type="text" class="form-control" name="reset_email" id="reset_email" value="<?php echo htmlspecialchars($reset_email, ENT_QUOTES, 'UTF-8'); ?>" required readonly>
        </div>
        <div class="mb-3">
            <label for="kyc_full_name">OTP</label>
            <input type="text" class="form-control" name="reset_otp" id="reset_otp" value="" required>
        </div>
        <?=$csrf->input('verifyotp-form', -1, 1);?>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Verify OTP</button>
        
    </form>

    <?php } ?>
</div>





</body>
</html>