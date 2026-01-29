<?php
// save_bereich.php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

require __DIR__ . '/../config/Datenbank.php';
require __DIR__ . '/../helpers/Rolle.php';

$pdo = dbconnect();

// read JSON body
$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage (kein JSON).']);
    exit;
}

// CSRF
if (!isset($body['csrf']) || !function_exists('csrf_validate') || !csrf_validate($body['csrf'])) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage (CSRF).']);
    exit;
}

if (empty($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Nicht angemeldet.']);
    exit;
}
// zusätzliche Rolle-Prüfung
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'lehrer') {
    // falls Rolle nicht stimmt -> verweigern
    echo json_encode(['success' => false, 'error' => 'Keine Berechtigung.']);
    exit;
}

// Verarbeitung
$action = $body['action'] ?? '';

try {
    if ($action === 'create') {
        $titel = trim((string)($body['titel'] ?? ''));
        if ($titel === '') {
            echo json_encode(['success' => false, 'error' => 'Titel fehlt']);
            exit;
        }
        $stmt = $pdo->prepare('INSERT INTO bereiche (titel) VALUES (?)');
        $ok = $stmt->execute([$titel]);
        if (!$ok) throw new Exception('DB Fehler');
        $id = (int)$pdo->lastInsertId();
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    if ($action === 'update') {
        $id = (int)($body['id'] ?? 0);
        $titel = trim((string)($body['titel'] ?? ''));
        if (!$id || $titel === '') {
            echo json_encode(['success' => false, 'error' => 'Fehlerhafte Daten']);
            exit;
        }
        $stmt = $pdo->prepare('UPDATE bereiche SET titel = ? WHERE id = ?');
        $stmt->execute([$titel, $id]);
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Aktion nicht unterstützt']);
    exit;

} catch (Exception $e) {
    // Nachricht für das eine Debugging
    echo json_encode(['success' => false, 'error' => 'Serverfehler: ' . $e->getMessage()]);
    exit;
}
