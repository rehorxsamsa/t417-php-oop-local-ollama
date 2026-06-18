<?php

declare(strict_types=1);

namespace App;

/**
 * Jednoduchý OOP klient pro lokální Ollama LLM server.
 * Bez frameworku, používá pouze vestavěný cURL.
 */
final class OllamaClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $model,
        private readonly int $timeout = 120,
    ) {
    }

    /** Vytvoří klienta z proměnných prostředí (Docker). */
    public static function fromEnv(): self
    {
        return new self(
            baseUrl: rtrim(getenv('OLLAMA_URL') ?: 'http://localhost:11434', '/'),
            model: getenv('LLM_MODEL') ?: 'qwen2.5:0.5b',
        );
    }

    /**
     * Jednoduchý dotaz – vrátí celou odpověď jako text.
     *
     * @param array<string,mixed> $options Volitelné parametry modelu (temperature, …)
     */
    public function generate(string $prompt, string $system = '', array $options = []): string
    {
        $payload = [
            'model'  => $this->model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => $options,
        ];
        if ($system !== '') {
            $payload['system'] = $system;
        }

        $data = $this->post('/api/generate', $payload);

        return trim((string) ($data['response'] ?? ''));
    }

    /**
     * Chat s historií zpráv.
     *
     * @param list<array{role:string,content:string}> $messages
     * @param array<string,mixed> $options
     */
    public function chat(array $messages, array $options = []): string
    {
        $data = $this->post('/api/chat', [
            'model'    => $this->model,
            'messages' => $messages,
            'stream'   => false,
            'options'  => $options,
        ]);

        return trim((string) ($data['message']['content'] ?? ''));
    }

    /** Seznam stažených modelů. */
    public function listModels(): array
    {
        $data = $this->get('/api/tags');
        return array_map(
            static fn (array $m): string => (string) $m['name'],
            $data['models'] ?? [],
        );
    }

    // ---- HTTP helpery (cURL) -------------------------------------------------

    /** @param array<string,mixed> $payload @return array<string,mixed> */
    private function post(string $path, array $payload): array
    {
        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_TIMEOUT        => $this->timeout,
        ]);

        return $this->exec($ch);
    }

    /** @return array<string,mixed> */
    private function get(string $path): array
    {
        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
        ]);

        return $this->exec($ch);
    }

    /** @param \CurlHandle $ch @return array<string,mixed> */
    private function exec(\CurlHandle $ch): array
    {
        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($errno !== 0) {
            throw new \RuntimeException("Chyba spojení s Ollama: {$error}");
        }

        /** @var array<string,mixed> $decoded */
        $decoded = json_decode((string) $response, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
