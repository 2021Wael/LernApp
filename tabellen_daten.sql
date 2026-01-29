USE swedatenbank;

-- Bereich Mathematik erstellen
INSERT INTO bereiche(titel) VALUE('Mathematik');

-- Alle Themen hinzufügen (für den Bereich Mathematik)
INSERT INTO themen(titel, bereich_id) VALUES
                                          ('Addition und Subtraktion', 1),
                                          ('Multiplikation und Division', 1),
                                          ('Grundlagen der Bruchrechnung', 1),
                                          ('Brüche erweitern und kürzen', 1),
                                          ('Flächeninhalt von Figuren', 1),
                                          ('Umfang von Figuren', 1),
                                          ('Flächeninhalt von Dreiecken', 1),
                                          ('Umfang und Fläche eines Kreises', 1),
                                          ('Volumen von Quadern', 1),
                                          ('Grundbegriffe des Dezimalsystems', 1),
                                          ('Runden von Dezimalzahlen', 1),
                                          ('Grundlegende Prozentrechnung', 1),
                                          ('Einfache Zinsrechnung', 1),
                                          ('Wahrscheinlichkeitsrechnung', 1),
                                          ('Graphen und Diagramme', 1);

-- Erklärungen hinzufügen
INSERT INTO erklaerungen(themaId, text) VALUES
                                            (1, 'Bei der Addition und Subtraktion grosser Zahlen hilft es, die Zahlen stellengerecht untereinander zu schreiben.'),
                                            (2, 'Bei der Multiplikation und Division grosser Zahlen ist es wichtig, Schritt für Schritt vorzugehen. Nutze das Einmaleins als Grundlage.'),
                                            (3, 'Ein Bruch zeigt einen Teil eines Ganzen. Der Zähler steht oben und der Nenner unten.'),
                                            (4, 'Zum Erweitern multipliziere Zähler und Nenner mit der gleichen Zahl. Zum Kürzen dividiere sie durch den größten gemeinsamen Teiler.'),
                                            (5, 'Die Fläche eines Rechtecks oder Quadrats berechnet sich durch <b>Länge × Breite</b>.'),
                                            (6, 'Der Umfang einer Figur berechnet sich durch das <b>Addieren aller Seiten der Figur</b>.'),
                                            (7, 'Der Flächeninhalt eines Dreiecks berechnet sich durch <b>(Grundseite g x Höhe h) / 2</b>.'),
                                            (8, 'Der Umfang eines Kreises ist <b>2 x pi x Radius</b>.<br>Der Fläche eines Kreises ist <b>pi x Radius x Radius</b>'),
                                            (9, 'Der Volumen von Quadern ist <b>Länge × Breite × Höhe</b>.'),
                                            (10, 'Das Dezimalsystem verwendet <b>die Basis 10</b>. Jede <b>Stelle</b> repräsentiert <b>eine Potenz von 10</b>.'),
                                            (11, 'Runde immer zur nächsten Zahl auf oder ab, je nachdem, ob die Zahl <b>über</b> oder <b>unter 5</b> liegt.'),
                                            (12, 'In der Prozentrechnung berechnest du den Anteil (Prozentwert), den Grundwert oder den Prozentsatz.'),
                                            (15, 'Graphen und Diagramme helfen uns, <b>Zahlen schnell zu verstehen</b> und zu vergleichen. Sie zeigen, wie oft etwas vorkommt (z.B. Lieblingsfarben) oder wie sich eine Menge entwickelt (z.B. gesparte Euro).<br>Wichtige Begriffe:<br>- Balkendiagramm: Zum Vergleich von Mengen (Wer hat mehr?).<br>- Kreisdiagramm: Zum Anzeigen von Anteilen (Wie viel Prozent vom Ganzen?).<br>- Achsen: Die Linien, die messen (z.B. eine Achse für die Namen, eine für die Menge).');

