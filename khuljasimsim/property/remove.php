<?php
require_once __DIR__ . "/../../files/include/auth.php";
require_once __DIR__ . "/../../files/include/admin-action.php";

$pid = requireAdminPost('property-status', 'pid');
require_once __DIR__ . "/../../api/autoload/init.php";

$db = Database::getInstance();
Property::constructStatic($db);
Functions::constructStatic($db);

Property::removeAdmin($pid);
header('Location: index.php', true, 303);
exit();
