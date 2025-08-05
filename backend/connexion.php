<?php
session_start();

// 1. Connexion à la base de données
$host = '192.168.56.80';
$dbname = 'register';
$user = 'user';
$pass = 'mdp';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("Location: login.html");
    exit();
}

// 2. Récupération des données du formulaire
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    die("Veuillez remplir tous les champs.");
}

// 3. Recherche de l'utilisateur par email
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 4. Vérification du mot de passe
if ($user && password_verify($password, $user['mot_de_passe'])) {
    // 5. Authentification réussie
    $_SESSION['utilisateur_id'] = $user['id'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];
    $_SESSION['email'] = $user['email'];

    header("Location: accueil.php");
    exit();

} else {
    // 6. Échec de l’authentification
    header("Location: login.html");
    exit();
}
?>