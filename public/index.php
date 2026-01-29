<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
/**
 * Front Controller / Router
 * <br>
 * Um irgendeine Seite zuzugreifen, muss die uri durch den Front Controller laufen, <br>
 * und er leitet auf das entsprechende Verzeichnis weiter. <br>
 * Z.B.: ../public/index.php?page=login <br>
 * -> views/login.php
 */

// Datenbank laden
require_once __DIR__ . '/../config/Datenbank.php';

// Controllers laden
require_once __DIR__ . '/../controllers/ThemaController.php';
require_once __DIR__ . '/../controllers/HomeController.php';


// Get database connection
$pdo = dbconnect();

// Seite und ID nehmen von URL Get the requested page and ID from URL
$page = $_GET['page'] ?? 'home';
$id = $_GET['id'] ?? null;

// Routing
switch ($page) {
    case 'thema':
        if ($id && is_numeric($id)) {
            $controller = new ThemaController($pdo);
            $controller->show((int)$id);
        } else {
            header('Location: /public/index.php');
            exit;
        }
        break;

    // Login System und Lehrer Ansicht als Legacy Module
    case 'login':
        require __DIR__ . '/../views/login.php';
        exit;
    case 'process_login':
        require __DIR__ . '/../controllers/process_login.php';
        exit;
    case 'register':
        require __DIR__ . '/../views/register.php';
        exit;
    case 'process_register':
        require __DIR__ . '/../controllers/process_register.php';
        exit;
    case 'logout':
        require __DIR__ . '/../views/logout.php';
        exit;
    case 'teacher_dashboard':
        require __DIR__ . '/../Lehrer/teacher_dashboard.php';
        exit;

    /*======= Lehrer ===== */
    case 'save_bereich':
        require __DIR__ . '/../Lehrer/save_bereich.php';
        exit;

    case 'delete_bereich':
        require __DIR__ . '/../Lehrer/delete_bereich.php';
        exit;

    case 'save_thema':
        require __DIR__ . '/../Lehrer/save_thema.php';
        exit;

    case 'delete_thema':
        require __DIR__ . '/../Lehrer/delete_thema.php';
        exit;

    case 'home':
    default:
        $controller = new HomeController($pdo);
        $controller->index();
        break;
}