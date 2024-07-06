
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

    <form action="<?php echo $route->urlFor('addProperty'); ?>" method="post" id="addpropertyForm" enctype="multipart/form-data">
        <h1 class="text-center">Property details</h1>

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
                <option value="<?php echo $location["location_id"]; ?>"><?php echo $location["location_name"]; ?></option>
            <?php } } ?>
        </select><br>

                <label for="plotSize">Property size (sq m):</label>
                <input class="form-control" type="number" id="plotSize" name="plotSize"><br>
            
                <label for="price">Price (Rs) (Is case of Lease price will be per month):</label>
                <input class="form-control" type="text" id="price" name="price"><br>

                <label for="plotType">Property type:</label>
                <select name="property_type" id="property_type" class="form-control">
                    <option value="0">Lease</option>
                    <option value="1">Sell</option>
                </select>

                <br>
                <div class="lease-month-inp">
                <label for="price">Lease Duration (months):</label>
                <input class="form-control" type="number" id="lease_months" name="lease_months">
                <br>
                </div>
                <label for="plotType">Amenity</label>
                <select class="select amenity form-control" name="amenity[]" id="amenity" multiple data-width="100%" required>
                    <?php foreach($amenitys as $amenity){ ?>
                    <option value="<?php echo $amenity["amenity_id"]; ?>"><?php echo $amenity["amenity_name"]; ?></option>
                    <?php } ?>
</select>
                
<br><br>
                <div class="mb-3">
  <label for="propert_images" class="form-label">Property Image</label>
  <input class="form-control" type="file" id="propert_images" name="propert_images">
</div>
<div class="mb-3">
  <label for="property_ownership" class="form-label">Ownership Proof</label>
  <input class="form-control" type="file" id="property_ownership" name="property_ownership">
</div>



<div class="mb-3">
  <label for="propert_available_from" class="form-label">Available From</label>
  <input class="datepicker form-control" id="propert_available_from" name="propert_available_from" placeholder="Select date...">
</div>

                <div>
                <label for="address">Address:</label>
                <textarea class="form-control" type="text" id="address" name="address"></textarea>
<br>
                <label for="description">Description:</label>
                <textarea class="form-control" type="text" id="description" name="description"></textarea>
<br>
<?=$csrf->input('addlisting-form', -1, 1);?>

                    <button type="submit" class="btn btn-primary" id="btn btn-next ">Add property</button>
                </div>
            </div>
        
    </form>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="/files/js/bootstrap-select.js"></script>
<script src="/files/js/jquery.validate.min.js"></script>


    <script>
       $.validator.addMethod("imageOnly", function (value, element) {
            return this.optional(element) || /^image\//.test(element.files[0].type);
        }, "Please select a valid image file.");
        $.validator.addMethod("pdfOnly", function (value, element) {
            return this.optional(element) || element.files[0].name.toLowerCase().endsWith(".pdf");
        }, "Please select a valid PDF file.");
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
            propert_images: {
                required: true,
                imageOnly: true
            },
            property_ownership: {
                required: true,
                pdfOnly: true
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
 $('#property_type').on('change', function() {
  if($("#property_type").val()=="0"){
    $(".lease-month-inp").css("display","block");
  }else{
    $(".lease-month-inp").css("display","none");
  }
});
       
       $('.datepicker').datepicker({
    format: 'dd-mm-yyyy'
});
        $(function () {
    $('.amenity').selectpicker();
});
    </script>

</div>
    </section>
