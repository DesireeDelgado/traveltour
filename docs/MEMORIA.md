# Memoria Técnica del Proyecto — TravelTour

---

## Índice

1. [Descripción del Proyecto](#1-descripción-del-proyecto)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Especificaciones Técnicas](#3-especificaciones-técnicas)
4. [Manual de Despliegue](#4-manual-de-despliegue)

---

## 1. Descripción del Proyecto

### 1.1 Introducción

**TravelTour** es una plataforma web social orientada a la comunidad viajera, donde los usuarios pueden compartir sus experiencias de viaje en formato de publicaciones detalladas, incluyendo texto, imágenes, información de alojamiento y gastronomía. La aplicación permite la interacción entre usuarios mediante comentarios, sistema de favoritos y notificaciones en tiempo real.

### 1.2 Objetivos

| Objetivo | Descripción |
|----------|-------------|
| **Compartir experiencias** | Permitir a los usuarios crear publicaciones de viajes con contenido enriquecido (texto, imágenes, datos de alojamiento y gastronomía). |
| **Interacción social** | Fomentar la comunidad mediante comentarios en publicaciones y un sistema de favoritos. |
| **Descubrimiento** | Ofrecer un buscador con autocompletado y filtros (presupuesto, duración, destino) para explorar viajes. |
| **Moderación** | Proporcionar un panel de administración completo para gestionar usuarios, viajes y comentarios. |
| **Seguridad** | Implementar autenticación robusta con control de cuentas baneadas y borrado suave (soft delete). |

### 1.3 Alcance

El proyecto cubre las siguientes funcionalidades:

- **Registro y autenticación** de usuarios con roles diferenciados (`ROLE_USER`, `ROLE_ADMIN`).
- **CRUD completo de viajes** con subida de múltiples imágenes.
- **Sistema de comentarios** con protección anti-duplicados mediante token de un solo uso.
- **Sistema de favoritos** con toggle vía AJAX.
- **Notificaciones automáticas** al recibir comentarios, favoritos o acciones administrativas.
- **Buscador con autocompletado** de destinos y perfiles de usuario.
- **Panel de administración** (EasyAdmin) para gestión integral.
- **Soft delete** de cuentas con período de gracia de 30 días y reactivación automática al iniciar sesión.
- **Sistema de baneo** de usuarios con verificación pre y post autenticación.

### 1.4 Público Objetivo

- **Viajeros** que desean documentar y compartir sus experiencias.
- **Usuarios que buscan inspiración** para planificar sus próximos viajes, pudiendo filtrar por presupuesto, duración y destino.
- **Comunidades de viaje** que necesitan una plataforma sencilla e interactiva.

---

## 2. Arquitectura del Sistema

### 2.1 Diagrama de Arquitectura de Alto Nivel

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENTE (Navegador)                      │
│  ┌──────────────┐  ┌──────────────┐  ┌────────────────────┐    │
│  │  Twig Views  │  │  JavaScript  │  │  CSS / TailwindCSS │    │
│  │  (SSR HTML)  │  │  (AJAX/Fetch)│  │                    │    │
│  └──────┬───────┘  └──────┬───────┘  └────────────────────┘    │
└─────────┼─────────────────┼────────────────────────────────────┘
          │ HTTP            │ AJAX (JSON)
          ▼                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                    SERVIDOR WEB (Symfony 7.2)                    │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │                      CAPA DE SEGURIDAD                     │  │
│  │  AppCustomAuthenticator · UserChecker · Firewall           │  │
│  └────────────────────────┬───────────────────────────────────┘  │
│                           │                                      │
│  ┌────────────────────────▼───────────────────────────────────┐  │
│  │                      CONTROLADORES                         │  │
│  │                                                            │  │
│  │  ┌─────────────┐ ┌─────────────┐ ┌──────────────────┐     │  │
│  │  │ HomeCtrl    │ │ ViajeCtrl   │ │ UsuarioCtrl      │     │  │
│  │  └─────────────┘ └─────────────┘ └──────────────────┘     │  │
│  │  ┌─────────────┐ ┌─────────────┐ ┌──────────────────┐     │  │
│  │  │ FavoritosC  │ │ ComentarioC │ │ NotificacionCtrl │     │  │
│  │  └─────────────┘ └─────────────┘ └──────────────────┘     │  │
│  │  ┌─────────────┐ ┌─────────────┐ ┌──────────────────┐     │  │
│  │  │ SearchCtrl  │ │ ImagenCtrl  │ │ RegistrationCtrl │     │  │
│  │  └─────────────┘ └─────────────┘ └──────────────────┘     │  │
│  │  ┌─────────────┐                                          │  │
│  │  │ SecurityC   │                                          │  │
│  │  └─────────────┘                                          │  │
│  └────────────────────────┬───────────────────────────────────┘  │
│                           │                                      │
│  ┌────────────────────────▼───────────────────────────────────┐  │
│  │              PANEL DE ADMINISTRACIÓN (EasyAdmin 5)          │  │
│  │  DashboardCtrl · UsuarioCrud · ViajeCrud · ComentarioCrud  │  │
│  └────────────────────────┬───────────────────────────────────┘  │
│                           │                                      │
│  ┌────────────────────────▼───────────────────────────────────┐  │
│  │                    EVENT LISTENERS                          │  │
│  │             DatabaseChangeListener (preRemove)              │  │
│  │      → Genera notificaciones en borrado administrativo      │  │
│  └────────────────────────┬───────────────────────────────────┘  │
│                           │                                      │
│  ┌────────────────────────▼───────────────────────────────────┐  │
│  │                    CAPA DE DOMINIO                          │  │
│  │                                                            │  │
│  │  Entidades Doctrine ORM:                                   │  │
│  │  Usuario · Viaje · Comentario · Favoritos · Imagen ·      │  │
│  │  Notificacion                                              │  │
│  │                                                            │  │
│  │  Repositorios:                                             │  │
│  │  UsuarioRepository · ViajeRepository · FavoritosRepository │  │
│  │  ComentarioRepository · NotificacionRepository             │  │
│  └────────────────────────┬───────────────────────────────────┘  │
│                           │                                      │
│  ┌────────────────────────▼───────────────────────────────────┐  │
│  │                 ALMACENAMIENTO DE ARCHIVOS                  │  │
│  │  storage/viajes/   → Imágenes de viajes                    │  │
│  │  storage/profiles/ → Fotos de perfil                       │  │
│  │  (Servidos vía ImagenController)                           │  │
│  └────────────────────────────────────────────────────────────┘  │
└──────────────────────────┬──────────────────────────────────────┘
                           │ Doctrine DBAL
                           ▼
              ┌─────────────────────────┐
              │   PostgreSQL 16         │
              │   (Docker Container)    │
              │   Puerto: 5432          │
              └─────────────────────────┘
```

### 2.2 Modelo Entidad-Relación

```
┌──────────────┐       1:N       ┌──────────────┐       1:N       ┌──────────────┐
│   USUARIO    │────────────────▶│    VIAJE      │────────────────▶│   IMAGEN     │
│──────────────│                 │──────────────│                 │──────────────│
│ id (PK)      │                 │ id (PK)      │                 │ id (PK)      │
│ email        │                 │ id_usuario(FK)│                 │ url_path     │
│ password     │                 │ titulo       │                 │ id_viaje(FK) │
│ nickname     │                 │ destino      │                 └──────────────┘
│ roles[]      │                 │ duracion     │
│ biografia    │                 │ presupuesto  │       1:N       ┌──────────────┐
│ fecha_registro│                │ contenido    │────────────────▶│ COMENTARIO   │
│ url_foto_perfil│               │ alojamiento  │                 │──────────────│
│ deletedAt    │                 │ gastronomia  │                 │ id (PK)      │
│ baneado      │                 │ fecha_creacion│                │ id_usuario(FK)│
└──────┬───────┘                 └──────┬───────┘                 │ id_viaje(FK) │
       │                                │                         │ comentario   │
       │         1:N                    │                         │ fecha_creacion│
       │    ┌──────────────┐            │                         └──────────────┘
       ├───▶│  FAVORITOS   │◀───────────┤
       │    │──────────────│            │        1:N       ┌────────────────┐
       │    │ id (PK)      │            ├────────────────▶│ NOTIFICACION   │
       │    │ id_usuario(FK)│            │                 │────────────────│
       │    │ id_viaje(FK) │            │                 │ id (PK)        │
       │    └──────────────┘            │                 │ mensaje        │
       │                                │                 │ leido          │
       │         1:N                    │                 │ createdAt      │
       └────────────────────────────────┼────────────────▶│ usuario(FK)    │
                                        └────────────────▶│ viaje(FK) null │
                                                          └────────────────┘
```

### 2.3 Patrón Arquitectónico

El proyecto sigue el patrón **MVC (Modelo-Vista-Controlador)** proporcionado de forma nativa por Symfony:

- **Modelo**: Entidades Doctrine ORM (`src/Entity/`) y Repositorios (`src/Repository/`).
- **Vista**: Plantillas Twig (`templates/`) con renderizado del lado del servidor (SSR).
- **Controlador**: Controladores Symfony (`src/Controller/`) que gestionan la lógica de negocio y las peticiones HTTP.

---

## 3. Especificaciones Técnicas

### 3.1 Stack Tecnológico

| Capa | Tecnología | Versión |
|------|-----------|---------|
| **Lenguaje Backend** | PHP | ≥ 8.2 |
| **Framework Backend** | Symfony | 7.2.* |
| **ORM** | Doctrine ORM | ^3.6 |
| **Motor de plantillas** | Twig | ^2.12 / ^3.0 |
| **Base de datos** | PostgreSQL | 16 (Alpine) |
| **Panel de administración** | EasyAdmin | ^5.0 |
| **Bundler Frontend** | Vite (pentatrion/vite-bundle) | ^8.2 |
| **CSS** | TailwindCSS | — |
| **Contenedores** | Docker / Docker Compose | — |
| **Testing** | PHPUnit | ^12.5 |

### 3.2 Justificación de las Tecnologías

| Tecnología | Justificación |
|-----------|---------------|
| **Symfony 7.2** | Framework PHP maduro, modular y con amplia comunidad. Proporciona seguridad robusta (firewall, CSRF, autenticación), inyección de dependencias y un ecosistema de bundles extenso. |
| **Doctrine ORM 3** | Permite trabajar con la base de datos de forma orientada a objetos, facilita las migraciones de esquema y proporciona un potente sistema de repositorios y query builder. |
| **PostgreSQL 16** | Base de datos relacional avanzada, con excelente rendimiento, soporte nativo de JSON y una robusta gestión de integridad referencial. |
| **EasyAdmin 5** | Genera un panel de administración completo con configuración mínima, permitiendo operaciones CRUD sobre todas las entidades sin desarrollo adicional significativo. |
| **Twig** | Motor de plantillas seguro por defecto (auto-escaping), con herencia de plantillas y macros que facilitan la reutilización de componentes de vista. |
| **Vite + TailwindCSS** | Vite ofrece Hot Module Replacement (HMR) para desarrollo ágil. TailwindCSS permite un diseño rápido y consistente mediante clases de utilidad. |
| **Docker** | Garantiza la reproducibilidad del entorno de desarrollo y facilita el despliegue, encapsulando PostgreSQL y los servicios auxiliares en contenedores aislados. |

### 3.3 Dependencias Clave de Producción

| Paquete | Función |
|---------|---------|
| `symfony/security-bundle` | Autenticación, autorización, firewalls y gestión de roles. |
| `symfony/form` | Generación y validación de formularios del lado del servidor. |
| `symfony/validator` | Validación de entidades con atributos PHP 8 (UniqueEntity, etc.). |
| `symfony/mailer` | Infraestructura de envío de correos electrónicos. |
| `symfony/notifier` | Sistema de notificaciones multicanal. |
| `symfony/stimulus-bundle` | Integración de Stimulus para JavaScript ligero en las vistas. |
| `symfony/ux-turbo` | Navegación tipo SPA sin escribir JavaScript personalizado. |
| `symfony/ux-icons` | Sistema de iconos integrado en las plantillas Twig. |
| `symfony/asset-mapper` | Gestión de assets sin necesidad de Node.js en producción. |
| `symfony/monolog-bundle` | Logging centralizado de la aplicación. |
| `twig/extra-bundle` | Filtros y funciones adicionales para Twig. |

### 3.4 Dependencias de Desarrollo

| Paquete | Función |
|---------|---------|
| `doctrine/doctrine-fixtures-bundle` | Carga de datos de prueba (fixtures) en la base de datos. |
| `phpunit/phpunit` | Framework de testing unitario y funcional. |
| `symfony/maker-bundle` | Generación de código (entidades, controladores, formularios). |
| `symfony/web-profiler-bundle` | Barra de depuración y profiler web para desarrollo. |
| `symfony/debug-bundle` | Herramientas de depuración avanzada. |

### 3.5 Componentes de Seguridad

El sistema implementa múltiples capas de seguridad:

1. **`AppCustomAuthenticator`**: Autenticador personalizado que extiende `AbstractLoginFormAuthenticator`. Gestiona el login con email/contraseña, verifica el estado de soft delete (con ventana de 30 días para reactivación) y redirige según el rol del usuario (admin → panel admin, usuario → dashboard).

2. **`UserChecker`**: Implementa `UserCheckerInterface` para verificar en pre y post autenticación si un usuario está baneado, lanzando una excepción personalizada que impide el acceso.

3. **Protección CSRF**: Todos los formularios de modificación de datos (borrado, comentarios) incluyen validación de tokens CSRF.

4. **Token de un solo uso**: El sistema de comentarios utiliza un token de sesión de un solo uso (`comment_sid`) para prevenir envíos duplicados.

5. **Control de acceso por propiedad**: Los controladores verifican que el usuario autenticado sea el propietario del recurso antes de permitir ediciones o borrados.

### 3.6 Sistema de Notificaciones

La aplicación implementa notificaciones automáticas para:

- **Comentarios**: Cuando un usuario comenta en un viaje ajeno, el autor recibe una notificación.
- **Favoritos**: Cuando un usuario marca como favorito un viaje ajeno, el autor es notificado (y la notificación se elimina si se desmarca).
- **Acciones administrativas**: El `DatabaseChangeListener` escucha eventos `preRemove` de Doctrine para generar notificaciones cuando un administrador elimina viajes o comentarios por infracción de normas.

### 3.7 Estructura del Proyecto

```
traveltour/
├── assets/                  # Assets del frontend (JS, CSS)
├── bin/                     # Consola de Symfony
├── config/                  # Configuración de la aplicación
│   ├── packages/            # Configuración de bundles
│   ├── routes/              # Definición de rutas
│   ├── bundles.php          # Registro de bundles
│   └── services.yaml       # Servicios e inyección de dependencias
├── docs/                    # Documentación del proyecto
├── migrations/              # Migraciones de Doctrine
├── public/                  # Directorio público (punto de entrada web)
│   └── img/                 # Imágenes estáticas (fixtures)
├── src/                     # Código fuente PHP
│   ├── Command/             # Comandos de consola personalizados
│   ├── Controller/          # Controladores de la aplicación
│   │   └── Admin/           # Controladores del panel EasyAdmin
│   ├── DataFixtures/        # Datos de prueba
│   ├── Entity/              # Entidades Doctrine ORM
│   ├── EventListener/       # Listeners de eventos Doctrine
│   ├── Form/                # Tipos de formulario
│   ├── Repository/          # Repositorios de datos
│   └── Security/            # Autenticadores y checkers
├── storage/                 # Almacenamiento de archivos subidos
│   ├── viajes/              # Imágenes de viajes
│   └── profiles/            # Fotos de perfil
├── templates/               # Plantillas Twig
│   ├── viaje/               # Vistas de viajes
│   ├── usuario/             # Vistas de perfil de usuario
│   ├── favoritos/           # Vistas de favoritos
│   ├── registration/        # Vistas de registro
│   ├── security/            # Vistas de login
│   ├── base.html.twig       # Layout base
│   ├── index.html.twig      # Página pública de inicio
│   └── home_logueado.html.twig  # Dashboard del usuario
├── tests/                   # Tests automatizados
├── translations/            # Archivos de traducción
├── compose.yaml             # Docker Compose principal
├── compose.override.yaml    # Docker Compose override (dev)
├── composer.json            # Dependencias PHP
├── package.json             # Dependencias Node.js
├── vite.config.js           # Configuración de Vite
├── tailwind.config.js       # Configuración de TailwindCSS
└── postcss.config.js        # Configuración de PostCSS
```

---

## 4. Manual de Despliegue

### 4.1 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

| Software | Versión mínima | Verificar con |
|----------|---------------|---------------|
| **Docker** | 20.x | `docker --version` |
| **Docker Compose** | 2.x | `docker compose version` |
| **PHP** | 8.2 | `php -v` |
| **Composer** | 2.x | `composer --version` |
| **Node.js** | 18.x | `node -v` |
| **npm** | 9.x | `npm -v` |


### 4.2 Paso 1 — Configurar las Variables de Entorno

Crear el archivo `.env.local` a partir del ejemplo:

```bash
cp .env.example .env.local
```

Editar `.env.local` con los valores apropiados. La variable más importante es la conexión a la base de datos:

```dotenv
# Configuración de la base de datos
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"

# En caso de usar el contenedor Docker por defecto:
# DATABASE_URL="postgresql://app:!ChangeMe!@database:5432/app?serverVersion=16&charset=utf8"
```

> **Nota**: Si se utiliza Docker Compose, las credenciales por defecto son `app` / `!ChangeMe!` como se define en `compose.yaml`.

### 4.3 Paso 2 — Levantar los Contenedores Docker

Iniciar el contenedor de PostgreSQL y el servicio de correo (Mailpit):

```bash
docker compose up -d
```

Esto levantará:

| Servicio | Imagen | Puerto |
|----------|--------|--------|
| **database** | `postgres:16-alpine` | `5432` |
| **mailer** | `axllent/mailpit` | `1025` (SMTP) / `8025` (Web UI) |

Verificar que los contenedores están corriendo:

```bash
docker compose ps
```

Esperar a que PostgreSQL esté saludable (el healthcheck verifica con `pg_isready`):

```bash
docker compose logs database
```

### 4.4 Paso 3 — Instalar Dependencias

**Dependencias PHP (backend):**

```bash
composer install
```

**Dependencias Node.js (frontend):**

```bash
npm install
```

### 4.5 Paso 4 — Ejecutar Migraciones de Base de Datos

Crear el esquema de la base de datos ejecutando todas las migraciones:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

Las migraciones crean las siguientes tablas:

| Tabla | Descripción |
|-------|-------------|
| `usuario` | Usuarios de la plataforma |
| `viaje` | Publicaciones de viajes |
| `comentario` | Comentarios en viajes |
| `favoritos` | Relación usuario-viaje favorito |
| `imagen` | Imágenes asociadas a viajes |
| `notificacion` | Notificaciones del sistema |
| `messenger_messages` | Cola de mensajes de Symfony Messenger |

El proyecto incluye **8 migraciones** que van desde la creación inicial del esquema hasta las últimas adiciones (notificaciones, campo baneado, etc.).

### 4.6 Paso 5 — Cargar Datos de Prueba (Fixtures)

Cargar los datos de prueba predefinidos:

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

> **Atención**: Este comando **borra todos los datos existentes** de la base de datos antes de cargar las fixtures.

Las fixtures (`src/DataFixtures/AppFixtures.php`) crean:

| Datos | Detalle |
|-------|---------|
| **4 Usuarios** | `lolamento`, `fermin_trujillo`, `ines_table` (ROLE_USER) y `admin` (ROLE_ADMIN) |
| **7 Viajes** | Madrid, Brujas, Barcelona, Ciudad de México, Toscana, Berlín y más |
| **Imágenes** | Copiadas desde `public/img/viajes/` a `storage/viajes/` |
| **Favoritos** | Relaciones predefinidas entre usuarios y viajes |
| **Comentarios** | Comentarios de ejemplo en varios viajes |

**Credenciales de acceso por defecto:**

| Usuario | Email | Contraseña | Rol |
|---------|-------|-----------|-----|
| lolamento | lolamento@traveltour.com | `123456` | ROLE_USER |
| fermin_trujillo | fermintrujillo@traveltour.com | `123456` | ROLE_USER |
| ines_table | inestable95@traveltour.com | `123456` | ROLE_USER |
| admin | admin@traveltour.com | `123456` | ROLE_ADMIN |

### 4.7 Paso 6 — Compilar Assets del Frontend

**Para desarrollo** (con Hot Module Replacement):

```bash
npm run dev
```

**Para producción:**

```bash
npm run build
```

### 4.8 Paso 7 — Iniciar el Servidor de Desarrollo

Usando el servidor web integrado de Symfony:

```bash
symfony server:start
```

O alternativamente con PHP:

```bash
php -S 127.0.0.1:8000 -t public
```

La aplicación estará disponible en: **http://localhost:8000**

El panel de administración está en: **http://localhost:8000/admin**

### 4.9 Resumen de Comandos

```bash
# 1. Configurar entorno
cp .env.example .env.local

# 2. Levantar base de datos con Docker
docker compose up -d

# 3. Instalar dependencias
composer install
npm install

# 4. Crear esquema de base de datos
php bin/console doctrine:migrations:migrate --no-interaction

# 5. Cargar datos de prueba
php bin/console doctrine:fixtures:load --no-interaction

# 6. Compilar assets
npm run dev

# 7. Arrancar servidor
symfony server:start
```

---

