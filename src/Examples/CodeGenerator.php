<?php

declare(strict_types=1);

namespace App\Examples;

use App\OllamaClient;

/** 7) Generování kódu na základě popisu. */
final class CodeGenerator implements ExampleInterface
{
    public function title(): string
    {
        return 'Generátor kódu';
    }

    public function description(): string
    {
        return 'Z popisu vygeneruje krátkou PHP funkci s komentářem.';
    }

    public function run(OllamaClient $llm, string $input): array
    {
        $output = $llm->generate(
            prompt: "Napiš PHP funkci, která: {$input}",
            system: 'Jsi PHP vývojář. Napiš čistou, krátkou PHP funkci podle zadání. '
                  . 'Přidej PHPDoc komentář. Vrať jen kód v bloku ```php ... ```.',
            options: ['temperature' => 0.2],
        );

        return ['input' => $input, 'output' => $output];
    }
}
