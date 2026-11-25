# Punto de venta: Papelería "El Lápiz Feliz"

Sistema web para registrar las ventas de una papelería y controlar su inventario.
Está dividido en **dos proyectos separados**:

- **backend/**: API REST en PHP con arquitectura **MVC** y base de datos **MySQL** (7 tablas en 3FN).
- **frontend/**: aplicación **Vue 3** creada con **Vite**.

## Tecnologías

| Parte          | Tecnología                          | Por qué                                                          |
|----------------|-------------------------------------|------------------------------------------------------------------|
| Base de datos  | MySQL 8 (motor InnoDB)              | Llaves foráneas, restricciones y transacciones                   |
| Backend        | PHP 8 + PDO, MVC sin framework      | Viene con WampServer; cada capa tiene una sola responsabilidad   |
| Frontend       | Vue 3 + Vue Router + Vite           | Componentes reutilizables y la interfaz se actualiza sola (reactividad) |
| Estilos        | Bootstrap 5 + Bootstrap Icons       | Diseño responsivo (computadora y celular)                         |

## Arquitectura

```
┌──────────────── FRONTEND (Vue) ────────────────┐        ┌──────────────────── BACKEND (PHP · MVC) ─────────────────────┐
│ views/ProductosView.vue                        │        │ public/index.php  (todas las peticiones entran aquí)         │
│   └─ services/api.js  →  fetch('/api/...')     │ ─JSON─▶│   └─ routes/api.php      (qué controlador atiende cada URL)  │
│                                                │        │        └─ Middleware/Auth (¿tiene sesión? ¿es admin?)        │
│ router/index.js  (páginas y protección)        │◀─JSON─ │             └─ Controllers/ProductoController (valida)       │
│ services/sesion.js (usuario conectado)         │        │                  └─ Models/Producto (consultas SQL con PDO)  │
└────────────────────────────────────────────────┘        └───────────────────────────────┬──────────────────────────────┘
                                                                                          ▼
                                                                                 MySQL · papeleria_db
```

**Flujo de una petición** (ej. el administrador guarda un producto):
1. `ProductosView.vue` llama a `api('/productos', 'POST', datos)`.
2. La petición llega a `backend/public/index.php` (gracias a `.htaccess`).
3. El **Router** busca la ruta `POST /api/productos` en `routes/api.php`.
4. El **Middleware** `Auth::requiereAdmin()` revisa que el usuario sea administrador.
5. El **Controlador** `ProductoController@store` valida los datos.
6. El **Modelo** `Producto::crear()` ejecuta el `INSERT` con una consulta preparada.
7. Se responde `{ "ok": true, "mensaje": "Producto registrado." }` y Vue muestra el aviso y recarga la tabla.

## Estructura de carpetas

```
papeleria/
├── database/
│   ├── 01_esquema.sql          Tablas, llaves, restricciones, índices y vista
│   ├── 02_datos.sql            Datos de ejemplo
│   └── 03_consultas.sql        Consultas con JOIN, GROUP BY y subconsultas
│
├── backend/                    API REST (MVC)
│   ├── public/
│   │   ├── index.php           Punto de entrada (Front Controller)
│   │   └── .htaccess           Envía todas las URL a index.php
│   ├── routes/api.php          Lista de rutas → controlador + middleware
│   ├── config/config.php       Datos de conexión a MySQL
│   └── app/
│       ├── bootstrap.php       Arranque: carga de clases, sesión y manejo de errores
│       ├── Core/               Router, Database (PDO), Controller base, Response (JSON)
│       ├── Middleware/Auth.php requiereLogin() / requiereAdmin()
│       ├── Controllers/        Auth, Panel, Producto, Categoria, Cliente, Venta
│       └── Models/             Usuario, Producto, Categoria, Cliente, Venta, Reporte
│
└── frontend/                   Aplicación Vue (Vite)
    ├── package.json            Dependencias y comandos (dev / build)
    ├── vite.config.js          Proxy /api → backend en Apache
    └── src/
        ├── main.js             Arranque de Vue
        ├── App.vue             Estructura: menú + barra superior + página actual
        ├── router/index.js     Rutas y guardia de sesión
        ├── services/           api.js, sesion.js, avisos.js, formato.js
        ├── components/         BarraLateral, BarraSuperior, VentanaModal, EtiquetaStock, Avisos
        ├── views/              Login, Panel, Venta, Ventas, Productos, Categorias, Clientes
        └── assets/estilos.css
```

### ¿Qué hace cada capa del MVC?

| Capa            | Archivos                | Responsabilidad                                         |
|-----------------|-------------------------|---------------------------------------------------------|
| **Modelo**      | `app/Models/*.php`      | Solo SQL: consultar y guardar en MySQL                  |
| **Vista**       | El frontend en Vue      | Mostrar los datos y capturar lo que escribe el usuario  |
| **Controlador** | `app/Controllers/*.php` | Recibir la petición, validar, llamar al modelo y responder JSON |
| Rutas           | `routes/api.php`        | Decidir qué controlador atiende cada URL                |
| Middleware      | `app/Middleware/Auth.php` | Revisar sesión y rol antes del controlador            |

## API (rutas)

| Método | Ruta                    | Controlador                  | Acceso   |
|--------|-------------------------|------------------------------|----------|
| POST   | `/api/login`            | AuthController@login         | Público  |
| POST   | `/api/logout`           | AuthController@logout        | Público  |
| GET    | `/api/sesion`           | AuthController@sesion        | Sesión   |
| GET    | `/api/panel`            | PanelController@index        | Sesión   |
| GET    | `/api/productos`        | ProductoController@index     | Sesión   |
| POST   | `/api/productos`        | ProductoController@store     | Admin    |
| PUT    | `/api/productos/{id}`   | ProductoController@update    | Admin    |
| DELETE | `/api/productos/{id}`   | ProductoController@destroy   | Admin    |
| GET/POST/PUT/DELETE | `/api/categorias[/{id}]` | CategoriaController | Igual que productos |
| GET/POST/PUT/DELETE | `/api/clientes[/{id}]`   | ClienteController   | Igual que productos |
| GET    | `/api/ventas?desde=&hasta=` | VentaController@index    | Sesión   |
| GET    | `/api/ventas/{id}`      | VentaController@show         | Sesión   |
| POST   | `/api/ventas`           | VentaController@store        | Sesión   |

## Base de datos

```
roles 1───N usuarios 1───N ventas N───1 clientes
                             │
                             1
                             │
                             N
                       detalle_venta N───1 productos N───1 categorias
```

- **3FN:** la categoría, el cliente y el rol no se repiten; solo se guarda su id (llave foránea).
- **`ventas` no tiene total:** se calcula del detalle con la vista `vista_ventas`.
- **`precio_unitario` en el detalle:** guarda el precio del día de la venta (historial).
- **CHECK:** precio > 0, stock ≥ 0, cantidad > 0. **UNIQUE:** código de producto, usuario, email.
- **Índices:** `productos.nombre` (búsqueda en caja) y `ventas.fecha` (reportes).
- **ENGINE = InnoDB:** WampServer usa MyISAM por defecto, que no respeta llaves foráneas ni transacciones.

## Descargar el proyecto

Repositorio: https://github.com/ampeflo/papeleria-punto-de-venta

**Opción 1, con Git** (la carpeta debe llamarse `papeleria`):
```
cd C:\wamp64\www
git clone https://github.com/ampeflo/papeleria-punto-de-venta.git papeleria
```

**Opción 2, sin Git:** en la página del repositorio, botón verde **Code → Download ZIP**. Descomprimir, renombrar la carpeta `papeleria-punto-de-venta-main` a `papeleria` y moverla a `C:\wamp64\www\`.

> La carpeta debe quedar como `C:\wamp64\www\papeleria`, porque el frontend compilado usa la ruta `/papeleria/`.

## Instalación

**Requisitos:** WampServer (MySQL 8 y PHP 8, con `mod_rewrite` activo) y Node.js 18 o superior.

### 1. Base de datos
1. Enciende WampServer (ícono verde).
2. En `http://localhost/phpmyadmin` → **Importar**: primero `database/01_esquema.sql` y después `database/02_datos.sql`.
3. Si tu MySQL tiene contraseña, escríbela en `backend/config/config.php`.

### 2. Backend
No necesita instalación: Apache lo sirve desde `C:\wamp64\www\papeleria\backend\public`.
Prueba: `http://localhost/papeleria/backend/public/api/panel` debe responder `"Debes iniciar sesión."`.

### 3. Frontend
```
cd C:\wamp64\www\papeleria\frontend
npm install
```

**Modo desarrollo** (para programar; se actualiza al guardar):
```
npm run dev
```
Abrir `http://localhost:5173`.

**Modo final** (lo sirve Apache, sin Node):
```
npm run build
```
Abrir `http://localhost/papeleria`.

### Usuarios de prueba

| Usuario | Contraseña | Rol           |
|---------|------------|---------------|
| admin   | admin123   | Administrador |
| luis    | cajero123  | Cajero        |

## Roles

| Acción                                                    | Administrador | Cajero |
|-----------------------------------------------------------|:-------------:|:------:|
| Ver panel, vender, ver ventas y tickets                   | ✅            | ✅     |
| Consultar productos, categorías y clientes                | ✅            | ✅     |
| Crear, editar y eliminar productos, categorías y clientes | ✅            | ❌     |

En Vue los botones se ocultan con `v-if="esAdmin"`, pero **la seguridad real está en el backend**:
el middleware `admin` responde "Acceso denegado" aunque alguien llame a la API directamente.

## Seguridad y buenas prácticas
- **Consultas preparadas (PDO)** en todos los modelos: evitan la inyección SQL.
- **Contraseñas cifradas** con `password_hash()` / `password_verify()`; el hash nunca sale del servidor.
- **Transacción en la venta** (`Venta::crear`): venta + detalle + stock, o `ROLLBACK` si algo falla.
  `SELECT ... FOR UPDATE` evita que dos cajas vendan la última pieza al mismo tiempo.
- **Validación doble:** HTML5 en los formularios de Vue y validación en los controladores PHP.
- **Manejo central de errores** (`app/bootstrap.php`): los errores de MySQL se convierten en mensajes claros.
- **Solo `public/` es accesible** desde el navegador; `app/`, `config/` y `routes/` están bloqueadas.
- Vue escapa automáticamente el texto que muestra con `{{ }}` (protección contra XSS).
