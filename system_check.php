<?php

/**
 * Script de Verificación del Sistema
 * Verifica que todos los componentes estén configurados correctamente
 */

// Cargar configuración si está disponible
$configLoaded = false;
try {
    require_once __DIR__ . '/config.php';
    $configLoaded = true;
} catch (Exception $e) {
    // Config no disponible
}

class SystemChecker
{
    private $results = [];
    private $errors = [];
    private $warnings = [];

    public function checkAll()
    {
        $this->checkPHP();
        $this->checkComposer();
        $this->checkEnvironment();
        $this->checkDatabase();
        $this->checkPermissions();
        $this->checkShopifyCredentials();
        $this->generateReport();
    }

    private function checkPHP()
    {
        $this->addSection("🐘 Verificación de PHP");

        // Versión PHP
        $phpVersion = phpversion();
        if (version_compare($phpVersion, '8.0.0', '>=')) {
            $this->addSuccess("PHP Version: $phpVersion ✅");
        } else {
            $this->addError("PHP Version: $phpVersion (se requiere 8.0+) ❌");
        }

        // Extensiones requeridas
        $extensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'mbstring'];
        foreach ($extensions as $ext) {
            if (extension_loaded($ext)) {
                $this->addSuccess("Extensión $ext: Disponible ✅");
            } else {
                $this->addError("Extensión $ext: No disponible ❌");
            }
        }

        // Configuración PHP
        $memory = ini_get('memory_limit');
        $this->addInfo("Memory Limit: $memory");

        $maxExecution = ini_get('max_execution_time');
        $this->addInfo("Max Execution Time: {$maxExecution}s");
    }

    private function checkComposer()
    {
        $this->addSection("📦 Verificación de Composer");

        if (file_exists(__DIR__ . '/vendor/autoload.php')) {
            $this->addSuccess("Autoload de Composer: Disponible ✅");

            // Verificar dependencias específicas
            $composerLock = __DIR__ . '/composer.lock';
            if (file_exists($composerLock)) {
                $lockData = json_decode(file_get_contents($composerLock), true);
                $packages = count($lockData['packages'] ?? []);
                $this->addSuccess("Dependencias instaladas: $packages paquetes ✅");
            }
        } else {
            $this->addError("Autoload de Composer: No encontrado ❌");
            $this->addError("Ejecutar: composer install");
        }
    }

    private function checkEnvironment()
    {
        $this->addSection("🔧 Verificación de Variables de Entorno");

        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $this->addSuccess("Archivo .env: Existe ✅");

            // Verificar variables críticas
            $requiredVars = [
                'DB_HOST',
                'DB_USER',
                'DB_NAME',
                'SHOPIFY_API_KEY',
                'SHOPIFY_TOKEN_ACCESS',
                'SHOPIFY_HOST_NAME'
            ];

            foreach ($requiredVars as $var) {
                $value = env($var, null);
                if ($value !== null && !empty($value)) {
                    $displayValue = (strpos($var, 'PASS') !== false || strpos($var, 'KEY') !== false || strpos($var, 'TOKEN') !== false)
                        ? '***' : $value;
                    $this->addSuccess("$var: Configurado ($displayValue) ✅");
                } else {
                    $this->addWarning("$var: No configurado ⚠️");
                }
            }
        } else {
            $this->addError("Archivo .env: No encontrado ❌");
            $this->addError("Copiar desde .env.example y configurar");
        }
    }

    private function checkDatabase()
    {
        $this->addSection("🗄️ Verificación de Base de Datos");

        if (!$GLOBALS['configLoaded']) {
            $this->addWarning("No se puede verificar BD: Config no cargado ⚠️");
            return;
        }

        try {
            $db = new db();
            $connection = $db->conectar_db();
            $this->addSuccess("Conexión a BD: Exitosa ✅");

            // Verificar tablas
            $stmt = $connection->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $expectedTables = ['productos', 'product_variants', 'api_logs', 'configuraciones', 'discount_codes'];

            foreach ($expectedTables as $table) {
                if (in_array($table, $tables)) {
                    $this->addSuccess("Tabla $table: Existe ✅");
                } else {
                    $this->addWarning("Tabla $table: No encontrada ⚠️");
                }
            }

            $this->addInfo("Total de tablas: " . count($tables));
        } catch (Exception $e) {
            $this->addError("Error de BD: " . $e->getMessage() . " ❌");
        }
    }

    private function checkPermissions()
    {
        $this->addSection("🔐 Verificación de Permisos");

        $directories = [
            __DIR__ . '/vendor',
            __DIR__ . '/database',
            __DIR__ . '/APIS',
            __DIR__ . '/modelo'
        ];

        foreach ($directories as $dir) {
            if (is_readable($dir)) {
                $this->addSuccess("Lectura $dir: OK ✅");
            } else {
                $this->addError("Lectura $dir: Sin acceso ❌");
            }
        }

        // Verificar archivos críticos
        $files = [
            __DIR__ . '/index.php',
            __DIR__ . '/config.php',
            __DIR__ . '/DB.php'
        ];

        foreach ($files as $file) {
            if (is_readable($file)) {
                $this->addSuccess("Archivo " . basename($file) . ": Legible ✅");
            } else {
                $this->addError("Archivo " . basename($file) . ": No legible ❌");
            }
        }
    }

    private function checkShopifyCredentials()
    {
        $this->addSection("🛍️ Verificación de Credenciales Shopify");

        if (!$GLOBALS['configLoaded']) {
            $this->addWarning("No se pueden verificar credenciales: Config no cargado ⚠️");
            return;
        }

        $apiKey = env('SHOPIFY_API_KEY');
        $token = env('SHOPIFY_TOKEN_ACCESS');
        $hostname = env('SHOPIFY_HOST_NAME');

        if ($apiKey && $token && $hostname) {
            $this->addSuccess("Credenciales Shopify: Configuradas ✅");

            // Verificar formato del hostname
            if (strpos($hostname, '.myshopify.com') !== false) {
                $this->addSuccess("Hostname Shopify: Formato válido ✅");
            } else {
                $this->addWarning("Hostname Shopify: Formato inusual ⚠️");
            }

            // Test de conectividad (opcional - comentado para evitar rate limits)
            // $this->testShopifyConnection($apiKey, $token, $hostname);

        } else {
            $this->addWarning("Credenciales Shopify: Incompletas ⚠️");
        }
    }

    private function addSection($title)
    {
        $this->results[] = ['type' => 'section', 'message' => $title];
    }

    private function addSuccess($message)
    {
        $this->results[] = ['type' => 'success', 'message' => $message];
    }

    private function addError($message)
    {
        $this->results[] = ['type' => 'error', 'message' => $message];
        $this->errors[] = $message;
    }

    private function addWarning($message)
    {
        $this->results[] = ['type' => 'warning', 'message' => $message];
        $this->warnings[] = $message;
    }

    private function addInfo($message)
    {
        $this->results[] = ['type' => 'info', 'message' => $message];
    }

    private function generateReport()
    {
        echo "<div class='report-container'>";
        echo "<h1>📋 Reporte de Verificación del Sistema</h1>";
        echo "<div class='timestamp'>Generado: " . date('Y-m-d H:i:s') . "</div>";

        // Resumen
        $totalErrors = count($this->errors);
        $totalWarnings = count($this->warnings);

        echo "<div class='summary'>";
        echo "<h2>📊 Resumen</h2>";
        if ($totalErrors == 0) {
            echo "<div class='summary-success'>✅ Sistema configurado correctamente</div>";
        } else {
            echo "<div class='summary-error'>❌ Se encontraron $totalErrors errores críticos</div>";
        }

        if ($totalWarnings > 0) {
            echo "<div class='summary-warning'>⚠️ $totalWarnings advertencias encontradas</div>";
        }
        echo "</div>";

        // Resultados detallados
        echo "<div class='details'>";
        foreach ($this->results as $result) {
            $class = 'result-' . $result['type'];
            echo "<div class='$class'>{$result['message']}</div>";
        }
        echo "</div>";

        // Recomendaciones
        if ($totalErrors > 0 || $totalWarnings > 0) {
            echo "<div class='recommendations'>";
            echo "<h2>🔧 Recomendaciones</h2>";

            if ($totalErrors > 0) {
                echo "<h3>Errores críticos a resolver:</h3>";
                echo "<ul>";
                foreach ($this->errors as $error) {
                    echo "<li>$error</li>";
                }
                echo "</ul>";
            }

            if ($totalWarnings > 0) {
                echo "<h3>Advertencias a revisar:</h3>";
                echo "<ul>";
                foreach ($this->warnings as $warning) {
                    echo "<li>$warning</li>";
                }
                echo "</ul>";
            }
            echo "</div>";
        }

        echo "</div>";
    }
}

