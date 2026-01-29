<?php
// BereichModel.php

class BereichModel
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Liefert alle Bereiche.
     *
     * @return array
     */
    public function getAlleBereiche(): array
    {
        $stmt = $this->pdo->query("SELECT id, titel FROM bereiche ORDER BY id");
        return $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * Liefert einen Bereich anhand ID.
     *
     * @return array|null
     */
    public function getBereichById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, titel FROM bereiche WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Holt einen Bereich mit allen zugehörigen Themen.
     *
     * @return array|null
     */
    public function getBereichMitThemen(int $bereichId): ?array
    {
        // Bereich selbst
        $bereich = $this->getBereichById($bereichId);
        if (!$bereich) return null;

        // die Themen holen mithilfe von ThemaModel
        $themaModel = new ThemaModel($this->pdo);
        $themen = $themaModel->getAllThemesByBereichId($bereichId);

        $bereich['themen'] = $themen;

        return $bereich;
    }

    /**
     * Holt ALLE Bereiche mit ihren Themen.
     */
    public function getAlleBereicheMitThemen(): array
    {
        $bereiche = $this->getAlleBereiche();

        foreach ($bereiche as &$b) {
            $stmt = $this->pdo->prepare("
                SELECT id, titel
                FROM themen
                WHERE bereich_id = :bid
                ORDER BY id
            ");
            $stmt->execute([':bid' => $b['id']]);
            $b['themen'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $bereiche;
    }

    //  createBereich und deleteBereich

    /**
     * Legt einen neuen Bereich an und liefert die neue ID zurück.
     *
     * @param string $titel
     * @return int eingefügte ID
     * @throws \Exception
     */
    public function createBereich(string $titel): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO bereiche (titel) VALUES (:titel)');
        if (!$stmt->execute([':titel' => $titel])) {
            throw new \Exception('DB Fehler beim Anlegen des Bereichs');
        }
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Löscht einen Bereich (bei Bedarf auch Themen vorher löschen, je nach DB-FK).
     *
     * @param int $id
     * @return bool
     */
    public function deleteBereich(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM bereiche WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
