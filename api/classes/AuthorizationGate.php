<?php
/** Dispatch a protected handler only after checking the current account. */
function dispatchAuthorized($request, $handler, array &$session, callable $loadUser, callable $redirect) {
    if (($session['isLoggedIn'] ?? false) !== true ||
        !isset($session['userId']) || !filter_var($session['userId'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
        return $redirect('home');
    }
    try {
        $user = $loadUser($session['userId']);
    } catch (Throwable $e) {
        // Database failures must never grant access to a protected route.
        return $redirect('home');
    }
    if (!is_array($user) || !isset($user['isActive']) || (int) $user['isActive'] !== 1) {
        $session['isActive'] = 0;
        return $redirect('home');
    }
    $session['isActive'] = 1;
    // Use the database state, not stale session flags, for every request.
    $session['isEmailVerified'] = (int) ($user['isEmailVerified'] ?? 0);
    $session['isKYC'] = (int) ($user['isKYC'] ?? 0);
    if ($session['isEmailVerified'] !== 1) return $redirect('verifyEmailForm');
    if ($session['isKYC'] !== 1) return $redirect('ekyc');
    return $handler->handle($request);
}
