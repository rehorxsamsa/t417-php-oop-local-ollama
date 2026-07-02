# PHP-OOP + Lokální LLM (Ollama) v Dockeru

Čistá PHP 8.2 OOP aplikace **bez frameworku**, propojená s **lokálním open-source LLM**
přes [Ollama](https://ollama.com). Vše běží v Dockeru na Ubuntu, model má jen pár GB.

## Co to obsahuje

- `OllamaClient` – OOP klient pro Ollama API (jen vestavěný cURL, žádné závislosti)
- **7 praktických příkladů**, každý jako samostatná OOP třída:

| # | Příklad | Co dělá |
|---|---------|---------|
| 1 | Otázka a odpověď | Položí modelu otázku |
| 2 | Shrnutí textu | Zkrátí text do 2–3 vět |
| 3 | Překlad | CZ ↔ EN |
| 4 | Analýza sentimentu | Strukturovaný JSON výstup |
| 5 | Klíčová slova | Extrahuje 5 klíčových frází |
| 6 | Chat s pamětí | Vícekolová konverzace s historií |
| 7 | Generátor kódu | Vygeneruje PHP funkci z popisu |

- Webové rozhraní (Bootstrap 5) + JSON API
- CLI runner pro terminál
- **Kvíz** (21 otázek) k procvičení znalostí o projektu – [`public/kviz.php`](http://localhost:8080/kviz.php)

## Doporučený model

Výchozí je **`qwen2.5:0.5b`** (~400 MB) – rychlý i na slabším CPU.
Alternativy (přepiš v `.env` nebo proměnnou `LLM_MODEL`):

| Model | Velikost | Poznámka |
|-------|----------|----------|
| `qwen2.5:0.5b` | ~0,4 GB | výchozí, nejrychlejší |
| `gemma2:2b` | ~1,6 GB | kvalitnější odpovědi |
| `llama3.2:1b` | ~1,3 GB | dobrý kompromis |
| `phi3:mini` | ~2,3 GB | silný na kód |

## Spuštění

```bash
# 1) Volitelně vyber model
echo "LLM_MODEL=qwen2.5:0.5b" > .env

# 2) Start (poprvé stáhne image + model, chvíli to trvá)
docker compose up -d

# 3) Sleduj stahování modelu
docker compose logs -f ollama-pull

# 4) Otevři v prohlížeči
#    http://localhost:8080            # aplikace
#    http://localhost:8080/kviz.php   # kvíz (21 otázek)
```

## Spuštění z terminálu (CLI)

```bash
# Seznam příkladů
docker compose exec php php src/cli.php list

# Konkrétní příklad
docker compose exec php php src/cli.php 1 "Co je Docker?"
docker compose exec php php src/cli.php 3 "Dobrý den, jak se máte?"
docker compose exec php php src/cli.php 7 "sečte dvě čísla"
```

## Test API přímo (curl)

```bash
curl -s http://localhost:8080/?api=run \
  -H 'Content-Type: application/json' \
  -d '{"id":"2","input":"Docker je nástroj pro běh aplikací v kontejnerech..."}'
```

## Architektura

```
php-llm/
├── docker-compose.yml      # Ollama + PHP + init pull
├── Dockerfile              # php:8.2-cli-alpine
├── public/
│   ├── index.php           # web UI + JSON API
│   └── kviz.php            # samostatná stránka s kvízem (21 otázek)
└── src/
    ├── autoload.php        # PSR-4 bez Composeru
    ├── OllamaClient.php     # OOP klient
    ├── ExampleRegistry.php  # registr příkladů
    ├── cli.php              # CLI runner
    └── Examples/            # 7 tříd implementujících ExampleInterface
```

📐 Podrobný popis vrstev, toku požadavku a návrhových principů najdeš v [ARCHITEKTURE.md](ARCHITEKTURE.md).

## Vypnutí

```bash
docker compose down          # zastaví
docker compose down -v       # zastaví + smaže stažený model
```