-- Formeln hinzufügen
INSERT INTO formeln(themaId, text) VALUES
                                       (5, '<b>A = Länge × Breite</b>'),
                                       (6, '<b>U = 4 x a (Quadrate)</b><br><b>U = 2 x (a + b) (Rechtecke)</b><br><b>U = a + b + c (Dreiecke)</b>'),
                                       (7, '<b>A = (g x h) / 2</b>'),
                                       (8, '<b>U = 2 x pi × Radius</b><br><b>A = 2 x Radius x Radius</b>'),
                                       (9, '<b>L = l × b × h</b>'),
                                       (10, '<b>Zahl</b> = (Ziffer<b><sub>n</sub></b> x 10<b><sup>n</sup></b>) + (Ziffer<b><sub>n-1</sub></b> x 10<b><sup>n-1</sup></b>) + ... + (Ziffer<b><sub>1</sub></b> x 10<b><sup>1</sup></b>) + (Ziffer<b><sub>0</sub></b> x 10<b><sup>0</sup></b>) + (Ziffer<b><sub>-1</sub></b> x 10<b><sup>-1</sup></b>) + ...'),
                                       (12, '<b>Prozentwert = Grundwert × Prozentsatz / 100</b>'),
                                       (13, 'Die Grundformel der Zinsrechnung lautet:<br><b>Zinsen = Kapital x Zinssatz x Zeit / 100</b>'),
                                       (14, 'Die Wahrscheinlichkeit eines Ereignisses berechnet sich mit: <b>P(E) = günstige Fälle / mögliche Fälle.</b>');

-- Beispiele hinzufügen
INSERT INTO beispiele(themaId, text, bild) VALUES
                                               (1, '<b>7421</b> + <b>5634</b> = <b>13.055</b> und <b>9876</b> - <b>1234</b> = <b>8642</b>', null),
                                               (2, '<b>234</b> x <b>12</b> = <b>2808</b> und <b>144</b> / <b>12</b> = <b>12</b>.', null),
                                               (3, '1/2 + 1/4 = 3/4', null),
                                               (4, '2/4 = 1/2 und 3/6 = 1/2', null),
                                               (5, 'Ein Rechteck mit <b>L = 5 cm</b> und <b>B = 3 cm</b> hat eine Fläche von <b>15 cm<sup>2</sup></b>.', 'u5.jpg'),
                                               (6, 'Ein Quadrat mit <b>a = 5 cm</b> hat einen Umfang von <b>20 cm</b>.<br>Ein Rechteck mit <b>a = 8 cm</b> und <b>b = 4 cm</b> hat einen Umfang von <b>24 cm</b>.', 'geo_umfang_von_figuren.jpg'),
                                               (7, 'Ein Dreieck mit <b>g = 8 cm</b> und <b>h = 3 cm</b> hat einen Flächeninhalt von <b>12 cm<sup>2</sup></b>.', 'geo_flaeche_von_dreiecken.jpg'),
                                               (8, 'Ein Kreis mit <b>R = 3 cm</b> hat ein Umfang von <b>19 cm</b> und Fläche von <b>28 cm<sup>2</sup></b>.', 'geo_kreise.png'),
                                               (9, 'Ein Quader mit <b>l = 5 cm, b= 3 cm und h= 2 cm</b> hat ein Volumen von <b>30 cm<sup>3</sup></b>.', 'geo_volumen_von_quadern.jpg'),
                                               (10, 'Die Zahl <b>435.12</b> bedeutet: <b>4</b> x <b>100</b> + <b>3</b> x <b>10</b> + <b>5</b> x <b>1</b> + <b>1</b> x <b>0.1</b> + <b>2</b> x <b>0.01</b>', null),
                                               (11, '7.119 auf <b>eine</b> Dezimalstelle gerundet ist: 7.1', null),
                                               (11, '3.146 auf <b>zwei</b> Dezimalstellen gerundet ist: 3.15', null),
                                               (12, '30 % von 200 € sind 200 € × 30 % / 100 = <b>60 €</b>.', null),
                                               (12, '20 von 100 Schülern haben eine Eins: (20/100) x 100 = <b>20 %</b>', null),
                                               (13, 'Ein Kapital von 1000 Euro bei 5 % Zinsen für 1 Jahr: <b>Zinsen = (1000 x 5 x 1) / 100 = 50 Euro</b>', null),
                                               (14, 'Beim Würfeln ist die Wahrscheinlichkeit, eine „6“ zu würfeln:<br>1/6 ≈ 0.1667 = 16,7 %', null),
                                               (15, 'Wenn 5 Schüler Hunde und 3 Schüler Katzen mögen, kann man das in einem <b>Balkendiagramm</b> darstellen: Der Balken für \'Hund\' ist höher als der für \'Katze\'.<br><b>Oder als Kreisdiagramm:</b> Die Hundeliebhaber bekommen ein größeres Kuchenstück (Anteil) als die Katzenliebhaber.', 'graphen_diagramme.jpg');


