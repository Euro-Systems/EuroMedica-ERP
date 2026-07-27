<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Controlador de Sesión Predeterminado (Default Session Driver)
    |--------------------------------------------------------------------------
    |
    | Esta opción determina el controlador de sesión predeterminado que se utiliza
    | para las solicitudes entrantes. Laravel soporta una variedad de opciones de
    | almacenamiento para persistir los datos de la sesión. El almacenamiento en
    | base de datos es una excelente opción por defecto.
    |
    | Soportados: "file", "cookie", "database", "memcached",
    |             "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Tiempo de Vida de la Sesión (Session Lifetime)
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar el número de minutos que deseas que la sesión
    | permanezca inactiva antes de que expire. Si deseas que expiren
    | inmediatamente al cerrar el navegador, puedes indicarlo a través de
    | la opción de configuración 'expire_on_close'.
    |
    */

    'lifetime' => (int) env('SESSION_LIFETIME', 120), // Duración de la sesión en minutos

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false), // ¿Expirar sesión al cerrar el navegador?

    /*
    |--------------------------------------------------------------------------
    | Encriptación de la Sesión (Session Encryption)
    |--------------------------------------------------------------------------
    |
    | Esta opción te permite especificar fácilmente que todos los datos de tu
    | sesión deben ser encriptados antes de ser almacenados. Toda la encriptación
    | es realizada automáticamente por Laravel y puedes usar la sesión normalmente.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', false), // Habilita/deshabilita el cifrado de los datos guardados en la sesión

    /*
    |--------------------------------------------------------------------------
    | Ubicación de Archivos de Sesión (Session File Location)
    |--------------------------------------------------------------------------
    |
    | Al utilizar el controlador de sesión "file", los archivos de sesión se
    | guardan en el disco. La ubicación de almacenamiento predeterminada se
    | define aquí; sin embargo, eres libre de proporcionar otra ubicación.
    |
    */

    'files' => storage_path('framework/sessions'), // Ruta del servidor donde se alojan los archivos de sesión (si usas driver 'file')

    /*
    |--------------------------------------------------------------------------
    | Conexión de Base de Datos de la Sesión (Session Database Connection)
    |--------------------------------------------------------------------------
    |
    | Al usar los controladores de sesión "database" o "redis", puedes especificar
    | una conexión que deba usarse para gestionar estas sesiones. Esto debe
    | corresponder a una conexión en tus opciones de configuración de base de datos.
    |
    */

    'connection' => env('SESSION_CONNECTION'), // Conexión específica de BD/Redis para las sesiones (null usa la predeterminada)

    /*
    |--------------------------------------------------------------------------
    | Tabla de Base de Datos de la Sesión (Session Database Table)
    |--------------------------------------------------------------------------
    |
    | Al usar el controlador de sesión "database", puedes especificar la tabla
    | que se utilizará para almacenar las sesiones. Por supuesto, se define un
    | valor predeterminado sensato; sin embargo, eres libre de cambiarlo.
    |
    */

    'table' => env('SESSION_TABLE', 'sessions'), // Nombre de la tabla en la base de datos

    /*
    |--------------------------------------------------------------------------
    | Almacenamiento de Caché de la Sesión (Session Cache Store)
    |--------------------------------------------------------------------------
    |
    | Al usar uno de los backends de sesión basados en la caché del framework,
    | puedes definir el almacén de caché que debe usarse para guardar los datos
    | de la sesión entre solicitudes. Debe coincidir con uno de tus almacenes de caché.
    |
    | Afecta a: "dynamodb", "memcached", "redis"
    |
    */

    'store' => env('SESSION_STORE'), // Tienda de caché específica asignada a la sesión

    /*
    |--------------------------------------------------------------------------
    | Lotería de Limpieza de Sesiones (Session Sweeping Lottery)
    |--------------------------------------------------------------------------
    |
    | Algunos controladores de sesión deben limpiar manualmente su ubicación de
    | almacenamiento para eliminar las sesiones antiguas. Aquí están las de probabilidades
    | de que esto ocurra en una solicitud determinada. Por defecto, la probabilidad es de 2 de 100.
    |
    */

    'lottery' => [2, 100], // [X, Y] significa un X% de probabilidad (X de cada Y peticiones) de ejecutar el Garbage Collector

    /*
    |--------------------------------------------------------------------------
    | Nombre de la Cookie de Sesión (Session Cookie Name)
    |--------------------------------------------------------------------------
    |
    | Aquí puedes cambiar el nombre de la cookie de sesión creada por el
    | framework. Por lo general, no deberías necesitar cambiar este valor,
    | ya que hacerlo no otorga una mejora de seguridad significativa.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session' // Nombre del identificador de la cookie generado a partir del nombre de la app
    ),

    /*
    |--------------------------------------------------------------------------
    | Ruta de la Cookie de Sesión (Session Cookie Path)
    |--------------------------------------------------------------------------
    |
    | La ruta de la cookie de sesión determina la ruta para la cual se considerará
    | disponible la cookie. Normalmente, esta será la ruta raíz de tu
    | aplicación, pero eres libre de cambiarla cuando sea necesario.
    |
    */

    'path' => env('SESSION_PATH', '/'), // Disponibilidad de la ruta en el dominio (por defecto todo el sitio)

    /*
    |--------------------------------------------------------------------------
    | Dominio de la Cookie de Sesión (Session Cookie Domain)
    |--------------------------------------------------------------------------
    |
    | Este valor determina el dominio y los subdominios para los cuales está
    | disponible la cookie de sesión. Por defecto, la cookie estará disponible
    | para el dominio raíz sin subdominios. Normalmente, esto no debe cambiarse.
    |
    */

    'domain' => env('SESSION_DOMAIN'), // Restringe la cookie a un dominio o subdominio específico (ej: '.tuweb.com')

    /*
    |--------------------------------------------------------------------------
    | Cookies Solo para HTTPS (HTTPS Only Cookies)
    |--------------------------------------------------------------------------
    |
    | Al establecer esta opción en true, las cookies de sesión solo se enviarán
    | de regreso al servidor si el navegador tiene una conexión HTTPS. Esto evitará
    | que la cookie se envíe cuando no se pueda hacer de forma segura.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'), // Fuerza al navegador a transmitir la cookie únicamente bajo conexiones SSL/TLS

    /*
    |--------------------------------------------------------------------------
    | Acceso Solo por HTTP (HTTP Access Only)
    |--------------------------------------------------------------------------
    |
    | Establecer este valor en true evitará que JavaScript acceda al valor
    | de la cookie y la cookie solo será accesible a través del protocolo HTTP.
    | Es poco probable que debas deshabilitar esta opción.
    |
    */

    'http_only' => env('SESSION_HTTP_ONLY', true), // Protege contra ataques XSS al bloquear el acceso a la cookie vía document.cookie

    /*
    |--------------------------------------------------------------------------
    | Cookies del Mismo Sitio (Same-Site Cookies)
    |--------------------------------------------------------------------------
    |
    | Esta opción determina cómo se comportan tus cookies cuando se realizan
    | solicitudes entre sitios (cross-site), y puede usarse para mitigar ataques CSRF.
    | Por defecto, estableceremos este valor en "lax" para permitir solicitudes seguras entre sitios.
    |
    | Ver: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Soportados: "lax", "strict", "none", null
    |
    */

    'same_site' => env('SESSION_SAME_SITE', 'lax'), // Control de comportamiento de navegación cruzada para mitigar CSRF

    /*
    |--------------------------------------------------------------------------
    | Cookies Particionadas (Partitioned Cookies)
    |--------------------------------------------------------------------------
    |
    | Establecer este valor en true vinculará la cookie al sitio de nivel superior
    | para un contexto de sitios cruzados (CHIPS). Las cookies particionadas son aceptadas
    | por el navegador cuando están marcadas como "secure" y el atributo Same-Site se define en "none".
    |
    */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false), // Soporte para cookies particionadas (CHIPS) frente al bloqueo de cookies de terceros

];