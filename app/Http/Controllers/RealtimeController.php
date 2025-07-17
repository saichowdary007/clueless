<?php

namespace App\Http\Controllers;

use App\Services\ApiKeyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RealtimeController extends Controller
{
    private ApiKeyService $apiKeyService;

    public function __construct(ApiKeyService $apiKeyService)
    {
        $this->apiKeyService = $apiKeyService;
    }

    /**
     * Generate ephemeral key for secure browser usage
     */
    public function generateEphemeralKey(Request $request)
    {
        try {
            $providers = ['openai', 'openrouter', 'anthropic', 'gemini', 'deepseek'];
            $data = null;
            foreach ($providers as $provider) {
                $apiKey = $this->apiKeyService->getApiKey($provider);
                if (!$apiKey) {
                    continue;
                }

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/realtime/sessions', [
                    'model' => 'gpt-4o-realtime-preview-2024-12-17',
                    'voice' => $request->input('voice', 'alloy'),
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    break;
                }

                if (in_array($response->status(), [401, 403, 500])) {
                    Log::warning("{$provider} key failed: " . $response->status());
                    continue;
                }

                Log::error('API error: ' . $response->body());
            }

            if (!$data) {
                if (!$this->apiKeyService->getFallbackKey($providers)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No API key configured',
                    ], 422);
                }

                throw new \Exception('Failed to generate ephemeral key');
            }

            if (!isset($data['client_secret']['value']) || !isset($data['client_secret']['expires_at'])) {
                Log::error('Invalid response structure from OpenAI API', ['response' => $data]);
                throw new \Exception('Invalid response structure from OpenAI API');
            }
            
            // Return ephemeral key data
            return response()->json([
                'status' => 'success',
                'ephemeralKey' => $data['client_secret']['value'],
                'expiresAt' => $data['client_secret']['expires_at'],
                'sessionId' => $data['id'] ?? null,
                'model' => $data['model'] ?? 'gpt-4o-realtime-preview-2024-12-17',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate ephemeral key: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate ephemeral key',
            ], 500);
        }
    }
}
