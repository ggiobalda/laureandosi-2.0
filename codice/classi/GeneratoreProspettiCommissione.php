<?php
/*
Classe deputata alla creazione materiale dei file PDF del prospetto per la commissione. Utilizza la libreria esterna FPDF.
*/
require_once __DIR__."/../lib/fpdf184/fpdf.php";
require_once 'GeneratoreProspettiLaureandi.php';

class GeneratoreProspettiCommissione
{
    private array $matricole;
    private array $prospettiLaureandi;
    private string $cdl;
    private string $dataLaurea;

    public function __construct(array $matricole, string $cdl, string $dataLaurea)
    {
        $this->matricole = $matricole;
        $this->prospettiLaureandi = [];
        $this->cdl = $cdl;
        $this->dataLaurea = $dataLaurea;
        $this->genera();
    }

    private function genera(): void
    {
        foreach ($this->matricole as $m) {
            $prospettoLaureando = new GeneratoreProspettiLaureandi($m, $this->cdl, $this->dataLaurea);
            $this->prospettiLaureandi[] = $prospettoLaureando;
        }

        $pdf = new FPDF();
        $pdf->SetMargins(11, 8);
        $pdf->AddPage();
        $font = "Arial";

        // generazione della tabella dei laureandi
        $pdf->SetFont($font, "", 13);
        $pdf->Cell(0, 6, $this->cdl, 0, 1, "C");
        $pdf->Cell(0, 6, "LISTA LAUREANDI", 0, 1, "C");
        $pdf->Ln(3);

        $pdf->SetFontSize(11);
        $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 7, "COGNOME", 1, 0, "C");
        $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 7, "NOME", 1, 0, "C");
        $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 7, "CDL", 1, 0, "C");
        $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 7, "VOTO LAUREA", 1, 1, "C");

        $pdf->SetFontSize(10);
        foreach ($this->prospettiLaureandi as $prospetto) {
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 6, $prospetto->carriera->cognome, 1, 0, "C");
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 6, $prospetto->carriera->nome, 1, 0, "C");
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 6, "", 1, 0, "C");
            $pdf->Cell(($pdf->GetPageWidth() - 22) / 4, 6, "/110", 1, 1, "C");
        }

        // aggiunta dei prospetti dei laureandi completi di simulazione
        foreach ($this->prospettiLaureandi as $prospetto) {
            $prospetto->costruisciPdf($pdf);
            $prospetto->aggiungiTabella($pdf);
        }
        // la cartella del cdl è stata sicuramente già creata durante la generazione del prospetto del primo laureando
        $pdf->Output(__DIR__."/../prospetti/$this->cdl/commissione_prospetto.pdf", "F");
    }
}