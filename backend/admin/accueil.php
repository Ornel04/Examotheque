<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: http://localhost:3000/admin/connexion.html');
    exit();
}

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

// Récupération des critères POST
$ecole = $_POST['ecole'] ?? '';
$site = $_POST['site'] ?? '';
$classe = $_POST['classe'] ?? '';
$annee = $_POST['annee'] ?? '';
$option = $_POST['option'] ?? '';
$ue = $_POST['ue'] ?? '';
$matiere = $_POST['matiere'] ?? '';

$data = [
    'ecole' => $ecole,
    'site' => $site,
    'classe' => $classe,
    'annee' => $annee,
    'option' => $option,
    'ue' => $ue,
    'matiere' => $matiere,
    'error' => '',
    'listeEcoles' => [],
    'sites' => [],
    'classes' => [],
    'annees' => [],
    'options' => [],
    'ues' => [],
    'matieres' => [],
];

// Récupérer la liste des écoles
$stmt = $pdo->query("SELECT DISTINCT ecole_universite FROM download_epreuve ORDER BY ecole_universite");
$data['listeEcoles'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Si école sélectionnée, récupérer sites
if ($ecole !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT site_ville FROM download_epreuve WHERE ecole_universite = ? ORDER BY site_ville");
    $stmt->execute([$ecole]);
    $data['sites'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Si site sélectionné, récupérer classes
if ($ecole !== '' && $site !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT classe_niveau FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? ORDER BY classe_niveau");
    $stmt->execute([$ecole, $site]);
    $data['classes'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Si classe sélectionnée, récupérer années
if ($ecole !== '' && $site !== '' && $classe !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT annee_academique FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? ORDER BY annee_academique");
    $stmt->execute([$ecole, $site, $classe]);
    $data['annees'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Si année sélectionnée, récupérer options
if ($ecole !== '' && $site !== '' && $classe !== '' && $annee !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT option_filiere FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? ORDER BY option_filiere");
    $stmt->execute([$ecole, $site, $classe, $annee]);
    $data['options'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Si option sélectionnée, récupérer UE
if ($ecole !== '' && $site !== '' && $classe !== '' && $annee !== '' && $option !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT ue FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ? ORDER BY ue");
    $stmt->execute([$ecole, $site, $classe, $annee, $option]);
    $data['ues'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Si UE sélectionnée, récupérer matières
if ($ecole !== '' && $site !== '' && $classe !== '' && $annee !== '' && $option !== '' && $ue !== '') {
    $stmt = $pdo->prepare("SELECT DISTINCT matiere FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ? AND ue = ? ORDER BY matiere");
    $stmt->execute([$ecole, $site, $classe, $annee, $option, $ue]);
    $data['matieres'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Appel de la vue traitement.php
require 'traitement.php';
