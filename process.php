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

        // Nettoyage : on remplace tout ce qui n'est pas du texte/chiffre par un espace
        // Cela permet de coller les données aux étiquettes
        $cleanText = preg_replace('/[^a-zA-Z0-9\s,.\-]/', ' ', $text);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);

        // --- FONCTION D'EXTRACTION ROBUSTE ---
        function extraire($cle, $source) {
            // On cherche le mot clé et on prend les 4-5 mots qui suivent
            if (preg_match('/' . $cle . '\s+([^,]+)/i', $source, $match)) {
                return trim($match[1]);
            }
            return "N/C";
        }

        // --- RÉCUPÉRATION DES DONNÉES (Basé sur test.pdf) ---
        $immat = extraire('Immatriculation', $cleanText);
        
        // Pour le véhicule, on combine Marque et Modèle 
        $marque = extraire('Marque', $cleanText);
        $modele = extraire('Modèle', $cleanText);
        $vehicule = ($marque !== "N/C") ? $marque . " " . $modele : "Inconnu";

        $km = extraire('Kilométrage', $cleanText);
        $h_recup = extraire('Huiler récupérée', $cleanText); // Avec la faute d'orthographe Bardahl 
        $h_inj = extraire('Huile injectée', $cleanText);
        
        // Pressions 
        preg_match('/début de prestation relevée\s+([\d,.]+)/i', $cleanText, $p1);
        $p_debut = isset($p1[1]) ? $p1[1] . " Bars" : "0,0 Bars";

        preg_match('/fin de prestation relevée\s+([\d,.]+)/i', $cleanText, $p2);
        $p_fin = isset($p2[1]) ? $p2[1] . " Bars" : "0,0 Bars";

        // Infos supplémentaires 
        $norme = extraire('Norme d h uile d origine', $cleanText);
        $type_h = extraire('Type d h uile', $cleanText);

        // --- GÉNÉRATION DU PDF FINAL (Format Garage Surannais) ---
        if (ob_get_length()) ob_end_clean();
        $pdf = new FPDF(); 
        $pdf->AddPage();
        
        // En-tête (Style modele-vierge.pdf) [cite: 19, 21]
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(200, 0, 0); 
        $pdf->Cell(0, 12, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, iconv('UTF-8', 'windows-1252', 'Rapport d\'intervention vidange BVA'), 0, 1, 'C');
        $pdf->Ln(8);

        // Tableau complet 
        $pdf->SetFont('Arial', '', 10);
        $data = [
            ['Véhicule', $vehicule],
            ['Immatriculation', $immat],
            ['Kilométrage', $km . ' km'],
            ['Huile récupérée', $h_recup],
            ['Huile injectée', $h_inj],
            ['Pression début de prestation', $p_debut],
            ['Pression fin de prestation', $p_fin],
            ['Norme d\'huile', $norme],
            ['Type d\'huile', $type_h]
        ];

        foreach ($data as $row) {
            $pdf->Cell(85, 9, iconv('UTF-8', 'windows-1252', $row[0]), 1); 
            $pdf->Cell(105, 9, iconv('UTF-8', 'windows-1252', $row[1]), 1, 1);
        }

        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "Signature / Cachet du garage :"), 0, 1);

        // Téléchargement avec le nom de la plaque 
        $pdf->Output('D', 'Rapport_BVA_' . $immat . '.pdf');
        exit;
    } catch (Exception $e) { echo "Erreur : " . $e->getMessage(); }
}
