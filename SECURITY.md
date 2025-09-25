# Guía de Seguridad - API Shopify

## Cambios de Seguridad Implementados

### 1. Variables de Entorno
Se han movido todas las credenciales sensibles del código fuente a un archivo `.env`:

**Antes (INSEGURO):**
```php
public $API_KEY = "your_api_key_here";
public $TOKEN_ACCESS = "shpat_your_token_here";
```

**Después (SEGURO):**
```php
private $API_KEY;
private $TOKEN_ACCESS;

public function __construct() {
    $this->API_KEY = env('SHOPIFY_API_KEY');
    $this->TOKEN_ACCESS = env('SHOPIFY_TOKEN_ACCESS');
}
```

### 2. Archivos Protegidos

**Archivo `.env`**: Contiene todas las credenciales sensibles
- ✅ Incluido en `.gitignore`
- ✅ No se sube al repositorio
- ✅ Accesible solo desde el servidor

**Archivo `.env.example`**: Plantilla sin credenciales reales
- ✅ Se puede subir al repositorio
- ✅ Guía para nuevas instalaciones

### 3. Variables Migradas

| Variable Original | Variable de Entorno | Descripción |
|-------------------|-------------------|-------------|
| `$API_KEY` (MetodosAPI) | `SHOPIFY_API_KEY` | API Key Shopify principal |
| `$TOKEN_ACCESS` | `SHOPIFY_TOKEN_ACCESS` | Token de acceso Shopify |
| `$HOST_NAME` | `SHOPIFY_HOST_NAME` | Nombre del host Shopify |
| `$API_KEY` (LoginAPI) | `SHOPIFY_LOGIN_API_KEY` | API Key para login |
| `$PASSWORD` | `SHOPIFY_LOGIN_PASSWORD` | Password para login |
| `$db_host` | `DB_HOST` | Host de base de datos |
| `$db_user` | `DB_USER` | Usuario de base de datos |
| `$db_pass` | `DB_PASS` | Contraseña de base de datos |
| `$db_name` | `DB_NAME` | Nombre de base de datos |
| APIKEY (header) | `GENERAL_API_KEY` | API Key general |

### 4. Configuración Recomendada

#### Producción:
1. **Usar HTTPS**: Todas las comunicaciones deben ser encriptadas
2. **Validar entradas**: Sanitizar todos los datos de entrada
3. **Logs seguros**: No registrar credenciales en logs
4. **Rotación de claves**: Cambiar periódicamente las API keys
5. **Monitoreo**: Vigilar accesos no autorizados

#### Desarrollo:
1. **Archivo .env local**: Cada desarrollador tiene su propio .env
2. **Credenciales de desarrollo**: No usar credenciales de producción
3. **Control de versiones**: Nunca commitear el archivo .env

### 5. Comandos de Configuración

```bash
# Copiar plantilla de variables
cp .env.example .env

# Editar con tus credenciales
nano .env  # o tu editor preferido

# Verificar que .env está en .gitignore
cat .gitignore | grep .env
```

### 6. Solución de Problemas

**Error: "Call to undefined function env()"**
- Verificar que `config.php` esté incluido
- Comprobar que Composer autoload esté cargado

**Error: Variables de entorno vacías**
- Verificar que el archivo `.env` existe
- Comprobar permisos de lectura del archivo
- Validar sintaxis del archivo .env

### 7. Checklist de Seguridad

- [ ] Archivo `.env` creado y configurado
- [ ] `.env` incluido en `.gitignore`
- [ ] Credenciales removidas del código fuente
- [ ] Variables convertidas de public a private
- [ ] Constructores actualizados para usar env()
- [ ] Archivo `.env.example` creado
- [ ] README.md actualizado con instrucciones
- [ ] Pruebas de sintaxis PHP exitosas

## Mantenimiento

### Rotación de Credenciales
1. Generar nuevas credenciales en Shopify
2. Actualizar archivo `.env`
3. Probar conexiones
4. Revocar credenciales antiguas

### Respaldo de Configuración
- **NO** respaldar el archivo `.env` en repositorios públicos
- Usar gestores de secretos en producción (AWS Secrets Manager, Azure Key Vault, etc.)
- Mantener documentación de configuración actualizada