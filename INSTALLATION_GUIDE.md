# 🚀 Guía de Instalación y Configuración - API Shopify CRUD

## 📋 Requisitos Previos

### Software Necesario:
- ✅ **XAMPP** (Apache + MySQL + PHP)
- ✅ **Composer** (para dependencias PHP)
- ✅ **Editor de código** (VS Code, PHPStorm, etc.)

### Versiones Recomendadas:
- **PHP**: 8.0 o superior
- **MySQL**: 5.7 o superior
- **Apache**: 2.4 o superior

---

## 🔧 Instalación Paso a Paso

### 1. **Configurar XAMPP**

#### Instalar XAMPP:
1. Descargar XAMPP desde: https://www.apachefriends.org/
2. Instalar siguiendo el asistente
3. Iniciar el panel de control de XAMPP

#### Configurar servicios:
1. **Iniciar Apache** ✅
2. **Iniciar MySQL** ✅
3. Verificar que funcionen correctamente

```bash
# URLs de verificación:
http://localhost/          # Apache funcionando
http://localhost/phpmyadmin # MySQL funcionando
```

### 2. **Ubicar el Proyecto**

#### Mover carpeta a XAMPP:
```bash
# Mover la carpeta del proyecto a:
C:\xampp\htdocs\API_SHOPIFY\

# O crear un enlace simbólico:
mklink /D "C:\xampp\htdocs\API_SHOPIFY" "C:\Users\Ingca\Downloads\API_SHOPIFY"
```

#### Verificar ubicación:
- ✅ Proyecto en: `C:\xampp\htdocs\API_SHOPIFY\`
- ✅ Accesible en: `http://localhost/API_SHOPIFY/`

### 3. **Instalar Dependencias**

#### Usar Composer:
```bash
# Abrir terminal en la carpeta del proyecto
cd C:\xampp\htdocs\API_SHOPIFY

# Instalar dependencias
composer install
```

#### Verificar instalación:
```bash
# Verificar que vendor/ existe
dir vendor\

# Probar autoload
php -r "require 'vendor/autoload.php'; echo 'Dependencias OK!';"
```

### 4. **Configurar Variables de Entorno**

#### Copiar archivo de configuración:
```bash
copy .env.example .env
```

#### Editar archivo `.env`:
```env
# Configuración de Base de Datos (XAMPP por defecto)
DB_HOST=127.0.0.1
DB_USER=root
DB_PASS=
DB_NAME=shopify_api_db

# Configuración Shopify - REEMPLAZAR CON TUS CREDENCIALES
SHOPIFY_API_KEY=tu_api_key_real
SHOPIFY_TOKEN_ACCESS=tu_token_real
SHOPIFY_HOST_NAME=tu_tienda.myshopify.com

# API Key General - REEMPLAZAR
GENERAL_API_KEY=tu_api_key_general
```

### 5. **Crear Base de Datos**

#### Opción A: Usando el instalador web
1. Navegar a: `http://localhost/API_SHOPIFY/database/install.php`
2. Seguir las instrucciones en pantalla
3. Verificar que la instalación sea exitosa

#### Opción B: Usando phpMyAdmin
1. Ir a: `http://localhost/phpmyadmin`
2. Crear nueva base de datos: `shopify_api_db`
3. Importar archivo: `database/create_database.sql`

#### Opción C: Usando línea de comandos
```bash
# Acceder a MySQL
mysql -u root -p

# Ejecutar script
source C:\xampp\htdocs\API_SHOPIFY\database\create_database.sql
```

### 6. **Verificar Configuración**

#### Probar PHP:
```bash
# Verificar sintaxis
php -l index.php

# Probar configuración
php -r "require 'config.php'; echo 'Config OK!';"
```

#### Probar conexión a BD:
```bash
php -r "
require 'config.php';
require 'DB.php';
$db = new db();
$conn = $db->conectar_db();
echo 'DB Connection OK!';
"
```

---

## 🧪 Probar la API

### Endpoints de prueba:

#### 1. Verificar que la API responda:
```bash
curl http://localhost/API_SHOPIFY/index.php?action=getAllProducts
```

#### 2. Crear un producto de prueba:
```bash
curl -X POST "http://localhost/API_SHOPIFY/index.php?action=createProduct" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Producto de Prueba",
    "body_html": "<p>Producto creado desde la API</p>",
    "product_type": "Test"
  }'
```

#### 3. Verificar logs:
- Ir a phpMyAdmin
- Revisar tabla `api_logs`
- Verificar que se registren las peticiones

---

## 📁 Estructura Final del Proyecto

```
C:\xampp\htdocs\API_SHOPIFY\
├── 📁 APIS/
│   ├── MetodosAPI.php      # API principal CRUD
│   └── LoginAPI.php        # API secundaria
├── 📁 database/
│   ├── create_database.sql # Script de BD
│   └── install.php         # Instalador web
├── 📁 modelo/
│   ├── metodos_database.php
│   └── metodos_generales.php
├── 📁 vendor/              # Dependencias Composer
├── .env                    # Variables de entorno
├── .env.example           # Plantilla de variables
├── config.php             # Configuración principal
├── DB.php                 # Conexión a BD
├── index.php              # Punto de entrada
└── composer.json          # Dependencias
```

---

## 🔍 Solución de Problemas

### Error: "Call to undefined function env()"
```bash
# Verificar que config.php esté incluido
# Verificar que vendor/autoload.php exista
composer install
```

### Error: "Access denied for user 'root'"
```bash
# En XAMPP, MySQL por defecto no tiene contraseña
# Verificar configuración en .env:
DB_PASS=
```

### Error: "Database connection failed"
```bash
# Verificar que MySQL esté iniciado en XAMPP
# Verificar nombre de base de datos:
DB_NAME=shopify_api_db
```

### Error: "Headers already sent"
```bash
# Verificar que no haya espacios o caracteres antes de <?php
# Verificar codificación UTF-8 sin BOM
```

### Error: "Composer not found"
```bash
# Instalar Composer desde: https://getcomposer.org/
# O usar desde XAMPP:
C:\xampp\php\php.exe composer.phar install
```

---

## 🎯 URLs Importantes

### Desarrollo:
- **API Base**: `http://localhost/API_SHOPIFY/index.php`
- **Instalador BD**: `http://localhost/API_SHOPIFY/database/install.php`
- **phpMyAdmin**: `http://localhost/phpmyadmin`
- **XAMPP Control**: `http://localhost/xampp`

### Documentación:
- **API Completa**: `API_DOCUMENTATION.md`
- **Guía de Pruebas**: `TESTING_GUIDE.md`
- **Seguridad**: `SECURITY.md`

---

## ✅ Checklist de Configuración

- [ ] XAMPP instalado y funcionando
- [ ] Apache iniciado ✅
- [ ] MySQL iniciado ✅
- [ ] Proyecto en `C:\xampp\htdocs\API_SHOPIFY\`
- [ ] Dependencias instaladas (`composer install`)
- [ ] Archivo `.env` configurado
- [ ] Base de datos creada (`shopify_api_db`)
- [ ] Tablas creadas (5 tablas)
- [ ] Conexión a BD funcionando
- [ ] API respondiendo correctamente
- [ ] Credenciales de Shopify configuradas

---

## 🚀 ¡Listo para Usar!

Una vez completados todos los pasos, tu API estará completamente funcional en:

```
http://localhost/API_SHOPIFY/index.php
```

**¡Ahora puedes empezar a desarrollar con tu API CRUD completa! 🎉**