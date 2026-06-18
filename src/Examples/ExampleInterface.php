<?php

declare(strict_types=1);

namespace App\Examples;

use App\OllamaClient;

interface ExampleInterface
{
    /** Krátký název pro menu. */
    public function title(): string;

    /** Co příklad demonstruje. */
    public function description(): string;

    /**
     * Spustí příklad nad daným vstupem a vrátí výsledek.
     *
     * @return array{input:string,output:string}
     */
    public function run(OllamaClient $llm, string $input): array;
}
