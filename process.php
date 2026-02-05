<?php
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;
use Fpdf\Fpdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file']['tmp_name'];
    
    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();

        // RECHERCHE LARGE (Regex) pour s'adapter au PDF Bardahl
        // On cherche le kilométrage (souvent près de 'km')
        preg_match('/(?:Kilométrage|KM|Compteur)\s*[:\.]?\s*(\d+[\s\.]?\d*)/i', $text, $matches_k);
        $km = isset($matches_k[1]) ? trim($matches_k[1]) . " km" : "À remplir";

        // On cherche les pressions (souvent un chiffre avec une virgule suivi de 'bar')
        preg_match('/(?:Pression entrée|Début|Initial)\s*[:\.]?\s*([\d,.]+)/i', $text, $matches_pe);
        $p_debut = isset($matches_pe[1]) ? str_replace('.', ',', $matches_pe[1]) : "0,0";

        preg_match('/(?:Pression sortie|Fin|Final)\s*[:\.]?\s*([\d,.]+)/i', $text, $matches_ps);
        $p_fin = isset($matches_ps[1]) ? str_replace('.', ',', $matches_ps[1]) : "0,0";

        // GÉNÉRATION DU NOUVEAU PDF
        $report = new FPDF();
        $report->AddPage();
        $report->SetFont('Arial', 'B', 18);
        $report->Cell(0, 10, 'RAPPORT DE PRESTATION BVA', 0, 1, 'C');
        $report->Ln(10);
        
        $report->SetFont('Arial', '', 12);
        $report->Cell(0, 10, "Kilometrage : " . $km, 0, 1);
        $report->Ln(5);
        $report->SetTextColor(200, 0, 0); // Rouge
        $report->Cell(0, 10, "Pression avant intervention : " . $p_debut . " bar", 0, 1);
        $report->SetTextColor(0, 150, 0); // Vert
        $report->Cell(0, 10, "Pression apres intervention : " . $p_fin . " bar", 0, 1);
        
        $report->SetTextColor(0, 0, 0);
        $report->Ln(20);
        $report->MultiCell(0, 10, "Synthese : La pression a ete retablie de maniere optimale apres le nettoyage complet du circuit.");

        // Téléchargement automatique du nouveau PDF
        $report->Output('D', 'Rapport_BVA_Final.pdf');

    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
