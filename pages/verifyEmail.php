<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<div class="form-ekyc">
    <form action="<?php echo $route->urlFor('verifyEmail'); ?>" method="post" enctype="multipart/form-data">
        <h1 class="h3 mb-3 fw-normal">OTP Verify</h1>
        <p>OTP send on <?php echo htmlspecialchars($_SESSION["email"], ENT_QUOTES, 'UTF-8'); ?></p>
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
            <label for="kyc_full_name">OTP</label>
            <input type="text" class="form-control" name="otp_code" id="otp_code" value="" required>
        </div>
        <?=$csrf->input('verifyemail-form', -1, 1);?>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Verify</button><br><br>
        <p style="text-align:center;"><a href="<?php echo $route->urlFor('resendEmailOTP'); ?>">Resend OTP</a></p>
    </form>
</div>
