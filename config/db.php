<?php
declare(strict_types=1);

return [
    'dsn'  => sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        getenv('DB_HOST') ?: '127.0.0.1',
        getenv('DB_PORT') ?: '3306',
        getenv('DB_NAME') ?: 'gestion_etudiants_pdo'
    ),

    'user' => getenv('DB_USER') ?: 'root',
    'pass' => getenv('DB_PASS') ?: '1234',

    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // afficher erreurs
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch en tableau assoc
        PDO::ATTR_EMULATE_PREPARES   => false,                   // vrai prepare mysql
    ],
];