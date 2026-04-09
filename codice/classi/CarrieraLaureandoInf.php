<?php
/*
Estende CarrieraLaureando per implementare la logica specifica per Ingegneria Informatica.
Calcola se lo studente ha diritto al bonus confrontando la data di laurea con la data di iscrizione.
Se ha il bonus, invoca togliVotoPiuBasso() che cerca l'esame peggiore (tenendo conto anche del peso in CFU per massimizzare il vantaggio) e lo esclude dalla media.
Infine calcola la mediaInformatica filtrando solo gli esami taggati come "INF"
*/

require_once 'CarrieraLaureando.php';

class CarrieraLaureandoInf extends CarrieraLaureando
{
    public bool $bonus;
    public float $mediaInformatica;

    public function __construct($matricola, $cdl, $dataLaurea)
    {
        parent::__construct($matricola, $cdl, $dataLaurea);
        if ($this->dataLaurea - 3.6 * 365 * 86400 < $this->dataIscrizione) //3.6 sono gli anni che intercorrono dall'iscrizione ad Aprile del 4° anno
        {
            $this->bonus = true;
        } else {
            $this->bonus = false;
        }
        if ($this->bonus) {
            $this->togliVotoPiuBasso();
        }
        // ora che è stato eventualmente rimosso l'esame dal voto più basso è possibile calcolare le medie pesate
        $result = $this->calcolaMedia();
        $this->cfuMedia = $result['cfu'];
        $this->mediaPesata = $result['media'];
        $this->mediaInformatica = $this->calcolaMediaInf();
    }

    private function togliVotoPiuBasso(): void
    {
        $minVoto = null;
        $maxCfu = null;
        $esameMin = null;
        foreach ($this->esami as $esame) {
            if (!$esame->media) {
                continue;
            }
            //si cerca l'esame che se rimosso migliori il più possibile la media pesata, considerando quindi anche il suo peso in cfu
            if ($minVoto == null || $esame->voto < $minVoto || ($esame->voto == $minVoto && $esame->cfu > $maxCfu)) {
                $minVoto = $esame->voto;
                $maxCfu = $esame->cfu;
                $esameMin = $esame->nome;
            }
        }
        //si rimuove l'esame da quelli che fanno media
        foreach ($this->esami as $esame) {
            if ($esame->nome == $esameMin) {
                $esame->media = false;
                break;
            }
        }
    }

    private function calcolaMediaInf(): float
    {
        $tot = 0;
        $cfu = 0;
        foreach ($this->esami as $esame) {
            if (!$esame->media || !$esame->inf) {
                continue;
            }
            $tot += $esame->voto * $esame->cfu;
            $cfu += $esame->cfu;
        }
        if ($cfu == 0) {
            return 0;
        }

        return $tot / $cfu;
    }
}