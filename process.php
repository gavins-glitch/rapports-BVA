<?php
// On bloque les erreurs pour ne pas casser le PDF
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED);
ob_start(); 

require 'vendor/autoload.php';
use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    try {
        $parser = new Parser();
        $pdfParsed = $parser->parseFile($_FILES['pdf_file']['tmp_name']);
        
        // On récupère le texte sous forme de tableau de lignes
        $rows = explode("\n", $pdfParsed->getText());
        $cleanRows = array_values(array_filter(array_map('trim', $rows)));

        // Stratégie : On cherche l'index du mot-clé, 
        // la valeur se trouve X lignes plus bas selon ton copier-coller.
        function findValue($label, $array, $offset) {
            foreach ($array as $index => $row) {
                if (stripos($row, $label) !== false) {
                    return $array[$index + $offset] ?? "N/C";
                }
            }
            return "N/C";
        }

        // Selon ton copier-coller :
        // "Immatriculation" est en haut, sa valeur est 4 lignes plus bas.
        $immat  = findValue('Immatriculation', $cleanRows, 4); [cite: 3]
        $marque = findValue('Marque', $cleanRows, 4); [cite: 3]
        $modele = findValue('Modèle', $cleanRows, 4); [cite: 3]
        $km     = findValue('Kilométrage', $cleanRows, 4); [cite: 3]

        if (ob_get_length()) ob_clean();
        
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "RAPPORT EXTRAIT"), 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, "Immat :", 1); $pdf->Cell(0, 10, $immat, 1, 1); [cite: 3]
        $pdf->Cell(50, 10, "Marque :", 1); $pdf->Cell(0, 10, $marque, 1, 1); [cite: 3]
        $pdf->Cell(50, 10, "Modele :", 1); $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', $modele), 1, 1); [cite: 3]
        $pdf->Cell(50, 10, "KM :", 1); $pdf->Cell(0, 10, $km, 1, 1); [cite: 3]
        
        $pdf->Output('D', 'Rapport_Final.pdf');
        exit;

    } catch (Exception $e) {
        ob_end_clean();
        echo "Erreur : " . $e->getMessage();
    }
}
?>
