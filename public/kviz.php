<?php
declare(strict_types=1);

/**
 * Samostatná stránka s kvízem k projektu (PHP 8.2 OOP + lokální LLM / Ollama).
 * 21 otázek, každá se 4 možnostmi, právě jedna je správná.
 * Vyhodnocení běží v prohlížeči (JavaScript) – žádné volání LLM není potřeba.
 *
 * Otázky (včetně indexu správné odpovědi) žijí na serveru, do prohlížeče se
 * posílají už jen jako data. Index správné odpovědi je 0-based.
 */

$questions = [
    [
        'q' => 'V jakém programovacím jazyce je aplikace napsaná?',
        'a' => ['Python 3.11', 'PHP 8.2', 'Node.js', 'Go'],
        'correct' => 1,
    ],
    [
        'q' => 'Přes jaký nástroj se aplikace připojuje k lokálnímu LLM?',
        'a' => ['OpenAI API', 'Hugging Face', 'Ollama', 'LM Studio'],
        'correct' => 2,
    ],
    [
        'q' => 'Jaký je výchozí model nastavený v projektu?',
        'a' => ['llama3.2:1b', 'gemma2:2b', 'phi3:mini', 'qwen2.5:0.5b'],
        'correct' => 3,
    ],
    [
        'q' => 'Kolik ukázkových příkladů registruje ExampleRegistry?',
        'a' => ['5', '7', '10', '3'],
        'correct' => 1,
    ],
    [
        'q' => 'Jaký framework aplikace používá?',
        'a' => ['Symfony', 'Laravel', 'Žádný – čisté PHP', 'Slim'],
        'correct' => 2,
    ],
    [
        'q' => 'Jak je řešený autoloading tříd?',
        'a' => ['Přes Composer', 'Manuálně (PSR-4) v src/autoload.php', 'Přes spl_autoload_register v každém souboru', 'Vůbec – všechno je v jednom souboru'],
        'correct' => 1,
    ],
    [
        'q' => 'Jaký kořenový namespace projekt používá?',
        'a' => ['Ollama\\', 'Src\\', 'App\\', 'Main\\'],
        'correct' => 2,
    ],
    [
        'q' => 'Jakou technologii používá OllamaClient pro HTTP komunikaci?',
        'a' => ['file_get_contents', 'Guzzle', 'cURL', 'Symfony HttpClient'],
        'correct' => 2,
    ],
    [
        'q' => 'Který Ollama endpoint slouží pro jednorázový dotaz (metoda generate())?',
        'a' => ['/api/chat', '/api/generate', '/api/tags', '/api/embeddings'],
        'correct' => 1,
    ],
    [
        'q' => 'Který endpoint se používá pro konverzaci s historií zpráv (metoda chat())?',
        'a' => ['/api/generate', '/api/tags', '/api/chat', '/api/history'],
        'correct' => 2,
    ],
    [
        'q' => 'Který endpoint vrací seznam dostupných modelů (metoda listModels())?',
        'a' => ['/api/tags', '/api/models', '/api/list', '/api/generate'],
        'correct' => 0,
    ],
    [
        'q' => 'Na jakém portu běží webová aplikace?',
        'a' => ['80', '8081', '8080', '11434'],
        'correct' => 2,
    ],
    [
        'q' => 'Na jakém portu naslouchá server Ollama?',
        'a' => ['8080', '11434', '3000', '5432'],
        'correct' => 1,
    ],
    [
        'q' => 'Který příklad demonstruje vícekolovou konverzaci s pamětí kontextu?',
        'a' => ['Otázka a odpověď', 'Chat s pamětí', 'Shrnutí textu', 'Generátor kódu'],
        'correct' => 1,
    ],
    [
        'q' => 'Co vrací příklad „Analýza sentimentu“ (ID 4)?',
        'a' => ['Prostý text', 'Strukturovaný JSON', 'HTML tabulku', 'CSV soubor'],
        'correct' => 1,
    ],
    [
        'q' => 'Na jakém Docker image je postavený PHP kontejner?',
        'a' => ['php:8.2-apache', 'php:8.2-fpm', 'php:8.2-cli-alpine', 'ubuntu:22.04'],
        'correct' => 2,
    ],
    [
        'q' => 'K čemu slouží služba ollama-pull v docker-compose.yml?',
        'a' => ['Trvale běží a obsluhuje požadavky', 'Jednorázově stáhne zvolený model při startu', 'Zálohuje databázi', 'Spouští PHP server'],
        'correct' => 1,
    ],
    [
        'q' => 'Jakým příkazem se spustí všechny služby na pozadí?',
        'a' => ['docker compose start', 'docker compose up -d', 'docker run ollama', 'php -S 0.0.0.0:8080'],
        'correct' => 1,
    ],
    [
        'q' => 'Co je potřeba pro přidání nového příkladu?',
        'a' => ['Upravit databázové schéma', 'Implementovat ExampleInterface a přidat do ExampleRegistry', 'Přegenerovat Composer autoload', 'Vytvořit nový Docker kontejner'],
        'correct' => 1,
    ],
    [
        'q' => 'Jaký CSS framework používá webové UI?',
        'a' => ['Tailwind CSS', 'Bootstrap 5', 'Bulma', 'Foundation'],
        'correct' => 1,
    ],
    [
        'q' => 'Co dělá tovární metoda OllamaClient::fromEnv()?',
        'a' => ['Vytvoří klienta z proměnných prostředí (URL a model)', 'Načte konfiguraci z databáze', 'Stáhne model z internetu', 'Otevře interaktivní CLI'],
        'correct' => 0,
    ],
];

