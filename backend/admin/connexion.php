<?php
session_start();

// Récupérer les infos DB depuis les variables d'environnement Docker
$host = getenv('DB_HOST') ?: 'db';        // le nom du service Docker db
$dbname = getenv('DB_NAME') ?: 'examotheque';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASSWORD') ?: 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur, rediriger vers la page de connexion (ou afficher un message)
    header("Location: http://localhost:3000/admin/connexion.html");
    exit();
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    die("Veuillez remplir tous les champs.");
}

$stmt = $pdo->prepare("SELECT * FROM utilisateur_etudiants WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['mot_de_passe'])) {
    $_SESSION['utilisateur_id'] = $user['id'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];
    $_SESSION['email'] = $user['email'];

    header("Location: accueil.php");
    exit();
} else {
    header("Location: http://localhost:3000/connexion_etu.html?error=1");
    exit();
}
?>
