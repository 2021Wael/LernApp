<?php
/**
 * Datenbank Configuration
 * Returns: ein PDO Instanz
 */

if (!function_exists('dbconnect')) {
    function dbconnect(): PDO
    {
        $host = 'localhost';
        $dbname = 'sweDatenbank';
        $username = 'root';
        $password = ''; //Passwort

        $dsn = "mysql:host=$host;
            dbname=$dbname;
            charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            die("Konnte keine Verbindung zu der Datenbank erstellen: " . $e->getMessage());
        }
    }
}