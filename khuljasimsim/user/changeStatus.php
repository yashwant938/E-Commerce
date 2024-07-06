<?php
require_once "../../files/include/auth.php";
require_once "../../api/autoload/init.php";

$db = Database::getInstance();
User::constructStatic($db);
Functions::constructStatic($db);
//$users = User::fetchAllUserWithAuth();

$user_id=$_GET["user_id"];
User::toggleActive($user_id);
header('Location: index.php');
?>
