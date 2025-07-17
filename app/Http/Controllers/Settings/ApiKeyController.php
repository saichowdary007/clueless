<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\ApiKeyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ApiKeyController extends Controller
{
    private ApiKeyService $apiKeyService;

    public function __construct(ApiKeyService $apiKeyService)
    {
        $this->apiKeyService = $apiKeyService;
    }

    /**
     * Show the API keys settings page
     */
    public function edit(Request $request): Response
    {
        $providers = ['openai', 'anthropic', 'gemini', 'openrouter', 'deepseek'];
        $status = [];
        foreach ($providers as $provider) {
            $hasKey = $this->apiKeyService->hasApiKey($provider);
            $status[$provider] = [
                'hasKey' => $hasKey,
                'isUsingEnvKey' => !cache()->has('app_'.$provider.'_api_key') && $hasKey,
            ];
        }

        return Inertia::render('settings/ApiKeys', [
            'providers' => $status,
        ]);
    }

    /**
     * Update the OpenAI API key
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', 'in:openai,anthropic,gemini,openrouter,deepseek'],
            'api_key' => ['required', 'string', 'min:20'],
        ]);

        if (!$this->apiKeyService->validateApiKey($validated['provider'], $validated['api_key'])) {
            throw ValidationException::withMessages([
                'api_key' => ['The provided API key is invalid. Please check and try again.'],
            ]);
        }

        $this->apiKeyService->setApiKey($validated['provider'], $validated['api_key']);

        return redirect()->route('api-keys.edit')->with('success', 'API key updated successfully.');
    }

    /**
     * Delete the OpenAI API key
     */
    public function destroy(Request $request): RedirectResponse
    {
        $provider = $request->input('provider', 'openai');
        $this->apiKeyService->removeApiKey($provider);

        return redirect()->route('api-keys.edit')->with('success', 'API key deleted successfully.');
    }

    /**
     * Store the OpenAI API key (used by onboarding)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => ['sometimes', 'string', 'in:openai,anthropic,gemini,openrouter,deepseek'],
            'api_key' => ['required', 'string', 'min:20'],
        ]);

        $provider = $validated['provider'] ?? 'openai';
        $apiKey = $validated['api_key'];

        if (!$this->apiKeyService->validateApiKey($provider, $apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided API key is invalid. Please check and try again.',
            ], 422);
        }

        // Store the API key
        $this->apiKeyService->setApiKey($provider, $apiKey);

        return response()->json([
            'success' => true,
            'message' => 'API key saved successfully.',
        ]);
    }
}
