<?php

declare(strict_types=1);

namespace App;

use App\Examples\AskQuestion;
use App\Examples\ChatWithMemory;
use App\Examples\CodeGenerator;
use App\Examples\ExampleInterface;
use App\Examples\KeywordExtraction;
use App\Examples\SentimentAnalysis;
use App\Examples\Summarize;
use App\Examples\Translate;

/** Centrální registr 7 příkladů. */
final class ExampleRegistry
{
    /** @var array<string,ExampleInterface> */
    private array $examples;

    public function __construct()
    {
        $list = [
            new AskQuestion(),
            new Summarize(),
            new Translate(),
            new SentimentAnalysis(),
            new KeywordExtraction(),
            new ChatWithMemory(),
            new CodeGenerator(),
        ];

        foreach ($list as $i => $example) {
            $this->examples[(string) ($i + 1)] = $example;
        }
    }

    /** @return array<string,ExampleInterface> */
    public function all(): array
    {
        return $this->examples;
    }

    public function get(string $id): ?ExampleInterface
    {
        return $this->examples[$id] ?? null;
    }
}
