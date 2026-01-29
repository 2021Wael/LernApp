<?php
// Rolle.php
// Minimal: Session, CSRF, Rollenprüfung (lehrer)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * CSRF token helpers
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_validate(?string $token): bool {
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Rolle prüfen
 * Erwartet: $_SESSION['user_id'] und $_SESSION['rolle'] bei eingeloggtem Benutzer.
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}
function is_lehrer(): bool {
    return !empty($_SESSION['rolle']) && $_SESSION['rolle'] === 'lehrer';
}

/**
 * Middleware: nur Lehrer
 */
function require_lehrer(): void
{
    if (!is_logged_in() || !is_lehrer()) {
        http_response_code(403);
        echo "Zugriff verweigert. Nur für Lehrkräfte.";
        exit;
    }
}
