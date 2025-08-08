<?php
// Récupération des paramètres de connexion à la base de données
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'examotheque';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASSWORD') ?: 'password';

// Construction du DSN
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    die("Erreur de connexion à la base de données.");
}

// Vérification de l'id dans l'URL
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    http_response_code(400);
    die("Identifiant de fichier invalide.");
}

$id = (int)$_GET['id'];

// Requête pour récupérer le fichier
$sql = "SELECT nom_epreuve, fichier_path FROM download_epreuve WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    http_response_code(404);
    die("Fichier non trouvé.");
}

// Répertoire uploads (chemin absolu serveur)
$uploadDir = '/var/www/html/admin/uploads/';

// Résolution du chemin complet sécurisé
$filepath = realpath($uploadDir . basename($file['fichier_path']));

if (
    !$filepath ||
    strpos($filepath, realpath($uploadDir)) !== 0 ||
    !is_file($filepath)
) {
    http_response_code(404);
    die("Le fichier est introuvable sur le serveur.");
}

// Nom pour le téléchargement (ou affichage)
$downloadName = $file['nom_epreuve'] ?: basename($filepath);

// Détection automatique du type MIME
$mimeType = mime_content_type($filepath);

// Envoi des headers adaptés
header('Content-Description: File Transfer');
header('Content-Type: ' . $mimeType);

// Affichage inline pour PDF sinon attachment pour les autres fichiers
if ($mimeType === 'application/pdf') {
    header('Content-Disposition: inline; filename="' . basename($downloadName) . '"');
} else {
    header('Content-Disposition: attachment; filename="' . basename($downloadName) . '"');
}

header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

// Envoi du fichier au client
readfile($filepath);
exit;
?>
