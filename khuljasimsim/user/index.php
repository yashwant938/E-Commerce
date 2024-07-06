<?php
require_once "../../files/include/auth.php";
require_once "../../api/autoload/init.php";

define('app', TRUE);
$db = Database::getInstance();
User::constructStatic($db);
Functions::constructStatic($db);
$users = User::fetchAllUserWithAuth();

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
                        <div class="page-name">User</div>
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
            <div class="container">
                <div class="holder">
                    <div class="grid">
                        <div class="contain-box mt30">

                        <table class="table table-striped" id="amazonusertable">
        <thead>
            <tr>
            <th class="text-center">
                #
            </th>
            <th>Full name</th>
            <th>Email</th> 
            <th>Contracts</th> 
            <th>IP</th> 
            <th>LoggedIn On</th> 
            <th>Action</th> 
            </tr>
        </thead>
        <tbody>
            <?php if($users){ $count=1; foreach ($users as $user) { ?>
            <tr>
                <td class="text-center"><?php echo $count; $count++; ?></td>
                <td><?php echo Functions::filterInput($user['full_name']); ?></td>
                <td><?php echo Functions::filterInput($user['email']); ?></td> 
                <td><?php echo Functions::filterInput($user['contracts']); ?></td>       
                <td><?php echo Functions::filterInput($user['user_ip']); ?></td> 
                <td><?php echo Functions::filterInput(Functions::date_time_disp_format($user['loggedin_on'])); ?></td> 
                <td>
                <?php if($user['isActive']==1){ ?>
                    <a class='waves-effect waves-light btn-small delete' href="changeStatus.php?user_id=<?php echo $user['user_id']; ?>">Disable</a>
                    <?php }else{ ?>
                    <a class='waves-effect waves-light btn-small edit' href="changeStatus.php?user_id=<?php echo $user['user_id']; ?>">Enable</a>
                    <?php } ?>
                </td>                    
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
