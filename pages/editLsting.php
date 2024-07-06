
<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<?php
$db = Database::getInstance();
Property::constructStatic($db);
$amenitys = Property::fetchAllAmenity();
$locations = Property::fetchAllLocations();

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
    <br>
    
    <section id="lesection">
        <div class="container">

    <form action="<?php echo $route->urlFor('editPoperty',array("uid"=>$propertyDetail["property_uid"])); ?>" method="post" action="" enctype="multipart/form-data">
        <a href="<?php echo $route->urlFor('viewPropertyDetails',array("uid"=>$propertyDetail["property_uid"])); ?>">< Go Back</a>
        <h1 class="text-center">Edit Property details</h1>

        <br><br>
        <div class="form-step form-step-active">
            <?php
            if(isset($message)){ ?> 
            <div class="alert alert-secondary" role="alert">
  <?php echo $message; ?>
</div>
        
        <?php }
            ?>
            <br>
        
        <label for="location">Location:</label>
        <select name="location" id="location" class="form-control">
            <?php if($locations){ foreach($locations as $location){ ?>
                <option value="<?php echo htmlspecialchars($location["location_id"], ENT_QUOTES, 'UTF-8'); ?>" <?php if($location["location_id"]==$propertyDetail["location_id"]){ echo " selected"; } ?>><?php echo $location["location_name"]; ?></option>
            <?php } } ?>
        </select><br>

                <label for="plotSize">Property size (sq m):</label>
                <input class="form-control" type="number" id="plotSize" name="plotSize" value="<?php echo htmlspecialchars($propertyDetail["property_size"], ENT_QUOTES, 'UTF-8'); ?>"><br>
            
                <label for="price">Price (Rs):</label>
                <input class="form-control" type="number" id="price" name="price" value="<?php echo htmlspecialchars($propertyDetail["price"], ENT_QUOTES, 'UTF-8'); ?>"><br>

                <label for="plotType">Property type:</label>
                <select name="property_type" id="property_type" class="form-control">
                    <option value="0" <?php if($propertyDetail["property_type"]==0){ echo " selected"; } ?>>Lease</option>
                    <option value="1" <?php if($propertyDetail["property_type"]==1){ echo " selected"; } ?>>Sell</option>
                </select>

                <br>
                <div class="lease-month-inp">
                <label for="price">Lease Duration (months):</label>
                <input class="form-control" type="number" id="lease_months" name="lease_months" value="<?php echo htmlspecialchars($propertyDetail["lease_months"], ENT_QUOTES, 'UTF-8'); ?>">
                <br>
                </div>

                <label for="plotType">Amenity</label>
                <select class="select amenity form-control" id="amenity" name="amenity[]" multiple data-width="100%" required>
                    <?php $amenity_arr=json_decode($propertyDetail["amenity"]); foreach($amenitys as $amenity){ ?>
                    <option value="<?php echo $amenity["amenity_id"]; ?>" <?php if(in_array($amenity["amenity_id"],$amenity_arr)){ echo " selected"; } ?>><?php echo $amenity["amenity_name"]; ?></option>
                    <?php } ?>
</select>
                
<br><br>
                
<div class="mb-3">
  <label for="propert_available_from" class="form-label">Available From</label>
  <input class="datepicker form-control" id="propert_available_from" name="propert_available_from" placeholder="Select date..." value="<?php echo htmlspecialchars(Functions::date_disp_format($propertyDetail["available_from"]), ENT_QUOTES, 'UTF-8'); ?>">
</div>

                <div>
                <label for="address">Address:</label>
                <textarea class="form-control" type="text" id="address" name="address"><?php echo htmlspecialchars($propertyDetail["address"], ENT_QUOTES, 'UTF-8'); ?></textarea>
<br>
                <label for="description">Description:</label>
                <textarea class="form-control" type="text" id="description" name="description"><?php echo htmlspecialchars($propertyDetail["description"], ENT_QUOTES, 'UTF-8'); ?></textarea>
<br>
<?=$csrf->input('editlisting-form', -1, 1);?>
                    <button type="submit" class="btn btn-primary" id="btn btn-next ">Update property</button>
                </div>
            </div>


    </form>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="/files/js/bootstrap-select.js"></script>
<script src="/files/js/jquery.validate.min.js"></script>
    <script>
        $('.datepicker').datepicker({
    format: 'dd-mm-yyyy'
});
        $(function () {
    $('.amenity').selectpicker();
});
if($("#property_type").val()=="0"){
    $(".lease-month-inp").css("display","block");
  }else{
    $(".lease-month-inp").css("display","none");
  }
$('#property_type').on('change', function() {
  if($("#property_type").val()=="0"){
    $(".lease-month-inp").css("display","block");
  }else{
    $(".lease-month-inp").css("display","none");
  }
});

        $.validator.addMethod("maxDecimalValue", function (value, element) {
            return this.optional(element) || (parseFloat(value) <= 70000);
        }, "Please enter amount not greater than 70000.");



$("#addpropertyForm").validate({
        rules: {      
            
            location: {
                required: true
            },
            plotSize: {
                required: true,
                digits: true,
            },
            price: {
                required: true,
                number: true,
                maxDecimalValue: true,
            },
            lease_months: {
                required: true,
                digits: true,
                range: [1, 60]
            },
            amenity: {
                required: true,
            },
            propert_available_from: {
                required: true,
            },
            address: {
                required: true,
            },
            description: {
                required: true,
            },
           
        },
  submitHandler: function(form) {

    form.submit();

  }
 });

    </script>

</div>
    </section>
