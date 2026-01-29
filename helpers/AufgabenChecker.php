<?php

class AufgabenChecker {

    /**
     * Prüft die vom Benutzer eingegebenen Ergebnisse gegen die berechneten Lösungen.
     * @param array $aufgaben Das Array mit den ursprünglichen Aufgaben und Lösungen.
     * @return array Ein Array mit Fehlermeldungen (Strings). Leer, wenn alles korrekt.
     */
    public static function checkErgebnisse(array $aufgaben): array {
        $fehler = [];

        foreach ($aufgaben as $id => $daten) {
            $input_name = "aufgabe_" . $id;
            $user = $_POST[$input_name] ?? null;

            // Stelle sicher, dass $user ein String ist (nicht null)
            $user = (string)$user;

            $user_cleaned = str_replace(' ', '', $user);
            $user_cleaned = str_replace(',', '.', $user_cleaned);
            $loesung_cleaned = str_replace(' ', '', (string)$daten["loesung"]);

            if (strcasecmp($user_cleaned, $loesung_cleaned) !== 0) {
                $fehler[] = "Aufgabe $id ist falsch";
            }
        }

        return $fehler;
    }
}