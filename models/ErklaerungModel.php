<?php

class ErklaerungModel
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Lädt alle Erklärungen eines bestimmten Themas.
     * Return-Typ: array
     */
    public function getByTheme($themaId)
    {
        $stmt = $this->db->prepare("SELECT * FROM erklaerungen WHERE themaId = ?");
        $stmt->execute([$themaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lädt eine einzelne Erklärung anhand der ID.
     * Return-Typ: array
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM erklaerungen WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Legt eine neue Erklärung in der Datenbank an.
     * Return-Typ: boolean (true = Erfolg, false = Fehler)
     */
    public function create($themaId, $text)
    {
        $stmt = $this->db->prepare("INSERT INTO erklaerungen (themaId, text) VALUES (?, ?)");
        return $stmt->execute([$themaId, $text]);
    }

    /**
     * Aktualisiert eine bestehende Erklärung.
     * Return-Typ: boolean
     */
    public function update($id, $text)
    {
        $stmt = $this->db->prepare("UPDATE erklaerungen SET text = ? WHERE id = ?");
        return $stmt->execute([$text, $id]);
    }

    /**
     * Löscht eine Erklärung.
     * Return-Typ: boolean
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM erklaerungen WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
