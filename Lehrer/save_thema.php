<?php
// save_thema.php
require __DIR__ . '/../config/Datenbank.php';
require __DIR__ . '/../helpers/Rolle.php';
require_lehrer();

$pdo = dbconnect();
$data = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json; charset=utf-8');

if (!$data || !isset($data['csrf']) || !csrf_validate($data['csrf'])) {
    echo json_encode(['success'=>false,'error'=>'Ungültige Anfrage (CSRF).']); exit;
}

$action = $data['action'] ?? '';
if ($action === 'create') {
    $bereich_id = (int)($data['bereich_id'] ?? 0);
    $titel = trim($data['titel'] ?? '');
    if (!$bereich_id || $titel === '') { echo json_encode(['success'=>false,'error'=>'Fehlerhafte Daten']); exit; }
    $stmt = $pdo->prepare('INSERT INTO themen (titel, bereich_id) VALUES (?, ?)');
    $stmt->execute([$titel, $bereich_id]);
    echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]); exit;
}
if ($action === 'update') {
    $id = (int)($data['id'] ?? 0);
    $titel = trim($data['titel'] ?? '');
    if (!$id || $titel === '') { echo json_encode(['success'=>false,'error'=>'Fehlerhafte Daten']); exit; }
    $stmt = $pdo->prepare('UPDATE themen SET titel = ? WHERE id = ?');
    $stmt->execute([$titel, $id]);
    echo json_encode(['success'=>true]); exit;
}

echo json_encode(['success'=>false,'error'=>'Aktion nicht unterstützt']);
