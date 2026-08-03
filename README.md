# 🚀 Guía de Inicio - EuroMédica Clínica

¡Bienvenido al proyecto! Esta guía te ayudará a configurar el sistema en tu computadora y te enseñará cómo trabajar con él paso a paso, incluso si eres nuevo en Laravel.

---

## 💻 Paso 1: Instalar el Proyecto en tu Computadora

Sigue estos sencillos pasos en orden usando tu terminal (consola):

1. **Descargar el código (Clonar):**

    ```bash
    git clone https://github.com/Euro-Systems/EuroMedica-ERP
    cd clinica
    ```

2. **Instalar las librerías de PHP:**
   _(Esto descarga todo lo necesario para que Laravel funcione)._

    ```bash
    composer install
    ```

3. **Instalar las librerías de Diseño y Pantallas:**
   _(Esto descarga las herramientas visuales del proyecto)._

    ```bash
    npm install
    ```

4. **Crear tu archivo de configuración:**
   Copia el archivo de plantilla con este comando:

    ```bash
    cp .env.example .env
    ```

5. **Generar la llave de seguridad:**
   _(Laravel la necesita para encriptar sesiones y contraseñas)._

    ```bash
    php artisan key:generate
    ```

6. **Encender las pantallas (Assets):**
   _(Deja esta terminal abierta para ver los cambios visuales en vivo)._
    ```bash
    npm run dev
    ```

---

## 🔑 Paso 2: Conectar la Base de Datos

El archivo `.env` es el "cerebro" donde guardamos las contraseñas. **¡Por seguridad, este archivo nunca se sube a GitHub!**

Para conectar tu base de datos:

