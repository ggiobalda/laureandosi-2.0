<?php
/*
Classe fondamentale che astrae la carriera di uno studente. Nel costruttore preleva anagrafica ed esami, itera sugli esami scartando quelli sovrannumerari o filtrati e infine istanzia oggetti di tipo Esame, ordinandoli per data. Calcola inoltre la media pesata e i CFU totali validi per la media tramite il metodo calcolaMedia().
*/

require_once 'GestioneCarrieraStudente.php';
require_once 'FileConfigurazione.php';
require_once 'Esame.php';

class CarrieraLaureando
{
    public string $nome;
    public string $cognome;
    public string $matricola;
    public string $email;
    public ?int $dataIscrizione = null; //nel caso lo studente sia di ingegneria informatica serve per la verifica del bonus
    public string $dataLaurea;
    public array $esami = []; //array contenente gli esami del laureando
    public int $cfuMedia; //somma dei cfu degli esami che concorrono alla media pesata
    public float $mediaPesata;

    public function __construct(string $matricola, string $cdl, string $dataLaurea)
    {
        $this->matricola = $matricola;
        $this->dataLaurea = strtotime($dataLaurea);
        $anagrafica = GestioneCarrieraStudente::prelevaAnagrafica($matricola);
        $esami = GestioneCarrieraStudente::prelevaCarriera($matricola);
        $this->nome = $anagrafica["nome"];
        $this->cognome = $anagrafica["cognome"];
        $this->email = $anagrafica["email_ate"];
        $esamiNonCarriera = FileConfigurazione::getEsamiNonCarriera($cdl, $matricola);
        foreach ($esami as $esame) {
            if ($this->dataIscrizione == null) //la data di iscrizione del laureando viene prelevata dal primo esame conseguito
            {
                $this->dataIscrizione = strtotime(str_replace("/", "-", $esame["INIZIO_CARRIERA"]));
            }
            //non vengono considerati gli esami sovrannumerari e gli esami che per altre motivazioni sono contenuti tra gli esami da rimuovere in filtri.json
            if ($esame["SOVRAN_FLG"] == 1 || !is_string($esame["DES"]) || !is_int($esame["PESO"]) || in_array(
                    $esame["DES"],
                    $esamiNonCarriera
                )) {
                continue;
            }
            $this->esami[] = new Esame($esame, $cdl, $matricola);
        }
        usort($this->esami, function ($e1, $e2) //gli esami vengono riordinati a partire dal primo conseguito
        {
            return $e1->dataSuperamento > $e2->dataSuperamento;
        });
        //nel caso lo studente sia di ingegneria informatica la sua media pesata viene calcolata dopo la verifica del bonus
        if ($cdl != "T. Ing. Informatica") {
            $result = $this->calcolaMedia();
            $this->cfuMedia = $result['cfu'];
            $this->mediaPesata = $result['media'];
        }
    }

    protected function calcolaMedia(): array
    {
        $tot = 0;
        $cfu = 0;
        foreach ($this->esami as $esame) {
            if (!$esame->media) {
                continue;
            }
            $tot += $esame->voto * $esame->cfu;
            $cfu += $esame->cfu;
        }

        return [
            'media' => $tot / $cfu,
            'cfu' => $cfu,
        ];
    }

    public function calcolaCfuCurricolari(): int
    {
        $cfu = 0;
        foreach ($this->esami as $esame) {
            $cfu += $esame->cfu;
        }

        return $cfu;
    }
}

