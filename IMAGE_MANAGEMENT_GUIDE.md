# 📸 Guía de Manejo de Imágenes en Shopify API

## 🎯 **Estrategias Disponibles**

### **1. Crear Producto SIN Imagen (Más Rápido)**
```json
POST /index.php?action=createProduct
{
    "title": "Mi Producto",
    "body_html": "Descripción del producto",
    "product_type": "Electrónicos",
    "price": "29.99"
}
```
**✅ Ventajas:** Creación rápida, se puede agregar imagen después  
**⚠️ Desventajas:** Producto sin imagen visual inicialmente

---

### **2. Crear Producto CON Imágenes desde URLs**
```json
POST /index.php?action=createProduct
{
    "title": "Mi Producto",
    "body_html": "Descripción del producto",
    "product_type": "Electrónicos",
    "price": "29.99",
    "images": [
        "https://ejemplo.com/imagen1.jpg",
        "https://ejemplo.com/imagen2.png"
    ]
}
```
**✅ Ventajas:** Producto completo desde el inicio  
**⚠️ Requisitos:** Las URLs deben ser públicamente accesibles

---

### **3. Agregar Imagen a Producto Existente (URL)**
```json
POST /index.php?action=addProductImage&product_id=123456789
{
    "image_url": "https://ejemplo.com/nueva-imagen.jpg",
    "alt_text": "Descripción de la imagen"
}
```

---

### **4. Subir Imagen desde Archivo Local (Base64)**
```json
POST /index.php?action=uploadProductImage&product_id=123456789
{
    "image_base64": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEA...",
    "filename": "mi-imagen.jpg",
    "alt_text": "Descripción de la imagen"
}
```

## 🔄 **Flujos de Trabajo Recomendados**

### **Opción A: Crear → Agregar Imagen (Recomendado)**
1. **Crear producto sin imagen** (rápido)
2. **Obtener el product_id** de la respuesta
3. **Agregar imagen(es)** usando el ID

**Ejemplo completo:**
```bash
# Paso 1: Crear producto
curl -X POST "http://localhost/api_shopify/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Smartphone XYZ",
    "body_html": "El mejor smartphone del mercado",
    "product_type": "Electrónicos",
    "price": "599.99"
  }'

# Respuesta: {"ok": true, "product": {"id": 123456789, ...}}

# Paso 2: Agregar imagen
curl -X POST "http://localhost/api_shopify/index.php?action=addProductImage&product_id=123456789" \
  -H "Content-Type: application/json" \
  -d '{
    "image_url": "https://mi-servidor.com/images/smartphone-xyz.jpg",
    "alt_text": "Smartphone XYZ vista frontal"
  }'
```

### **Opción B: Crear con Imagen Directamente**
```bash
curl -X POST "http://localhost/api_shopify/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Smartphone XYZ",
    "body_html": "El mejor smartphone del mercado",
    "product_type": "Electrónicos",
    "price": "599.99",
    "images": [
      "https://mi-servidor.com/images/smartphone-xyz-front.jpg",
      "https://mi-servidor.com/images/smartphone-xyz-back.jpg"
    ]
  }'
```

## 📋 **Formatos de Imagen Soportados**

| Formato | Extensión | Tamaño Máximo | Recomendado |
|---------|-----------|---------------|-------------|
| JPEG    | .jpg, .jpeg | 20MB | ✅ Sí |
| PNG     | .png | 20MB | ✅ Sí |
| GIF     | .gif | 20MB | ⚠️ Solo estáticas |
| WebP    | .webp | 20MB | ✅ Moderno |

## 🚀 **Mejores Prácticas**

### **1. URLs de Imágenes**
- ✅ Usar HTTPS siempre
- ✅ URLs públicamente accesibles
- ✅ Imágenes optimizadas (< 500KB recomendado)
- ✅ Dimensiones mínimas: 800x800px

### **2. Base64 (Archivos Locales)**
- ⚠️ Solo para imágenes pequeñas (< 1MB)
- ✅ Formato: `data:image/jpeg;base64,`
- ✅ Incluir filename descriptivo

### **3. Performance**
- 🚄 **Más rápido:** Crear producto → Agregar imagen después
- 🐌 **Más lento:** Crear producto con múltiples imágenes
- 💾 **Memoria:** Base64 consume más recursos

## 🔍 **Casos de Uso Comunes**

### **E-commerce Dropshipping**
```json
// Crear producto sin imagen, obtener de proveedor después
{
    "title": "Producto Importado",
    "body_html": "Descripción temporal",
    "product_type": "Importados",
    "price": "0.00"
}
// Luego actualizar con imagen del proveedor
```

### **Catálogo con Imágenes Locales**
```javascript
// Frontend: Convertir archivo a Base64
const file = document.getElementById('imageInput').files[0];
const reader = new FileReader();
reader.onload = function(e) {
    const base64 = e.target.result;
    
    fetch('/api_shopify/index.php?action=uploadProductImage&product_id=123', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            image_base64: base64,
            filename: file.name,
            alt_text: 'Imagen del producto'
        })
    });
};
reader.readAsDataURL(file);
```

### **Migración de Catálogo**
```json
// Crear productos en lote con imágenes existentes
{
    "title": "Producto Migrado",
    "body_html": "Descripción completa",
    "product_type": "Categoría",
    "price": "49.99",
    "images": [
        "https://mi-antigua-tienda.com/img/producto1.jpg"
    ]
}
```

## ⚡ **Endpoints Disponibles**

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `?action=createProduct` | POST | Crear producto (con o sin imágenes) |
| `?action=addProductImage&product_id=X` | POST | Agregar imagen por URL |
| `?action=uploadProductImage&product_id=X` | POST | Subir imagen Base64 |

## 🔧 **Troubleshooting**

### **Error: "URL de imagen no válida"**
- ✅ Verificar que la URL sea accesible públicamente
- ✅ Usar HTTPS en lugar de HTTP
- ✅ Verificar que el servidor permita hotlinking

### **Error: "Formato Base64 no válido"**
- ✅ Verificar formato: `data:image/[tipo];base64,`
- ✅ Tipos válidos: jpeg, jpg, png, gif
- ✅ No incluir espacios o saltos de línea

### **Error: "Imagen muy grande"**
- ✅ Comprimir imagen antes de subir
- ✅ Usar herramientas como TinyPNG
- ✅ Considerar formato WebP para mejor compresión

---

> 💡 **Recomendación:** Para la mayoría de casos, crear el producto primero sin imagen y agregar la imagen después es la estrategia más eficiente y flexible.