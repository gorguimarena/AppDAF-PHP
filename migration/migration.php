<?php

$host = 'dpg-d1vaoq95pdvs73d1uadg-a.oregon-postgres.render.com';
$pass = '2hBgej82rgFmwVYtxO0VaxKOQGjwJ3b9';

$driver = 'pgsql';
$port = '5432';
$user = 'appdaf_user';
$dbName = 'appdaf';


try {
    // 1. Connexion directe à la base déjà créée par Render
    $dsn = "$driver:host=$host;port=$port;dbname=$dbName";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Création des tables
    $tables = [
        "CREATE TABLE IF NOT EXISTS citoyen (
            id SERIAL PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            date_naissance DATE NOT NULL,
            lieu_naissance VARCHAR(150) NOT NULL,
            cni VARCHAR(20) UNIQUE NOT NULL,
            cni_recto_url TEXT NOT NULL,
            cni_verso_url TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE INDEX IF NOT EXISTS idx_citoyen_cni ON citoyen(cni);",
        "CREATE TABLE IF NOT EXISTS log (
            id SERIAL PRIMARY KEY,
            date DATE NOT NULL,
            heure TIME NOT NULL,
            localisation VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            statut VARCHAR(10) CHECK (statut IN ('SUCCES', 'ERROR')),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );"
    ];

    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }

    echo "✅ Tables `citoyen` et `log` créées avec succès.\n";

} catch (PDOException $e) {
    echo "❌ Erreur PDO : " . $e->getMessage() . "\n";
}