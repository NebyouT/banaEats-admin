<?php

namespace App\Services;

use App\Services\AI\AIProviderInterface;
use App\Services\AI\OpenAIProvider;
use App\Services\AI\GeminiProvider;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected array $providers = [];
    protected AIProviderInterface $currentProvider;
    protected string $defaultProvider;

    public function __construct()
    {
        $this->defaultProvider = config('ai.default_provider', 'openai');
        $this->initializeProviders();
        $this->setCurrentProvider($this->defaultProvider);
    }

    /**
     * Initialize all AI providers
     *
     * @return void
     */
    private function initializeProviders(): void
    {
        // Initialize OpenAI provider
        if (config('ai.openai.api_key')) {
            $this->providers['openai'] = new OpenAIProvider(config('ai.openai'));
        }

        // Initialize Gemini provider
        if (config('ai.gemini.api_key')) {
            $this->providers['gemini'] = new GeminiProvider(config('ai.gemini'));
        }

        if (empty($this->providers)) {
            throw new \Exception('No AI providers configured. Please configure at least one provider.');
        }
    }

    /**
     * Set current provider
     *
     * @param string $provider
     * @return self
     */
    public function setCurrentProvider(string $provider): self
    {
        if (!isset($this->providers[$provider])) {
            throw new \Exception("AI provider '{$provider}' is not configured.");
        }

        $this->currentProvider = $this->providers[$provider];
        return $this;
    }

    /**
     * Get current provider
     *
     * @return AIProviderInterface
     */
    public function getCurrentProvider(): AIProviderInterface
    {
        return $this->currentProvider;
    }

    /**
     * Get available providers
     *
     * @return array
     */
    public function getAvailableProviders(): array
    {
        return array_keys($this->providers);
    }

    /**
     * Generate text response
     *
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function generateText(string $prompt, array $options = []): array
    {
        return $this->executeWithFallback('generateText', [$prompt, $options]);
    }

    /**
     * Analyze image and generate response
     *
     * @param string $imageUrl
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function analyzeImage(string $imageUrl, string $prompt, array $options = []): array
    {
        return $this->executeWithFallback('analyzeImage', [$imageUrl, $prompt, $options]);
    }

    /**
     * Generate code
     *
     * @param string $prompt
     * @param string $language
     * @param array $options
     * @return array
     */
    public function generateCode(string $prompt, string $language = 'php', array $options = []): array
    {
        return $this->executeWithFallback('generateCode', [$prompt, $language, $options]);
    }

    /**
     * Translate text
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string $sourceLanguage
     * @return array
     */
    public function translateText(string $text, string $targetLanguage, string $sourceLanguage = 'auto'): array
    {
        return $this->executeWithFallback('translateText', [$text, $targetLanguage, $sourceLanguage]);
    }

    /**
     * Summarize text
     *
     * @param string $text
     * @param array $options
     * @return array
     */
    public function summarizeText(string $text, array $options = []): array
    {
        return $this->executeWithFallback('summarizeText', [$text, $options]);
    }

    /**
     * Analyze sentiment
     *
     * @param string $text
     * @return array
     */
    public function analyzeSentiment(string $text): array
    {
        return $this->executeWithFallback('analyzeSentiment', [$text]);
    }

    /**
     * Moderate content
     *
     * @param string $text
     * @return array
     */
    public function moderateContent(string $text): array
    {
        return $this->executeWithFallback('moderateContent', [$text]);
    }

    /**
     * Process multimodal input
     *
     * @param array $inputs
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function processMultimodal(array $inputs, string $prompt, array $options = []): array
    {
        return $this->executeWithFallback('processMultimodal', [$inputs, $prompt, $options]);
    }

    /**
     * Generate structured data (Gemini specific)
     *
     * @param string $prompt
     * @param array $schema
     * @param array $options
     * @return array
     */
    public function generateStructuredData(string $prompt, array $schema, array $options = []): array
    {
        if ($this->currentProvider instanceof GeminiProvider) {
            return $this->currentProvider->generateStructuredData($prompt, $schema, $options);
        }

        return [
            'success' => false,
            'error' => 'Structured data generation is only supported by Gemini provider',
            'provider' => $this->currentProvider->getProviderName(),
        ];
    }

    /**
     * Generate creative content (Gemini specific)
     *
     * @param string $type
     * @param array $params
     * @param array $options
     * @return array
     */
    public function generateCreativeContent(string $type, array $params, array $options = []): array
    {
        if ($this->currentProvider instanceof GeminiProvider) {
            return $this->currentProvider->generateCreativeContent($type, $params, $options);
        }

        return [
            'success' => false,
            'error' => 'Creative content generation is only supported by Gemini provider',
            'provider' => $this->currentProvider->getProviderName(),
        ];
    }

    /**
     * Analyze document (Gemini specific)
     *
     * @param string $documentUrl
     * @param array $options
     * @return array
     */
    public function analyzeDocument(string $documentUrl, array $options = []): array
    {
        if ($this->currentProvider instanceof GeminiProvider) {
            return $this->currentProvider->analyzeDocument($documentUrl, $options);
        }

        return [
            'success' => false,
            'error' => 'Document analysis is only supported by Gemini provider',
            'provider' => $this->currentProvider->getProviderName(),
        ];
    }

    /**
     * Execute method with fallback support
     *
     * @param string $method
     * @param array $arguments
     * @return array
     */
    private function executeWithFallback(string $method, array $arguments): array
    {
        $primaryProvider = $this->currentProvider;
        $fallbackEnabled = config('ai.fallback.enabled', true);
        $fallbackProvider = config('ai.fallback.provider', 'gemini');
        $retryAttempts = config('ai.fallback.retry_attempts', 2);

        try {
            // Try primary provider first
            $result = $this->callProviderMethod($primaryProvider, $method, $arguments);
            
            if ($result['success']) {
                return $result;
            }

            if (!$fallbackEnabled) {
                return $result;
            }

            // Try fallback provider
            Log::info("Primary provider failed, trying fallback: {$fallbackProvider}");
            $this->setCurrentProvider($fallbackProvider);
            $result = $this->callProviderMethod($this->currentProvider, $method, $arguments);
            
            if ($result['success']) {
                return $result;
            }

            // Restore primary provider
            $this->setCurrentProvider($primaryProvider->getProviderName());
            return $result;

        } catch (\Exception $e) {
            Log::error("AI service error: {$e->getMessage()}");
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $this->currentProvider->getProviderName(),
            ];
        }
    }

    /**
     * Call method on provider
     *
     * @param AIProviderInterface $provider
     * @param string $method
     * @param array $arguments
     * @return array
     */
    private function callProviderMethod(AIProviderInterface $provider, string $method, array $arguments): array
    {
        if (!method_exists($provider, $method)) {
            return [
                'success' => false,
                'error' => "Method '{$method}' not supported by provider",
                'provider' => $provider->getProviderName(),
            ];
        }

        return call_user_func_array([$provider, $method], $arguments);
    }

    /**
     * Get provider comparison
     *
     * @return array
     */
    public function getProviderComparison(): array
    {
        $comparison = [];
        
        foreach ($this->providers as $name => $provider) {
            $comparison[$name] = [
                'name' => $provider->getProviderName(),
                'features' => [
                    'text_generation' => $provider->supportsFeature('text_generation'),
                    'image_analysis' => $provider->supportsFeature('image_analysis'),
                    'multimodal' => $provider->supportsFeature('multimodal'),
                    'code_generation' => $provider->supportsFeature('code_generation'),
                    'translation' => $provider->supportsFeature('translation'),
                    'summarization' => $provider->supportsFeature('summarization'),
                    'sentiment_analysis' => $provider->supportsFeature('sentiment_analysis'),
                    'content_moderation' => $provider->supportsFeature('content_moderation'),
                ],
                'config' => [
                    'model' => $provider->getConfig()['model'] ?? 'default',
                    'timeout' => $provider->getConfig()['request_timeout'] ?? 60,
                ],
            ];
        }

        return $comparison;
    }

    /**
     * Test provider connectivity
     *
     * @param string|null $provider
     * @return array
     */
    public function testProvider(string $provider = null): array
    {
        if ($provider) {
            if (!isset($this->providers[$provider])) {
                return [
                    'success' => false,
                    'error' => "Provider '{$provider}' not found",
                ];
            }

            $testProvider = $this->providers[$provider];
        } else {
            $testProvider = $this->currentProvider;
        }

        try {
            $result = $testProvider->generateText('Hello, this is a test message.', ['max_tokens' => 10]);
            
            return [
                'success' => $result['success'],
                'provider' => $testProvider->getProviderName(),
                'response_time' => $result['response_time'] ?? null,
                'error' => $result['error'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'provider' => $testProvider->getProviderName(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get AI usage statistics
     *
     * @return array
     */
    public function getUsageStats(): array
    {
        // This would typically be implemented with a database or cache
        // For now, return basic information
        return [
            'providers' => count($this->providers),
            'default_provider' => $this->defaultProvider,
            'current_provider' => $this->currentProvider->getProviderName(),
            'supported_features' => [
                'text_generation' => true,
                'image_analysis' => true,
                'multimodal' => true,
                'code_generation' => true,
                'translation' => true,
                'summarization' => true,
                'sentiment_analysis' => true,
                'content_moderation' => true,
            ],
        ];
    }

    /**
     * Reset to default provider
     *
     * @return self
     */
    public function resetToDefault(): self
    {
        $this->setCurrentProvider($this->defaultProvider);
        return $this;
    }

    /**
     * Get provider by name
     *
     * @param string $name
     * @return AIProviderInterface|null
     */
    public function getProvider(string $name): ?AIProviderInterface
    {
        return $this->providers[$name] ?? null;
    }
}
