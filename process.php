<?php
require 'vendor/autoload.php';
use Smalot\PdfParser\Parser;

// Utilisation de la classe FPDF classique
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file']['tmp_name'];
    try {
        $parser = new Parser();
        $pdfParsed = $parser->parseFile($file);
        $text = $pdfParsed->getText();

        // Extraction des données du test.pdf [cite: 3, 11]
        preg_match('/"Marque"\s*,\s*"([^"]+)"/i', $text, $m_marque);
        preg_match('/"Modèle"\s*,\s*"([^"]+)"/i', $text, $m_modele);
        $vehicule = (isset($m_marque[1]) ? trim($m_marque[1]) : "Inconnu") . " " . (isset($m_modele[1]) ? trim($m_modele[1]) : "");
        
        preg_match('/"Immatriculation"\s*,\s*"([^"]+)"/i', $text, $m_imm);
        $immat = isset($m_imm[1]) ? trim($m_imm[1]) : "NC"; [cite: 3]

        preg_match('/"Kilométrage"\s*,\s*"([^"]+)"/i', $text, $m_km);
        $km = isset($m_km[1]) ? trim($m_km[1]) : "0"; [cite: 3]

        preg_match('/"Pression début[^"]*"\s*,\s*"([^"]+)"/i', $text, $m_p1);
        $p_debut = isset($m_p1[1]) ? trim($m_p1[1]) : "0,0 Bars"; [cite: 11]

        preg_match('/"Pression fin[^"]*"\s*,\s*"([^"]+)"/i', $text, $m_p2);
        $p_fin = isset($m_p2[1]) ? trim($m_p2[1]) : "0,0 Bars"; [cite: 11]

        // Création du nouveau PDF (Garage Surannais) [cite: 20, 21]
        $pdf = new FPDF(); 
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'GARAGE SURANNAIS',0,1,'C');
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,10,'RAPPORT D\'INTERVENTION BVA',0,1,'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial','',11);
        $pdf->Cell(80,10,'Vehicule:',1); $pdf->Cell(110,10,utf8_decode($vehicule),1,1);
        $pdf->Cell(80,10,'Immatriculation:',1); $pdf->Cell(110,10,$immat,1,1);
        $pdf->Cell(80,10,'Kilometrage:',1); $pdf->Cell(110,10,$km . ' km',1,1);
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(80,10,'Pression Debut:',1); $pdf->Cell(110,10,$p_debut,1,1);
        $pdf->Cell(80,10,'Pression Fin:',1); $pdf->Cell(110,10,$p_fin,1,1);

        $pdf->Output('D', 'Rapport_GSW_'.$immat.'.pdf');
    } catch (Exception $e) { echo "Erreur: " . $e->getMessage(); }
}
