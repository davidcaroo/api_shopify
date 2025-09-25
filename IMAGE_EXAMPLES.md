# 🧪 Ejemplos Prácticos - Manejo de Imágenes Shopify

## 📌 **Caso 1: E-commerce Dropshipping**

### Flujo Recomendado: Crear → Obtener Imagen → Actualizar

```bash
# 1. Crear producto básico rápidamente
curl -X POST "http://localhost/api_shopify/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "iPhone 15 Pro Max",
    "body_html": "<p>El último iPhone con chip A17 Pro</p>",
    "product_type": "Smartphones",
    "vendor": "Apple",
    "price": "1199.99",
    "sku": "IPHONE15PM-256GB"
  }'
```

**Respuesta esperada:**
```json
{
    "ok": true,
    "message": "Producto creado exitosamente",
    "product": {
        "id": 8234567890123,
        "title": "iPhone 15 Pro Max",
        "handle": "iphone-15-pro-max",
        "images": []
    }
}
```

```bash
# 2. Agregar imagen del proveedor
curl -X POST "http://localhost/api_shopify/index.php?action=addProductImage&product_id=8234567890123" \
  -H "Content-Type: application/json" \
  -d '{
    "image_url": "https://supplier.com/images/iphone15-pro-max.jpg",
    "alt_text": "iPhone 15 Pro Max - Titanio Natural"
  }'
```

---

## 📌 **Caso 2: Catálogo con Múltiples Imágenes**

### Crear producto con todas las imágenes desde el inicio:

```bash
curl -X POST "http://localhost/api_shopify/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Zapatillas Nike Air Max",
    "body_html": "<p>Comodidad y estilo en cada paso</p>",
    "product_type": "Calzado",
    "vendor": "Nike",
    "price": "149.99",
    "images": [
      "https://mi-tienda.com/img/nike-air-max-front.jpg",
      "https://mi-tienda.com/img/nike-air-max-side.jpg",
      "https://mi-tienda.com/img/nike-air-max-back.jpg",
      "https://mi-tienda.com/img/nike-air-max-sole.jpg"
    ]
  }'
```

---

## 📌 **Caso 3: Upload desde Archivo Local (React/JavaScript)**

### Frontend JavaScript para subir imagen:

```javascript
// Función para convertir archivo a Base64
function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
}

// Crear producto y subir imagen
async function createProductWithImage() {
    try {
        // 1. Crear producto
        const productResponse = await fetch('/api_shopify/index.php?action=createProduct', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                title: 'Producto con Imagen Local',
                body_html: 'Descripción del producto',
                product_type: 'Electrónicos',
                price: '99.99'
            })
        });
        
        const productData = await productResponse.json();
        const productId = productData.product.id;
        
        // 2. Obtener archivo de input
        const fileInput = document.getElementById('imageFile');
        const file = fileInput.files[0];
        
        if (file) {
            // 3. Convertir a Base64
            const base64Image = await fileToBase64(file);
            
            // 4. Subir imagen
            const imageResponse = await fetch(`/api_shopify/index.php?action=uploadProductImage&product_id=${productId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    image_base64: base64Image,
                    filename: file.name,
                    alt_text: 'Imagen del producto'
                })
            });
            
            const imageData = await imageResponse.json();
            console.log('Imagen subida:', imageData);
        }
        
    } catch (error) {
        console.error('Error:', error);
    }
}
```

### HTML para el formulario:

```html
<form id="productForm">
    <input type="text" id="productTitle" placeholder="Título del producto" required>
    <textarea id="productDescription" placeholder="Descripción"></textarea>
    <input type="number" id="productPrice" placeholder="Precio" step="0.01" required>
    <input type="file" id="imageFile" accept="image/*">
    <button type="button" onclick="createProductWithImage()">Crear Producto</button>
</form>
```

---

## 📌 **Caso 4: Migración de Catálogo Existente**

### Script PHP para migrar productos con imágenes:

```php
<?php
// migrar_productos.php

$productos_existentes = [
    [
        'title' => 'Laptop Dell XPS 13',
        'description' => 'Ultrabook premium con pantalla 4K',
        'price' => '1299.99',
        'image_url' => 'https://mi-antigua-tienda.com/images/dell-xps-13.jpg'
    ],
    [
        'title' => 'Mouse Logitech MX Master 3',
        'description' => 'Mouse ergonómico para profesionales',
        'price' => '99.99',
        'image_url' => 'https://mi-antigua-tienda.com/images/logitech-mx3.jpg'
    ]
];

