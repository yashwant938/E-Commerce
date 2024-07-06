<?php
require_once "../../files/include/auth.php";
require_once "../../api/autoload/init.php";

$db = Database::getInstance();
Property::constructStatic($db);
Functions::constructStatic($db);

$pid=$_GET["pid"];
Property::removeAdmin($pid);
header('Location: index.php');
?>
