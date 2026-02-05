<?php
error_reporting(0);
ini_set('display_errors', 0);
require 'vendor/autoload.php';
use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file']['tmp_name'];
    try {
        $parser = new Parser();
        $pdfParsed = $parser->parseFile($file);
        $text = $pdfParsed->getText();

        // 1. NETTOYAGE TOTAL : on enlève les sauts de ligne et on réduit les espaces
        $clean = str_replace(["\n", "\r", "\t"], ' ', $text);
        $clean = preg_replace('/\s+/', ' ', $clean);

        // 2. EXTRACTION PAR BLOCS (Méthode la plus fiable pour ton fichier)
        function getVal($label, $txt) {
            // Cherche le label, ignore ce qu'il y a entre, et prend la valeur entre guillemets
            if (preg_match('/' . $label . '\s*"\s*,\s*"\s*([^"]+)/i', $txt, $m)) {
                return trim($m[1]);
            }
            return "N/C";
        }

        $immat = getVal('Immatriculation', $clean);
        $marque = getVal('Marque', $clean);
        $modele = getVal('Modèle', $clean);
        $vehicule = ($marque !== "N/C") ? $marque . " " . $modele : "Inconnu";
        $km = getVal('Kilométrage', $clean);
        
        // Données techniques (Volumes et Pressions)
        $h_recup = getVal('Huiler récupérée', $clean); // Garde la faute Bardahl
        $h_inj = getVal('Huile injectée', $clean);
        $p_debut = getVal('Pression début de prestation relevée', $clean);
        $p_fin = getVal('Pression fin de prestation relevée', $clean);
        $norme = getVal('Norme d\'huile d\'origine', $clean);
        $type_h = getVal('Type d\'huile', $clean);

        // 3. GÉNÉRATION DU PDF (Format identique à ton modèle vierge)
        if (ob_get_length()) ob_end_clean();
        $pdf = new FPDF(); 
        $pdf->AddPage();
        
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(200, 0, 0); 
        $pdf->Cell(0, 15, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Rapport d\'intervention vidange BVA'), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 10);
        $colonnes = [
            ['Véhicule', $vehicule], ['Immatriculation', $immat], ['Kilométrage', $km . ' km'],
            ['Huile récupérée', $h_recup], ['Huile injectée', $h_inj],
            ['Pression début', $p_debut], ['Pression fin', $p_fin],
            ['Norme d\'huile', $norme], ['Type d\'huile', $type_h]
        ];

        foreach ($colonnes as $c) {
            $pdf->Cell(85, 9, iconv('UTF-8', 'windows-1252', $c[0]), 1); 
            $pdf->Cell(105, 9, iconv('UTF-8', 'windows-1252', $c[1]), 1, 1);
        }

        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, "Signature / Cachet du garage :", 0, 1);

        $pdf->Output('D', 'BVA_' . $immat . '.pdf');
        exit;
    } catch (Exception $e) { echo "Erreur : " . $e->getMessage(); }
}
