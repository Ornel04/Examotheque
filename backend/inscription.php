<?php
// 1. Connexion à la base de données
$host = '192.168.56.80';
$dbname = 'register';
$user = 'user';
$pass = 'mdp';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("Location: register.html");
    exit();
}

// 2. Récupération des données du formulaire
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// 3. Vérification des mots de passe
if ($password !== $confirm_password) {
    header("Location: register.html");
    exit();
}

// 4. Hachage du mot de passe
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// 5. Insertion dans la base de données
try {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $passwordHash]);
    echo "Compte créé avec succès ! <a href='login.html'>Se connecter</a>";
} catch (PDOException $e) {
    // En cas d'erreur (email déjà utilisé, etc.)
    header("Location: inscription.html");
    exit();
}
?>
