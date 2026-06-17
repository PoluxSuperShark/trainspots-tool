<?php

require 'config.php';

/*
|--------------------------------------------------------------------------
| ACTION : VALIDER
|--------------------------------------------------------------------------
*/

if (isset($_GET['validate'])) {

    $id = (int) $_GET['validate'];

    $stmt = $pdo->prepare("
        UPDATE entries
        SET statut = 'valide'
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header("Location: suggestions.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| ACTION : SUPPRIMER
|--------------------------------------------------------------------------
*/

if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    $stmt = $pdo->prepare("
        DELETE FROM entries
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header("Location: suggestions.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| FILTRES
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$typeFilter = $_GET['type'] ?? '';

$sql = "SELECT * FROM entries WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (titre LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status !== '') {
    $sql .= " AND statut = ?";
    $params[] = $status;
}

if ($typeFilter !== '') {
    $sql .= " AND type = ?";
    $params[] = $typeFilter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$entries = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| TYPES
|--------------------------------------------------------------------------
*/

$typeLabels = [
    'spot' => '📷 Spot',
    'trajet' => '🚆 Trajet',

    'spots' => '📷 Spots',
    'voyages' => '🚆 Voyages',
    'departs' => '🟢 Départs',
    'arrives' => '🔴 Arrivées',
    'departs_arrives' => '🔵 Départs / Arrivées',
    'compilations' => '🎬 Compilations',
    'documentaires' => '🎥 Documentaires',
    'passages' => '🚄 Passages de trains'
];

?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Suggestions - LTAP</title>

<link rel="stylesheet" href="./assets/bootstrap.min.css">
<link rel="stylesheet" href="./assets/bootstrap-icons.min.css">

<link rel="shortcut icon" href="./assets/favicon.jpg" type="image/x-icon">

</head>

<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="mb-0"><a href="/index.php">Accueil</a></h1>
            <h2 class="text-muted">Gestion des contenus</h2>
            <p>Valider quand la vidéo est tournée, supprimer une fois en ligne (visible)</p>
        </div>

        <a href="index.php" class="btn btn-primary"> + Ajouter</a>

    </div>

    <form method="GET" class="card p-3 mb-4 shadow-sm" autocomplete="off">

        <div class="row g-2">

            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                       placeholder="Recherche par type, statut, titre..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" <?= $status === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="valide" <?= $status === 'valide' ? 'selected' : '' ?>>Validé</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>

                    <?php foreach ($typeLabels as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $typeFilter === $key ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Filtrer</button>
            </div>

        </div>

    </form>

    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (count($entries) > 0): ?>

                    <?php foreach ($entries as $e): ?>

                        <?php
                            $entryType = $e['type'] ?? '';
                            $label = $typeLabels[$entryType] ?? null;
                        ?>

                        <tr>

                            <td>#<?= $e['id'] ?></td>

                            <td>
                                <span class="badge bg-secondary">
                                    <?= $label ?? '➕ ' . $entryType ?>
                                </span>
                            </td>

                            <td><?= htmlspecialchars($e['titre']) ?></td>

                            <td>
                                <?php if ($e['statut'] === 'valide'): ?>
                                    <span class="badge bg-success">Validé</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">En attente</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= date('d/m/Y H:i', strtotime($e['created_at'])) ?>
                            </td>

                            <td class="text-end">

                                <?php if ($e['statut'] !== 'valide'): ?>
                                    <a href="?validate=<?= $e['id'] ?>" class="btn btn-success btn-sm">✔</a>
                                <?php endif; ?>

                                <a href="?delete=<?= $e['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm(`Voulez-vous vraiment supprimer l'entrée ?`)">
                                    🗑
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Aucune entrée n'a été trouvée.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="./assets/bootstrap.bundle.min.js"></script>

</body>
</html>