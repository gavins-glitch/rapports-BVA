<?php
// On désactive l'affichage des erreurs pour éviter de corrompre le fichier PDF
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED);
ob_start(); 

require 'vendor/autoload.php';

// Test de la classe FPDF : on essaie les deux écritures courantes
if (class_exists('FPDF')) {
    $pdf = new FPDF();
} elseif (class_exists('\FPDF')) {
    $pdf = new \FPDF();
} else {
    ob_end_clean();
    die("Erreur : La bibliotheque FPDF n'est pas chargee. Verifiez votre fichier composer.json.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $immat    = $_POST['immat'] ?? 'N/C';
    $marque   = $_POST['marque'] ?? 'N/C';
    $modele   = $_POST['modele'] ?? 'N/C';
    $km       = $_POST['km'] ?? 'N/C';
    $h_recup  = $_POST['h_recup'] ?? 'N/C';
    $h_inj    = $_POST['h_inj'] ?? 'N/C';
    $p_debut  = $_POST['p_debut'] ?? 'N/C';
    $p_fin    = $_POST['p_fin'] ?? 'N/C';
    $remarques = $_POST['remarques'] ?? ''; // Renommé selon ta demande

    $pdf->AddPage();
    
    // Titre principal
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(200, 0, 0); 
    $pdf->Cell(0, 15, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'RAPPORT D\'INTERVENTION VIDANGE BVA'), 0, 1, 'C');
    $pdf->Ln(5);

    // Tableau des données techniques
    $pdf->SetFont('Arial', '', 11);
    $donnees = [
        ['Vehicule', strtoupper($marque . ' ' . $modele)],
        ['Immatriculation', strtoupper($immat)],
        ['Kilometrage', $km],
        ['Huile recuperee', $h_recup],
        ['Huile injectee', $h_inj],
        ['Pression Debut', $p_debut],
        ['Pression Fin', $p_fin]
    ];

    foreach ($donnees as $ligne) {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(80, 10, iconv('UTF-8', 'windows-1252', $ligne[0]), 1, 0, 'L', true); 
        $pdf->Cell(110, 10, iconv('UTF-8', 'windows-1252', $ligne[1]), 1, 1, 'L');
    }

    // Zone de Remarques (au lieu de Conseils pro)
    if (!empty($remarques)) {
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 10, 'Remarques :', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 8, iconv('UTF-8', 'windows-1252', $remarques), 1);
    }

    $pdf->Ln(15);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, "Fait le : " . date('d/m/Y'), 0, 1);
    $pdf->Cell(0, 10, "Signature et tampon du garage :", 0, 1);

    // Nettoyage du tampon de sortie pour envoyer le PDF proprement
    if (ob_get_length()) ob_clean();
    $pdf->Output('D', 'Rapport_BVA_' . str_replace(' ', '_', $immat) . '.pdf');
    exit;
}
