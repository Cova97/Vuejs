# Vuejs lab

Proyecto de aprendizaje: un pequeño **inventario de piezas por empresa** con un backend en **Laravel** y un frontend en **Vue 3**.

> 👋 Soy nuevo en este stack y este repositorio es mi espacio para aprender y practicar estas tecnologías paso a paso. El código puede tener errores o cosas por mejorar, y la idea es justamente ir aprendiendo de ellas. Cualquier sugerencia es bienvenida.

## ¿Qué hace?

- **Piezas**: registrar, listar, editar y borrar piezas. Cada pieza tiene SKU (único), nombre, descripción (opcional), precio y si está activa.
- **Empresas**: registrar, listar, editar y borrar empresas. Cada empresa tiene nombre, dirección y teléfono (opcionales) y RFC (único, de 12 o 13 caracteres).
- **Stock por empresa**: cada empresa tiene su propio stock de cada pieza, además de un **stock mínimo**. Cuando el stock es menor que el mínimo, la pieza se marca como **bajo stock**.
- **Cantidad total de una pieza**: ya no se escribe a mano. Se calcula sumando el stock que tiene esa pieza en todas las empresas.

## Tecnologías que estoy aprendiendo

| Parte | Tecnología |
| --- | --- |
| Backend (API) | [Laravel 13](https://laravel.com) (PHP 8.3+), Sanctum, Pest |
| Frontend | [Vue 3](https://vuejs.org) + [Vite](https://vite.dev) + TypeScript + Vue Router (CSS propio, sin librerías de UI) |
| Base de datos | MySQL 8.4 en [Docker](https://www.docker.com) |

## Cómo está organizado el backend

Cada petición pasa por varias capas, y cada una tiene un solo trabajo:

```
Ruta (routes/api.php)
  → Form Request  (valida los datos; si fallan responde 422)
  → Controller    (recibe la petición y responde)
  → Service       (la lógica: guardar, buscar, calcular)
  → Model         (la tabla en la base de datos)
  → Resource      (da forma al JSON de la respuesta)
```

### Base de datos

| Tabla | Para qué sirve |
| --- | --- |
| `parts` | Las piezas (sku, name, description, price, active) |
| `companies` | Las empresas (name, address, phone, rfc) |
| `company_part` | Tabla intermedia: qué pieza tiene cada empresa, con `stock` y `min_stock`. Una pieza no puede repetirse en la misma empresa (unique `company_id + part_id`) |

Una empresa tiene muchas piezas y una pieza está en muchas empresas. Por eso es una relación **muchos a muchos**, y se define con `belongsToMany(...)->withPivot('stock', 'min_stock')` en los dos modelos.

> **Cambio de diseño:** antes `parts` tenía una columna `quantity`. Se quitó con una migración que primero movió esas cantidades a una empresa llamada **"Bodega principal"** para no perder datos. Ahora `quantity` se calcula con `withSum` (la suma del stock en todas las empresas).

## Estructura

```
Vuejs/
├── backend/                        # API REST en Laravel
│   ├── app/Http/Controllers/       # PartController, CompanyController
│   ├── app/Http/Requests/          # Validaciones (Store/UpdatePart, Store/UpdateCompany, UpdateStock)
│   ├── app/Http/Resources/         # Forma del JSON (PartResource, CompanyResource)
│   ├── app/Services/               # Lógica (PartService, CompanyService)
│   ├── app/Models/                 # Part, Company
│   ├── database/migrations/
│   ├── database/factories/         # Datos falsos para pruebas (CompanyFactory...)
│   ├── database/seeders/           # DatabaseSeeder, CompanySeeder
│   └── routes/api.php
├── frontend/                       # SPA en Vue 3
│   └── src/
│       ├── api/                    # http.ts (fetch compartido), parts.ts, companies.ts
│       ├── assets/main.css         # Estilos propios (botones, tarjetas, tablas, badges)
│       ├── components/             # PartForm, PartList, CompanyForm, CompanyList, CompanyStock
│       ├── types/                  # part.ts, company.ts
│       ├── views/                  # Piezas y Empresas (lista, detalle, edición)
│       └── router/
└── docker-compose.yml              # Contenedor de MySQL
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

En `backend/.env` configura la conexión a MySQL y el idioma de los datos falsos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=minilab
DB_USERNAME=cova
DB_PASSWORD=secret

APP_FAKER_LOCALE=es_ES
```

Luego:

```bash
php artisan migrate
php artisan db:seed       # crea 20 piezas y 5 empresas con stock de ejemplo
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

### Piezas

| Método | Ruta | Descripción |
| --- | --- | --- |
| GET | `/api/ping` | Prueba de que la API responde |
| GET | `/api/parts` | Listar piezas (incluye `quantity` = suma del stock) |
| POST | `/api/parts` | Crear pieza |
| GET | `/api/parts/{id}` | Ver una pieza |
| PUT/PATCH | `/api/parts/{id}` | Actualizar pieza |
| DELETE | `/api/parts/{id}` | Borrar pieza |

### Empresas

| Método | Ruta | Descripción |
| --- | --- | --- |
| GET | `/api/companies` | Listar empresas |
| POST | `/api/companies` | Crear empresa (201) |
| GET | `/api/companies/{id}` | Ver una empresa con sus piezas y stock (404 si no existe) |
| PUT/PATCH | `/api/companies/{id}` | Actualizar empresa |
| DELETE | `/api/companies/{id}` | Borrar empresa (204) |

### Stock de una pieza en una empresa

| Método | Ruta | Descripción |
| --- | --- | --- |
| PUT | `/api/companies/{company}/parts/{part}` | Poner o cambiar el stock. Body: `{"stock": 40, "min_stock": 8}` (`min_stock` es opcional). Si la pieza no estaba en la empresa, la agrega |
| DELETE | `/api/companies/{company}/parts/{part}` | Quitar la pieza de la empresa (204). La pieza no se borra |

> En PowerShell manda siempre el header `Accept: application/json`. Si no lo mandas, cuando la validación falla Laravel responde con una redirección en vez del error 422 en JSON.

## Pruebas que he hecho

### En `php artisan tinker`

- Agregar piezas a una empresa con `attach` y cambiar su stock con `updateExistingPivot`. ✅
- Intentar agregar la misma pieza dos veces a la misma empresa: la base de datos lo **rechazó** gracias al `unique`. ✅
- Consultar la relación en los dos sentidos (las piezas de una empresa y las empresas de una pieza). ✅
- Sumar el stock con `withSum`: el balero dio 27, y después de agregarlo a más empresas dio **147 = 40 + 24 + 83**. ✅

### Seeders

- `php artisan db:seed` corrió sin errores y creó piezas, empresas y stock. ✅

### API con PowerShell (`Invoke-RestMethod`)

| Prueba | Resultado esperado | ✔ |
| --- | --- | --- |
| `GET /api/companies` | Lista de empresas | ✅ |
| `GET /api/companies/1` | Empresa con sus piezas | ✅ |
| `GET /api/companies/999` | 404 | ✅ |
| `POST /api/companies` con datos correctos | 201 | ✅ |
| `POST` con RFC repetido | 422 | ✅ |
| `POST` sin nombre | 422 | ✅ |
| `POST` con RFC muy corto | 422 | ✅ |
| `PUT /api/companies/{id}` | 200 | ✅ |
| `PUT` con un RFC que ya usa otra empresa | 422 | ✅ |
| `DELETE /api/companies/{id}` | 204 | ✅ |
| `PUT /api/companies/1/parts/3` con `{"stock":40,"min_stock":8}` | 200, stock actualizado | ✅ |
| `PUT` con `{"stock":-5}` | 422 | ✅ |

### Frontend

- `vue-tsc` (revisión de tipos de TypeScript): sin errores. ✅
- Pantallas de Empresas (lista, detalle con stock, edición y bajo stock): **pendiente probarlas en el navegador**.

## Próximos pasos de aprendizaje

- [x] CRUD básico de piezas (API + frontend)
- [x] Control de versiones con Git y GitHub
- [x] Empresas con stock por pieza (relación muchos a muchos)
- [x] Separar la lógica en Services, Resources y Form Requests
- [x] Factories y seeders
- [x] Nuevo diseño del frontend con menú lateral y pantallas de Empresas
- [ ] Probar las pantallas de Empresas en el navegador
- [ ] Tests automáticos con Pest
- [ ] Login con Laravel Sanctum
- [ ] Paginación y búsqueda
