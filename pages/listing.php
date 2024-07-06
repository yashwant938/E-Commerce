
<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<?php
$db = Database::getInstance();
Property::constructStatic($db);
$locations=Property::fetchAllLocations();
$amenitys=Property::fetchAllAmenity();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">

<div class="properties-filter-bar" id="properties-filter-bar">
        <div class="properties-filter-nav container">
            <div class="filter-nav-lhs">
            Properties 
            </div>
            <div class="filter-nav-rhs">
                <input type="hidden" id="orderby" value="0">
                <nav class="filter-nav">
                    <ul class="pful">
                         <li class="fddli">
                            <span class="filtnav-padd">
                                <div class="search-holder">
                                    <div class="search-box">
                                        <input type="text" name="filtter_search" class="form-control" placeholder="Search Address...">
                                    </div>
                                </div>
                            </span>
                            
                        </li>
                        <li class="fddli">
                            <span class="filtnav-padd">
                                <div class="budget-holder">
                                    <div class="budget-box">
                                        <input type="number" name="filtter_budget_min" class="form-control" placeholder="Min">
                                    </div>
                                    <div class="budget-box">
                                        <input type="number" name="filtter_budget_max" class="form-control" placeholder="Max">
                                    </div>
                                </div>
                            </span>
                            
                        </li>
                        <li class="fddli">
                            <span class="filtnav-padd">
                                <span class="filter-selected-text">Filters</span>
                                <span class="filter-dd-icon"><svg class="fdd-icon" width="8" height="8" viewBox="0 0 15 15"><path d="M2.1,3.2l5.4,5.4l5.4-5.4L15,4.3l-7.5,7.5L0,4.3L2.1,3.2z"></path></svg></span>
                            </span>
                            <div class="fdd-menu property-filters">
                                <div class="property-filters-holder">
                                    
                                <div class="filter-box type-filter-box">
                                        <div class="filter-headline">Type</div>
                                        
                                        <ul class="filter-ul">
                                                <li class="fli"><input type="checkbox" name="filtter_type" class="filter-checkbox" id="type_0" value="0"> <label for="type_0">Lease</label></li>
                                                <li class="fli"><input type="checkbox" name="filtter_type" class="filter-checkbox" id="type_1" value="1"> <label for="type_1">Buy</label></li>
                                            </ul>
                                    </div>
                                    <div class="filter-box location-filter-box">
                                        <div class="filter-headline">Location</div>
                                        
                                        <ul class="filter-ul">
                                                <?php foreach($locations as $location) { ?>
                                                <li class="fli"><input type="checkbox" name="filtter_location" class="filter-checkbox" id="filtter_location_<?php echo $location["location_id"]; ?>" value="<?php echo $location["location_id"]; ?>"> <label for="filtter_location_<?php echo $location["location_id"]; ?>"><?php echo $location["location_name"]; ?></label></li>
                                                <?php } ?>
                                            </ul>
                                    </div>
                                    <div class="filter-box amenity-filter-box">
                                        <div class="filter-headline">Amenities</div>
                                        
                                        <ul class="filter-ul">
                                                <?php foreach($amenitys as $amenity) { ?>
                                                <li class="fli"><input type="checkbox" name="filtter_amenity" class="filter-checkbox" id="filtter_amenity_<?php echo $amenity["amenity_id"]; ?>" value="<?php echo $amenity["amenity_id"]; ?>"> <label for="filtter_amenity_<?php echo $amenity["amenity_id"]; ?>"><?php echo $amenity["amenity_name"]; ?></label></li>
                                                <?php } ?>
                                            </ul>
                                    </div>
                                </div>
                               
                            </div>
                            
                        </li>
                        <li class="fddli last-pfdd"> 
                            <span class="filtnav-padd">
                                <span class="filter-selected-text" id="sortbytext">Date of Availability</span>
                                <span class="filter-dd-icon"><svg class="fdd-icon" width="8" height="8" viewBox="0 0 15 15"><path d="M2.1,3.2l5.4,5.4l5.4-5.4L15,4.3l-7.5,7.5L0,4.3L2.1,3.2z"></path></svg></span>
                            </span>
                            <ul class="fdd-menu last-fdd date-menu">
                                <li class="fli"><input type="text" class="form-control datepicker" id="filter-avi-date"></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div><br><br>
    <section id="listingsection">
            


    </section>


    <script>
        function setsortby(sortbytext,sortbyval){
            $("#sortbytext").text(sortbytext);
            $("#orderby").val(sortbyval);
            property_render();
        }
        $("input[name=filtter_budget_min]").on('input',function(e){
            property_render();
        });
        $("input[name=filtter_budget_max]").on('input',function(e){
            property_render();
        });
        $("input[name=filtter_search]").on('input',function(e){
            property_render();
        });
        $("input:checkbox[name=filtter_type]").click(function(){
            property_render();
        });
        $("input:checkbox[name=filtter_location]").click(function(){
            property_render();
        });
        $("input:checkbox[name=filtter_amenity]").click(function(){
            property_render();
        });
        function property_render(){
            var orderby=$("#orderby").val();
            var filtter_type = [];
            $("input:checkbox[name=filtter_type]:checked").each(function(){
                filtter_type.push($(this).val());
            });
            var filtter_location = [];
            $("input:checkbox[name=filtter_location]:checked").each(function(){
                filtter_location.push($(this).val());
            });
            var filtter_amenity = [];
            $("input:checkbox[name=filtter_amenity]:checked").each(function(){
                filtter_amenity.push($(this).val());
            });
            var filtter_type_str = encodeURIComponent(JSON.stringify(filtter_type));
            var filtter_location_str = encodeURIComponent(JSON.stringify(filtter_location));
            var filtter_amenity_str = encodeURIComponent(JSON.stringify(filtter_amenity));

            var min_budget = $("input[name=filtter_budget_min]").val();
            var max_budget = $("input[name=filtter_budget_max]").val();
            var avi_date = $("#filter-avi-date").val();
            var search = $("input[name=filtter_search]").val();
            

            $.get("propertyList?ft="+filtter_type_str+"&fl="+filtter_location_str+"&fa="+filtter_amenity_str+"&min="+min_budget+"&max="+max_budget+"&avi="+avi_date+"&s="+search, function(data, status){
                $("#listingsection").html(data);
            });

            
        }
        property_render();
    </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true
}).change(function(){
  $(".date-menu").css("display","none");
  property_render();
});
$(".datepicker").click(function(){
  $(".date-menu").css("display","block");
});
    </script>
