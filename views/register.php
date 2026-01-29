<?php
// register.php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
// CSRF token
if (empty($_SESSION['csrf_register'])) {
    $_SESSION['csrf_register'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_register'];

// optional: show message
$message = $_GET['m'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Registrieren — Lehrkraft</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root{
            --btn-bg:#236C93; --btn-text:#fff; --bg:#f1f7fa; --card:#fff; --text:#12323a;
        }
        *{box-sizing:border-box}
        body{font-family:Verdana,Arial,sans-serif;background:var(--bg);margin:0;display:flex;align-items:center;justify-content:center;height:100vh}
        .card{width:100%;max-width:480px;background:var(--card);border-radius:12px;padding:20px;box-shadow:0 8px 30px rgba(0,0,0,0.08)}
        h1{margin:0 0 12px;color:var(--btn-bg);text-align:center}
        .form-row{margin-bottom:10px}
        label{display:block;font-size:0.9rem;margin-bottom:6px}
        input[type="text"],input[type="password"]{width:100%;padding:10px;border-radius:8px;border:1px solid #e3e8ec;font-size:1rem}
        .btn {width:100%;padding:10px;border-radius:8px;border:0;background:var(--btn-bg);color:var(--btn-text);font-weight:700;cursor:pointer}
        .small{font-size:0.85rem;color:#6b7780}
        .error{background:#ffe6e6;color:#8a1212;padding:8px;border-radius:6px;margin-bottom:8px}
        .note{font-size:0.9rem;color:#6b7780;margin-top:8px}
        .row{display:flex;gap:8px}
    </style>
</head>
<body>
<div class="card" role="main">
    <h1>Lehrer Registrierung</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($message): ?>
        <div class="note"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form id="regForm" action="../public/index.php?page=process_register" method="post" autocomplete="off" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-row">
            <label for="username">Benutzername</label>
            <input id="username" name="username" type="text" maxlength="50" required>
        </div>

        <div class="form-row">
            <label for="password">Passwort</label>
            <input id="password" name="password" type="password" minlength="6" required>
        </div>

        <div class="form-row">
            <label for="password2">Passwort wiederholen</label>
            <input id="password2" name="password2" type="password" minlength="6" required>
        </div>


        <div class="form-row">
            <button class="btn" type="submit">Registrieren</button>
        </div>

        <div style="display:flex;gap:8px;justify-content:center;margin-top:6px">
            <a href="../public/index.php?page=login" class="small">Hast du bereits ein Konto? Anmelden</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('regForm').addEventListener('submit', function(e){
        const a = document.getElementById('password').value;
        const b = document.getElementById('password2').value;
        if (a !== b) {
            e.preventDefault();
            alert('Passwörter stimmen nicht überein.');
            return false;
        }
        if (a.length < 6) {
            e.preventDefault();
            alert('Passwort mindestens 6 Zeichen.');
            return false;
        }
    });
</script>
</body>
</html>
