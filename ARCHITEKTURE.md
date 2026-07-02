# Architektura projektu

Čistá PHP 8.2 OOP aplikace (bez frameworku a bez Composeru), která demonstruje
práci s lokálním LLM přes [Ollama](https://ollama.com). Vše běží v Dockeru.

## Přehled na jeden pohled

```
┌─────────────────────────────────────────────────────────────────┐
│                         Docker Compose                            │
│                                                                   │
│  ┌───────────────┐   depends_on    ┌──────────────────────────┐  │
│  │  ollama-pull  │────(healthy)────▶│        ollama            │  │
│  │  (jednorázový │   stáhne model   │  LLM server, port 11434  │  │
│  │   init)       │                  │  volume: ollama_data     │  │
│  └───────────────┘                  └──────────────────────────┘  │
│                                            ▲                       │
│                                            │ HTTP /api/*           │
│                                            │ (cURL)                │
│  ┌─────────────────────────────────────────────────────────┐     │
│  │  php  –  vestavěný PHP server, port 8080                  │     │
│  │  ./src a ./public jsou mountnuté (živé úpravy)            │     │
│  └─────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────┘
          ▲                                   ▲
          │ Web UI (Bootstrap 5)              │ CLI
          │ http://localhost:8080             │ php src/cli.php …
       Prohlížeč                           Terminál
```

## Vrstvy aplikace

Aplikace má tři jasně oddělené vrstvy:

1. **Vstupní body (entrypoints)** — `public/index.php` (web + JSON API) a
   `src/cli.php` (příkazová řádka).
2. **Doménová logika** — `ExampleRegistry` + sada příkladů implementujících
   `ExampleInterface` (v `src/Examples/`).
3. **Infrastruktura** — `OllamaClient` jako jediný bod komunikace s LLM serverem.

Klíčová vlastnost: **oba vstupní body sdílejí naprosto stejnou logiku**. Rozdíl je
jen ve způsobu vstupu (argv vs. HTTP) a formátu výstupu (text vs. JSON/HTML).

## Tok požadavku

### Web (HTML)
```
GET /  →  public/index.php
        →  new ExampleRegistry()
        →  vyrenderuje Bootstrap 5 stránku s <select> všech příkladů
```

### Web (JSON API)
```
POST /?api=run   {"id":"2","input":"…"}
        →  public/index.php  (větev api=run)
        →  ExampleRegistry::get(id)  →  ExampleInterface
        →  $example->run(OllamaClient::fromEnv(), $input)
        →  {"ok":true,"result":{"input":…,"output":…}}
```
Chyby se vrací jako `{"ok":false,"error":…}` s HTTP 400.

### CLI
```
php src/cli.php <id> "<text>"
        →  src/cli.php
        →  ExampleRegistry::get(id)  →  ExampleInterface
        →  $example->run(OllamaClient::fromEnv(), $input)
        →  vypíše "Vstup:" a "Výstup:" na stdout
```
`php src/cli.php list` (nebo bez argumentů) vypíše všechny příklady.

Frontend (`index.php`) je čistý vanilla JS `fetch()` na `?api=run` — žádný build
krok, žádné npm. Bootstrap se načítá z CDN.

## Klíčové komponenty

### `OllamaClient` (`src/OllamaClient.php`)
Jediná třída, která ví, jak mluvit s Ollamou. Používá **výhradně vestavěný cURL**,
žádnou HTTP knihovnu.

- `generate(prompt, system, options)` → `/api/generate` — jednorázový dotaz,
  vrací text z pole `response`.
- `chat(messages, options)` → `/api/chat` — konverzace s historií zpráv
  (`role`/`content`), vrací text z `message.content`.
- `listModels()` → `/api/tags` — seznam stažených modelů.
- `OllamaClient::fromEnv()` — továrna, která přečte `OLLAMA_URL` a `LLM_MODEL`
  z prostředí; fallback `http://localhost:11434` a `qwen2.5:0.5b`.
- Soukromé helpery `post()`/`get()`/`exec()` obalují cURL; při chybě spojení
  vyhodí `RuntimeException`, JSON se dekóduje s `JSON_THROW_ON_ERROR`.
- Neměnná (`readonly` properties), `final`.

### `ExampleInterface` (`src/Examples/ExampleInterface.php`)
Kontrakt, který musí splnit každý příklad. Tři metody:

| Metoda | Účel |
|--------|------|
| `title(): string` | Krátký název do menu |
| `description(): string` | Co příklad demonstruje |
| `run(OllamaClient, string): array{input, output}` | Provede příklad nad vstupem |

### `ExampleRegistry` (`src/ExampleRegistry.php`)
Centrální registr. V konstruktoru vytvoří pole instancí příkladů a **automaticky
jim přiřadí ID podle pořadí** (`"1"`–`"7"`). Nabízí `all()` (celá mapa) a
`get(id)` (jeden příklad nebo `null`).

### Příklady (`src/Examples/`)
Sedm samostatných tříd, každá `final` a implementující `ExampleInterface`:

| ID | Třída | Ollama endpoint | Demonstruje |
|----|-------|-----------------|-------------|
| 1 | `AskQuestion` | `generate` | Základní otázka → odpověď |
| 2 | `Summarize` | `generate` | Shrnutí textu do 2–3 vět |
| 3 | `Translate` | `generate` | Překlad CZ ↔ EN |
| 4 | `SentimentAnalysis` | `generate` | Strukturovaný JSON výstup |
| 5 | `KeywordExtraction` | `generate` | Extrakce klíčových slov |
| 6 | `ChatWithMemory` | `chat` | Vícekolová konverzace s historií |
| 7 | `CodeGenerator` | `generate` | Generování PHP funkce |

Většina příkladů volá `generate()` s vhodným `system` promptem a nízkou
`temperature`. Příklad 6 (`ChatWithMemory`) jako jediný používá `chat()`
s předvyplněnou historií zpráv, aby ukázal, že si model drží kontext.

## Autoloading

Žádný Composer. `src/autoload.php` registruje minimální **PSR-4** autoloader:
namespace `App\` se mapuje na adresář `src/` (např. `App\Examples\AskQuestion`
→ `src/Examples/AskQuestion.php`). Oba vstupní body ho načítají hned na začátku.

## Docker

Tři služby v `docker-compose.yml`:

| Služba | Image | Role |
|--------|-------|------|
| `ollama` | `ollama/ollama:latest` | LLM server, port 11434, data ve volume `ollama_data`, healthcheck přes `ollama list` |
| `ollama-pull` | `ollama/ollama:latest` | Jednorázový init — po `service_healthy` stáhne `LLM_MODEL`, pak skončí (`restart: no`) |
| `php` | build z `Dockerfile` (`php:8.2-cli-alpine`) | Vestavěný PHP server na portu 8080 |

Model se předává přes proměnnou `LLM_MODEL` (`.env`), výchozí `qwen2.5:0.5b`.
Adresáře `./src` a `./public` jsou u `php` služby **mountnuté jako volumes**, takže
změny kódu jsou okamžitě aktivní bez rebuildu image. `Dockerfile` neinstaluje
žádné závislosti — cURL je součástí základního image.

## Návrhové principy

- **Zero dependencies** — čisté PHP, žádný framework ani Composer. Vše, co je
  potřeba, je v PHP standardně (cURL, JSON).
- **Rozšiřitelnost přes rozhraní** — přidat příklad znamená vytvořit třídu
  implementující `ExampleInterface` a zapsat ji do `$list` v
  `ExampleRegistry::__construct()`. ID se přiřadí automaticky, nic dalšího se
  nedrátuje.
- **Jeden bod pravdy pro LLM** — veškerá HTTP komunikace prochází přes
  `OllamaClient`, příklady s Ollamou komunikují jen skrze něj.
- **Sdílená logika** — web i CLI používají stejný registr i stejné příklady.
- **Moderní PHP** — `declare(strict_types=1)`, `final` třídy, `readonly`
  properties, named arguments, typed properties.

## Přidání nového příkladu

1. Vytvoř `src/Examples/MujPriklad.php` implementující `ExampleInterface`.
2. Přidej `new MujPriklad()` do pole `$list` v `ExampleRegistry::__construct()`.
3. Hotovo — příklad se objeví v CLI, ve webovém `<select>` i v JSON API pod
   automaticky přiděleným ID.
