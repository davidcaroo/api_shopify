<?php

/**
 * Instalador de Base de Datos - API Shopify
 * 
 * Este script configura la base de datos necesaria para el funcionamiento
 * de la API Shopify CRUD.
 */

// Cargar configuración
require_once __DIR__ . '/../config.php';

class DatabaseInstaller
{
    private $host;
    private $user;
    private $password;
    private $database;
    private $connection;

    public function __construct()
    {
        $this->host = env('DB_HOST', '127.0.0.1');
        $this->user = env('DB_USER', 'root');
        $this->password = env('DB_PASS', '');
        $this->database = env('DB_NAME', 'shopify_api_db');
    }

    /**
     * Conectar a MySQL (sin especificar base de datos)
     */
    private function connectToMySQL()
    {
        try {
            $dsn = "mysql:host={$this->host}";
            $this->connection = new PDO($dsn, $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->exec("SET NAMES utf8mb4");
            return true;
        } catch (PDOException $e) {
            $this->showError("Error de conexión a MySQL: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si la base de datos existe
     */
    private function databaseExists()
    {
        try {
            $stmt = $this->connection->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
            $stmt->execute([$this->database]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Ejecutar el script SQL
     */
    private function executeSQLFile($filepath)
    {
        if (!file_exists($filepath)) {
            $this->showError("Archivo SQL no encontrado: $filepath");
            return false;
        }

        $sql = file_get_contents($filepath);

        // Dividir el script en statements individuales
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        $success = 0;
        $errors = 0;

        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }

            try {
                $this->connection->exec($statement);
                $success++;
            } catch (PDOException $e) {
                $errors++;
                echo "<div class='error'>Error ejecutando statement: " . $e->getMessage() . "</div>";
            }
        }

        return ['success' => $success, 'errors' => $errors];
    }

    /**
     * Probar la conexión a la base de datos creada
     */
    private function testDatabaseConnection()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database}";
            $testConnection = new PDO($dsn, $this->user, $this->password);
            $testConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Probar una consulta simple
            $stmt = $testConnection->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = '{$this->database}'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['table_count'];
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Mostrar error
     */
    private function showError($message)
    {
        echo "<div class='error'>❌ $message</div>";
    }

    /**
     * Mostrar éxito
     */
    private function showSuccess($message)
    {
        echo "<div class='success'>✅ $message</div>";
    }

    /**
     * Proceso principal de instalación
     */
    public function install()
    {
        echo "<h2>🔧 Instalador de Base de Datos - API Shopify</h2>";

        // Verificar archivo .env
        if (!file_exists(__DIR__ . '/../.env')) {
            $this->showError("Archivo .env no encontrado. Por favor configúralo primero.");
            return false;
        }

        echo "<div class='info'>📋 Configuración detectada:</div>";
        echo "<ul>";
        echo "<li><strong>Host:</strong> {$this->host}</li>";
        echo "<li><strong>Usuario:</strong> {$this->user}</li>";
        echo "<li><strong>Base de datos:</strong> {$this->database}</li>";
        echo "</ul>";

        // Conectar a MySQL
        echo "<div class='step'>🔌 Conectando a MySQL...</div>";
        if (!$this->connectToMySQL()) {
            return false;
        }
        $this->showSuccess("Conexión a MySQL establecida");

        // Verificar si la base de datos ya existe
        if ($this->databaseExists()) {
            $this->showSuccess("Base de datos '{$this->database}' ya existe");
        }

        // Ejecutar script SQL
        echo "<div class='step'>📄 Ejecutando script de creación...</div>";
        $sqlFile = __DIR__ . '/create_database.sql';
        $result = $this->executeSQLFile($sqlFile);

        if ($result) {
            $this->showSuccess("Script ejecutado: {$result['success']} statements exitosos, {$result['errors']} errores");
        }

        // Probar conexión final
        echo "<div class='step'>🧪 Probando conexión a la base de datos...</div>";
        $tableCount = $this->testDatabaseConnection();

        if ($tableCount !== false) {
            $this->showSuccess("Conexión exitosa. Base de datos configurada con $tableCount tablas");
            echo "<div class='final-success'>🎉 ¡Instalación completada exitosamente!</div>";
            return true;
        } else {
            $this->showError("No se pudo conectar a la base de datos creada");
            return false;
        }
    }
}

// Solo ejecutar si se accede directamente
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instalador de Base de Datos - API Shopify</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background-color: #f5f5f5;
            }

            .container {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .error {
                background: #ffebee;
                color: #c62828;
                padding: 10px;
                border-radius: 5px;
                margin: 10px 0;
                border-left: 4px solid #c62828;
            }

            .success {
                background: #e8f5e8;
                color: #2e7d32;
                padding: 10px;
                border-radius: 5px;
                margin: 10px 0;
                border-left: 4px solid #2e7d32;
            }

            .info {
                background: #e3f2fd;
                color: #1565c0;
                padding: 10px;
                border-radius: 5px;
                margin: 10px 0;
                border-left: 4px solid #1565c0;
            }

            .step {
                background: #fff3e0;
                color: #ef6c00;
                padding: 10px;
                border-radius: 5px;
                margin: 10px 0;
                border-left: 4px solid #ef6c00;
            }

            .final-success {
                background: #e8f5e8;
                color: #2e7d32;
                padding: 20px;
                border-radius: 10px;
                margin: 20px 0;
                text-align: center;
                font-size: 18px;
                font-weight: bold;
                border: 2px solid #2e7d32;
            }

            ul {
                margin: 10px 0;
            }

            li {
                margin: 5px 0;
            }

            h2 {
                color: #333;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <?php
            $installer = new DatabaseInstaller();
            $installer->install();
            ?>
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                <h3>📋 Próximos pasos:</h3>
                <ol>
                    <li>Verifica que la instalación fue exitosa</li>
                    <li>Configura tu archivo <code>.env</code> con los datos correctos</li>
                    <li>Prueba la API con los endpoints documentados</li>
                    <li>Revisa los logs en la tabla <code>api_logs</code></li>
                </ol>
            </div>
        </div>
    </body>

    </html>
<?php
}
?>