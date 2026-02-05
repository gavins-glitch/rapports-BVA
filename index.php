<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Surannais - Générateur BVA</title>
    <style>
        body { background: #121212; color: white; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: #1e1e1e; padding: 30px; border-radius: 15px; border: 1px solid #333; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); width: 350px; }
        h1 { color: #d32f2f; font-size: 22px; margin-bottom: 20px; }
        input[type="text"], input[type="file"] { width: 100%; padding: 12px; margin: 10px 0; border-radius: 5px; border: 1px solid #444; background: #252525; color: white; box-sizing: border-box; }
        button { background: #d32f2f; color: white; border: none; padding: 15px; width: 100%; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 16px; margin-top: 10px; }
        button:hover { background: #b71c1c; }
        .hidden { display: none; }
        #error-msg { color: #ff5252; font-size: 14px; margin-bottom: 10px; display: none; }
    </style>
</head>
<body>

<div class="container">
    <h1>Garage Surannais</h1>
    
    <div id="login-section">
        <p>Entrez le code d'accès :</p>
        <div id="error-msg">Code incorrect</div>
        <input type="text" id="access-code" placeholder="Code GSW...">
        <button onclick="checkCode()">Valider</button>
    </div>

    <div id="upload-section" class="hidden">
        <p>Sélectionnez le rapport Bardahl :</p>
        <form action="process.php" method="post" enctype="multipart/form-data">
            <input type="file" name="pdf_file" accept=".pdf" required>
            <button type="submit">Générer le Rapport Final</button>
        </form>
    </div>
</div>

<script>
    function checkCode() {
        const code = document.getElementById('access-code').value;
        const error = document.getElementById('error-msg');
        
        // On vérifie le code (GSW)
        if (code.toUpperCase() === 'GSW') {
            document.getElementById('login-section').classList.add('hidden');
            document.getElementById('upload-section').classList.remove('hidden');
        } else {
            error.style.display = 'block';
        }
    }
</script>

</body>
</html>
