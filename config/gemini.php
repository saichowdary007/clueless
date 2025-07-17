<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gemini API Key and Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Gemini API Key and organization. This will be
    | used to authenticate with the Gemini API - you can find your API key
    | and organization on your Gemini dashboard, at https://openai.com.
    */

    'api_key' => env('GEMINI_API_KEY'),
    'organization' => env('GEMINI_ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | Gemini API Project
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Gemini API project. This is used optionally in
    | situations where you are using a legacy user API key and need association
    | with a project. This is not required for the newer API keys.
    */
    'project' => env('GEMINI_PROJECT'),

    /*
    |--------------------------------------------------------------------------
    | Gemini Base URL
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Gemini API base URL used to make requests. This
    | is needed if using a custom API endpoint. Defaults to: api.openai.com/v1
    */
    'base_uri' => env('GEMINI_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 30),
];
