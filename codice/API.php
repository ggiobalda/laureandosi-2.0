<?php

require_once "classi/InterfacciaGrafica.php";
session_start();

// inizializzazione valori di default solo se non esistono già (evita reset inutili)
$defaults = [
    "stato"        => "",
    "link"         => "",
    "statoErrori"  => "",
    "disabilitato" => "disabled",
    "test"         => "",
    "testTitolo"   => "",
    "sfondo"       => ""
];

foreach ($defaults as $key => $value) {
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = $value;
    }
}

/**
 * GESTIONE TEST
 * Separazione della logica di test dall'interfaccia principale
 */
if (isset($_GET["test"])) {
    require_once "test.php";
    $_SESSION["test"] = 'style="display: none"';
    $_SESSION["testTitolo"] = '<p style="font-size: 30pt"><b>Generatore Prospetti di Laurea</b><br>TEST</p>';
    $_SESSION["sfondo"] = 'style="background-color: rgb(230, 230, 230);"';
    $test = new Test();
}

/**
 * CREAZIONE PROSPETTI
 * Validazione input e generazione file
 */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["crea"])) {
    // recupero dati, se presenti
    $matricole = $_POST["matricole"] ?? "";
    $cdl = $_POST["cdl"] ?? "";
    $data = $_POST["data"] ?? "";

    // nuova istanza dell'interfaccia grafica
    $gp = new InterfacciaGrafica($matricole, $cdl, $data);
    $_SESSION["gp"] = $gp;

    if (!$gp->controlloMatricole()) {
        $_SESSION["statoErrori"] = "Errore: una o più matricole non sono valide";
        $_SESSION["stato"] = "";
        $_SESSION["disabilitato"] = "disabled"; // diasbilita tasto invio
    }
    else {
        // generazione dei prospetti
        $gp->generaProspetti();
        $_SESSION["link"] = $gp->apriProspetti();
        $_SESSION["stato"] = "Prospetti creati con successo!";
        $_SESSION["statoErrori"] = "";
        $_SESSION["disabilitato"] = ""; // abilita tasto invio
    }
}

/**
 * INVIO PROSPETTI
 * Eseguito solo se sono stati generati i prospetti
 */
if (isset($_POST["invia"]) && isset($_SESSION["gp"])) {
    try {
        $_SESSION["gp"]->inviaProspetti();
        $_SESSION["stato"] = "Email inviate correttamente.";
        $_SESSION["link"]  = $_SESSION["gp"]->apriProspetti();
    } catch (Exception $e) {
        $_SESSION["statoErrori"] = "Errore durante l'invio: " . $e->getMessage();
    }
}