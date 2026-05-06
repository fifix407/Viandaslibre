# 🛠️ Manual Técnico - ViandaLibre

Este manual está dirigido a desarrolladores y analistas que deseen comprender la arquitectura del sistema, su base de datos y cómo extender su funcionalidad.

## 🏗️ Arquitectura MVC
El sistema sigue el patrón Modelo-Vista-Controlador:
- **Modelos (`app/models/`):** Clases PHP que utilizan `MySQLi` con sentencias preparadas para interactuar con la base de datos.
- **Vistas (`app/views/`):** Archivos `.php` que contienen el HTML y la lógica de presentación.
- **Controladores (`app/controllers/`):** Orquestadores que reciben las peticiones, llaman a los modelos y cargan las vistas.

## 📡 Documentación de Endpoints (API)

### Listar Viandas
- **URL:** `public/api/get_viandas.php`
- **Método:** `GET`
- **Parámetros:** Ninguno.
- **Respuesta:** Array de objetos JSON con las viandas disponibles.

### Crear Pedido
- **URL:** `public/api/save_pedidos.php`
- **Método:** `POST`
- **Cuerpo Sugerido (JSON):**
  ```json
  {
    "cliente_nombre": "Nombre del Cliente",
    "cliente_whatsapp": "Número de Teléfono",
    "direccion_entrega": "Dirección de Envío",
    "items": [
      { "id_vianda": 1, "cantidad": 2 },
      { "id_vianda": 5, "cantidad": 1 }
    ]
  }
  ```
- **Lógica:** El endpoint valida stock, calcula el total en el servidor y registra la transacción.

## 📊 Diccionario de Datos: Tabla `pedidos`

| Campo | Tipo | Nulo | Relación | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id_pedido` | INT(11) | NO | PK | Clave primaria autoincremental. |
| `fecha_pedido` | TIMESTAMP | NO | - | Registro automático de fecha y hora. |
| `cliente_nombre` | VARCHAR(100) | NO | - | Nombre para la etiqueta de entrega. |
| `cliente_whatsapp` | VARCHAR(20) | NO | - | Formato internacional sugerido. |
| `direccion_entrega`| VARCHAR(255) | NO | - | Punto de destino del envío. |
| `total_pago` | DECIMAL(10,2)| NO | - | Calculado automáticamente por el modelo. |
| `estado` | ENUM | NO | - | Flujo: Pendiente -> Cocina -> Enviado -> Entregado. |

## 👨‍💻 Guía para el Nuevo Programador
Si eres nuevo en "ViandaLibre", sigue estos pasos para sumarte al desarrollo:
1. **Entorno:** Asegúrate de tener XAMPP configurado y la base de datos importada desde `sql/schema.sql`.
2. **Nuevas Funcionalidades:**
   - Si necesitas una nueva entidad, crea el archivo en `app/models/` siguiendo el estilo de `Vianda.php`.
   - Define las rutas y acciones en un nuevo controlador en `app/controllers/`.
3. **Estándares de Código:**
   - Usa **PHPDoc** para comentar todas las funciones.
   - Utiliza siempre **sentencias preparadas** (`mysqli_prepare`) para evitar inyecciones SQL.
   - Los archivos estáticos (JS/CSS) deben ir dentro de `public/assets/`.
4. **Pruebas:** Verifica que tus cambios no rompan el flujo de compra principal testeando el `index.php` y el panel de administración.
