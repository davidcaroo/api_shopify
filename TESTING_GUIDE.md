# Script de Pruebas para API Shopify CRUD

## Instrucciones de Prueba

### 1. Configuración Previa
Asegúrate de que:
- El archivo `.env` esté configurado con tus credenciales de Shopify
- El servidor web esté funcionando
- Las dependencias de Composer estén instaladas

### 2. Pruebas con cURL

#### Probar GET - Obtener todos los productos
```bash
curl -X GET "http://localhost/tu-proyecto/index.php?action=getAllProducts&limit=5"
```

#### Probar GET - Obtener producto por ID (reemplaza PRODUCT_ID)
```bash
curl -X GET "http://localhost/tu-proyecto/index.php?action=getProductById&id=PRODUCT_ID"
```

#### Probar POST - Crear producto
```bash
curl -X POST "http://localhost/tu-proyecto/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Producto de Prueba API",
    "body_html": "<p>Este es un producto creado desde la API para pruebas</p>",
    "product_type": "Pruebas",
    "vendor": "API Test Store",
    "price": "19.99",
    "inventory_quantity": 10,
    "sku": "API-TEST-001"
  }'
```

#### Probar PUT - Actualizar producto (reemplaza PRODUCT_ID con el ID del producto creado)
```bash
curl -X PUT "http://localhost/tu-proyecto/index.php?action=updateProduct&id=PRODUCT_ID" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Producto de Prueba API - ACTUALIZADO",
    "status": "active"
  }'
```

#### Probar DELETE - Eliminar producto (reemplaza PRODUCT_ID)
```bash
curl -X DELETE "http://localhost/tu-proyecto/index.php?action=deleteProduct&id=PRODUCT_ID"
```

### 3. Pruebas con Postman/Insomnia

#### Colección de Endpoints:

**GET Requests:**
1. `GET http://localhost/tu-proyecto/index.php?action=getAllProducts`
2. `GET http://localhost/tu-proyecto/index.php?action=getProductById&id=PRODUCT_ID`
3. `GET http://localhost/tu-proyecto/index.php?action=getInventory&product_id=PRODUCT_ID`
4. `GET http://localhost/tu-proyecto/index.php?action=getAllDiscounts`

**POST Requests:**
1. `POST http://localhost/tu-proyecto/index.php?action=createProduct`
   - Headers: `Content-Type: application/json`
   - Body: Ver ejemplo en documentación

**PUT Requests:**
1. `PUT http://localhost/tu-proyecto/index.php?action=updateProduct&id=PRODUCT_ID`
   - Headers: `Content-Type: application/json`
   - Body: Campos a actualizar

**DELETE Requests:**
1. `DELETE http://localhost/tu-proyecto/index.php?action=deleteProduct&id=PRODUCT_ID`

### 4. Verificar Respuestas

#### Respuesta Exitosa (200):
```json
{
    "ok": true,
    "message": "Operación exitosa",
    "data": {...}
}
```

#### Respuesta de Error (4xx/5xx):
```json
{
    "status": "error",
    "message": "Descripción del error"
}
```

### 5. Casos de Prueba Recomendados

#### Test Case 1: CRUD Completo
1. ✅ Crear un producto con POST
2. ✅ Obtener el producto creado con GET
3. ✅ Actualizar el producto con PUT
4. ✅ Verificar la actualización con GET
5. ✅ Eliminar el producto con DELETE
6. ✅ Verificar que ya no existe con GET (debería devolver 404)

#### Test Case 2: Validaciones
1. ✅ POST sin campos requeridos (debería devolver 422)
2. ✅ GET con ID inexistente (debería devolver 404)
3. ✅ PUT con ID inexistente (debería devolver error)
4. ✅ DELETE con ID inexistente (debería devolver error)

#### Test Case 3: Filtros y Parámetros
1. ✅ GET con filtros (status, product_type, etc.)
2. ✅ GET con límite de resultados
3. ✅ GET inventario de producto

### 6. Debugging

#### Si obtienes errores:

**Error de conexión:**
- Verificar credenciales en `.env`
- Comprobar que Shopify API esté activa
- Revisar permisos de la API key

**Error 500:**
- Revisar logs de PHP
- Verificar sintaxis de archivos
- Comprobar que las dependencias estén instaladas

**JSON inválido:**
- Verificar que el Content-Type sea `application/json`
- Asegurar que el JSON esté bien formado
- Usar herramientas de validación JSON

### 7. Logs de Prueba

Crear un log de tus pruebas:

```
Fecha: ___________
Endpoint: _________________
Método: __________
Parámetros: ________________
Body: ____________________
Respuesta: ________________
Estado: ✅ Exitoso / ❌ Error
Notas: ____________________
```

### 8. Automatización de Pruebas

Para automatizar las pruebas, puedes crear un script bash:

```bash
#!/bin/bash
echo "Iniciando pruebas de API..."

# Crear producto
echo "1. Creando producto..."
PRODUCT_RESPONSE=$(curl -s -X POST "http://localhost/tu-proyecto/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{"title":"Test Product","body_html":"<p>Test</p>","product_type":"Test"}')

echo "Respuesta: $PRODUCT_RESPONSE"

# Obtener productos
echo "2. Obteniendo productos..."
GET_RESPONSE=$(curl -s -X GET "http://localhost/tu-proyecto/index.php?action=getAllProducts&limit=1")
echo "Respuesta: $GET_RESPONSE"

echo "Pruebas completadas."
```