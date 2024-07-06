<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<div class="container">
<?php if($properties){ ?>
    <div class="row">
        <?php foreach($properties as $property){ ?>
                <div class="col-md-4">
            <a href="<?php echo $route->urlFor('viewPropertyDetails',array("uid"=>$property["property_uid"])); ?>" style="text-decoration:none;">
                    <div class="card mb-3">
                        <div class="card-img-holder">
                            <img src="files/img/property/<?php echo json_decode($property["images"])[0]; ?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($property["location"], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($property["price"], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>
            </a>
                </div>
            
        <?php } ?>
    </div>   
<?php } ?>
</div>