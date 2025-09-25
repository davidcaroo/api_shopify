# Contributing to Shopify API CRUD

¡Gracias por tu interés en contribuir a este proyecto! 🎉

## 🚀 Cómo contribuir

### 1. Fork y Clone
```bash
# Fork el repositorio en GitHub
# Luego clona tu fork:
git clone https://github.com/tu-usuario/api_shopify.git
cd api_shopify
```

### 2. Instalar Dependencias
```bash
composer install
```

### 3. Configurar Entorno
```bash
cp .env.example .env
# Editar .env con tus credenciales de desarrollo
```

### 4. Crear Rama para tu Feature
```bash
git checkout -b feature/nombre-de-tu-feature
```

### 5. Realizar Cambios
- Mantén el código limpio y bien documentado
- Sigue las convenciones de codificación PHP
- Agrega tests si es necesario
- Actualiza la documentación si es relevante

### 6. Probar Cambios
```bash
# Verificar sintaxis
php -l archivo.php

# Probar API
curl -X GET "http://localhost/api_shopify/index.php?action=getAllProducts"
```

### 7. Commit y Push
```bash
git add .
git commit -m "✨ feat: descripción de tu cambio"
git push origin feature/nombre-de-tu-feature
```

### 8. Crear Pull Request
- Ve a GitHub y crea un Pull Request
- Describe claramente los cambios realizados
- Incluye capturas de pantalla si es relevante

## 📋 Convenciones

### Commits
Usamos [Conventional Commits](https://www.conventionalcommits.org/):
- `feat:` nueva funcionalidad
- `fix:` corrección de bug
- `docs:` cambios en documentación
- `style:` cambios de formato
- `refactor:` refactorización de código
- `test:` agregar o modificar tests
- `chore:` tareas de mantenimiento

### Código PHP
- Usar PSR-4 para autoloading
- Seguir PSR-12 para estilo de código
- Validar todas las entradas de usuario
- Manejar errores apropiadamente
- Documentar funciones complejas

### Base de Datos
- Usar prepared statements (PDO)
- Nombrar tablas en singular
- Usar índices apropiados
- Documentar cambios de esquema

## 🐛 Reportar Bugs

### Antes de reportar:
1. Busca si el bug ya fue reportado
2. Verifica que estés usando la última versión
3. Reproduce el bug en un entorno limpio

### Al reportar incluye:
- Versión de PHP
- Versión del proyecto
- Pasos para reproducir
- Comportamiento esperado vs actual
- Screenshots o logs si es relevante

## 💡 Sugerir Mejoras

¿Tienes ideas para mejorar el proyecto? ¡Nos encantaría escucharlas!

1. Abre un Issue describiendo tu idea
2. Explica el problema que resuelve
3. Propón una solución
4. Discute con la comunidad

## 🔒 Reporte de Vulnerabilidades

Si encuentras una vulnerabilidad de seguridad:

**NO** la reportes públicamente. En su lugar:
1. Envía un email a: security@tu-dominio.com
2. Incluye una descripción detallada
3. Espera nuestra respuesta antes de divulgar

## 📚 Recursos Útiles

- [Shopify API Documentation](https://shopify.dev/api)
- [PHP Documentation](https://www.php.net/docs.php)
- [Composer Documentation](https://getcomposer.org/doc/)
- [Git Flow](https://nvie.com/posts/a-successful-git-branching-model/)

## 🏆 Reconocimientos

Todos los contribuidores serán reconocidos en el README del proyecto.

¡Gracias por ayudar a mejorar este proyecto! 🙏