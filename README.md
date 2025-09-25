# API REST Shopify Integration

Este proyecto es una **API REST en PHP** que actúa como intermediario para conectarse con la **API de Shopify**. Permite realizar operaciones sobre productos, descuentos y otros recursos de tiendas Shopify.

## Características

- ✅ **Integración con Shopify API**: Conecta con múltiples tiendas Shopify
- ✅ **CORS habilitado**: Permite peticiones desde cualquier origen
- ✅ **Base de datos local**: Conexión MySQL para operaciones adicionales
- ✅ **Variables de entorno**: Configuración segura mediante archivo .env
- ✅ **Operaciones CRUD**: Estructura para GET, POST, PUT, DELETE

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <url-del-repositorio>
   cd API_SHOPIFY
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   ```
   
   Edita el archivo `.env` con tus credenciales:
   ```env
   # Configuración de Base de Datos
   DB_HOST=127.0.0.1
   DB_USER=tu_usuario
   DB_PASS=tu_contraseña
   DB_NAME=tu_base_de_datos

   # Configuración Shopify - Tienda Principal
   SHOPIFY_API_KEY=tu_api_key_shopify
   SHOPIFY_TOKEN_ACCESS=tu_token_acceso_shopify
   SHOPIFY_HOST_NAME=tu_tienda.myshopify.com
   
   # API Key General
   GENERAL_API_KEY=tu_api_key_general
   ```

4. **Configurar servidor web**
   - Apunta el documento root a la carpeta del proyecto
   - Asegúrate de que PHP tenga los módulos PDO y cURL habilitados

## Endpoints Disponibles

### 📖 Operaciones de Lectura (GET)
- **Obtener todos los productos**: `GET /index.php?action=getAllProducts`
- **Obtener producto por ID**: `GET /index.php?action=getProductById&id=PRODUCT_ID`
- **Obtener inventario**: `GET /index.php?action=getInventory&product_id=PRODUCT_ID`
- **Obtener descuentos**: `GET /index.php?action=getAllDiscounts`

### ➕ Operaciones de Creación (POST)
- **Crear producto**: `POST /index.php?action=createProduct`
- **Crear descuento**: `POST /index.php?action=createDiscount`

### ✏️ Operaciones de Actualización (PUT)
- **Actualizar producto**: `PUT /index.php?action=updateProduct&id=PRODUCT_ID`
- **Actualizar inventario**: `PUT /index.php?action=updateInventory&variant_id=VARIANT_ID`

### ❌ Operaciones de Eliminación (DELETE)
- **Eliminar producto**: `DELETE /index.php?action=deleteProduct&id=PRODUCT_ID`
- **Eliminar variante**: `DELETE /index.php?action=deleteVariant&product_id=PRODUCT_ID&variant_id=VARIANT_ID`

> 📋 **Documentación completa**: Ver `API_DOCUMENTATION.md` para ejemplos detallados y parámetros de cada endpoint.

## Códigos de Respuesta HTTP

- `200` - OK: La petición se ha completado correctamente
- `400` - Bad Request: La solicitud contiene sintaxis errónea
- `404` - Not Found: El recurso solicitado no existe
- `422` - Unprocessable Entity: Entidad no procesable
- `500` - Internal Server Error: Error interno del servidor

## Estructura del Proyecto

```
.
├── APIS/
│   ├── MetodosAPI.php     # API principal de Shopify
│   └── LoginAPI.php       # API secundaria
├── modelo/
│   ├── metodos_database.php    # Funciones de base de datos
│   └── metodos_generales.php   # Funciones auxiliares
├── vendor/                # Dependencias de Composer
├── .env                   # Variables de entorno (NO subir a git)
├── .env.example          # Plantilla de variables de entorno
├── config.php            # Configuración y carga de .env
├── DB.php                # Clase de conexión a base de datos
├── index.php             # Punto de entrada principal
└── composer.json         # Dependencias del proyecto
```

## Seguridad

⚠️ **IMPORTANTE**: 
- Nunca subas el archivo `.env` al repositorio
- Mantén tus API keys y tokens seguros
- Usa HTTPS en producción
- Valida todas las entradas de usuario

## Testing

Para probar la API puedes usar herramientas como:
- **Postman**
- **Insomnia**
- **cURL**

Ejemplo con cURL:
```bash
curl -X GET "http://localhost/tu-proyecto/index.php?action=getAllProducts"
```

## Dependencias

- `vlucas/phpdotenv`: Manejo de variables de entorno
- `dompdf/dompdf`: Generación de PDFs
- `phpoffice/phpspreadsheet`: Manejo de archivos Excel

## Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request