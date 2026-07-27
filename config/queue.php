<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nombre de la Conexión de Cola Predeterminada
    |--------------------------------------------------------------------------
    |
    | El sistema de colas de Laravel soporta una variedad de backends a través
    | de una API única y unificada, dándote un acceso conveniente a cada uno
    | mediante el uso de una sintaxis idéntica. La conexión predeterminada se
    | define a continuación.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Conexiones de Cola (Queue Connections)
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar las opciones de conexión para cada backend de cola
    | utilizado por tu aplicación. Se proporciona una configuración de ejemplo
    | para cada backend soportado por Laravel. También eres libre de añadir más.
    |
    | Controladores (Drivers): "sync", "database", "beanstalkd", "sqs", "redis",
    |                          "deferred", "background", "failover", "null"
    |
    */

    'connections' => [

        // Controlador síncrono: Ejecuta los trabajos inmediatamente en el mismo hilo (útil para desarrollo local)
        'sync' => [
            'driver' => 'sync',
        ],

        // Controlador basado en base de datos: Almacena los trabajos en una tabla relacional
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'), // Conexión de base de datos específica para la cola
            'table' => env('DB_QUEUE_TABLE', 'jobs'),    // Tabla donde se guardarán los trabajos pendientes
            'queue' => env('DB_QUEUE', 'default'),      // Nombre de la cola por defecto
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90), // Segundos a esperar antes de reintentar un trabajo que se quedó procesando
            'after_commit' => false,                     // Si se establece en true, el trabajo solo se encola si la transacción de BD actual tiene éxito
        ],

        // Controlador para Beanstalkd: Un servicio de colas de trabajo rápido y especializado
        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_QUEUE_HOST', 'localhost'), // Host del servidor Beanstalkd
            'queue' => env('BEANSTALKD_QUEUE', 'default'),       // Tubo (tube) o cola por defecto
            'retry_after' => (int) env('BEANSTALKD_QUEUE_RETRY_AFTER', 90), // Tiempo de reserva antes de que el trabajo vuelva a estar disponible
            'block_for' => 0,                                    // Segundos que el worker esperará en una consulta larga antes de cerrarse (0 deshabilitado)
            'after_commit' => false,
        ],

        // Controlador para Amazon SQS: Servicio de colas simple y administrado de AWS
        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),          // Clave de acceso de AWS
            'secret' => env('AWS_SECRET_ACCESS_KEY'),    // Clave secreta de AWS
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'), // Prefijo de la URL de tu cuenta de SQS
            'queue' => env('SQS_QUEUE', 'default'),      // Nombre de la cola de SQS externa
            'suffix' => env('SQS_SUFFIX'),               // Sufijo opcional para la cola (por ejemplo, .fifo)
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'), // Región de AWS donde está alojada la cola
            'after_commit' => false,
        ],

        // Controlador basado en Redis: Estructura de almacenamiento en caché clave-valor de alta velocidad
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'), // Conexión de Redis configurada en database.php
            'queue' => env('REDIS_QUEUE', 'default'),                 // Nombre de la clave/cola en Redis
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90), // Segundos antes de reincorporar un trabajo colgado a la cola
            'block_for' => null,                                      // Segundos para bloquear la conexión mientras se espera un trabajo (BLPOP)
            'after_commit' => false,
        ],

        // Controlador diferido (Deferred): Pospone la ejecución automática de trabajos hasta el final del ciclo de vida de la solicitud HTTP
        'deferred' => [
            'driver' => 'deferred',
        ],

        // Controlador en segundo plano (Background): Ejecuta los trabajos de forma asíncrona en un subproceso del servidor
        'background' => [
            'driver' => 'background',
        ],

        // Controlador de tolerancia a fallos (Failover): Si la cola primaria (ej. database) falla, conmuta automáticamente a la siguiente (ej. deferred)
        'failover' => [
            'driver' => 'failover',
            'connections' => [
                'database',
                'deferred',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Procesamiento de Trabajos en Lote (Job Batching)
    |--------------------------------------------------------------------------
    |
    | Las siguientes opciones configuran la base de datos y la tabla que almacena
    | la información de los lotes de trabajos. Estas opciones pueden actualizarse
    | a cualquier conexión de base de datos y tabla definida por tu aplicación.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'sqlite'), // Base de datos donde se registran los lotes
        'table' => 'job_batches',                     // Tabla que almacena el progreso del lote (completados, fallidos, etc.)
    ],

    /*
    |--------------------------------------------------------------------------
    | Trabajos de Cola Fallidos (Failed Queue Jobs)
    |--------------------------------------------------------------------------
    |
    | Estas opciones configuran el comportamiento del registro de trabajos fallidos
    | de la cola, permitiéndote controlar cómo y dónde se almacenan. Laravel incluye
    | soporte para almacenar los trabajos fallidos en un archivo simple o en una BD.
    |
    | Controladores soportados: "database-uuids", "dynamodb", "file", "null"
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'), // Controlador para el registro de fallos
        'database' => env('DB_CONNECTION', 'sqlite'),            // Conexión de base de datos para registrar los fallos
        'table' => 'failed_jobs',                                 // Tabla donde se volcará el log detallado del error y el payload del job
    ],

];