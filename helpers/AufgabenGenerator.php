<?php

/**
 * AufgabenGenerator
 * Generiert dynamische Übungsaufgaben basierend auf dem Thema
 */
class AufgabenGenerator {

    /**
     * Generiert Aufgaben basierend auf der Themen-ID
     */
    public static function generateByThemaId(int $themaId): array {
        // Map thema IDs zu Generatorfunktionen
        $generators = [
            1 => 'generateAdditionSubtraktion',
            2 => 'generateMultiplikationDivision',
            3 => 'generateBruchrechnung',
            4 => 'generateErweiternKuerzen',
            5 => 'generateFlaecheninhalt',
            6 => 'generateUmfang',
            7 => 'generateDreieckFlaeche',
            8 => 'generateKreise',
            9 => 'generateVolumen',
            10 => 'generateDezimalsystem',
            11 => 'generateDezimalzahlenRunden',
            12 => 'generateProzentrechnung',
            13 => 'generateZinsrechnung',
            14 => 'generateWahrscheinlichkeit',
            15 => 'generateGraphenDiagramme'
        ];

        $sessionKey = 'aufgaben_' . $themaId;

        // Prüfe, ob Neugenerierung oder Session schon vorhanden
        if (!isset($_SESSION[$sessionKey]) || isset($_POST['regen'])) {
            $generatorMethod = $generators[$themaId] ?? null;

            if ($generatorMethod && method_exists(self::class, $generatorMethod)) {
                $aufgaben = self::$generatorMethod();
                $_SESSION[$sessionKey] = $aufgaben;
            } else {
                $aufgaben = [];
            }
        } else {
            $aufgaben = $_SESSION[$sessionKey];
        }

        return $aufgaben;
    }

    // Generatorfunktionen ---

