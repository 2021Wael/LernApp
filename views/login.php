<?php
// login.php
// Session already started in index.php, so we don't call session_start() here.

// simple CSRF token (store in session)
if (empty($_SESSION['csrf_login'])) {
    $_SESSION['csrf_login'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_login'];

$err = $_GET['err'] ?? '';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Login — Lern App</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root{
            --btn-bg:#236C93; --btn-text:#fff; --bg:#f1f7fa; --card:#fff;
            --text:#12323a; --radius:10px;
        }
        *{box-sizing:border-box}
        body{font-family:Verdana,Arial,sans-serif;background:var(--bg);color:var(--text);margin:0;padding:24px;display:flex;align-items:center;justify-content:center;height:100vh}
        .card{width:100%;max-width:420px;background:var(--card);border-radius:12px;padding:20px;box-shadow:0 8px 30px rgba(0,0,0,0.08)}
        h1{margin:0 0 12px;font-size:1.1rem;color:var(--btn-bg);text-align:center}
        .form-row{margin-bottom:12px}
        input[type="text"],input[type="password"]{width:100%;padding:10px;border-radius:8px;border:1px solid #e3e8ec;font-size:1rem}
        button.submit{width:100%;padding:10px;border-radius:8px;border:0;background:var(--btn-bg);color:var(--btn-text);font-weight:700;cursor:pointer}
        .note{font-size:0.9rem;color:#7b8992;margin-top:8px;text-align:center}
        .error{background:#ffe6e6;color:#8a1212;padding:8px;border-radius:6px;margin-bottom:8px}
        .small{font-size:0.85rem;color:#6b7780}
    </style>
</head>
<body>
<div class="card" role="main">
    <h1>Anmeldung</h1>

    <?php if ($err): ?>
        <div class="error"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form method="post" action="../public/index.php?page=process_login" autocomplete="off" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-row">
            <label for="username" class="small">Benutzername</label>
            <input id="username" name="username" type="text" required autofocus>
        </div>

        <div class="form-row">
            <label for="password" class="small">Passwort</label>
            <input id="password" name="password" type="password" required>
        </div>

        <div class="form-row">
            <button type="submit" class="submit">Einloggen</button>
        </div>

        <div class="note">Sie werden nach erfolgreichem Login automatisch an Ihren Bereich weitergeleitet.</div>
    </form>
</div>
</body>
</html>