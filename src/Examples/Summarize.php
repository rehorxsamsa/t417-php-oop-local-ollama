<?php

declare(strict_types=1);

namespace App\Examples;

use App\OllamaClient;

/** 2) Shrnutí delšího textu do několika vět. */
final class Summarize implements ExampleInterface
{
    public function title(): string
    {
        return 'Shrnutí textu';
    }

    public function description(): string
    {
        return 'Zkrátí vložený text do 2–3 vět.';
    }

    public function run(OllamaClient $llm, string $input): array
    {
        $output = $llm->generate(
            prompt: $input,
            system: 'Jsi expert na shrnování. Shrň text do 2–3 vět v češtině. '
                  . 'Zachovej klíčové informace, vynech vatu.',
            options: ['temperature' => 0.2],
        );

        return ['input' => $input, 'output' => $output];
    }
}
