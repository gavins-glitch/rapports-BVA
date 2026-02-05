<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Garage Surannais - Rapport Complet BVA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h2 class="text-danger mb-4">Saisie du Rapport d'Intervention</h2>
        <form action="process.php" method="POST" class="card p-4 shadow-sm">
            <h5 class="border-bottom pb-2">Informations Véhicule</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3"><label class="form-label">Immatriculation</label><input type="text" name="immat" class="form-control" placeholder="GL-424-RT" required></div>
                <div class="col-md-3"><label class="form-label">Marque</label><input type="text" name="marque" class="form-control" placeholder="VOLKSWAGEN" required></div>
                <div class="col-md-3"><label class="form-label">Modèle</label><input type="text" name="modele" class="form-control" placeholder="GOLF VI" required></div>
                <div class="col-md-3"><label class="form-label">Kilométrage</label><input type="text" name="km" class="form-control" placeholder="149071" required></div>
                <div class="col-md-4"><label class="form-label">Utilisation</label><input type="text" name="utilisation" class="form-control" placeholder="Urbain"></div>
            </div>

            <h5 class="border-bottom pb-2">Données Techniques Vidange</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3"><label class="form-label">Huile récupérée (L)</label><input type="text" name="h_recup" class="form-control" placeholder="10,57"></div>
                <div class="col-md-3"><label class="form-label">Huile injectée (L)</label><input type="text" name="h_inj" class="form-control" placeholder="10,22"></div>
                <div class="col-md-3"><label class="form-label">Filtre remplacé</label><select name="filtre" class="form-select"><option value="Oui">Oui</option><option value="Non" selected>Non</option></select></div>
                <div class="col-md-3"><label class="form-label">Quantité Rinçage (L)</label><input type="text" name="rincage" class="form-control" placeholder="0,00"></div>
                <div class="col-md-3"><label class="form-label">Pression Début (Bars)</label><input type="text" name="p_debut" class="form-control" placeholder="5,5"></div>
                <div class="col-md-3"><label class="form-label">Pression Fin (Bars)</label><input type="text" name="p_fin" class="form-control" placeholder="5,6"></div>
                <div class="col-md-3"><label class="form-label">Norme Huile</label><input type="text" name="norme" class="form-control" placeholder="VW G 052 182"></div>
                <div class="col-md-3"><label class="form-label">Type d'Huile</label><input type="text" name="type_h" class="form-control" placeholder="DCT XTG"></div>
            </div>

            <h5 class="border-bottom pb-2">Remarques</h5>
            <div class="row g-3">
                <div class="col-12">
                    <textarea name="remarques" class="form-control" rows="3" placeholder="Ex: Prochaine vidange conseillée dans 2 ans ou 60 000 km..."></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-danger mt-4 w-100">Générer le Rapport PDF Final</button>
        </form>
    </div>
</body>
</html>
