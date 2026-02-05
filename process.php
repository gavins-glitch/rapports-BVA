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
        echo "<h2>Données extraites avec succès :</h2>";
        echo "<b>Véhicule :</b> " . $marque . " " . $modele . "<br>";
        echo "<b>Kilométrage :</b> " . $km . " km<br>";
        echo "<br><a href='index.php'>Retour</a>";

    } catch (Exception $e) {
        echo "Erreur lors de la lecture du PDF : " . $e->getMessage();
    }
}
?>