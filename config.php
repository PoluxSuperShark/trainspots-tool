<?php

session_start(); // Démarrer la session

// Bases de données
$host = "localhost";
$dbname = "trainspots";
$user = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $user,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Créer la base de données
    $pdo->exec("
        CREATE DATABASE IF NOT EXISTS $dbname
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_unicode_ci
    ");

    $pdo->exec("USE $dbname");

    // Pré-exécution du SQL pour insérer dans la BDD
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS entries (
            id INT AUTO_INCREMENT PRIMARY KEY,

            type ENUM(
                'spots',
                'voyages',
                'departs',
                'arrives',
                'departs_arrives',
                'compilations',
                'documentaires',
                'passages'
            ) NOT NULL,

            titre VARCHAR(255) NOT NULL,
            description TEXT,

            statut ENUM('en_attente','valide') DEFAULT 'en_attente',

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

} catch(PDOException $e) {
    die("Erreur SQL : " . $e->getMessage()); // Erreur
}
