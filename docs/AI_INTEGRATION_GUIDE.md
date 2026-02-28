# AI Integration Guide - ChatGPT & Gemini

## Overview

This guide explains how to use the dual AI system supporting both ChatGPT (OpenAI) and Google Gemini APIs with full feature support.

## 🚀 Quick Setup

### 1. Environment Variables

Add to your `.env` file:

```env
# AI Configuration
AI_DEFAULT_PROVIDER=gemini

# OpenAI Settings
OPENAI_API_KEY=your_openai_api_key
OPENAI_MODEL=gpt-3.5-turbo
OPENAI_MAX_TOKENS=1000
OPENAI_TEMPERATURE=0.7

# Gemini Settings  
GEMINI_API_KEY=your_gemini_api_key
GEMINI_MODEL=gemini-pro
GEMINI_VISION_MODEL=gemini-pro-vision
GEMINI_MAX_TOKENS=1000
GEMINI_TEMPERATURE=0.7

# Fallback & Performance
AI_FALLBACK_ENABLED=true
AI_FALLBACK_PROVIDER=gemini
AI_CACHE_ENABLED=true
AI_RATE_LIMIT_ENABLED=true
```

## 📡 API Endpoints

### Text Generation
```
POST /api/v1/ai/generate-text
{
  "prompt": "Your text here",
  "provider": "gemini",
  "options": {
    "max_tokens": 1000,
    "temperature": 0.7
  }
}
```

### Image Analysis (Gemini Only)
```
POST /api/v1/ai/analyze-image
{
  "image": "https://example.com/image.jpg",
  "prompt": "Describe this image",
  "provider": "gemini"
}
```

### Multimodal Processing (Gemini Only)
```
POST /api/v1/ai/process-multimodal
{
  "inputs": [
    {"type": "image", "url": "image.jpg"},
    {"type": "text", "content": "Analyze this"}
  ],
  "prompt": "What do you see?"
}
```

## 🎯 Features Comparison

| Feature | ChatGPT | Gemini |
|---------|---------|---------|
| Text Generation | ✅ | ✅ |
| Image Analysis | ❌ | ✅ |
| Multimodal | ❌ | ✅ |
| Code Generation | ✅ | ✅ |
| Translation | ✅ | ✅ |
| Sentiment Analysis | ✅ | ✅ |
| Document Analysis | ❌ | ✅ |
| Creative Content | ✅ | ✅ |

## 🔧 Usage Examples

### Basic Text Generation
```php
$ai = app(AIService::class);
$result = $ai->generateText("Write a product description");
```

### Switch Providers
```php
$ai->setCurrentProvider('gemini');
$result = $ai->analyzeImage($imageUrl, "What's in this image?");
```

### Gemini Advanced Features
```php
// Structured data generation
$schema = ['type' => 'object', 'properties' => ['name' => ['type' => 'string']]];
$result = $ai->generateStructuredData("Extract name from text", $schema);

// Creative content
$result = $ai->generateCreativeContent('story', ['theme' => 'adventure']);

// Document analysis
$result = $ai->analyzeDocument($documentUrl, ['prompt' => 'Summarize this document']);
```

## 🛠️ Configuration

### Provider Features
Enable/disable features per provider in `config/ai.php`:

```php
'features' => [
    'image_analysis' => [
        'openai' => false,
        'gemini' => true,
    ],
    'multimodal' => [
        'openai' => false,
        'gemini' => true,
    ],
]
```

### Rate Limiting
```php
'rate_limiting' => [
    'enabled' => true,
    'requests_per_minute' => 60,
    'requests_per_hour' => 1000,
]
```

### Caching
```php
'cache' => [
    'enabled' => true,
    'ttl' => 3600, // 1 hour
]
```

## 📊 Monitoring

### Get Provider Info
```
GET /api/v1/ai/providers
```

### Test Connectivity
```
POST /api/v1/ai/test-provider
{
  "provider": "gemini"
}
```

### Usage Statistics
The system tracks:
- Request counts per provider
- Response times
- Error rates
- Cache hit rates

## 🔄 Fallback System

When primary provider fails, system automatically switches to fallback:

```php
// If OpenAI fails, automatically tries Gemini
$ai->generateText("Hello"); // Uses fallback if needed
```

## 🎨 Gemini-Specific Features

### Vision & Multimodal
- Image analysis with detailed descriptions
- Video and audio processing
- Document OCR and analysis
- Multi-input processing

### Advanced Generation
- Structured JSON output
- Creative writing (stories, poems, scripts)
- Code generation with explanations
- Translation with context

### Document Intelligence
- PDF analysis
- Image text extraction
- Table data extraction
- Document summarization

## 🔒 Security & Best Practices

1. **API Keys**: Store securely in environment variables
2. **Rate Limiting**: Enable to prevent abuse
3. **Caching**: Enable for performance and cost control
4. **Logging**: Monitor for errors and usage patterns
5. **Fallback**: Always configure backup provider

## 📱 Mobile Integration

### Flutter Example
```dart
Future<String> generateText(String prompt) async {
  final response = await http.post(
    Uri.parse('$baseUrl/api/v1/ai/generate-text'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'prompt': prompt,
      'provider': 'gemini',
    }),
  );
  
  final data = jsonDecode(response.body);
  return data['text'] ?? '';
}
```

## 🚨 Troubleshooting

### Common Issues

**API Key Errors**
- Verify keys in `.env` file
- Check key permissions and quotas

**Rate Limiting**
- Increase limits in config
- Enable caching to reduce requests

**Fallback Not Working**
- Ensure both providers are configured
- Check `AI_FALLBACK_ENABLED=true`

**Gemini Vision Issues**
- Use `gemini-pro-vision` model
- Check image format and size limits

## 📈 Performance Tips

1. **Use Caching**: Enable for repeated requests
2. **Choose Right Provider**: Gemini for vision, ChatGPT for text
3. **Optimize Prompts**: Keep prompts concise
4. **Monitor Usage**: Track costs and quotas
5. **Batch Requests**: Group similar requests

## 🔄 Migration from ChatGPT Only

If you're currently using only ChatGPT:

1. Add Gemini API key to `.env`
2. Set `AI_DEFAULT_PROVIDER=gemini` (optional)
3. Enable fallback to ChatGPT
4. Test all existing endpoints
5. Gradually migrate to Gemini features

## 📚 Additional Resources

- [OpenAI API Documentation](https://platform.openai.com/docs)
- [Gemini API Documentation](https://ai.google.dev/docs)
- [Laravel HTTP Client](https://laravel.com/docs/http-client)
- [API Rate Limiting](https://laravel.com/docs/rate-limiting)

---

This dual AI system provides maximum flexibility and reliability for your application's AI needs.
