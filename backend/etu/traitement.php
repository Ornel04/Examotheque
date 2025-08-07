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

<?php if (!empty($data['error'])): ?>
  <p class="error"><?= htmlspecialchars($data['error']) ?></p>
<?php endif; ?>

<form method="POST" action="">
  <label for="ecole">École / Université :</label>
  <input
    type="text"
    id="ecole"
    name="ecole"
    list="ecoles-list"
    value="<?= htmlspecialchars($data['ecole']) ?>"
    autocomplete="off"
    required
    onchange="this.form.submit()"
  />
  <datalist id="ecoles-list">
    <?php foreach ($data['listeEcoles'] as $uneEcole): ?>
      <option value="<?= htmlspecialchars($uneEcole) ?>"></option>
    <?php endforeach; ?>
  </datalist>

  <?php if (!empty($data['sites'])): ?>
    <label for="site">Site / Ville :</label>
    <select name="site" id="site" onchange="this.form.submit()" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($data['sites'] as $s): ?>
        <option value="<?= htmlspecialchars($s) ?>" <?= $s === $data['site'] ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
      <?php endforeach; ?>
    </select>
  <?php endif; ?>

  <?php if (!empty($data['classes'])): ?>
    <label for="classe">Classe / Niveau :</label>
    <select name="classe" id="classe" onchange="this.form.submit()" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($data['classes'] as $c): ?>
        <option value="<?= htmlspecialchars($c) ?>" <?= $c === $data['classe'] ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
      <?php endforeach; ?>
    </select>
  <?php endif; ?>

  <?php if (!empty($data['annees'])): ?>
    <label for="annee">Année académique :</label>
    <select name="annee" id="annee" onchange="this.form.submit()" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($data['annees'] as $a): ?>
        <option value="<?= htmlspecialchars($a) ?>" <?= $a === $data['annee'] ? 'selected' : '' ?>><?= htmlspecialchars($a) ?></option>
      <?php endforeach; ?>
    </select>
  <?php endif; ?>

  <?php if (!empty($data['options'])): ?>
    <label for="option">Option / Filière :</label>
    <select name="option" id="option" onchange="this.form.submit()" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($data['options'] as $opt): ?>
        <option value="<?= htmlspecialchars($opt) ?>" <?= $opt === $data['option'] ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
      <?php endforeach; ?>
    </select>
  <?php endif; ?>

  <?php if (!empty($data['ues'])): ?>
    <label for="ue">UE :</label>
    <select name="ue" id="ue" onchange="this.form.submit()" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($data['ues'] as $u): ?>
        <option value="<?= htmlspecialchars($u) ?>" <?= $u === $data['ue'] ? 'selected' : '' ?>><?= htmlspecialchars($u) ?></option>
      <?php endforeach; ?>
    </select>
  <?php endif; ?>

  <?php if (!empty($data['matieres'])): ?>
    <label for="matiere">Matière :</label>
    <select name="matiere" id="matiere" onchange="this.form.submit()" required>
      <option value="">-- Choisissez --</option>
      <?php foreach ($data['matieres'] as $m): ?>
        <option value="<?= htmlspecialchars($m) ?>" <?= $m === $data['matiere'] ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
      <?php endforeach; ?>
    </select>
  <?php endif; ?>

</form>

</body>
</html>
