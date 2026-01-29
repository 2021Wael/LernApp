<?php $allowed_tags = '<b><br><sup>';?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/CSS/komponente.css">
    <link rel="stylesheet" href="/CSS/style.css">
    <link rel="stylesheet" href="/CSS/navigationsbuttons.css">
    <title><?= htmlspecialchars($thema['titel']) ?> - Lern App</title>
</head>
<body>
<!-- Accessibility Menu -->
<button id="menuBtn" aria-label="Barrierefreiheits-Menü öffnen">
    <img src="/images/menu-icon.png" alt="Menü" width="100" height="100">
</button>
<div id="cursorCircle"></div>
<div id="menu" class="menu hidden" aria-hidden="true">
    <div>
        <div><label for="fontSlider">Schriftgröße (%)</label></div>
        <input id="fontSlider" type="range" min="50" max="200" value="100">
    </div>
    <div><button id="resetFont">Zurücksetzen</button></div>
    <div><button id="contrastBtn">Hoher Kontrast</button></div>
    <div><button id="toggleCircleBtn">Mauszeiger Highlight</button></div>
</div>

<!-- Header -->
<header class="obere_teil">
    <div class="topmenu">
        <img src="/images/logo.jpg" alt="Lern App Logo" class="logo">
        <h2><?= htmlspecialchars($thema['bereich_name']) ?></h2>
        <div class="button-container">
            <button class="button" onclick="window.location.href=<?php if(!$isTeacher):?>'/public/index.php'<?php else:?>'/public/index.php?page=teacher_dashboard'<?php endif?>"
                    aria-label="Zurück zur Startseite" title="Startseite">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 1024 1024">
                    <path d="M946.5 505L560.1 118.8l-25.9-25.9a31.5 31.5 0 0 0-44.4 0L77.5 505a63.9 63.9 0 0 0-18.8 46c.4 35.2 29.7 63.3 64.9 63.3h42.5V940h691.8V614.3h43.4c17.1 0 33.2-6.7 45.3-18.8a63.6 63.6 0 0 0 18.7-45.3c0-17-6.7-33.1-18.8-45.2zM568 868H456V664h112v204zm217.9-325.7V868H632V640c0-22.1-17.9-40-40-40H432c-22.1 0-40 17.9-40 40v228H238.1V542.3h-96l370-369.7 23.1 23.1L882 542.3h-96.1z"></path>
                </svg>
            </button>
        </div>
    </div>
    <h1 class="titel"><?= htmlspecialchars($thema['titel']) ?></h1>
</header>

