<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

$cloud = require __DIR__ . '/../app/config/cloudinary.php';

Configuration::instance([
    'cloud' => [
        'cloud_name' => $cloud['cloud_name'],
        'api_key'    => $cloud['api_key'],
        'api_secret' => $cloud['api_secret'],
    ],
    'url' => ['secure' => true]
]);

$cloudinary = new Cloudinary(Configuration::instance());

$password ='2hBgej82rgFmwVYtxO0VaxKOQGjwJ3b9';
$host = 'dpg-d1vaoq95pdvs73d1uadg-a.oregon-postgres.render.com';
$port = '5432';
$driver = 'pgsql';
$dbName = 'appdaf';
$user = 'appdaf_user';

$dsn = "$driver:host=$host;port=$port;dbname=$dbName";


try {
    echo "🔗 Connexion à la base...\n";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie à la base de données\n\n";

    $pdo->exec("DELETE FROM log;");
    $pdo->exec("DELETE FROM citoyen;");
    echo "♻️  Tables `citoyen` et `log` vidées avec succès.\n\n";

    $citoyens = [
        [
            'nom' => 'Gueye',
            'prenom' => 'Ramatoulaye',
            'date_naissance' => '1995-01-02',
            'lieu_naissance' => 'Dakar',
            'cni' => 'CNI1090',
            'recto' => 'cni_recto_url.png',
            'verso' => 'cni_verso_url.png'
        ],
        [
            'nom' => 'Ndour',
            'prenom' => 'Moussa',
            'date_naissance' => '1998-05-11',
            'lieu_naissance' => 'Thiès',
            'cni' => 'CNI1002',
            'recto' => 'cni_recto_url.png',
            'verso' => 'cni_verso_url.png'
        ],
        [
            'nom' => 'Fall',
            'prenom' => 'Cheikh',
            'date_naissance' => '1990-01-15',
            'lieu_naissance' => 'Saint-Louis',
            'cni' => 'CNI1003',
            'recto' => 'cni_recto_url.png',
            'verso' => 'cni_verso_url.png'
        ],
    ];

    foreach ($citoyens as $citoyen) {
        try {
            $pathRecto = __DIR__ . '/images/' . $citoyen['recto'];
            $pathVerso = __DIR__ . '/images/' . $citoyen['verso'];

            echo "📤 Upload recto pour {$citoyen['nom']}...\n";
            $urlRecto = $cloudinary->uploadApi()->upload($pathRecto, ['folder' => 'cni/recto'])['secure_url'];

            echo "📤 Upload verso pour {$citoyen['nom']}...\n";
            $urlVerso = $cloudinary->uploadApi()->upload($pathVerso, ['folder' => 'cni/verso'])['secure_url'];

            $stmt = $pdo->prepare("
                INSERT INTO citoyen (nom, prenom, date_naissance, lieu_naissance, cni, cni_recto_url, cni_verso_url)
                VALUES (:nom, :prenom, :date_naissance, :lieu_naissance, :cni, :cni_recto_url, :cni_verso_url)
            ");

            $stmt->execute([
                'nom' => $citoyen['nom'],
                'prenom' => $citoyen['prenom'],
                'date_naissance' => $citoyen['date_naissance'],
                'lieu_naissance' => $citoyen['lieu_naissance'],
                'cni' => $citoyen['cni'],
                'cni_recto_url' => $urlRecto,
                'cni_verso_url' => $urlVerso
            ]);

            echo "✅ {$citoyen['nom']} inséré avec succès.\n\n";
        } catch (Exception $e) {
            echo "❌ Erreur lors de l'insertion de {$citoyen['nom']} : " . $e->getMessage() . "\n";
        }
    }

    // Insertion logs
    $pdo->exec("
        INSERT INTO log (date, heure, localisation, ip_address, statut) VALUES
        ('2025-07-21', '14:30:00', 'Dakar - Plateau', '192.168.1.10', 'SUCCES'),
        ('2025-07-21', '15:45:12', 'Thiès - Grand Standing', '192.168.1.11', 'ERROR'),
        ('2025-07-20', '09:15:05', 'Saint-Louis - Centre-ville', '10.0.0.1', 'SUCCES');
    ");

    echo "✅ Logs insérés avec succès.\n";
    echo "🎉 Seeder terminé.\n";

} catch (PDOException $e) {
    echo "❌ Erreur PDO : " . $e->getMessage() . "\n";
}