<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED);
ob_start(); 

require 'vendor/autoload.php';
use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    try {
        $parser = new Parser();
        $pdfParsed = $parser->parseFile($_FILES['pdf_file']['tmp_name']);
        $text = $pdfParsed->getText();

        // On nettoie juste les retours à la ligne pour avoir un bloc de texte continu
        $text = str_replace(["\n", "\r"], " ", $text);
        $text = preg_replace('/\s+/', ' ', $text);

        // Cette fonction est beaucoup plus permissive
        function extractData($label, $fullText) {
            // On cherche le label, on ignore les symboles éventuels (: , " ) 
            // et on capture jusqu'au prochain mot qui commence par une majuscule (souvent le label suivant)
            $pattern = '/' . preg_quote($label, '/') . '[\s" ,:]+([^"|:|,]+)/i';
            if (preg_match($pattern, $fullText, $m)) {
                return trim($m[1]);
            }
            return "Non trouve";
        }

        $immat   = extractData('Immatriculation', $text);
        $marque  = extractData('Marque', $text);
        $modele  = extractData('Modèle', $text);
        $km      = extractData('Kilométrage', $text);
        $h_recup = extractData('Huiler récupérée', $text); // Garde la faute Bardahl [cite: 11]

        if (ob_get_length()) ob_clean();
        
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        
        // Test d'affichage simple pour vérifier si l'extraction marche
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "DEBUG - Immat trouvee : " . $immat), 0, 1);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "Vehicule : " . $marque . " " . $modele), 0, 1);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "KM : " . $km), 0, 1);
        
        $pdf->Output('D', 'test_debug.pdf');
        exit;

    } catch (Exception $e) {
        ob_end_clean();
        echo "Erreur : " . $e->getMessage();
    }
}
