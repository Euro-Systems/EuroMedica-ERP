<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Valores Predeterminados de Autenticación
    |--------------------------------------------------------------------------
    |
    | Esta opción define el "guard" (guardián) de autenticación y el "broker"
    | (intermediario) de restablecimiento de contraseña predeterminados para
    | tu aplicación. Puedes cambiar estos valores según sea necesario, pero
    | son un punto de partida perfecto para la mayoría de las aplicaciones.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Guardianes de Autenticación (Guards)
    |--------------------------------------------------------------------------
    |
    | A continuación, puedes definir cada guardián de autenticación para tu
    | aplicación. Por supuesto, se ha definido una excelente configuración
    | predeterminada que utiliza el almacenamiento de sesión junto con el
    | proveedor de usuarios Eloquent.
    |
    | Todos los guardianes de autenticación tienen un proveedor de usuarios,
    | el cual define cómo se recuperan realmente los usuarios de la base de
    | datos u otro sistema de almacenamiento utilizado por la aplicación.
    | Normalmente, se utiliza Eloquent.
    |
    | Soportado: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Proveedores de Usuarios (Providers)
    |--------------------------------------------------------------------------
    |
    | Todos los guardianes de autenticación tienen un proveedor de usuarios,
    | el cual define cómo se recuperan realmente los usuarios de la base de
    | datos u otro sistema de almacenamiento utilizado por la aplicación.
    | Normalmente, se utiliza Eloquent.
    |
    | Si tienes múltiples tablas o modelos de usuarios, puedes configurar
    | múltiples proveedores para representar al modelo o tabla. Estos
    | proveedores pueden asignarse a cualquier guardián adicional que definas.
    |
    | Soportados: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Restablecimiento de Contraseñas
    |--------------------------------------------------------------------------
    |
    | Estas opciones de configuración especifican el comportamiento de la
    | funcionalidad de restablecimiento de contraseñas de Laravel, incluyendo
    | la tabla utilizada para el almacenamiento de tokens y el proveedor de
    | usuarios que se invoca para recuperar realmente a los usuarios.
    |
    | El tiempo de expiración (expire) es el número de minutos durante los cuales
    | cada token de restablecimiento se considerará válido. Esta característica
    | de seguridad hace que los tokens duren poco tiempo para que haya menos
    | margen de que sean adivinados. Puedes cambiarlo según lo necesites.
    |
    | La configuración de regulación (throttle) es el número de segundos que
    | un usuario debe esperar antes de generar más tokens de restablecimiento
    | de contraseña. Esto evita que el usuario genere rápidamente una cantidad
    | muy grande de tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tiempo de Expiración de la Confirmación de Contraseña
    |--------------------------------------------------------------------------
    |
    | Aquí puedes definir el número de segundos antes de que expire la ventana
    | de confirmación de contraseña y se les pida a los usuarios que vuelvan
    | a introducir su contraseña a través de la pantalla de confirmación. Por
    | defecto, el tiempo de espera dura tres horas (10800 segundos).
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];