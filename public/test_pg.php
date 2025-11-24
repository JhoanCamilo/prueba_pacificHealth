//? Este archivo es para probar la conexión a la base de datos antes.
<?php

try {
    $pdo = new PDO(
        "pgsql:host=localhost;port=5432;dbname=Pacific_Test",
        "postgres",
        "root",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    echo "Conexión exitosa!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
