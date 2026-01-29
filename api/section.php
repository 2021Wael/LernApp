<?php
// api/sections.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../config/Datenbank.php';

try {
    $pdo = dbconnect();
    $stmt = $pdo->query('
        SELECT b.id AS bereich_id, b.titel AS bereich_titel, t.id AS thema_id, t.titel AS thema_titel
        FROM bereiche b
        LEFT JOIN themen t ON t.bereich_id = b.id
        ORDER BY b.id, t.id
    ');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $bereiche = [];
    foreach ($rows as $r) {
        $bid = (int)$r['bereich_id'];
        if (!isset($bereiche[$bid])) {
            $bereiche[$bid] = ['id'=>$bid, 'titel'=>$r['bereich_titel'], 'themen'=>[]];
        }
        if (!empty($r['thema_id'])) {
            $bereiche[$bid]['themen'][] = [
                'id' => (int)$r['thema_id'],
                'titel' => $r['thema_titel'],
                'href' => "Schuler/{$r['thema_id']}.php"
            ];
        }
    }

    // reindex numeric array
    $out = array_values($bereiche);
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'server error']);
    exit;
}
