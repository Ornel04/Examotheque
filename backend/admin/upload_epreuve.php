<?php

// Vérifier que l'utilisateur est connecté en tant qu'admin
if (!isset($_SESSION['admin_id'])) {
    echo "<p>Vous devez être connecté pour ajouter une épreuve.</p>";
    exit;
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
    echo "<p>Erreur de connexion à la base de données : " . $e->getMessage() . "</p>";
    exit;
}

// Récupération des filtres depuis POST (ils doivent être envoyés par la page appelante)
$ecole = $_POST['ecole'] ?? '';
$site = $_POST['site'] ?? '';
$classe = $_POST['classe'] ?? '';
$annee = $_POST['annee'] ?? '';
$option = $_POST['option'] ?? '';
$ue = $_POST['ue'] ?? '';
$matiere = $_POST['matiere'] ?? '';

$message = '';

// *** Suppression d'une épreuve ***
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_epreuve'])) {
    $indexToDelete = (int)$_POST['delete_epreuve'];

    // Récupérer toutes les épreuves triées par nom_epreuve (afin de recalculer les noms)
    $stmt = $pdo->prepare("SELECT id FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ? AND ue = ? AND matiere = ? AND nom_epreuve IS NOT NULL ORDER BY id ASC");
    $stmt->execute([$ecole, $site, $classe, $annee, $option, $ue, $matiere]);
    $epreuves = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($epreuves[$indexToDelete])) {
        $idToDelete = $epreuves[$indexToDelete]['id'];

        // Récupérer le chemin du fichier pour suppression
        $stmtFile = $pdo->prepare("SELECT fichier_path FROM download_epreuve WHERE id = ?");
        $stmtFile->execute([$idToDelete]);
        $filePath = $stmtFile->fetchColumn();

        // Supprimer le fichier PDF
        if ($filePath && file_exists(__DIR__ . '/' . $filePath)) {
            unlink(__DIR__ . '/' . $filePath);
        }

        // Supprimer la ligne dans la BDD
        $stmtDelete = $pdo->prepare("DELETE FROM download_epreuve WHERE id = ?");
        $stmtDelete->execute([$idToDelete]);

        // Recompter et renommer les épreuves restantes pour garder la numérotation
        $stmt = $pdo->prepare("SELECT id FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ? AND ue = ? AND matiere = ? AND nom_epreuve IS NOT NULL ORDER BY id ASC");
        $stmt->execute([$ecole, $site, $classe, $annee, $option, $ue, $matiere]);
        $epreuves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($epreuves as $idx => $epreuve) {
            $nouveauNom = 'Épreuve ' . ($idx + 1);
            $stmtUpdate = $pdo->prepare("UPDATE download_epreuve SET nom_epreuve = ? WHERE id = ?");
            $stmtUpdate->execute([$nouveauNom, $epreuve['id']]);
        }

        $message = "<span style='color:green;'>L’épreuve a été supprimée avec succès.</span>";
    } else {
        $message = "<span style='color:red;'>Épreuve invalide.</span>";
    }
}

// Traitement du formulaire d'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['epreuve']) && $_FILES['epreuve']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['epreuve']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['epreuve']['tmp_name'];
        $originalName = basename($_FILES['epreuve']['name']);

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $message = "<span style='color:red;'>Seuls les fichiers PDF sont autorisés.</span>";
        } else {
            $newName = uniqid('epreuve_', true) . '.pdf';
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $uploadPath = $uploadDir . $newName;
            if (move_uploaded_file($tmpName, $uploadPath)) {
                // Compter uniquement les épreuves existantes qui ont un nom et un fichier
                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ? AND ue = ? AND matiere = ? AND nom_epreuve IS NOT NULL AND fichier_path IS NOT NULL");
                $countStmt->execute([$ecole, $site, $classe, $annee, $option, $ue, $matiere]);
                $count = $countStmt->fetchColumn();

                $nomEpreuve = 'Épreuve ' . ($count + 1);

                // Insérer dans la BDD
                $stmt = $pdo->prepare("INSERT INTO download_epreuve (ecole_universite, site_ville, classe_niveau, annee_academique, option_filiere, ue, matiere, nom_epreuve, fichier_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$ecole, $site, $classe, $annee, $option, $ue, $matiere, $nomEpreuve, 'uploads/' . $newName]);

                $message = "<span style='color:green;'>L’épreuve a été ajoutée avec succès.</span>";
            } else {
                $message = "<span style='color:red;'>Erreur lors du déplacement du fichier uploadé.</span>";
            }
        }
    } else {
        $message = "<span style='color:red;'>Erreur lors de l’upload du fichier.</span>";
    }
}

// Récupérer les épreuves déjà ajoutées pour ces critères
try {
    $stmt = $pdo->prepare("SELECT nom_epreuve FROM download_epreuve WHERE ecole_universite = ? AND site_ville = ? AND classe_niveau = ? AND annee_academique = ? AND option_filiere = ? AND ue = ? AND matiere = ? AND nom_epreuve IS NOT NULL ORDER BY id ASC");
    $stmt->execute([$ecole, $site, $classe, $annee, $option, $ue, $matiere]);
    $listeEpreuves = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $listeEpreuves = [];
}
?>

<div style="margin-top:30px; padding:15px; border:1px solid #ccc; max-width:400px;">
  <h3>Ajouter une épreuve (PDF)</h3>

  <?php if ($message) echo "<p>$message</p>"; ?>

  <form method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">
    <input type="hidden" name="ecole" value="<?= htmlspecialchars($ecole) ?>">
    <input type="hidden" name="site" value="<?= htmlspecialchars($site) ?>">
    <input type="hidden" name="classe" value="<?= htmlspecialchars($classe) ?>">
    <input type="hidden" name="annee" value="<?= htmlspecialchars($annee) ?>">
    <input type="hidden" name="option" value="<?= htmlspecialchars($option) ?>">
    <input type="hidden" name="ue" value="<?= htmlspecialchars($ue) ?>">
    <input type="hidden" name="matiere" value="<?= htmlspecialchars($matiere) ?>">

    <input type="file" name="epreuve" accept=".pdf" required>
    <br><br>
    <button type="submit" style="background:#007bff; color:#fff; padding:8px 14px; border:none; border-radius:4px; cursor:pointer;">Ajouter l'épreuve</button>
  </form>

  <?php if ($listeEpreuves): ?>
    <h4>Épreuves déjà ajoutées :</h4>
    <ul style="padding-left: 20px;">
      <?php foreach ($listeEpreuves as $index => $epreuve): ?>
        <li style="margin-bottom: 12px; line-height: 1.4;">
          <?= htmlspecialchars($epreuve) ?>

          <form method="POST" style="display:inline; margin-left: 10px;" onsubmit="return confirm('Supprimer cette épreuve ?');">
            <input type="hidden" name="ecole" value="<?= htmlspecialchars($ecole) ?>">
            <input type="hidden" name="site" value="<?= htmlspecialchars($site) ?>">
            <input type="hidden" name="classe" value="<?= htmlspecialchars($classe) ?>">
            <input type="hidden" name="annee" value="<?= htmlspecialchars($annee) ?>">
            <input type="hidden" name="option" value="<?= htmlspecialchars($option) ?>">
            <input type="hidden" name="ue" value="<?= htmlspecialchars($ue) ?>">
            <input type="hidden" name="matiere" value="<?= htmlspecialchars($matiere) ?>">
            <input type="hidden" name="delete_epreuve" value="<?= $index ?>">
            <button type="submit" style="background:#dc3545; color:#fff; border:none; padding:4px 8px; border-radius:4px; cursor:pointer;">Supprimer</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