foreach ($productos_existentes as $producto) {
    // Crear producto con imagen
    $data = [
        'title' => $producto['title'],
        'body_html' => '<p>' . $producto['description'] . '</p>',
        'product_type' => 'Electrónicos',
        'price' => $producto['price'],
        'images' => [$producto['image_url']]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/api_shopify/index.php?action=createProduct');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo "Producto creado: " . $producto['title'] . "\n";
    echo "Respuesta: " . $response . "\n\n";
}
?>
```

---

## 📌 **Caso 5: Optimización de Imágenes Antes de Subir**

### Función JavaScript para redimensionar imágenes:

```javascript
function resizeImage(file, maxWidth = 800, quality = 0.8) {
    return new Promise((resolve) => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = function() {
            // Calcular nuevas dimensiones manteniendo aspecto
            const ratio = Math.min(maxWidth / img.width, maxWidth / img.height);
            canvas.width = img.width * ratio;
            canvas.height = img.height * ratio;
            
            // Dibujar imagen redimensionada
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            
            // Convertir a Base64 con compresión
            const base64 = canvas.toDataURL('image/jpeg', quality);
            resolve(base64);
        };
        
        img.src = URL.createObjectURL(file);
    });
}

// Uso de la función
async function uploadOptimizedImage(productId, file) {
    try {
        // Redimensionar y comprimir
        const optimizedImage = await resizeImage(file, 800, 0.8);
        
        // Subir imagen optimizada
        const response = await fetch(`/api_shopify/index.php?action=uploadProductImage&product_id=${productId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                image_base64: optimizedImage,
                filename: file.name,
                alt_text: 'Imagen optimizada del producto'
            })
        });
        
        return await response.json();
    } catch (error) {
        console.error('Error al optimizar imagen:', error);
    }
}
```

---

## 📌 **Caso 6: Validación de URLs de Imágenes**

### Script para verificar que las URLs sean válidas antes de crear:

```javascript
async function validateImageUrl(url) {
    try {
        const response = await fetch(url, { method: 'HEAD' });
        const contentType = response.headers.get('content-type');
        
        return {
            valid: response.ok && contentType.startsWith('image/'),
            contentType: contentType,
            size: response.headers.get('content-length')
        };
    } catch (error) {
        return { valid: false, error: error.message };
    }
}

async function createProductWithValidatedImages(productData) {
    // Validar todas las URLs de imágenes
    if (productData.images && productData.images.length > 0) {
        console.log('Validando imágenes...');
        
        for (let i = 0; i < productData.images.length; i++) {
            const validation = await validateImageUrl(productData.images[i]);
            
            if (!validation.valid) {
                console.warn(`Imagen ${i + 1} no válida:`, productData.images[i]);
                productData.images.splice(i, 1); // Remover URL inválida
                i--; // Ajustar índice
            } else {
                console.log(`✅ Imagen ${i + 1} válida (${validation.contentType})`);
            }
        }
    }
    
    // Crear producto con imágenes validadas
    const response = await fetch('/api_shopify/index.php?action=createProduct', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(productData)
    });
    
    return await response.json();
}
```

---

## 🚀 **Comandos de Prueba Rápida**

```bash
# Crear producto sin imagen (más rápido)
curl -X POST "http://localhost/api_shopify/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{"title":"Test Product","body_html":"Test","product_type":"Test","price":"1.00"}'

# Agregar imagen por URL
curl -X POST "http://localhost/api_shopify/index.php?action=addProductImage&product_id=PRODUCT_ID" \
  -H "Content-Type: application/json" \
  -d '{"image_url":"https://via.placeholder.com/800x600.jpg","alt_text":"Imagen de prueba"}'

# Obtener productos para verificar
curl "http://localhost/api_shopify/index.php?action=getProducts"
```

---

> 💡 **Tip:** Para desarrollo, usa https://via.placeholder.com/ para generar imágenes de prueba: `https://via.placeholder.com/800x600.jpg?text=Mi+Producto`