<?php
$root = __DIR__;
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/__test/ready') { echo 'ready'; return; }
if ($path === '/__test/login') {
    session_start();
    $_SESSION['login'] = true;
    $_SESSION['is_super_user'] = 1;
    echo 'Synthetic admin session';
    return;
}
$allowed = ['/khuljasimsim/user/index.php', '/khuljasimsim/property/index.php',
    '/khuljasimsim/user/changeStatus.php', '/khuljasimsim/property/remove.php'];
if (in_array($path, $allowed, true)) {
    $target = $root . $path;
    chdir(dirname($target));
    include $target;
    return;
}
http_response_code(404);
