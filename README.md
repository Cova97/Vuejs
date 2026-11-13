# Vuejs lab

Proyecto de aprendizaje: un pequeño **inventario de piezas** (CRUD) con un backend en **Laravel** y un frontend en **Vue 3**.

> 👋 Soy nuevo en este stack y este repositorio es mi espacio para aprender y practicar estas tecnologías paso a paso. El código puede tener errores o cosas por mejorar, y la idea es justamente ir aprendiendo de ellas. Cualquier sugerencia es bienvenida.

## ¿Qué hace?

Permite registrar, listar, editar y borrar piezas de inventario. Cada pieza tiene:

- **SKU** (único)
- **Nombre**
- **Descripción** (opcional)
- **Cantidad**
- **Precio**
- **Activo** (sí / no)

## Tecnologías que estoy aprendiendo

| Parte | Tecnología |
| --- | --- |
| Backend (API) | [Laravel 13](https://laravel.com) (PHP 8.3+), Sanctum, Pest |
| Frontend | [Vue 3](https://vuejs.org) + [Vite](https://vite.dev) + TypeScript + Vue Router |
| Base de datos | MySQL 8.4 en [Docker](https://www.docker.com) |

## Estructura

```
Vuejs/
├── backend/            # API REST en Laravel
│   ├── app/Http/Controllers/PartController.php
│   ├── app/Http/Requests/    # Validaciones (StorePartRequest, UpdatePartRequest)
│   ├── app/Models/Part.php
│   ├── database/migrations/
│   └── routes/api.php
├── frontend/           # SPA en Vue 3
│   └── src/
│       ├── components/ # PartForm.vue, PartList.vue
│       ├── views/      # PartsView.vue, PartEditView.vue
│       └── router/
└── docker-compose.yml  # Contenedor de MySQL
```

## Cómo correrlo en local

### Requisitos

- PHP 8.3+ y [Composer](https://getcomposer.org)
- Node.js 22.18+ (o 24.12+) y npm
- Docker Desktop

### 1. Base de datos

Desde la raíz del proyecto:

```bash
docker compose up -d
```

Levanta MySQL en el puerto **3307** con la base `minilab`.

### 2. Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env      # en Windows: copy .env.example .env
php artisan key:generate
```

En `backend/.env` configura la conexión a MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=minilab
DB_USERNAME=cova
DB_PASSWORD=secret
```

Luego:

```bash
php artisan migrate
php artisan serve         # http://localhost:8000
```

### 3. Frontend (Vue)

En otra terminal:

```bash
cd frontend
npm install
npm run dev               # http://localhost:5173
```

Vite redirige las llamadas a `/api` hacia `http://localhost:8000`, así que no hace falta configurar nada más.

## Endpoints de la API

| Método | Ruta | Descripción |
| --- | --- | --- |
| GET | `/api/ping` | Prueba de que la API responde |
| GET | `/api/parts` | Listar piezas |
| POST | `/api/parts` | Crear pieza |
| GET | `/api/parts/{id}` | Ver una pieza |
| PUT/PATCH | `/api/parts/{id}` | Actualizar pieza |
| DELETE | `/api/parts/{id}` | Borrar pieza |

## Próximos pasos de aprendizaje

- [x] CRUD básico de piezas (API + frontend)
- [x] Control de versiones con Git y GitHub
- [ ] Limpiar y reutilizar el formulario en un solo componente
- [ ] Factories, seeders y tests con Pest
- [ ] Login con Laravel Sanctum
- [ ] Paginación y búsqueda
