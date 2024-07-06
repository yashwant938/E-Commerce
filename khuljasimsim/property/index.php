<?php
require_once "../../files/include/auth.php";
require_once "../../api/autoload/init.php";

define('app', TRUE);
$db = Database::getInstance();
Property::constructStatic($db);
Functions::constructStatic($db);
$properties = Property::fetchAllAdminProperty();

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../../files/css/materialize.min.css">
    <link rel="stylesheet" href="../../files/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="../../files/css/dashboard.css">
    <style>
        select{
            display: inherit;
        }   
    </style>
	<title>Dashboard</title>
</head>
<body>
	<div class="body">
        <?php require_once "../../files/include/nav.php";?>
        <div class="main-body">
            <header>
                <div class="left">
                    <div class="left-holder">
                        <div class="menu-btn sidenav-trigger" data-target="slide-out"><i class="fas fa-bars"></i></div>
                        <div class="page-name">Property</div>
                    </div>
                </div>
                <div class="right">
                    <div class="user-holder">
                        <div class="user"><div class="dp circle">
                            <?php
                                echo '<img src="">';
                            ?>
                        </div>
                        <div class="uname">Admin</div>
                        <div class="da"><i class="fas fa-angle-down"></i></div></div>
                        <div class="user-opt">
                            <ul>
                                <a href="../user/logout.php">
                                    <li><i class="fas fa-power-off"></i> Logout</li>
                                </a>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <div class="container" style="overflow-y: scroll;">
                <div class="holder">
                    <div class="grid">
                        <div class="contain-box mt30">

                        <table class="table table-striped" id="amazonusertable">
        <thead>
            <tr>
            <th class="text-center">
                #
            </th>
            <th>Action</th>
            <th>Created By</th>
            <th>Created On</th>
            <th>Modified By</th>
            <th>Modified On</th>
            <th>Location</th>
            <th>Property Size</th> 
            <th>Price</th> 
            <th>Property Type</th> 
            <th>Description</th>  
            <th>Images</th>  
            <th>Address</th>  
            <th>Is Sold</th>  
            <th>Buyer</th>  
            <th>Available From</th> 
            <th>Buy Date</th> 
            <th>Property Ownership</th> 
            <th>Lease Months</th> 
            </tr>
        </thead>
        <tbody>
            <?php if($properties){ $count=1; foreach ($properties as $property) { ?>
            <tr>
                <td class="text-center"><?php echo $count; $count++; ?></td>
                <td>
                <?php if($property['isActive']==1){ ?>
                    <a class='waves-effect waves-light btn-small delete' href="remove.php?pid=<?php echo $property['property_id']; ?>">Remove</a>
                    <?php }else{ ?>
                    <a class='waves-effect waves-light btn-small edit' href="remove.php?pid=<?php echo $property['property_id']; ?>">Active</a>
                    <?php } ?>
                </td> 

                <td><?php echo Functions::filterInput($property['owner_name']); ?></td>
                <td><?php echo Functions::filterInput($property['created_on']); ?></td>
                <td><?php echo Functions::filterInput($property['modified_name']); ?></td>
                <td><?php echo Functions::filterInput($property['modified_on']); ?></td>
                <td><?php echo Functions::filterInput($property['location']); ?></td>
                <td><?php echo Functions::filterInput($property['property_size']); ?></td>
                <td><?php echo Functions::filterInput($property['price']); ?></td>
                <td><?php echo Functions::filterInput($property['property_type']); ?></td>
                <td><?php echo Functions::filterInput($property['description']); ?></td>
                <td><?php echo Functions::filterInput($property['images']); ?></td>
                <td><?php echo Functions::filterInput($property['address']); ?></td>
                <td><?php echo Functions::filterInput($property['isSold']); ?></td>
                <td><?php echo Functions::filterInput($property['buyer_name']); ?></td>
                <td><?php echo Functions::filterInput($property['available_from']); ?></td>
                <td><?php echo Functions::filterInput($property['buy_date']); ?></td>
                <td><?php echo Functions::filterInput($property['property_ownership']); ?></td>
                <td><?php echo Functions::filterInput($property['lease_months']); ?></td>
                                   
            </tr>
            <?php } } ?>
        </tbody>
    </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="blur"></div>
    
    <script src="../../files/js/jquery.js"></script>
    <script src="../../files/js/materialize.min.js"></script>
    <script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="../../files/js/dashboard.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.modal').modal();
          });
    </script>
</body>
</html>
