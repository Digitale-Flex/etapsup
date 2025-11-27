<?php

namespace App\Services;

use setasign\Fpdi\Tcpdf\Fpdi;

class PdfService
{
    public function generateContract($user, $reservation)
    {
        $templatePath = storage_path('app/private/contracts/medium_duration_converted.pdf');
        $outputPath = storage_path('app/private/contracts/generated/contract_'.$reservation->id.'.pdf');

        // Initialisation avec TCPDF
        $pdf = new Fpdi();

        // Import de la page 1
        $pageCount = $pdf->setSourceFile($templatePath);
        $templateId = $pdf->importPage(2);
        $pdf->AddPage();
        $pdf->useTemplate($templateId);


        // Configuration du PDF
        $pdf->SetCreator('Your App');
        $pdf->SetAuthor('Your Company');
        $pdf->SetTitle('Contrat de location');
        $pdf->SetSubject('Contrat généré automatiquement');

        // Désactivation de l'édition
        $pdf->SetProtection(
            [
                'modify',
                'copy',
                'annot-forms',
                'fill-forms',
                'extract',
                'assemble'
            ],
            null, // Mot de passe utilisateur
            'master-password' // Mot de passe propriétaire (optionnel)
        );

        // Ajout des données
        $this->addDynamicContent($pdf, $user, $reservation);

        // Génération du fichier
        $pdf->Output($outputPath, 'F');

        return $outputPath;
    }

    private function addDynamicContent($pdf, $user, $reservation)
    {
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(0, 0, 0);

        // Exemple de positionnement
        $coordinates = [
            'name' => ['x' => 50, 'y' => 100],
          //  'adresse' => ['x' => 50, 'y' => 120],
           // 'date' => ['x' => 50, 'y' => 140]
        ];

        $pdf->SetXY($coordinates['name']['x'], $coordinates['name']['y']);
        $pdf->Write(0, '$user->name');

       // $pdf->SetXY($coordinates['adresse']['x'], $coordinates['adresse']['y']);
       // $pdf->Write(0, $reservation->property->address);

        //$pdf->SetXY($coordinates['date']['x'], $coordinates['date']['y']);
        //$pdf->Write(0, now()->format('d/m/Y'));
    }
}
