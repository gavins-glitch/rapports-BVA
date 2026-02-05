<?php
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file']['tmp_name'];
    
    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();

        // Nettoyage et extraction des données
        preg_match('/Véhicule\s*:\s*(.*)/i', $text, $matches_v);
        $vehicule = isset($matches_v[1]) ? trim($matches_v[1]) : "Non détecté";

        preg_match('/Kilométrage\s*:\s*(\d+)/i', $text, $matches_k);
        $km = isset($matches_k[1]) ? $matches_k[1] . " km" : "Non détecté";

        preg_match('/Pression entrée\s*:\s*([\d,.]+)/i', $text, $matches_pe);
        $p_debut = isset($matches_pe[1]) ? $matches_pe[1] : "0.0";

        preg_match('/Pression sortie\s*:\s*([\d,.]+)/i', $text, $matches_ps);
        $p_fin = isset($matches_ps[1]) ? $matches_ps[1] : "0.0";

        // Affichage propre du résultat
        echo "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Rapport BVA - Résultat</title>
        </head>
        <body style='background:#121212; color:#e0e0e0; font-family:sans-serif; padding:20px; text-align:center;'>
            <div style='background:#1e1e1e; padding:30px; border-radius:15px; display:inline-block; border:1px solid #333; text-align:left; max-width:400px; box-shadow: 0 4px 15px rgba(0,0,0,0.5);'>
                <h2 style='color:#d32f2f; margin-top:0; border-bottom:2px solid #d32f2f; padding-bottom:10px;'>Analyse Terminée</h2>
                
                <p style='font-size:1.1em;'><strong>🚗 Véhicule :</strong> <span style='color:#fff;'>$vehicule</span></p>
                <p style='font-size:1.1em;'><strong>🛣️ Kilométrage :</strong> <span style='color:#fff;'>$km</span></p>
                
                <div style='margin-top:20px; padding:15px; background:#252525; border-radius:8px;'>
                    <p style='margin:5px 0;'><strong>📉 Pression Début :</strong> <span style='color:#ff9800;'>$p_debut bar</span></p>
                    <p style='margin:5px 0;'><strong>📈 Pression Fin :</strong> <span style='color:#4caf50;'>$p_fin bar</span></p>
                </div>

                <a href='index.php' style='display:block; margin-top:25px; background:#d32f2f; color:white; padding:12px; text-decoration:none; border-radius:5px; text-align:center; font-weight:bold;'>🔄 Analyser un autre PDF</a>
            </div>
        </body>
        </html>";

    } catch (Exception $e) {
        echo "<body style='background:#121212; color:white; font-family:sans-serif; padding:20px;'>";
        echo "<h3>❌ Erreur lors de l'analyse :</h3> " . htmlspecialchars($e->getMessage());
        echo "<br><br><a href='index.php' style='color:#d32f2f;'>Retour</a></body>";
    }
} else {
    header("Location: index.php");
    exit;
}
?>
