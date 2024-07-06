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
<style>
  .payment_box{
    display: none;
    width:100%;
  }
  .ekyc_box{
    width:100%;
  }
  .ekyc_box button{
    width:100%;
  }
  .razorpay-payment-button{
    width:100%;
  }
</style>
</head>
<body>

<br>
    <section id="lesection">
        <div class="container">


        <main>
    <div class="py-5 text-center">
      <h2>Property Payment</h2>
    </div>

    <div class="row g-5">
      
      <div class="col-md-5 col-lg-4 order-md-last">
     
      
      <div class="ekyc_box">
        <div class="error-box"></div>
          <div class="mb-3">
          <label for="kyc_full_name">KYC Email</label>
          <input type="text" class="form-control" id="kyc_email" value="" required>
          </div>
          
          <div class="mb-3">
          <label for="kyc_full_name">KYC Password</label>
          <input type="password" class="form-control" id="kyc_password" value="" required>
          </div>
      </div>
      
      <div class="payment_box">
      <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-primary">Property</span>
        </h4>
        <ul class="list-group mb-3">
          <li class="list-group-item d-flex justify-content-between lh-sm">
            <div>
              <h6 class="my-0"><?php echo htmlspecialchars($propertyDetail["location"], ENT_QUOTES, 'UTF-8'); ?></h6>
              <small class="text-muted"><?php echo htmlspecialchars($propertyDetail["address"], ENT_QUOTES, 'UTF-8'); ?></small>
            </div>
            <span class="text-muted"><?php echo htmlspecialchars($propertyDetail["price"], ENT_QUOTES, 'UTF-8'); ?> Rs</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span>Total (INR)</span>
            <strong><?php echo htmlspecialchars($propertyDetail["price"], ENT_QUOTES, 'UTF-8'); ?> Rs</strong>
          </li>
        </ul>
</div>


        <div class="card p-2">
        
        <div class="payment_box">
        <form action="<?php echo $route->urlFor('buyPoperty',array("uid"=>$propertyDetail["property_uid"])); ?>" method="POST">
  <script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="<?php echo $payment_json['key']?>"
    data-amount="<?php echo htmlspecialchars($payment_json['amount'], ENT_QUOTES, 'UTF-8'); ?>"
    data-currency="INR"
    data-name="<?php echo $payment_json['name']?>"
    data-image="<?php echo $payment_json['image']?>"
    data-description="<?php echo $payment_json['description']?>"
    data-prefill.name="<?php echo htmlspecialchars($payment_json['prefill']['name'], ENT_QUOTES, 'UTF-8'); ?>"
    data-prefill.email="<?php echo htmlspecialchars($payment_json['prefill']['email'], ENT_QUOTES, 'UTF-8'); ?>"
    data-prefill.contact="<?php echo htmlspecialchars($payment_json['prefill']['contact'], ENT_QUOTES, 'UTF-8'); ?>"
    data-order_id="<?php echo $payment_json['order_id']?>"
  >
  </script>
  <input type="hidden" name="kyc_email" id="recheck_kyc_email" required>
  <input type="hidden" name="kyc_password" id="recheck_kyc_password" required>
  <?=$csrf->input('submitpayment-form', 60*5, 1);?>

</form>
        </div>

        
        <div class="ekyc_box">
        <button type="button" class="btn btn-secondary" onClick="verifyKyc();">Verify</button>
        </div>



</div>


      </div>


      <div class="col-md-3 col-lg-4">
        <h4 class="mb-3">Buyer</h4>
        <form>
          <div class="row g-3">
            <div class="col-12">
              <label for="firstName" class="form-label">Name</label>
              <input type="text" class="form-control" disabled value="<?php echo htmlspecialchars($_SESSION["full_name"], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>


            <div class="col-12">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" disabled value="<?php echo htmlspecialchars($_SESSION["email"], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="col-12">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="form-control" disabled value="<?php echo htmlspecialchars($_SESSION["address"], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            
          </div>

        </form>
      </div>
      <div class="col-md-3 col-lg-4">
        <h4 class="mb-3">Seller</h4>
        <form>
          <div class="row g-3">
          <div class="col-12">
              <label for="firstName" class="form-label">Name</label>
              <input type="text" class="form-control" disabled value="<?php echo htmlspecialchars($propertyDetail["owner_name"], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>


            <div class="col-12">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" disabled value="<?php echo htmlspecialchars($propertyDetail["owner_email"], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="col-12">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="form-control" disabled value="<?php echo htmlspecialchars($propertyDetail["owner_address"], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            
          </div>

        </form>
      </div>

    </div>



</div></section>

<br>
<br>
<br>

<br>
<br>




<script>
  function verifyKyc(){
    var kyc_email = $("#kyc_email").val();
    var kyc_password = $("#kyc_password").val();
    var token = "<?php echo $csrf->string('paymentcheckkyc-form', -1, 1); ?>";
    $.post("<?php echo $route->urlFor('verifyEkyc'); ?>",{
      kyc_email: kyc_email,
      kyc_password: kyc_password,
      token: token
    },
    function(data, status){
      if(data.status==200){
        $(".ekyc_box").css("display","none");
        $(".payment_box").css("display","block");

        $("#recheck_kyc_email").val(kyc_email);
        $("#recheck_kyc_password").val(kyc_password);

      }else{
        $(".error-box").html("<div class='alert alert-danger' role='alert'>"+data.message+"</div>");
      }
    });

  }
</script>
</body>
</html>