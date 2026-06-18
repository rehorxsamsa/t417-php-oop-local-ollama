<?php

declare(strict_types=1);

namespace App\Examples;

use App\OllamaClient;

/** 3) Překlad textu (auto-detekce → angličtina, nebo naopak). */
final class Translate implements ExampleInterface
{
    public function title(): string
    {
        return 'Překlad';
    }

    public function description(): string
    {
        return 'Přeloží text mezi češtinou a angličtinou.';
    }

    public function run(OllamaClient $llm, string $input): array
    {
        $output = $llm->generate(
            prompt: $input,
            system: 'Jsi překladač. Pokud je text česky, přelož do angličtiny. '
                  . 'Pokud je anglicky, přelož do češtiny. Vrať POUZE překlad bez komentáře.',
            options: ['temperature' => 0.1],
        );

        return ['input' => $input, 'output' => $output];
    }
}
