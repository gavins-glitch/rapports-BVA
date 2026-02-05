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

        // 1. EXTRACTION DES DONNÉES (Basée sur ton fichier test.pdf)
        
        // Extraction du véhicule (Marque + Modèle)
        preg_match('/"Marque"\s*,\s*"([^"]+)"/i', $text, $m_marque);
        preg_match('/"Modèle"\s*,\s*"([^"]+)"/i', $text, $m_modele);
        $vehicule = (isset($m_marque[1]) ? trim($m_marque[1]) : "") . " " . (isset($m_modele[1]) ? trim($m_modele[1]) : "");
        $vehicule = !empty(trim($vehicule)) ? $vehicule : "Non trouvé";

        // Extraction Kilométrage
        preg_match('/"Kilométrage"\s*,\s*"([^"]+)"/i', $text, $m_km);
        $km = isset($m_km[1]) ? trim($m_km[1]) . " km" : "À vérifier";

        // Extraction Immatriculation
        preg_match('/"Immatriculation"\s*,\s*"([^"]+)"/i', $text, $m_imm);
        $immat = isset($m_imm[1]) ? trim($m_imm[1]) : "NC";

        // Extraction Pressions
        preg_match('/"Pression début de prestation relevée"\s*,\s*"([^"]+)"/i', $text, $m_p1);
        $p_debut = isset($m_p1[1]) ? trim($m_p1[1]) : "0,0 Bars";

        preg_match('/"Pression fin de prestation relevée"\s*,\s*"([^"]+)"/i', $text, $m_p2);
        $p_fin = isset($m_p2[1]) ? trim($m_p2[1]) : "0,0 Bars";

        // 2. GÉNÉRATION DU NOUVEAU PDF PROPRE
        $report = new FPDF();
        $report->AddPage();
        
        // Entête
        $report->SetFont('Arial', 'B', 16);
        $report->SetTextColor(211, 47, 47); // Rouge Bardahl
        $report->Cell(0, 10, 'COMPTE-RENDU DE PRESTATION BVA', 0, 1, 'C');
        $report->Ln(10);
        
        // Infos Véhicule
        $report->SetFont('Arial', 'B', 12);
        $report->SetTextColor(0, 0, 0);
        $report->Cell(0, 10, utf8_decode("VÉHICULE : ") . utf8_decode($vehicule), 0, 1);
        $report->Cell(0, 10, "IMMATRICULATION : " . $immat, 0, 1);
        $report->Cell(0, 10, utf8_decode("KILOMÉTRAGE : ") . $km, 0, 1);
        $report->Ln(5);
        
        // Pressions
        $report->SetFillColor(240, 240, 240);
        $report->SetFont('Arial', 'B', 14);
        $report->Cell(0, 12, " RESULTATS DES PRESSIONS", 0, 1, 'L', true);
        
        $report->SetFont('Arial', '', 12);
        $report->Cell(0, 10, " Pression avant intervention : " . $p_debut, 0, 1);
        $report->SetTextColor(0, 128, 0); // Vert
        $report->Cell(0, 10, " Pression apres intervention : " . $p_fin, 0, 1);
        
        $report->Ln(10);
        $report->SetTextColor(0, 0, 0);
        $report->SetFont('Arial', 'I', 10);
        $report->MultiCell(0, 8, utf8_decode("La prestation a permis d'optimiser les pressions de fonctionnement de votre boîte de vitesses automatique."));

        // Sortie : Téléchargement direct
        $report->Output('D', 'Rapport_BVA_' . $immat . '.pdf');

    } catch (Exception $e) {
        echo "Erreur lors de l'analyse : " . $e->getMessage();
    }
}
