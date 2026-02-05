<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Vidange BVA - Outil Interne</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a1a;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #2a2a2a;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.6);
            border-top: 6px solid #d32f2f;
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        h1 {
            font-size: 1.6rem;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .subtitle {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }
        .file-input-wrapper {
            margin-bottom: 25px;
        }
        input[type="file"] {
            background: #333;
            color: #ccc;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #444;
            width: 100%;
            box-sizing: border-box;
            cursor: pointer;
        }
        .btn-submit {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-submit:hover {
            background-color: #b71c1c;
        }
        .btn-submit:active {
            transform: scale(0.98);
        }
        .icon {
            font-size: 40px;
            margin-bottom: 15px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="icon">🔧</span>
        <h1>Rapport Vidange BVA</h1>
        <p class="subtitle">Outil d'extraction et de conversion</p>
        
        <form action="process.php" method="post" enctype="multipart/form-data">
            <div class="file-input-wrapper">
                <input type="file" name="pdf_file" accept=".pdf" required>
            </div>
            <button type="submit" class="btn-submit">Générer le rapport final</button>
        </form>
    </div>
</body>
</html>
