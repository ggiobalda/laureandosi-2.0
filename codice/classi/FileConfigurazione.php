<?php

class FileConfigurazione
{
    private static $cdlData = null;
    private static $esamiInformatici = null;
    private static $filtriData = null;

    // Funzione che carica il JSON negli array della classe, restituendo un array vuoto in caso di errore
    private static function caricaJson(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        return is_array($data) ? $data : [];
    }

    // Inizializzazione dei dati dai file json se non già caricati
    private static function init(): void
    {
        if (self::$cdlData === null) {
            self::$cdlData = self::caricaJson(__DIR__."/../config/cdl.json");
            self::$esamiInformatici = self::caricaJson(__DIR__."/../config/esami_inf.json");
            self::$filtriData = self::caricaJson(__DIR__."/../config/filtri.json");
        }
    }

    public static function getEsamiNonCarriera(string $cdl, string $matricola = null): array
    {
        self::init();

        $esamiDaTogliere = self::$filtriData[$cdl]["*"]["da_togliere"] ?? [];

        if ($matricola && isset(self::$filtriData[$cdl][$matricola]["da_togliere"])) {
            $esamiDaTogliere = array_merge($esamiDaTogliere, self::$filtriData[$cdl][$matricola]["da_togliere"]);
        }

        return is_array($esamiDaTogliere) ? $esamiDaTogliere : [];
    }

    public static function getEsamiNonMedia(string $cdl, string $matricola = null): array
    {
        self::init();

        $esamiNonMedia = self::$filtriData[$cdl]["*"]["non_media"] ?? [];

        if ($matricola && isset(self::$filtriData[$cdl][$matricola]["non_media"])) {
            $esamiNonMedia = array_merge($esamiNonMedia, self::$filtriData[$cdl][$matricola]["non_media"]);
        }

        return is_array($esamiNonMedia) ? $esamiNonMedia : [];
    }

    public static function getEsamiInformatici(): array
    {
        self::init();

        return is_array(self::$esamiInformatici) ? self::$esamiInformatici : [];
    }

    public static function getCfuCurricolari(string $cdl): int
    {
        self::init();

        return self::$cdlData[$cdl]["crediti_totali"] ?? 0;
    }

    public static function getValoreLode(string $cdl): int
    {
        self::init();

        return self::$cdlData[$cdl]["valore_lode"] ?? 0;
    }

    public static function getFormulaVoto(string $cdl): string
    {
        self::init();

        return self::$cdlData[$cdl]["formula"] ?? "";
    }

    public static function getInfoParametro(string $cdl): array
    {
        self::init();

        return self::$cdlData[$cdl]["info_parametro"] ?? [];
    }

    public static function getMessaggioProspetto(string $cdl): string
    {
        self::init();

        return self::$cdlData[$cdl]["msg_commissione"] ?? "Messaggio non disponibile.";
    }

    public static function getEmailBody(string $cdl): string
    {
        self::init();

        return self::$cdlData[$cdl]["corpo_email"] ?? "Corpo email non disponibile.";
    }
}
