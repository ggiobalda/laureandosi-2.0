<?php
/*
Classe di front-end logico. Riceve i dati in input (matricole, corso, data). Dispone di un metodo controlloMatricole() che verifica se tutte le matricole inserite esistono e appartengono al CdL selezionato. Contiene i metodi per avviare la generazione (generaProspetti()) e l'invio delle email (inviaProspetti())
*/

require_once "GeneratoreProspettiCommissione.php";
require_once "GestoreEmail.php";
require_once "GestioneCarrieraStudente.php";

$carriere = [];

class InterfacciaGrafica
{
    private array $matricole;
    private string $cdl;
    private string $dataLaurea;

    public function __construct(string $matricole, string $cdl, string $dataLaurea)
    {
        $this->matricole = explode(" ", $matricole);
        $this->cdl = $cdl;
        $this->dataLaurea = $dataLaurea;
    }

    public function controlloMatricole(): bool
    {
        $cdl = null;
        foreach ($this->matricole as $m) {
            $esami = GestioneCarrieraStudente::prelevaCarriera($m);
            //si controlla l'esistenza della matricola controllando se sia presente la sua carriera
            if ($esami && is_array($esami)) {
                if ($cdl == null) //si prende il corso dal primo esame
                {
                    $cdl = $esami[0]['CORSO'];
                } else {
                    if ($cdl != $esami[0]['CORSO']) //se verifica che gli studenti appartengano allo stesso cdl
                    {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

        return true;
    }

    public function generaProspetti(): void
    {
        new GeneratoreProspettiCommissione($this->matricole, $this->cdl, $this->dataLaurea);
    }

    public function apriProspetti(): string
    {
        return str_replace(
            " ",
            "%20",
            site_url("/wp-content/themes/codice/prospetti/$this->cdl/commissione_prospetto.pdf")
        );
    }

    public function inviaProspetti(): void
    {
        new GestoreEmail($this->cdl);
    }
}