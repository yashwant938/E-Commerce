<?php
if(!defined('app')) {
   die('Direct access not permitted');
}
?>
<div class="form-ekyc">
    
    <form action="<?php echo $route->urlFor('checkProperty'); ?>" method="post" enctype="multipart/form-data">
        <h1 class="h3 mb-3 fw-normal">Check Poperty Document</h1>
        <?php
            if(isset($message)){ ?> 
            <div class="alert alert-secondary" role="alert">
  <?php echo $message; ?>
</div>
        
        <?php }
            ?>
        <div class="mb-3">
        <label for="property_file">Property document (pdf)</label>  
        <input class="form-control" type="file" id="property_file" name="property_file" accept="application/pdf" required>
        <?=$csrf->input('checkcontract-form', -1, 1);?>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Check</button>
    </form>
</div>
