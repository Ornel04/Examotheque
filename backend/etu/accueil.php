<?php
session_start();

// Empêche la mise en cache du navigateur
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache");
header("Expires: 0");

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: http://localhost:3000/etu/connexion.html");
    exit();
}

// Connexion à la base de données avec variables d'environnement Docker
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

// Fonction pour récupérer les écoles pour datalist
function getEcoles(PDO $pdo) {
    $stmt = $pdo->query("SELECT DISTINCT ecole_universite FROM download_epreuve ORDER BY ecole_universite ASC");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Nettoyage basique des entrées
function clean($str) {
    return htmlspecialchars(trim($str));
}

// Récupération ou traitement des données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ecole = clean($_POST['ecole'] ?? '');
    $site = clean($_POST['site'] ?? '');
    $classe = clean($_POST['classe'] ?? '');
    $annee = clean($_POST['annee'] ?? '');
    $option = clean($_POST['option'] ?? '');
    $ue = clean($_POST['ue'] ?? '');
    $matiere = clean($_POST['matiere'] ?? '');

    $_SESSION['form_data'] = compact('ecole', 'site', 'classe', 'annee', 'option', 'ue', 'matiere');

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} elseif (isset($_SESSION['form_data'])) {
    extract($_SESSION['form_data']);
    unset($_SESSION['form_data']);
} else {
    $ecole = $site = $classe = $annee = $option = $ue = $matiere = '';
}

$error = '';
$sites = $classes = $annees = $options = $ues = $matieres = [];

if ($ecole !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT site_ville FROM download_epreuve WHERE ecole_universite = ? ORDER BY site_ville");
    $stmt->execute([$ecole]);
    $sites = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (count($sites) === 0) {
        $error = "L'école/université saisie n'existe pas dans la base.";
    }
}

if ($ecole !== '' && $site !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT classe_niveau FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? ORDER BY classe_niveau");
    $stmt->execute([$ecole, $site]);
    $classes = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($ecole !== '' && $site !== '' && $classe !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT annee_academique FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? ORDER BY annee_academique");
    $stmt->execute([$ecole, $site, $classe]);
    $annees = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($ecole !== '' && $site !== '' && $classe !== '' && $annee !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT option_filiere FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? ORDER BY option_filiere");
    $stmt->execute([$ecole, $site, $classe, $annee]);
    $options = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($ecole !== '' && $site !== '' && $classe !== '' && $annee !== '' && $option !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT ue FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ? ORDER BY ue");
    $stmt->execute([$ecole, $site, $classe, $annee, $option]);
    $ues = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($ecole !== '' && $site !== '' && $classe !== '' && $annee !== '' && $option !== '' && $ue !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT matiere FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ? AND ue = ? ORDER BY matiere");
    $stmt->execute([$ecole, $site, $classe, $annee, $option, $ue]);
    $matieres = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Préparation des données à envoyer à la vue
$data = [
    'ecole' => $ecole,
    'site' => $site,
    'classe' => $classe,
    'annee' => $annee,
    'option' => $option,
    'ue' => $ue,
    'matiere' => $matiere,
    'sites' => $sites,
    'classes' => $classes,
    'annees' => $annees,
    'options' => $options,
    'ues' => $ues,
    'matieres' => $matieres,
    'listeEcoles' => getEcoles($pdo),
    'error' => $error
];

include 'traitement.php';
