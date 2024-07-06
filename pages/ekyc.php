<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<div class="form-ekyc">
    <form action="<?php echo $route->urlFor('addEkyc'); ?>" method="post" enctype="multipart/form-data">
        <h1 class="h3 mb-3 fw-normal">EKYC</h1>
        <?php
            if(isset($message)){ ?> 
            <div class="alert alert-secondary" role="alert">
  <?php echo $message; ?>
</div>
        
        <?php }
            ?>
        <div class="mb-3">
        <label for="kyc_full_name">Full Name</label>
        <input type="text" class="form-control" name="kyc_full_name" id="kyc_full_name" value="<?php echo htmlspecialchars($_SESSION["full_name"], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="mb-3">
        <label for="addhar_file">Addhar (pdf)</label>  
        <input class="form-control" type="file" id="addhar_file" name="addhar_file" accept="application/pdf" required>
        </div>
        
        <div class="mb-3">
        <label for="kyc_full_name">KYC Email</label>
        <input type="text" class="form-control" name="kyc_email" id="kyc_email" value="" required>
        </div>
        
        <div class="mb-3">
        <label for="kyc_full_name">KYC Password</label>
        <input type="password" class="form-control" name="kyc_password" id="kyc_password" value="" required>
        </div>

        <div class="mb-3">
            <label for="ekyc_address">Address</label>  
            <textarea class="form-control" type="text" id="ekyc_address" name="ekyc_address" required></textarea>
        </div>
        <?=$csrf->input('ekyc-form', -1, 1);?>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Submit KYC</button>
    </form>
</div>