<!-- Main Content -->
<main>
    <div class="infos">
        <!-- Erklärungen Section -->
        <?php if (!empty($erklaerungen) || ($isTeacher)): ?>
        <section class="content-section">
            <h2>🧠 Erklärungen</h2>

                <?php foreach ($erklaerungen as $erklaerung): ?>
                    <div class="erklärung" id="erklaerung-<?= $erklaerung['id'] ?>">
                        <p><?= nl2br(strip_tags($erklaerung['text'],$allowed_tags)) ?></p>

                        <?php if ($isTeacher): ?>
                            <!-- Edit Form (hidden by default) -->
                            <form method="post" class="edit-form" id="edit-erklaerung-<?= $erklaerung['id'] ?>" style="display: none;">
                                <input type="hidden" name="action" value="edit_erklaerung">
                                <input type="hidden" name="id" value="<?= $erklaerung['id'] ?>">
                                <textarea name="text" rows="4" required><?= htmlspecialchars($erklaerung['text']) ?></textarea>
                                <div class="form-buttons">
                                    <button type="submit" class="save-btn">Speichern</button>
                                    <button type="button" class="cancel-btn"
                                            onclick="hideForm('edit-erklaerung-<?= $erklaerung['id'] ?>')">
                                        Abbrechen
                                    </button>
                                </div>
                            </form>

                            <!-- Action Buttons -->
                            <div class="edit-buttons">
                                <button class="edit-btn" onclick="showForm('edit-erklaerung-<?= $erklaerung['id'] ?>')">
                                    Bearbeiten
                                </button>
                                <button class="delete-btn"
                                        onclick="confirmDelete('<?= $erklaerung['id'] ?>', 'erklaerung', 'Erklärung')">
                                    Löschen
                                </button>
                            </div>

                            <!-- Hidden Delete Form -->
                            <form method="post" id="delete-erklaerung-<?= $erklaerung['id'] ?>" style="display: none;">
                                <input type="hidden" name="action" value="delete_erklaerung">
                                <input type="hidden" name="id" value="<?= $erklaerung['id'] ?>">
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php if($isTeacher && empty($erklaerung)): ?>
                <p class="no-content">Noch keine Erklärungen vorhanden.</p>
            <?php endif; ?>

            <?php if ($isTeacher): ?>
                <!-- Add New Explanation Button -->
                <button class="add-btn" onclick="showForm('new-erklaerung-form')">
                    + Neue Erklärung hinzufügen
                </button>

                <!-- New Explanation Form (hidden by default) -->
                <form method="post" class="edit-form" id="new-erklaerung-form" style="display: none;">
                    <input type="hidden" name="action" value="new_erklaerung">
                    <input type="hidden" name="thema_id" value="<?= $themaId ?>">
                    <textarea name="text" rows="4" placeholder="Neue Erklärung eingeben..." required></textarea>
                    <div class="form-buttons">
                        <button type="submit" class="save-btn">Hinzufügen</button>
                        <button type="button" class="cancel-btn" onclick="hideForm('new-erklaerung-form')">
                            Abbrechen
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </section>
        <?php endif; ?>
        <!-- Formeln Section -->
        <?php if (!empty($formeln) || $isTeacher): ?>
            <section class="content-section">
                <h2>📐 Formeln</h2>
                <?php foreach ($formeln as $formel): ?>
                    <div class="formel" id="formel-<?= $formel['id'] ?>">
                        <p><?= nl2br(strip_tags($formel['text'],$allowed_tags)) ?></p>

                        <?php if ($isTeacher): ?>
                            <!-- Edit Form for Example -->
                            <form method="post" class="edit-form" id="edit-formel-<?= $formel['id'] ?>" style="display: none;">
                                <input type="hidden" name="action" value="edit_formel">
                                <input type="hidden" name="id" value="<?= $formel['id'] ?>">
                                <textarea name="text" rows="4" required><?= htmlspecialchars($formel['text']) ?></textarea>
                                <div class="form-buttons">
                                    <button type="submit" class="save-btn">Speichern</button>
                                    <button type="button" class="cancel-btn"
                                            onclick="hideForm('edit-beispiel-<?= $formel['id'] ?>')">
                                        Abbrechen
                                    </button>
                                </div>
                            </form>

                            <!-- Action Buttons -->
                            <div class="edit-buttons">
                                <button class="edit-btn" onclick="showForm('edit-formel-<?= $formel['id'] ?>')">
                                    Bearbeiten
                                </button>
                                <button class="delete-btn"
                                        onclick="confirmDelete('<?= $formel['id'] ?>', 'formel', 'Formel')">
                                    Löschen
                                </button>
                            </div>

                            <!-- Hidden Delete Form -->
                            <form method="post" id="delete-formel-<?= $formel['id'] ?>" style="display: none;">
                                <input type="hidden" name="action" value="delete_formel">
                                <input type="hidden" name="id" value="<?= $formel['id'] ?>">
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if($isTeacher && empty($formel)): ?>
                    <p class="no-content">Noch keine Formel vorhanden.</p>
                <?php endif; ?>

                <?php if ($isTeacher): ?>
                    <!-- Add New Example Button -->
                    <button class="add-btn" onclick="showForm('new-formel-form')">
                        + Neues Formel hinzufügen
                    </button>

                    <!-- New Example Form -->
                    <form method="post" class="edit-form" id="new-formel-form" style="display: none;">
                        <input type="hidden" name="action" value="new_formel">
                        <input type="hidden" name="thema_id" value="<?= $themaId ?>">
                        <textarea name="text" rows="4" placeholder="Neues Formel eingeben..." required></textarea>
                        <div class="form-buttons">
                            <button type="submit" class="save-btn">Hinzufügen</button>
                            <button type="button" class="cancel-btn" onclick="hideForm('new-formel-form')">
                                Abbrechen
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </section>
        <?php endif; ?>
        <!-- Beispiele Section -->
        <?php if (!empty($beispiele) || ($isTeacher)): ?>
        <section class="content-section">
            <h2>🧮 Beispiele</h2>
                <?php foreach ($beispiele as $beispiel): ?>
                    <div class="beispiel" id="beispiel-<?= $beispiel['id'] ?>">
                        <p><?= nl2br(strip_tags($beispiel['text'],$allowed_tags)) ?></p>

                        <?php if (!empty($beispiel['bild'])): ?>
                            <div class="image-container">
                                <img src="/images/<?= htmlspecialchars($beispiel['bild']) ?>"
                                     alt="Beispielbild" height="250">
                            </div>
                        <?php endif; ?>

                        <?php if ($isTeacher): ?>
                            <!-- Edit Form for Example -->
                            <form method="post" class="edit-form" id="edit-beispiel-<?= $beispiel['id'] ?>" style="display: none;">
                                <input type="hidden" name="action" value="edit_beispiel">
                                <input type="hidden" name="id" value="<?= $beispiel['id'] ?>">
                                <textarea name="text" rows="4" required><?= htmlspecialchars($beispiel['text']) ?></textarea>
                                <div class="form-buttons">
                                    <button type="submit" class="save-btn">Speichern</button>
                                    <button type="button" class="cancel-btn"
                                            onclick="hideForm('edit-beispiel-<?= $beispiel['id'] ?>')">
                                        Abbrechen
                                    </button>
                                </div>
                            </form>

                            <!-- Action Buttons -->
                            <div class="edit-buttons">
                                <button class="edit-btn" onclick="showForm('edit-beispiel-<?= $beispiel['id'] ?>')">
                                    Bearbeiten
                                </button>
                                <button class="delete-btn"
                                        onclick="confirmDelete('<?= $beispiel['id'] ?>', 'beispiel', 'Beispiel')">
                                    Löschen
                                </button>
                            </div>

                            <!-- Hidden Delete Form -->
                            <form method="post" id="delete-beispiel-<?= $beispiel['id'] ?>" style="display: none;">
                                <input type="hidden" name="action" value="delete_beispiel">
                                <input type="hidden" name="id" value="<?= $beispiel['id'] ?>">
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php if($isTeacher && empty($beispiel)): ?>
                <p class="no-content">Noch keine Beispiele vorhanden.</p>
            <?php endif; ?>

            <?php if ($isTeacher): ?>
                <!-- Add New Example Button -->
                <button class="add-btn" onclick="showForm('new-beispiel-form')">
                    + Neues Beispiel hinzufügen
                </button>

                <!-- New Example Form -->
                <form method="post" class="edit-form" id="new-beispiel-form" style="display: none;">
                    <input type="hidden" name="action" value="new_beispiel">
                    <input type="hidden" name="thema_id" value="<?= $themaId ?>">
                    <textarea name="text" rows="4" placeholder="Neues Beispiel eingeben..." required></textarea>
                    <div class="form-buttons">
                        <button type="submit" class="save-btn">Hinzufügen</button>
                        <button type="button" class="cancel-btn" onclick="hideForm('new-beispiel-form')">
                            Abbrechen
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </section>
        <?php elseif(empty($beispiele) && empty($formeln) && empty($erklaerungen)): ?>
            <p class="no-content">Baustelle: Diese Seite wird aktuell weiterentwickelt und bald aktualisiert.</p>
        <?php endif; ?>
    </div>

    <!-- Übungsaufgaben Section -->
    <?php if ($themaId >= 1 && $themaId <= 15): ?>
    <div class="übung">
        <h2>🧩 Übungsaufgaben</h2>
        <?php if(empty($aufgaben) && !$isTeacher): ?>
        <p class="no-content">Baustelle: Diese Seite wird aktuell weiterentwickelt und bald aktualisiert.</p>
        <?php else: ?>
        <form method="post">
            <?php foreach ($aufgaben as $id => $daten): ?>
                <?php
                $class = "";
                $showSolution = false;

                if (isset($exercise_results)) {
                    $class = "correct";
                    $showSolution = false;

                    foreach ($exercise_results['fehler'] as $err) {
                        if (strpos($err, "Aufgabe $id") !== false) {
                            $class = "wrong";
                            $showSolution = true;
                            break;
                        }
                    }
                }
                ?>

                <div class="aufgabe">
                    <p><?= $id . ') ' . htmlspecialchars($daten["text"]) ?>
                        <span class="loesung"><?php if($class == 'wrong') {
                                echo "Lösung: {$aufgaben[$id]['loesung']}"; } ?></span>
                    </p>

                    <input
                            class="<?= $class ?>"
                            type="text"
                            name="aufgabe_<?= $id ?>"
                            placeholder="Ergebnis:"
                            value="<?= htmlspecialchars($aufgaben_antwort["aufgabe_$id"] ?? '') ?>"
                    >
                </div>
            <?php endforeach; ?>

            <div class="pruf_gener_container">
                <div>
                    <button class="pruf_gener_button" type="submit">
                        Antworten überprüfen
                    </button>
                </div>
                <div>
                    <button class="pruf_gener_button" type="submit" name="regen" value="1">
                        Neue Aufgaben generieren
                    </button>
                </div>
            </div>
        </form>

        <!-- Exercise Results -->
        <?php if (isset($exercise_results)): ?>
            <div class="error">
                <?php
                $richtigaufgaben = $exercise_results['aufgabenanzahl'] - count($exercise_results['fehler']);
                ?>
                <div><strong>Ergebnis:</strong> <?= $richtigaufgaben ?>/<?= $exercise_results['aufgabenanzahl'] ?> Aufgaben richtig</div>
                <?php if ($exercise_results['alles_richtig']): ?>
                    <div class="success">SUPER! Alle Aufgaben richtig gelöst! 🎉</div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="error"></div>
        <?php endif; ?>
