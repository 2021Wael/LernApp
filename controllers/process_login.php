<?php
// process_login.php
session_start();
require __DIR__ . '/../config/Datenbank.php';

// CSRF check
$csrf_session = $_SESSION['csrf_login'] ?? '';
$csrf_post = $_POST['csrf'] ?? '';
if (!$csrf_session || !$csrf_post || !hash_equals($csrf_session, $csrf_post)) {
    header('Location: login.php?err=Ungültiges Token');
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header('Location: login.php?err=Bitte Benutzername und Passwort eingeben');
    exit;
}

try {
    $pdo = dbconnect();
    $stmt = $pdo->prepare('SELECT id, username, password, rolle FROM benutzer WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: login.php?err=Ungültige Anmeldedaten');
        exit;
    }

    $stored = $user['password'];

    $ok = false;
    // if stored is a bcrypt/argon2 hash use password_verify
    if (password_get_info($stored)['algo'] !== 0) {
        if (password_verify($password, $stored)) {
            $ok = true;
            // rehash if algorithm changed/settings changed
            if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $u = $pdo->prepare('UPDATE benutzer SET password = ? WHERE id = ?');
                $u->execute([$newHash, $user['id']]);
            }
        }
    } else {
        // fallback: plain text match (legacy). If matches, upgrade to hashed password.
        if (hash_equals($stored, $password)) {
            $ok = true;
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $u = $pdo->prepare('UPDATE benutzer SET password = ? WHERE id = ?');
            $u->execute([$newHash, $user['id']]);
        }
    }

    if (!$ok) {
        header('Location: login.php?err=Ungültige Anmeldedaten');
        exit;
    }

    // success: create session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['rolle'] = $user['rolle'];

    // redirect based on role
    if ($user['rolle'] === 'lehrer') {
        header('Location: ../public/index.php?page=teacher_dashboard');
    } else {
        header('Location: ../public/index.php');
    }
    exit;

} catch (Exception $e) {
    // don't reveal internal errors to users
    header('Location: login.php?err=Serverfehler');
    exit;
}
