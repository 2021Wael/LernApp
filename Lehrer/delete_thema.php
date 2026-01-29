<?php
// delete_thema.php
require __DIR__ . '/../config/Datenbank.php';
require __DIR__ . '/../helpers/Rolle.php';
require_lehrer();

$pdo = dbconnect();
$data = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json; charset=utf-8');

if (!$data || !isset($data['csrf']) || !csrf_validate($data['csrf'])) {
    echo json_encode(['success'=>false,'error'=>'Ungültige Anfrage (CSRF).']); exit;
}

$id = (int)($data['id'] ?? 0);
if (!$id) { echo json_encode(['success'=>false,'error'=>'Keine ID']); exit; }

$stmt = $pdo->prepare('DELETE FROM themen WHERE id = ?');
$stmt->execute([$id]);
echo json_encode(['success'=>true]);
