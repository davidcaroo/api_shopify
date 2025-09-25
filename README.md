# API REST Shopify Integration

Este proyecto es una **API REST en PHP** que actúa como intermediario para conectarse con la **API de Shopify**. Permite realizar operaciones CRUD completas sobre productos, variantes, inventario y descuentos de tiendas Shopify.

---

## 🚀 Características Principales

- ✅ CRUD completo para productos, variantes, inventario y descuentos
- ✅ Integración con Shopify API 2023-10
- ✅ Validación y manejo de errores robusto
- ✅ CORS habilitado para frontend
- ✅ Configuración segura por `.env`
- ✅ Ejemplos de uso con cURL y Postman
- ✅ Documentación y guías de prueba incluidas

---

## 📦 Instalación

1. **Clona el repositorio**
   ```bash
   git clone <url-del-repositorio>
   cd API_SHOPIFY
   ```
2. **Instala dependencias**
   ```bash
   composer install
   ```
3. **Configura variables de entorno**
   Copia `.env.example` a `.env` y edítalo con tus credenciales:
   ```env
   # Configuración Shopify
   SHOPIFY_API_KEY=tu_api_key
   SHOPIFY_TOKEN_ACCESS=tu_token
   SHOPIFY_HOST_NAME=tu_tienda.myshopify.com
   # Configuración de Base de Datos
   DB_HOST=127.0.0.1
   DB_USER=usuario
   DB_PASS=contraseña
   DB_NAME=base_de_datos
   # API Key General
   GENERAL_API_KEY=tu_api_key_general
   ```
4. **Configura tu servidor web** (XAMPP, Apache, Nginx, etc.)
   - Apunta el document root a la carpeta del proyecto
   - PHP debe tener PDO y cURL habilitados

---

## 📖 Endpoints Disponibles

### GET (Consultar)
- `GET /index.php?action=getAllProducts` — Lista productos (filtros: limit, status, product_type, vendor)
- `GET /index.php?action=getProductById&id=PRODUCT_ID` — Producto específico
- `GET /index.php?action=getInventory&product_id=PRODUCT_ID` — Inventario de producto
- `GET /index.php?action=getAllDiscounts` — Códigos de descuento

### POST (Crear)
- `POST /index.php?action=createProduct` — Crear producto
- `POST /index.php?action=createDiscount` — Crear código descuento

### PUT (Actualizar)
- `PUT /index.php?action=updateProduct&id=PRODUCT_ID` — Actualizar producto
- `PUT /index.php?action=addVariant&id=PRODUCT_ID` — Agregar variante a producto
- `PUT /index.php?action=updateInventory&variant_id=VARIANT_ID` — Actualizar inventario de variante
- `PUT /index.php?action=updateVariant&variant_id=VARIANT_ID` — Actualizar campos de una variante

### DELETE (Eliminar)
- `DELETE /index.php?action=deleteProduct&id=PRODUCT_ID` — Eliminar producto
- `DELETE /index.php?action=deleteVariant&product_id=PRODUCT_ID&variant_id=VARIANT_ID` — Eliminar variante

> 📋 **Documentación completa**: Ver `API_DOCUMENTATION.md` para ejemplos detallados y parámetros de cada endpoint.

---

## 🧪 Ejemplos de Uso con cURL

### Crear un producto
```bash
curl -X POST "http://localhost/API_SHOPIFY/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Camiseta Ejemplo",
    "body_html": "<p>Camiseta de algodón 100%</p>",
    "product_type": "Ropa",
    "price": "25.99",
    "inventory_quantity": 50
  }'
```

### Agregar variante a un producto
```bash
curl -X PUT "http://localhost/API_SHOPIFY/index.php?action=addVariant&id=123456" \
  -H "Content-Type: application/json" \
  -d '{
    "option1": "XL",
    "price": "29.99",
    "sku": "SKU-XL",
    "inventory_management": "shopify",
    "inventory_quantity": 10
  }'
```

### Actualizar inventario de una variante
```bash
curl -X PUT "http://localhost/API_SHOPIFY/index.php?action=updateInventory&variant_id=789012" \
  -H "Content-Type: application/json" \
  -d '{ "quantity": 75 }'
```

### Actualizar campos de una variante
```bash
curl -X PUT "http://localhost/API_SHOPIFY/index.php?action=updateVariant&variant_id=789012" \
  -H "Content-Type: application/json" \
  -d '{ "price": "27.99", "sku": "SKU-XL-EDIT" }'
```

### Eliminar producto
```bash
curl -X DELETE "http://localhost/API_SHOPIFY/index.php?action=deleteProduct&id=123456"
```

---

## 🛡️ Seguridad y Buenas Prácticas
- Nunca subas el archivo `.env` al repositorio
- Mantén tus API keys y tokens seguros
- Usa HTTPS en producción
- Valida todas las entradas de usuario
- CORS habilitado para desarrollo frontend

---

## 🛠️ Mejoras Técnicas y Validaciones
- Validación JSON en todas las operaciones POST/PUT
- Códigos HTTP apropiados (200, 400, 404, 422, 500)
- Mensajes de error descriptivos y consistentes
- Validación y sanitización de parámetros
- Manejo seguro de IDs dinámicos
- Headers CORS apropiados
- Respuestas estructuradas y uniformes
- Soporte para filtros y paginación

---

## 📁 Estructura del Proyecto
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
├── .env.example           # Plantilla de variables de entorno
├── config.php             # Configuración y carga de .env
├── DB.php                 # Clase de conexión a base de datos
├── index.php              # Punto de entrada principal
├── composer.json          # Dependencias del proyecto
├── API_DOCUMENTATION.md   # Documentación de endpoints
├── CRUD_IMPLEMENTATION_SUMMARY.md # Resumen técnico CRUD
├── TESTING_GUIDE.md       # Guía de pruebas
├── SECURITY.md            # Guía de seguridad
└── README.md              # Este archivo
```

---

## ✅ Estado del Proyecto
- CRUD completo implementado
- Validaciones y manejo de errores
- Documentación y guías de prueba
- Listo para integración con frontend y despliegue

---

## 📚 Referencias y Documentación
- `API_DOCUMENTATION.md`: Documentación completa de endpoints
- `CRUD_IMPLEMENTATION_SUMMARY.md`: Resumen técnico CRUD
- `TESTING_GUIDE.md`: Guía de pruebas y ejemplos
- `SECURITY.md`: Guía de seguridad

---

**¡La API está completamente funcional y lista para usar!** 🎉