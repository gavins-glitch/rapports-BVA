<?php
// On affiche les erreurs pour le debug sur Render (à retirer en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
// Note : Assure-toi d'avoir installé fpdf/fpdf via composer
use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file']['tmp_name'];
    
    try {
        $parser = new Parser();
        $pdfParsed = $parser->parseFile($file);
        $text = $pdfParsed->getText();

        // 1. On garde les sauts de ligne pour mieux isoler les champs
        // Mais on normalise les espaces multiples
        $text = preg_replace('/\s+/', ' ', $text);

        // 2. FONCTION D'EXTRACTION AMÉLIORÉE
        // On cherche le libellé et on capture ce qui vient juste après
        function getVal($label, $txt) {
            // Cette regex cherche le label et capture tout jusqu'au prochain mot clé ou fin de ligne
            // Adaptée à la structure : "Label Valeur"
            if (preg_match('/' . preg_quote($label, '/') . '\s+([^:]+?)(?=\s+[A-Z][a-z]|$)/u', $txt, $m)) {
                return trim($m[1]);
            }
            return "N/C";
        }

        // Extraction spécifique selon ton document
        $immat    = getVal('Immatriculation', $text);
        $marque   = getVal('Marque', $text);
        $modele   = getVal('Modèle', $text);
        $km       = getVal('Kilométrage', $text);
        $h_recup  = getVal('Huiler récupérée', $text); // Correction faute Bardahl incluse
        $h_inj    = getVal('Huile injectée', $text);
        $p_debut  = getVal('Pression début de prestation relevée', $text);
        $p_fin    = getVal('Pression fin de prestation relevée', $text);
        $norme    = getVal('Norme d\'huile d\'origine', $text);
        $type_h   = getVal('Type d\'huile', $text);

        // Nettoyage des valeurs (on enlève les résidus de tableaux s'il y en a)
        $immat = str_replace(['"', ','], '', $immat);
        $km = preg_replace('/[^0-9]/', '', $km); // On ne garde que les chiffres

        // 3. GÉNÉRATION DU PDF
        if (ob_get_length()) ob_end_clean();
        
        $pdf = new FPDF(); 
        $pdf->AddPage();
        
        // Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(200, 0, 0); 
        $pdf->Cell(0, 15, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, utf8_decode('RAPPORT D\'INTERVENTION VIDANGE BVA'), 0, 1, 'C');
        $pdf->Ln(5);

        // Tableau de données
        $pdf->SetFont('Arial', '', 10);
        $data = [
            ['Véhicule', $marque . ' ' . $modele],
            ['Immatriculation', $immat],
            ['Kilométrage', $km . ' km'],
            ['Huile récupérée', $h_recup],
            ['Huile injectée', $h_inj],
            ['Pression début', $p_debut],
            ['Pression fin', $p_fin],
            ['Norme d\'huile', $norme],
            ['Type d\'huile', $type_h]
        ];

        foreach ($data as $row) {
            $pdf->Cell(80, 8, utf8_decode($row[0]), 1); 
            $pdf->Cell(100, 8, utf8_decode($row[1]), 1, 1);
        }

        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, "Signature / Cachet du garage :", 0, 1);

        // Téléchargement
        $pdf->Output('D', 'Rapport_BVA_' . $immat . '.pdf');
        exit;

    } catch (Exception $e) {
        echo "Erreur lors de l'analyse : " . $e->getMessage();
    }
}
