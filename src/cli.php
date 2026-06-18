<?php

declare(strict_types=1);

require __DIR__ . '/autoload.php';

use App\ExampleRegistry;
use App\OllamaClient;

/*
 * CLI spouštěč:  php src/cli.php <číslo> "<text>"
 * Příklad:       php src/cli.php 1 "Co je Docker?"
 */

$registry = new ExampleRegistry();
$id    = $argv[1] ?? '';
$input = $argv[2] ?? '';

if ($id === '' || $id === 'list') {
    echo "Dostupné příklady:\n";
    foreach ($registry->all() as $key => $ex) {
        printf("  %s) %-22s – %s\n", $key, $ex->title(), $ex->description());
    }
    echo "\nPoužití: php src/cli.php <číslo> \"<text>\"\n";
    exit(0);
}

$example = $registry->get($id);
if ($example === null) {
    fwrite(STDERR, "Neznámý příklad: {$id}\n");
    exit(1);
}
if ($input === '') {
    fwrite(STDERR, "Chybí vstupní text.\n");
    exit(1);
}

echo "▶ {$example->title()}\n";
echo str_repeat('-', 50) . "\n";

try {
    $result = $example->run(OllamaClient::fromEnv(), $input);
    echo "Vstup:  {$result['input']}\n\n";
    echo "Výstup: {$result['output']}\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Chyba: {$e->getMessage()}\n");
    exit(1);
}
