<?php
/** Session-bound tokens also bind each form to its action and record. */
function adminActionToken($action, $id) {
    if (!isset($_SESSION['admin_csrf_secret']) ||
        !is_string($_SESSION['admin_csrf_secret']) || strlen($_SESSION['admin_csrf_secret']) !== 32) {
        $_SESSION['admin_csrf_secret'] = random_bytes(32);
    }
    return hash_hmac('sha256', $action . ':' . (string) $id, $_SESSION['admin_csrf_secret']);
}

/** Reject invalid requests before loading the database or calling a mutation. */
function requireAdminPost($action, $idField) {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
        exit('Method not allowed');
    }

    $value = $_POST[$idField] ?? null;
    if (!is_string($value) || filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
        http_response_code(400);
        exit('Invalid record ID');
    }

    $token = $_POST['csrf_token'] ?? null;
    $secret = $_SESSION['admin_csrf_secret'] ?? null;
    if (!is_string($token) || !is_string($secret) || strlen($secret) !== 32 ||
        !hash_equals(hash_hmac('sha256', $action . ':' . $value, $secret), $token)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    return (int) $value;
}
