<?php

namespace App\Services\AI;

interface AIProviderInterface
{
    /**
     * Generate text response
     *
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function generateText(string $prompt, array $options = []): array;

    /**
     * Analyze image and generate response
     *
     * @param string $imageUrl
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function analyzeImage(string $imageUrl, string $prompt, array $options = []): array;

    /**
     * Generate code
     *
     * @param string $prompt
     * @param string $language
     * @param array $options
     * @return array
     */
    public function generateCode(string $prompt, string $language = 'php', array $options = []): array;

    /**
     * Translate text
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string $sourceLanguage
     * @return array
     */
    public function translateText(string $text, string $targetLanguage, string $sourceLanguage = 'auto'): array;

    /**
     * Summarize text
     *
     * @param string $text
     * @param array $options
     * @return array
     */
    public function summarizeText(string $text, array $options = []): array;

    /**
     * Analyze sentiment
     *
     * @param string $text
     * @return array
     */
    public function analyzeSentiment(string $text): array;

    /**
     * Moderate content
     *
     * @param string $text
     * @return array
     */
    public function moderateContent(string $text): array;

    /**
     * Process multimodal input (text + images)
     *
     * @param array $inputs
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function processMultimodal(array $inputs, string $prompt, array $options = []): array;

    /**
     * Check if provider supports specific feature
     *
     * @param string $feature
     * @return bool
     */
    public function supportsFeature(string $feature): bool;

    /**
     * Get provider name
     *
     * @return string
     */
    public function getProviderName(): string;

    /**
     * Get provider configuration
     *
     * @return array
     */
    public function getConfig(): array;
}
