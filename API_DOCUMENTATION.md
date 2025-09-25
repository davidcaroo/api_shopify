# Documentación de Endpoints - API Shopify CRUD

## Descripción General

Esta API proporciona operaciones CRUD completas para productos de Shopify, incluyendo gestión de inventario y códigos de descuento.

## Base URL
```
http://localhost/tu-proyecto/index.php
```

## Autenticación
La API utiliza las credenciales configuradas en el archivo `.env` para autenticarse con Shopify.

---

## 📖 Endpoints GET (Consultar)

### 1. Obtener todos los productos
```http
GET /index.php?action=getAllProducts
```

**Parámetros opcionales:**
- `limit`: Número de productos a devolver (máximo 250, por defecto 50)
- `status`: Filtrar por estado (`active`, `archived`, `draft`)
- `product_type`: Filtrar por tipo de producto
- `vendor`: Filtrar por proveedor

**Ejemplo:**
```http
GET /index.php?action=getAllProducts&limit=10&status=active
```

**Respuesta exitosa:**
```json
{
    "ok": true,
    "message": "getAllProducts",
    "count": 10,
    "products": [...]
}
```

### 2. Obtener producto por ID
```http
GET /index.php?action=getProductById&id=PRODUCT_ID
```

**Parámetros requeridos:**
- `id`: ID del producto en Shopify

**Respuesta exitosa:**
```json
{
    "ok": true,
    "message": "getProductById",
    "product": {...}
}
```

### 3. Obtener inventario de producto
```http
GET /index.php?action=getInventory&product_id=PRODUCT_ID
```

**Parámetros requeridos:**
- `product_id`: ID del producto

**Respuesta exitosa:**
```json
{
    "ok": true,
    "message": "getInventory",
    "product_id": "123456",
    "variants": [...]
}
```

### 4. Obtener conteo de descuentos
```http
GET /index.php?action=getAllDiscounts
```

**Respuesta exitosa:**
```json
{
    "ok": true,
    "message": "getAllDiscounts",
    "discount_count": {...}
}
```

---

## ➕ Endpoints POST (Crear)

### 1. Crear producto
```http
POST /index.php?action=createProduct
Content-Type: application/json
```

**Body requerido:**
```json
{
    "title": "Nombre del producto",
    "body_html": "<p>Descripción del producto</p>",
    "product_type": "Categoría"
}
```

**Body opcional:**
```json
{
    "title": "Producto Ejemplo",
    "body_html": "<p>Descripción detallada</p>",
    "product_type": "Electrónicos",
    "vendor": "Mi Tienda",
    "status": "active",
    "price": "29.99",
    "inventory_quantity": 100,
    "sku": "PROD-001"
}
```

**Respuesta exitosa:**
```json
{
    "ok": true,
    "message": "Producto creado exitosamente",
    "product": {...}
}
```

### 2. Crear código de descuento
```http
POST /index.php?action=createDiscount
Content-Type: application/json
```

**Body requerido:**
```json
{
    "code": "DESCUENTO10",
    "discount_type": "percentage",
    "value": "10.00"
}
```

**Body opcional:**
```json
{
    "code": "DESCUENTO10",
    "discount_type": "percentage",
    "value": "10.00",
    "usage_limit": 100
}
```

**Tipos de descuento:**
- `percentage`: Porcentaje de descuento
- `fixed_amount`: Cantidad fija de descuento

---

## ✏️ Endpoints PUT (Actualizar)

### 1. Actualizar producto
```http
PUT /index.php?action=updateProduct&id=PRODUCT_ID
Content-Type: application/json
```

**Parámetros requeridos:**
- `id`: ID del producto a actualizar

**Body (campos opcionales):**
```json
{
    "title": "Nuevo título",
    "body_html": "<p>Nueva descripción</p>",
    "vendor": "Nuevo proveedor",
    "product_type": "Nueva categoría",
    "status": "active"
}
```

**Para actualizar variantes:**
```json
{
    "variants": [
        {
            "id": 123456,
            "price": "39.99",
            "inventory_quantity": 50
        }
    ]
}
```

### 2. Actualizar inventario
```http
PUT /index.php?action=updateInventory&variant_id=VARIANT_ID
Content-Type: application/json
```

**Parámetros requeridos:**
- `variant_id`: ID de la variante a actualizar

**Body requerido:**
```json
{
    "quantity": 75
}
```

---

## ❌ Endpoints DELETE (Eliminar)

### 1. Eliminar producto
```http
DELETE /index.php?action=deleteProduct&id=PRODUCT_ID
```

**Parámetros requeridos:**
- `id`: ID del producto a eliminar

**Respuesta exitosa:**
```json
{
    "ok": true,
    "message": "Producto eliminado exitosamente",
    "product_id": "123456"
}
```

### 2. Eliminar variante de producto
```http
DELETE /index.php?action=deleteVariant&product_id=PRODUCT_ID&variant_id=VARIANT_ID
```

**Parámetros requeridos:**
- `product_id`: ID del producto
- `variant_id`: ID de la variante a eliminar

**Respuesta exitosa:**
```json
{
    "ok": true,
    "message": "Variante eliminada exitosamente",
    "variant_id": "789012"
}
```

---

## 📋 Códigos de Respuesta HTTP

| Código | Descripción |
|--------|-------------|
| 200 | OK - Operación exitosa |
| 400 | Bad Request - Solicitud inválida |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Datos inválidos |
| 500 | Internal Server Error - Error del servidor |

---

## 🧪 Ejemplos de Uso con cURL

### Crear un producto:
```bash
curl -X POST "http://localhost/tu-proyecto/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Camiseta Ejemplo",
    "body_html": "<p>Camiseta de algodón 100%</p>",
    "product_type": "Ropa",
    "price": "25.99",
    "inventory_quantity": 50
  }'
```

### Obtener productos:
```bash
curl -X GET "http://localhost/tu-proyecto/index.php?action=getAllProducts&limit=5"
```

### Actualizar producto:
```bash
curl -X PUT "http://localhost/tu-proyecto/index.php?action=updateProduct&id=123456" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Camiseta Actualizada",
    "status": "active"
  }'
```

### Eliminar producto:
```bash
curl -X DELETE "http://localhost/tu-proyecto/index.php?action=deleteProduct&id=123456"
```

---

## ⚠️ Notas Importantes

1. **Límites de API**: Shopify tiene límites de rate limiting. La API maneja hasta 250 productos por solicitud.

2. **IDs de Shopify**: Los IDs de productos y variantes son números enteros grandes proporcionados por Shopify.

3. **Validación**: Todos los endpoints validan los datos de entrada y devuelven errores descriptivos.

4. **Errores**: Los errores se devuelven en formato JSON con códigos HTTP apropiados.

5. **CORS**: La API tiene CORS habilitado para permitir peticiones desde navegadores web.

## 🔧 Solución de Problemas

- **Error 422**: Verificar que todos los campos requeridos estén presentes
- **Error 500**: Revisar las credenciales de Shopify en el archivo `.env`
- **Error 404**: Verificar que el ID del producto/variante sea correcto
- **JSON inválido**: Asegurar que el Content-Type sea `application/json`