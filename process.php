<?php
// 1. GESTION DES ERREURS : On logue mais on n'affiche rien pour ne pas bloquer le PDF
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED);
ob_start(); 

require 'vendor/autoload.php';
use Smalot\PdfParser\Parser;

// Vérification de l'existence de la classe FPDF (pour éviter un crash si autoload manque)
if (!class_exists('FPDF')) {
    die("Erreur : La bibliothèque FPDF n'est pas installée. Lancez 'composer require setasign/fpdf'.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file']['tmp_name'];
    
    try {
        $parser = new Parser();
        $pdfParsed = $parser->parseFile($file);
        $text = $pdfParsed->getText();

        // NETTOYAGE : On normalise les espaces et on met tout sur une ligne
        $cleanText = preg_replace('/\s+/', ' ', $text);

        // FONCTION D'EXTRACTION : On cherche le texte entre guillemets après le label
        function getVal($label, $txt) {
            $pattern = '/' . preg_quote($label, '/') . '\s*"\s*,\s*"\s*([^"]+)/i';
            if (preg_match($pattern, $txt, $m)) {
                return trim($m[1]);
            }
            return "N/C";
        }

        // Extraction des données
        $immat    = getVal('Immatriculation', $cleanText);
        $marque   = getVal('Marque', $cleanText);
        $modele   = getVal('Modèle', $cleanText);
        $km       = getVal('Kilométrage', $cleanText);
        $h_recup  = getVal('Huiler récupérée', $cleanText);
        $h_inj    = getVal('Huile injectée', $cleanText);
        $p_debut  = getVal('Pression début de prestation relevée', $cleanText);
        $p_fin    = getVal('Pression fin de prestation relevée', $cleanText);
        $norme    = getVal('Norme d\'huile d\'origine', $cleanText);
        $type_h   = getVal('Type d\'huile', $cleanText);

        // GÉNÉRATION DU PDF
        if (ob_get_length()) ob_clean(); 
        
        $pdf = new FPDF(); 
        $pdf->AddPage();
        
        // Titre
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(200, 0, 0); 
        $pdf->Cell(0, 15, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'RAPPORT D\'INTERVENTION VIDANGE BVA'), 0, 1, 'C');
        $pdf->Ln(10);

        // Construction du tableau
        $pdf->SetFont('Arial', '', 11);
        $data = [
            ['Véhicule', $marque . ' ' . $modele],
            ['Immatriculation', $immat],
            ['Kilométrage', $km],
            ['Huile récupérée', $h_recup],
            ['Huile injectée', $h_inj],
            ['Pressions (Début / Fin)', $p_debut . ' / ' . $p_fin],
            ['Norme d\'huile', $norme],
            ['Type d\'huile', $type_h]
        ];

        foreach ($data as $row) {
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell(80, 10, iconv('UTF-8', 'windows-1252', $row[0]), 1, 0, 'L', true); 
            $pdf->Cell(110, 10, iconv('UTF-8', 'windows-1252', $row[1]), 1, 1, 'L');
        }

        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "Signature et tampon du professionnel :"), 0, 1);

        // Téléchargement immédiat
        $pdf->Output('D', 'Rapport_BVA_' . str_replace(' ', '_', $immat) . '.pdf');
        exit;

    } catch (Exception $e) {
        ob_end_clean();
        echo "Erreur lors de l'extraction : " . $e->getMessage();
    }
} else {
    echo "Aucun fichier reçu.";
}
?>
