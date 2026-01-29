<?php

require_once __DIR__ . '/../models/BereichModel.php';
require_once __DIR__ . '/../models/ThemaModel.php';

/**
 * HomeController
 * Handles the homepage displaying all subject areas
 */
class HomeController {
    private BereichModel $bereichModel;

    public function __construct(PDO $pdo) {
        $this->bereichModel = new BereichModel($pdo);
    }

    /**
     * Homeseite
     */
    public function index(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Alle zugehörige Unterthemen holen
        $bereiche = $this->bereichModel->getAlleBereicheMitThemen();

        foreach ($bereiche as &$bereich) {
            if (!empty($bereich['themen'])) {
                foreach ($bereich['themen'] as &$thema) {
                    $thema['href'] = '/public/index.php?page=thema&id=' . $thema['id'];
                }
            }
        }

        // Daten vorbereiten (inklusive Session-Daten)
        $data = [
            'bereiche' => $bereiche,
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'rolle' => $_SESSION['rolle'] ?? null
        ];

        // View laden
        $this->loadView('home', $data);
    }

    /**
     * Hilfsfunktion, um den View zu laden.
     */
    private function loadView(string $viewName, array $data = []): void {
        extract($data);
        require __DIR__ . '/../views/' . $viewName . '.php';
    }
}