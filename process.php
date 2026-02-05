<?php
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file']['tmp_name'];
    
    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();

        // Extraction des données avec des recherches souples (Regex)
        preg_match('/Véhicule\s*:\s*(.*)/i', $text, $matches_v);
        $vehicule = isset($matches_v[1]) ? trim($matches_v[1]) : "Non trouvé";

        preg_match('/Kilométrage\s*:\s*(\d+)/i', $text, $matches_k);
        $km = isset($matches_k[1]) ? $matches_k[1] . " km" : "Non trouvé";

        preg_match('/Pression entrée\s*:\s*([\d,.]+)/i', $text, $matches_pe);
        $p_debut = isset($matches_pe[1]) ? $matches_pe[1] : "0.0";

        preg_match('/Pression sortie\s*:\s*([\d,.]+)/i', $text, $matches_ps);
        $p_fin = isset($matches_ps[1]) ? $matches_ps[1] : "0.0";

        // Affichage du résultat
        echo "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <title>Résultat Analyse BVA</title>
        </head>
        <body style='background:#121212; color:#e0e0e0; font-family:sans-serif; padding:40px; text-align:center;'>
            <div style='background:#1e1e1e; padding:20px; border-radius:10px; display:inline-block; border:1px solid #333; text-align:left; min-width:300px;'>
                <h2 style='color:#d32f2f; border-bottom:1px solid #333; padding-bottom:10px;'>Rapport d'Analyse</h2>
                <p><strong>🚗 Véhicule :</strong> $vehicule</p>
                <p><strong>🛣️ Kilométrage :</strong> $km</p>
                <hr style='border:0; border-top:1px solid #333;'>
                <p><strong>📉 Pression Début :</strong> <span style='color:#ff9800;'>$p_debut bar</span></p>
                <p><strong>📈 Pression Fin :</strong> <span style='color:#4caf50;'>$p_fin bar</span></p>
                <br>
                <a href='index.php' style='display:block; background:#d32f2f; color:white; padding:12px; text-decoration:none; border-radius:5px; text-align:center; font-weight:bold;'>🔄 Analyser un autre fichier</a>
            </div>
        </body>
