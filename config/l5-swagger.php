<?php

/*
|--------------------------------------------------------------------------
| L5 Swagger / OpenAPI Configuration
|--------------------------------------------------------------------------
| Configures where annotations are scanned and where generated docs are
| stored. The Swagger UI is served at /api/documentation.
|
| Regenerate docs manually: php artisan l5-swagger:generate
| Auto-generate on every request: set L5_SWAGGER_GENERATE_ALWAYS=true in .env
*/

return [
    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Book Library API',
            ],

            'routes' => [
                // URL where the Swagger UI is accessible
                'api' => 'api/documentation',
            ],

            'paths' => [
                // Directory where the generated JSON/YAML spec file is saved
                'docs'      => storage_path('api-docs'),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',

                // Directories scanned for @OA\* annotation docblocks
                'annotations' => [
                    base_path('app'),
                ],

                'excludes' => [],
            ],

            'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

            'proxy'   => false,
            'ui'      => [
                'display' => [
                    'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'list'),
                    'filter'        => env('L5_SWAGGER_UI_FILTERS', true),
                ],
            ],

            'security'            => [],
            'securityDefinitions' => [
                'securitySchemes' => [],
                'security'        => [],
            ],
        ],
    ],

    'defaults' => [
        'routes' => [
            'docs'            => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware'      => [
                'api'            => [],
                'asset'          => [],
                'docs'           => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],

        'paths' => [
            'docs'                    => storage_path('api-docs'),
            'views'                   => base_path('resources/views/vendor/l5-swagger'),
            'base'                    => env('L5_SWAGGER_BASE_PATH', null),
            'swagger_ui_assets_path'  => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
            'excludes'                => [],
        ],

        'scanOptions' => [
            'default_processors_configuration' => [],
            'analyser'   => null,
            'analysis'   => null,
            'processors' => [],
            'pattern'    => null,
            'exclude'    => [],
            'open_api_spec_version' => env('SWAGGER_VERSION', \OpenApi\Generator::UNDEFINED),
        ],

        'securityDefinitions' => [
            'securitySchemes' => [],
            'security'        => [],
        ],

        'generate_always'      => env('L5_SWAGGER_GENERATE_ALWAYS', false),
        'generate_yaml_copy'   => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
        'proxy'                => false,
        'additional_config_url' => null,
        'operations_sort'      => env('L5_SWAGGER_OPERATIONS_SORT', null),
        'validator_url'        => null,

        'ui' => [
            'display' => [
                'doc_expansion'         => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
                'filter'                => env('L5_SWAGGER_UI_FILTERS', true),
                'show_extensions'       => false,
                'show_common_extensions' => false,
                'try_it_out_enabled'    => env('L5_SWAGGER_TRY_IT_OUT_ENABLED', true),
            ],
            'authorization' => [
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),
                'oauth2'                => [
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        'security' => [],
    ],
];