// Do prohlížeče posíláme jen text otázek + index správné odpovědi.
$payload = array_map(static fn(array $q): array => [
    'q'       => $q['q'],
    'a'       => $q['a'],
    'correct' => $q['correct'],
], $questions);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kvíz · PHP-OOP + lokální LLM (Ollama)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .q-correct   { background-color: #d1e7dd !important; }
        .q-wrong     { background-color: #f8d7da !important; }
        .form-check.answer { padding: .35rem .5rem .35rem 2rem; border-radius: .375rem; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0">🧠 Kvíz k projektu</h1>
                <a href="index.php" class="btn btn-outline-secondary btn-sm">← Zpět na aplikaci</a>
            </div>
            <p class="text-muted">21 otázek o projektu PHP-OOP + lokální LLM (Ollama). U každé otázky je právě jedna správná odpověď.</p>

            <form id="quiz"></form>

            <div class="d-flex align-items-center gap-3 my-3">
                <button id="evaluate" type="button" class="btn btn-primary">Vyhodnotit kvíz ✔</button>
                <button id="reset" type="button" class="btn btn-outline-secondary d-none">Zkusit znovu ↻</button>
            </div>

            <div id="result" class="card shadow-sm d-none mb-5">
                <div class="card-body">
                    <h2 class="h5 mb-2" id="scoreLine"></h2>
                    <div class="progress" style="height: 1.5rem;">
                        <div id="scoreBar" class="progress-bar" role="progressbar" style="width:0%">0 %</div>
                    </div>
                    <p id="scoreMsg" class="mt-3 mb-0"></p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const QUESTIONS = <?= json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

const quizEl   = document.getElementById('quiz');
const resultEl = document.getElementById('result');
const evalBtn  = document.getElementById('evaluate');
const resetBtn = document.getElementById('reset');
let evaluated  = false;

function renderQuiz() {
    quizEl.innerHTML = QUESTIONS.map((item, qi) => {
        const options = item.a.map((text, ai) => `
            <label class="form-check answer d-block" id="opt-${qi}-${ai}">
                <input class="form-check-input" type="radio" name="q${qi}" value="${ai}">
                <span class="form-check-label">${escapeHtml(text)}</span>
            </label>`).join('');
        return `
            <div class="card shadow-sm mb-3" id="card-${qi}">
                <div class="card-body">
                    <p class="fw-semibold mb-2">${qi + 1}. ${escapeHtml(item.q)}</p>
                    ${options}
                </div>
            </div>`;
    }).join('');
}

function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

function evaluate() {
    if (evaluated) return;
    let score = 0;
    let unanswered = 0;

    QUESTIONS.forEach((item, qi) => {
        const chosen = quizEl.querySelector(`input[name="q${qi}"]:checked`);
        // Zvýrazni správnou odpověď zeleně.
        document.getElementById(`opt-${qi}-${item.correct}`).classList.add('q-correct');

        if (!chosen) {
            unanswered++;
            return;
        }
        const val = parseInt(chosen.value, 10);
        if (val === item.correct) {
            score++;
        } else {
            // Špatně zvolená odpověď červeně.
            document.getElementById(`opt-${qi}-${val}`).classList.add('q-wrong');
        }
    });

    // Zamkni další změny.
    quizEl.querySelectorAll('input').forEach(i => i.disabled = true);
    evaluated = true;

    const total = QUESTIONS.length;
    const pct = Math.round(score / total * 100);

    document.getElementById('scoreLine').textContent =
        `Výsledek: ${score} / ${total} správně` + (unanswered ? ` (${unanswered} nezodpovězeno)` : '');

    const bar = document.getElementById('scoreBar');
    bar.style.width = pct + '%';
    bar.textContent = pct + ' %';
    bar.className = 'progress-bar ' + (pct >= 80 ? 'bg-success' : pct >= 50 ? 'bg-warning' : 'bg-danger');

    let msg;
    if (pct === 100)      msg = '🏆 Perfektní! Projekt máš v malíčku.';
    else if (pct >= 80)   msg = '💪 Výborně, jen pár drobností.';
    else if (pct >= 50)   msg = '🙂 Slušné, ale ještě je co dohánět.';
    else                  msg = '📚 Zkus si znovu projít README a CLAUDE.md.';
    document.getElementById('scoreMsg').textContent = msg;

    resultEl.classList.remove('d-none');
    evalBtn.classList.add('d-none');
    resetBtn.classList.remove('d-none');
    resultEl.scrollIntoView({ behavior: 'smooth' });
}

function reset() {
    evaluated = false;
    renderQuiz();
    resultEl.classList.add('d-none');
    evalBtn.classList.remove('d-none');
    resetBtn.classList.add('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

evalBtn.addEventListener('click', evaluate);
resetBtn.addEventListener('click', reset);
renderQuiz();
</script>
</body>
</html>