1. Abre el archivo `.env` con cualquier editor de código.
2. Busca la sección que dice `DB_` y **pide los datos reales al encargado del puesto de tecnologías** para llenar estos campos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1          # Cambia por la dirección IP de tu servidor (ej: Hostinger)
DB_PORT=3306
DB_DATABASE=mi_base_datos  # El nombre de tu base de datos
DB_USERNAME=mi_usuario     # El usuario de tu base de datos
DB_PASSWORD=mi_contraseña  # La contraseña de tu base de datos
```

_Nota: Si solo estás probando en local y no tienes MySQL instalado, puedes escribir `DB_CONNECTION=sqlite` y borrar el resto de las líneas de base de datos._

---

## 📂 Paso 3: Cómo Crear Nuevas Tablas (Migraciones)

En Laravel **no** creamos las tablas escribiendo código SQL en phpMyAdmin. En su lugar, usamos "Migraciones", que son como planos de construcción escritos en PHP.

### Ejemplo paso a paso para crear una tabla de "Pacientes":

1. **Crear el plano (Migración):**
   Escribe en tu consola:

    ```bash
    php artisan make:migration create_pacientes_table
    ```

    _Esto creará un archivo nuevo dentro de la carpeta `database/migrations/`._

2. **Diseñar la tabla:**
   Abre ese archivo nuevo y escribe qué columnas quieres que tenga tu tabla:

    ```php
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id(); // Crea un ID automático
            $table->string('nombre'); // Crea un campo de texto para el nombre
            $table->string('telefono'); // Crea un campo de texto para el teléfono
            $table->timestamps(); // Registra fecha de creación y edición automáticamente
        });
    }
    ```

3. **Crear el modelo (para poder usar la tabla en el código):**

    ```bash
    php artisan make:model Paciente
    ```

4. **Aplicar los cambios a la base de datos real:**
   Para que las tablas se creen de verdad en tu MySQL, corre este comando:
    ```bash
    php artisan migrate
    ```

---

## 📥 Paso 4: Cómo Subir tus Cambios a GitHub de Forma Segura (Paso a Paso)

Para mantener la seguridad y el orden del proyecto, **nunca trabajamos ni subimos cambios directamente a la rama `main`**. Sigue este flujo en orden:

---

### 1️⃣ Crea tu rama

Antes de empezar a escribir código o hacer cambios en tu computadora, abre tu terminal y crea tu propia rama con un nombre representativo de lo que vas a desarrollar (ejemplo: `mi-nueva-funcion` o `feature/mantenimiento-modulo`):

```bash
git checkout -b mi-nueva-funcion
```

> 💡 _Este comando crea la rama y te cambia a ella inmediatamente._

---

### 2️⃣ Añade los cambios y revisa qué vas a subir

Una vez que terminaste de programar o probar:

1. **Revisa el estado de tus archivos:**
    ```bash
    git status
    ```

#### 🚨 REVISIÓN DE SEGURIDAD IMPORTANTE (Checklist):

- ❌ **JAMÁS subas el archivo `.env`**, llaves privadas (`.pem`, `.key`), tokens ni contraseñas reales.
- ❌ **JAMÁS subas logs de errores** (`laravel.log`) ni archivos de pruebas temporales (ej: `test_user.php`, `temp_users.txt`).
- ⚙️ **¿Agregaste una nueva variable en tu `.env` local?**
  Si agregaste algo nuevo (ej: `API_CORREO=`), abre el archivo `.env.example` y agrégala con el valor vacío:
    ```env
    API_CORREO=
    ```
    _(Esto le indica al equipo qué variables deben configurar en el servidor o en su local)._

2. **Añade los archivos al área de preparación:**
    ```bash
    git add .
    ```
    _(Si solo quieres agregar un archivo específico, usa `git add ruta/del/archivo.php`)._

---

### 3️⃣ Guarda tus cambios (Commit)

Crea el mensaje del cambio describiendo brevemente lo que hiciste:

```bash
git commit -m "feat: agrega formulario de actividades y mejoras en reportes"
```

---

### 4️⃣ Sube tu rama a GitHub

Envía tu rama local al repositorio en GitHub con el siguiente comando:

```bash
git push -u origin mi-nueva-funcion
```

_(Sustituye `mi-nueva-funcion` por el nombre exacto de la rama que creaste)._

---

### 5️⃣ Haz el Pull Request (PR) en GitHub (Guía de Clicks)

Una vez ejecutado el `git push`, ve a tu navegador web a la página de GitHub del proyecto:
👉 **[https://github.com/Euro-Systems/EuroMedica-ERP](https://github.com/Euro-Systems/EuroMedica-ERP)**

Sigue estos clicks exactos:

1. **Aviso automático en GitHub:**
   En la pantalla principal del repositorio, verás un recuadro amarillo arriba que dice:
   `"mi-nueva-funcion had recent pushes..."`
   👉 Haz click en el botón verde **`Compare & pull request`**.

2. **Si no aparece el recuadro amarillo:**
    - Haz click en la pestaña **`Pull requests`** (en la parte superior de la pantalla).
    - Haz click en el botón verde **`New pull request`** (arriba a la derecha).
    - En el menú desplegable que dice **`compare:`**, selecciona tu rama (`mi-nueva-funcion`).
    - Verifica que el menú **`base:`** tenga seleccionada la rama **`main`**.
    - Haz click en **`Create pull request`**.

3. **Llenar la descripción del Pull Request:**
   En el formulario del PR, coloca un título claro y copia/completa esta plantilla en la descripción:

    ```markdown
    ### 📝 ¿Qué hace este cambio o nueva funcionalidad?

    - Describe aquí en 2 o 3 viñetas qué agregaste o corregiste.

    ### 🗄️ ¿Se agregaron o modificaron tablas de Base de Datos?

    - [ ] Sí (Se debe ejecutar php artisan migrate en el servidor).
    - [ ] No.

    ### 🔑 ¿Hay variables nuevas en el .env?

    - [ ] Sí (Se agregó NOMBRE_VARIABLE al archivo .env.example).
    - [ ] No.
    ```

4. **Enviar el PR a revisión:**
    - Haz click final en el botón verde **`Create pull request`** abajo a la derecha.
    - **Solicita la revisión y aprobación** del encargado del equipo para que revise tu código y haga el "Merge" (combinación) oficial hacia `main`. **¡Ningún cambio se mezcla a `main` sin aprobación previa!**

---

## 🔄 Paso 5: Cómo Actualizar tu Proyecto (Descargar cambios nuevos)

Si otro miembro del equipo (o tú en otra computadora) hizo cambios y se actualizaron en el repositorio principal, necesitas descargar esos cambios a tu computadora para tener la versión más reciente. **No necesitas borrar lo que ya tienes**, solo haz lo siguiente:

1. Asegúrate de estar en tu rama de trabajo o en la rama principal sin cambios pendientes por guardar.
2. Ejecuta este comando en la terminal para descargar los últimos cambios:
    ```bash
    git pull
    ```
    _(Esto descargará y combinará los cambios más recientes del repositorio sin borrar tus archivos locales)._

---

## 🏗️ Guía: Arquitectura MVC y Creación de un Nuevo Módulo

Este proyecto utiliza el patrón de diseño **MVC** (Modelo-Vista-Controlador). Si eres nuevo y necesitas crear un nuevo módulo, así es como debes estructurarlo para mantener el orden:

### 1. El Modelo (M) - La Base de Datos

El modelo se encarga de hablar con la base de datos.

- **Ubicación:** `app/Models/`
- **Comando:** `php artisan make:model NombreModulo`
- **¿Qué hace?** Aquí defines a qué tabla se conecta y qué campos se pueden llenar masivamente (ej. `$fillable`).

### 2. El Controlador (C) - La Lógica

El controlador es el cerebro. Recibe las peticiones del usuario, consulta al Modelo y envía la información a la Vista.

- **Ubicación:** `app/Http/Controllers/`
- **Comando:** `php artisan make:controller NombreModuloController`
- **¿Qué hace?** Contiene funciones lógicas como `index()` para mostrar listas, `store()` para guardar, `update()` para actualizar, etc.

### 3. La Vista (V) - La Pantalla Visual

La vista es lo que el usuario final ve (HTML, CSS, JS).

- **Ubicación:** `resources/views/nombre_modulo/`
- **¿Qué hace?** Son archivos con terminación `.blade.php`. Aquí recibes los datos del Controlador y los muestras en tablas, formularios o gráficas.

### Flujo de trabajo ideal para un nuevo módulo:

1. **Crear la tabla en BD:** Usa `php artisan make:migration create_modulo_table` para generar el archivo en `database/migrations/`.
2. **Crear el Modelo:** Ejecuta `php artisan make:model Modulo` para crearlo en `app/Models/`.
3. **Crear el Controlador:** Ejecuta `php artisan make:controller ModuloController` para crearlo en `app/Http/Controllers/`.
4. **Registrar las Rutas:** Vas al archivo `routes/web.php` y conectas una URL (ruta) con tu nuevo Controlador.
5. **Crear las Vistas:** Creas una nueva carpeta en `resources/views/modulo/` y dentro añades tus archivos visuales como `index.blade.php`.

¡Siguiendo este orden mantendremos el código limpio, estructurado y fácil de entender para todos!
