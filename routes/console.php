<?php

//importa la clase Inspiring de Laravel, esta clase contiene frases motivacionales predeterminadas
use Illuminate\Foundation\Inspiring;

//importa la fachada Artisan para crear comandos personalizados
use Illuminate\Support\Facades\Artisan;


/*
/-----------------------------------------------
/Comando personalizado de Artisan
/-----------------------------------------------

/Aqui se define un comando llamado "inspire", (los comandos Artisan se ejecutan desde la terminal)
/Ejemplo de uso: php artisan inspire
*/


//crea el comando "inspire"
Artisan::command('inspire', function () {
    //Muestra en consola una frase inspiradora aleatoria
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote'); //define la descripcion del comando
