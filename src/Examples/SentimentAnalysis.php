<?php

declare(strict_types=1);

namespace App\Examples;

use App\OllamaClient;

/** 4) Analýza sentimentu se strukturovaným JSON výstupem. */
final class SentimentAnalysis implements ExampleInterface
{
    public function title(): string
    {
        return 'Analýza sentimentu';
    }

    public function description(): string
    {
        return 'Vyhodnotí náladu textu a vrátí strukturovaný JSON.';
    }

    public function run(OllamaClient $llm, string $input): array
    {
        $raw = $llm->generate(
            prompt: $input,
            system: 'Analyzuj sentiment textu. Vrať POUZE validní JSON ve tvaru: '
                  . '{"sentiment":"pozitivní|neutrální|negativní","skore":0.0,"duvod":"krátké zdůvodnění"}. '
                  . 'Žádný další text, žádné markdown bloky.',
            options: ['temperature' => 0.0],
        );

        // Pokus o parsování – ukázka práce se strukturovaným výstupem v PHP.
        $clean = trim(str_replace(['```json', '```'], '', $raw));
        $parsed = json_decode($clean, true);

        $output = is_array($parsed)
            ? json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : "Model nevrátil validní JSON:\n" . $raw;

        return ['input' => $input, 'output' => (string) $output];
    }
}
