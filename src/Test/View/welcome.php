<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Willkommen</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #e9f5ff;
            padding: 2rem;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px #bbb;
        }
        .logout-button {
            float: right;
            padding: 0.5rem 1rem;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: -2rem;
        }
        h1 {
            margin-bottom: 0.5rem;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }
        .log-table th,
        .log-table td {
            border: 1px solid #ccc;
            padding: 0.5rem;
            text-align: left;
        }
        .log-table th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="GET" action="">
            <button type="submit" name="action" value="logout" class="logout-button">Logout</button>
        </form>

        <h1>Willkommen, <?= htmlspecialchars($username) ?>!</h1>

        <?php
            $last = new DateTime($lastlogin);
            $now = new DateTime();
            $diff = $last->diff($now);
            $delta = [];

            if ($diff->y) $delta[] = $diff->y . ' Jahr(e)';
            if ($diff->m) $delta[] = $diff->m . ' Monat(e)';
            if ($diff->d) $delta[] = $diff->d . ' Tag(e)';
            if ($diff->h) $delta[] = $diff->h . ' Stunde(n)';
            if ($diff->i) $delta[] = $diff->i . ' Minute(n)';

            $deltaStr = count($delta) > 0 ? implode(', ', $delta) : 'weniger als eine Minute';
        ?>

        <p>Letzter Login war vor: <strong><?= $deltaStr ?></strong></p>

        <h2>Aktionsprotokoll</h2>
        <?php if (!empty($logEntries)): ?>
            <table class="log-table">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logEntries as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['date']) ?></td>
                            <td><?= htmlspecialchars($log['action']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Keine protokollierten Aktionen gefunden.</p>
        <?php endif; ?>
    </div>
</body>
</html>
