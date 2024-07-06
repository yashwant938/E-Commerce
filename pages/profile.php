<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<br>
    
    <section id="lesection">
        <div class="container">
<h4 class="font-weight-bold py-3 mb-4">
    Profile
</h4>
<div class="card overflow-hidden">
    <div class="row no-gutters row-bordered row-border-light">
        <div class="col-md-3 pt-0">
            <div class="list-group list-group-flush account-settings-links">
                <a class="list-group-item list-group-item-action active" data-bs-toggle="tab"
                    href="#account-general">General</a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="tab"
                    href="#account-identity-proof">Identity Proof</a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="account-general">
                    <div class="card-body">
                    <?php
            if(isset($message)){ ?> 
            <div class="alert alert-secondary" role="alert">
  <?php echo $message; ?>
</div>
        
        <?php }
            ?>
                        <form action="<?php echo $route->urlFor('updateProfile'); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="profile_full_name" value="<?php echo $_SESSION["full_name"]; ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="text" class="form-control mb-1" value="<?php echo $_SESSION["email"]; ?>" required disabled>
                            <!--div class="alert alert-warning mt-3">
                                Your email is not confirmed. Please check your inbox.<br>
                                <a href="javascript:void(0)">Resend confirmation</a>
                            </div-->
                        </div>
        <div class="form-group mb-3">
            <label for="profile_address">Address</label>  
            <textarea class="form-control" type="text" id="profile_address" name="profile_address" required><?php echo $_SESSION["address"]; ?></textarea>
        </div>

        <?=$csrf->input('profiledetails-form', -1, 1);?>

        <div class="text-right mt-3">
    <button type="submit" class="btn btn-danger" style="float: right;">Save Changes</button>
    <br><br>
</div>

        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="account-identity-proof">
                    <div class="card-body pb-2">
                        <div class="form-group">
                      
                        <?php if(isset($_SESSION["message"])){ ?> 
            <div class="alert alert-secondary" role="alert">
                <?php echo $_SESSION["message"]; unset($_SESSION["message"]); ?>
            </div>        
        <?php } ?>

                        <form action="<?php echo $route->urlFor('updateAadhar'); ?>" method="post" enctype="multipart/form-data">
                            <br>
                            <h5>Aadhar</h5>
                            <a href="<?php echo $route->urlFor('home'); ?>files/upload/aadhar/<?php echo $_SESSION["addhar_file_name"]; ?>" target="_BLANK">Aadhar.pdf</a>
                            <br>
                            <div class="mb-3">
        <label for="addhar_file">Addhar (pdf)</label>  
        <input class="form-control" type="file" id="addhar_file" name="addhar_file" accept="application/pdf" required>
        </div>
                            <br>
                            
        <?=$csrf->input('aadharupdate-form', -1, 1);?>
        <button class="btn btn-primary" type="submit">Update Aadhar</button>
    </form>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="account-info">
                    <div class="card-body pb-2">
                    </div>
                    <hr class="border-light m-0">
                </div>
            </div>
        </div>
    </div>
</div>


</div>
    </section>