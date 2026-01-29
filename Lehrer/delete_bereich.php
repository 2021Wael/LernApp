<?php
// delete_bereich.php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

require __DIR__ . '/../config/Datenbank.php';
require __DIR__ . '/../helpers/Rolle.php';

$pdo = dbconnect();
$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage (kein JSON).']);
    exit;
}
if (!isset($body['csrf']) || !function_exists('csrf_validate') || !csrf_validate($body['csrf'])) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage (CSRF).']);
    exit;
}

if (empty($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Nicht angemeldet.']);
    exit;
}
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'lehrer') {
    echo json_encode(['success' => false, 'error' => 'Keine Berechtigung.']);
    exit;
}

$id = (int)($body['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Keine ID angegeben.']);
    exit;
}


$stmt = $pdo->prepare('DELETE FROM bereiche WHERE id = ?');
$ok = $stmt->execute([$id]);
if ($ok) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Löschen fehlgeschlagen.']);
}
