<?php

declare(strict_types=1);

namespace App;

use App\Examples\AskQuestion;
use App\Examples\ExampleInterface;

/** Centrální registr příkladů. */
final class ExampleRegistry
{
    /** @var array<string,ExampleInterface> */
    private array $examples;

    public function __construct()
    {
        $list = [
            new AskQuestion(),
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
