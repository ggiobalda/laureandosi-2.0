<?php

require_once 'FileConfigurazione.php';

class Esame
{
    public string $nome;
    public string $dataSuperamento;
    public int $voto;
    public int $cfu;
    public bool $media; //indica se l'esame faccia media
    public bool $inf; //indica se l'esame sia informatico

    public function __construct(array $rawData, string $cdl, string $matricola)
    {
        $this->nome = $rawData["DES"];
        $this->cfu = $rawData["PESO"];
        $voto = $rawData["VOTO"];
        $this->dataSuperamento = strtotime(str_replace("/", "-", $rawData["DATA_ESAME"]));

        if ($voto == null) {
            $voto = 0;
        } else {
            if ($voto == "30  e lode") {
                $voto = FileConfigurazione::getValoreLode($cdl);
            }
        }
        $this->voto = (int)$voto;

        $this->media = true;
        $this->inf = false;
        //si veriica se l'esame faccia media
        if ($this->voto == 0 || in_array($this->nome, FileConfigurazione::getEsamiNonMedia($cdl, $matricola))) {
            $this->media = false;
        }
        //verifica se l'esame sia informatico
        if ($cdl == "T. Ing. Informatica" && in_array($this->nome, FileConfigurazione::getEsamiInformatici())) {
            $this->inf = true;
        }
    }
}