// Ejecutar verificación si se accede directamente
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>System Check - API Shopify</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 20px;
                background: #f5f7fa;
            }

            .report-container {
                max-width: 900px;
                margin: 0 auto;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            h1 {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                margin: 0;
                padding: 30px;
                text-align: center;
            }

            .timestamp {
                text-align: center;
                color: #666;
                margin: 20px 0;
                font-size: 14px;
            }

            .summary {
                background: #f8f9fa;
                padding: 25px;
                border-bottom: 1px solid #dee2e6;
            }

            .summary h2 {
                margin-top: 0;
                color: #495057;
            }

            .summary-success {
                background: #d4edda;
                color: #155724;
                padding: 15px;
                border-radius: 8px;
                border: 1px solid #c3e6cb;
                margin: 10px 0;
            }

            .summary-error {
                background: #f8d7da;
                color: #721c24;
                padding: 15px;
                border-radius: 8px;
                border: 1px solid #f5c6cb;
                margin: 10px 0;
            }

            .summary-warning {
                background: #fff3cd;
                color: #856404;
                padding: 15px;
                border-radius: 8px;
                border: 1px solid #ffeaa7;
                margin: 10px 0;
            }

            .details {
                padding: 25px;
            }

            .result-section {
                font-size: 18px;
                font-weight: bold;
                color: #343a40;
                margin: 25px 0 15px 0;
                padding: 10px 0;
                border-bottom: 2px solid #007bff;
            }

            .result-success {
                color: #28a745;
                padding: 8px 15px;
                margin: 5px 0;
                background: #f8fff9;
                border-left: 4px solid #28a745;
            }

            .result-error {
                color: #dc3545;
                padding: 8px 15px;
                margin: 5px 0;
                background: #fff8f8;
                border-left: 4px solid #dc3545;
            }

            .result-warning {
                color: #ffc107;
                padding: 8px 15px;
                margin: 5px 0;
                background: #fffdf5;
                border-left: 4px solid #ffc107;
            }

            .result-info {
                color: #17a2b8;
                padding: 8px 15px;
                margin: 5px 0;
                background: #f8fcfd;
                border-left: 4px solid #17a2b8;
            }

            .recommendations {
                background: #f8f9fa;
                padding: 25px;
                margin-top: 20px;
                border-top: 1px solid #dee2e6;
            }

            .recommendations h2,
            .recommendations h3 {
                color: #495057;
            }

            .recommendations ul {
                padding-left: 20px;
            }

            .recommendations li {
                margin: 8px 0;
            }
        </style>
    </head>

    <body>
        <?php
        $checker = new SystemChecker();
        $checker->checkAll();
        ?>
    </body>

    </html>
<?php
}
?>