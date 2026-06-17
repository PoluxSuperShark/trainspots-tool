<?php

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = $pdo->prepare("
        INSERT INTO entries(type, titre, description)
        VALUES (?, ?, ?)
    ");

    $sql->execute([
        $_POST['type'],
        $_POST['titre'],
        $_POST['description'] ?? ''
    ]);

    header("Location: suggestions.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Nouvelle entrée - LTAP</title>

<link rel="stylesheet" href="./assets/bootstrap.min.css">
<link rel="stylesheet" href="./assets/bootstrap-icons.min.css">

<link rel="shortcut icon" href="./assets/favicon.jpg" type="image/x-icon">

</head>

<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="mb-0"><a href="/index.php">Accueil</a></h1>
            <small class="text-muted">Ajoute un contenu ferroviaire</small>
        </div>

        <a href="suggestions.php" class="btn btn-outline-primary">
            Consulter toutes les suggestions
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-header">
            <strong>Ajouter une entrée</strong>
        </div>

        <div class="card-body">

            <form method="POST" autocomplete="off">

                <div class="mb-3">

                    <label>Type</label>

                    <select name="type" class="form-select" required>

                        <option value="spots">📷 Spots</option>
                        <option value="voyages">🚆 Voyages</option>
                        <option value="departs">🟢 Départs</option>
                        <option value="arrives">🔴 Arrivées</option>
                        <option value="departs_arrives">🔵 Départs / Arrivées</option>
                        <option value="compilations">🎬 Compilations</option>
                        <option value="documentaires">🎥 Documentaires</option>
                        <option value="passages">🚄 Passages de trains</option>

                    </select>

                </div>

                <div class="mb-3">
                    <label>Titre</label>
                    <input type="text" name="titre" class="form-control" required placeholder="Titre de la suggestion (ex. : TGV à Valence)">
                </div>

                <button class="btn btn-primary">
                    Ajouter l'entrée
                </button>

            </form>

        </div>

    </div>

</div>

<script src="./assets/bootstrap.bundle.min.js"></script>

</body>
</html>