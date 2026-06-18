<?php

declare(strict_types=1);

namespace App\Examples;

use App\OllamaClient;

/** 1) Základní otázka → odpověď. */
final class AskQuestion implements ExampleInterface
{
    public function title(): string
    {
        return 'Otázka a odpověď';
    }

    public function description(): string
    {
        return 'Položí modelu libovolnou otázku a vrátí stručnou odpověď.';
    }

    public function run(OllamaClient $llm, string $input): array
    {
        $output = $llm->generate(
            prompt: $input,
            system: 'Odpovídej stručně a věcně v češtině.',
            options: ['temperature' => 0.3],
        );

        return ['input' => $input, 'output' => $output];
    }
}
