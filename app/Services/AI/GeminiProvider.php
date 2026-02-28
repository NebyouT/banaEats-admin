<?php

namespace App\Services\AI;

class GeminiProvider extends AbstractAIProvider
{
    protected string $providerName = 'gemini';

    /**
     * Get Gemini headers
     *
     * @return array
     */
    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Parse Gemini response
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
     * Perform text generation with Gemini
     *
     * @param string $prompt
     * @param array $options
     * @return array
     */
    protected function performTextGeneration(string $prompt, array $options): array
    {
        $endpoint = "/models/{$this->config['model']}:generateContent?key={$this->config['api_key']}";
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? $this->config['temperature'],
                'topP' => $this->config['top_p'] ?? 0.8,
                'topK' => $this->config['top_k'] ?? 40,
                'maxOutputTokens' => $options['max_tokens'] ?? $this->config['max_tokens'],
            ],
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response['success'] && isset($response['data']['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'success' => true,
                'text' => $response['data']['candidates'][0]['content']['parts'][0]['text'],
                'usage' => $response['data']['usageMetadata'] ?? [],
                'provider' => $this->providerName,
            ];
        }

        return $response;
    }

    /**
     * Analyze image with Gemini
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

        $model = $options['model'] ?? $this->config['vision_model'];
        $endpoint = "/models/{$model}:generateContent?key={$this->config['api_key']}";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                        [
                            'inline_data' => [
                                'mime_type' => $this->getMimeType($imageUrl),
                                'data' => $this->getImageData($imageUrl),
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? $this->config['temperature'],
                'topP' => $this->config['top_p'] ?? 0.8,
                'topK' => $this->config['top_k'] ?? 40,
                'maxOutputTokens' => $options['max_tokens'] ?? $this->config['max_tokens'],
            ],
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response['success'] && isset($response['data']['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'success' => true,
                'analysis' => $response['data']['candidates'][0]['content']['parts'][0]['text'],
                'usage' => $response['data']['usageMetadata'] ?? [],
                'provider' => $this->providerName,
            ];
        }

        return $response;
    }

    /**
     * Generate code with Gemini
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

        $codePrompt = "Generate clean, well-commented {$language} code for: {$prompt}\n\nPlease provide:\n1. Complete working code\n2. Clear comments explaining the logic\n3. Error handling if applicable\n4. Best practices implementation";

        return $this->generateText($codePrompt, $options);
    }

    /**
     * Translate text with Gemini
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

        $translatePrompt = "Translate the following text from {$sourceLanguage} to {$targetLanguage}:\n\n\"{$text}\"\n\nProvide only the accurate translation without additional commentary. Maintain the original tone and context.";

        return $this->generateText($translatePrompt);
    }

    /**
     * Summarize text with Gemini
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
        $focus = $options['focus'] ?? 'general'; // general, key_points, conclusions

        $summarizePrompt = "Create a {$length} {$style} summary of the following text, focusing on {$focus}:\n\n\"{$text}\"\n\nProvide a clear, accurate, and comprehensive summary that captures the main ideas and important details.";

        return $this->generateText($summarizePrompt, $options);
    }

    /**
     * Analyze sentiment with Gemini
     *
     * @param string $text
     * @return array
     */
    public function analyzeSentiment(string $text): array
    {
        if (!$this->supportsFeature('sentiment_analysis')) {
            return $this->unsupportedFeature('sentiment_analysis');
        }

        $sentimentPrompt = "Analyze the sentiment of the following text and provide a detailed analysis:\n\n\"{$text}\"\n\nPlease respond in JSON format with:\n{\n  \"sentiment\": \"positive|negative|neutral\",\n  \"confidence\": 0.0-1.0,\n  \"emotional_tone\": \"string\",\n  \"key_emotions\": [\"emotion1\", \"emotion2\"],\n  \"intensity\": \"low|medium|high\",\n  \"explanation\": \"brief explanation\"\n}";

        $response = $this->generateText($sentimentPrompt);

        if ($response['success']) {
            try {
                $analysis = json_decode($response['text'], true);
                if ($analysis) {
                    $response['sentiment'] = $analysis['sentiment'] ?? 'neutral';
                    $response['confidence'] = $analysis['confidence'] ?? 0.5;
                    $response['tone'] = $analysis['emotional_tone'] ?? 'neutral';
                    $response['emotions'] = $analysis['key_emotions'] ?? [];
                    $response['intensity'] = $analysis['intensity'] ?? 'medium';
                    $response['explanation'] = $analysis['explanation'] ?? '';
                }
            } catch (\Exception $e) {
                $response['sentiment'] = 'neutral';
                $response['confidence'] = 0.5;
                $response['tone'] = 'neutral';
                $response['emotions'] = [];
                $response['intensity'] = 'medium';
                $response['explanation'] = '';
            }
        }

        return $response;
    }

    /**
     * Moderate content with Gemini
     *
     * @param string $text
     * @return array
     */
    public function moderateContent(string $text): array
    {
        if (!$this->supportsFeature('content_moderation')) {
            return $this->unsupportedFeature('content_moderation');
        }

        $moderatePrompt = "Analyze the following content for safety and appropriateness:\n\n\"{$text}\"\n\nPlease respond in JSON format with:\n{\n  \"is_safe\": true/false,\n  \"risk_level\": \"low|medium|high\",\n  \"categories\": [\"category1\", \"category2\"],\n  \"confidence\": 0.0-1.0,\n  \"explanation\": \"brief explanation\",\n  \"suggested_action\": \"allow|flag|block\"\n}\n\nCategories to check: hate_speech, violence, sexual_content, self_harm, illegal_activities, spam, harassment.";

        $response = $this->generateText($moderatePrompt);

        if ($response['success']) {
            try {
                $moderation = json_decode($response['text'], true);
                if ($moderation) {
                    $response['is_safe'] = $moderation['is_safe'] ?? true;
                    $response['risk_level'] = $moderation['risk_level'] ?? 'low';
                    $response['categories'] = $moderation['categories'] ?? [];
                    $response['confidence'] = $moderation['confidence'] ?? 0.5;
                    $response['explanation'] = $moderation['explanation'] ?? '';
                    $response['suggested_action'] = $moderation['suggested_action'] ?? 'allow';
                }
            } catch (\Exception $e) {
                $response['is_safe'] = true;
                $response['risk_level'] = 'low';
                $response['categories'] = [];
                $response['confidence'] = 0.5;
                $response['explanation'] = '';
                $response['suggested_action'] = 'allow';
            }
        }

        return $response;
    }

    /**
     * Process multimodal input with Gemini
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

        $model = $options['model'] ?? $this->config['vision_model'];
        $endpoint = "/models/{$model}:generateContent?key={$this->config['api_key']}";

        $parts = [
            [
                'text' => $prompt,
            ],
        ];

        foreach ($inputs as $input) {
            if ($input['type'] === 'image' && isset($input['url'])) {
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $this->getMimeType($input['url']),
                        'data' => $this->getImageData($input['url']),
                    ],
                ];
            } elseif ($input['type'] === 'text' && isset($input['content'])) {
                $parts[] = [
                    'text' => $input['content'],
                ];
            } elseif ($input['type'] === 'video' && isset($input['url'])) {
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => 'video/mp4',
                        'data' => $this->getVideoData($input['url']),
                    ],
                ];
            } elseif ($input['type'] === 'audio' && isset($input['url'])) {
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => 'audio/mp3',
                        'data' => $this->getAudioData($input['url']),
                    ],
                ];
            }
        }

        $data = [
            'contents' => [
                [
                    'parts' => $parts,
                ],
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? $this->config['temperature'],
                'topP' => $this->config['top_p'] ?? 0.8,
                'topK' => $this->config['top_k'] ?? 40,
                'maxOutputTokens' => $options['max_tokens'] ?? $this->config['max_tokens'],
            ],
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response['success'] && isset($response['data']['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'success' => true,
                'response' => $response['data']['candidates'][0]['content']['parts'][0]['text'],
                'usage' => $response['data']['usageMetadata'] ?? [],
                'provider' => $this->providerName,
            ];
        }

        return $response;
    }

    /**
     * Get MIME type from file URL or path
     *
     * @param string $url
     * @return string
     */
    private function getMimeType(string $url): string
    {
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'mp3' => 'audio/mp3',
            'wav' => 'audio/wav',
            'pdf' => 'application/pdf',
        ];

        return $mimeTypes[$extension] ?? 'image/jpeg';
    }

    /**
     * Get image data as base64
     *
     * @param string $url
     * @return string
     */
    private function getImageData(string $url): string
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $imageData = file_get_contents($url);
        } else {
            $imageData = file_get_contents(storage_path('app/public/' . $url));
        }

        return base64_encode($imageData);
    }

    /**
     * Get video data as base64
     *
     * @param string $url
     * @return string
     */
    private function getVideoData(string $url): string
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $videoData = file_get_contents($url);
        } else {
            $videoData = file_get_contents(storage_path('app/public/' . $url));
        }

        return base64_encode($videoData);
    }

    /**
     * Get audio data as base64
     *
     * @param string $url
     * @return string
     */
    private function getAudioData(string $url): string
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $audioData = file_get_contents($url);
        } else {
            $audioData = file_get_contents(storage_path('app/public/' . $url));
        }

        return base64_encode($audioData);
    }

    /**
     * Advanced feature: Generate structured data
     *
     * @param string $prompt
     * @param array $schema
     * @param array $options
     * @return array
     */
    public function generateStructuredData(string $prompt, array $schema, array $options = []): array
    {
        $structuredPrompt = "Generate structured data based on the following schema:\n\nSchema: " . json_encode($schema, JSON_PRETTY_PRINT) . "\n\nRequest: {$prompt}\n\nPlease respond with valid JSON that matches the schema exactly.";

        $response = $this->generateText($structuredPrompt, $options);

        if ($response['success']) {
            try {
                $structuredData = json_decode($response['text'], true);
                if ($structuredData) {
                    $response['structured_data'] = $structuredData;
                    $response['valid_schema'] = true;
                } else {
                    $response['structured_data'] = null;
                    $response['valid_schema'] = false;
                }
            } catch (\Exception $e) {
                $response['structured_data'] = null;
                $response['valid_schema'] = false;
            }
        }

        return $response;
    }

    /**
     * Advanced feature: Generate creative content
     *
     * @param string $type
     * @param array $params
     * @param array $options
     * @return array
     */
    public function generateCreativeContent(string $type, array $params, array $options = []): array
    {
        $creativePrompts = [
            'story' => "Write a creative story with the following elements: " . json_encode($params),
            'poem' => "Write a poem with the following theme and style: " . json_encode($params),
            'script' => "Write a script with the following characters and plot: " . json_encode($params),
            'dialogue' => "Create a dialogue between characters with this context: " . json_encode($params),
            'description' => "Write a vivid description of: " . json_encode($params),
        ];

        $prompt = $creativePrompts[$type] ?? $params['prompt'] ?? '';

        return $this->generateText($prompt, $options);
    }

    /**
     * Advanced feature: Analyze document
     *
     * @param string $documentUrl
     * @param array $options
     * @return array
     */
    public function analyzeDocument(string $documentUrl, array $options = []): array
    {
        $model = $options['model'] ?? $this->config['vision_model'];
        $endpoint = "/models/{$model}:generateContent?key={$this->config['api_key']}";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $options['prompt'] ?? 'Analyze this document and provide a comprehensive summary including key points, structure, and important information.',
                        ],
                        [
                            'inline_data' => [
                                'mime_type' => $this->getMimeType($documentUrl),
                                'data' => $this->getImageData($documentUrl),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->makeRequest($endpoint, $data);

        if ($response['success'] && isset($response['data']['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'success' => true,
                'analysis' => $response['data']['candidates'][0]['content']['parts'][0]['text'],
                'usage' => $response['data']['usageMetadata'] ?? [],
                'provider' => $this->providerName,
            ];
        }

        return $response;
    }
}
