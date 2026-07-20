<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nombre de la aplicacion
    |--------------------------------------------------------------------------
    |
    | Este valor es el nombre de la aplicacion, el cual se utilizara cuando el
    | framework necesite colocar el nombre de la aplicacion en una notificacion
    | u otros elementos de la interfaz de usuario donde se requiera mostrarlo
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Entorno de la aplicacion
    |--------------------------------------------------------------------------
    |
    | Este valor determina el "entorno" en el que se esta ejecutando actulamente 
    | la aplicacion. Esto puede definir como prefieres configurar diversos
    | servicios que utiliza la aplicacion. Configuralo en el archuvo ".env"
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Modo de depuracion de la aplicacion (debug)
    |--------------------------------------------------------------------------
    |
    | Cuando la aplicacion esta en modo de depuracion, se mostraran mensajes de
    | error detallados con seguimientos de la pila (stack traces) en cada error
    | que ocurra dentro de la aplicacion. Si se desactiva, se mostrará una 
    | página de error génerica y simple
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL de la aplicacion
    |--------------------------------------------------------------------------
    | 
    | Esta URl es utilizadas por la consola para generar correctamente las URLs
    | cuando se utiliza la herramienta de línea de comandos Artisan. 
    | 
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Zona horaria de la aplicacion
    |--------------------------------------------------------------------------
    |
    | Aqui puedes especificar la zona horaria predeterminada para la aplicacion
    | la cual sera utilizada por las funciones de fecha y hora de PHP. La zona 
    | horaria esta configurada en "UTC" por defecto, ya que es adecuada para la
    | mayoria de los casos de uso 
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Configuracion de localizacion (idioma) de la aplicacion
    |--------------------------------------------------------------------------
    |
    | La localizacion de la aplicacion determina el idioma predeterminado que
    | se utilizara en los metodos de traduccion / localizacion de Laravel. Esta
    | opcion puede establacerse en cualquier idioma para el cual se tenga previsto
    | disponer de cadenas de traduccion
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Clave de encriptacion
    |--------------------------------------------------------------------------
    |
    | Esta clave es utilizada por los servicios de encriptacion de Laravel y 
    | debe configurarse con una cadena aleatoria de 32 caracteres para garantizar
    | que todos los valores encriptados esten seguros. Debes hacer esto antes
    | de desplegar la aplicacion
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Controlador del modo de mantenmiento (Driver)
    |--------------------------------------------------------------------------
    |
    | Estas opciones de configuracion determinan el controlador utilizado para 
    | definir y gestionar el estado del "modo de mantenimiento" de Laravel.
    | El controlador "cache" permitira controlar el modo de mantenimiento 
    | a traves de multiples servidores o maquinas 
    | 
    | Controladores soportados: "files", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
