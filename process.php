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

        // --- EXTRACTION DES DONNÉES (Basée sur test.pdf) ---
        preg_match('/"Marque"\s*,\s*"([^"]+)"/i', $text, $m_marque);
        preg_match('/"Modèle"\s*,\s*"([^"]+)"/i', $text, $m_modele);
        $vehicule = (isset($m_marque[1]) ? trim($m_marque[1]) : "") . " " . (isset($m_modele[1]) ? trim($m_modele[1]) : "");
        
        preg_match('/"Immatriculation"\s*,\s*"([^"]+)"/i', $text, $m_imm);
        $immat = isset($m_imm[1]) ? trim($m_imm[1]) : "N/C";

        preg_match('/"Kilométrage"\s*,\s*"([^"]+)"/i', $text, $m_km);
        $km = isset($m_km[1]) ? trim($m_km[1]) : "N/C";

        // Données d'huile et pressions
        preg_match('/"Huiler récupérée"\s*,\s*"([^"]+)"/i', $text, $m_h1);
        $h_recup = isset($m_h1[1]) ? trim($m_h1[1]) : "";

        preg_match('/"Huile injectée"\s*,\s*"([^"]+)"/i', $text, $m_h2);
        $h_inj = isset($m_h2[1]) ? trim($m_h2[1]) : "";

        preg_match('/"Pression début de prestation relevée"\s*,\s*"([^"]+)"/i', $text, $m_p1);
        $p_debut = isset($m_p1[1]) ? trim($m_p1[1]) : "";

        preg_match('/"Pression fin de prestation relevée"\s*,\s*"([^"]+)"/i', $text, $m_p2);
        $p_fin = isset($m_p2[1]) ? trim($m_p2[1]) : "";

        // --- GÉNÉRATION DU MODÈLE "GARAGE SURANNAIS" ---
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Header (Style Bardahl / Garage Surannais)
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(200, 0, 0); 
        $pdf->Cell(0, 10, 'BARDAHL', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'Rapport de vidange', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Garage Surannais', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->Cell(0, 0, '', 'T', 1); // Ligne de séparation
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "RAPPORT D'INTERVENTION", 0, 1, 'L');
        $pdf->Ln(5);

        // Tableau des données (Même ordre que ton modèle vierge)
        $pdf->SetFont('Arial', '', 11);
        $data = [
            ["Véhicule", $vehicule],
            ["Immatriculation", $immat],
            ["Kilométrage", $km],
            ["Huile récupérée", $h_recup],
            ["Huile injectée", $h_inj],
            ["Pression début de prestation", $p_debut],
            ["Pression fin de prestation", $p_fin]
        ];

        foreach ($data as $row) {
            $pdf->Cell(80, 10, utf8_decode($row[0]), 1);
            $pdf->Cell(110, 10, utf8_decode($row[1]), 1, 1);
        }

        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, "Signature/Cachet du garage:", 0, 1);

        // Téléchargement
        $pdf->Output('D', 'Rapport_Surannais_' . $immat . '.pdf');

    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
