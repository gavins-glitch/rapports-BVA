<?php
// Désactive l'affichage des erreurs qui polluent la génération du PDF
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

        // --- EXTRACTION DES DONNÉES ---
        preg_match('/"Marque"\s*,\s*"([^"]+)"/i', $text, $m_marque);
        preg_match('/"Modèle"\s*,\s*"([^"]+)"/i', $text, $m_modele);
        $marque = isset($m_marque[1]) ? trim($m_marque[1]) : "";
        $modele = isset($m_modele[1]) ? trim($m_modele[1]) : "";
        $vehicule = trim($marque . " " . $modele);
        if(empty($vehicule)) $vehicule = "Inconnu";
        
        preg_match('/"Immatriculation"\s*,\s*"([^"]+)"/i', $text, $m_imm);
        $immat = isset($m_imm[1]) ? trim($m_imm[1]) : "NC";

        preg_match('/"Kilométrage"\s*,\s*"([^"]+)"/i', $text, $m_km);
        $km = isset($m_km[1]) ? trim($m_km[1]) : "0";

        preg_match('/"Pression début[^"]*"\s*,\s*"([^"]+)"/i', $text, $m_p1);
        $p_debut = isset($m_p1[1]) ? trim($m_p1[1]) : "0,0 Bars";

        preg_match('/"Pression fin[^"]*"\s*,\s*"([^"]+)"/i', $text, $m_p2);
        $p_fin = isset($m_p2[1]) ? trim($m_p2[1]) : "0,0 Bars";

        // --- GÉNÉRATION DU PDF ---
        
        // Nettoyage de sécurité pour éviter le bug "Some data already output"
        if (ob_get_length()) ob_end_clean();

        $pdf = new FPDF(); 
        $pdf->AddPage();
        
        // En-tête
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(200, 0, 0); 
        $pdf->Cell(0, 15, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Rapport d\'intervention vidange BVA'), 0, 1, 'C');
        $pdf->Ln(10);

        // Tableau
        $pdf->SetFont('Arial', '', 11);
        
        $pdf->Cell(80, 10, iconv('UTF-8', 'windows-1252', 'Véhicule'), 1); 
        $pdf->Cell(110, 10, iconv('UTF-8', 'windows-1252', $vehicule), 1, 1);
        
        $pdf->Cell(80, 10, 'Immatriculation', 1); 
        $pdf->Cell(110, 10, $immat, 1, 1);
        
        $pdf->Cell(80, 10, iconv('UTF-8', 'windows-1252', 'Kilométrage'), 1); 
        $pdf->Cell(110, 10, $km . ' km', 1, 1);
        
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(80, 10, 'Pression Debut', 1); 
        $pdf->Cell(110, 10, $p_debut,
