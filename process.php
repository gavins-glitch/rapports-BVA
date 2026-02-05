<?php
require 'vendor/autoload.php';
use Fpdf\Fpdf; // Assure-toi que fpdf est installé via composer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération sécurisée des données
    $immat  = $_POST['immat'] ?? 'N/C';
    $marque = $_POST['marque'] ?? 'N/C';
    $modele = $_POST['modele'] ?? 'N/C';
    $km     = $_POST['km'] ?? '0';
    $h_recup = $_POST['h_recup'] ?? '0';
    $h_inj   = $_POST['h_inj'] ?? '0';
    $p_debut = $_POST['p_debut'] ?? '0';
    $p_fin   = $_POST['p_fin'] ?? '0';

    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Header
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetTextColor(200, 0, 0); 
    $pdf->Cell(0, 15, 'BARDAHL - GARAGE SURANNAIS', 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'RAPPORT D\'INTERVENTION BVA'), 0, 1, 'C');
    $pdf->Ln(5);

    // Tableau de données
    $pdf->SetFont('Arial', '', 11);
    $data = [
        ['Véhicule', strtoupper($marque) . ' ' . strtoupper($modele)],
        ['Immatriculation', strtoupper($immat)],
        ['Kilométrage', number_format($km, 0, '.', ' ') . ' km'],
        ['Huile récupérée', $h_recup . ' L'],
        ['Huile injectée', $h_inj . ' L'],
        ['Pression Début', $p_debut . ' Bars'],
        ['Pression Fin', $p_fin . ' Bars'],
    ];

    foreach ($data as $row) {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(80, 10, iconv('UTF-8', 'windows-1252', $row[0]), 1, 0, 'L', true); 
        $pdf->Cell(110, 10, iconv('UTF-8', 'windows-1252', $row[1]), 1, 1, 'L');
    }

    $pdf->Ln(20);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "Date de l'intervention : " . date('d/m/Y')), 0, 1);
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', "Signature et tampon :"), 0, 1);

    $pdf->Output('D', 'Rapport_' . $immat . '.pdf');
    exit;
}
