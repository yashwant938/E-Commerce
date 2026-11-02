<?php
require_once __DIR__ . "/../../files/include/auth.php";
require_once __DIR__ . "/../../files/include/admin-action.php";

$user_id = requireAdminPost('user-status', 'user_id');
require_once __DIR__ . "/../../api/autoload/init.php";

$db = Database::getInstance();
User::constructStatic($db);
Functions::constructStatic($db);
User::toggleActive($user_id);
header('Location: index.php', true, 303);
exit();