<?php endif; ?>
        <!-- PDF Download -->
            <?php if (!empty($pdf_datei)): ?>
                <a href="/pdfs/<?= htmlspecialchars($pdf_datei) ?>"
                   download
                   class="pdf_button"
                   aria-label="PDF zu Thema <?= $thema['titel'] ?> herunterladen">
                    PDF herunterladen
                </a>
            <?php endif; ?>
        <?php endif;?>
    </div>
</main>

<!-- Navigation Footer -->
<footer class="nav-button">
    <?php if ($prevThema): ?>
        <button class="prev"
                onclick="window.location.href='/public/index.php?page=thema&id=<?= $prevThema['id'] ?>'"
                aria-label="Vorheriges Thema: <?= htmlspecialchars($prevThema['titel']) ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 66 43">
                <polygon points="26.42,0 30.95,4.46 13.59,21.5 30.95,38.54 26.42,43 4.53,21.5"></polygon>
                <polygon points="46.21,0 50.74,4.46 33.38,21.5 50.74,38.54 46.21,43 24.32,21.5"></polygon>
                <polygon points="66,0 70.53,4.46 53.17,21.5 70.53,38.54 66,43 44.11,21.5"></polygon>
            </svg>
            <span><?= htmlspecialchars($prevThema['titel']) ?></span>
        </button>
    <?php endif; ?>

    <?php if ($nextThema): ?>
        <button class="next"
                onclick="window.location.href='/public/index.php?page=thema&id=<?= $nextThema['id'] ?>'"
                aria-label="Nächstes Thema: <?= htmlspecialchars($nextThema['titel']) ?>">
            <span><?= htmlspecialchars($nextThema['titel']) ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 66 43">
                <polygon points="39.58,4.46 44.11,0 66,21.5 44.11,43 39.58,38.54 56.94,21.5"></polygon>
                <polygon points="19.79,4.46 24.32,0 46.21,21.5 24.32,43 19.79,38.54 37.15,21.5"></polygon>
                <polygon points="0,4.46 4.53,0 26.42,21.5 4.53,43 0,38.54 17.36,21.5"></polygon>
            </svg>
        </button>
    <?php endif; ?>
</footer>

<script src="/scripts/barrierfreiheit.js"></script>
<script>
    // Form handling functions
    function showForm(formId) {
        document.getElementById(formId).style.display = 'block';
    }

    function hideForm(formId) {
        document.getElementById(formId).style.display = 'none';
    }

    function confirmDelete(id, type, name) {
        if (confirm(`Möchten Sie diese ${name} wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.`)) {
            document.getElementById(`delete-${type}-${id}`).submit();
        }
    }

    // Clear exercise results when generating new exercises
    document.addEventListener('DOMContentLoaded', function() {
        const regenButton = document.querySelector('button[name="regen"]');
        if (regenButton) {
            regenButton.addEventListener('click', function() {
                // Clear any existing exercise results
                const errorDiv = document.querySelector('.error');
                if (errorDiv) {
                    errorDiv.innerHTML = '';
                }

                // Reset input fields
                document.querySelectorAll('.aufgabe input').forEach(input => {
                    input.value = '';
                    input.className = '';
                });
            });
        }
    });
</script>
</body>
</html>