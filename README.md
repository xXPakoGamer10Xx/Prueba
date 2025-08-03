# ERP Laravel para Gestión Integral de Clínicas

[![Build Status](https://github.com/[[tu-usuario]]/[[tu-repositorio]]/actions/workflows/tests.yml/badge.svg)](https://github.com/[[tu-usuario]]/[[tu-repositorio]]/actions)
[![Linting Status](https://github.com/[[tu-usuario]]/[[tu-repositorio]]/actions/workflows/lint.yml/badge.svg)](https://github.com/[[tu-usuario]]/[[tu-repositorio]]/actions)

## 1. Introducción

Este proyecto es un Sistema de Planificación de Recursos Empresariales (ERP) robusto y modular, desarrollado sobre **Laravel 11**. Su finalidad es proporcionar una solución digital centralizada para la administración de operaciones en clínicas y centros de salud. La arquitectura está diseñada para ser escalable y mantenible, separando la lógica de negocio en módulos de dominio específicos.

La aplicación utiliza un stack tecnológico moderno con **Livewire 3** para construir una interfaz de usuario dinámica y reactiva, minimizando la necesidad de código JavaScript complejo y ofreciendo una experiencia de usuario fluida, similar a la de una Single-Page Application (SPA).

<!-- 
### Capturas de Pantalla (Placeholder)
*Añade aquí capturas de pantalla de la aplicación para dar una idea visual del producto final.*

**(Dashboard Principal)**
![Dashboard](https://via.placeholder.com/800x400.png?text=Dashboard+Principal)

**(Gestión de Inventario - Odontología)**
![Inventario](https://via.placeholder.com/800x400.png?text=Módulo+de+Inventario)
-->

## 2. Características Detalladas

El sistema se organiza en los siguientes módulos funcionales:

### Módulo de Autenticación y Seguridad
- **Control de Acceso Basado en Roles (RBAC)**: Utiliza un middleware (`app/Http/Middleware/CheckRole.php`) para proteger rutas y acciones según el rol del usuario.
- **Gestión de Sesiones**: Funcionalidades completas de login, logout y registro de usuarios.

### Módulo de Ginecología (`app/Ginecologia`)
- **Gestión de Expedientes de Pacientes**: Creación, lectura, actualización y eliminación (CRUD) de registros de pacientes.
- **Administración de Doctores**: Mantenimiento de un directorio de doctores y sus especialidades.
- **Registro de Diagnósticos**: Almacenamiento de historiales de diagnósticos vinculados a pacientes.
- **Planificación de Cirugías**: Gestión de procedimientos de cirugía general y ginecológica, asignando pacientes, doctores y recursos.
- **Control de Material Quirúrgico**: Inventario y seguimiento del material utilizado en cada intervención.

### Módulo de Odontología (`app/Odontologia`)
- **Gestión de Almacén e Insumos**: Control de stock de insumos, con tablas dinámicas para visualización y gestión.
- **Inventario en Consultorio**: Administración del material disponible directamente en cada consultorio.
- **Gestión de Catálogos**:
    - **Laboratorios**: CRUD para laboratorios externos.
    - **Presentaciones**: Administración de los tipos de presentación de insumos (caja, unidad, etc.).
    - **Materiales Externos**: Registro de materiales que no forman parte del stock principal.
- **Sistema de Peticiones**: Creación y seguimiento de peticiones de materiales o trabajos a laboratorios.

### Módulo de Servicios y Equipamiento (`app/Servicios`)
- **Inventario Central de Equipos**: Registro detallado de todo el equipamiento médico, incluyendo especificaciones y garantías.
- **Gestión de Mantenimiento**: Planificación y registro de mantenimientos preventivos y correctivos.
- **Administración de Áreas y Encargados**: Asignación de responsables a diferentes áreas de la clínica para la gestión de equipos.
- **Proceso de Baja de Equipos**: Flujo de trabajo para dar de baja equipos obsoletos o dañados, registrando la fecha y motivo.

## 3. Arquitectura y Stack Tecnológico

El proyecto sigue las mejores prácticas del ecosistema Laravel, con una arquitectura MVC (Model-View-Controller) extendida por componentes reactivos de Livewire.

### Backend
- **Framework**: Laravel 11.x
- **Lenguaje**: PHP 8.2
- **Componentes Reactivos**: Livewire 3.4
- **Suite de Pruebas**: Pest y PHPUnit
- **Gestor de Dependencias**: Composer

### Frontend
- **Framework CSS**: Tailwind CSS (a través del preset de Laravel)
- **JavaScript**: Alpine.js (integrado con Livewire)
- **Bundler**: Vite
- **Gestor de Paquetes**: npm

### Base de Datos
- **Sistema Gestor**: Compatible con MySQL, PostgreSQL, SQLite.
- **ORM**: Eloquent ORM
- **Control de Esquema**: Migraciones de Laravel (`database/migrations`)

## 4. Instalación y Configuración Local

Sigue estos pasos para levantar un entorno de desarrollo.

### Prerrequisitos
- PHP >= 8.2
- Composer 2.x
- Node.js >= 18.x y npm
- Servidor de base de datos (ej. MySQL 8.0)
- Git

### Pasos de Instalación
1.  **Clonar el repositorio (reemplaza los placeholders):**
    ```bash
    git clone https://github.com/[[tu-usuario]]/[[tu-repositorio]].git
    cd erp-laravel
    ```

2.  **Instalar dependencias de PHP:**
    ```bash
    composer install --no-interaction --prefer-dist --optimize-autoloader
    ```

3.  **Instalar dependencias de Node.js:**
    ```bash
    npm install
    ```

4.  **Configurar el archivo de entorno:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Configurar la conexión a la base de datos en `.env`:**
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=erp_laravel
    DB_USERNAME=root
    DB_PASSWORD=
    ```

6.  **Ejecutar migraciones y seeders (opcional):**
    Esto creará la estructura de la base de datos.
    ```bash
    php artisan migrate --seed
    ```

7.  **Compilar assets para desarrollo:**
    ```bash
    npm run dev
    ```
    Para producción, usar `npm run build`.

8.  **Iniciar el servidor local:**
    ```bash
    php artisan serve
    ```
    La aplicación estará disponible en `http://127.0.0.1:8000`.

## 5. Calidad de Código y Pruebas

### Ejecutar Pruebas
El proyecto utiliza Pest como framework principal de pruebas. Para ejecutar toda la suite:
```bash
php artisan test
```

### Análisis Estático y Linting
Para asegurar la consistencia del código, puedes usar Laravel Pint (configurado por defecto):
```bash
./vendor/bin/pint
```

## 6. Integración Continua (CI)

El repositorio está configurado con **GitHub Actions** para automatizar la verificación de calidad:
- **`tests.yml`**: Se dispara en cada `push` y `pull request` a las ramas `main` y `develop`. Instala dependencias, configura el entorno y ejecuta la suite de `php artisan test`.
- **`lint.yml`**: Se dispara junto con el workflow de tests y ejecuta el linter para asegurar que el código cumple con los estándares de estilo del proyecto.

## 7. Contribuciones

Las contribuciones son bienvenidas. Por favor, sigue este flujo de trabajo:
1.  Haz un **Fork** de este repositorio.
2.  Crea una nueva rama para tu funcionalidad (`git checkout -b feature/nueva-funcionalidad`).
3.  Realiza tus cambios y haz **commit** (`git commit -m 'Añade nueva funcionalidad'`).
4.  Asegúrate de que todas las pruebas pasen (`php artisan test`).
5.  Haz **push** a tu rama (`git push origin feature/nueva-funcionalidad`).
6.  Abre un **Pull Request** hacia la rama `develop` de este repositorio.

## 8. Licencia

Este proyecto se distribuye bajo la licencia especificada en el archivo [LICENSE](LICENSE).