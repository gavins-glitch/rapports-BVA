<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Surannais - Accès</title>
    <style>
        body { background: #121212; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: #1e1e1e; padding: 30px; border-radius: 15px; border: 1px solid #333; text-align: center; width: 320px; }
        h1 { color: #d32f2f; }
        input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 5px; border: 1px solid #444; background: #252525; color: white; box-sizing: border-box; }
        button { background: #d32f2f; color: white; border: none; padding: 15px; width: 100%; border-radius: 5px; font-weight: bold; cursor: pointer; }
        .hidden { display: none; }
    </style>
</head>
<body>
<div class="container">
    <h1>Garage Surannais</h1>
    <div id="login">
        <p>Code d'accès requis :</p>
        <input type="password" id="access-code" placeholder="••••">
        <button onclick="check()">Entrer</button>
    </div>
    <div id="upload" class="hidden">
        <p>Sélectionnez le rapport Bardahl :</p>
        <form action="process.php" method="post" enctype="multipart/form-data">
            <input type="file" name="pdf_file" accept=".pdf" required>
            <button type="submit">Générer Rapport GSW</button>
        </form>
    </div>
</div>
<script>
    function check() {
        if (document.getElementById('access-code').value.toUpperCase() === 'GSW') {
            document.getElementById('login').style.display = 'none';
            document.getElementById('upload').style.display = 'block';
        } else { alert('Code incorrect'); }
    }
</script>
</body>
</html>
