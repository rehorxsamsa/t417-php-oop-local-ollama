<?php

declare(strict_types=1);

namespace App\Examples;

use App\OllamaClient;

/** 6) Chat s pamětí konverzace (historie zpráv). */
final class ChatWithMemory implements ExampleInterface
{
    public function title(): string
    {
        return 'Chat s pamětí';
    }

    public function description(): string
    {
        return 'Demonstruje vícekolovou konverzaci, kde si model pamatuje kontext.';
    }

    public function run(OllamaClient $llm, string $input): array
    {
        // Předvyplněná historie – ukázka, že model navazuje na předchozí zprávy.
        $messages = [
            ['role' => 'system', 'content' => 'Jsi přátelský asistent. Odpovídej česky a stručně.'],
            ['role' => 'user', 'content' => 'Jmenuji se Rehor a programuji v PHP.'],
            ['role' => 'assistant', 'content' => 'Těší mě, Rehore! PHP je skvělá volba.'],
            ['role' => 'user', 'content' => $input],
        ];

        $output = $llm->chat($messages, ['temperature' => 0.4]);

        $context = "[Historie] Uživatel se představil jako Rehor (PHP vývojář).\n";
        $context .= "[Nová zpráva] {$input}";

        return ['input' => $context, 'output' => $output];
    }
}
