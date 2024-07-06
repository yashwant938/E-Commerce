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
<script src="<?php echo $route->urlFor('home'); ?>files/js/main.js"></script>

</head>
<body>
    <?php require_once "files/include/header.php"; ?>


    <?=$content?>  

    <?php require_once "files/include/footer.php"; ?>
            






</body>
</html>