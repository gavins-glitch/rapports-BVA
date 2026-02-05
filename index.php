<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Garage Surannais - Générateur de Rapport</title>
    <style>
        body { font-family: sans-serif; background: #1a1a1a; color: white; text-align: center; padding: 50px; }
        .box { background: #2a2a2a; padding: 30px; border-radius: 10px; border-top: 5px solid #d32f2f; display: inline-block; }
        input { margin: 20px 0; }
        button { background: #d32f2f; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Garage Surannais</h1>
        <p>Glissez le rapport Bardahl (PDF) pour extraire les données</p>
        <form action="process.php" method="post" enctype="multipart/form-data">
            <input type="file" name="pdf_file" accept=".pdf" required><br>
            <button type="submit">Générer le rapport Excel/PDF</button>
        </form>
    </div>
</body>
</html>