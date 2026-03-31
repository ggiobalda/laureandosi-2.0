<?php

class GestioneCarrieraStudente
{
    public static function prelevaAnagrafica(string $matricola): array
    {
        $json = file_get_contents(__DIR__."/../GestioneCarrieraStudente/".$matricola."_anagrafica.json");
        $anagrafica = json_decode($json, true);

        return $anagrafica["Entries"]["Entry"];
    }

    public static function prelevaCarriera(string $matricola): ?array
    {
        $path = __DIR__."/../GestioneCarrieraStudente/".$matricola."_esami.json";
        //controllo necessario per il metodo controlloMatricole in InterfacciaGrafica
        if (file_exists($path)) {
            $json = file_get_contents($path);
        } else {
            return null;
        }
        $esami = json_decode($json, true);

        return $esami["Esami"]["Esame"];
    }
}