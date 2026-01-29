<?php
// process_register.php
session_start();
require __DIR__ . '/../config/Datenbank.php';


// CSRF
$csrf_session = $_SESSION['csrf_register'] ?? '';
$csrf_post = $_POST['csrf'] ?? '';
if (!$csrf_session || !$csrf_post || !hash_equals($csrf_session, $csrf_post)) {
    header('Location: register.php?err=Ungültiges Token');
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$password2 = (string)($_POST['password2'] ?? '');
$regcode = trim((string)($_POST['regcode'] ?? ''));

// Validation
if ($username === '' || $password === '' || $password2 === '') {
    header('Location: register.php?err=Bitte alle Felder ausfüllen');
    exit;
}
if ($password !== $password2) {
    header('Location: register.php?err=Passwörter stimmen nicht überein');
    exit;
}
if (strlen($password) < 6) {
    header('Location: register.php?err=Passwort muss mindestens 6 Zeichen haben');
    exit;
}
if (!preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $username)) {
    header('Location: register.php?err=Benutzername: 3-50 Zeichen, nur Buchstaben/Zahlen ._- erlaubt');
    exit;
}



try {
    $pdo = dbconnect();

    // ensure password column long enough (optional check)
    // Check username unique
    $stmt = $pdo->prepare('SELECT id FROM benutzer WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        header('Location: register.php?err=Benutzername bereits vergeben');
        exit;
    }

    // Hash password and insert as 'lehrer'
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $pdo->prepare('INSERT INTO benutzer (username, password, rolle) VALUES (?, ?, ?)');
    $ins->execute([$username, $hash, 'lehrer']);
    $newId = $pdo->lastInsertId();

    // Auto-login: set session and redirect to teacher dashboard
    session_regenerate_id(true);
    $_SESSION['user_id'] = $newId;
    $_SESSION['username'] = $username;
    $_SESSION['rolle'] = 'lehrer';

    header('Location: ../public/index.php?page=teacher_dashboard');
    exit;

} catch (Exception $e) {
    // For production: log $e->getMessage() into server logs and show generic message to user
    header('Location: register.php?err=Serverfehler beim Anlegen des Kontos');
    exit;
}
