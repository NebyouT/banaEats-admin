<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Generate text response
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateText(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:4000',
            'provider' => 'sometimes|string|in:openai,gemini',
            'options' => 'sometimes|array',
            'options.max_tokens' => 'integer|min:1|max:4000',
            'options.temperature' => 'numeric|min:0|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider', config('ai.default_provider'));
            $this->aiService->setCurrentProvider($provider);

            $result = $this->aiService->generateText(
                $request->input('prompt'),
                $request->input('options', [])
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI text generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate text: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string|url',
            'prompt' => 'required|string|max:2000',
            'provider' => 'sometimes|string|in:openai,gemini',
            'options' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider', config('ai.default_provider'));
            $this->aiService->setCurrentProvider($provider);

            $result = $this->aiService->analyzeImage(
                $request->input('image'),
                $request->input('prompt'),
                $request->input('options', [])
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI image analysis error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate code
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:2000',
            'language' => 'sometimes|string|max:50',
            'provider' => 'sometimes|string|in:openai,gemini',
            'options' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider', config('ai.default_provider'));
            $this->aiService->setCurrentProvider($provider);

            $result = $this->aiService->generateCode(
                $request->input('prompt'),
                $request->input('language', 'php'),
                $request->input('options', [])
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI code generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate code: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Translate text
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translateText(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:2000',
            'target_language' => 'required|string|max:50',
            'source_language' => 'sometimes|string|max:50',
            'provider' => 'sometimes|string|in:openai,gemini',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider', config('ai.default_provider'));
            $this->aiService->setCurrentProvider($provider);

            $result = $this->aiService->translateText(
                $request->input('text'),
                $request->input('target_language'),
                $request->input('source_language', 'auto')
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI translation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to translate text: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Summarize text
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function summarizeText(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:10000',
            'provider' => 'sometimes|string|in:openai,gemini',
            'options' => 'sometimes|array',
            'options.length' => 'string|in:short,medium,long',
            'options.style' => 'string|in:neutral,formal,casual',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider', config('ai.default_provider'));
            $this->aiService->setCurrentProvider($provider);

            $result = $this->aiService->summarizeText(
                $request->input('text'),
                $request->input('options', [])
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI summarization error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to summarize text: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze sentiment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeSentiment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:2000',
            'provider' => 'sometimes|string|in:openai,gemini',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider', config('ai.default_provider'));
            $this->aiService->setCurrentProvider($provider);

            $result = $this->aiService->analyzeSentiment($request->input('text'));

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI sentiment analysis error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze sentiment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Moderate content
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function moderateContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:2000',
            'provider' => 'sometimes|string|in:openai,gemini',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider', config('ai.default_provider'));
            $this->aiService->setCurrentProvider($provider);

            $result = $this->aiService->moderateContent($request->input('text'));

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI content moderation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to moderate content: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process multimodal input
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processMultimodal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inputs' => 'required|array|min:1',
            'inputs.*.type' => 'required|string|in:image,text,video,audio',
            'inputs.*.url' => 'required_if:inputs.*.type,image,video,audio|string|url',
            'inputs.*.content' => 'required_if:inputs.*.type,text|string',
            'prompt' => 'required|string|max:2000',
            'provider' => 'sometimes|string|in:openai,gemini',
            'options' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider', config('ai.default_provider'));
            $this->aiService->setCurrentProvider($provider);

            $result = $this->aiService->processMultimodal(
                $request->input('inputs'),
                $request->input('prompt'),
                $request->input('options', [])
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI multimodal processing error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to process multimodal input: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate structured data (Gemini specific)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateStructuredData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string|max:2000',
            'schema' => 'required|array',
            'options' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->aiService->setCurrentProvider('gemini');

            $result = $this->aiService->generateStructuredData(
                $request->input('prompt'),
                $request->input('schema'),
                $request->input('options', [])
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI structured data generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate structured data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate creative content (Gemini specific)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateCreativeContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:story,poem,script,dialogue,description',
            'params' => 'required|array',
            'options' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->aiService->setCurrentProvider('gemini');

            $result = $this->aiService->generateCreativeContent(
                $request->input('type'),
                $request->input('params'),
                $request->input('options', [])
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI creative content generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate creative content: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze document (Gemini specific)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|string|url',
            'options' => 'sometimes|array',
            'options.prompt' => 'sometimes|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->aiService->setCurrentProvider('gemini');

            $result = $this->aiService->analyzeDocument(
                $request->input('document'),
                $request->input('options', [])
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI document analysis error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to analyze document: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get provider information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProviders()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'available_providers' => $this->aiService->getAvailableProviders(),
                    'current_provider' => $this->aiService->getCurrentProvider()->getProviderName(),
                    'comparison' => $this->aiService->getProviderComparison(),
                    'usage_stats' => $this->aiService->getUsageStats(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('AI provider info error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get provider information: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test provider connectivity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testProvider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'sometimes|string|in:openai,gemini',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->input('provider');
            $result = $this->aiService->testProvider($provider);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI provider test error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to test provider: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload and analyze image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAndAnalyzeImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'prompt' => 'required|string|max:2000',
            'provider' => 'sometimes|string|in:openai,gemini',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Upload image
            $image = $request->file('image');
            $path = $image->store('ai-images', 'public');
            $imageUrl = asset('storage/' . $path);

            // Analyze image
            $provider = $request->input('provider', config('ai.default_provider'));
            $this->aiService->setCurrentProvider($provider);

            $result = $this->aiService->analyzeImage($imageUrl, $request->input('prompt'));

            if ($result['success']) {
                $result['image_url'] = $imageUrl;
                $result['image_path'] = $path;
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('AI image upload and analysis error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to upload and analyze image: ' . $e->getMessage(),
            ], 500);
        }
    }
}
