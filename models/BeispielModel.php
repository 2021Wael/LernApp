<?php

class BeispielModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lädt alle Beispiele eines Themas
     */
    public function getByTheme(int $themaId): array
    {
        $sql = "SELECT id, themaId, text, bild 
                FROM beispiele 
                WHERE themaId = :themaId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':themaId' => $themaId]);

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Lädt ein einzelnes Beispiel
     */
    public function getById(int $id): ?string
    {
        $sql = "SELECT text FROM beispiele WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();
        return $row['text'] ?? null;
    }


    /**
     * Neues Beispiel anlegen
     */
    public function create(int $themaId, string $text): bool
    {
        $sql = "INSERT INTO beispiele (themaId, text) 
                VALUES (:themaId, :text)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':themaId' => $themaId,
            ':text' => $text
        ]);
    }

    /**
     * Beispiel bearbeiten
     */
    public function update(int $id, string $text): bool
    {
        $sql = "UPDATE beispiele 
                SET text = :text 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':text' => $text,
            ':id'   => $id
        ]);    }

    /**
     * Beispiel löschen
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM beispiele WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }
}