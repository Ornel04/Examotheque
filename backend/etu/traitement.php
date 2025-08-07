<?php
session_start();

// Empêche le cache
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache");
header("Expires: 0");

// Vérifie la session
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: http://localhost:3000/etu/connexion.html");
    exit();
}

// Connexion BDD
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

// Récupération progressive des valeurs
$ecole = $_POST['ecole'] ?? '';
$site = $_POST['site'] ?? '';
$classe = $_POST['classe'] ?? '';
$annee = $_POST['annee'] ?? '';
$option = $_POST['option'] ?? '';
$ue = $_POST['ue'] ?? '';
$matiere = $_POST['matiere'] ?? '';

// Préparer les prochaines options
$classes = $annees = $options = $ues = $matieres = [];

if ($site && !$classe) {
    $stmt = $pdo->prepare("SELECT DISTINCT classe_niveau FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ?");
    $stmt->execute([$ecole, $site]);
    $classes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} elseif ($classe && !$annee) {
    $stmt = $pdo->prepare("SELECT DISTINCT annee_academique FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ?");
    $stmt->execute([$ecole, $site, $classe]);
    $annees = $stmt->fetchAll(PDO::FETCH_COLUMN);
} elseif ($annee && !$option) {
    $stmt = $pdo->prepare("SELECT DISTINCT option_filiere FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ?");
    $stmt->execute([$ecole, $site, $classe, $annee]);
    $options = $stmt->fetchAll(PDO::FETCH_COLUMN);
} elseif ($option && !$ue) {
    $stmt = $pdo->prepare("SELECT DISTINCT ue FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ?");
    $stmt->execute([$ecole, $site, $classe, $annee, $option]);
    $ues = $stmt->fetchAll(PDO::FETCH_COLUMN);
} elseif ($ue && !$matiere) {
    $stmt = $pdo->prepare("SELECT DISTINCT matiere FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ? AND ue = ?");
    $stmt->execute([$ecole, $site, $classe, $annee, $option, $ue]);
    $matieres = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Sélection Étapes</title>
</head>
<body>

<h2>Navigation progressive des épreuves</h2>

<form method="POST" action="">
  <input type="hidden" name="ecole" value="<?= htmlspecialchars($ecole) ?>">
  <input type="hidden" name="site" value="<?= htmlspecialchars($site) ?>">
  <input type="hidden" name="classe" value="<?= htmlspecialchars($classe) ?>">
  <input type="hidden" name="annee" value="<?= htmlspecialchars($annee) ?>">
  <input type="hidden" name="option" value="<?= htmlspecialchars($option) ?>">
  <input type="hidden" name="ue" value="<?= htmlspecialchars($ue) ?>">

  <?php if ($site && !$classe): ?>
    <label for="classe">Classe / Niveau :</label>
    <select name="classe" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($classes as $c): ?>
        <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Suivant</button>

  <?php elseif ($classe && !$annee): ?>
    <label for="annee">Année académique :</label>
    <select name="annee" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($annees as $a): ?>
        <option value="<?= htmlspecialchars($a) ?>"><?= htmlspecialchars($a) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Suivant</button>

  <?php elseif ($annee && !$option): ?>
    <label for="option">Option / Filière :</label>
    <select name="option" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($options as $o): ?>
        <option value="<?= htmlspecialchars($o) ?>"><?= htmlspecialchars($o) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Suivant</button>

  <?php elseif ($option && !$ue): ?>
    <label for="ue">Unité d'enseignement (UE) :</label>
    <select name="ue" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($ues as $u): ?>
        <option value="<?= htmlspecialchars($u) ?>"><?= htmlspecialchars($u) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Suivant</button>

  <?php elseif ($ue && !$matiere): ?>
    <label for="matiere">Matière :</label>
    <select name="matiere" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($matieres as $m): ?>
        <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Terminer</button>

  <?php elseif ($matiere): ?>
    <p><strong>Sélection complète :</strong></p>
    <ul>
      <li>École : <?= htmlspecialchars($ecole) ?></li>
      <li>Site : <?= htmlspecialchars($site) ?></li>
      <li>Classe : <?= htmlspecialchars($classe) ?></li>
      <li>Année : <?= htmlspecialchars($annee) ?></li>
      <li>Option : <?= htmlspecialchars($option) ?></li>
      <li>UE : <?= htmlspecialchars($ue) ?></li>
      <li>Matière : <?= htmlspecialchars($matiere) ?></li>
    </ul>
    <!-- Ici, tu peux rediriger ou afficher les fichiers -->
    <p><a href="liste_epreuves.php?ecole=<?= urlencode($ecole) ?>&site=<?= urlencode($site) ?>&classe=<?= urlencode($classe) ?>&annee=<?= urlencode($annee) ?>&option=<?= urlencode($option) ?>&ue=<?= urlencode($ue) ?>&matiere=<?= urlencode($matiere) ?>">Afficher les épreuves</a></p>

  <?php endif; ?>
</form>

</body>
</html>
