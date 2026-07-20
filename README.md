# 🚀 Guía de Inicio - EuroMédica Clínica

¡Bienvenido al proyecto! Esta guía te ayudará a configurar el sistema en tu computadora y te enseñará cómo trabajar con él paso a paso, incluso si eres nuevo en Laravel.

---

## 💻 Paso 1: Instalar el Proyecto en tu Computadora

Sigue estos sencillos pasos en orden usando tu terminal (consola):

1. **Descargar el código (Clonar):**
   ```bash
   git clone <url-del-repositorio>
   cd clinica
   ```

2. **Instalar las librerías de PHP:**
   *(Esto descarga todo lo necesario para que Laravel funcione).*
   ```bash
   composer install
   ```

3. **Instalar las librerías de Diseño y Pantallas:**
   *(Esto descarga las herramientas visuales del proyecto).*
   ```bash
   npm install
   ```

4. **Crear tu archivo de configuración:**
   Copia el archivo de plantilla con este comando:
   ```bash
   cp .env.example .env
   ```

5. **Generar la llave de seguridad:**
   *(Laravel la necesita para encriptar sesiones y contraseñas).*
   ```bash
   php artisan key:generate
   ```

6. **Encender las pantallas (Assets):**
   *(Deja esta terminal abierta para ver los cambios visuales en vivo).*
   ```bash
   npm run dev
   ```

---

## 🔑 Paso 2: Conectar la Base de Datos

El archivo `.env` es el "cerebro" donde guardamos las contraseñas. **¡Por seguridad, este archivo nunca se sube a GitHub!**

Para conectar tu base de datos:
1. Abre el archivo `.env` con cualquier editor de código.
2. Busca la sección que dice `DB_` y pon tus datos reales:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1          # Cambia por la dirección IP de tu servidor (ej: Hostinger)
DB_PORT=3306
DB_DATABASE=mi_base_datos  # El nombre de tu base de datos
DB_USERNAME=mi_usuario     # El usuario de tu base de datos
DB_PASSWORD=mi_contraseña  # La contraseña de tu base de datos
```

*Nota: Si solo estás probando en local y no tienes MySQL instalado, puedes escribir `DB_CONNECTION=sqlite` y borrar el resto de las líneas de base de datos.*

---

## 📂 Paso 3: Cómo Crear Nuevas Tablas (Migraciones)

En Laravel **no** creamos las tablas escribiendo código SQL en phpMyAdmin. En su lugar, usamos "Migraciones", que son como planos de construcción escritos en PHP.

### Ejemplo paso a paso para crear una tabla de "Pacientes":

1. **Crear el plano (Migración):**
   Escribe en tu consola:
   ```bash
   php artisan make:migration create_pacientes_table
   ```
   *Esto creará un archivo nuevo dentro de la carpeta `database/migrations/`.*

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

## 📥 Paso 4: Cómo Subir tus Cambios a GitHub de Forma Segura

Antes de subir tus cambios para que otros los vean, sigue esta lista de revisión (Checklist):

1. **No trabajes en la rama `main`:**
   Crea siempre una rama nueva para tu trabajo:
   ```bash
   git checkout -b mi-nueva-funcion
   ```

2. **Revisa qué archivos vas a subir (`git status`):**
   Comprueba que **no** estés subiendo archivos que contengan contraseñas o datos reales (como `.env`, archivos `.log` de errores o contraseñas temporales como `temp_users.txt`).

3. **¿Agregaste nuevas configuraciones?**
   Si creaste una variable nueva en tu `.env` (por ejemplo, una API Key de correos), agrégala al archivo `.env.example` con el valor vacío (ej: `API_CORREO=`) para que otros sepan que deben configurarla.

4. **Subir los cambios y crear un Pull Request (PR):**
   Sube tu rama a GitHub y abre un Pull Request hacia la rama `main`. **Ningún cambio se sube directamente a `main` ni se mezcla sin aprobación.** Debes solicitar la revisión y aprobación correspondiente para que tus cambios sean incorporados. En el PR, detalla:
   - ¿Qué hace esta nueva funcionalidad o cambio?
   - ¿Agregaste o modificaste tablas? (Indicar si se debe correr `php artisan migrate`).
   - ¿Hay variables nuevas en el `.env` que se deban configurar en el servidor?
