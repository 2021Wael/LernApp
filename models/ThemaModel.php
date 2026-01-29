<?php

/**
 * ThemaModel
 *
 * Greift auf die Tabelle `themen` zu.
 * Erwartet eine vorhandene Umgebungsvariable für die DB-Verbindung oder
 * eine Konfigurationsdatei. Hier wird PDO mit DSN genutzt.
 */
class ThemaModel
{
    private \PDO $pdo;

    /**
     * Konstruktor.
     *
     * @param \PDO $pdo Bereits konfigurierte PDO-Instanz
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Liefert alle Themen für die Homepage.
     *
     * @return array Liste der Themen (assoziativ)
     */
    public function getAllThemesByBereichId(int $bereichId): array
    {
        $sql = "
            SELECT 
                t.id,
                t.titel,
                t.bereich_id,
                b.titel AS bereich_name
            FROM themen t
            LEFT JOIN bereiche b ON b.id = t.bereich_id
            WHERE t.bereich_id = :bid
            ORDER BY t.id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':bid' => $bereichId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Sucht das Thema mit der vorherigen ID (für Nav-Buttons)
     *
     * @param int $id Aktuelle ID
     * @return array|null Vorheriges Thema (Titel und ID) oder null wenn nicht vorhanden
     */
    public function getPrevTheme(int $id): ?array
    {
        $sql = "
        SELECT t2.id, t2.titel
        FROM themen t2
        WHERE t2.bereich_id = (
            SELECT t1.bereich_id
            FROM themen t1
            WHERE t1.id = :id
        )
        AND t2.id < :id
        ORDER BY t2.id DESC
        LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Sucht das Thema mit der nächsten ID (für Nav-Buttons)
     *
     * @param int $id Aktuelle ID
     * @return array|null Nächstes Thema (Titel und ID) oder null wenn nicht vorhanden
     */
    public function getNextTheme(int $id): ?array
    {
        $sql = "
        SELECT t2.id, t2.titel
        FROM themen t2
        WHERE t2.bereich_id = (
            SELECT t1.bereich_id
            FROM themen t1
            WHERE t1.id = :id
        )
        AND t2.id > :id
        ORDER BY t2.id 
        LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Liefert Titel + TopmenuTitel eines Themas.
     *
     * @param int $id Themen-ID
     * @return array|null Assoziatives Array mit 'titel' und 'topmenu_titel' oder null
     */
    public function getThemeById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT 
                                            t.titel,
                                            b.titel AS bereich_name
                                        FROM themen t
                                        LEFT JOIN bereiche b ON b.id = t.bereich_id
                                        WHERE t.id = :id');

        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Liefert PDF Datei
     *
     * @param int $id Themen-ID
     * @return string|null PDF Datei oder leerer String
     */
    public function getPdfDatei(int $id): ?string
    {
        $sql = "SELECT pdf_datei FROM themen WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $pdf = $stmt->fetchColumn();

        return $pdf !== false ? $pdf : null;
    }

}

