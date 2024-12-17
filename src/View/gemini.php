<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Gemini Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            margin: 10px;
            padding: 15px;
        }

        .card h2 {
            margin-top: 0;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            background-color: #f5f5f5;
            border-radius: 3px;
            margin-bottom: 5px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>AI Gemini Chat</h1>

        <form method="POST" action="<?= base_url()?>/gemini">
            <?= csrf() ?>
            <label for="question">Tanyakan sesuatu:</label><br>
            <textarea name="question" id="question" rows="4" cols="50" required></textarea><br>
            <button type="submit">Kirim</button>
        </form>

    <?php if (isset($response)): ?>
        <div class="card">
            <h2>Jawaban:</h2>
            <ul>
                <?php foreach ($response as $text): ?>
                    <li><?= htmlspecialchars(is_array($text) ? implode(', ', $text) : $text) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (isset($error)): ?>
        <div class="card">
            <h2>Terjadi Kesalahan:</h2>
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>
</body>
</html>
