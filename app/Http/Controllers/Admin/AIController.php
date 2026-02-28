<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display AI management dashboard
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        try {
            $providers = $this->aiService->getProviderComparison();
            $usageStats = $this->aiService->getUsageStats();
            
            return view('admin-views.ai.dashboard', compact('providers', 'usageStats'));
        } catch (\Exception $e) {
            Log::error('AI dashboard error: ' . $e->getMessage());
            
            return view('admin-views.ai.dashboard')->with('error', 'Failed to load AI dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Display AI settings page
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        $aiConfig = config('ai');
        
        return view('admin-views.ai.settings', compact('aiConfig'));
    }

    /**
     * Update AI settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'default_provider' => 'required|string|in:openai,gemini',
            'openai_api_key' => 'required_if:openai_enabled,true|string',
            'openai_model' => 'string|max:100',
            'openai_max_tokens' => 'integer|min:1|max:4000',
            'openai_temperature' => 'numeric|min:0|max:2',
            'gemini_api_key' => 'required_if:gemini_enabled,true|string',
            'gemini_model' => 'string|max:100',
            'gemini_max_tokens' => 'integer|min:1|max:4000',
            'gemini_temperature' => 'numeric|min:0|max:2',
            'fallback_enabled' => 'boolean',
            'fallback_provider' => 'string|in:openai,gemini',
            'cache_enabled' => 'boolean',
            'cache_ttl' => 'integer|min:60|max:86400',
            'rate_limiting_enabled' => 'boolean',
            'requests_per_minute' => 'integer|min:1|max:1000',
            'logging_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update environment variables
            $this->updateEnvironmentVariables($request->all());
            
            // Clear cache to reload configuration
            Cache::flush();
            
            return redirect()->route('admin.ai.settings')
                ->with('success', 'AI settings updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('AI settings update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update AI settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Test AI provider connectivity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testProvider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|in:openai,gemini',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $result = $this->aiService->testProvider($request->input('provider'));
            
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
     * Get AI usage statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsageStats()
    {
        try {
            $stats = $this->aiService->getUsageStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('AI usage stats error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get usage statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear AI cache
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            $cacheKeys = Cache::get('ai_cache_keys', []);
            $cleared = 0;
            
            foreach ($cacheKeys as $key) {
                if (Cache::forget($key)) {
                    $cleared++;
                }
            }
            
            Cache::forget('ai_cache_keys');
            
            return response()->json([
                'success' => true,
                'message' => "Cleared {$cleared} cached AI responses",
            ]);
        } catch (\Exception $e) {
            Log::error('AI cache clear error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to clear cache: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get AI logs
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lines' => 'integer|min:10|max:1000',
            'level' => 'string|in:error,info,debug,warning',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $lines = $request->input('lines', 100);
            $level = $request->input('level', null);
            
            $logFile = storage_path('logs/ai.log');
            
            if (!file_exists($logFile)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'logs' => [],
                        'total_lines' => 0,
                    ],
                ]);
            }

            $content = file_get_contents($logFile);
            $logLines = explode("\n", $content);
            
            // Filter by level if specified
            if ($level) {
                $logLines = array_filter($logLines, function($line) use ($level) {
                    return stripos($line, strtoupper($level)) !== false;
                });
            }
            
            // Get last N lines
            $logLines = array_slice($logLines, -$lines);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'logs' => array_filter($logLines),
                    'total_lines' => count($logLines),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('AI logs error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get logs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update environment variables
     *
     * @param array $settings
     * @return void
     */
    private function updateEnvironmentVariables(array $settings)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);
        
        $mappings = [
            'default_provider' => 'AI_DEFAULT_PROVIDER',
            'openai_api_key' => 'OPENAI_API_KEY',
            'openai_model' => 'OPENAI_MODEL',
            'openai_max_tokens' => 'OPENAI_MAX_TOKENS',
            'openai_temperature' => 'OPENAI_TEMPERATURE',
            'gemini_api_key' => 'GEMINI_API_KEY',
            'gemini_model' => 'GEMINI_MODEL',
            'gemini_max_tokens' => 'GEMINI_MAX_TOKENS',
            'gemini_temperature' => 'GEMINI_TEMPERATURE',
            'fallback_enabled' => 'AI_FALLBACK_ENABLED',
            'fallback_provider' => 'AI_FALLBACK_PROVIDER',
            'cache_enabled' => 'AI_CACHE_ENABLED',
            'cache_ttl' => 'AI_CACHE_TTL',
            'rate_limiting_enabled' => 'AI_RATE_LIMIT_ENABLED',
            'requests_per_minute' => 'AI_REQUESTS_PER_MINUTE',
            'logging_enabled' => 'AI_LOGGING_ENABLED',
        ];

        foreach ($mappings as $field => $envVar) {
            if (isset($settings[$field])) {
                $value = is_bool($settings[$field]) ? ($settings[$field] ? 'true' : 'false') : $settings[$field];
                
                // Remove existing line
                $envContent = preg_replace("/^{$envVar}=.*$/m", '', $envContent);
                
                // Add new line
                $envContent .= "\n{$envVar}={$value}";
            }
        }
        
        file_put_contents($envFile, $envContent);
    }

    /**
     * Display AI analytics page
     *
     * @return \Illuminate\View\View
     */
    public function analytics()
    {
        try {
            $usageStats = $this->aiService->getUsageStats();
            $providers = $this->aiService->getProviderComparison();
            
            // Get recent logs for analytics
            $logFile = storage_path('logs/ai.log');
            $recentLogs = [];
            
            if (file_exists($logFile)) {
                $content = file_get_contents($logFile);
                $logLines = explode("\n", $content);
                $recentLogs = array_slice(array_filter($logLines), -50);
            }
            
            return view('admin-views.ai.analytics', compact('usageStats', 'providers', 'recentLogs'));
        } catch (\Exception $e) {
            Log::error('AI analytics error: ' . $e->getMessage());
            
            return view('admin-views.ai.analytics')->with('error', 'Failed to load analytics: ' . $e->getMessage());
        }
    }

    /**
     * Export AI configuration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportConfig()
    {
        try {
            $config = [
                'ai' => config('ai'),
                'exported_at' => now()->toISOString(),
                'version' => '1.0.0',
            ];
            
            return response()->json([
                'success' => true,
                'data' => $config,
            ]);
        } catch (\Exception $e) {
            Log::error('AI config export error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to export configuration: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import AI configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'config' => 'required|array',
            'config.ai' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $config = $request->input('config.ai');
            
            // Update environment variables
            $this->updateEnvironmentVariables($config);
            
            // Clear cache
            Cache::flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Configuration imported successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('AI config import error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to import configuration: ' . $e->getMessage(),
            ], 500);
        }
    }
}
