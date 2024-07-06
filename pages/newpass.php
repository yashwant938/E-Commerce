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
    <?php if(isset($status) && $status=='1'){ echo '<div class="alert alert-success" role="alert">
  Password changed sucessfully, now you can login...
</div>'; }else{ ?>
    
    <form action="<?php echo $route->urlFor('updatePass',array("link"=>$link)); ?>" method="post" enctype="multipart/form-data" id="resetpassform">
        <h1 class="h3 mb-3 fw-normal">Reset Password</h1>
        <?php if(isset($status) && $status=='0'){ ?> 
            <div class="alert alert-secondary" role="alert">
                Error while updating, try again
            </div>        
        <?php } ?>
        
        <div class="mb-3">
            <label for="kyc_full_name">New Password</label>
            <input type="password" class="form-control" name="new_pass" id="new_pass" value="" required>
        </div>
        <div class="mb-3">
            <label for="kyc_full_name">Re-enter Password</label>
            <input type="password" class="form-control" name="renew_pass" id="renew_pass" value="" required>
        </div>
        <?=$csrf->input('newpass-form', -1, 1);?>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Update</button>
        
    </form>
    <?php } ?>
</div>


<script src="<?php echo $route->urlFor('home'); ?>files/js/jquery.validate.min.js"></script>
<script>
    $("#resetpassform").validate({
rules: {
    new_pass: {
      required: true,
            minlength: 5
    },
    renew_pass: {
      required: true,
            minlength: 5,
            equalTo: "#new_pass"
        }
  },

  submitHandler: function(form) {
    form.submit();
  }
 });
 </script>

</body>
</html>