    /**
     * Generiert 5 Aufgaben zum Thema "Addition und Subtraktion von großen Zahlen". <br>
     * Wertebereich: [1000, 9999]
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateAdditionSubtraktion(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; $i++) {
            $a = rand(1000, 9999);
            $b = rand(1000, 9999);

            if (rand(0, 1) === 1) {
                // --- ADDITION ---
                $text = $a . ' + ' . $b . ' = ?';
                $loesung = $a + $b;
            } else {
                // --- SUBTRAKTION ---
                // das Ergebnis bleibt positiv
                $min = min($a, $b);
                $max = max($a, $b);

                $text = $max . ' - ' . $min . ' = ?';
                $loesung = $max - $min;
            }

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }


    /**
     * Generiert 5 Aufgaben zum Thema "Multiplikation und Division".<br>
     * Wertebereich Multiplikation: [200, 18000] <br>
     * Wertebereich Division: [4, 1000]
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateMultiplikationDivision(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            $a_mult = rand(100, 600);
            $b_mult = rand(2, 30);
            // ganzzahlige Division
            $divisor = rand(2, 20);
            $quotient = rand(2, 50);
            $dividend = $quotient * $divisor;

            if (rand(0,1) === 1) {
                // --- MULTIPLIKATION ---
                $text = $a_mult . ' x ' . $b_mult . ' = ?';
                $loesung = $a_mult * $b_mult;
            } else {
                // --- DIVISION ---
                $text = $dividend . ' / ' . $divisor . ' = ?';
                $loesung = $quotient;
            }

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zur Bruchrechnung. <br>
     * Die Nenner liegen zwischen 2 und 10. Die Ergebnisse sind stets positiv.
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateBruchrechnung(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            do {
                $nenner = rand(2, 10);
                $zaehler1 = rand(1, $nenner - 1);
                $zaehler2 = rand(1, $nenner - 1);

                if (rand(0, 1) === 1) {
                    // ADDITION
                    $ergebnis = $zaehler1 + $zaehler2;
                    $text = "$zaehler1/$nenner + $zaehler2/$nenner = ?";
                } else {
                    // SUBTRAKTION
                    $zaehler_a = max($zaehler1, $zaehler2);
                    $zaehler_b = min($zaehler1, $zaehler2);

                    $ergebnis = $zaehler_a - $zaehler_b;
                    $text = "$zaehler_a/$nenner - $zaehler_b/$nenner = ?";
                }

                // Zähler darf nicht 0 sein
                $gleich_null = ($ergebnis === 0);

                // Zähler darf nicht gleich Nenner sein
                $gleich_eins = ($ergebnis === $nenner);

            } while ($gleich_eins || $gleich_null);
            $loesung = "$ergebnis/$nenner";

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Erweitern und Kürzen von Brüchen". <br>
     * Die Nenner liegen zwischen 2 und 10. Die Ergebnisse sind stets positiv.
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateErweiternKuerzen(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            if (rand(0,1) === 1) {
                // --- KÜRZEN ---
                // gemeinsamen Teiler (Kürzungsfaktor)
                $faktor = rand(2, 5);
                $zaehler_klein = rand(1, 5);
                $nenner_klein = rand($zaehler_klein + 1, 10); // Nenner > Zähler

                $zaehler_gross = $zaehler_klein * $faktor;
                $nenner_gross = $nenner_klein * $faktor;
                // Vollständiges Kürzen
                $teiler = ggt($zaehler_klein, $nenner_klein);
                $loesung_zaehler_max = $zaehler_klein / $teiler;
                $loesung_nenner_max = $nenner_klein / $teiler;

                $text = "Kürze $zaehler_gross/$nenner_gross vollständig.";
                $loesung = "$loesung_zaehler_max/$loesung_nenner_max";
            } else {
                // --- ERWEITERN ---
                // Erweiterungsfaktor
                $faktor = rand(2, 7);

                $nenner_alt = rand(3, 10);
                $zaehler_alt = rand(1, $nenner_alt - 1);

                $zaehler_neu = $zaehler_alt * $faktor;
                $nenner_neu = $nenner_alt * $faktor;

                $text = "Erweitere $zaehler_alt/$nenner_alt mit $faktor.";
                $loesung = "$zaehler_neu/$nenner_neu";
            }

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Geometrie: Berechnung von Flächeninhalte". <br>
     * Wertebereich: [4, 600]
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateFlaecheninhalt(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            $a = rand(2, 20);
            $b = rand($a + 1, 30);
            if (rand(0,1) === 1) {
                // --- QUADRAT ---
                $text = "Ein Quadrat mit Seitenlänge $a cm - berechne die Fläche.";
                $loesung = $a * $a;
            } else {
                // --- RECHTECK ---
                $text = "Ein Rechteck mit l = $b cm und b = $a cm - berechne die Fläche.";
                $loesung = $a * $b;
            }

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Geometrie: Umfang von Figuren". <br>
     * Wertebereich: [7, 100]
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateUmfang(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            $a = rand(2, 20);
            $b = rand($a + 1, 30);
            $c = rand(2, 30);
            $wahl =rand(0,2);
            if ($wahl === 0) {
                // --- QUADRAT ---
                $text = "Berechne den Umfang eines Quadrats mit a = $a cm.";
                $loesung = 4 * $a;
            } elseif ($wahl === 1) {
                // --- RECHTECK ---
                $text = "Ein Rechteck mit l = $b cm und b = $a cm - berechne den Umfang.";
                $loesung = 2 * ($a + $b);
            } else {
                // --- DREIECK ---
                $text = "Ein Dreieck mit a = $b cm, b = $a cm und c = $c cm - berechne den Umfang.";
                $loesung = $a + $b + $c;
            }

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Geometrie: Fläche von Dreiecken". <br>
     *  Wertebereich: [3, 300]
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateDreieckFlaeche(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            $g = rand(2, 20);
            $h = rand($g + 1, 30);

            $text = "Grundlinie = $g cm, Hoehe = $h cm.";
            $loesung = round(($g * $h) / 2,1);

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Geometrie: Kreise".
     *  Wertebereich: [12,56; 314]
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateKreise(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            $r = rand(2, 10);

            if (rand(0,1) === 1) {
                // --- UMFANG ---
                $text = "Radius = $r cm: Berechne den Umfang.";
                $loesung = round(2 * pi() * $r, 2);
            } else {
                // --- FLÄCHE ---
                $text = "Radius = $r cm: Berechne die Fläche.";
                $loesung = round(pi() * $r * $r, 2);
            }
            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Geometrie: Volumen von Quadern".
     *  Wertebereich: [4; 250]
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateVolumen(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            $l = rand(2, 10);
            $b = rand(2, 5);
            $h = rand(1, 5);

            $text = "Gegeben: l = $l cm, b = $b cm, h = $h cm. Berechne das Volumen.";
            $loesung = $b * $h * $l;

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Dezimalsystem".
     *  Wertebereich: [50.001; 999.999]
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateDezimalsystem(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {

            // ganzzahligen Teil
            $ganzzahl = rand(50, 999);

            // dezimalen Teil
            $dezimalzahl_int = rand(1, 999);
            $dezimalzahl_string = str_pad($dezimalzahl_int, 3, '0', STR_PAD_LEFT);

            $zahl_string = $ganzzahl . "." . $dezimalzahl_string;

            // --- Logik zur Erstellung der Lösung (Zerlegung) ---

            $zerlegung = [];
            $einheiten = [100, 10, 1, 0.1, 0.01, 0.001];

            // die Zahl in ihre einzelnen Ziffern zerlegen
            $ziffern = str_split(str_replace('.', '', $zahl_string));

            foreach ($ziffern as $index => $ziffer) {
                if ((int)$ziffer > 0) {
                    $einheit_str = (string)$einheiten[$index];
                    $zerlegung[] = "$ziffer x $einheit_str";
                }
            }

            $loesung = implode(" + ", $zerlegung);

            if (rand(0,1) === 1) {
                $text = "Zerlege $zahl_string in seine Bestandteile.";
            } else {
                $text = "Was bedeutet die Zahl $zahl_string im Dezimalsystem?";
            }

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Dezimalzahlen: Runden".
     * Wertebereich [1.001, 9.999]
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateDezimalzahlenRunden(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            $zahl_int = rand(1001, 9999);
            $zahl_original = $zahl_int / 1000;

            $dezimalstellen = rand(1, 2);

            $loesung = round($zahl_original, $dezimalstellen);

            $text = "Runde $zahl_original auf " . ($dezimalstellen == 1 ? 'eine' : 'zwei') . " Dezimalstellen.";

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Prozentrechnung".
     * Aufgabe 1: Prozentsatz zwischen 1-99, Ganzes zwischen 5-1000 (durch 5 teilbar).
     * Aufgabe 2: Teil zwischen 1-100, Ganzes zwischen 5-500 (durch 5 teilbar).
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateProzentrechnung(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            if (rand(0, 1) === 1) {
                $prozentsatz = rand(1, 99);
                // durch 5 teilbar
                $ganzes = rand(1, 200) * 5;
                $teil = round(($ganzes * $prozentsatz) / 100);

                $text = "Berechne, wie viel Prozent $teil von $ganzes sind.";
            } else {
                $ganzes = rand(1, 100) * 5;
                $teil = rand(1, 20) * 5;
                $prozentsatz = round(($teil / $ganzes) * 100);

                $text = "Wenn $teil von $ganzes Schülern Sport machen, wie viel Prozent sind das?";
            }

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $prozentsatz
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Zinsrechnung".
     * Wertebereich Kapital = [1000, 5000] Euro, durch 100 teilbar
     * Wertebereich Zinssatz = [2, 14] %
     * Wertebereich Zeit = [1, 10] Jahre
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateZinsrechnung(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            // durch 100 teilbar
            $kapital = rand(10, 50) * 100;
            $zinssatz = rand(2, 14);
            $zeit = rand(1, 10);
            $loesung = ($kapital * $zinssatz * $zeit) / 100;

            $text = "Kapital = $kapital Euro, Zinssatz = $zinssatz %, Zeit = $zeit Jahre.";

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Wahrscheinlichkeitsrechnung".
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateWahrscheinlichkeit(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            if (rand(0, 1) === 0) {
                // --- WÜRFEL ---
                $seiten = rand(4, 10);

                $ist_gerade = rand(0, 1);
                $ereignis_text = $ist_gerade ? 'gerade' : 'ungerade';

                // wenn Seiten=5 (1,2,3,4,5): Gerade (2,4) = 2; Ungerade (1,3,5) = 3
                $anzahl_gerade = floor($seiten / 2);
                $anzahl_ungerade = ceil($seiten / 2);

                $guenstige_faelle = $ist_gerade ? $anzahl_gerade : $anzahl_ungerade;

                $loesung = round($guenstige_faelle / $seiten, 2);

                $text = "Was ist die Wahrscheinlichkeit, bei einem $seiten-seitigen Würfel eine $ereignis_text Zahl zu würfeln?";

            } else {
                // --- KARTENSPIEL ---
                $gesamt_karten = rand(52, 100);

                // Farbe wählen
                $farben = ['rote', 'grüne', 'gelbe'];
                $farbe_text = $farben[array_rand($farben)];

                $karten_der_farbe = rand(2, $gesamt_karten - 1);

                $loesung = round($karten_der_farbe / $gesamt_karten, 2);

                $text = "Ziehe eine $farbe_text Karte aus einem Kartenspiel ($gesamt_karten Karten, $karten_der_farbe $farbe_text)";
            }

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }

    /**
     * Generiert 5 Aufgaben zum Thema "Graphen und Diagramme".
     * @return array Ein Array mit 5 Aufgaben, jeweils mit 'text' (Aufgabenstellung) und 'loesung'.
     */
    private static function generateGraphenDiagramme(): array {
        $aufgaben = [];
        for ($i = 1; $i <= 5; ++$i) {
            if (rand(0, 1) === 0) {
                // --- BALKENDIAGRAMM (Summe und Prozent) ---

                $wert_a = rand(10, 50) * 5; // Durch 5 teilbar
                $wert_b = rand(10, 50) * 5;
                $wert_c = rand(10, 50) * 5;

                $summe = $wert_a + $wert_b + $wert_c;

                // einen der Werte zur Prozentberechnung wählen
                $wert_prozent = [$wert_a, $wert_b, $wert_c][array_rand([0, 1, 2])];

                // Prozentsatz auf zwei Nachkommastellen runden
                $prozentsatz = round(($wert_prozent / $summe) * 100, 2);
                $kategorien = ["Hunde", "Katzen", "Fische"];
                $kategorie_prozent = $kategorien[array_rand($kategorien)];

                $loesung = round($prozentsatz);
                $text = "In einer Umfrage wurden die Lieblingstiere der Klasse gezählt: $kategorien[0]: $wert_a, $kategorien[1]: $wert_b und $kategorien[2]: $wert_c. Wie viel Prozent aller Stimmen entfallen auf $kategorie_prozent?";

            } else {
                // --- KREISDIAGRAMM (Gradzahl des Winkels) ---

                $gesamt = rand(50, 200); // Gesamtzahl der Elemente
                $teil = rand(10, $gesamt - 10); // Anzahl der Elemente für die Kategorie

                // Berechnung des Winkels: (Teil / Gesamt) * 360 Grad
                $winkel = ($teil / $gesamt) * 360;

                $kategorie_name = ["Hunde", "Katzen", "Fische"][array_rand([0,1,2])];

                $loesung = round($winkel);
                $text = "In einer Klasse wurden Lieblingstiere gefragt. Von $gesamt Kindern mögen $teil Kinder am liebsten $kategorie_name. Wenn du ein Kreisdiagramm zeichnest, wie groß muss der Kuchenwinkel für die $kategorie_name sein?";
            }

            $aufgaben[$i] = [
                "text" => $text,
                "loesung" => $loesung
            ];
        }
        return $aufgaben;
    }
}

/**
 * Hilfsfunktion yur Berechnung von Größtem Gemeinsamen Teiler.
 * @param int $a Zahl a
 * @param int $b Zahl b
 * @return int größter gemeinsame Teiler.
 */
function ggt(int $a, int $b): int {
    while ($b !== 0) {
        $temp = $b;
        $b = $a % $b;
        $a = $temp;
    }
    return $a;
}