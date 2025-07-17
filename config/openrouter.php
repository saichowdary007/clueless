<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenRouter API Key and Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenRouter API Key and organization. This will be
    | used to authenticate with the OpenRouter API - you can find your API key
    | and organization on your OpenRouter dashboard, at https://openai.com.
    */

    'api_key' => env('OPENROUTER_API_KEY'),
    'organization' => env('OPENROUTER_ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | OpenRouter API Project
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenRouter API project. This is used optionally in
    | situations where you are using a legacy user API key and need association
    | with a project. This is not required for the newer API keys.
    */
    'project' => env('OPENROUTER_PROJECT'),

    /*
    |--------------------------------------------------------------------------
    | OpenRouter Base URL
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenRouter API base URL used to make requests. This
    | is needed if using a custom API endpoint. Defaults to: api.openai.com/v1
    */
    'base_uri' => env('OPENROUTER_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('OPENROUTER_REQUEST_TIMEOUT', 30),
];
