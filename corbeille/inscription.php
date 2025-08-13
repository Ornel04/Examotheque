<?php
session_start();

// Connexion à la base via variables d'environnement Docker
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'examotheque';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASSWORD') ?: 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("Location: http://localhost:3000/etu/inscription.html");
    exit();
}

// Récupération des données du formulaire (penser à valider côté frontend aussi)
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (!$nom || !$prenom || !$email || !$password || !$confirm_password) {
    header("Location: http://localhost:3000/etu/inscription.html?error=empty");
    exit();
}

// Vérification des mots de passe
if ($password !== $confirm_password) {
    header("Location: http://localhost:3000/etu/inscription.html?error=password_mismatch");
    exit();
}

// Hachage du mot de passe
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insertion dans la table utilisateur_etudiants
try {
    $stmt = $pdo->prepare("INSERT INTO utilisateur_etudiants (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $passwordHash]);

    // Envoi du mail simple sans SMTP
    $to = $email;
    $subject = "Bienvenue sur Examothèque !";
    $message = "Bonjour $prenom $nom,\n\nVotre compte a bien été créé.\nVous pouvez maintenant vous connecter ici : http://localhost:3000/etu/connexion.html\n\nMerci de votre inscription !";
    $headers = "From: noreply@examotheque.com\r\n" .
               "Reply-To: noreply@examotheque.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $message, $headers)) {
        echo "Compte créé avec succès ! Un email de confirmation a été envoyé.<br><a href='http://localhost:3000/etu/connexion.html'>Se connecter</a>";
    } else {
        echo "Compte créé avec succès, mais l'email de confirmation n'a pas pu être envoyé.<br><a href='http://localhost:3000/etu/connexion.html'>Se connecter</a>";
    }

} catch (PDOException $e) {
    // Par exemple, email déjà utilisé
    header("Location: http://localhost:3000/etu/inscription.html?error=duplicate_email");
    exit();
}
?>