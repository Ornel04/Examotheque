<?php
session_start();

// Empêche la mise en cache du navigateur
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.html");
    exit();
}

// Connexion à la base de données
$host = '192.168.56.80';
$dbname = 'register';
$user = 'user';
$pass = 'mdp';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

$ecole = '';
$sites = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ecole = trim($_POST['ecole'] ?? '');

    if ($ecole === '') {
        $error = "Veuillez saisir un nom d'école/université.";
    } else {
        $stmt = $pdo->prepare("SELECT DISTINCT Site_Ville FROM categorie_ecole WHERE Ecole_Université = ?");
        $stmt->execute([$ecole]);
        $sites = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($sites) === 0) {
            $error = "L'école/université saisie n'existe pas dans la base.";
        }
    }
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
    }
    .top-bar {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding: 10px 20px;
      background-color: #f4f4f4;
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
    h1 {
      color: #2c3e50;
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    input[type="text"], select {
      width: 300px;
      padding: 5px;
      margin-top: 5px;
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
        // Recharge la page, donc refait la vérification de session côté serveur
        window.location.reload();
      }
    });
  </script>
</head>
<body>

  <div class="top-bar">
    <a href="logout.php">Se déconnecter</a>
  </div>

  <h1>Sélectionnez votre École / Université</h1>

  <form method="POST" action="">
    <label for="ecole">École / Université :</label>
    <input type="text" id="ecole" name="ecole" value="<?= htmlspecialchars($ecole) ?>" autocomplete="off" />

    <button type="submit" style="margin-top: 15px;">Valider</button>
  </form>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if (!empty($sites)): ?>
    <form method="POST" action="traitement.php" style="margin-top: 20px;">
      <input type="hidden" name="ecole" value="<?= htmlspecialchars($ecole) ?>" />
      <label for="site">Site / Ville :</label>
      <select id="site" name="site" required>
        <option value="">-- Choisissez --</option>
        <?php foreach ($sites as $site): ?>
          <option value="<?= htmlspecialchars($site) ?>"><?= htmlspecialchars($site) ?></option>
        <?php endforeach; ?>
      </select>

      <button type="submit" style="margin-top: 15px;">Suivant</button>
    </form>
  <?php endif; ?>

</body>
</html>
