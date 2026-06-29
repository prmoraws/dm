<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Nome da Aplicação
    |--------------------------------------------------------------------------
    |
    | Nome usado em notificações ou elementos da interface.
    |
    */

    'name' => env('APP_NAME', 'MW'),

    /*
    |--------------------------------------------------------------------------
    | Ambiente da Aplicação
    |--------------------------------------------------------------------------
    |
    | Define o ambiente de execução, configurado no .env.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Modo de Depuração
    |--------------------------------------------------------------------------
    |
    | Ativa mensagens de erro detalhadas com stack traces.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL da Aplicação
    |--------------------------------------------------------------------------
    |
    | Usada pelo Artisan para gerar URLs corretas.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Fuso Horário
    |--------------------------------------------------------------------------
    |
    | Fuso horário padrão para funções de data e hora.
    |
    */

    'timezone' => 'America/Sao_Paulo',

    /*
    |--------------------------------------------------------------------------
    | Configuração de Localidade
    |--------------------------------------------------------------------------
    |
    | Localidade padrão para tradução e localização.
    |
    */

    'locale' => env('APP_LOCALE', 'pt_BR'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'pt_BR'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Chave de Criptografia
    |--------------------------------------------------------------------------
    |
    | String aleatória de 32 caracteres para criptografia.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Driver de Modo de Manutenção
    |--------------------------------------------------------------------------
    |
    | Gerencia o modo de manutenção, com suporte a "file" ou "cache".
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Provedores de Serviço
    |--------------------------------------------------------------------------
    |
    | Carrega funcionalidades do framework e pacotes de terceiros.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Intervention\Image\Laravel\ServiceProvider::class, // Provedor para intervention/image-laravel
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Aliases de Classes
    |--------------------------------------------------------------------------
    |
    | Facilita o acesso às classes com nomes mais curtos.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'Image' => Intervention\Image\Laravel\Facades\Image::class, // Alias para intervention/image-laravel
    ])->toArray(),

];
