<?php

use App\Services\ApiKeyService;

it('returns a successful response', function () {
    // Mock API key service to return true (API key exists)
    $mockApiKeyService = Mockery::mock(ApiKeyService::class);
    $mockApiKeyService->shouldReceive('getFallbackKey')->andReturn('key');
    $this->app->instance(ApiKeyService::class, $mockApiKeyService);
    
    $response = $this->get('/api/openai/status');

    $response->assertStatus(200);
});
