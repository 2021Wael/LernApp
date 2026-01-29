<?php
// ThemaController.php - UPDATED WITH SESSION CHECK

require_once __DIR__ . '/../models/ThemaModel.php';
require_once __DIR__ . '/../models/ErklaerungModel.php';
require_once __DIR__ . '/../models/FormelModel.php';
require_once __DIR__ . '/../models/BeispielModel.php';
require_once __DIR__ . '/../helpers/AufgabenGenerator.php';
require_once __DIR__ . '/../helpers/AufgabenChecker.php';

class ThemaController {
    private ThemaModel $themaModel;
    private ErklaerungModel $erklaerungModel;
    private FormelModel $formelModel;
    private BeispielModel $beispielModel;
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->themaModel = new ThemaModel($pdo);
        $this->erklaerungModel = new ErklaerungModel($pdo);
        $this->formelModel = new FormelModel($pdo);
        $this->beispielModel = new BeispielModel($pdo);
    }

    /**
     * Main entry point for Thema pages
     */
    public function show(int $themaId): void {
        // Start session only if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Thema holen
        $thema = $this->themaModel->getThemeById($themaId);

        if (!$thema) {
            die("Thema nicht gefunden");
        }
        // Handle POST requests (forms submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest($themaId);

            // POST-Redirect-GET pattern to prevent form resubmission
            header("Location: /public/index.php?page=thema&id=" . $themaId);
            exit;
        }

        // Display the Thema page
        $this->displayThemaPage($themaId);
    }

    /**
     * Handle all POST requests
     */
    private function handlePostRequest(int $themaId): void {
        $action = $_POST['action'] ?? '';

        // Teacher CRUD operations
        if (isset($_SESSION['rolle']) && $_SESSION['rolle'] === 'lehrer') {
            $this->handleTeacherOperations($action, $themaId);
            return;
        }

        // Student exercise operations
        $this->handleExerciseOperations($action);
    }

    /**
     * Handle teacher CRUD operations
     */
    private function handleTeacherOperations(string $action, int $themaId): void {
        switch ($action) {
            case 'edit_erklaerung':
                $this->updateErklaerung();
                break;

            case 'delete_erklaerung':
                $this->deleteErklaerung();
                break;

            case 'new_erklaerung':
                $this->createErklaerung($themaId);
                break;

            case 'edit_formel':
                $this->updateFormel();
                break;

            case 'delete_formel':
                $this->deleteFormel();
                break;

            case 'new_formel':
                $this->createFormel($themaId);
                break;

            case 'edit_beispiel':
                $this->updateBeispiel();
                break;

            case 'delete_beispiel':
                $this->deleteBeispiel();
                break;

            case 'new_beispiel':
                $this->createBeispiel($themaId);
                break;
        }
    }

    /**
     * Handle exercise operations
     */
    private function handleExerciseOperations(string $action): void {
        if (isset($_POST['regen'])) {
            // Clear existing exercise results when regenerating
            unset($_SESSION['exercise_results']);
            unset($_SESSION['current_aufgaben']);
            $_SESSION['current_aufgaben'] = AufgabenGenerator::generateByThemaId($_SESSION['current_thema_id'] ?? $themaId);
            $_SESSION['current_thema_id'] = $themaId;
            return;
        }

        // Check exercises if form was submitted
        if (isset($_SESSION['current_aufgaben'])) {
            $aufgaben = $_SESSION['current_aufgaben'];
            $fehler = AufgabenChecker::checkErgebnisse($aufgaben);

            // Store results in session for display
            $_SESSION['exercise_results'] = [
                'fehler' => $fehler,
                'alles_richtig' => empty($fehler),
                'aufgabenanzahl' => count($aufgaben),
                'aufgaben_antwort' => $_POST
            ];
        }
    }

    /**
     * Display the Thema page with all data
     */
    private function displayThemaPage(int $themaId): void {
        // Thema Validieren
        $thema = $this->themaModel->getThemeById($themaId);
        if (!$thema) {
            header('Location: /public/index.php?page=home');
            exit;
        }

        // Fetch data
        $data = $this->fetchThemaData($themaId);
        $pdf_datei = $data['pdf_datei'];
        $data['thema'] = $thema;
        $data['themaId'] = $themaId;
        $data['isTeacher'] = isset($_SESSION['rolle']) && $_SESSION['rolle'] === 'lehrer';

        // Ergebnis leeren
        $data['exercise_results'] = $_SESSION['exercise_results'] ?? null;
        $data['aufgaben_antwort'] = $_SESSION['exercise_results']['aufgaben_antwort'] ?? [];
        unset($_SESSION['exercise_results']);

        // View Laden
        $this->loadView('thema', $data);
    }

    /**
     * Fetch all data needed for Thema page
     */
    private function fetchThemaData(int $themaId): array {
        return [
            'erklaerungen' => $this->erklaerungModel->getByTheme($themaId),
            'formeln' => $this->formelModel->getByTheme($themaId),
            'beispiele' => $this->beispielModel->getByTheme($themaId),
            'aufgaben' => $this->getOrGenerateExercises($themaId),
            'pdf_datei' => $this->themaModel->getPdfDatei($themaId),
            'prevThema' => $this->themaModel->getPrevTheme($themaId),
            'nextThema' => $this->themaModel->getNextTheme($themaId),
        ];
    }

    /**
     * Get or generate exercises for this Thema
     */
    private function getOrGenerateExercises(int $themaId): array {
        // Check if we should regenerate or use cached exercises
        $shouldRegenerate = isset($_GET['regen']) || !isset($_SESSION['current_aufgaben']) || ($_SESSION['current_thema_id'] ?? 0) !== $themaId;

        if ($shouldRegenerate) {
            $aufgaben = AufgabenGenerator::generateByThemaId($themaId);
            $_SESSION['current_aufgaben'] = $aufgaben;
            $_SESSION['current_thema_id'] = $themaId;
        } else {
            $aufgaben = $_SESSION['current_aufgaben'];
        }

        return $aufgaben;
    }

    /**
     * CRUD Methods for Teachers
     */
    private function updateErklaerung(): void {
        if (isset($_POST['id']) && isset($_POST['text'])) {
            $id = (int)$_POST['id'];
            $text = trim($_POST['text']);
            if (!empty($text)) {
                $this->erklaerungModel->update($id, $text);
            }
        }
    }

    private function deleteErklaerung(): void {
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $this->erklaerungModel->delete($id);
        }
    }

    private function createErklaerung(int $themaId): void {
        if (isset($_POST['text'])) {
            $text = trim($_POST['text']);
            if (!empty($text)) {
                $this->erklaerungModel->create($themaId, $text);
            }
        }
    }
    private function updateFormel(): void {
        if (isset($_POST['id']) && isset($_POST['text'])) {
            $id = (int)$_POST['id'];
            $text = trim($_POST['text']);
            if (!empty($text)) {
                $this->formelModel->update($id, $text);
            }
        }
    }

    private function deleteFormel(): void {
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $this->formelModel->delete($id);
        }
    }

    private function createFormel(int $themaId): void {
        if (isset($_POST['text'])) {
            $text = trim($_POST['text']);
            if (!empty($text)) {
                $this->formelModel->create($themaId, $text);
            }
        }
    }
    private function updateBeispiel(): void {
        if (isset($_POST['id']) && isset($_POST['text'])) {
            $id = (int)$_POST['id'];
            $text = trim($_POST['text']);
            if (!empty($text)) {
                $this->beispielModel->update($id, $text);
            }
        }
    }

    private function deleteBeispiel(): void {
        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $this->beispielModel->delete($id);
        }
    }

    private function createBeispiel(int $themaId): void {
        if (isset($_POST['text'])) {
            $text = trim($_POST['text']);
            if (!empty($text)) {
                $this->beispielModel->create($themaId, $text);
            }
        }
    }

    /**
     * Load view with data
     */
    private function loadView(string $viewName, array $data = []): void {
        extract($data);
        require __DIR__ . '/../views/' . $viewName . '.php';
    }
}