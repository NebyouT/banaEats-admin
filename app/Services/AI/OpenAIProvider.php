<?php

namespace App\Services\AI;

class OpenAIProvider extends AbstractAIProvider
{
    protected string $providerName = 'openai';

    /**
     * Get OpenAI headers
     *
     * @return array
     */
    protected function getHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->config['api_key'],
        ];

        if (!empty($this->config['organization'])) {
            $headers['OpenAI-Organization'] = $this->config['organization'];
        }

        return $headers;
    }

    /**
     * Parse OpenAI response
     *
     * @param array $response
     * @return array
     */
    protected function parseResponse(array $response): array
    {
        if (isset($response['error'])) {
            return [
                'success' => false,
                'error' => $response['error']['message'] ?? 'Unknown error',
                'provider' => $this->providerName,
            ];
        }

        return [
            'success' => true,
            'data' => $response,
            'provider' => $this->providerName,
        ];
    }

    /**
     * Perform text generation with OpenAI
     *
     * @param string $prompt
     * @param array $options
     * @return array
     */
    protected function performTextGeneration(string $prompt, array $options): array
    {
        $data = [
            'model' => $options['model'] ?? $this->config['model'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens' => $options['max_tokens'] ?? $this->config['max_tokens'],
            'temperature' => $options['temperature'] ?? $this->config['temperature'],
        ];

        $response = $this->makeRequest('/chat/completions', $data);

        if ($response['success'] && isset($response['data']['choices'][0]['message']['content'])) {
            return [
                'success' => true,
                'text' => $response['data']['choices'][0]['message']['content'],
                'usage' => $response['data']['usage'] ?? [],
                'provider' => $this->providerName,
            ];
        }

        return $response;
    }

    /**
     * Analyze image with OpenAI (requires GPT-4 Vision)
     *
     * @param string $imageUrl
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function analyzeImage(string $imageUrl, string $prompt, array $options = []): array
    {
        if (!$this->supportsFeature('image_analysis')) {
            return $this->unsupportedFeature('image_analysis');
        }

        $data = [
            'model' => $options['model'] ?? 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $imageUrl,
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => $options['max_tokens'] ?? $this->config['max_tokens'],
        ];

        $response = $this->makeRequest('/chat/completions', $data);

        if ($response['success'] && isset($response['data']['choices'][0]['message']['content'])) {
            return [
                'success' => true,
                'analysis' => $response['data']['choices'][0]['message']['content'],
                'usage' => $response['data']['usage'] ?? [],
                'provider' => $this->providerName,
            ];
        }

        return $response;
    }

    /**
     * Generate code with OpenAI
     *
     * @param string $prompt
     * @param string $language
     * @param array $options
     * @return array
     */
    public function generateCode(string $prompt, string $language = 'php', array $options = []): array
    {
        if (!$this->supportsFeature('code_generation')) {
            return $this->unsupportedFeature('code_generation');
        }

        $codePrompt = "Generate {$language} code for: {$prompt}\n\nPlease provide clean, well-commented code.";

        return $this->generateText($codePrompt, $options);
    }

    /**
     * Translate text with OpenAI
     *
     * @param string $text
     * @param string $targetLanguage
     * @param string $sourceLanguage
     * @return array
     */
    public function translateText(string $text, string $targetLanguage, string $sourceLanguage = 'auto'): array
    {
        if (!$this->supportsFeature('translation')) {
            return $this->unsupportedFeature('translation');
        }

        $translatePrompt = "Translate the following text from {$sourceLanguage} to {$targetLanguage}:\n\n\"{$text}\"\n\nProvide only the translation without additional commentary.";

        return $this->generateText($translatePrompt);
    }

    /**
     * Summarize text with OpenAI
     *
     * @param string $text
     * @param array $options
     * @return array
     */
    public function summarizeText(string $text, array $options = []): array
    {
        if (!$this->supportsFeature('summarization')) {
            return $this->unsupportedFeature('summarization');
        }

        $length = $options['length'] ?? 'medium'; // short, medium, long
        $style = $options['style'] ?? 'neutral'; // neutral, formal, casual

        $summarizePrompt = "Summarize the following text in a {$length} {$style} style:\n\n\"{$text}\"\n\nProvide a clear, concise summary.";

        return $this->generateText($summarizePrompt, $options);
    }

    /**
     * Analyze sentiment with OpenAI
     *
     * @param string $text
     * @return array
     */
    public function analyzeSentiment(string $text): array
    {
        if (!$this->supportsFeature('sentiment_analysis')) {
            return $this->unsupportedFeature('sentiment_analysis');
        }

        $sentimentPrompt = "Analyze the sentiment of the following text and provide:\n1. Overall sentiment (positive, negative, neutral)\n2. Confidence score (0-1)\n3. Emotional tone\n\nText: \"{$text}\"\n\nRespond in JSON format with keys: sentiment, confidence, tone.";

        $response = $this->generateText($sentimentPrompt);

        if ($response['success']) {
            try {
                $analysis = json_decode($response['text'], true);
                if ($analysis) {
                    $response['sentiment'] = $analysis['sentiment'] ?? 'neutral';
                    $response['confidence'] = $analysis['confidence'] ?? 0.5;
                    $response['tone'] = $analysis['tone'] ?? 'neutral';
                }
            } catch (\Exception $e) {
                $response['sentiment'] = 'neutral';
                $response['confidence'] = 0.5;
                $response['tone'] = 'neutral';
            }
        }

        return $response;
    }

    /**
     * Moderate content with OpenAI
     *
     * @param string $text
     * @return array
     */
    public function moderateContent(string $text): array
    {
        if (!$this->supportsFeature('content_moderation')) {
            return $this->unsupportedFeature('content_moderation');
        }

        $moderatePrompt = "Analyze the following content for safety and appropriateness. Check for:\n1. Hate speech\n2. Violence\n3. Sexual content\n4. Self-harm\n5. Illegal activities\n\nContent: \"{$text}\"\n\nRespond in JSON format with keys: is_safe (boolean), categories (array of detected issues), confidence (0-1).";

        $response = $this->generateText($moderatePrompt);

        if ($response['success']) {
            try {
                $moderation = json_decode($response['text'], true);
                if ($moderation) {
                    $response['is_safe'] = $moderation['is_safe'] ?? true;
                    $response['categories'] = $moderation['categories'] ?? [];
                    $response['confidence'] = $moderation['confidence'] ?? 0.5;
                }
            } catch (\Exception $e) {
                $response['is_safe'] = true;
                $response['categories'] = [];
                $response['confidence'] = 0.5;
            }
        }

        return $response;
    }

    /**
     * Process multimodal input with OpenAI
     *
     * @param array $inputs
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function processMultimodal(array $inputs, string $prompt, array $options = []): array
    {
        if (!$this->supportsFeature('multimodal')) {
            return $this->unsupportedFeature('multimodal');
        }

        $content = [
            [
                'type' => 'text',
                'text' => $prompt,
            ],
        ];

        foreach ($inputs as $input) {
            if ($input['type'] === 'image' && isset($input['url'])) {
                $content[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => $input['url'],
                    ],
                ];
            } elseif ($input['type'] === 'text' && isset($input['content'])) {
                $content[] = [
                    'type' => 'text',
                    'text' => $input['content'],
                ];
            }
        }

        $data = [
            'model' => $options['model'] ?? 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
            'max_tokens' => $options['max_tokens'] ?? $this->config['max_tokens'],
        ];

        $response = $this->makeRequest('/chat/completions', $data);

        if ($response['success'] && isset($response['data']['choices'][0]['message']['content'])) {
            return [
                'success' => true,
                'response' => $response['data']['choices'][0]['message']['content'],
                'usage' => $response['data']['usage'] ?? [],
                'provider' => $this->providerName,
            ];
        }

        return $response;
    }
}
