# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Pure PHP 8.2 OOP application (no framework, no Composer) that connects to a local LLM via [Ollama](https://ollama.com). Everything runs in Docker. The default model is `qwen2.5:0.5b` (~400 MB), configurable via `.env`.

## Common Commands

```bash
# Start all services (Ollama + PHP app + one-shot model pull)
docker compose up -d

# Watch model download progress (first run only)
docker compose logs -f ollama-pull

# Stop
docker compose down
docker compose down -v   # also deletes the downloaded model volume

# CLI runner
docker compose exec php php src/cli.php list
docker compose exec php php src/cli.php 1 "Co je Docker?"
docker compose exec php php src/cli.php 3 "Dobrý den, jak se máte?"
docker compose exec php php src/cli.php 7 "sečte dvě čísla"

# Test JSON API
curl -s http://localhost:8080/?api=run \
  -H 'Content-Type: application/json' \
  -d '{"id":"2","input":"Docker je nástroj pro běh aplikací v kontejnerech..."}'
```

Web UI: http://localhost:8080

## Architecture

No Composer, no framework — everything is manual PSR-4 via `src/autoload.php` (`App\` namespace → `src/`).

**Request flow (web):** `public/index.php` checks for `?api=run` (JSON API) or renders the Bootstrap 5 HTML UI. Both paths instantiate `ExampleRegistry` → pick an `ExampleInterface` implementation → call `run(OllamaClient, string)` → return `['input' => ..., 'output' => ...]`.

**Request flow (CLI):** `src/cli.php` does the same lookup via argv.

**Key classes:**
- `OllamaClient` — cURL-only HTTP client wrapping Ollama's `/api/generate` (single-shot) and `/api/chat` (with message history). Built from env vars via `OllamaClient::fromEnv()`.
- `ExampleRegistry` — maps string IDs `"1"`–`"7"` to `ExampleInterface` instances.
- `ExampleInterface` — three methods: `title()`, `description()`, `run(OllamaClient, string): array{input:string,output:string}`.

**Adding a new example:** implement `ExampleInterface`, add it to the `$list` array in `ExampleRegistry::__construct()`. No other wiring needed.

## Environment

| Variable | Default | Description |
|----------|---------|-------------|
| `OLLAMA_URL` | `http://ollama:11434` | Ollama server URL (set by Docker Compose) |
| `LLM_MODEL` | `qwen2.5:0.5b` | Model to use; override in `.env` |

Alternative models (change `LLM_MODEL` in `.env` before `docker compose up`):

| Model | Size | Notes |
|-------|------|-------|
| `qwen2.5:0.5b` | ~0.4 GB | default, fastest |
| `gemma2:2b` | ~1.6 GB | better quality |
| `llama3.2:1b` | ~1.3 GB | good balance |
| `phi3:mini` | ~2.3 GB | best for code |

## Docker Services

- **ollama** — Ollama server, exposes port 11434, data in `ollama_data` volume.
- **ollama-pull** — one-shot init container that pulls `LLM_MODEL`; runs only once at startup.
- **php** — `php:8.2-cli-alpine` with built-in PHP server on port 8080; `src/` and `public/` are mounted as volumes so edits are live without rebuilding.
