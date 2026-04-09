<?php
/*
Utilizza la libreria esterna PHPMailer per autenticarsi su un server SMTP e inviare in loop le email a tutti gli studenti con in allegato il relativo PDF.
*/
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__."/../lib/PHPMailer/src/PHPMailer.php";
require __DIR__."/../lib/PHPMailer/src/SMTP.php";
require_once "CarrieraLaureando.php";
require_once "FileConfigurazione.php";

class GestoreEmail
{
    public function __construct(string $cdl)
    {
        // i dati anagrafici e gli indirizzi mail vengono prelevati dall'array di sessione
        $carriere = $_SESSION['carriere'];
        $totali = count($carriere);
        $inviate = 0;
        $_SESSION["stato"] = "Prospetti inviati: ".$inviate."/".$totali;

        $email = new PHPMailer();
        $email->IsSMTP();
        $email->Host = "mixer.unipi.it";
        $email->SMTPSecure = "tls";
        $email->Port = 25;
        $email->SMTPAuth = false;
        $email->addCustomHeader('Content-Type', 'text/plain; windows-1252');
        $email->setFrom("no-reply-laureandosi@ing.unipi.it", "Laureandosi 2.0");
        $email->Subject = "Appello di laurea in ".$cdl."- indicatori per voto di laurea";
        $email->Body = mb_convert_encoding(FileConfigurazione::getEmailBody($cdl), 'Windows-1252', 'UTF-8');;

        foreach ($carriere as $destinatario) {
            $email->clearAddresses();
            $email->clearAttachments();
            $email->AddAddress($destinatario->email);
            $email->addAttachment(__DIR__."/../prospetti/$cdl/".$destinatario->matricola."_prospetto.pdf");

            if (!$email->Send()) {
                $_SESSION["statoErrori"] .= "Errore nell'invio dei prospetti!<br>";
                break;
            } else {
                // il numero di mail inviate viene aggiornato
                $inviate++;
                $_SESSION["stato"] = "Prospetti inviati: ".$inviate."/".$totali;
            }
            sleep(13);
        }

        // Chiude la connessione SMTP
        $email->SmtpClose();
    }
}