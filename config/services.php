<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Servicios de Terceros (Third Party Services)
    |--------------------------------------------------------------------------
    |
    | Este archivo se utiliza para almacenar las credenciales de servicios de
    | terceros como Mailgun, Postmark, AWS y más. Este archivo proporciona la
    | ubicación de facto para este tipo de información, permitiendo que los
    | paquetes tengan un archivo convencional para localizar las distintas
    | credenciales de los servicios.
    |
    */

    // Credenciales para Postmark (Servicio de envío de correos transaccionales)
    'postmark' => [
        'key' => env('POSTMARK_API_KEY'), // Clave de la API de Postmark
    ],

    // Credenciales para Resend (Servicio moderno de envío de correos para desarrolladores)
    'resend' => [
        'key' => env('RESEND_API_KEY'), // Clave de la API de Resend
    ],

    // Credenciales para Amazon SES (Simple Email Service de AWS)
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),          // Clave de acceso de AWS
        'secret' => env('AWS_SECRET_ACCESS_KEY'),    // Clave secreta de AWS
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'), // Región de AWS por defecto para el servicio de correo
    ],

    // Configuración para la integración con canales y notificaciones de Slack
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'), // Token de acceso OAuth para el usuario bot de tu app de Slack
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),       // Canal de Slack predeterminado para el envío de alertas o mensajes
        ],
    ],

];