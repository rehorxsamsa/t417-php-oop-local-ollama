<?php

declare(strict_types=1);

require __DIR__ . '/../src/autoload.php';

use App\ExampleRegistry;
use App\OllamaClient;

$registry = new ExampleRegistry();

// --- JSON API endpoint: POST /?api=run ------------------------------------
if (($_GET['api'] ?? '') === 'run') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $body  = json_decode(file_get_contents('php://input') ?: '[]', true);
        $id    = (string) ($body['id'] ?? '');
        $input = trim((string) ($body['input'] ?? ''));

        $example = $registry->get($id);
        if ($example === null) {
            throw new RuntimeException('Neznámý příklad.');
        }
        if ($input === '') {
            throw new RuntimeException('Zadej vstupní text.');
        }

        $llm    = OllamaClient::fromEnv();
        $result = $example->run($llm, $input);

        echo json_encode(['ok' => true, 'result' => $result], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// --- HTML stránka ----------------------------------------------------------
$examples = $registry->all();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP-OOP + lokální LLM (Ollama)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small text-uppercase mb-1" style="letter-spacing:.05em">t417-php-oop-local-ollama</div>
                    <h1 class="h3 mb-1">🦙 PHP-OOP + lokální LLM</h1>
                    <p class="text-muted">Čisté PHP 8.2 bez frameworku · Ollama · Docker · model
                        <code><?= htmlspecialchars(getenv('LLM_MODEL') ?: 'qwen2.5:0.5b') ?></code>
                    </p>
                </div>
                <a href="kviz.php" class="btn btn-outline-primary btn-sm">🧠 Kvíz</a>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">1. Vyber příklad</label>
                    <select id="example" class="form-select mb-3">
                        <?php foreach ($examples as $id => $ex): ?>
                            <option value="<?= $id ?>" data-desc="<?= htmlspecialchars($ex->description()) ?>">
                                <?= $id ?>) <?= htmlspecialchars($ex->title()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p id="desc" class="text-muted small"></p>

                    <label class="form-label fw-semibold">2. Vstupní text</label>
                    <textarea id="input" class="form-control mb-3" rows="4"
                        placeholder="Napiš sem text…"></textarea>

                    <button id="run" class="btn btn-primary">Spustit ▶</button>
                    <span id="spinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
                </div>
            </div>

            <div id="resultCard" class="card shadow-sm d-none">
                <div class="card-body">
                    <h2 class="h6 text-muted">Výsledek</h2>
                    <pre id="output" class="bg-dark text-light p-3 rounded" style="white-space:pre-wrap"></pre>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const sel = document.getElementById('example');
const desc = document.getElementById('desc');
const updateDesc = () => desc.textContent = sel.selectedOptions[0].dataset.desc;
sel.addEventListener('change', updateDesc);
updateDesc();

document.getElementById('run').addEventListener('click', async () => {
    const spinner = document.getElementById('spinner');
    const card = document.getElementById('resultCard');
    const out = document.getElementById('output');

    spinner.classList.remove('d-none');
    card.classList.add('d-none');

    try {
        const res = await fetch('?api=run', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                id: sel.value,
                input: document.getElementById('input').value,
            }),
        });
        const data = await res.json();
        out.textContent = data.ok ? data.result.output : '❌ ' + data.error;
    } catch (e) {
        out.textContent = '❌ Chyba: ' + e.message;
    } finally {
        spinner.classList.add('d-none');
        card.classList.remove('d-none');
    }
});
</script>
</body>
</html>
