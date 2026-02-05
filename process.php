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
        
        // On récupère tout le texte et on enlève les guillemets et sauts de ligne inutiles
        $rawText = $pdfParsed->getText();
        $cleanText = str_replace(['"', "\n", "\r", "\t"], ' ', $rawText);

        // --- EXTRACTION CHIRURGICALE ---
        
        // Immatriculation : cherche le texte après "Immatriculation"
        preg_match('/Immatriculation\s+([A-Z0-9-]+)/i', $cleanText, $m_imm);
        $immat = isset($m_imm[1]) ? trim($m_imm[1]) : "NC";

        // Véhicule : prend ce qu'il y a entre Marque et Kilométrage
        preg_match('/Marque\s+(.*?)\s+Modèle\s+(.*?)\s+Kilométrage/i', $cleanText, $m_veh);
        $vehicule = (isset($m_veh[1]) && isset($m_veh[2])) ? trim($m_veh[1] . " " . $m_veh[2]) : "Inconnu";

        // Kilométrage : cherche les chiffres après le mot
        preg_match('/Kilométrage\s+(\d+)/i', $cleanText, $m_km);
        $km = isset($m_km[1]) ? trim($m_km[1]) : "0";

        // Pressions : cherche les valeurs avant "Bars"
        preg_match('/début de prestation relevée\s+([\d,.]+)\s*Bars/i', $cleanText, $m_p1);
        $p_debut = isset($m_p1[1]) ? trim($m_p1[1]) . " Bars" : "0,0 Bars";

        preg_match('/fin de prestation relevée\s+([\d,.]+)\s*Bars/i', $cleanText, $m_p2);
        $p_fin = isset($m_p2[1]) ? trim($m_p2[1]) . " Bars" : "0,0 Bars";

        // --- GÉNÉRATION DU RAPPORT SURANNAIS ---
        if (ob_get_length()) ob_end_clean();
        $pdf = new FPDF(); 
        $pdf->AddPage();
        
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(200, 0, 0); 
        $pdf->Cell(0, 15, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Rapport d\'intervention vidange BVA'), 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(80, 10, iconv('UTF-8', 'windows-1252', 'Véhicule'), 1); 
        $pdf->Cell(110, 10, iconv('UTF-8', 'windows-1252', $vehicule), 1, 1);
        $pdf->Cell(80, 10, 'Immatriculation', 1); 
        $pdf->Cell(110, 10, $immat, 1, 1);
        $pdf->Cell(80, 10, iconv('UTF-8', 'windows-1252', 'Kilométrage'), 1); 
        $pdf->Cell(110, 10, $km . ' km', 1, 1);
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(80, 10, 'Pression Debut', 1); $pdf->Cell(110, 10, $p_debut, 1, 1);
        $pdf->Cell(80, 10, 'Pression Fin', 1); $pdf->Cell(110, 10, $p_fin, 1, 1);

        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, "Signature / Cachet du garage :", 0, 1);

        $pdf->Output('D', 'Rapport_BVA_' . $immat . '.pdf');
        exit;
    } catch (Exception $e) { echo "Erreur : " . $e->getMessage(); }
}
