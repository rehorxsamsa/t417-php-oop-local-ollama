<?php

declare(strict_types=1);

namespace App\Examples;

use App\OllamaClient;

/** 5) Extrakce klíčových slov z textu. */
final class KeywordExtraction implements ExampleInterface
{
    public function title(): string
    {
        return 'Klíčová slova';
    }

    public function description(): string
    {
        return 'Vytáhne 5 nejdůležitějších klíčových slov z textu.';
    }

    public function run(OllamaClient $llm, string $input): array
    {
        $output = $llm->generate(
            prompt: $input,
            system: 'Vytáhni z textu 5 nejdůležitějších klíčových slov nebo frází. '
                  . 'Vrať je jako odrážkový seznam, každé na novém řádku s pomlčkou.',
            options: ['temperature' => 0.2],
        );

        return ['input' => $input, 'output' => $output];
    }
}
