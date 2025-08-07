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

// Gestion des valeurs selon POST ou redirection GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ecole = clean($_POST['ecole'] ?? '');
    $site = clean($_POST['site'] ?? '');
    $classe = clean($_POST['classe'] ?? '');
    $annee = clean($_POST['annee'] ?? '');
    $option = clean($_POST['option'] ?? '');
    $ue = clean($_POST['ue'] ?? '');
    $matiere = clean($_POST['matiere'] ?? '');

    // Stockage temporaire dans la session
    $_SESSION['form_data'] = [
        'ecole' => $ecole,
        'site' => $site,
        'classe' => $classe,
        'annee' => $annee,
        'option' => $option,
        'ue' => $ue,
        'matiere' => $matiere
    ];

    // Redirection en GET
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} elseif (isset($_SESSION['form_data'])) {
    extract($_SESSION['form_data']);
    unset($_SESSION['form_data']); // Nettoyage
} else {
    $ecole = $site = $classe = $annee = $option = $ue = $matiere = '';
}

$error = '';

// Préparer les listes selon les choix précédents
$sites = [];
$classes = [];
$annees = [];
$options = [];
$ues = [];
$matieres = [];

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Banque Épreuve</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      margin: 0;
      background: #f9f9f9;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background-color: #f4f4f4;
      border-bottom: 1px solid #ddd;
    }
    .top-bar a {
      text-decoration: none;
      color: #e74c3c;
      font-weight: bold;
      border: 1px solid #e74c3c;
      padding: 5px 10px;
      border-radius: 4px;
      transition: background-color 0.3s;
    }
    .top-bar a:hover {
      background-color: #e74c3c;
      color: white;
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    input[type="text"], select {
      width: 300px;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1em;
    }
    .error {
      color: red;
      margin-top: 10px;
    }
  </style>
  <script>
    // Empêche l’affichage en cache quand on revient en arrière
    window.addEventListener('pageshow', function(event) {
      if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        window.location.reload();
      }
    });
  </script>
</head>
<body>

  <div class="top-bar">
    <div style="font-size: 1.5em; font-weight: bold; color: black;">Examothèque</div>
    <a href="deconnexion.php">Se déconnecter</a>
  </div>

  <h3>Sélectionnez votre École / Université et autres critères</h3>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="">
    <label for="ecole">École / Université :</label>
    <?php $listeEcoles = getEcoles($pdo); ?>
    <input
      type="text"
      id="ecole"
      name="ecole"
      list="ecoles-list"
      value="<?= htmlspecialchars($ecole) ?>"
      autocomplete="off"
      required
      onchange="this.form.submit()"
    />
    <datalist id="ecoles-list">
      <?php foreach ($listeEcoles as $uneEcole): ?>
        <option value="<?= htmlspecialchars($uneEcole) ?>"></option>
      <?php endforeach; ?>
    </datalist>

    <?php if (!empty($sites)): ?>
      <label for="site">Site / Ville :</label>
      <select name="site" id="site" onchange="this.form.submit()" required>
        <option value="">-- Choisissez --</option>
        <?php foreach ($sites as $s): ?>
          <option value="<?= htmlspecialchars($s) ?>" <?= $s === $site ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

    <?php if (!empty($classes)): ?>
      <label for="classe">Classe / Niveau :</label>
      <select name="classe" id="classe" onchange="this.form.submit()" required>
        <option value="">-- Choisissez --</option>
        <?php foreach ($classes as $c): ?>
          <option value="<?= htmlspecialchars($c) ?>" <?= $c === $classe ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

    <?php if (!empty($annees)): ?>
      <label for="annee">Année académique :</label>
      <select name="annee" id="annee" onchange="this.form.submit()" required>
        <option value="">-- Choisissez --</option>
        <?php foreach ($annees as $a): ?>
          <option value="<?= htmlspecialchars($a) ?>" <?= $a === $annee ? 'selected' : '' ?>><?= htmlspecialchars($a) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

    <?php if (!empty($options)): ?>
      <label for="option">Option / Filière :</label>
      <select name="option" id="option" onchange="this.form.submit()" required>
        <option value="">-- Choisissez --</option>
        <?php foreach ($options as $opt): ?>
          <option value="<?= htmlspecialchars($opt) ?>" <?= $opt === $option ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

    <?php if (!empty($ues)): ?>
      <label for="ue">UE :</label>
      <select name="ue" id="ue" onchange="this.form.submit()" required>
        <option value="">-- Choisissez --</option>
        <?php foreach ($ues as $u): ?>
          <option value="<?= htmlspecialchars($u) ?>" <?= $u === $ue ? 'selected' : '' ?>><?= htmlspecialchars($u) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

    <?php if (!empty($matieres)): ?>
      <label for="matiere">Matière :</label>
      <select name="matiere" id="matiere" onchange="this.form.submit()" required>
        <option value="">-- Choisissez --</option>
        <?php foreach ($matieres as $m): ?>
          <option value="<?= htmlspecialchars($m) ?>" <?= $m === $matiere ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

  </form>

</body>
</html>
