<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ApiKeyService
{
    private const CACHE_PREFIX = 'app_%s_api_key';

    /**
     * Get an API key for the given provider from cache or environment.
     */
    public function getApiKey(string $provider): ?string
    {
        $cacheKey = sprintf(self::CACHE_PREFIX, $provider);
        $cachedKey = Cache::get($cacheKey);
        if ($cachedKey) {
            return $cachedKey;
        }

        return config("{$provider}.api_key");
    }

    /**
     * Store an API key for a provider in cache.
     */
    public function setApiKey(string $provider, string $apiKey): void
    {
        Cache::forever(sprintf(self::CACHE_PREFIX, $provider), $apiKey);
    }

    /**
     * Remove the stored API key for a provider.
     */
    public function removeApiKey(string $provider): void
    {
        Cache::forget(sprintf(self::CACHE_PREFIX, $provider));
    }

    /**
     * Check if an API key is available for the provider.
     */
    public function hasApiKey(string $provider): bool
    {
        return !empty($this->getApiKey($provider));
    }

    /**
     * Validate an API key by hitting the provider's models endpoint.
     */
    public function validateApiKey(string $provider, string $apiKey): bool
    {
        try {
            $base = config("{$provider}.base_uri") ?: 'https://api.openai.com/v1';
            $url = rtrim($base, '/') . '/models';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->get($url);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Return the first available API key from the given providers.
     */
    public function getFallbackKey(array $providers): ?string
    {
        foreach ($providers as $provider) {
            $key = $this->getApiKey($provider);
            if (!empty($key)) {
                return $key;
            }
        }

        return null;
    }
}