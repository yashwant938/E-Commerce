<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<br>
    
    <section id="lesection">
        <div class="container">
<div class="row">
    <div class="col-md-6">
        <img src="../files/img/property/<?php echo json_decode($propertyDetail["images"])[0]; ?>" alt="Property Photo" class="property-photo" style="width:90%;">
    </div>
    <div class="col-md-6">
        <h2>Location <?php echo htmlspecialchars($propertyDetail["location"], ENT_QUOTES, 'UTF-8'); ?></h2>
        <p class="text-muted">Price: <?php echo htmlspecialchars($propertyDetail["price"], ENT_QUOTES, 'UTF-8'); ?> <?php if($propertyDetail["property_type"]==0){ echo "Per Month"; } ?></p>
        <p class="text-muted">Owner: <?php if($propertyDetail["isSold"]==1 && $propertyDetail["property_type"]==1){ echo htmlspecialchars($propertyDetail["buyer_name"], ENT_QUOTES, 'UTF-8'); }else{ echo htmlspecialchars($propertyDetail["owner_name"], ENT_QUOTES, 'UTF-8'); } ?></p>
        <p class="text-muted">Type: <?php if($propertyDetail["property_type"]==0){ echo "Lease"; }else{ echo "Sell"; } ?></p>
        <?php if($propertyDetail["property_type"]==0){ ?>
        <p class="text-muted">Duration: <?php echo $propertyDetail["lease_months"]; ?> Months</p>
        <?php } ?>
        <h4>Address</h4>
        <p class="text-muted"><?php echo htmlspecialchars($propertyDetail["address"], ENT_QUOTES, 'UTF-8'); ?></p>
        <hr>
        <h4>Description</h4>
        <p><?php echo htmlspecialchars($propertyDetail["description"], ENT_QUOTES, 'UTF-8'); ?></p>




        <?php if(isset($_SESSION["errorMessage"])){ ?> 
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION["errorMessage"]; unset($_SESSION["errorMessage"]); ?>
            </div>        
        <?php } ?>

        <?php if($propertyDetail["isSold"]==1){ ?>
            <?php if($propertyDetail["buyer_id"]==$_SESSION["userId"]){ ?>
                <div class="alert alert-success" role="alert">
                    You <?php if($propertyDetail["property_type"]==0){ echo "leased"; }else{ echo "purchased"; } ?> this property
                </div>
                <a href="<?php echo $route->urlFor('popertyContract',array("uid"=>$propertyDetail["property_uid"])); ?>" class="btn btn-dark btn-block" target="_BLANK">Download Contract</a>
            <?php }elseif($propertyDetail["created_by"]==$_SESSION["userId"]){ ?>
                <div class="alert alert-success" role="alert">
                    Your property has been <?php if($propertyDetail["property_type"]==0){ echo "Leased"; }else{ echo "Sold"; } ?> to <?php echo htmlspecialchars($propertyDetail["buyer_name"], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <a href="<?php echo $route->urlFor('popertyContract',array("uid"=>$propertyDetail["property_uid"])); ?>" class="btn btn-dark btn-block" target="_BLANK">Download Contract</a>
            <?php }else{ ?>
                <div class="alert alert-warning" role="alert">
                    Property already <?php if($propertyDetail["property_type"]==0){ echo "Leased"; }else{ echo "Sold"; } ?> 
                </div>
            <?php } ?>
        <?php }else{ ?>  
            <?php if($propertyDetail["created_by"]!=$_SESSION["userId"]){ ?>
                <?php if($propertyDetail["property_ownership"]!=""){ ?>
                <div class="mt-4">
                <a href="<?php echo "../files/upload/ownership/".$propertyDetail["property_ownership"]; ?>" target="_BLANK">View Ownership</a>
                
            <?php }?>

            </div>
            <div class="mt-4">
                <a href="#" data-bs-toggle="modal" data-bs-target="#buyModal" class="btn btn-dark btn-block">Buy/Rent</a>
            </div>
            <?php }else{ ?>
                <div class="alert alert-warning" role="alert">
                    Status: Pending 
                </div>
                <div class="mt-4">
                <a href="<?php echo $route->urlFor('editPopertyForm',array("uid"=>$propertyDetail["property_uid"])); ?>" class="btn btn-warning btn-block">Edit</a>
                <a href="<?php echo $route->urlFor('removePoperty',array("uid"=>$propertyDetail["property_uid"])); ?>" class="btn btn-danger btn-block">Remove</a>
            </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>

</div>
    </section>
    <div class="modal fade" id="buyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Buy/Rent</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo $route->urlFor('paymentPoperty',array("uid"=>$propertyDetail["property_uid"])); ?>" method="POST">
      <div class="modal-body">
      
        <div style="height: 60vh;max-height: 60vh;overflow-y: scroll;padding: 10px;">
        <h3>Terms and Conditions</h3>
        <p>By accessing and using this website, you agree to be bound by the following terms and conditions:
</p>
<ol>
    <li> Upload of original documents</li>
    <p>All users must upload original documents related to their property listing or application. This includes but is not limited to:</p>
    <ul>
        <li>User Identification</li>
        <li>Title deeds</li>
    </ul>
    <li>Service is not liable for seller-buyer disputes. Our service guarantees secure transactions.</li>
    <p>The site is a platform for buyers and sellers to connect and transact. The site is not liable for any disputes between buyers and sellers after a transaction has been completed.</p>
    <li>Legal action for using fake documents</li>
    <p>The site has a zero-tolerance policy for the use of fake documents. If a user is found to have uploaded fake documents, the site may take legal action against the user.</p>
    <li>Other important terms and conditions</li>
    <ul>
        <li>The site reserves the right to modify these terms and conditions at any time with prior notice.</li>
        <li>The site is not responsible for any errors or omissions in the information listed.</li>
    </ul>
</ol>


    </div>
    <hr>
    <input type="checkbox" name="toc" id="toc" required> <label for="toc">I aggree to given terms and condition</label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        


        <button type="submit" class="btn btn-dark">Buy/Rent</button>
      </div>
</form>

    </div>
  </div>
</div>

    