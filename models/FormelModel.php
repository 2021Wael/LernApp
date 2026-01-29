<?php

class FormelModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lädt alle Formeln eines bestimmten Themas.
     * Return-Typ: array
     */
    public function getByTheme($themaId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM formeln WHERE themaId = ?");
        $stmt->execute([$themaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lädt eine einzelne Formel anhand der ID.
     * Return-Typ: array
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM formeln WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Legt eine neue Formel in der Datenbank an.
     * Return-Typ: boolean (true = Erfolg, false = Fehler)
     */
    public function create($themaId, $text)
    {
        $stmt = $this->pdo->prepare("INSERT INTO formeln (themaId, text) VALUES (?, ?)");
        return $stmt->execute([$themaId, $text]);
    }

    /**
     * Aktualisiert eine bestehende Formel.
     * Return-Typ: boolean
     */
    public function update($id, $text)
    {
        $stmt = $this->pdo->prepare("UPDATE formeln SET text = ? WHERE id = ?");
        return $stmt->execute([$text, $id]);
    }

    /**
     * Löscht eine Formel.
     * Return-Typ: boolean
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM formeln WHERE id = ?");
        return $stmt->execute([$id]);
    }
}