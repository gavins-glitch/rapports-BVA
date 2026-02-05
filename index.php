<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Garage Surannais - Nouveau Rapport</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="text-danger mb-4">Créer un Rapport de Vidange BVA</h2>
        <form action="process.php" method="POST" class="card p-4 shadow-sm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Immatriculation</label>
                    <input type="text" name="immat" class="form-control" placeholder="ex: GL-424-RT" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kilométrage</label>
                    <input type="number" name="km" class="form-control" placeholder="149071" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Marque</label>
                    <input type="text" name="marque" class="form-control" placeholder="VOLKSWAGEN" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Modèle</label>
                    <input type="text" name="modele" class="form-control" placeholder="GOLF VI" required>
                </div>
                <hr>
                <div class="col-md-6">
                    <label class="form-label">Huile Récupérée (L)</label>
                    <input type="text" name="h_recup" class="form-control" placeholder="10.57">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Huile Injectée (L)</label>
                    <input type="text" name="h_inj" class="form-control" placeholder="10.22">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pression Début (Bars)</label>
                    <input type="text" name="p_debut" class="form-control" placeholder="5.5">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pression Fin (Bars)</label>
                    <input type="text" name="p_fin" class="form-control" placeholder="5.6">
                </div>
            </div>
            <button type="submit" class="btn btn-danger mt-4">Générer le PDF</button>
        </form>
    </div>
</body>
</html>
