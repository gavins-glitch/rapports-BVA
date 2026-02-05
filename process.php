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
        $rawText = $pdfParsed->getText();
        
        // Nettoyage radical du texte pour l'analyse
        $cleanText = str_replace(['"', "\n", "\r", "\t"], ' ', $rawText);

        // --- EXTRACTION DE TOUTES LES DONNÉES ---
        preg_match('/Immatriculation\s+([A-Z0-9-]+)/i', $cleanText, $m_imm);
        $immat = isset($m_imm[1]) ? trim($m_imm[1]) : "NC";

        preg_match('/Marque\s+(.*?)\s+Modèle\s+(.*?)\s+Kilométrage/i', $cleanText, $m_veh);
        $vehicule = (isset($m_veh[1]) && isset($m_veh[2])) ? trim($m_veh[1] . " " . $m_veh[2]) : "Inconnu";

        preg_match('/Kilométrage\s+(\d+)/i', $cleanText, $m_km);
        $km = isset($m_km[1]) ? trim($m_km[1]) : "0";

        // Huiles et Pressions
        preg_match('/Huiler récupérée\s+([\d,.]+ L)/i', $cleanText, $m_h1);
        $h_recup = isset($m_h1[1]) ? trim($m_h1[1]) : "0,00 L";

        preg_match('/Huile injectée\s+([\d,.]+ L)/i', $cleanText, $m_h2);
        $h_inj = isset($m_h2[1]) ? trim($m_h2[1]) : "0,00 L";

        preg_match('/début de prestation relevée\s+([\d,.]+ Bars)/i', $cleanText, $m_p1);
        $p_debut = isset($m_p1[1]) ? trim($m_p1[1]) : "0,0 Bars";

        preg_match('/fin de prestation relevée\s+([\d,.]+ Bars)/i', $cleanText, $m_p2);
        $p_fin = isset($m_p2[1]) ? trim($m_p2[1]) : "0,0 Bars";

        // Normes et Types
        preg_match('/Norme d\'huile d\'origine\s+(.*?)\s+Type d\'huile/i', $cleanText, $m_norme);
        $norme = isset($m_norme[1]) ? trim($m_norme[1]) : "NC";

        preg_match('/Type d\'huile\s+(.*?)\s+Nettoyant/i', $cleanText, $m_type);
        $type_huile = isset($m_type[1]) ? trim($m_type[1]) : "NC";

        // --- GÉNÉRATION DU PDF (Format Modèle Vierge) ---
        if (ob_get_length()) ob_end_clean();
        $pdf = new FPDF(); 
        $pdf->AddPage();
        
        // En-tête Garage Surannais
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(200, 0, 0); 
        $pdf->Cell(0, 10, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, iconv('UTF-8', 'windows-1252', 'Rapport d\'intervention vidange BVA'), 0, 1, 'C');
        $pdf->Ln(5);

        // Tableau complet comme ton modèle vierge
        $pdf->SetFont('Arial', '', 10);
        
        $lignes = [
            ['Véhicule', $vehicule],
            ['Immatriculation', $immat],
            ['Kilométrage', $km . ' km'],
            ['Huile récupérée', $h_recup],
            ['Huile injectée', $h_inj],
            ['Pression début de prestation', $p_debut],
            ['Pression fin de prestation', $p_fin],
            ['Norme d\'huile', $norme],
            ['Type d\'huile', $type_huile]
        ];

        foreach ($lignes as $ligne) {
            $pdf->Cell(85, 9, iconv('UTF-8', 'windows-1252', $ligne[0]), 1); 
            $pdf->Cell(105, 9, iconv('UTF-8', 'windows-1252', $ligne[1]), 1, 1);
        }

        $pdf->Ln(15);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, "Signature / Cachet du garage :", 0, 1);

        // Nom du fichier personnalisé : BVA_PLAQUE.pdf
        $pdf->Output('D', 'BVA_' . $immat . '.pdf');
        exit;
    } catch (Exception $e) { echo "Erreur : " . $e->getMessage(); }
}
