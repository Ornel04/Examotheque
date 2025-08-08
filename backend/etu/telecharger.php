<?php
session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    header('HTTP/1.1 403 Forbidden');
    echo "Accès refusé. Veuillez vous connecter.";
    exit();
}

if (!isset($_GET['id'])) {
    die("Identifiant d'épreuve manquant.");
}

$id = intval($_GET['id']);

$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'examotheque';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASSWORD') ?: 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT nom_epreuve, fichier_path FROM download_epreuve WHERE id = ?");
$stmt->execute([$id]);
$epreuve = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$epreuve) {
    die("Épreuve introuvable.");
}

$filepath = __DIR__ . '/' . $epreuve['fichier_path'];

if (!file_exists($filepath)) {
    die("Fichier introuvable.");
}

$filename = basename($filepath);
$filesize = filesize($filepath);

// Envoi des headers pour forcer le téléchargement
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . $filesize);

readfile($filepath);
exit();
