<?php
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf_file'])) {
    $parser = new Parser();
    
    try {
        $pdf = $parser->parseFile($_FILES['pdf_file']['tmp_name']);
        $text = $pdf->getText();

        // Nettoyage du texte pour éviter les bugs de lecture
        $text = str_replace("\n", " ", $text);

        // Extraction des données avec des expressions régulières (Regex)
        function extraire($pattern, $input) {
            if (preg_match($pattern, $input, $matches)) {
                return trim($matches[1]);
            }
            return "Non détecté";
        }

        $marque = extraire('/Marque\s+([^\s]+)/i', $text); [cite: 4]
        $modele = extraire('/Modèle\s+([^\s]+)/i', $text); [cite: 4]
        $km = extraire('/Kilométrage\s+([0-9\s]+)/i', $text); [cite: 4]
        
        // Affichage test avant de générer le fichier final
        echo "<body style='background:#1a1a1a;color:white;font-family:sans-serif;padding:20px;'>";
        echo "<h2>Résultat de l'analyse :</h2>";
        echo "<ul style='list-style:none;padding:0;line-height:2;'>";
        echo "<li><strong>Véhicule :</strong> " . $marque . " " . $modele . "</li>";
        echo "<li><strong>KM :</strong> " . $km . "</li>";
        echo "<li><strong>Pression Début :</strong> " . $p_debut . " bar</li>";
        echo "<li><strong>Pression Fin :</strong> " . $p_fin . " bar</li>";
        echo "</ul>";
        echo "<br><br><a href='index.php' style='background:#d32f2f;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Nouveau rapport</a>";
        echo "</body>";
?>
