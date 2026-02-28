<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

abstract class AbstractAIProvider implements AIProviderInterface
{
    protected array $config;
    protected string $providerName;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Make HTTP request to AI provider
     *
     * @param string $endpoint
     * @param array $data
     * @param string $method
     * @return array
     */
    protected function makeRequest(string $endpoint, array $data, string $method = 'POST'): array
    {
        $url = $this->config['base_url'] . $endpoint;
        $headers = $this->getHeaders();
        $timeout = $this->config['request_timeout'] ?? 60;

        try {
            $response = Http::timeout($timeout)
                ->withHeaders($headers)
                ->{$method}($url, $data);

            if ($response->successful()) {
                return $this->parseResponse($response->json());
            }

            throw new \Exception("API request failed: {$response->status()} - {$response->body()}");
        } catch (\Exception $e) {
            $this->logError($e, $data);
            throw $e;
        }
    }

    /**
     * Get cache key for request
     *
     * @param string $method
     * @param array $params
     * @return string
     */
    protected function getCacheKey(string $method, array $params): string
    {
        $key = 'ai_' . $this->providerName . '_' . $method . '_' . md5(serialize($params));
        return config('ai.cache.prefix', 'ai_response:') . $key;
    }

    /**
     * Get cached response
     *
     * @param string $cacheKey
     * @return array|null
     */
    protected function getCachedResponse(string $cacheKey): ?array
    {
        if (!config('ai.cache.enabled', true)) {
            return null;
        }

        return Cache::get($cacheKey);
    }

    /**
     * Cache response
     *
     * @param string $cacheKey
     * @param array $response
     * @return void
     */
    protected function cacheResponse(string $cacheKey, array $response): void
    {
        if (!config('ai.cache.enabled', true)) {
            return;
        }

        $ttl = config('ai.cache.ttl', 3600);
        Cache::put($cacheKey, $response, $ttl);
    }

    /**
     * Log error
     *
     * @param \Exception $e
     * @param array $data
     * @return void
     */
    protected function logError(\Exception $e, array $data): void
    {
        if (!config('ai.logging.enabled', true)) {
            return;
        }

        $logData = [
            'provider' => $this->providerName,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ];

        if (config('ai.logging.include_request_data', false)) {
            $logData['request_data'] = $data;
        }

        Log::channel('ai')->error($e->getMessage(), $logData);
    }

    /**
     * Log successful request
     *
     * @param string $method
     * @param array $request
     * @param array $response
     * @return void
     */
    protected function logSuccess(string $method, array $request, array $response): void
    {
        if (!config('ai.logging.enabled', true)) {
            return;
        }

        $logData = [
            'provider' => $this->providerName,
            'method' => $method,
            'success' => true,
        ];

        if (config('ai.logging.include_request_data', false)) {
            $logData['request_data'] = $request;
        }

        if (config('ai.logging.include_response_data', false)) {
            $logData['response_data'] = $response;
        }

        Log::channel('ai')->info("AI request successful: {$method}", $logData);
    }

    /**
     * Check rate limiting
     *
     * @return bool
     */
    protected function checkRateLimit(): bool
    {
        if (!config('ai.rate_limiting.enabled', true)) {
            return true;
        }

        $key = 'ai_rate_limit_' . $this->providerName;
        $current = Cache::get($key, 0);

        $limits = [
            'minute' => config('ai.rate_limiting.requests_per_minute', 60),
            'hour' => config('ai.rate_limiting.requests_per_hour', 1000),
            'day' => config('ai.rate_limiting.requests_per_day', 10000),
        ];

        // Check minute limit
        if ($current >= $limits['minute']) {
            return false;
        }

        // Increment counter
        Cache::increment($key);
        Cache::expire($key, 60); // Reset every minute

        return true;
    }

    /**
     * Get provider-specific headers
     *
     * @return array
     */
    abstract protected function getHeaders(): array;

    /**
     * Parse provider response
     *
     * @param array $response
     * @return array
     */
    abstract protected function parseResponse(array $response): array;

    /**
     * Get provider name
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * Get provider configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Check if provider supports specific feature
     *
     * @param string $feature
     * @return bool
     */
    public function supportsFeature(string $feature): bool
    {
        return config("ai.features.{$feature}.{$this->providerName}", false);
    }

    /**
     * Default implementation for unsupported features
     *
     * @param string $feature
     * @return array
     */
    protected function unsupportedFeature(string $feature): array
    {
        return [
            'success' => false,
            'error' => "Feature '{$feature}' is not supported by {$this->providerName}",
            'provider' => $this->providerName,
        ];
    }

    /**
     * Generate text response with caching and rate limiting
     *
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function generateText(string $prompt, array $options = []): array
    {
        if (!$this->supportsFeature('text_generation')) {
            return $this->unsupportedFeature('text_generation');
        }

        if (!$this->checkRateLimit()) {
            return [
                'success' => false,
                'error' => 'Rate limit exceeded',
                'provider' => $this->providerName,
            ];
        }

        $cacheKey = $this->getCacheKey('generate_text', ['prompt' => $prompt, 'options' => $options]);
        $cached = $this->getCachedResponse($cacheKey);

        if ($cached) {
            return $cached;
        }

        try {
            $response = $this->performTextGeneration($prompt, $options);
            $this->cacheResponse($cacheKey, $response);
            $this->logSuccess('generate_text', ['prompt' => $prompt], $response);
            return $response;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $this->providerName,
            ];
        }
    }

    /**
     * Abstract method for text generation
     *
     * @param string $prompt
     * @param array $options
     * @return array
     */
    abstract protected function performTextGeneration(string $prompt, array $options): array;
}
