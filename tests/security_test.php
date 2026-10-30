<?php
require_once __DIR__ . '/../api/classes/AuthorizationGate.php';
require_once __DIR__ . '/../api/classes/PropertySearch.php';

function check($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
class CountingHandler {
    public $calls = 0;
    public function handle($request) { $this->calls++; return 'handled'; }
}
$handler = new CountingHandler();
$redirect = function ($route) { return $route; };
$active = ['isActive' => 1, 'isEmailVerified' => 1, 'isKYC' => 1];
$load = function ($id) use ($active) { return $active; };

foreach ([[], ['isLoggedIn' => false, 'userId' => 1], ['isLoggedIn' => true, 'userId' => '1 OR 1=1']] as $session) {
    check(dispatchAuthorized(null, $handler, $session, $load, $redirect) === 'home', 'Invalid session was not denied');
}
check($handler->calls === 0, 'A denied session invoked its handler');
$session = ['isLoggedIn' => true, 'userId' => 1];
foreach ([false, ['isActive' => 0], ['isActive' => 1, 'isEmailVerified' => 0],
          ['isActive' => 1, 'isEmailVerified' => 1, 'isKYC' => 0]] as $user) {
    $loader = function ($id) use ($user) { return $user; };
    dispatchAuthorized(null, $handler, $session, $loader, $redirect);
}
check($handler->calls === 0, 'An invalid account invoked its handler');
$error = function ($id) { throw new RuntimeException('database unavailable'); };
check(dispatchAuthorized(null, $handler, $session, $error, $redirect) === 'home', 'Database error did not deny access');
check($handler->calls === 0, 'Database failure invoked handler');
check(dispatchAuthorized(null, $handler, $session, $load, $redirect) === 'handled', 'Valid account was rejected');
check($handler->calls === 1, 'Valid account should invoke the handler exactly once');

$attack = "x%' OR 1=1 --";
$query = buildPropertySearch(['0', '1'], '100', '500', '2026-01-01', ['2', '7'], ['1', '10'], $attack);
check($query !== false, 'Valid filter was rejected');
check(strpos($query[0], $attack) === false && $query[1]['search'] === '%' . $attack . '%', 'Search term was not bound');
check(strpos($query[0], ':location0') !== false && $query[1]['location0'] === 2, 'Location was not bound');
check(buildPropertySearch(['0'], '', '', '2026-01-01', ['2) OR 1=1 --'], ['1'], '') === false, 'Injected location was accepted');
check(buildPropertySearch(['0'], '', '', '2026-01-01', ['2'], ['1 OR 1=1'], '') === false, 'Injected amenity was accepted');
check(buildPropertySearch(['0 OR 1=1'], '', '', '2026-01-01', ['2'], ['1'], '') === false, 'Injected type was accepted');
echo "Authorization and listing-filter checks passed.\n";
