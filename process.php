<?php
// 1. GESTION DES ERREURS : On logue mais on n'affiche rien pour ne pas casser le PDF
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED);
ob_start(); // Empêche tout affichage avant le PDF

require 'vendor/autoload.php';
use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file']['tmp_name'];
    
    try {
        $parser = new Parser();
        $pdfParsed = $parser->parseFile($file);
        $text = $pdfParsed->getText();

        // NETTOYAGE : On normalise les espaces
        $cleanText = preg_replace('/\s+/', ' ', $text);

        // FONCTION D'EXTRACTION : Adaptée au format texte brut
        function getVal($label, $txt) {
            // Cherche le label et capture ce qui suit jusqu'au prochain mot-clé ou virgule
            if (preg_match('/' . preg_quote($label, '/') . '\s*"\s*,\s*"\s*([^"]+)/i', $txt, $m)) {
                return trim($m[1]);
            }
            // Secours si le format est différent (sans guillemets)
            if (preg_match('/' . preg_quote($label, '/') . '\s+([^,]+)/i', $txt, $m)) {
                return trim($m[1]);
            }
            return "N/C";
        }

        // Extraction des données du rapport Bardahl
        $immat    = getVal('Immatriculation', $cleanText); //
        $marque   = getVal('Marque', $cleanText); //
        $modele   = getVal('Modèle', $cleanText); //
        $km       = getVal('Kilométrage', $cleanText); //
        $h_recup  = getVal('Huiler récupérée', $cleanText); //
        $h_inj    = getVal('Huile injectée', $cleanText); //
        $p_debut  = getVal('Pression début de prestation relevée', $cleanText); //
        $p_fin    = getVal('Pression fin de prestation relevée', $cleanText); //
        $norme    = getVal('Norme d\'huile d\'origine', $cleanText); //
        $type_h   = getVal('Type d\'huile', $cleanText); //

        // GÉNÉRATION DU NOUVEAU PDF
        if (ob_get_length()) ob_clean(); // On vide le buffer avant d'envoyer le PDF
        
        $pdf = new FPDF(); 
        $pdf->AddPage();
        
        // Header stylisé
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(200, 0, 0); 
        $pdf->Cell(0, 15, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'RAPPORT D\'INTERVENTION VIDANGE BVA'), 0, 1, 'C');
        $pdf->Ln(10);

        // Tableau des résultats
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
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell(80, 10, iconv('UTF-8', 'windows-1252', $row[0]), 1, 0
