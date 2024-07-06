<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<?php
$db = Database::getInstance();
Property::constructStatic($db);
$properties=Property::fetchAllMyProperty($_SESSION["userId"]);

?>
<br>
    
    <section id="lesection">
        <div class="container">
            <div class="container-holder">
            
            <?php if($properties){ ?>
                <div class="row">
                    <?php foreach($properties as $property){ ?>
                        <div class="col-md-4">
                        <a href="<?php echo $route->urlFor('viewPropertyDetails',array("uid"=>$property["property_uid"])); ?>" style="text-decoration:none;">
                            <div class="card mb-3">
                                <div class="card-img-holder">
                                    <img src="../files/img/property/<?php echo json_decode($property["images"])[0]; ?>">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($property["location"], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($property["price"], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <?php if($property["buyer_id"]==$_SESSION["userId"]){ ?>
                                        <div class="alert alert-success" role="alert">
                           You <?php if($property["property_type"]==0){ echo "leased"; }else{ echo "purchased"; } ?> this property
                        </div>
                    <?php }elseif($property["isSold"]==1){ ?>
                        <div class="alert alert-success" role="alert">
                        Your property has been <?php if($property["property_type"]==0){ echo "Leased"; }else{ echo "Sold"; } ?> to <?php echo htmlspecialchars($property["buyer_name"], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php }else{ ?>
                        <div class="alert alert-warning" role="alert">
                            Status: Pending 
                        </div>
                    <?php } ?>
                                </div>
                            </div>
                    </a>
                        </div>
                        
                    <?php } ?>
                </div>   
            <?php } ?>
            </div>      
    <div style ="position: fixed; bottom: 10px;right:10px;">
    <a href="add" class="btn btn-danger btn-circle btn-xl addpropertybtn"><i class="fa-solid fa-plus fa-2xl"></i></a>
</div>
   
</div>
    </section>